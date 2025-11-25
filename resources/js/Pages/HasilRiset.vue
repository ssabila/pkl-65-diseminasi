<script setup>
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'
import LeafletMap from '@/Components/Charts/LeafletMap.vue'

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    visualizations: { type: Array, default: () => [] },
    activeTopic: { type: Object, default: null },
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
    'area-chart': ApexAreaChart
};

// Ref untuk download multiple charts
const chartRefs = ref({});
const setChartRef = (el, id) => { if (el) chartRefs.value[id] = el; };

const getChartComponent = (typeCode) => chartComponents[typeCode] || null;

const formatChartData = (vis) => {
    if (!vis || !vis.chart_data) return null;
    const rawData = vis.chart_data;
    const typeCode = vis.type?.type_code;
    try {
        if (['pie-chart', 'donut-chart'].includes(typeCode)) {
            if (rawData.labels && Array.isArray(rawData.series)) return { labels: rawData.labels, datasets: rawData.series };
        } else {
            if (rawData.categories && Array.isArray(rawData.series)) return { labels: rawData.categories, datasets: rawData.series };
        }
        
        return rawData;
    } catch (e) { return null; }
};

const triggerDownload = (id) => {
    const chartComponent = chartRefs.value[id];
    if (chartComponent && typeof chartComponent.downloadChart === 'function') chartComponent.downloadChart();
};
</script>

<template>
    <Head title="Hasil Riset" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-[#FDFBF7] font-sans overflow-hidden text-gray-800">
        
        <Sidebar 
            :riset-topics="risetTopics" 
            :selected-topic-id="selectedTopicId" 
            active-page="hasil-riset" 
        />

        <main class="flex-1 p-6 md:p-10 h-screen overflow-y-auto relative scroll-smooth">
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-30 mix-blend-multiply z-0 fixed">
                 <img src="/images/assets/pattern kuning 1.svg" class="absolute top-0 right-0 w-[600px] opacity-30" />
            </div>

            <div v-if="activeTopic" class="max-w-7xl mx-auto pb-20 relative z-10">
                
                <div class="mb-12 border-b-2 border-orange-100 pb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-4 py-1 rounded-full bg-white border border-orange-200 text-orange-500 text-[11px] font-bold tracking-widest uppercase shadow-sm">
                            {{ activeTopic.riset?.name }}
                        </span>
                    </div>
                    <h1 class="font-headline text-5xl md:text-6xl text-pkl-base-orange leading-none drop-shadow-sm">
                        {{ activeTopic.name }}
                    </h1>
                    <p v-if="activeTopic.description" class="mt-4 text-gray-600 max-w-3xl font-sans">
                        {{ activeTopic.description }}
                    </p>
                </div>

                <div v-if="visualizations.length > 0" class="space-y-16">
                    <div v-for="vis in visualizations" :key="vis.id" 
                         class="bg-white rounded-[2.5rem] shadow-2xl shadow-orange-900/5 border border-white overflow-hidden ring-1 ring-black/5 transition-all duration-500 ease-in-out">
                        
                        <div class="px-8 py-6 bg-white border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex-1">
                                <h2 class="font-headline text-2xl text-gray-800 leading-snug">{{ vis.title }}</h2>
                                <p class="text-xs text-gray-400 font-bold tracking-widest uppercase mt-1">{{ vis.type?.type_name }}</p>
                            </div>
                            <button @click="triggerDownload(vis.id)" class="px-8 py-3 bg-pkl-base-orange text-white text-xs font-bold tracking-widest uppercase rounded-full shadow-md hover:bg-orange-600 hover:shadow-lg transition-all active:scale-95">Download</button>
                        </div>

                        <div class="p-8 min-h-[450px] flex flex-col justify-center bg-white">
                            <component
                                :ref="(el) => setChartRef(el, vis.id)"
                                :is="getChartComponent(vis.type?.type_code)"
                                v-if="getChartComponent(vis.type?.type_code) && formatChartData(vis)"
                                :chart-data="formatChartData(vis)"
                                :title="vis.title"
                                :colors="pklColors" 
                                height="450" 
                            />
                            <div v-else class="flex flex-col items-center justify-center h-80 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 text-center">
                                <p class="font-bold text-gray-400 text-sm uppercase tracking-wide">Data visualisasi tidak valid</p>
                            </div>
                        </div>

                        <div class="bg-[#F9F9F9] px-8 py-10 border-t border-gray-100 relative">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 p-2 bg-teal-50 rounded-lg text-pkl-compliment-teal shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <h3 class="font-headline text-lg text-pkl-compliment-teal tracking-wide mb-2">Interpretasi Data</h3>
                                    <p class="text-gray-700 leading-relaxed text-justify font-sans text-base whitespace-pre-line">{{ vis.interpretation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center h-96 text-center opacity-60 border-2 border-dashed border-orange-200 rounded-[2.5rem]">
                    <h3 class="font-headline text-xl text-gray-600">Belum Ada Visualisasi</h3>
                </div>
            </div>

            <div v-else class="h-full flex flex-col items-center justify-center text-center pb-20 opacity-80">
                <div class="mb-6 p-6 bg-white rounded-full shadow-xl shadow-orange-100/50 border border-white">
                    <img src="/images/assets/LOGO-PKL_REV8.png" class="w-24 h-24 object-contain opacity-90" />
                </div>
                <h2 class="font-headline text-5xl text-pkl-base-orange mb-3">HASIL PKL 65</h2>
                <p class="font-sans text-gray-500 tracking-wide text-lg">Silakan pilih topik riset pada menu di sebelah kiri.</p>
            </div>
        </main>
    </div>
</template>