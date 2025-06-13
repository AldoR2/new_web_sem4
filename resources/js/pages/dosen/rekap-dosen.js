$(document).ready(function () {
    const table = $("#data-rekap-dosen").DataTable({
        scrollX: true,

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
                "border border-gray-300 dark:border-gray-800 px-2 py-1"
            );
        },
    });

    $("#tahun-ajaran").on("change", function () {
        const tahunId = $(this).val();

        if (tahunId) {
            fetch(`/dosen/getFilterRekap?tahun_ajaran=${tahunId}`)
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    data.rekap.forEach((item, index) => {
                        const row = [
                            index + 1,
                            item.nama_prodi,
                            item.semester,
                            item.nama_matkul,
                        ];

                        for (let i = 0; i < data.totalPertemuan; i++) {
                            const status = item.status_pertemuan[i] ?? null;
                            // const status = tanggal ? "M" : "-";

                            const bgClass =
                                status === "M"
                                    ? "text-green-500"
                                    : "text-gray-500";

                            const cell = `<div class="font-semibold ${bgClass}" title="${
                                item.nama_dosen ?? ""
                            }">${status}</div>`;
                            row.push(cell);
                        }

                        row.push(item.total_pertemuan);
                        table.row.add(row);
                    });

                    table.draw();
                })
                .catch((error) => console.error("Gagal ambil data:", error));
        }
    });
});
