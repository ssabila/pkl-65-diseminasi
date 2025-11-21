<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    // Data dinamis dari Database via Controller
    documentCategories: { type: Array, default: () => [] },
});

// --- LOGIC SCROLL TO CATEGORY (Navigasi Cepat) ---
const scrollToCategory = (catId) => {
    const element = document.getElementById(catId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// --- LOGIC SIDEBAR (SAMA PERSIS DENGAN HASILRISET.VUE) ---
// Kita perlu dummy data risetTopics atau logic ini biar sidebar tidak error
// jika props.risetTopics tidak dikirim ke halaman ini.
// Idealnya Layout menangani ini, tapi untuk konsistensi visual kita hardcode strukturnya.
const openRisetId = ref(null);
const toggleRiset = (id) => {
    openRisetId.value = openRisetId.value === id ? null : id;
};
</script>

<template>
    <Head title="Dokumen" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden text-pkl-dark">
        
        <aside class="w-full md:w-80 bg-pkl-base-orange flex-shrink-0 md:h-screen shadow-2xl z-20 flex flex-col relative">
            
            <div class="px-6 pt-8 pb-4 z-10">
                <Link href="/" class="group flex items-center gap-3 mb-4 transition-transform duration-300 hover:translate-x-1">
                    <img src="/images/assets/LOGO-PKL_REV8.png" alt="Logo PKL 65" class="h-12 w-auto drop-shadow-md transition group-hover:scale-105">
                    <div class="flex flex-col justify-center">
                        <h2 class="font-headline text-xl text-white tracking-wider drop-shadow-sm leading-none group-hover:text-pkl-compliment-yellow transition">
                            HASIL PKL 65
                        </h2>
                        <p class="font-sub text-white text-[10px] mt-1 font-bold tracking-[0.2em] opacity-80 group-hover:opacity-100 transition">
                            TAHUN AJARAN 2025/2026
                        </p>
                    </div>
                </Link>
                <div class="h-px w-full bg-white/30"></div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide">
                
                <div class="mt-4 mb-2 px-2">
                    <h3 class="font-sub text-[10px] font-bold text-white/60 tracking-[0.2em] uppercase mb-3">
                        KATEGORI DOKUMEN
                    </h3>
                </div>
                
                <div v-for="category in documentCategories" :key="category.id" class="mb-1">
                    <button 
                        @click="scrollToCategory(category.id)"
                        class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-white/10 rounded-xl transition-all group focus:outline-none text-left"
                    >
                        <span class="font-sub text-sm font-bold tracking-wide uppercase">
                            {{ category.name }}
                        </span>
                        <span class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded-md group-hover:bg-white group-hover:text-pkl-base-orange transition">
                            {{ category.items.length }}
                        </span>
                    </button>
                </div>

                <div class="mt-8 pt-4 border-t border-white/20">
                     <h3 class="px-2 font-sub text-[10px] font-bold text-white/60 tracking-[0.2em] mb-3 uppercase">MENU UTAMA</h3>
                    <ul class="space-y-1 pl-1">
                        <li>
                            <Link :href="route('hasil-riset')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                                HASIL RISET
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('dokumen')" class="flex items-center px-4 py-2.5 text-sm font-bold rounded-lg bg-white text-pkl-base-orange shadow-md translate-x-1 font-sans transition-all w-full">
                                DOKUMEN
                            </Link>
                        </li>
                        <li>
                            <Link href="/" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                                KEMBALI KE BERANDA
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>

             <div class="p-6 text-center opacity-70 bg-black/5 mt-auto">
                <p class="text-[10px] text-white font-sub uppercase tracking-widest font-bold">
                    &copy; 2025 Polstat STIS
                </p>
            </div>
        </aside>

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
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>