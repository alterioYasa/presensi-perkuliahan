<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Presensi</title>
</head>
<body>
    <h2>Input Presensi</h2>

    <h3>Detail Mata Kuliah</h3>
    <p><strong>Kode Mata Kuliah:</strong> {{ $jadwalMatakuliah->kode_mk }}</p>
    <p><strong>Nama Mata Kuliah:</strong> {{ $jadwalMatakuliah->matakuliah->nama_mk }}</p>
    <p><strong>Semester:</strong> {{ $jadwalMatakuliah->semester }}</p>
    <p><strong>Jam Mulai:</strong> {{ $jadwalMatakuliah->jam_mulai }}</p>
    <p><strong>Dosen:</strong> {{ $dosen->nama }}</p>

    <h3>Daftar Mahasiswa</h3>
    <form action="{{ route('simpan-presensi') }}" method="post">
        @csrf
        <input type="hidden" name="kode_mk" value="{{ $kode_mk }}">
        <input type="hidden" name="semester" value="{{ $semester }}">

        <label>Pertemuan Ke:</label>
        <input type="number" name="pertemuan" required><br><br>

        <table border="1">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peserta as $m)
                    <tr>
                        <td>{{ $m->nim }}</td>
                        <td>{{ $m->mahasiswa->nama }}</td>
                        <td>
                            <input type="hidden" name="status_presensi[{{ $m->nim }}]" value="A">
                            <input type="checkbox" name="status_presensi[{{ $m->nim }}]" value="H">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <label>Realisasi Perkuliahan:</label><br>
        <textarea name="realisasi_perkuliahan" rows="4" cols="50" required></textarea><br><br>

        <button type="submit">Simpan Presensi</button>
    </form>

    <br>
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>