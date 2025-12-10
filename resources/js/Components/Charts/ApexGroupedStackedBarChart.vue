<script setup>
import { computed, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b', '#748d63', '#fcda7b', '#8174a0', '#f69a5c', '#5c8f9a', '#a07481', '#6b9f5c', '#d49a6a'] }
});

const chartRef = ref(null);

// Generate colors for each stack within each subgroup
const generateColors = computed(() => {
    const subgroups = props.chartData?.subgroups || [];
    const stacks = props.chartData?.stacks || [];
    const colorList = [];
    
    // Each subgroup gets a base color, stacks get variations
    subgroups.forEach((_, subgroupIdx) => {
        const baseColor = props.colors[subgroupIdx % props.colors.length];
        stacks.forEach((_, stackIdx) => {
            // Darken/lighten the base color for different stacks
            const variation = 1 - (stackIdx * 0.15);
            colorList.push(adjustColorBrightness(baseColor, variation));
        });
    });
    
    return colorList.length > 0 ? colorList : props.colors;
});

// Helper to adjust color brightness
const adjustColorBrightness = (hex, factor) => {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    
    const newR = Math.min(255, Math.floor(r * factor));
    const newG = Math.min(255, Math.floor(g * factor));
    const newB = Math.min(255, Math.floor(b * factor));
    
    return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
};

const chartOptions = computed(() => ({
    chart: {
        id: 'grouped-stacked-bar-chart',
        type: 'bar',
        fontFamily: 'TT Bells, sans-serif',
        toolbar: { show: false },
        foreColor: '#1e293b',
        stacked: true,
        stackType: 'normal',
        zoom: { enabled: false }
    },
    colors: generateColors.value,
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '70%',
            borderRadius: 2,
            borderRadiusApplication: 'end',
        },
    },
    dataLabels: { 
        enabled: false
    },
    stroke: { show: true, width: 1, colors: ['#fff'] },
    xaxis: {
        categories: props.chartData?.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        },
        title: {
            text: 'Group',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        }
    },
    yaxis: {
        title: {
            text: props.chartData?.yAxisTitle || 'Nilai',
            style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TT Bells, sans-serif' }
        },
        labels: {
            style: { colors: '#1e293b', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 },
            formatter: function(val) {
                return val.toFixed(0);
            }
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
        horizontalAlign: 'center',
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 600,
        labels: { colors: '#1e293b' },
        markers: { radius: 4 },
        itemMargin: { horizontal: 10, vertical: 5 }
    },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        shared: true,
        intersect: false,
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            const groupName = w.globals.labels[dataPointIndex];
            let tooltipHtml = `
                <div class="px-3 py-2 bg-white shadow-lg rounded-lg border max-w-xs">
                    <div class="font-semibold text-gray-800 mb-2 border-b pb-1">${groupName}</div>
            `;
            
            // Group series by subgroup for better display
            const subgroups = props.chartData?.subgroups || [];
            const stacks = props.chartData?.stacks || [];
            
            if (subgroups.length > 0 && stacks.length > 0) {
                let seriesIdx = 0;
                subgroups.forEach((subgroupName) => {
                    tooltipHtml += `<div class="mt-1"><span class="font-medium text-gray-700">${subgroupName}:</span>`;
                    let subgroupTotal = 0;
                    stacks.forEach((stackName) => {
                        const value = series[seriesIdx]?.[dataPointIndex] || 0;
                        const color = w.config.colors[seriesIdx];
                        subgroupTotal += value;
                        tooltipHtml += `
                            <div class="flex items-center justify-between text-sm ml-2">
                                <span class="flex items-center">
                                    <span class="inline-block w-2 h-2 rounded mr-1" style="background-color: ${color}"></span>
                                    ${stackName}:
                                </span>
                                <span class="font-bold ml-2">${value}</span>
                            </div>
                        `;
                        seriesIdx++;
                    });
                    tooltipHtml += `<div class="text-xs text-gray-500 ml-2">Total: ${subgroupTotal}</div></div>`;
                });
            } else {
                // Fallback for simple data format
                series.forEach((seriesData, idx) => {
                    const seriesName = w.globals.seriesNames[idx];
                    const value = seriesData[dataPointIndex];
                    const color = w.config.colors[idx % w.config.colors.length];
                    
                    tooltipHtml += `
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: ${color}"></span>
                                ${seriesName}:
                            </span>
                            <span class="font-bold ml-3">${value}</span>
                        </div>
                    `;
                });
            }
            
            tooltipHtml += `</div>`;
            return tooltipHtml;
        }
    },
    fill: {
        opacity: 1
    }
}));

const series = computed(() => {
    const datasets = props.chartData?.datasets || [];
    
    // The datasets should already be properly formatted with:
    // - name: "SubgroupName - StackName"
    // - group: "SubgroupName" (for stacking)
    // - data: [values per group]
    
    return datasets.map(dataset => ({
        name: dataset.name,
        group: dataset.group || dataset.name,
        data: dataset.data
    }));
});

const downloadChart = () => {
    if (chartRef.value) {
        chartRef.value.chart.dataURI().then(({ imgURI }) => {
            const link = document.createElement('a');
            link.href = imgURI;
            link.download = `GroupedStackedBarChart-${props.title || 'Export'}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
};

defineExpose({ downloadChart });
</script>

<template>
    <div>
        <!-- Legend explanation -->
        <div v-if="chartData?.subgroups?.length > 0" class="mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="text-sm text-gray-600">
                <strong>Struktur:</strong>
                <span class="ml-2">Groups: {{ chartData?.labels?.join(', ') || '-' }}</span>
                <span class="mx-2">|</span>
                <span>Sub-groups: {{ chartData?.subgroups?.join(', ') || '-' }}</span>
                <span class="mx-2">|</span>
                <span>Stacks: {{ chartData?.stacks?.join(', ') || '-' }}</span>
            </div>
        </div>
        
        <VueApexCharts 
            ref="chartRef" 
            type="bar" 
            :height="height" 
            :options="chartOptions" 
            :series="series" 
        />
    </div>
</template>
