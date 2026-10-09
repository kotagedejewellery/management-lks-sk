<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

class ActiveUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve only accounts that may receive a password reset link.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        return $user?->is_active ? $user : null;
    }
}
