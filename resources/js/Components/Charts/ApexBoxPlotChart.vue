<script setup>
import { computed, ref, watch, onMounted, nextTick } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b'] }
});

const chartRef = ref(null);
const chartKey = ref(0);

// Check if data has outliers
const hasOutliers = computed(() => {
    const datasets = props.chartData?.datasets || [];
    return datasets.some(d => d.type === 'scatter');
});

const chartOptions = computed(() => ({
    chart: {
        id: `boxplot-chart-${chartKey.value}`,
        type: 'boxPlot',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        animations: {
            enabled: true,
            speed: 300
        },
        redrawOnParentResize: true,
        redrawOnWindowResize: true
    },
    colors: [props.colors[0] || '#ef874b', props.colors[1] || '#50829b', '#e74c3c'], // Third color for outliers
    plotOptions: {
        boxPlot: {
            colors: {
                upper: props.colors[0] || '#ef874b',
                lower: props.colors[1] || '#50829b'
            }
        }
    },
    stroke: {
        colors: ['#1e293b']
    },
    markers: {
        size: hasOutliers.value ? [0, 6] : [0], // No markers for boxplot, circle for scatter outliers
        colors: ['transparent', '#e74c3c'],
        strokeColors: ['transparent', '#c0392b'],
        strokeWidth: 2,
        hover: {
            size: 8
        }
    },
    legend: {
        show: hasOutliers.value,
        position: 'top',
        horizontalAlign: 'right',
        markers: {
            width: 10,
            height: 10,
            radius: 12
        }
    },
    xaxis: {
        type: 'category',
        labels: {
            style: {
                colors: '#1e293b',
                fontSize: '12px',
                fontFamily: 'TT Bells, sans-serif'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Nilai',
            style: {
                fontSize: '12px',
                fontWeight: 600,
                fontFamily: 'TT Bells, sans-serif'
            }
        },
        labels: {
            style: {
                colors: '#1e293b',
                fontSize: '12px',
                fontFamily: 'TT Bells, sans-serif'
            }
        }
    },
    grid: {
        borderColor: '#e2e8f0'
    },
    tooltip: {
        theme: 'light',
        shared: false,
        intersect: true,
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            // Add null checks to prevent errors
            if (!w?.config?.series?.[seriesIndex]) {
                return undefined;
            }
            
            const seriesData = w.config.series[seriesIndex];
            const dataPoint = seriesData?.data?.[dataPointIndex];
            
            // If no data point, let ApexCharts handle default tooltip
            if (!dataPoint) {
                return undefined;
            }
            
            // Check if this is an outlier (scatter series)
            if (seriesData.type === 'scatter' || seriesData.name === 'Outliers') {
                return `
                    <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                        <div class="font-semibold text-red-600">⚠️ Outlier</div>
                        <div class="text-sm text-gray-600">Kelompok: <strong>${dataPoint.x || ''}</strong></div>
                        <div class="text-sm text-gray-600">Nilai: <strong class="text-red-600">${dataPoint.y || 0}</strong></div>
                    </div>
                `;
            }
            
            // Default boxplot tooltip - handled by ApexCharts
            return undefined;
        }
    }
}));

// Transform series data - handle both boxPlot and scatter (outliers) series
const series = computed(() => {
    const datasets = props.chartData?.datasets || [];
    
    const transformedSeries = datasets.map(dataset => {
        if (dataset.type === 'scatter') {
            // Outliers series - keep type for ApexCharts to render as scatter
            return {
                type: 'scatter',
                name: dataset.name || 'Outliers',
                data: dataset.data || []
            };
        } else {
            // BoxPlot series - remove type property
            return {
                type: 'boxPlot',
                name: dataset.name || 'Box Plot',
                data: dataset.data || []
            };
        }
    });
    
    return transformedSeries;
});

// Force chart redraw - this fixes the ApexCharts rendering issue
const forceChartRedraw = async () => {
    if (!chartRef.value?.chart) return;
    
    await nextTick();
    
    try {
        const chart = chartRef.value.chart;
        const transformedData = series.value;
        
        if (transformedData.length > 0 && transformedData[0].data.length > 0) {
            chart.updateSeries(transformedData, true);
            
            // Trigger window resize to force ApexCharts to recalculate dimensions
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 50);
        }
    } catch (e) {
        console.warn('Chart redraw error:', e);
    }
};

// Force chart update after data changes
watch(() => props.chartData, async (newData) => {
    if (newData?.datasets?.length > 0) {
        await nextTick();
        // Force chart key change to trigger re-render
        chartKey.value++;
        
        // Multiple redraw attempts with increasing delays
        setTimeout(forceChartRedraw, 100);
        setTimeout(forceChartRedraw, 300);
    }
}, { deep: true });

// Force update on mount with multiple attempts
onMounted(async () => {
    await nextTick();
    
    // Multiple redraw attempts to ensure chart renders
    const delays = [100, 200, 400, 600];
    delays.forEach(delay => {
        setTimeout(forceChartRedraw, delay);
    });
});

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
    <VueApexCharts 
        ref="chartRef" 
        :key="chartKey"
        type="boxPlot" 
        :height="height" 
        :options="chartOptions" 
        :series="series" 
    />
</template>

<style>
/* Force box plot elements to be visible */
.apexcharts-boxPlot-area {
    fill-opacity: 1 !important;
}

/* Style for outlier points */
.apexcharts-scatter-series .apexcharts-marker {
    stroke-width: 2px !important;
}
</style>

