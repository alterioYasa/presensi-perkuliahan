<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisi Presensi</title>
</head>
<body>
    <h2>Revisi Presensi</h2>

    <h3>Detail Mata Kuliah</h3>
    <p><strong>Kode Mata Kuliah:</strong> {{ $kode_mk }}</p>
    <p><strong>Semester:</strong> {{ $semester }}</p>
    <p><strong>Dosen:</strong> {{ $dosen->nama }}</p>

    <form action="{{ route('simpan-revisi-presensi') }}" method="post">
        @csrf
        @method('POST')
        <input type="hidden" name="kode_mk" value="{{ $kode_mk }}">
        <input type="hidden" name="semester" value="{{ $semester }}">

        <label>Pertemuan Ke:</label>
        <input type="number" name="pertemuan" required><br><br>

        <table border="1">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presensi as $p)
                    <tr>
                        <td>{{ $p->nim }}</td>
                        <td>{{ $p->mahasiswa->nama }}</td>
                        <td>
                            <input type="hidden" id="hidden_{{ $p->nim }}" name="status_presensi[{{ $p->nim }}]" value="A">
    
                            <input type="checkbox" name="status_presensi[{{ $p->nim }}]" value="H" {{ $p->status_presensi == 'H' ? 'checked' : '' }} onclick="updateHiddenInput('{{ $p->nim }}', this)">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>
        <label>Realisasi Perkuliahan:</label><br>
        <textarea name="realisasi_perkuliahan" rows="4" cols="50" required></textarea><br><br>

        <br>
        <label>Alasan Revisi:</label><br>
        <textarea name="alasan_revisi" rows="4" cols="50">{{ old('alasan_revisi', $realisasi->alasan_revisi ?? '') }}</textarea>

        <br>
        <br>
        
        <button type="submit">Simpan Revisi</button>
    </form>

    <br>
    <a href="{{ route('dashboard') }}">Kembali</a>
</body>
</html>

<script>
    function updateHiddenInput(nim, checkbox) {
        let hiddenInput = document.getElementById("hidden_" + nim);

        if (checkbox.checked) {
            if (hiddenInput) {
                hiddenInput.remove();
            }
        } else {
            if (!hiddenInput) {
                let newHiddenInput = document.createElement("input");
                newHiddenInput.type = "hidden";
                newHiddenInput.id = "hidden_" + nim;
                newHiddenInput.name = "status_presensi[" + nim + "]";
                newHiddenInput.value = "A";
                checkbox.parentNode.appendChild(newHiddenInput);
            }
        }
    }
</script>