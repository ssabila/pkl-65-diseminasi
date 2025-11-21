<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    activeVisualization: { type: Object, default: null },
    selectedTopicId: { type: [String, Number], default: null }
})

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
const dynamicChartComponent = computed(() => {
    if (!props.activeVisualization || !props.activeVisualization.type) return null
    const code = props.activeVisualization.type.type_code
    return code === 'bar-chart' ? ApexBarChart : code === 'pie-chart' ? ApexDonutChart : null
})

const formattedChartData = computed(() => {
    if (!props.activeVisualization?.chart_data) return null
    const raw = props.activeVisualization.chart_data
    if (raw.categories) {
        return {
            labels: raw.categories,
            datasets: raw.series.map(s => ({ label: s.name, data: s.data }))
        }
    } else if (raw.labels) {
        return { labels: raw.labels, datasets: [{ label: 'Data', data: raw.series }] }
    }
    return null
})
</script>

<template>
    <Head title="Hasil Riset" />

    <div
        class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden">
        <aside
            class="w-full md:w-80 bg-pkl-base-orange flex-shrink-0 md:h-screen shadow-2xl z-20 flex flex-col relative">
            <div class="px-6 pt-6 pb-3 z-10">
                <Link
                    href="/"
                    class="group flex items-center gap-2 mb-4 transition-transform duration-300 hover:translate-x-1">
                    <img
                        src="/images/assets/LOGO-PKL_REV8.png"
                        alt="Logo PKL 65"
                        class="h-10 w-auto transition group-hover:opacity-100" />

                    <div class="flex flex-col justify-center">
                        <h2
                            class="font-headline text-lg text-white tracking-wide drop-shadow-sm leading-tight group-hover:text-pkl-compliment-yellow transition">
                            WEBSITE HASIL PKL 65
                        </h2>
                        <p
                            class="font-sans text-white text-[10px] mt-0.5 font-bold tracking-[0.15em] opacity-70 group-hover:opacity-100 transition">
                            TAHUN AJARAN 2025/2026
                        </p>
                    </div>
                </Link>
                <div class="h-px w-full bg-white/20"></div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide">
                <div
                    v-for="riset in risetTopics"
                    :key="riset.id"
                    class="mt-3 border-b border-white/10 pb-1">
                    <button
                        @click="toggleRiset(riset.id)"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-white hover:bg-white/10 rounded-lg transition-all group focus:outline-none">
                        <h3
                            class="font-sans text-base font-bold text-white/90 tracking-wide uppercase text-left">
                            {{ riset.name }}
                        </h3>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-white/70 transition-transform duration-300"
                            :class="{ 'rotate-180 text-white': openRisetId === riset.id }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        class="overflow-hidden transition-all duration-500 ease-in-out"
                        :class="
                            openRisetId === riset.id
                                ? 'max-h-96 opacity-100 mt-1'
                                : 'max-h-0 opacity-0'
                        ">
                        <ul class="space-y-1 pl-2">
                            <li v-for="topic in riset.topics" :key="topic.id">
                                <Link
                                    :href="route('hasil-riset', { topic_id: topic.id })"
                                    preserve-scroll
                                    :class="[
                                        'flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-300 w-full relative overflow-hidden shadow-sm', // py-2.5 -> py-2
                                        String(topic.id) === String(selectedTopicId)
                                            ? 'bg-white text-pkl-base-orange shadow-md translate-x-1 font-bold'
                                            : 'text-white/80 hover:text-white hover:bg-white/10 hover:translate-x-1 font-medium'
                                    ]">
                                    <span class="relative z-10 tracking-wide font-sans text-sm">
                                        {{ topic.name }}
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <h3
                        class="px-4 font-sub text-[10px] font-bold text-white/60 tracking-widest mb-2 uppercase">
                        LAINNYA
                    </h3>
                    <ul class="space-y-1 pl-2">
                        <li>
                            <Link
                                :href="route('dokumen')"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                                DOKUMEN
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/"
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                                KEMBALI KE BERANDA
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="p-4 text-center opacity-60 bg-orange-900/10 mt-auto">
                <p class="text-[10px] text-white font-sub uppercase tracking-widest">
                    &copy; 2025 Polstat STIS
                </p>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12 h-screen overflow-y-auto scroll-smooth relative">
            <div
                class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-50 mix-blend-multiply">
                <img
                    src="/images/assets/pattern kuning 1.svg"
                    class="absolute top-0 right-0 w-96 opacity-20" />
            </div>

            <div
                v-if="activeVisualization"
                class="animate-fade-in max-w-6xl mx-auto pb-20 relative z-10">
                <div class="mb-8">
                    <h1
                        class="font-headline text-4xl md:text-5xl text-pkl-dark-blue mb-3 leading-tight uppercase">
                        {{ activeVisualization.topic?.name }}
                    </h1>
                    <span
                        class="bg-pkl-compliment-blue text-white py-1.5 px-4 rounded-full text-xs font-sub font-bold uppercase tracking-wider shadow-md">
                        {{ activeVisualization.topic?.riset?.name }}
                    </span>
                </div>

                <div
                    class="bg-white rounded-[2rem] shadow-xl border border-white/50 overflow-hidden mb-10">
                    <div class="p-8 border-b border-gray-100 bg-white">
                        <h2
                            class="font-sub text-lg text-gray-800 font-bold tracking-tight uppercase">
                            {{ activeVisualization.title }}
                        </h2>
                    </div>
                    <div class="p-8 min-h-[450px] flex flex-col justify-center bg-white">
                        <component
                            :is="dynamicChartComponent"
                            v-if="dynamicChartComponent && formattedChartData"
                            :chart-data="formattedChartData"
                            :title="activeVisualization.title"
                            height="400" />
                        <div
                            v-else
                            class="flex items-center justify-center h-64 bg-gray-50 rounded-xl border-2 border-dashed">
                            <p class="font-sub text-gray-400">Chart belum tersedia</p>
                        </div>
                    </div>
                    <div class="px-8 py-8 bg-blue-50/50 border-t border-blue-100">
                        <h3 class="font-sub text-lg font-bold text-pkl-compliment-blue mb-3">
                            💡 Interpretasi Data
                        </h3>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100">
                            <p
                                class="text-gray-700 leading-relaxed text-justify text-base font-sans">
                                {{
                                    activeVisualization.interpretation ||
                                    'Belum ada interpretasi data.'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="h-full flex flex-col items-center justify-center text-center opacity-80">
                <h2 class="font-headline text-4xl text-pkl-compliment-blue">Mulai Eksplorasi</h2>
                <p class="font-sub text-gray-500 mt-2">Pilih topik di sidebar kiri.</p>
            </div>
        </main>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
