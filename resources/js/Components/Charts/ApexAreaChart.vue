<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b'] }
});

const chartOptions = computed(() => ({
    chart: {
        id: 'area-chart',
        fontFamily: 'TT Bells, sans-serif',
        // --- AKTIFKAN TOOLBAR ---
        toolbar: { 
            show: true,
            tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
            export: {
                csv: { filename: 'Data_Riset_PKL65' },
                png: { filename: 'Grafik_Riset_PKL65' },
                svg: { filename: 'Grafik_Riset_PKL65' }
            }
        },
        zoom: { enabled: false }
    },
    colors: props.colors,
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.2, 
            stops: [0, 90, 100]
        }
    },
    xaxis: {
        categories: props.chartData.labels || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { 
            style: { colors: '#333333', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 } 
        }
    },
    yaxis: {
        labels: {
            style: { colors: '#333333', fontSize: '12px', fontFamily: 'TT Bells, sans-serif', fontWeight: 600 }
        }
    },
    grid: { borderColor: '#E5E5E5', strokeDashArray: 4 },
    tooltip: {
        theme: 'light',
        style: { fontSize: '12px', fontFamily: 'TT Bells, sans-serif' },
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            const categoryName = w.globals.labels[dataPointIndex];
            const value = series[seriesIndex][dataPointIndex];
            
            return `
                <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                    <div class="font-semibold text-gray-800">${categoryName}</div>
                    <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                </div>
            `;
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontWeight: 600,
        labels: { colors: '#333333' },
        fontFamily: 'TT Bells, sans-serif'
    }
}));

const series = computed(() => {
    // Jika datasets adalah array dengan objek berisi data
    if (Array.isArray(props.chartData.datasets) && props.chartData.datasets[0]?.data) {
        return props.chartData.datasets;
    }
    // Jika datasets langsung array nilai (fallback)
    if (Array.isArray(props.chartData.datasets)) {
        return [{ name: 'Nilai', data: props.chartData.datasets }];
    }
    return [];
});
</script>

<template>
    <VueApexCharts type="area" :height="height" :options="chartOptions" :series="series" />
</template>