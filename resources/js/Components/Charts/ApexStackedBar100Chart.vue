<script setup>
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b', '#748d63', '#fcda7b', '#8174a0', '#f69a5c'] }
});

const chartRef = ref(null);

const chartOptions = computed(() => ({
    chart: {
        id: '100-stacked-bar-chart',
        type: 'bar',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        stacked: true,
        stackType: '100%',
        zoom: { enabled: false }
    },
    colors: props.colors,
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 4,
            borderRadiusApplication: 'end',
        },
    },
    dataLabels: { 
        enabled: true,
        formatter: function(val) {
            return val.toFixed(1) + '%';
        }
    },
    stroke: { show: true, width: 1, colors: ['#fff'] },
    xaxis: {
        categories: props.chartData.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        }
    },
    yaxis: {
        title: {
            text: 'Percentage (%)',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
        labels: {
            formatter: function(val) {
                return val.toFixed(0) + '%';
            },
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
        y: {
            formatter: function(val) {
                return val.toFixed(1) + '%';
            }
        }
    },
    fill: {
        opacity: 1
    }
}));

const series = computed(() => props.chartData.datasets || []);

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `100StackedBarChart-${props.title || 'Export'}.png`;
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
