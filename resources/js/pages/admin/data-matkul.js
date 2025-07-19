const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

$(document).ready(function () {
    const table = $("#data-matkul").DataTable({
        searching: true, // Aktifkan pencarian
        paging: true, // Aktifkan pagination
        info: true, // Menampilkan informasi tabel
        scrollX: true, // Aktifkan scroll horizontal
        autoWidth: false, // Hindari ukuran otomatis
    });

    $("#prodi, #semester, #tahun_ajaran").on("change", function () {
        const prodiId = $("#prodi").val();
        const semester = $("#semester").val();
        const tahunAjaran = $("#tahun_ajaran").val();

        if (semester || prodiId || tahunAjaran) {
            fetch(
                `/admin/getFilterMatkul?prodi=${prodiId}&semester=${semester}&tahun_ajaran=${tahunAjaran}`
            )
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    data.forEach((item, index) => {
                        console.log(item);
                        table.row.add([
                            `<div style="text-align:left;">${index + 1}</div>`,
                            `<div style="text-align:left;">${item.nama_matkul}</div>`,
                            `${item.prodi?.nama_prodi ?? ""}` || "",
                            `<div style="text-align:left;">${
                                item.durasi_matkul + " SKS"
                            }</div>`,
                            `${item.tahun_ajaran?.tahun_awal + "/" ?? ""} ${
                                item.tahun_ajaran?.tahun_akhir ?? ""
                            } ${item.tahun_ajaran?.keterangan ?? ""}` || "",
                            item.semester,

                            `<div class="flex gap-2 justify-center">
                                <a href="/admin/master-matkul/${item.id}/edit" class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                                <form action="/admin/master-matkul/${item.id}" method="POST" class="form-hapus inline-block">
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
