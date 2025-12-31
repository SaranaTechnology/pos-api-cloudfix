<?php

namespace App\Http\Controllers\API\V1\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function ui(): Response
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarana Cashier POS API - Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <link rel="icon" type="image/png" href="https://unpkg.com/swagger-ui-dist@5/favicon-32x32.png" sizes="32x32">
    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        .swagger-ui .topbar { display: none; }
        .custom-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .custom-header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .custom-header .subtitle { opacity: 0.9; font-size: 14px; margin-top: 5px; }
        .custom-header .env-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <div>
            <h1>Sarana Cashier POS API</h1>
            <div class="subtitle">Point of Sale & Self-Order System - Laravel Octane + FrankenPHP</div>
        </div>
        <div class="env-badge">{{ENV}}</div>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "/api/docs/openapi.yaml",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
                plugins: [SwaggerUIBundle.plugins.DownloadUrl],
                layout: "StandaloneLayout",
                persistAuthorization: true,
                displayRequestDuration: true,
                filter: true,
                tryItOutEnabled: true
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
HTML;

        $env = strtoupper(config('app.env', 'production'));
        $html = str_replace('{{ENV}}', $env, $html);

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    public function spec(): Response
    {
        $path = base_path('openapi.yaml');

        if (!File::exists($path)) {
            return response('OpenAPI specification not found', 404);
        }

        $content = File::get($path);
        $appUrl = config('app.url', request()->getSchemeAndHttpHost());
        $content = preg_replace(
            '/url: http:\/\/localhost:\d+\/api\/v1/',
            "url: {$appUrl}/api/v1",
            $content
        );

        return response($content, 200)
            ->header('Content-Type', 'application/x-yaml')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function specJson(): Response
    {
        $path = base_path('openapi.yaml');

        if (!File::exists($path)) {
            return response()->json(['error' => 'OpenAPI specification not found'], 404);
        }

        $yaml = File::get($path);
        $spec = yaml_parse($yaml);

        if ($spec === false) {
            return response('YAML parsing not available', 500);
        }

        return response()->json($spec)->header('Access-Control-Allow-Origin', '*');
    }
}
