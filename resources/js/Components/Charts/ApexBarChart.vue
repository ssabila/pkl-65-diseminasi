<script setup>
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b'] } 
});

const chartRef = ref(null);

const chartOptions = computed(() => ({
    chart: {
        id: 'bar-chart',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false }, // Hamburger OFF
        foreColor: '#1e293b', 
        zoom: { enabled: false }
    },
    colors: props.colors,
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '50%',
            borderRadius: 4,
            borderRadiusApplication: 'end',
        },
    },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 0, colors: ['transparent'] },
    xaxis: {
        categories: props.chartData.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        }
    },
    yaxis: {
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        }
    },
    grid: {
        show: true,
        borderColor: '#e2e8f0',
        strokeDashArray: 0,
        position: 'back',
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 600,
        labels: { colors: '#1e293b' },
        markers: { radius: 12 }
    },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            const categoryName = w.globals.labels[dataPointIndex];
            const value = series[seriesIndex][dataPointIndex];
            
            return `
                <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                    <div class="font-semibold text-gray-800">${categoryName}</div>
                    <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                </div>
            `;
        }
    }
}));

const series = computed(() => props.chartData.datasets || []);

// Fungsi Download
const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `Chart-${props.title || 'Export'}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
};

defineExpose({ downloadChart });
</script>

<template>
    <VueApexCharts ref="chartRef" type="bar" :height="height" :options="chartOptions" :series="series" />
</template>