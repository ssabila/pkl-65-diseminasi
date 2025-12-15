<script setup>
import { Head } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'
import LeafletMap from '@/Components/Charts/LeafletMap.vue'
import ApexHistogramChart from '@/Components/Charts/ApexHistogramChart.vue'
import ApexBoxPlotChart from '@/Components/Charts/ApexBoxPlotChart.vue'
import ApexStackedBarChart from '@/Components/Charts/ApexStackedBarChart.vue'
import VennDiagramChart from '@/Components/Charts/VennDiagramChart.vue'
import ApexHeatmapChart from '@/Components/Charts/ApexHeatmapChart.vue'
import ApexDensityChart from '@/Components/Charts/ApexDensityChart.vue'
import ApexGroupedBarChart from '@/Components/Charts/ApexGroupedBarChart.vue'
import ApexGroupedStackedBarChart from '@/Components/Charts/ApexGroupedStackedBarChart.vue'

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
    'area-chart': ApexAreaChart,
    'peta': LeafletMap,
    'choropleth': LeafletMap,
    'histogram': ApexHistogramChart,
    'box-plot': ApexBoxPlotChart,
    'stacked-bar-chart': ApexStackedBarChart,
    'venn-diagram': VennDiagramChart,
    'heatmap-matrix': ApexHeatmapChart,
    'density-plot': ApexDensityChart,
    'grouped-bar-chart': ApexGroupedBarChart,
    'grouped-stacked-bar-chart': ApexGroupedStackedBarChart
};

// Ref untuk download multiple charts
const chartRefs = ref({});
const setChartRef = (el, id) => { if (el) chartRefs.value[id] = el; };

// Mobile sidebar state
const isMobile = ref(false);
const isSidebarOpen = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 768; // md breakpoint
    if (!isMobile.value) {
        isSidebarOpen.value = false; // Always show sidebar on desktop
    }
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};

// Handle click outside sidebar on mobile
const handleClickAway = (event) => {
    if (!isMobile.value || !isSidebarOpen.value) return;
    
    const sidebar = event.target.closest('aside');
    const hamburger = event.target.closest('button[aria-label="Toggle sidebar"]');
    
    if (hamburger) return;
    
    if (!sidebar) {
        closeSidebar();
    }
};

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
    document.addEventListener('click', handleClickAway);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile);
    document.removeEventListener('click', handleClickAway);
});

const getChartComponent = (typeCode) => chartComponents[typeCode] || null;

const formatChartData = (vis) => {
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
            // Untuk peta heatmap, data berupa array koordinat dari upload Excel
            if (rawData.points && Array.isArray(rawData.points)) {
                return { heatmapData: rawData.points };
            }
            return rawData;
        } else if (typeCode === 'choropleth') {
            // Handle choropleth data - pass directly if already in correct format
            if (rawData.regions && rawData.selectedVariable && Array.isArray(rawData.regions)) {
                // Load GeoJSON data asynchronously  
                const loadGeojson = async () => {
                    try {
                        const response = await fetch('/geojson/yogyakarta.geojson');
                        if (!response.ok) throw new Error('Failed to load GeoJSON');
                        return await response.json();
                    } catch (error) {
                        console.error('Error loading GeoJSON:', error);
                        return null;
                    }
                };
                
                const result = { 
                    regions: rawData.regions,
                    selectedVariable: rawData.selectedVariable,
                    variables: rawData.variables || [],
                    geojson: rawData.geojson || null,
                    loadGeojson
                };
                
                console.log('Choropleth data formatted:', result);
                return result;
            }
            
            console.log('Choropleth data invalid:', rawData);
            return null;
        } else if (typeCode === 'histogram') {
            // Histogram data format: { labels: ['0-10', '10-20', ...], datasets: [{name: 'Frekuensi', data: [5, 10, ...]}] }
            if (rawData.labels && Array.isArray(rawData.datasets)) {
                return rawData;
            }
            return null;
        } else if (typeCode === 'box-plot') {
            // BoxPlot data format: { labels: ['Group1', 'Group2'], datasets: [{name: 'Data', type: 'boxPlot', data: [{x: 'Group1', y: [min, q1, median, q3, max]}, ...]}] }
            if (rawData.labels && Array.isArray(rawData.datasets)) {
                return rawData;
            }
            return null;
        } else if (['stacked-bar-chart', 'grouped-bar-chart', 'grouped-stacked-bar-chart'].includes(typeCode)) {
            // Multiple series bar charts: { labels: ['Cat1', 'Cat2'], datasets: [{name: 'Series1', data: [...]}, {name: 'Series2', data: [...]}] }
            if (rawData.labels && Array.isArray(rawData.datasets)) {
                return rawData;
            }
            return null;
        } else if (typeCode === 'venn-diagram') {
            // Venn diagram format: { vennData: { sets: [{name: 'A', size: 100}], overlaps: [{sets: ['A', 'B'], size: 30}] } }
            if (rawData.vennData) {
                return rawData;
            }
            return null;
        } else if (typeCode === 'heatmap-matrix') {
            // Heatmap format: { categories: ['X1', 'X2'], datasets: [{name: 'Y1', data: [10, 20, ...]}, ...] }
            if (rawData.categories && Array.isArray(rawData.datasets)) {
                return rawData;
            }
            return null;
        } else if (typeCode === 'density-plot') {
            // Density plot format: { labels: [values], datasets: [{name: 'Density', data: [densities]}] }
            if (rawData.labels && Array.isArray(rawData.datasets)) {
                return rawData;
            }
            return null;
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
        
        <!-- Mobile Overlay -->
        <div
            v-if="isMobile && isSidebarOpen"
            class="fixed inset-0 bg-black/30 z-40"
            @click="closeSidebar"></div>
            
        <!-- Mobile Hamburger Button -->
        <button
            v-if="isMobile && !isSidebarOpen"
            type="button"
            class="fixed top-4 left-4 z-50 p-3 bg-pkl-base-orange text-white rounded-lg shadow-lg hover:bg-orange-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-300 md:hidden"
            aria-label="Toggle sidebar"
            @click.stop="toggleSidebar">
            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <Sidebar 
            :riset-topics="risetTopics" 
            :selected-topic-id="selectedTopicId" 
            active-page="hasil-riset"
            :class="[
                'md:translate-x-0 md:static',
                isMobile && !isSidebarOpen ? '-translate-x-full' : 'translate-x-0'
            ]"
            @close="closeSidebar" 
        />

        <main class="flex-1 p-6 md:p-10 h-screen overflow-y-auto relative scroll-smooth transition-all duration-300">
            <div class="fixed top-0 right-0 w-full h-full pointer-events-none opacity-30 mix-blend-multiply z-0">
                 <img src="/images/assets/pattern kuning 1.png" class="absolute top-0 right-0 w-[600px] opacity-30" />
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
                                <p class="text-xs text-gray-400 mt-2">
                                    Type: {{ vis.type?.type_code || 'undefined' }} | 
                                    Component: {{ !!getChartComponent(vis.type?.type_code) ? 'Found' : 'Not Found' }} | 
                                    Data: {{ !!formatChartData(vis) ? 'Valid' : 'Invalid' }}
                                </p>
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