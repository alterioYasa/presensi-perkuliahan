<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Selamat Datang, {{ session('dosen')->nama }}</h2>
    <p>Anda telah berhasil login sebagai dosen.</p>
    <a href="{{ route('logout') }}">Logout</a>
</body>
</html>