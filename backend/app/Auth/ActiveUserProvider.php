<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;

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

        if ($user === null && isset($credentials['email']) && is_string($credentials['email'])) {
            $normalizedEmail = Str::lower(trim($credentials['email']));
            $credentials['email'] = fn ($query) => $query->whereRaw('LOWER(email) = ?', [$normalizedEmail]);
            $user = parent::retrieveByCredentials($credentials);
        }

        return $user?->is_active ? $user : null;
    }
}
