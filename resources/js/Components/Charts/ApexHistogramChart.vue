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
        id: 'histogram-chart',
        type: 'bar',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        zoom: { enabled: false }
    },
    colors: props.colors,
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '95%',
            borderRadius: 0,
            borderRadiusApplication: 'end',
        },
    },
    dataLabels: { 
        enabled: true,
        offsetY: -20,
        style: { 
            fontSize: '11px', 
            fontFamily: 'TT Bells, sans-serif',
            colors: ["#304758"] 
        },
        formatter: function(val) {
            return val > 0 ? val : '';
        }
    },
    stroke: { show: false },
    xaxis: {
        categories: props.chartData?.labels || [],
        axisBorder: { show: true },
        axisTicks: { show: true },
        title: {
            text: props.chartData?.xAxisTitle || 'Interval',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
        labels: {
            style: { colors: '#1e293b', fontSize: '10px', fontFamily: 'TT Bells, sans-serif', fontWeight: 500 },
            rotate: -45,
            rotateAlways: (props.chartData?.labels?.length || 0) > 6,
            hideOverlappingLabels: true
        }
    },
    yaxis: {
        title: {
            text: props.chartData?.yAxisTitle || 'Frekuensi',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 },
            formatter: function(val) {
                return Math.round(val);
            }
        },
        min: 0
    },
    grid: {
        show: true,
        borderColor: '#e2e8f0',
        strokeDashArray: 0,
        position: 'back',
    },
    legend: {
        show: false
    },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            const binLabel = w.globals.labels[dataPointIndex];
            const frequency = series[seriesIndex][dataPointIndex];
            const total = series[seriesIndex].reduce((sum, val) => sum + val, 0);
            const percentage = total > 0 ? ((frequency / total) * 100).toFixed(1) : 0;
            
            return `
                <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                    <div class="font-semibold text-gray-800">${binLabel}</div>
                    <div class="text-sm text-gray-600">Frekuensi: <span class="font-bold text-[#ef874b]">${frequency}</span></div>
                    <div class="text-xs text-gray-500">${percentage}% dari total</div>
                </div>
            `;
        }
    }
}));

const series = computed(() => props.chartData?.datasets || []);

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `Histogram-${props.title || 'Export'}.png`;
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
