<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\LksDocumentVerification;
use Illuminate\Http\Response;

class DocumentVerificationController extends Controller
{
    public function show(LksDocumentVerification $verification): Response
    {
        $verification->loadMissing('period');
        abort_unless($verification->period->status === 'closed', 404);

        return response()
            ->view('lks.document-verification', ['verification' => $verification])
            ->header('Cache-Control', 'no-store, private');
    }
}
