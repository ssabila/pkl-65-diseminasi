<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue'; // Import Sidebar

const props = defineProps({
    documentCategories: { type: Array, default: () => [] },
    risetTopics: { type: Array, default: () => [] }, // Pastikan controller mengirim ini
});
</script>

<template>
    <Head title="Dokumen" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden text-pkl-dark">
        
        <Sidebar 
            :riset-topics="risetTopics" 
            :document-categories="documentCategories" 
            active-page="dokumen" 
        />

        <main class="flex-1 p-6 md:p-12 h-screen overflow-y-auto scroll-smooth relative">
            <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-40 mix-blend-multiply">
                <img src="/images/assets/pattern kuning 1.svg" class="absolute top-0 right-0 w-[500px] opacity-30" />
            </div>

            <div class="max-w-6xl mx-auto pb-20 relative z-10">
                
                <div class="mb-10">
                    <div class="inline-flex items-center gap-2 mb-3 px-3 py-1.5 rounded-full bg-white/60 border border-pkl-base-orange/20">
                        <span class="w-2 h-2 rounded-full bg-pkl-base-orange"></span>
                        <span class="text-xs font-sub font-bold text-pkl-base-orange uppercase tracking-wider">
                            ARSIP & DATA
                        </span>
                    </div>
                    <h1 class="font-headline text-4xl md:text-5xl text-pkl-base-orange leading-tight uppercase">
                        Dokumen Publikasi
                    </h1>
                </div>

                <div v-for="category in documentCategories" :key="category.id" :id="category.id" class="mb-12 scroll-mt-8">
                    
                    <div class="flex items-center gap-4 mb-5">
                        <div class="h-8 w-1.5 bg-gradient-to-b from-pkl-base-orange to-pkl-compliment-yellow rounded-full"></div>
                        <h2 class="font-headline text-2xl text-pkl-base-orange uppercase tracking-wide">
                            {{ category.name }}
                        </h2>
                        <div class="h-px flex-1 bg-pkl-border"></div>
                    </div>

                    <div class="bg-white rounded-2xl border border-pkl-border shadow-sm overflow-hidden">
                        <div class="divide-y divide-pkl-border">
                            
                            <div v-for="doc in category.items" :key="doc.id" 
                                 class="group flex flex-col md:flex-row items-start md:items-center justify-between p-6 duration-200"
                            >
                                <div class="flex items-start gap-5 mb-4 md:mb-0 w-full md:w-3/4">
                                    <div class="p-3.5 rounded-xl bg-pkl-base-orange border border-pkl-base-orange/20 text-pkl-base-orange shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    
                                    <div class="w-full">
                                        <div class="flex flex-wrap items-center gap-3 mb-1">
                                            <h3 class="font-sub text-lg font-bold text-pkl-base-orange">
                                                {{ doc.name }}
                                            </h3>
                                            <span class="px-2 py-0.5 rounded bg-pkl-base-orange/10 text-pkl-base-orange text-[10px] font-bold uppercase tracking-wide">
                                                PDF
                                            </span>
                                        </div>
                                        <p class="text-sm text-pkl-base-orange leading-relaxed mb-2 font-medium">
                                            {{ doc.description || 'Dokumen resmi hasil kegiatan PKL 65.' }}
                                        </p>
                                        <div class="flex items-center gap-2 text-xs font-bold text-pkl-compliment-teal">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Diunggah: {{ doc.created_at_formatted || 'Terbaru' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 w-full md:w-auto pl-16 md:pl-0">
                                    
                                    <a :href="doc.file_url" target="_blank" 
                                       class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 rounded-lg border-2 border-pkl-border text-pkl-base-orange font-bold text-xs transition-all"
                                       title="Lihat Dokumen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        PREVIEW
                                    </a>

                                    <a :href="doc.file_url" download 
                                       class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-pkl-base-orange text-white font-bold text-xs shadow-sm hover:bg-orange-600 hover:shadow-md transition-all"
                                       title="Unduh File">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        DOWNLOAD
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="documentCategories.length === 0" class="flex flex-col items-center justify-center h-64 text-center opacity-60 border-2 border-dashed border-pkl-border rounded-2xl bg-white/50">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-pkl-base-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-sub text-pkl-dark font-bold text-lg">Belum ada dokumen tersedia.</p>
                    <p class="text-sm text-pkl-text mt-1">Silakan kembali lagi nanti.</p>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>