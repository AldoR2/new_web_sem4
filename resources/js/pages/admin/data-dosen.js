$(document).ready(function () {
    const table = $("#data-dosen").DataTable({
        searching: true,
        paging: true,
        info: true,
        scrollX: true,
        autoWidth: false,
    });
});

window.loadDosenDetail = function (id) {
    $.ajax({
        url: "/admin/master-dosen/" + id,
        method: "GET",
        success: function (res) {
            console.log(res);

            if (res.foto) {
                $("#foto").attr("src", "/storage/" + res.foto);
            } else {
                $("#foto").attr("src", "/images/profil-kosong.png");
            }
            $("#nama").val(res.nama);
            $("#nip").val(res.nip);
            $("#jenis_kelamin").val(
                res.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan"
            );
            $("#agama").val(res.agama);
            $("#tempat_lahir").val(res.tempat_lahir);
            $("#tgl_lahir").val(res.tgl_lahir);
            $("#email").val(res.email);
            $("#no_telp").val(res.no_telp);
            $("#alamat").val(res.alamat);
            $("#prodi").val(res.prodi.jenjang + " " + res.prodi.nama_prodi);
            $("#no_telp").val(res.no_telp);
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
