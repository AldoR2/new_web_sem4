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
                                case "UTS":
                                    bgClass = "text-red-500";
                                    break;
                                case "UAS":
                                    bgClass = "text-red-500";
                                    break;
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
