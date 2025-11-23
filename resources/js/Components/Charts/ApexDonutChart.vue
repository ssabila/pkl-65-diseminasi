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
        id: 'donut-chart',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false } // Hamburger mati
    },
    labels: props.chartData.labels || [],
    colors: props.colors,
    plotOptions: {
        pie: {
            donut: {
                size: '60%',
                labels: {
                    show: true,
                    name: { show: true, fontSize: '14px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' },
                    value: { show: true, fontSize: '20px', fontWeight: 700, fontFamily: 'TT Bells, sans-serif' },
                    total: { show: true, showAlways: true, label: 'Total', fontSize: '14px', color: '#333' }
                }
            }
        }
    },
    dataLabels: { enabled: true },
    stroke: { show: true, width: 2, colors: ['#ffffff'] },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 500,
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
    <div class="w-full flex justify-center">
        <VueApexCharts ref="chartRef" type="donut" :height="height" :options="chartOptions" :series="series" class="w-full max-w-lg" />
    </div>
</template>