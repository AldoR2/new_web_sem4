const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

$(document).ready(function () {
    const table = $("#data-mahasiswa").DataTable({
        searching: true,
        paging: true,
        info: true,
        scrollX: true,
        autoWidth: false,
    });

    $("#prodi, #semester").on("change", function () {
        const prodiId = $("#prodi").val();
        const semester = $("#semester").val();
        if (semester || prodiId) {
            fetch(
                `/admin/getFilterMahasiswa?prodi=${prodiId}&semester=${semester}`
            )
                .then((response) => response.json())
                .then((data) => {
                    table.clear();

                    data.forEach((item, index) => {
                        const fotoUrl = item.foto
                            ? `/storage/${item.foto}`
                            : "/images/profil-kosong.png";

                        table.row.add([
                            `<div style="text-align:left;">${index + 1}</div>`,
                            `<div class="w-10 h-10 bg-red-200 rounded-full overflow-hidden">
                                <img src="${fotoUrl}" alt="Photo" class="w-full h-full object-cover">
                            </div>`,
                            item.nim,
                            item.nama,
                            item.jenis_kelamin === "L"
                                ? "Laki-laki"
                                : "Perempuan",
                            item.email,
                            `${item.prodi?.jenjang ?? ""} ${
                                item.prodi?.nama_prodi ?? ""
                            }` || "-",
                            item.semester,
                            `<div class="flex gap-2 justify-center">
                                <button @click="openView = true; $nextTick(() => loadMahasiswaDetail(${item.id}))"
                                        class="cursor-pointer px-2 py-1 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white rounded-md">
                                    <i class="bi bi-eye text-lg"></i>
                                </button>
                                <a href="/admin/master-mahasiswa/${item.id}/edit" class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                                <form action="/admin/master-mahasiswa/${item.id}" method="POST" class="form-hapus inline-block">
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

window.loadMahasiswaDetail = function (id) {
    $.ajax({
        url: "/admin/master-mahasiswa/" + id,
        method: "GET",
        success: function (res) {
            if (res.foto) {
                $("#foto").attr("src", "/storage/" + res.foto);
            } else {
                $("#foto").attr("src", "/images/profil-kosong.png");
            }
            $("#nama").val(res.nama);
            $("#nim").val(res.nim);
            $("#rfid").val(res.rfid ?? "-");
            $("#jenis_kelamin").val(
                res.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan"
            );
            $("#agama").val(res.agama);
            $("#tempat_lahir").val(res.tempat_lahir);
            $("#tgl_lahir").val(res.tgl_lahir);
            $("#email").val(res.email);
            $("#no_telp").val(res.no_telp);
            $("#alamat").val(res.alamat);
            $("#prodi-mahasiswa").val(
                res.prodi.jenjang + " " + res.prodi.nama_prodi
            );
            $("#no_telp").val(res.no_telp);
            $("#tahun_masuk").val(res.tahun_masuk);
            $("#semester-mahasiswa").val(res.semester);
            $("#provinsi").val(res.provinsi.name);
            $("#kota").val(res.kota.name);
            $("#kecamatan").val(res.kecamatan.name);
            $("#kelurahan").val(res.kelurahan.name);
        },
        error: function () {
            alert("Gagal mengambil data dosen");
        },
    });
};
