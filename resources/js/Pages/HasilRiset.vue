<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'

// Import komponen chart
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    activeVisualization: { type: Object, default: null },
    selectedTopicId: { type: [String, Number], default: null }
})

// --- PALET WARNA CHART ---
const pklColors = [
    '#ef874b', // Orange
    '#50829b', // Teal
    '#748d63', // Green
    '#fcda7b', // Yellow
    '#8174a0', // Purple
    '#f69a5c'  // Light Orange
];

// --- LOGIC ACCORDION ---
const openRisetId = ref(null)
const toggleRiset = risetId => {
    openRisetId.value = openRisetId.value === risetId ? null : risetId
}

onMounted(() => {
    if (props.selectedTopicId && props.risetTopics.length > 0) {
        const activeRiset = props.risetTopics.find(r =>
            r.topics.some(t => String(t.id) === String(props.selectedTopicId))
        )
        if (activeRiset) openRisetId.value = activeRiset.id
    } else if (props.risetTopics.length > 0) {
        openRisetId.value = props.risetTopics[0].id
    }
})

// --- LOGIC CHART ---
const chartComponents = {
    'bar-chart': ApexBarChart,
    'pie-chart': ApexDonutChart,
    'donut-chart': ApexDonutChart,
    'line-chart': ApexLineChart,
    'area-chart': ApexAreaChart
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
        if (['bar-chart', 'line-chart', 'area-chart'].includes(typeCode)) {
            if (rawData.categories && Array.isArray(rawData.series)) {
                return { labels: rawData.categories, datasets: rawData.series };
            }
        } else if (['pie-chart', 'donut-chart'].includes(typeCode)) {
            if (rawData.labels && Array.isArray(rawData.series)) {
                return { labels: rawData.labels, datasets: rawData.series };
            }
        }
        return rawData;
    } catch (e) {
        console.error("Error parsing chart data:", e);
        return null;
    }
})
</script>

<template>
    <Head title="Hasil Riset" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden text-pkl-dark">
        
        <aside class="w-full md:w-80 bg-pkl-base-orange flex-shrink-0 md:h-screen shadow-2xl z-20 flex flex-col relative">
            
            <div class="px-6 pt-6 pb-3 z-10">
                <Link href="/" class="group flex items-center gap-2 mb-4 transition-transform duration-300 hover:translate-x-1">
                    <img src="/images/assets/LOGO-PKL_REV8.png" alt="Logo PKL 65" class="h-10 w-10 rounded-full bg-white p-1 transition group-hover:opacity-100" />
                    <div class="flex flex-col justify-center">
                        <h2 class="font-headline text-lg text-white tracking-wide drop-shadow-sm leading-tight group-hover:text-pkl-compliment-yellow transition">
                            WEBSITE HASIL PKL 65
                        </h2>
                        <p class="font-sans text-white text-[10px] mt-0.5 font-bold tracking-[0.15em] opacity-70 group-hover:opacity-100 transition">
                            TAHUN AJARAN 2025/2026
                        </p>
                    </div>
                </Link>
                <div class="h-px w-full bg-white/20"></div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide">
                <div v-for="riset in risetTopics" :key="riset.id" class="mt-3 border-b border-white/10 pb-1">
                    <button @click="toggleRiset(riset.id)" class="w-full flex items-center justify-between px-3 py-2.5 text-white hover:bg-white/10 rounded-lg transition-all group focus:outline-none">
                        <h3 class="font-sans text-base font-bold text-white/90 tracking-wide uppercase text-left">
                            {{ riset.name }}
                        </h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/70 transition-transform duration-300" :class="{ 'rotate-180 text-white': openRisetId === riset.id }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="overflow-hidden transition-all duration-500 ease-in-out" :class="openRisetId === riset.id ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0'">
                        <ul class="space-y-1 pl-2">
                            <li v-for="topic in riset.topics" :key="topic.id">
                                <Link :href="route('hasil-riset', { topic_id: topic.id })" preserve-scroll 
                                    :class="['flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-300 w-full relative overflow-hidden shadow-sm', 
                                    String(topic.id) === String(selectedTopicId) 
                                        ? 'bg-white text-pkl-base-orange shadow-md translate-x-1 font-bold' 
                                        : 'text-white/80 hover:text-white hover:bg-white/10 hover:translate-x-1 font-medium']">
                                    <span class="relative z-10 tracking-wide font-sans text-sm">{{ topic.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <h3 class="px-4 font-sub text-[10px] font-bold text-white/60 tracking-widest mb-2 uppercase">LAINNYA</h3>
                    <ul class="space-y-1 pl-2">
                        <li><Link :href="route('dokumen')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">DOKUMEN</Link></li>
                        <li><Link href="/" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">KEMBALI KE BERANDA</Link></li>
                    </ul>
                </div>
            </nav>

            <div class="p-4 text-center opacity-60 bg-orange-900/10 mt-auto">
                <p class="text-[10px] text-white font-sub uppercase tracking-widest">&copy; 2025 Polstat STIS</p>
            </div>
        </aside>

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
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>