// DOSEN DOSEN DOSEN
document.addEventListener("DOMContentLoaded", () => {
    const jumlahMinggu = chartData[0]?.data.length || 4;

    const categories = [];
    for (let i = 1; i <= jumlahMinggu; i++) {
        categories.push(`Minggu ${i}`);
    }

    const options = {
        chart: {
            type: "bar",
            height: 250,
            toolbar: { show: false },
        },
        series: chartData,
        //     {
        //         name: "Jumlah Presensi",
        //         data: [5, 4, 6, 3], // Minggu 1 - 4
        //     },
        // ],
        xaxis: {
            categories: categories,
        },
        colors: ["#1E88E5"],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: "50%",
            },
        },
        dataLabels: {
            enabled: true,
        },
    };

    // const chart = new ApexCharts(
    //     document.querySelector("#grafik-kehadiran"),
    //     options
    // );
    // chart.render();

    const chartContainer = document.querySelector("#grafik-kehadiran");

    if (chartContainer) {
        const chart = new ApexCharts(chartContainer, options);
        chart.render();
    }
});
