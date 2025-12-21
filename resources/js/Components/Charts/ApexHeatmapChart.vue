<script setup>
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b'] }
});

const chartRef = ref(null);

const chartOptions = computed(() => ({
    chart: {
        id: 'heatmap-chart',
        type: 'heatmap',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b'
    },
    plotOptions: {
        heatmap: {
            shadeIntensity: 0.5,
            radius: 2,
            useFillColorAsStroke: false,
            colorScale: {
                ranges: props.chartData.colorRanges || [
                    { from: -100, to: -20, color: '#50829b', name: 'Low' },
                    { from: -20, to: 20, color: '#fcda7b', name: 'Medium' },
                    { from: 20, to: 100, color: '#ef874b', name: 'High' }
                ]
            }
        }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '11px',
            fontFamily: 'TT Bells, sans-serif',
            fontWeight: 600,
            colors: ['#1e293b']
        }
    },
    xaxis: {
        type: 'category',
        categories: props.chartData.categories || [],
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
        show: false
    },
    legend: {
        show: true,
        position: 'bottom',
        fontFamily: 'TT Bells, sans-serif',
        labels: { colors: '#1e293b' }
    },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        y: {
            formatter: function(val) {
                return val;
            }
        }
    }
}));

const series = computed(() => props.chartData.datasets || []);

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `Heatmap-${props.title || 'Export'}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
};

defineExpose({ downloadChart });
</script>

<template>
    <VueApexCharts ref="chartRef" type="heatmap" :height="height" :options="chartOptions" :series="series" />
</template>
