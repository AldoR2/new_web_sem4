$(document).ready(function () {
    const table = $("#data-admin").DataTable({
        searching: true,
        paging: true,
        info: true,
        scrollX: true,
        autoWidth: false,
    });
});

window.loadAdminDetail = function (id) {
    $.ajax({
        url: "/superadmin/master-admin/" + id,
        method: "GET",
        success: function (res) {
            console.log(res);

            if (res.foto) {
                $("#foto").attr("src", "/storage/" + res.foto);
            } else {
                $("#foto").attr("src", "/images/profil-kosong.png");
            }
            $("#nama").val(res.nama);
            $("#jenis_kelamin").val(
                res.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan"
            );
            $("#agama").val(res.agama);
            $("#tempat_lahir").val(res.tempat_lahir);
            $("#tgl_lahir").val(res.tgl_lahir);
            $("#email").val(res.email);
            $("#no_telp").val(res.no_telp);
            $("#alamat").val(res.alamat);
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
