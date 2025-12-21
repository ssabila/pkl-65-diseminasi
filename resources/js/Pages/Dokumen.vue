<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed } from 'vue';

// Define Props
const props = defineProps({
    documentCategories: { type: Array, default: () => [] },
    risetTopics: { type: Array, default: () => [] }, 
});

// State untuk kategori yang sedang aktif (default: kategori pertama)
const activeCategoryId = ref(props.documentCategories.length > 0 ? props.documentCategories[0].id : null);

// Data kategori yang sedang dipilih
const activeCategory = computed(() => {
    return props.documentCategories.find(cat => cat.id === activeCategoryId.value) || null;
});

// Fungsi untuk pindah tab
const setTab = (id) => {
    activeCategoryId.value = id;
};
</script>

<template>
    <Head title="Dokumen" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-[#FDFBF7] font-sans overflow-hidden text-gray-800">
        
        <Sidebar 
            :riset-topics="risetTopics" 
            :document-categories="documentCategories" 
            active-page="dokumen" 
        />

        <main class="flex-1 p-6 md:p-12 h-screen overflow-y-auto scroll-smooth relative">
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-30 mix-blend-multiply z-0">
                <img src="/images/assets/pattern kuning 1.png" class="absolute top-0 right-0 w-[600px] opacity-30" />
            </div>

            <div class="max-w-6xl mx-auto pb-20 relative z-10">
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 mb-3 px-3 py-1.5 rounded-full bg-white border border-orange-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-pkl-base-orange"></span>
                        <span class="text-xs font-bold text-orange-500 uppercase tracking-wider">
                            ARSIP & DATA
                        </span>
                    </div>
                    <h1 class="font-headline text-5xl text-pkl-base-orange leading-tight drop-shadow-sm">
                        Dokumen Publikasi
                    </h1>
                </div>

                <div v-if="documentCategories.length > 0" class="mb-10 overflow-x-auto">
                    <div class="flex items-center gap-2 border-b border-orange-100 pb-px min-w-max">
                        <button 
                            v-for="category in documentCategories" 
                            :key="category.id"
                            @click="setTab(category.id)"
                            :class="[
                                'px-6 py-3 text-sm font-bold tracking-widest transition-all duration-300 border-b-2 uppercase',
                                activeCategoryId === category.id 
                                    ? 'border-pkl-base-orange text-pkl-base-orange bg-orange-50/50' 
                                    : 'border-transparent text-gray-400 hover:text-orange-400 hover:border-orange-200'
                            ]"
                        >
                            {{ category.name }}
                            <span class="ml-2 text-[10px] opacity-60">({{ category.items?.length || 0 }})</span>
                        </button>
                    </div>
                </div>

                <div v-if="activeCategory" class="animate-fade-in">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-8 w-1.5 bg-gradient-to-b from-pkl-base-orange to-yellow-400 rounded-full"></div>
                        <h2 class="font-headline text-2xl text-pkl-base-orange uppercase tracking-wide">
                            Daftar {{ activeCategory.name }}
                        </h2>
                    </div>

                    <div class="grid gap-4">
                        <div v-for="doc in activeCategory.items" :key="doc.id" 
                             class="group bg-white rounded-2xl p-6 border border-white shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col md:flex-row items-start md:items-center gap-6"
                        >
                            <div class="p-4 rounded-2xl bg-orange-50 text-pkl-base-orange group-hover:bg-pkl-base-orange group-hover:text-white transition-colors duration-300 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-headline text-xl text-gray-800 mb-2 group-hover:text-pkl-base-orange transition-colors">
                                    {{ doc.name }}
                                </h3>
                                <p class="text-sm text-gray-500 leading-relaxed mb-3">
                                    {{ doc.description || 'Dokumen resmi hasil kegiatan PKL 65.' }}
                                </p>
                                <div class="flex items-center gap-3 text-xs font-bold text-teal-600 uppercase tracking-wider">
                                    <span class="px-2 py-1 bg-teal-50 rounded">PDF</span>
                                    <span>•</span>
                                    <span>{{ doc.created_at_formatted || 'Terbaru' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0">
                                <a :href="doc.file_url" target="_blank" 
                                   class="flex-1 md:flex-none px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold text-xs hover:bg-gray-50 transition-all text-center">
                                    PREVIEW
                                </a>
                                <a :href="doc.file_url" download 
                                   class="flex-1 md:flex-none px-5 py-2.5 rounded-xl bg-pkl-base-orange text-white font-bold text-xs shadow-md hover:bg-orange-600 hover:shadow-lg transition-all text-center flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    DOWNLOAD
                                </a>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeCategory.items?.length === 0" class="flex flex-col items-center justify-center h-64 text-center opacity-60 bg-gray-50/50 border-2 border-dashed border-gray-200 rounded-[2rem]">
                        <p class="font-headline text-lg text-gray-500">Kategori ini belum memiliki dokumen.</p>
                    </div>
                </div>

                <div v-if="documentCategories.length === 0" class="flex flex-col items-center justify-center h-80 text-center opacity-60 border-2 border-dashed border-orange-200 rounded-[2.5rem]">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4 text-pkl-base-orange">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-headline text-xl text-gray-600">Belum ada kategori dokumen.</p>
                    <p class="text-sm text-gray-400 mt-1">Silakan hubungi admin atau kembali lagi nanti.</p>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Sembunyikan scrollbar pada navigasi tab mobile */
.overflow-x-auto {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}
</style>