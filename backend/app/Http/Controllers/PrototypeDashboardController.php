<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class PrototypeDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $html = File::get(base_path('../frontend/index.html'));
        $assetUrl = fn (string $asset): string => url("/lks-prototype/{$asset}").'?v='.File::lastModified(base_path("../frontend/{$asset}"));

        $html = str_replace(
            ['href="styles.css"', 'href="overrides.css"', 'src="app.js"', '</head>'],
            [
                'href="'.$assetUrl('styles.css').'"',
                'href="'.$assetUrl('overrides.css').'"',
                'src="'.$assetUrl('app.js').'"',
                '<meta name="csrf-token" content="'.csrf_token().'">'.'</head>',
            ],
            $html,
        );

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'private, no-store');
    }

    public function asset(string $asset): Response
    {
        abort_unless(in_array($asset, ['styles.css', 'overrides.css', 'app.js'], true), 404);

        $contentType = $asset === 'app.js' ? 'application/javascript' : 'text/css';

        return response(File::get(base_path("../frontend/{$asset}")))
            ->header('Content-Type', "{$contentType}; charset=UTF-8")
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }
}
