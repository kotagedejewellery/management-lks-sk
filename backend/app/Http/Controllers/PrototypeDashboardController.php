<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class PrototypeDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $html = File::get(base_path('../frontend/index.html'));

        $html = str_replace(
            ['href="styles.css"', 'href="overrides.css"', 'src="app.js"', '</head>'],
            [
                'href="'.url('/lks-prototype/styles.css').'"',
                'href="'.url('/lks-prototype/overrides.css').'"',
                'src="'.url('/lks-prototype/app.js').'"',
                '<meta name="csrf-token" content="'.csrf_token().'">'.'</head>',
            ],
            $html,
        );

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function asset(string $asset): Response
    {
        abort_unless(in_array($asset, ['styles.css', 'overrides.css', 'app.js'], true), 404);

        $contentType = $asset === 'app.js' ? 'application/javascript' : 'text/css';

        return response(File::get(base_path("../frontend/{$asset}")))
            ->header('Content-Type', "{$contentType}; charset=UTF-8");
    }
}
