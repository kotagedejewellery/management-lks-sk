<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;

class GenericFailedPasswordResetLinkResponse implements FailedPasswordResetLinkRequestResponse
{
    public function __construct(protected string $status)
    {
    }

    public function toResponse($request)
    {
        if ($this->status === Password::RESET_THROTTLED) {
            return $request->wantsJson()
                ? new JsonResponse(['message' => __('passwords.throttled')], 429)
                : back()->withInput($request->only('email'))->with('status', 'passwords.throttled');
        }

        return $request->wantsJson()
            ? new JsonResponse(['message' => __('passwords.sent')])
            : back()->withInput($request->only('email'))->with('status', 'passwords.sent');
    }
}
