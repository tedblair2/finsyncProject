<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Transaction;
use App\Models\TransactionCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Log;
use SimpleXMLElement;

class NCBAController extends Controller
{
    //
    public function saveTransactions($start,$end){
        // $fromDate=Carbon::parse($start)->format('dmY');
        // $toDate=Carbon::parse($end)->format('dmY');
        $fromDate="11092025";
        $toDate="11092025";
        $APIM_KEY=env('NCBA_APIM_KEY','');
        $environment=env('NCBA_ENVIRONMENT','sandbox');
        $baseUrl=$environment=='production' ? 'https://api.ncba.co.ke' : 'https://openbankingtest.api.ncbagroup.com/test/apigateway';
        $token=$this->getNCBAToken();
        $ncba=Bank::where('name','NCBA')->first();
        // $ncbaAccounts=$ncba->accounts;
        $ncbaAccounts=collect([
            (object)['account_number'=>'9115310018'],
        ]);

        $headers=[
            "Accept"=>"application/json",
            "Content-Type"=>"application/json",
            "Ocp-Apim-Subscription-Key"=>$APIM_KEY,
            "Authorization"=> "Bearer $token"
        ];

        $reponses=Http::pool(function ($pool) use ($headers,$baseUrl,$fromDate,$toDate,$ncbaAccounts) {
            foreach($ncbaAccounts as $account){
                $pool->withHeaders($headers)->post("$baseUrl/api/v1/AccountStatement/accountstatement",[
                    "fromDate"=>$fromDate,
                    "toDate"=>$toDate,
                    "country"=> "Kenya",
                    "accountNo"=> $account->account_number
                ]);
            }
        });
        foreach($reponses as $i=>$reponse){
            if($reponse->successful()){
                $result=$reponse->json();
                $accountNumber=$ncbaAccounts[$i]->account_number;
                $transactaionDate=Carbon::createFromFormat('dmY', $result['ValueDate'])->format('Y-m-d H:i:s');
                $creditdebitflag=strtolower($result['CreditDebitFlag']);
                $transactionID=$result['InstrumentReferenceNumber'];
                $amount=$creditdebitflag=='credit' ? floatval($result['CreditAmount']) : floatval($result['DebitAmount']);
                $runningBalance=$result['RunningBalance'];
                $narrative=$result['Narrative'];
                $transactioncode=$result['TransactionCode'];
                $transactionDesc=$result['TransactionCodeDescription'];
                $paymentDetails=$result['PaymentDetails'];
                $creditRef=$result['CreditReference'];
                $ftcrNarration=$result['AdditionalReference'];
                $currency=$result['TransactionCurrency'];

                TransactionCode::updateOrCreate([
                    'code' => $transactioncode
                ],[
                    'code' => $transactioncode,
                    'description' => $transactionDesc
                ]);

                $transaction=Transaction::where('transaction_id',$transactionID)->first();
                if(!$transaction){
                    Transaction::create([
                        'transaction_id' => $transactionID,
                        'transaction_code' => $transactioncode,
                        'amount' => $amount,
                        'account_number' => $accountNumber,
                        'phone_number' => '',
                        'status' => 'SUCCESS',
                        'narrative' => $narrative,
                        'ftCr_narration' => $ftcrNarration,
                        'transaction_date'=> $transactaionDate,
                        'creditdebitflag'=> $creditdebitflag,
                        'payment_details'=> $paymentDetails,
                        'credit_reference'=> $creditRef,
                        'currency'=> $currency,
                        'balance'=> $runningBalance
                    ]);
                }else{
                    $transaction->update([
                        'transaction_code' => $transactioncode,
                        'narrative' => $narrative,
                        'ftCr_narration' => $ftcrNarration,
                        'payment_details'=> $paymentDetails,
                        'credit_reference'=> $creditRef,
                        'currency'=> $currency,
                        'balance'=> $runningBalance
                    ]);
                }
                Log::info(now()." NCBA Transactions ".json_encode($result));
            }
        }
    }

    private function getNCBAToken(){
        $token=Cache::remember('ncbatoken',1500,function(){
            return $this->generateNCBAToken();
        });
        return $token;
    }

    private function generateNCBAToken(){
        $username=env('NCBA_USERID','');
        $password=env('NCBA_PASSWORD','');
        $APIM_KEY=env('NCBA_APIM_KEY','');
        $environment=env('NCBA_ENVIRONMENT','sandbox');
        $baseUrl=$environment=='production' ? 'https://api.ncba.co.ke' : 'https://openbankingtest.api.ncbagroup.com/test/apigateway';

        $headers=[
            "Accept"=>"application/json",
            "Content-Type"=>"application/json",
            "Ocp-Apim-Subscription-Key"=>$APIM_KEY
        ];
        $response=Http::withHeaders($headers)->post("$baseUrl/api/v1/Auth/generate-token",[
            "userID"=>$username,
            "password"=>$password
        ]);
        if($response->successful()){
            $result=$response->json();
            $token=$result['tokenID'];
            return $token;
        }else{
            return '';
        }
    }

    public function productionNCBANotification(Request $request){
        $xmlContent = $request->getContent();

        if (empty($xmlContent)) {
            return $this->ncbaResponse('FAIL: No Content',500);
        }

        try {
            $xml=new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $bodyNodes = $xml->xpath('//soapenv:Body/*');
            if (empty($bodyNodes)) {
                throw new \Exception("No SOAP Body found");
            }
            $bodyContent = $bodyNodes[0];
            $data = json_decode(json_encode($bodyContent), true);
            Log::info(now()." productionNCBANotification");
            Log::info(now()." ".json_encode($data));

            if(!isset($data['User']) && !isset($data['Password'])){
                return $this->ncbaResponse('FAIL: No Credentials Provided',401);
            }

            $bank=Bank::where('username', $data['User'])->where('password', $data['Password'])->first();
            if(!$bank){
                return $this->ncbaResponse('FAIL: Invalid Credentials',401);
            }

            if(!isset($data['HashVal']) || !isset($data['TransType']) || !isset($data['TransID']) || !isset($data['TransTime']) || !isset($data['TransAmount']) || !isset($data['AccountNr']) || !isset($data['CustomerName']) || !isset($data['Status'])){
                return $this->ncbaResponse('FAIL: Invalid Parameters',401);
            }

            $secretKey=$bank['secret_key'];
            $hashVal=$data['HashVal'];
            $transType=$data['TransType'];
            $transId=$data['TransID'];
            $transTime=$data['TransTime'];
            $transAmount=$data['TransAmount'];
            $accountNr=$data['AccountNr'];
            $narrative=isset($data['Narrative']) ? (is_array($data['Narrative']) ? implode('', $data['Narrative']) : $data['Narrative']) : '';
            $phoneNo=isset($data['PhoneNr']) ? (is_array($data['PhoneNr']) ? implode('', $data['PhoneNr']) : $data['PhoneNr']) : '';
            $name=$data['CustomerName'];
            $status=$data['Status'];
            $ftcrNarration=isset($data['FtCrNarration']) ? (is_array($data['FtCrNarration']) ? implode('', $data['FtCrNarration']) : $data['FtCrNarration']) : '';

            $key="$secretKey$transType$transId$transTime$transAmount$accountNr$narrative$phoneNo$name$status";
            $myhashVal=$this->generateHashValue($key);
            Log::info(now()." $hashVal invalidhash value");

            if($myhashVal!=$hashVal){
                Log::info(now()." $myhashVal invalid hash value");
                // return $this->ncbaResponse('FAIL: Invalid Hash Value',401);
            }

            $transAmount=floatval($transAmount);
            $date=Carbon::createFromFormat('ymdHi', $transTime);
            $transTime=$date->format('Y-m-d H:i:s');
            $creditdebitflag='credit';
            if($transAmount<0){
                $creditdebitflag='debit';
                $transAmount=abs($transAmount);
            }

            Transaction::updateOrCreate([
                'transaction_id' => $transId
            ],[
                'transaction_id' => $transId,
                'transaction_code' => $transType,
                'amount' => $transAmount,
                'account_number' => $accountNr,
                'customer_name' => $name,
                'phone_number' => $phoneNo,
                'status' => $status,
                'narrative' => $narrative,
                'ftCr_narration' => $ftcrNarration,
                'transaction_date'=> $transTime,
                'creditdebitflag'=> $creditdebitflag
            ]);

            Account::updateOrCreate([
                'account_number' => $accountNr
            ],[
                'account_number' => $accountNr,
                'bank_id' => $bank->id
            ]);
            
            return $this->ncbaResponse('OK',200);
        }catch(\Exception $e){
            Log::info(now()." $e");
            return $this->ncbaResponse('FAIL',500);
        }
    }

    public function sanboxNCBANotification(Request $request){
        $xmlContent = $request->getContent();

        if (empty($xmlContent)) {
            return $this->ncbaResponse('FAIL: No Content',500);
        }

        try {
            $xml=new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $bodyNodes = $xml->xpath('//soapenv:Body/*');
            if (empty($bodyNodes)) {
                throw new \Exception("No SOAP Body found");
            }
            $bodyContent = $bodyNodes[0];
            $data = json_decode(json_encode($bodyContent), true);
            Log::info(now()." sanboxNCBANotification");
            Log::info(now()." ".json_encode($data));

            if(!isset($data['User']) && !isset($data['Password'])){
                return $this->ncbaResponse('FAIL: No Credentials Provided',401);
            }

            $bank=Bank::where('username', $data['User'])->where('password', $data['Password'])->first();
            if(!$bank){
                return $this->ncbaResponse('FAIL: Invalid Credentials',401);
            }

            if(!isset($data['HashVal']) || !isset($data['TransType']) || !isset($data['TransID']) || !isset($data['TransTime']) || !isset($data['TransAmount']) || !isset($data['AccountNr']) || !isset($data['CustomerName']) || !isset($data['Status'])){
                return $this->ncbaResponse('FAIL: Invalid Parameters',401);
            }

            $secretKey=$bank['secret_key'];
            $hashVal=$data['HashVal'];
            $transType=$data['TransType'];
            $transId=$data['TransID'];
            $transTime=$data['TransTime'];
            $transAmount=$data['TransAmount'];
            $accountNr=$data['AccountNr'];
            $narrative=isset($data['Narrative']) ? (is_array($data['Narrative']) ? implode('', $data['Narrative']) : $data['Narrative']) : '';
            $phoneNo=isset($data['PhoneNr']) ? (is_array($data['PhoneNr']) ? implode('', $data['PhoneNr']) : $data['PhoneNr']) : '';
            $name=$data['CustomerName'];
            $status=$data['Status'];
            $ftcrNarration=isset($data['FtCrNarration']) ? (is_array($data['FtCrNarration']) ? implode('', $data['FtCrNarration']) : $data['FtCrNarration']) : '';

            $key="$secretKey$transType$transId$transTime$transAmount$accountNr$narrative$phoneNo$name$status";
            $myhashVal=$this->generateHashValue($key);
            Log::info(now()." $myhashVal invalid hash value");

            if($myhashVal!=$hashVal){
                Log::info(now()." $myhashVal invalid hash value");
                // return $this->ncbaResponse('FAIL: Invalid Hash Value',401);
            }

            return $this->ncbaResponse('OK',200);
        }catch(\Exception $e){
            Log::info(now()." $e");
            return $this->ncbaResponse('FAIL',500);
        }
    }

    private function generateHashValue($values){
        $hashedvalue=hash('sha256',$values,true);
        return base64_encode($hashedvalue);
    }

    private function ncbaResponse($msg,$code){
        $soapResponse = new SimpleXMLElement(
                '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"/>'
        );
        $soapResponse->addChild('soapenv:Header', null, 'http://schemas.xmlsoap.org/soap/envelope/');
        $soapBody = $soapResponse->addChild('soapenv:Body', null, 'http://schemas.xmlsoap.org/soap/envelope/');
        $responseElement = $soapBody->addChild('NCBAPaymentNotificationResult', null, '');
        $responseElement->addChild('Result', $msg, '');
        $output = str_replace(' xmlns=""', '', $soapResponse->asXML());
        $output=str_replace('<?xml version="1.0"?>','',$output);
        return response(trim($output),$code)->header('Content-Type', 'application/xml');
    }
}
