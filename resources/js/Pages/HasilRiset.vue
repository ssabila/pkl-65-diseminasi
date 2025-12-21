<script setup>
import { Head } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'

// --- Existing Imports ---
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'
import LeafletMap from '@/Components/Charts/LeafletMap.vue'

// --- New Imports ---
import ApexStackedBarChart from '@/Components/Charts/ApexStackedBarChart.vue'
import ApexStackedBar100Chart from '@/Components/Charts/ApexStackedBar100Chart.vue'
import ApexGroupedBarChart from '@/Components/Charts/ApexGroupedBarChart.vue'
import ApexGroupedStackedBarChart from '@/Components/Charts/ApexGroupedStackedBarChart.vue'
import ApexClusteredBarChart from '@/Components/Charts/ApexClusteredBarChart.vue'
import ApexBoxPlotChart from '@/Components/Charts/ApexBoxPlotChart.vue'
import ApexHeatmapChart from '@/Components/Charts/ApexHeatmapChart.vue'
import ApexHistogramChart from '@/Components/Charts/ApexHistogramChart.vue'
import ApexDensityChart from '@/Components/Charts/ApexDensityChart.vue'
import ApexPopulationPyramidChart from '@/Components/Charts/ApexPopulationPyramidChart.vue'
import VennDiagramChart from '@/Components/Charts/VennDiagramChart.vue'

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    visualizations: { type: Array, default: () => [] },
    activeTopic: { type: Object, default: null },
    selectedTopicId: { type: [String, Number], default: null }
})

const pklColors = ['#ef874b', '#50829b', '#748d63', '#fcda7b', '#8174a0', '#f69a5c'];

// --- Component Mapping ---
const chartComponents = {
    // Simple charts
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
    
    // Map types
    'map': LeafletMap, 
    'peta': LeafletMap,
    'choropleth': LeafletMap,
    
    // Stacked and grouped bars
    'stacked-bar': ApexStackedBarChart, 
    'stacked-bar-chart': ApexStackedBarChart,
    'stacked-bar-100': ApexStackedBar100Chart,
    '100-stacked-bar-chart': ApexStackedBar100Chart,
    'grouped-bar': ApexGroupedBarChart, 
    'grouped-bar-chart': ApexGroupedBarChart,
    'grouped-stacked-bar': ApexGroupedStackedBarChart,
    'grouped-stacked-bar-chart': ApexGroupedStackedBarChart,
    'clustered-bar': ApexClusteredBarChart,
    'clustered-bar-chart': ApexClusteredBarChart,
    
    // Statistical charts
    'box-plot': ApexBoxPlotChart,
    'boxplot': ApexBoxPlotChart,
    'heatmap': ApexHeatmapChart,
    'heatmap-matrix': ApexHeatmapChart,
    'histogram': ApexHistogramChart,
    'density': ApexDensityChart,
    'density-plot': ApexDensityChart,
    
    // Special charts
    'pyramid': ApexPopulationPyramidChart,
    'population-pyramid': ApexPopulationPyramidChart,
    'venn': VennDiagramChart,
    'venn-diagram': VennDiagramChart
};

// --- Logic Data & Tab ---
const chartRefs = ref({});
const setChartRef = (el, id) => { if (el) chartRefs.value[id] = el; };

// Gunakan hanya data dari database (DummyContentSeeder atau input user)
const mergedVisualizations = computed(() => {
    return props.visualizations.map(v => ({ 
        ...v, 
        category: v.category || v.title // Gunakan title sebagai category jika tidak ada category
    }));
});

// Mendapatkan daftar kategori unik
const categories = computed(() => {
    const cats = mergedVisualizations.value.map(v => v.category || v.title || 'Lainnya');
    return [...new Set(cats)].sort(); // Unik dan urut abjad
});

// State Tab Aktif (Default ke kategori pertama)
const activeTab = ref('');

// Set tab pertama saat data siap
onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
    document.addEventListener('click', handleClickAway);
    
    if (categories.value.length > 0) {
        activeTab.value = categories.value[0];
    }
});

// Filter Visualisasi berdasarkan Tab Aktif
const filteredVisualizations = computed(() => {
    return mergedVisualizations.value.filter(v => (v.category || v.title || 'Lainnya') === activeTab.value);
});

// --- Helper Functions ---
const getChartComponent = (typeCode) => chartComponents[typeCode] || null;

const formatChartData = (vis) => {
    if (!vis || !vis.chart_data) return null;
    const rawData = vis.chart_data;
    const typeCode = vis.type?.type_code;
    
    try {
        // Venn diagram
        if (typeCode === 'venn-diagram') {
            return rawData.vennData ? rawData : { vennData: rawData }
        }
        
        // Map types
        if (['peta', 'map', 'choropleth'].includes(typeCode)) {
            return rawData
        }
        
        // Standard format with labels and datasets
        if (rawData.labels && Array.isArray(rawData.datasets)) {
            return rawData
        }
        
        // Alternative format with categories instead of labels
        if (rawData.categories && Array.isArray(rawData.datasets)) {
            return { labels: rawData.categories, datasets: rawData.datasets, ...rawData }
        }
        
        // Fallback for old layout
        if (rawData.categories && Array.isArray(rawData.series)) {
            return { labels: rawData.categories, datasets: rawData.series }
        }
        
        if (rawData.labels && Array.isArray(rawData.series)) {
            return { labels: rawData.labels, datasets: rawData.series }
        }
        
        // Grouped stacked bar with subgroups
        if (typeCode === 'grouped-stacked-bar-chart' && rawData.subgroups) {
            return rawData
        }
        
        // Default return
        return rawData
    } catch (e) {
        console.error('Error formatting chart data:', e)
        return null
    }
}

const triggerDownload = (id) => {
    const chartComponent = chartRefs.value[id];
    if (chartComponent && typeof chartComponent.downloadChart === 'function') chartComponent.downloadChart();
};

// --- Mobile Sidebar Logic ---
const isMobile = ref(false);
const isSidebarOpen = ref(false);
const checkMobile = () => {
    isMobile.value = window.innerWidth < 768;
    if (!isMobile.value) isSidebarOpen.value = false;
};
const toggleSidebar = () => isSidebarOpen.value = !isSidebarOpen.value;
const closeSidebar = () => isSidebarOpen.value = false;
const handleClickAway = (event) => {
    if (!isMobile.value || !isSidebarOpen.value) return;
    const sidebar = event.target.closest('aside');
    const hamburger = event.target.closest('button[aria-label="Toggle sidebar"]');
    if (hamburger) return;
    if (!sidebar) closeSidebar();
};
onUnmounted(() => {
    window.removeEventListener('resize', checkMobile);
    document.removeEventListener('click', handleClickAway);
});
</script>

<template>
    <Head title="Hasil Riset" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-[#FDFBF7] font-sans overflow-hidden text-gray-800">
        
        <div v-if="isMobile && isSidebarOpen" class="fixed inset-0 bg-black/30 z-40" @click="closeSidebar"></div>
        <button v-if="isMobile && !isSidebarOpen" type="button"
            class="fixed top-4 left-4 z-50 p-3 bg-pkl-base-orange text-white rounded-lg shadow-lg hover:bg-orange-600 transition-all duration-300 md:hidden"
            aria-label="Toggle sidebar" @click.stop="toggleSidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <Sidebar :riset-topics="risetTopics" :selected-topic-id="selectedTopicId" active-page="hasil-riset"
            :class="['md:translate-x-0 md:static', isMobile && !isSidebarOpen ? '-translate-x-full' : 'translate-x-0']"
            @close="closeSidebar" />

        <main class="flex-1 p-6 md:p-10 h-screen overflow-y-auto relative scroll-smooth transition-all duration-300">
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-30 mix-blend-multiply z-0 fixed">
                 <img src="/images/assets/pattern kuning 1.svg" class="absolute top-0 right-0 w-[600px] opacity-30" />
            </div>

            <div v-if="activeTopic || mergedVisualizations.length > 0" class="max-w-7xl mx-auto pb-20 relative z-10">
                
                <div class="mb-8 border-b-2 border-orange-100 pb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-4 py-1 rounded-full bg-white border border-orange-200 text-orange-500 text-[11px] font-bold tracking-widest uppercase shadow-sm">
                            {{ activeTopic?.riset?.name || 'Riset PKL' }}
                        </span>
                    </div>
                    <h1 class="font-headline text-4xl md:text-6xl text-pkl-base-orange leading-none drop-shadow-sm">
                        {{ activeTopic?.name || 'Galeri Visualisasi' }}
                    </h1>
                    <p v-if="activeTopic?.description" class="mt-4 text-gray-600 max-w-3xl font-sans">
                        {{ activeTopic.description }}
                    </p>
                </div>

                <div v-if="categories.length > 0" class="mb-10 overflow-x-auto pb-2">
                    <div class="flex space-x-2 min-w-max p-1 bg-orange-50/50 rounded-xl border border-orange-100">
                        <button 
                            v-for="cat in categories" 
                            :key="cat"
                            @click="activeTab = cat"
                            class="px-6 py-2.5 rounded-lg text-sm font-bold tracking-wide transition-all duration-300 focus:outline-none"
                            :class="activeTab === cat 
                                ? 'bg-pkl-base-orange text-white shadow-md transform scale-105' 
                                : 'text-gray-500 hover:text-pkl-base-orange hover:bg-white'"
                        >
                            {{ cat }}
                        </button>
                    </div>
                </div>

                <div v-if="filteredVisualizations.length > 0" class="space-y-16">
                    <div v-for="vis in filteredVisualizations" :key="vis.id" 
                         class="bg-white rounded-[2.5rem] shadow-xl shadow-orange-900/5 border border-white overflow-hidden ring-1 ring-black/5 animate-fade-in-up">
                        
                        <div class="px-8 py-5 bg-gradient-to-r from-white to-orange-50/30 border-b border-orange-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex-1">
                                <h2 class="font-headline text-2xl text-gray-800 leading-snug">{{ vis.title }}</h2>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold tracking-widest uppercase text-white bg-pkl-compliment-teal/80 px-2 py-0.5 rounded">{{ vis.category }}</span>
                                    <span class="text-xs text-gray-400 font-medium">{{ vis.type?.type_name }}</span>
                                </div>
                            </div>
                            <button @click="triggerDownload(vis.id)" 
                                class="flex items-center gap-2 px-5 py-2.5 bg-white border border-orange-200 text-pkl-base-orange text-xs font-bold tracking-widest uppercase rounded-full shadow-sm hover:bg-orange-50 hover:shadow-md transition-all active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download
                            </button>
                        </div>

                        <div class="p-6 md:p-8 min-h-[400px] flex flex-col justify-center bg-white relative">
                            <component
                                :ref="(el) => setChartRef(el, vis.id)"
                                :is="getChartComponent(vis.type?.type_code)"
                                v-if="getChartComponent(vis.type?.type_code) && formatChartData(vis)"
                                :chart-data="formatChartData(vis)"
                                :title="vis.title"
                                :colors="pklColors" 
                                height="400" 
                            />
                            <div v-else class="flex flex-col items-center justify-center h-80 border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/50 text-center">
                                <p class="font-bold text-gray-400 text-sm uppercase tracking-wide">Visualisasi tidak tersedia</p>
                            </div>
                        </div>

                        <div class="bg-[#FDFBF7] px-8 py-8 border-t border-orange-100/50">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-8 h-8 rounded-full bg-pkl-compliment-teal/10 flex items-center justify-center text-pkl-compliment-teal shrink-0">
                                    <span class="text-lg">💡</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wide mb-1">Insight Utama</h3>
                                    <p class="text-gray-600 leading-relaxed font-sans text-[15px]">{{ vis.interpretation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center h-64 text-center border-2 border-dashed border-gray-200 rounded-3xl bg-white/50">
                    <p class="text-gray-400 font-sans">Tidak ada data untuk kategori ini.</p>
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

<style scoped>
/* Animation for Tab Switching */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
</style>