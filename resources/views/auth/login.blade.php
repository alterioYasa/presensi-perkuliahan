<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Presensi</title>
</head>
<body>
    <h2>Login Presensi</h2>
    
    @if ($errors->any())
        <p style="color:red;">{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('login.post') }}" method="post">
        @csrf
        <label>NIK:</label>
        <input type="text" name="nik" required><br>

        <label>PIN:</label>
        <input type="password" name="pin" required><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
