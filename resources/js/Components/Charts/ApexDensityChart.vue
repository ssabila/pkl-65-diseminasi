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
        id: 'density-chart',
        type: 'area',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        zoom: { enabled: false }
    },
    colors: props.colors,
    stroke: { 
        curve: 'smooth', 
        width: 2
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.6,
            opacityTo: 0.1,
            stops: [0, 90, 100]
        }
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.chartData.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        title: {
            text: props.chartData.xAxisTitle || 'Nilai',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        }
    },
    yaxis: {
        title: {
            text: props.chartData.yAxisTitle || 'Densitas',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
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
        x: {
            show: true
        }
    }
}));

const series = computed(() => props.chartData.datasets || []);

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `DensityPlot-${props.title || 'Export'}.png`;
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
