$("#prodi").select2({
    placeholder: "Cari Program Studi",
    width: "100%",
    allowClear: true,
});

$("#semester").select2({
    placeholder: "Cari Semester",
    width: "100%",
    allowClear: true,
});

$("#prodi-dosen").select2({
    placeholder: "Cari Program Studi",
    width: "100%",
    allowClear: true,
});

$("#semester-dosen").select2({
    placeholder: "Cari Semester",
    width: "100%",
    allowClear: true,
});

$("#matkul").select2({
    placeholder: "Pilih Prodi dan semester dahulu",
    width: "100%",
    allowClear: true,
});

$("#tahun-ajaran").select2({
    placeholder: "Cari Tahun Ajaran",
    width: "100%",
    allowClear: true,
});

$(document).ready(function () {
    const table = $("#data-rekap-mahasiswa").DataTable({
        searching: false,
        paging: false,
        info: false,
        language: {
            emptyTable: "Belum ada data presensi ditampilkan.",
        },
        scrollX: false,
        autoWidth: false,

        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass(
                "border border-gray-300 dark:border-gray-600 px-2 py-1"
            );
        },
    });

    $("#tahun-ajaran").on("change", function () {
        const tahunId = $(this).val();

        if (tahunId) {
            fetch(`/mahasiswa/getFilterRekap?tahun_ajaran=${tahunId}`)
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    console.log(data);

                    data.rekap.forEach((item, index) => {
                        const row = [
                            index + 1,
                            item.kode_matkul,
                            item.nama_matkul,
                        ];

                        for (let i = 1; i <= data.totalPertemuan; i++) {
                            const tanggal = item.tanggal_pertemuan[i] ?? null;
                            const status =
                                item.pertemuan[i] ?? (tanggal ? "H" : "-");

                            let bgClass = "text-gray-500";
                            switch (status) {
                                case "H":
                                    bgClass = "text-green-500";
                                    break;
                                case "I":
                                    bgClass = "text-blue-500";
                                    break;
                                case "S":
                                    bgClass = "text-yellow-500";
                                    break;
                                case "A":
                                    bgClass = "text-red-500";
                                    break;
                                default:
                                    bgClass = "text-gray-500";
                                    break;
                            }

                            const title = `${tanggal ?? ""} ${
                                item.nama_dosen?.[i] ?? ""
                            }`.trim();

                            const cell = `<div class="font-semibold ${bgClass}" title="${title}">${status}</div>`;
                            row.push(cell);
                        }

                        row.push(item.kehadiran ?? "");

                        table.row.add(row);
                    });

                    table.draw(); // Refresh tampilan
                })
                .catch((error) => console.error("Gagal ambil data:", error));
        }
    });
});

$(document).ready(function () {
    function loadMatkul(prodiId, semester, oldMatkulId = null) {
        if (prodiId && semester) {
            fetch(`/getMatkulByProdi?prodi=${prodiId}&semester=${semester}`)
                .then((response) => response.json())
                .then((data) => {
                    const mataKuliahSelect = $("#matkul");
                    mataKuliahSelect.empty();

                    // mataKuliahSelect.append(
                    //     '<option value="" hidden>Pilih Matkul</option>'
                    // );
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
    $("#prodi, #semester").on("change", function () {
        const prodiId = $("#prodi").val();
        const semester = $("#semester").val();
        loadMatkul(prodiId, semester);
    });

    // Trigger otomatis saat halaman reload karena error validasi
    const oldProdi = $("#prodi").val();
    const oldSemester = $("#semester").val();
    const oldMatkul = $("#matkul").data("old");

    if (oldProdi && oldSemester) {
        loadMatkul(oldProdi, oldSemester, oldMatkul);
    }
});

$(document).ready(function () {
    function loadMatkulDosen(prodiId, semester, oldMatkulId = null) {
        if (prodiId && semester) {
            fetch(`/dosen/getMatkulDosen?prodi=${prodiId}&semester=${semester}`)
                .then((response) => response.json())
                .then((data) => {
                    const mataKuliahSelect = $("#matkul");
                    mataKuliahSelect.empty();

                    // mataKuliahSelect.append(
                    //     '<option value="" hidden>Pilih Matkul</option>'
                    // );
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
    $("#prodi-dosen, #semester-dosen").on("change", function () {
        const prodiId = $("#prodi-dosen").val();
        const semester = $("#semester-dosen").val();
        loadMatkulDosen(prodiId, semester);
    });

    // Trigger otomatis saat halaman reload karena error validasi
    const oldProdi = $("#prodi-dosen").val();
    const oldSemester = $("#semester-dosen").val();
    const oldMatkul = $("#matkul").data("old");

    if (oldProdi && oldSemester) {
        loadMatkulDosen(oldProdi, oldSemester, oldMatkul);
    }
});
