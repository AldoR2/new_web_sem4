document.addEventListener("DOMContentLoaded", function () {
    const fotoInput = document.getElementById("foto");
    const previewImg = document.getElementById("previewImage");
    const resetBtn = document.getElementById("resetFoto");

    const defaultImage = "/images/profil-kosong.png";

    if (fotoInput && previewImg) {
        fotoInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (resetBtn && previewImg) {
        resetBtn.addEventListener("click", function () {
            previewImg.src = defaultImage;
            fotoInput.value = "";
        });
    }
});
