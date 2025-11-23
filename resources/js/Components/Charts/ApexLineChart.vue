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
        id: 'line-chart',
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
        zoom: { enabled: false },
        dropShadow: { enabled: false }
    },
    colors: props.colors,
    stroke: { curve: 'smooth', width: 4 },
    markers: {
        size: 6,
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: { size: 8 }
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
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontWeight: 600,
        labels: { colors: '#333333' },
        fontFamily: 'TT Bells, sans-serif'
    }
}));

const series = computed(() => props.chartData.datasets || []);
</script>

<template>
    <VueApexCharts type="line" :height="height" :options="chartOptions" :series="series" />
</template>