<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Page not found</title>
</head>
<body>
    @if ($errors->any())    
    <div class="alert alert-danger text-center">
        @foreach ($errors->all() as $error)
            <h1>{{ $error }}</h1>
        @endforeach
    </div>
    @endif
</body>
</html>