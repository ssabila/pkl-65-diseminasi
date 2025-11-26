<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

// Define Props dengan nilai default yang aman
const props = defineProps({
    documentCategories: { type: Array, default: () => [] },
    risetTopics: { type: Array, default: () => [] }, 
});
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
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-30 mix-blend-multiply z-0 fixed">
                <img src="/images/assets/pattern kuning 1.svg" class="absolute top-0 right-0 w-[600px] opacity-30" />
            </div>

            <div class="max-w-6xl mx-auto pb-20 relative z-10">
                <div class="mb-12">
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

                <div v-for="category in documentCategories" :key="category.id" :id="category.id" class="mb-16 scroll-mt-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-8 w-1.5 bg-gradient-to-b from-pkl-base-orange to-yellow-400 rounded-full"></div>
                        <h2 class="font-headline text-3xl text-pkl-base-orange uppercase tracking-wide">
                            {{ category.name }}
                        </h2>
                        <div class="h-px flex-1 bg-orange-200/50"></div>
                    </div>

                    <div class="grid gap-4">
                        <div v-for="doc in category.items" :key="doc.id" 
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
                </div>

                <div v-if="documentCategories.length === 0" class="flex flex-col items-center justify-center h-80 text-center opacity-60 border-2 border-dashed border-orange-200 rounded-[2.5rem]">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4 text-pkl-base-orange">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-headline text-xl text-gray-600">Belum ada dokumen.</p>
                    <p class="text-sm text-gray-400 mt-1">Silakan kembali lagi nanti.</p>
                </div>

            </div>
        </main>
    </div>
</template>