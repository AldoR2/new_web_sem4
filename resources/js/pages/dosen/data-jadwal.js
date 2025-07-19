$(document).ready(function () {
    const table = $("#data-jadwal").DataTable({
        searching: true,
        paging: true,
        info: true,
        scrollX: true,
        autoWidth: false,
    });

    $("#tahun-ajaran").on("change", function () {
        const tahunId = $("#tahun-ajaran").val();

        $("#export-pdf").attr(
            "href",
            `/dosen/jadwal/export/pdf?tahun_ajaran=${tahunId}`
        );
        $("#export-excel").attr(
            "href",
            `/dosen/jadwal/export/excel?tahun_ajaran=${tahunId}`
        );

        if (tahunId) {
            fetch(`/dosen/getFilterJadwal?tahun_ajaran=${tahunId}`)
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    data.forEach((item, index) => {
                        table.row.add([
                            item.hari,
                            item.jam.substr(0, 5),
                            item.durasi + " SKS",
                            `${item.matkul?.nama_matkul ?? ""}`,
                            `${item.prodi?.nama_prodi ?? ""}` || "-",
                            item.semester,
                            `${item.ruangan?.nama_ruangan ?? ""}`,
                        ]);
                    });

                    table.draw();
                })
                .catch((error) => console.error("Error fetching data:", error));
        }
    });
});
