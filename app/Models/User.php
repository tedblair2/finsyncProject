<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;

class User extends Authenticatable implements LdapAuthenticatable
{
    use AuthenticatesWithLdap;

    protected $fillable = [
        'name', 'email',
    ];
}