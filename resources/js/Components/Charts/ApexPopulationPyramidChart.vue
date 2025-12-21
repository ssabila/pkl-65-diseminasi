<script setup>
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 400 },
    colors: { type: Array, default: () => ['#3b82f6', '#ec4899'] }
});

const chartRef = ref(null);

// Transform data for pyramid (left side negative, right side positive)
const series = computed(() => {
    if (!props.chartData.datasets || props.chartData.datasets.length < 2) {
        return [];
    }
    
    return [
        {
            name: props.chartData.datasets[0].name || 'Left',
            data: props.chartData.datasets[0].data.map(val => -Math.abs(val))
        },
        {
            name: props.chartData.datasets[1].name || 'Right',
            data: props.chartData.datasets[1].data
        }
    ];
});

const chartOptions = computed(() => ({
    chart: {
        id: 'population-pyramid',
        type: 'bar',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        stacked: true,
        zoom: { enabled: false }
    },
    colors: props.colors,
    plotOptions: {
        bar: {
            horizontal: true,
            barHeight: '80%',
            borderRadius: 2,
        },
    },
    dataLabels: { 
        enabled: false
    },
    stroke: { show: true, width: 1, colors: ['#fff'] },
    xaxis: {
        labels: {
            formatter: function(val) {
                return Math.abs(val);
            },
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        },
        categories: props.chartData.labels || []
    },
    grid: {
        show: true,
        borderColor: '#e2e8f0',
        strokeDashArray: 0,
        xaxis: {
            lines: {
                show: true
            }
        },
        yaxis: {
            lines: {
                show: false
            }
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'center',
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 600,
        labels: { colors: '#1e293b' },
        markers: { radius: 12 }
    },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        shared: false,
        x: {
            formatter: function(val) {
                return val;
            }
        },
        y: {
            formatter: function(val) {
                return Math.abs(val);
            }
        }
    },
    fill: {
        opacity: 1
    }
}));

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `PopulationPyramid-${props.title || 'Export'}.png`;
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
