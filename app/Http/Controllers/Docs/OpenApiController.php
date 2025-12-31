<?php

namespace App\Http\Controllers\Docs;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class OpenApiController extends Controller
{
    public function ui()
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sarana Cashier POS API</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
  </head>
  <body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
      window.ui = SwaggerUIBundle({
        url: '/docs/openapi.yaml',
        dom_id: '#swagger-ui',
        presets: [SwaggerUIBundle.presets.apis],
        persistAuthorization: true
      })
    </script>
  </body>
</html>
HTML;
        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }

    public function spec(string $extension = 'yaml')
    {
        $extension = in_array($extension, ['yaml', 'yml'], true) ? $extension : 'yaml';

        $path = base_path("docs/openapi.$extension");

        if (! file_exists($path)) {
            $fallbackExtension = $extension === 'yaml' ? 'yml' : 'yaml';
            $fallbackPath = base_path("docs/openapi.$fallbackExtension");

            if (! file_exists($fallbackPath)) {
                throw new FileNotFoundException('Spec not found');
            }

            $path = $fallbackPath;
        }

        return response()->file($path, [
            'Content-Type' => 'application/yaml',
        ]);
    }
}
