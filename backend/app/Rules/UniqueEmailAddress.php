<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmailAddress implements ValidationRule
{
    public function __construct(private readonly ?string $ignoreUserId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = User::query()
            ->matchingEmail((string) $value)
            ->when($this->ignoreUserId !== null, fn ($query) => $query->whereKeyNot($this->ignoreUserId))
            ->exists();

        if ($exists) {
            $fail('Alamat email sudah digunakan.');
        }
    }
}
