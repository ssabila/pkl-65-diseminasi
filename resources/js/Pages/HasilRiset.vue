<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PKL65Layout from '@/Layouts/PKL65Layout.vue';
import { computed } from 'vue';

// Import Komponen Chart yang sudah ada
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue';
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue';

// Gunakan Layout Utama
defineOptions({ layout: PKL65Layout });

// Props dari Controller
const props = defineProps({
    risetTopics: {
        type: Array,
        default: () => []
    },
    activeVisualization: {
        type: Object,
        default: null
    },
    selectedTopicId: {
        type: [String, Number],
        default: null
    }
});

// --- LOGIC MEMILIH TIPE CHART ---
const dynamicChartComponent = computed(() => {
    if (!props.activeVisualization) return null;

    // Cek Type Code dari relasi (pastikan di Controller sudah di-load 'type')
    // Atau cek ID-nya sesuai Seeder kita tadi:
    // ID 1 = Bar Chart, ID 2 = Pie Chart
    const typeId = props.activeVisualization.visualization_type_id;

    if (typeId === 1) return ApexBarChart;
    if (typeId === 2) return ApexDonutChart; // Donut & Pie mirip strukturnya
    
    return null; // Fallback jika tipe tidak dikenali
});

// --- LOGIC ADAPTER DATA (PENTING!) ---
// Mengubah format JSON Database menjadi format Props Component
const formattedChartData = computed(() => {
    if (!props.activeVisualization || !props.activeVisualization.chart_data) return null;

    const raw = props.activeVisualization.chart_data; // Data mentah dari DB

    // Format yang diharapkan Component: { labels: [], datasets: [{ label: '', data: [] }] }
    let labels = [];
    let datasets = [];

    // Skenario 1: BAR CHART (Sesuai Seeder: ada 'categories' dan 'series' object)
    if (raw.categories) {
        labels = raw.categories;
        datasets = raw.series.map(s => ({
            label: s.name,
            data: s.data
        }));
    } 
    // Skenario 2: PIE/DONUT CHART (Sesuai Seeder: ada 'labels' dan 'series' array angka)
    else if (raw.labels) {
        labels = raw.labels;
        // ApexDonutChart.vue mengharapkan datasets[0].data
        datasets = [{
            label: 'Data',
            data: raw.series // Series di Pie Chart biasanya langsung array angka [60, 20, 10]
        }];
    }

    return { labels, datasets };
});

</script>

<template>
    <Head title="Hasil Riset" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-gray-50">
        
        <aside class="w-full md:w-1/4 bg-pkl-base-orange p-6 md:min-h-screen shadow-lg z-10 relative">
            <div class="sticky top-24">
                <h2 class="font-sub text-2xl font-bold text-white mb-6 border-b border-orange-400 pb-4">
                    Website Hasil PKL 65
                </h2>

                <nav class="space-y-6 overflow-y-auto max-h-[80vh] pr-2">
                    <div v-for="riset in risetTopics" :key="riset.id" class="group">
                        <h3 class="font-sub text-lg font-semibold text-orange-100 group-hover:text-white transition-colors duration-200 mb-2">
                            {{ riset.name }} </h3>
                        
                        <ul class="space-y-1 pl-3 border-l-2 border-orange-300/50">
                            <li v-for="topic in riset.topics" :key="topic.id">
                                <Link 
                                    :href="route('hasil-riset', { topic_id: topic.id })" 
                                    preserve-scroll
                                    :class="[
                                        'block px-3 py-2 text-sm rounded-md transition-all duration-200',
                                        String(topic.id) === String(selectedTopicId)
                                            ? 'bg-white text-pkl-base-orange font-bold shadow-md translate-x-1' 
                                            : 'text-white hover:bg-orange-500 hover:text-white'
                                    ]"
                                >
                                    {{ topic.name }} </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="risetTopics.length === 0" class="text-orange-200 text-sm italic">
                        Belum ada data riset.
                    </div>
                </nav>
            </div>
        </aside>

        <main class="w-full md:w-3/4 bg-pkl-light-yellow p-4 md:p-10 overflow-x-hidden">
            
            <div v-if="activeVisualization" class="animate-fade-in max-w-5xl mx-auto">
                <div class="mb-6">
                    <h1 class="font-sub text-3xl md:text-4xl font-bold text-pkl-dark-blue mb-2">
                        {{ activeVisualization.topic?.name }}
                    </h1>
                    <p class="text-gray-600 text-sm md:text-base">
                        Bagian dari riset: <span class="font-semibold">{{ activeVisualization.topic?.riset?.name }}</span>
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="font-sub text-xl md:text-2xl text-pkl-dark-blue font-bold">
                            {{ activeVisualization.title }}
                        </h2>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                           {{ activeVisualization.type?.type_name || 'Chart' }}
                        </span>
                    </div>

                    <div class="p-4 md:p-8 min-h-[450px] bg-white relative">
                        <component 
                            :is="dynamicChartComponent" 
                            v-if="dynamicChartComponent && formattedChartData"
                            :chart-data="formattedChartData"
                            :title="activeVisualization.title"
                            height="400"
                        />
                        
                        <div v-else class="h-64 flex items-center justify-center text-gray-400 border-2 border-dashed rounded-lg">
                            <p>Data visualisasi tidak valid atau tipe chart belum didukung.</p>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-blue-50/30 border-t border-gray-100">
                        <h3 class="font-sub text-lg font-bold text-pkl-dark-blue mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Interpretasi Data
                        </h3>
                        <div class="prose max-w-none text-gray-700 text-sm md:text-base leading-relaxed whitespace-pre-line text-justify">
                            {{ activeVisualization.interpretation || 'Belum ada interpretasi untuk data ini.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="h-[80vh] flex flex-col items-center justify-center text-center space-y-6 opacity-80">
                <div class="w-40 h-40 bg-white rounded-full flex items-center justify-center shadow-lg mb-4 animate-bounce-slow">
                    <img src="/images/assets/hasil-penelitian.png" alt="Ilustrasi" class="w-24 opacity-80">
                </div>
                <div>
                    <h2 class="text-3xl font-sub font-bold text-pkl-dark-blue mb-2">Jelajahi Hasil Riset</h2>
                    <p class="text-lg text-gray-600 max-w-md mx-auto">
                        Silakan pilih topik di sidebar kiri untuk melihat detail visualisasi.
                    </p>
                </div>
            </div>

        </main>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar untuk sidebar */
nav::-webkit-scrollbar {
    width: 4px;
}
nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}
nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}
nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>