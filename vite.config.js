// import { defineConfig } from "vite";
// import laravel from "laravel-vite-plugin";

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ["resources/css/app.css", "resources/js/app.js"],
//             refresh: true,
//         }),
//     ],
// });

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import fs from "fs";
import path from "path";

// Fungsi untuk mengambil semua file .js dari subfolder tertentu
function getJsFilesFrom(dir) {
    const fullPath = path.resolve(__dirname, dir);
    if (!fs.existsSync(fullPath)) return [];

    return fs
        .readdirSync(fullPath)
        .filter((file) => file.endsWith(".js"))
        .map((file) => `${dir}/${file}`);
}

// Ambil semua file JS dari folder halaman
const adminPages = getJsFilesFrom("resources/js/pages/admin");
const dosenPages = getJsFilesFrom("resources/js/pages/dosen");
const mahasiswaPages = getJsFilesFrom("resources/js/pages/mahasiswa");
const superAdminPages = getJsFilesFrom("resources/js/pages/superadmin");

export default defineConfig({
    base: "/gol_c2/",
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/components/data-wilayah.js",
                "resources/js/components/form-validasi.js",
                "resources/js/components/image-preview.js",
                ...adminPages,
                ...dosenPages,
                ...mahasiswaPages,
                ...superAdminPages,
            ],
            refresh: true,
        }),
    ],
});
