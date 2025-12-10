<script setup>
import { computed, ref, shallowRef, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b'] }
});

const chartRef = ref(null);
const isReady = shallowRef(false);

// Memoize chart options to prevent unnecessary recalculations
const chartOptions = computed(() => {
    if (!props.chartData?.labels) return {};
    
    return {
        chart: {
            id: 'boxplot-chart',
            type: 'boxPlot',
            fontFamily: 'TT Bells, sans-serif',
            toolbar: { show: false },
            foreColor: '#1e293b',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 400,
                animateGradually: {
                    enabled: false
                },
                dynamicAnimation: {
                    enabled: false
                }
            }
        },
        colors: props.colors,
        plotOptions: {
            boxPlot: {
                colors: {
                    upper: props.colors[0] || '#ef874b',
                    lower: props.colors[1] || '#50829b'
                }
            }
        },
        xaxis: {
            categories: props.chartData.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
            }
        },
        yaxis: {
            title: {
                text: props.chartData.yAxisTitle || 'Nilai',
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
        tooltip: {
            theme: 'light',
            style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
            shared: false,
            intersect: true
        }
    };
});

const series = computed(() => props.chartData?.datasets || []);

// Lazy initialize chart for better performance
watch(() => props.chartData, (newData) => {
    if (newData?.labels && newData?.datasets) {
        isReady.value = true;
    }
}, { immediate: true });

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `BoxPlot-${props.title || 'Export'}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
};

defineExpose({ downloadChart });
</script>

<template>
    <div v-if="isReady">
        <VueApexCharts ref="chartRef" type="boxPlot" :height="height" :options="chartOptions" :series="series" />
    </div>
    <div v-else class="flex items-center justify-center" :style="{ height: height + 'px' }">
        <div class="text-gray-400">Memuat grafik...</div>
    </div>
</template>
