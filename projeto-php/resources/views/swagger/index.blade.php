<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scalar - Catálogo de Produtos</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div id="app"></div>

    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
    <script>
        Scalar.createApiReference('#app', {
            url: @json($specUrl),
            theme: 'kepler',
            darkMode: true,
            hideDownloadButton: false,
            authentication: {
                preferredSecurityScheme: 'BearerAuth',
            },
            metaData: {
                title: 'Catálogo de Produtos',
            },
        })
    </script>
</body>
</html>
