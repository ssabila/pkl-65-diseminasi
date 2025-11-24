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
        id: 'area-chart',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false }, // Hamburger mati
        zoom: { enabled: false }
    },
    colors: props.colors,
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } },
    xaxis: {
        categories: props.chartData.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: '#333333', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 } }
    },
    yaxis: {
        labels: { style: { colors: '#333333', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 } }
    },
    grid: { show: true, borderColor: '#E5E5E5', strokeDashArray: 0 },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 600,
        labels: { colors: '#333333' },
        markers: { radius: 12 }
    },
    tooltip: { theme: 'light', style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' } }
}));

const series = computed(() => props.chartData.datasets || []);

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
    <VueApexCharts ref="chartRef" type="area" :height="height" :options="chartOptions" :series="series" />
</template>