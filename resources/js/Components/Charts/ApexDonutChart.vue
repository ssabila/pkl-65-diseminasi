<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 350 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b', '#748d63', '#fcda7b'] }
});

const chartOptions = computed(() => ({
    chart: { 
        fontFamily: 'TT Bells, sans-serif',
        // --- AKTIFKAN TOOLBAR ---
        toolbar: { show: true } // Default tools sudah cukup untuk donut
    },
    colors: props.colors,
    labels: props.chartData.labels || [],
    dataLabels: {
        enabled: true,
        style: { 
            fontSize: '13px', 
            fontFamily: 'TT Bells, sans-serif',
            fontWeight: 700,
        },
        dropShadow: { enabled: false }
    },
    stroke: { show: true, colors: ['#fff'], width: 2 },
    legend: {
        position: 'right',
        offsetY: 50,
        fontFamily: 'TT Bells, sans-serif',
        fontWeight: 600,
        markers: { radius: 12 },
        itemMargin: { vertical: 8 },
        labels: { colors: '#333333' }
    },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    name: { show: true, fontSize: '14px', color: '#555555', fontFamily: 'TT Bells, sans-serif' },
                    value: { 
                        show: true, 
                        fontSize: '24px', 
                        fontWeight: 700, 
                        color: '#ef874b', // Angka tengah Orange
                        fontFamily: 'TT Bells, sans-serif'
                    },
                    total: { show: true, label: 'Total', color: '#555555', fontFamily: 'TT Bells, sans-serif' }
                }
            }
        }
    }
}));

const series = computed(() => props.chartData.datasets || []);
</script>

<template>
    <VueApexCharts type="donut" :height="height" :options="chartOptions" :series="series" />
</template>