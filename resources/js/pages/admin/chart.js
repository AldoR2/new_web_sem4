document.addEventListener("DOMContentLoaded", function () {
    const jumlahMinggu = chartData[0]?.data.length || 4;

    const categories = [];
    for (let i = 1; i <= jumlahMinggu; i++) {
        categories.push(`Minggu ${i}`);
    }

    const options = {
        chart: {
            type: "bar",
            height: 300,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                columnWidth: "80%",
            },
        },
        series: chartData,
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    colors: "#555",
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: ["#555"],
                },
            },
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ["#555"],
            },
        },
        colors: ["#2563eb", "#555", "#f59e0b", "#ef4444"],
    };

    const chartContainer = document.querySelector("#chart");

    if (chartContainer) {
        const chart = new ApexCharts(chartContainer, options);
        chart.render();
    }
});
