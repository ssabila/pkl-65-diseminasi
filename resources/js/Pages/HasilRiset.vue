<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'

import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'
import LeafletMap from '@/Components/Charts/LeafletMap.vue'

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    activeVisualization: { type: Object, default: null },
    selectedTopicId: { type: [String, Number], default: null }
})

const pklColors = ['#ef874b', '#50829b', '#748d63', '#fcda7b', '#8174a0', '#f69a5c'];

const chartComponents = {
    'bar': ApexBarChart,
    'bar-chart': ApexBarChart,
    'pie': ApexDonutChart,
    'pie-chart': ApexDonutChart,
    'donut': ApexDonutChart,
    'donut-chart': ApexDonutChart,
    'line': ApexLineChart,
    'line-chart': ApexLineChart,
    'area': ApexAreaChart,
    'area-chart': ApexAreaChart,
    'peta': LeafletMap
};

const dynamicChartComponent = computed(() => {
    if (!props.activeVisualization || !props.activeVisualization.type) return null
    return chartComponents[props.activeVisualization.type.type_code] || null
})

const formattedChartData = computed(() => {
    const vis = props.activeVisualization;
    if (!vis || !vis.chart_data) return null;
    const rawData = vis.chart_data;
    const typeCode = vis.type?.type_code;

    try {
        // Data sudah dalam format yang benar dengan labels dan datasets
        if (rawData.labels && Array.isArray(rawData.datasets)) {
            return rawData;
        }
        
        // Fallback untuk format lama dengan categories dan series
        if (['bar', 'bar-chart', 'line', 'line-chart', 'area', 'area-chart'].includes(typeCode)) {
            if (rawData.categories && Array.isArray(rawData.series)) {
                return { labels: rawData.categories, datasets: rawData.series };
            }
        } else if (['pie', 'pie-chart', 'donut', 'donut-chart'].includes(typeCode)) {
            if (rawData.labels && Array.isArray(rawData.datasets)) {
                return { labels: rawData.labels, datasets: rawData.datasets };
            }
            // Fallback untuk format lama dengan series
            if (rawData.labels && Array.isArray(rawData.series)) {
                return { labels: rawData.labels, datasets: rawData.series };
            }
        } else if (typeCode === 'peta') {
            // Untuk peta, data bisa berupa array koordinat dari upload Excel
            return rawData;
        }
        
        return rawData;
    } catch (e) {
        console.error('Error formatting chart data:', e);
        return null;
    }
})
</script>

<template>
    <Head title="Hasil Riset" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden text-pkl-dark">
        
        <Sidebar 
            :riset-topics="risetTopics" 
            :selected-topic-id="selectedTopicId" 
            active-page="hasil-riset" 
        />

        <main class="flex-1 p-6 md:p-12 h-screen overflow-y-auto scroll-smooth relative">
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-40 mix-blend-multiply">
                <img src="/images/assets/pattern kuning 1.svg" class="absolute top-0 right-0 w-[500px] opacity-30" />
            </div>

            <div v-if="activeVisualization" class="animate-fade-in max-w-6xl mx-auto pb-20 relative z-10">
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 mb-3 px-3 py-1.5 rounded-full bg-white/60 border border-pkl-base-orange/20">
                        <span class="w-2 h-2 rounded-full bg-pkl-base-orange"></span>
                        <span class="text-xs font-sub font-bold text-pkl-base-orange uppercase tracking-wider">
                            {{ activeVisualization.topic?.riset?.name }}
                        </span>
                    </div>
                    <h1 class="font-headline text-4xl md:text-5xl text-pkl-base-orange leading-tight uppercase">
                        {{ activeVisualization.topic?.name }}
                    </h1>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-pkl-border overflow-hidden mb-10 relative">
                    <div class="h-1.5 w-full bg-gradient-to-r from-pkl-base-orange to-pkl-compliment-yellow"></div>

                    <div class="px-8 pt-8 pb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h2 class="font-sub text-xl text-pkl-base-orange font-bold tracking-wide uppercase max-w-3xl">
                            {{ activeVisualization.title }}
                        </h2>
                        <span class="px-3 py-1 border border-pkl-base-orange-200 text-pkl-base-orange text-[10px] font-bold font-sub uppercase rounded-md tracking-widest">
                            {{ activeVisualization.type?.name || 'CHART' }}
                        </span>
                    </div>

                    <div class="px-6 pb-8 min-h-[450px] flex flex-col justify-center">
                        <component
                            :is="dynamicChartComponent"
                            v-if="dynamicChartComponent && formattedChartData"
                            :chart-data="formattedChartData"
                            :title="activeVisualization.title"
                            :colors="pklColors"
                            height="420" 
                        />
                        <div v-else class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-pkl-base-orange-200 rounded-2xl bg-pkl-base-orange-50/50">
                            <p class="font-sub text-pkl-base-orange text-sm font-bold uppercase tracking-wide">Data visualisasi belum tersedia</p>
                        </div>
                    </div>

                    <div class="bg-[#FAFAF9] px-8 py-8 border-t border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pkl-compliment-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="font-sub text-sm font-bold text-pkl-compliment-teal uppercase tracking-widest">
                                Interpretasi Data
                            </h3>
                        </div>
                        <p class="text-pkl-text leading-relaxed text-justify text-base font-sans whitespace-pre-line">
                            {{ activeVisualization.interpretation || 'Belum ada interpretasi data untuk visualisasi ini.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-else class="h-full flex flex-col items-center justify-center text-center opacity-80 pb-20">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg mb-6 animate-pulse">
                    <img src="/images/assets/LOGO-PKL_REV8.png" class="w-14 opacity-80" />
                </div>
                <h2 class="font-headline text-4xl text-pkl-base-orange mb-2">MULAI EKSPLORASI</h2>
                <p class="font-sub text-pkl-text tracking-wide">Silakan pilih topik riset pada menu di sebelah kiri.</p>
            </div>
        </main>
    </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>