<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Log;
use SimpleXMLElement;

class BanksController extends Controller
{
    //
    public function generateAccessToken(Request $request){
        $validator=Validator::make($request->all(),[
            'grant_type'    => 'required|string',
            'client_id'     => 'required|string',
            'client_secret' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => "Provide valid details"
            ],400);
        }

        if ($request->grant_type !== 'client_credentials') {
            return response()->json(['error' => 'unsupported_grant_type'], 400);
        }

        $bank=Bank::where('username', $request->client_id)->where('password', $request->client_secret)->first();

        if (!$bank) {
            return response()->json(['error' => 'Client does not exist'], 401);
        }

        $token=$bank->createToken($bank['name'],['bank-oauth'], now()->addSeconds(3600))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600
        ], 200);
    }

    public function getTransactions(Request $request){
        $query=$request->input('query','');
        $start=$request->input('start', '2000-01-01 00:00:00');
        $end=$request->input('end', now());
        $page= $request->input('page',1);
        $perPage= $request->input('perPage',15);
        $filter = $request->input('filter','all');

        // $ncbaController=new NCBAController();
        // $ncbaController->saveTransactions($start,$end);

        $transactionsQuery=Transaction::where(function($q) use($query){
             $q->where('transaction_id','LIKE',"%{$query}%")
              ->orWhere('account_number', 'LIKE',"%{$query}%")
              ->orWhere('customer_name', 'LIKE',"%{$query}%")
              ->orWhere('phone_number', 'LIKE',"%{$query}%")
              ->orWhere('narrative', 'LIKE',"%{$query}%")
              ->orWhere('ftCr_narration', 'LIKE',"%{$query}%");
         })->where('transaction_date','>=',$start)->where('transaction_date','<=',$end);

        if($filter=='credit'){
            $transactionsQuery=$transactionsQuery->where('creditdebitflag','credit');
        }elseif($filter=='debit'){
            $transactionsQuery=$transactionsQuery->where('creditdebitflag','debit');
        }

        $pages=$this->calculateTotalpages((clone $transactionsQuery)->get()->count(),$perPage);
        $newPage = ($page > $pages) ? $pages : $page;
        $offset = ($newPage - 1) * $perPage;
        $transactions=$transactionsQuery->orderBy('transaction_date','desc')->offset($offset)->limit($perPage)->get();

        $transactions=$transactions->map(function($transaction){
            $desc=DB::table('transaction_codes')->where('code', $transaction['transaction_code'])->first();
            $transactionDescription=$desc ? $desc->description : '';
            $account=Account::where('account_number', $transaction['account_number'])->first();
            $bankName=$account ? ($account->bank ? $account->bank->name : '') : '';
            $currency=$account ? $account->currency : '';
            return [
                'transaction_id' => $transaction['transaction_id'],
                'transaction_description' => $transactionDescription,
                'amount' => "$currency ".number_format($transaction['amount'],2),
                'bank_name' => $bankName,
                'account_number' => $transaction['account_number'],
                'customer_name' => $transaction['customer_name'],
                'phone_number' => $transaction['phone_number'],
                'status' => $transaction['status'],
                'narrative' => $transaction['narrative'],
                'ftCr_narration' => $transaction['ftCr_narration'],
                'transaction_date'=> $transaction['transaction_date'],
                'creditdebitflag'=> $transaction['creditdebitflag']
            ];
        });

        return response()->json([
            'isSuccess'=>true,
            'data'=> $transactions,
            'pages'=>$pages
        ]);
    }

    public  function exportTransactions(Request $request){
        $usdToKesRate=$request->input('usdToKesRate',129);
        $query=$request->input('query','');
        $start=$request->input('start', '2000-01-01 00:00:00');
        $end=$request->input('end', now());
        $filter = $request->input('filter','all');

        $csv_title="transactions_".date('Ymd_His').".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$csv_title\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'X-Filename' => $csv_title
        ];

        $transactionsQuery=Transaction::where(function($q) use($query){
             $q->where('transaction_id','LIKE',"%{$query}%")
              ->orWhere('account_number', 'LIKE',"%{$query}%")
              ->orWhere('customer_name', 'LIKE',"%{$query}%")
              ->orWhere('phone_number', 'LIKE',"%{$query}%")
              ->orWhere('narrative', 'LIKE',"%{$query}%")
              ->orWhere('ftCr_narration', 'LIKE',"%{$query}%");
         })->where('transaction_date','>=',$start)->where('transaction_date','<=',$end);

        if($filter=='credit'){
            $transactionsQuery=$transactionsQuery->where('creditdebitflag','credit');
        }elseif($filter=='debit'){
            $transactionsQuery=$transactionsQuery->where('creditdebitflag','debit');
        }

        $transactions=$transactionsQuery->orderBy('transaction_date','desc')->get();

        return response()->stream(function () use($transactions,$usdToKesRate) {
            $handle = fopen('php://output', 'w');
            $csvHeaders = ['Bank Name','Account Number','Credit(USD)','Debit(USD)'];
            fputcsv($handle, $csvHeaders);
            $totalCredit = 0;
            $totalDebit  = 0;
            foreach ($transactions as $transaction){
                $account=Account::where('account_number', $transaction['account_number'])->first();
                $bankName=$account ? ($account->bank ? $account->bank->name : '') : '';
                $currency=$account ? $account->currency : 'KES';
                $amountUSD = ($currency == 'KES') ? $transaction['amount'] / $usdToKesRate : $transaction['amount'];
                if ($transaction['creditdebitflag'] == 'credit') {
                    $credit = number_format($amountUSD, 2);
                    $debit  = '';
                    $totalCredit += $amountUSD;
                } else {
                    $credit = '';
                    $debit  = number_format($amountUSD, 2);
                    $totalDebit += $amountUSD;
                }
                fputcsv($handle, [
                    $bankName,
                    $transaction['account_number'],
                    $credit,
                    $debit
                ]);
            }
            fputcsv($handle, ['Total(USD)','',number_format($totalCredit, 2),number_format($totalDebit, 2)]);
            fclose($handle);
        },200,$headers);
    }

    public function generateNewBankKey(Request $request){
        $id=$request->input('id',0);
        $bank=Bank::find($id);

        if(!$bank){
            return response()->json([
                'error' => "Bank not found"
            ],404);
        }

        $token=$bank->createToken($bank['name'],['bank-oauth'])->plainTextToken;
        $bank->update([
            'secret_key' => $token
        ]);

        return response()->json([
            'isSuccess'=>true,
            'data'=> [
                'name' => $bank->name,
                'username' => $bank->username,
                'password' => $bank->password,
                'secret_key'=> $bank->secret_key
            ]
        ]);
    }

    public function createBankDetails(Request $request){
        $validator=Validator::make($request->all(),[
            'name' =>'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => "Provide valid details"
            ],400);
        }

        $password=$this->generateRandomName(16);
        $username=$this->generateRandomName(20);
        
        $bank=Bank::create([
            'name' => $request->name,
            'username' => $username,
            'password' => $password
        ]);
        $token=$bank->createToken($bank['name'],['bank-oauth'])->plainTextToken;
        $bank->update([
            'secret_key' => $token
        ]);

        return response()->json([
            'isSuccess'=>true,
            'data'=> [
                'name' => $bank->name,
                'username' => $bank->username,
                'password' => $bank->password,
                'secret_key'=> $bank->secret_key
            ]
        ]);
    }

    public function generateRandomName($length=16){
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }

    public function calculateTotalpages($itemCount, $perPage){
        if($itemCount==0){
            return 1;
        }else{
            return floor(($itemCount + $perPage - 1) / $perPage); 
        }
    }
}
