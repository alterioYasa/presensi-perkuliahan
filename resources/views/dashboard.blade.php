<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a href="{{ route('input-presensi', ['kode_mk' => $j->kode_mk, 'semester' => $j->semester]) }}">
                            Input Presensi
                        </a>

                        <a href="{{ route('input-revisi-presensi', ['kode_mk' => $j->kode_mk, 'semester' => $j->semester]) }}">
                            Revisi Presensi
                        </a>

                        <a class="btn btn-primary print-rekap"
                            href="#"
                            data-kode-mk="{{ $j->kode_mk }}"
                            data-semester="{{ $j->semester }}">
                            Print Rekap
                        </a>
                        
                    </td>   
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

    <div id="printModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Print Rekap Kehadiran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed id="pdfViewer" height="1000px" type="application/pdf" width="100%">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>

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
