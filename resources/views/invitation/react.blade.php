<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Undangan</title>
    @viteReactRefresh
    @vite('resources/js/invitation-app/main.jsx')
</head>
<body style="margin:0;">
    <div id="invitation-root"></div>
</body>
</html>
