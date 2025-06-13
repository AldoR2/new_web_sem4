document.addEventListener("DOMContentLoaded", async function () {
    const provinsi = document.getElementById("provinsi");
    const kota = document.getElementById("kota");
    const kecamatan = document.getElementById("kecamatan");
    const kelurahan = document.getElementById("kelurahan");

    const selectedProvinsi = provinsi.dataset.selected;
    const selectedKota = kota.dataset.selected;
    const selectedKecamatan = kecamatan.dataset.selected;
    const selectedKelurahan = kelurahan.dataset.selected;

    async function loadWilayah(
        url,
        targetSelect,
        placeholder = "Pilih...",
        selected = null
    ) {
        const res = await fetch(url);
        const data = await res.json();
        targetSelect.innerHTML = `<option hidden selected>${placeholder}</option>`;
        data.forEach((item) => {
            const isSelected = selected == item.id ? "selected" : "";
            targetSelect.innerHTML += `<option value="${item.id}" ${isSelected}>${item.name}</option>`;
        });
    }

    // Load Provinsi
    await loadWilayah(
        "/wilayah/provinsis",
        provinsi,
        "Pilih Provinsi",
        selectedProvinsi
    );

    if (selectedProvinsi) {
        await loadWilayah(
            `/wilayah/kotas/${selectedProvinsi}`,
            kota,
            "Pilih Kota",
            selectedKota
        );
    }
    if (selectedKota) {
        await loadWilayah(
            `/wilayah/kecamatans/${selectedKota}`,
            kecamatan,
            "Pilih Kecamatan",
            selectedKecamatan
        );
    }
    if (selectedKecamatan) {
        await loadWilayah(
            `/wilayah/kelurahans/${selectedKecamatan}`,
            kelurahan,
            "Pilih Kelurahan",
            selectedKelurahan
        );
    }

    // Event listeners
    provinsi.addEventListener("change", async () => {
        const id = provinsi.value;
        await loadWilayah(`/wilayah/kotas/${id}`, kota, "Pilih Kota");
        kecamatan.innerHTML = `<option hidden selected>Pilih Kecamatan</option>`;
        kelurahan.innerHTML = `<option hidden selected>Pilih Kelurahan</option>`;
    });

    kota.addEventListener("change", async () => {
        const id = kota.value;
        await loadWilayah(
            `/wilayah/kecamatans/${id}`,
            kecamatan,
            "Pilih Kecamatan"
        );
        kelurahan.innerHTML = `<option hidden selected>Pilih Kelurahan</option>`;
    });

    kecamatan.addEventListener("change", async () => {
        const id = kecamatan.value;
        await loadWilayah(
            `/wilayah/kelurahans/${id}`,
            kelurahan,
            "Pilih Kelurahan"
        );
    });
});
