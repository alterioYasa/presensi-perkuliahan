@extends('layouts.app')

@section('title', 'Revisi Presensi')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Revisi Presensi</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">Detail Mata Kuliah</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Mata Kuliah:</strong> {{ $kode_mk }}</p>
                    <p><strong>Semester:</strong> {{ $semester }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Dosen:</strong> {{ $dosen->nama }}</p>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Pilih Pertemuan</h5>
            <form action="{{ route('simpan-revisi-presensi') }}" method="POST" id="formPresensi">
                @csrf
                <input type="hidden" name="kode_mk" value="{{ $kode_mk }}">
                <input type="hidden" name="semester" value="{{ $semester }}">

                <!-- Dropdown Pilih Pertemuan -->
                <div class="mb-3">
                    <label for="pertemuan" class="form-label">Pilih Pertemuan:</label>
                    <select id="pertemuan" name="pertemuan" class="form-select" required>
                        <option value="" selected>-- Pilih Pertemuan --</option>
                        @foreach ($daftarPertemuan as $p)
                            <option value="{{ $p }}">
                                Pertemuan Ke-{{ $p }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div id="loading" class="text-center text-muted d-none">
                    <p>Memuat data presensi...</p>
                </div>

                <!-- Daftar Mahasiswa -->
                <div id="presensiTableContainer" class="d-none">
                    <h5 class="mb-3">Daftar Mahasiswa</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>
                                        Status Kehadiran
                                        <input type="checkbox" id="checkAll" class="form-check-input ms-2">
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="presensiTable">

                            </tbody>
                        </table>
                    </div>

                    <!-- Realisasi Perkuliahan -->
                    <div class="mb-3">
                        <label for="realisasi_perkuliahan" class="form-label">Realisasi Perkuliahan:</label>
                        <textarea id="realisasi_perkuliahan" name="realisasi_perkuliahan" class="form-control" rows="4" required></textarea>
                    </div>

                    <!-- Alasan Revisi -->
                    <div class="mb-3">
                        <label for="alasan_revisi" class="form-label">Alasan Revisi:</label>
                        <textarea id="alasan_revisi" name="alasan_revisi" class="form-control" rows="4"></textarea>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">Simpan Revisi</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </div>
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
        $(".check-item").each(function () {
            updateHiddenInput($(this).data("nim"), this);
        });
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

    $("#pertemuan").change(function () {
        let pertemuan = $(this).val();

        if (!pertemuan) {
            $("#presensiTableContainer").addClass("d-none");
            return;
        }

        $("#loading").removeClass("d-none");
        $("#presensiTableContainer").addClass("d-none");

        $.ajax({
            url: `/get-presensi/{{ $kode_mk }}/{{ $semester }}/${pertemuan}`,
            method: "GET",
            success: function (response) {
                $("#loading").addClass("d-none");
                $("#presensiTableContainer").removeClass("d-none");

                let tableContent = "";
                response.presensi.forEach((p) => {
                    let checked = p.status_presensi === "H" ? "checked" : "";
                    tableContent += `
                        <tr>
                            <td>${p.nim}</td>
                            <td>${p.nama}</td>
                            <td class="text-center">
                                <input type="hidden" name="status_presensi[${p.nim}]" value="A">
                                <input type="checkbox" name="status_presensi[${p.nim}]" value="H" class="form-check-input check-item" ${checked}>
                            </td>
                        </tr>
                    `;
                });

                $("#presensiTable").html(tableContent);
                $("#realisasi_perkuliahan").val(response.realisasi_perkuliahan);
                $("#alasan_revisi").val(response.alasan_revisi || "");
            },
            error: function (xhr) {
                $("#loading").addClass("d-none");
                alert("Gagal mengambil data presensi. Silakan coba lagi.");
                console.error("AJAX Error:", xhr.responseText);
            }
        });
    });
});

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
@endpush