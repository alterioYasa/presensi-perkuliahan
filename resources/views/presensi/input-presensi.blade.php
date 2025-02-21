@extends('layouts.app')

@section('title', 'Input Presensi')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Input Presensi</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">Detail Mata Kuliah</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Mata Kuliah:</strong> {{ $jadwalMatakuliah->kode_mk }}</p>
                    <p><strong>Nama Mata Kuliah:</strong> {{ $jadwalMatakuliah->matakuliah->nama_mk }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Semester:</strong> {{ $jadwalMatakuliah->semester }}</p>
                    <p><strong>Jam Mulai:</strong> {{ $jadwalMatakuliah->jam_mulai }}</p>
                    <p><strong>Dosen:</strong> {{ $dosen->nama }}</p>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Daftar Mahasiswa</h5>
            <form action="{{ route('simpan-presensi') }}" method="POST">
                @csrf
                <input type="hidden" name="kode_mk" value="{{ $kode_mk }}">
                <input type="hidden" name="semester" value="{{ $semester }}">

                <!-- Pertemuan Ke -->
                <div class="mb-3">
                    <label for="pertemuan" class="form-label">Pertemuan Ke:</label>
                    <input type="number" id="pertemuan" name="pertemuan" class="form-control" min="1" required>
                </div>


                <!-- Tabel Mahasiswa -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>
                                    Kehadiran
                                    <input type="checkbox" id="checkAll" class="form-check-input ms-2">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peserta as $m)
                                <tr>
                                    <td>{{ $m->nim }}</td>
                                    <td>{{ $m->mahasiswa->nama }}</td>
                                    <td class="text-center">
                                        <input type="hidden" name="status_presensi[{{ $m->nim }}]" value="A">
                                        <input type="checkbox" name="status_presensi[{{ $m->nim }}]" value="H" class="form-check-input check-item">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Realisasi Perkuliahan -->
                <div class="mb-3">
                    <label for="realisasi_perkuliahan" class="form-label">Realisasi Perkuliahan:</label>
                    <textarea id="realisasi_perkuliahan" name="realisasi_perkuliahan" class="form-control" rows="4" required></textarea>
                </div>

                <!-- Tombol Simpan -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Simpan Presensi</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $("#checkAll").click(function () {
        $(".check-item").prop("checked", this.checked);
    });

    $(".check-item").change(function () {
        let totalCheckbox = $(".check-item").length;
        let checkedCheckbox = $(".check-item:checked").length;

        if (checkedCheckbox === totalCheckbox) {
            $("#checkAll").prop("checked", true);
        } else {
            $("#checkAll").prop("checked", false);
        }
    });
});
</script>
@endpush