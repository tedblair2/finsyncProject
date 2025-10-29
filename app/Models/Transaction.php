<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = ['transaction_id','transaction_code','amount','account_number','customer_name','phone_number',
    'status','narrative','ftCr_narration','payment_details','credit_reference','currency','transaction_date','creditdebitflag','balance'];
}
