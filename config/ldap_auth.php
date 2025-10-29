<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LDAP Connection
    |--------------------------------------------------------------------------
    */
    'connection' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Provider
    |--------------------------------------------------------------------------
    |
    | Use DatabaseUserProvider to sync LDAP users into the local database.
    |
    */
    'provider' => LdapRecord\Laravel\Auth\DatabaseUserProvider::class,

    /*
    |--------------------------------------------------------------------------
    | LDAP Model
    |--------------------------------------------------------------------------
    */
    // The LDAP model used for LDAP queries. Must extend LdapRecord\Models\Model.
    'model' => App\Ldap\User::class,

    /*
    |--------------------------------------------------------------------------
    | Authentication Rules & Scopes
    |--------------------------------------------------------------------------
    */
    'rules' => [],
    'scopes' => [],

    /*
    |--------------------------------------------------------------------------
    | Database Synchronization
    |--------------------------------------------------------------------------
    */
    'database' => [
        // Local Eloquent model used when syncing LDAP users into the DB
        'model' => App\Models\User::class,

        // Don't sync passwords from LDAP.
        'sync_passwords' => false,

        // Map LDAP attributes to local user fields
        'sync_attributes' => [
            'name'  => 'cn',
            'email' => 'mail',
        ],

        // Match existing users by email
        'sync_existing' => [
            'email' => 'mail',
        ],
    ],
];