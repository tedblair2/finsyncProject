<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Bank extends Authenticatable
{
    //
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable=['name','username','password','secret_key'];

    public function accounts()
    {
        return $this->hasMany(Account::class,'bank_id');
    }

}
