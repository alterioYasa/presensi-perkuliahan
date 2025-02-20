<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
</head>
<body>
    <h2>Selamat Datang, {{ $dosen->nama }}</h2>
    <p>NIK: {{ $dosen->nik }}</p>

    <h3>Daftar Perkuliahan Anda</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th>Semester</th>
                <th>Tanggal</th>
                <th>SKS</th>
                <th>Jam Mulai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $j)
                <tr>
                    <td>{{ $j->kode_mk }}</td>
                    <td>{{ $j->matakuliah->nama_mk ?? '-' }}</td>
                    <td>{{ $j->semester }}</td>
                    <td>{{ $j->tanggal }}</td>
                    <td>{{ $j->matakuliah->sks }}</td>
                    <td>{{ $j->jam_mulai }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada jadwal perkuliahan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <a href="{{ route('logout') }}">Logout</a>
</body>
</html>