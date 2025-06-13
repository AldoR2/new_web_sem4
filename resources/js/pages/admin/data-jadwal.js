const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

$("#prodi").select2({
    placeholder: "Cari Program Studi",
    width: "100%",
    allowClear: true,
});

$("#dosen").select2({
    placeholder: "Cari Dosen",
    width: "100%",
    allowClear: true,
});

$("#matkul").select2({
    placeholder: "Pilih Prodi dan semester dahulu",
    width: "100%",
    allowClear: true,
});

$("#ruangan").select2({
    placeholder: "Cari Ruangan",
    width: "100%",
    allowClear: true,
});

$("#semester").select2({
    placeholder: "Cari Semester",
    width: "100%",
    allowClear: true,
});

$("#tahun-ajaran").select2({
    placeholder: "Cari Tahun Ajaran",
    width: "100%",
    allowClear: true,
});

$(document).ready(function () {
    const table = $("#data-jadwal").DataTable({
        searching: true,
        paging: true,
        info: true,
        scrollX: true,
        autoWidth: false,
    });

    $("#dosen ,#prodi, #tahun-ajaran").on("change", function () {
        const dosenId = $("#dosen").val();
        const prodiId = $("#prodi").val();
        const tahunId = $("#tahun-ajaran").val();

        if (dosenId || prodiId || tahunId) {
            fetch(
                `/admin/getFilterJadwal?dosen=${dosenId}&prodi=${prodiId}&tahun_ajaran=${tahunId}`
            )
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    data.forEach((item, index) => {
                        table.row.add([
                            index + 1,
                            item.hari,
                            item.jam.substr(0, 5),
                            item.durasi + " SKS",
                            item.dosen?.nama ?? "",
                            `${item.prodi?.jenjang ?? ""} ${
                                item.prodi?.nama_prodi ?? ""
                            }` || "-",
                            `${item.tahun?.tahun_awal + " /"} ${
                                item.tahun?.tahun_akhir ?? ""
                            } ${item.tahun?.keterangan ?? ""}` || "-",
                            item.semester,
                            `${item.matkul?.nama_matkul ?? ""}`,
                            `${item.ruangan?.nama_ruangan ?? ""}`,
                            `<div class="flex gap-2 justify-center">
                                <a href="/admin/master-jadwal/${item.id}" class="cursor-pointer px-2 py-1 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white rounded-md">
                                    <i class="bi bi-card-text text-lg"></i>
                                </a>
                                <form action="/admin/master-jadwal/${item.id}" method="POST" class="form-hapus inline-block">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-md">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                              </div>`,
                        ]);
                    });

                    table.draw();
                })
                .catch((error) => console.error("Error fetching data:", error));
        }
    });
});

$(document).ready(function () {
    function loadMatkul(prodiId, semester, tahunAjaran, oldMatkulId = null) {
        if (prodiId && semester && tahunAjaran) {
            fetch(
                `/admin/getMatkulByTahun?prodi=${prodiId}&semester=${semester}&tahun=${tahunAjaran}`
            )
                .then((response) => response.json())
                .then((data) => {
                    const mataKuliahSelect = $("#matkul");
                    mataKuliahSelect.empty();

                    data.forEach((item) => {
                        mataKuliahSelect.append(
                            `<option value="${item.id}" ${
                                item.id == oldMatkulId ? "selected" : ""
                            }>${item.nama_matkul}</option>`
                        );
                    });
                })
                .catch((error) => {
                    console.error("Error fetching mata kuliah:", error);
                });
        }
    }

    // Trigger saat user ganti
    $("#prodi, #semester, #tahun-ajaran").on("change", function () {
        const prodiId = $("#prodi").val();
        const semester = $("#semester").val();
        const tahunAjaran = $("#tahun-ajaran").val();
        loadMatkul(prodiId, semester, tahunAjaran);
    });

    // Trigger otomatis saat halaman reload karena error validasi
    const oldProdi = $("#prodi").val();
    const oldSemester = $("#semester").val();
    const oldTahunAjaran = $("#tahun-ajaran").val();
    const oldMatkul = $("#matkul").data("old");

    if (oldProdi && oldSemester && oldTahunAjaran) {
        loadMatkul(oldProdi, oldSemester, oldTahunAjaran, oldMatkul);
    }
});
