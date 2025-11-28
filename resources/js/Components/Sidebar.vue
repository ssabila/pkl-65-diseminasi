<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// Tambahkan emits biar bisa laporan ke Layout kalau mau tutup sidebar
const emit = defineEmits(['close']);

const props = defineProps({
    risetTopics: { type: Array, default: () => [] },
    documentCategories: { type: Array, default: () => [] },
    activePage: { type: String, default: '' }, 
    selectedTopicId: { type: [String, Number], default: null },
});

const safeRisetTopics = computed(() => Array.isArray(props.risetTopics) ? props.risetTopics : []);
const safeDocumentCategories = computed(() => Array.isArray(props.documentCategories) ? props.documentCategories : []);

const openRisetId = ref(null);

const toggleRiset = (id) => {
    openRisetId.value = openRisetId.value === id ? null : id;
};

// Fungsi helper: Kirim sinyal close ke parent (buat mobile)
const closeSidebar = () => {
    emit('close');
};

// Auto-open accordion
onMounted(() => {
    if (props.selectedTopicId && safeRisetTopics.value.length > 0) {
        const activeRiset = safeRisetTopics.value.find(r =>
            r.topics?.some(t => String(t.id) === String(props.selectedTopicId))
        );
        if (activeRiset) openRisetId.value = activeRiset.id;
    } 
    else if (props.activePage === 'hasil-riset' && safeRisetTopics.value.length > 0) {
        openRisetId.value = safeRisetTopics.value[0].id;
    }
});

const scrollToCategory = (catId) => {
    const element = document.getElementById(catId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Tutup sidebar di mobile setelah klik filter
        closeSidebar();
    }
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-50 w-72 md:w-80 bg-pkl-base-orange flex flex-col shadow-2xl transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:h-screen md:shadow-none"
           v-bind="$attrs">
        
        <div class="px-6 pt-6 pb-3 z-10 flex items-start justify-between">
            <Link href="/" class="group flex items-center gap-2 mb-4 transition-transform duration-300 hover:translate-x-1">
                <img src="/images/assets/LOGO-PKL_REV8.png" alt="Logo PKL 65" class="h-10 w-10 rounded-full bg-white p-1 transition group-hover:opacity-100" />
                <div class="flex flex-col justify-center">
                    <h2 class="font-headline text-lg text-white tracking-wide drop-shadow-sm leading-tight group-hover:text-pkl-compliment-yellow transition">
                        Website Hasil PKL 65
                    </h2>
                    <p class="font-sans text-white text-[10px] mt-0.5 font-bold tracking-[0.15em] opacity-70 group-hover:opacity-100 transition">
                        Tahun Ajaran 2025/2026
                    </p>
                </div>
            </Link>

            <button @click="closeSidebar" class="md:hidden text-white/80 hover:text-white focus:outline-none p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-6">
            <div class="h-px w-full bg-white/20"></div>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide mt-3">
            
            <div v-for="riset in safeRisetTopics" :key="riset.id" class="border-b border-white/10 pb-1">
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
                            <Link :href="route('hasil-riset', { topic_id: topic.id })" 
                                  @click="closeSidebar" 
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

            <div class="mt-6 pt-2 border-t border-white/20">
                <h3 class="px-4 font-sub text-[10px] font-bold text-white/60 tracking-widest mb-2 uppercase">LAINNYA</h3>
                <ul class="space-y-1 pl-2">
                    
                    <li>
                        <Link :href="route('dokumen')" 
                              @click="closeSidebar"
                              :class="['flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all w-full',
                              activePage === 'dokumen'
                                ? 'bg-white text-pkl-base-orange shadow-md translate-x-1 font-bold' 
                                : 'text-white hover:bg-white/10 hover:translate-x-1']">
                            DOKUMEN
                        </Link>

                        <div v-if="activePage === 'dokumen' && safeDocumentCategories.length > 0" class="mt-2 mb-2 pl-4 ml-2 border-l border-white/20 animate-fade-in">
                            <h4 class="text-[9px] font-bold text-white/50 tracking-widest mb-2 uppercase pl-2 pt-1">
                                FILTER KATEGORI
                            </h4>
                            <div v-for="category in safeDocumentCategories" :key="category.id" class="mb-1">
                                <button 
                                    @click="scrollToCategory(category.id)"
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-md transition-all group focus:outline-none text-left"
                                >
                                    <span class="font-sans text-xs font-medium tracking-wide">
                                        {{ category.name }}
                                    </span>
                                    <span class="bg-white/20 text-white text-[9px] font-bold px-1.5 py-0.5 rounded group-hover:bg-white group-hover:text-pkl-base-orange transition">
                                        {{ category.items?.length || 0 }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </li>

                    <li>
                        <Link href="/" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                            KEMBALI KE BERANDA
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="p-4 text-center opacity-60 bg-orange-900/10 mt-auto">
            <p class="text-[10px] text-white font-sub uppercase tracking-widest">&copy; 2025 Polstat STIS</p>
        </div>
    </aside>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; opacity: 0; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>