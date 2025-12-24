<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'

const emit = defineEmits(['close'])

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    documentCategories: { type: Array, default: () => [] },
    activePage: { type: String, default: '' },
    selectedTopicId: { type: [String, Number], default: null }
})

const safeRisetTopics = computed(() => (Array.isArray(props.risetTopics) ? props.risetTopics : []))
const openRisetId = ref(null)

const toggleRiset = id => {
    openRisetId.value = openRisetId.value === id ? null : id
}

const closeSidebar = () => {
    emit('close')
}

onMounted(() => {
    if (props.selectedTopicId && safeRisetTopics.value.length > 0) {
        const activeRiset = safeRisetTopics.value.find(r =>
            r.topics?.some(t => String(t.id) === String(props.selectedTopicId))
        )
        if (activeRiset) openRisetId.value = activeRiset.id
    } else if (props.activePage === 'hasil-riset' && safeRisetTopics.value.length > 0) {
        openRisetId.value = safeRisetTopics.value[0].id
    }
})
</script>

<template>
    <aside
        class="fixed inset-y-0 left-0 z-50 w-72 md:w-80 bg-[#E87A3E] flex flex-col shadow-2xl transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:h-screen md:shadow-none"
        v-bind="$attrs">
        
        <div class="px-6 pt-6 pb-3 z-10 flex items-start justify-between">
            <Link
                href="/"
                class="group flex items-center gap-2 mb-4 transition-transform duration-300 hover:translate-x-1">
                <img
                    src="/images/assets/LOGO-PKL_REV8.png"
                    alt="Logo PKL 65"
                    class="h-10 w-10 rounded-full bg-white p-1" />
                <div class="flex flex-col justify-center">
                    <h2
                        class="font-headline text-lg text-white tracking-wide leading-tight group-hover:text-yellow-200 transition">
                        Website Hasil PKL 65
                    </h2>
                    <p
                        class="font-sans text-white text-[10px] mt-0.5 font-bold tracking-[0.15em] opacity-70">
                        Tahun Ajaran 2025/2026
                    </p>
                </div>
            </Link>

            <button @click="closeSidebar" class="md:hidden text-white/80 hover:text-white p-1">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-6">
            <div class="h-px w-full bg-white/20"></div>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide mt-3">
            <div
                v-for="riset in safeRisetTopics"
                :key="riset.id"
                class="border-b border-white/10 pb-1">
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
                    class="overflow-hidden transition-all duration-500"
                    :class="
                        openRisetId === riset.id ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0'
                    ">
                    <ul class="space-y-1 pl-2">
                        <li v-for="topic in riset.topics" :key="topic.id">
                            <Link
                                :href="route('hasil-riset', { topic_id: topic.id })"
                                @click="closeSidebar"
                                :class="[
                                    'flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-300 w-full shadow-sm',
                                    // REVISI: Text aktif pakai warna orange solid (#E87A3E)
                                    String(topic.id) === String(selectedTopicId)
                                        ? 'bg-white text-[#E87A3E] shadow-md translate-x-1 font-bold'
                                        : 'text-white/80 hover:text-white hover:bg-white/10 hover:translate-x-1'
                                ]">
                                <span class="tracking-wide font-sans text-sm">
                                    {{ topic.name }}
                                </span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 pt-2 border-t border-white/20">
                <h3
                    class="px-4 font-sub text-[10px] font-bold text-white/60 tracking-widest mb-2 uppercase">
                    LAINNYA
                </h3>
                <ul class="space-y-1 pl-2">
                    <li>
                        <Link
                            :href="route('dokumen')"
                            @click="closeSidebar"
                            :class="[
                                'flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all w-full',
                                // REVISI: Text aktif pakai #E87A3E
                                activePage === 'dokumen'
                                    ? 'bg-white text-[#E87A3E] shadow-md translate-x-1 font-bold'
                                    : 'text-white hover:bg-white/10 hover:translate-x-1'
                            ]">
                            DOKUMEN
                        </Link>
                    </li>

                    <li class="mt-4 px-2">
                        <Link
                            href="/"
                            class="flex items-center justify-center gap-2 px-4 py-3 text-xs font-bold rounded-xl bg-white text-[#E87A3E] shadow-lg hover:bg-yellow-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 w-full group tracking-widest">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 transition-transform group-hover:scale-110"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            KEMBALI KE BERANDA
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="p-4 text-center opacity-60 bg-black/10 mt-auto">
            <p class="text-[10px] text-white font-sub uppercase tracking-widest">
                &copy; 2025 Polstat STIS
            </p>
        </div>
    </aside>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>