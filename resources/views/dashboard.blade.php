@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Selamat Datang, {{ $dosen->nama }}</h2>
        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
    </div>
    <p><strong>NIK:</strong> {{ $dosen->nik }}</p>

    <!-- Tombol Generate Rekap -->
    <div class="mb-3">
        <a href="{{ route('generate-rekap-harian') }}" class="btn btn-primary" target="_blank">
            Generate & Download Rekap Harian
        </a>
    </div>    

    <!-- Daftar Perkuliahan -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Daftar Perkuliahan Anda</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Tanggal</th>
                        <th>SKS</th>
                        <th>Jam Mulai</th>
                        <th>Aksi</th>
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
                            <td>
                                <a href="{{ route('input-presensi', ['kode_mk' => $j->kode_mk, 'semester' => $j->semester]) }}" class="btn btn-success btn-sm">Input Presensi</a>

                                <a href="{{ route('input-revisi-presensi', ['kode_mk' => $j->kode_mk, 'semester' => $j->semester]) }}" class="btn btn-warning btn-sm">Revisi Presensi</a>

                                <button class="btn btn-primary btn-sm print-rekap" 
                                    data-kode-mk="{{ $j->kode_mk }}" 
                                    data-semester="{{ $j->semester }}">
                                    Print Rekap
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada jadwal perkuliahan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Print Rekap -->
<div id="printModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title">Print Rekap Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="pdfViewer" height="600px" type="application/pdf" width="100%">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $(".print-rekap").click(async function () {
        let kodeMk = $(this).data("kode-mk");
        let semester = $(this).data("semester");
        let endpoint = `/rekap-presensi/${kodeMk}/${semester}`;

        Swal.fire({
            title: "Loading...",
            text: "Sedang menyiapkan PDF.",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            let response = await fetch(endpoint, {
                method: "GET",
                headers: { "Accept": "application/json" }
            });

            if (!response.ok) {
                throw new Error("Gagal mengambil data PDF.");
            }

            let data = await response.json();
            let base64PDF = data.pdf_base64;

            let binary = atob(base64PDF);
            let array = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                array[i] = binary.charCodeAt(i);
            }
            let blob = new Blob([array], { type: "application/pdf" });
            let blobUrl = URL.createObjectURL(blob);

            $("#pdfViewer").attr("src", blobUrl);

            Swal.close();

            let modal = new bootstrap.Modal(document.getElementById("printModal"));
            modal.show();

        } catch (error) {
            Swal.close();
            console.error("Error PDF:", error);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Gagal memuat PDF, Tolong coba lagi."
            });
        }
    });
});
</script>
@endpush