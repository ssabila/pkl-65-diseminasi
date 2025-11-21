<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    // Hanya menerima ID dokumen yang sedang aktif dari Controller
    selectedDocumentId: { type: [String, Number], default: null }
});

// --- DATA DOKUMEN HARDCODE DI FRONTEND ---
const documentCategories = [
    {
        id: 1,
        name: 'DOKUMEN UTAMA',
        items: [
            { id: 101, name: 'Laporan Akhir PKL 65', file_url: '/dokumen/laporan_akhir.pdf' },
            { id: 102, name: 'Manual Pengguna Sistem', file_url: '/dokumen/manual_sistem.pdf' },
        ],
    },
    {
        id: 2,
        name: 'DATA PENDUKUNG',
        items: [
            { id: 201, name: 'Kuesioner Lapangan', file_url: '/dokumen/kuesioner.pdf' },
            { id: 202, name: 'Metadata Final', file_url: '/dokumen/metadata.zip' },
        ],
    }
];

// --- LOGIC ACCORDION ---
const openDocId = ref(null);

const toggleDoc = (categoryId) => {
    openDocId.value = openDocId.value === categoryId ? null : categoryId;
};

// Auto-buka kategori saat halaman dimuat
onMounted(() => {
    if (props.selectedDocumentId) {
        const activeCategory = documentCategories.find(c => 
            c.items.some(i => String(i.id) === String(props.selectedDocumentId))
        );
        if (activeCategory) {
            openDocId.value = activeCategory.id;
        }
    } else if (documentCategories.length > 0) {
        // Buka kategori pertama by default jika tidak ada yang dipilih
        openDocId.value = documentCategories[0].id;
    }
});

// Logic untuk menampilkan detail dokumen yang dipilih
const activeDocument = computed(() => {
    // Mencari dokumen yang cocok dengan ID yang ada di URL
    if (!props.selectedDocumentId) return null;
    return documentCategories.flatMap(c => c.items).find(i => String(i.id) === String(props.selectedDocumentId));
});
</script>

<template>
    <Head title="Dokumen" />

    <div class="flex flex-col md:flex-row min-h-screen w-full bg-pkl-base-cream font-sans overflow-hidden">
        
        <aside class="w-full md:w-80 bg-pkl-base-orange flex-shrink-0 md:h-screen shadow-2xl z-20 flex flex-col relative">
            
            <div class="px-6 pt-8 pb-4 z-10">
                <Link href="/" class="group flex items-center gap-3 mb-6 transition-transform duration-300 hover:translate-x-1">
                    <img src="/images/assets/LOGO-PKL_REV8.png" alt="Logo PKL 65" class="h-10 w-auto brightness-0 invert opacity-90 group-hover:opacity-100">
                    <div class="flex flex-col justify-center">
                        <h2 class="font-headline text-2xl text-white tracking-wide drop-shadow-sm leading-none group-hover:text-pkl-compliment-yellow transition">DOKUMEN PKL</h2>
                        <p class="font-sub text-white text-[10px] mt-0.5 font-bold tracking-[0.15em] opacity-70 group-hover:opacity-100 transition">TAHUN AJARAN 2025/2026</p>
                    </div>
                </Link>
                <div class="h-px w-full bg-white/20"></div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide">
                
                <div v-for="category in documentCategories" :key="category.id" class="mt-4 border-b border-white/10 pb-2">
                    
                    <button 
                        @click="toggleDoc(category.id)"
                        class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-white/10 rounded-xl transition-all group focus:outline-none"
                    >
                        <h3 class="font-sans text-base font-bold text-white/90 tracking-wide uppercase text-left">
                            {{ category.name }}
                        </h3>
                        
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            class="h-4 w-4 text-white/70 transition-transform duration-300"
                            :class="{'rotate-180 text-white': openDocId === category.id}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div 
                        class="overflow-hidden transition-all duration-500 ease-in-out"
                        :class="openDocId === category.id ? 'max-h-96 opacity-100 mt-1' : 'max-h-0 opacity-0'"
                    >
                        <ul class="space-y-1 pl-3">
                            <li v-for="item in category.items" :key="item.id">
                                <Link 
                                    :href="route('dokumen', { document_id: item.id })" 
                                    preserve-scroll
                                    :class="[
                                        'flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 w-full relative overflow-hidden shadow-sm',
                                        String(item.id) === String(selectedDocumentId)
                                            ? 'bg-white text-pkl-base-orange shadow-md translate-x-1 font-bold' 
                                            : 'text-white/80 hover:text-white hover:bg-white/10 hover:translate-x-1 font-medium'
                                    ]"
                                >
                                    <span class="relative z-10 tracking-wide font-sans text-sm">
                                        {{ item.name }}
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 pt-2 border-t border-white/10">
                     <h3 class="px-3 font-sub text-[10px] font-bold text-white/60 tracking-widest mb-2 uppercase">LAINNYA</h3>
                    <ul class="space-y-1 pl-2">
                        <li>
                            <Link :href="route('hasil-riset')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white hover:bg-white/10 hover:translate-x-1 font-sans transition-all w-full">
                                HASIL RISET
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

             <div class="p-4 text-center opacity-60 bg-orange-900/10 mt-auto">
                <p class="text-[10px] text-white font-sub uppercase tracking-widest">
                    &copy; 2025 Polstat STIS
                </p>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12 h-screen overflow-y-auto scroll-smooth relative">
            <div class="animate-fade-in max-w-4xl mx-auto pb-20 relative z-10">
                
                <div v-if="activeDocument">
                    <h1 class="font-headline text-4xl text-pkl-dark-blue mb-4">
                        {{ activeDocument.name }}
                    </h1>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                        <div class="h-[60vh] bg-gray-50 flex items-center justify-center border-2 border-dashed border-gray-200 rounded-lg mb-6">
                            <p class="text-gray-500 font-sans">
                                [PDF Viewer / Tampilan Dokumen Disini]
                            </p>
                        </div>
                        <a :href="activeDocument.file_url" target="_blank" class="inline-flex items-center justify-center px-6 py-3 bg-pkl-base-orange text-white text-base font-bold rounded-xl shadow-lg hover:bg-orange-600 transition duration-150">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414zM10 3a1 1 0 011 1v7a1 1 0 11-2 0V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            UNDUH DOKUMEN
                        </a>
                    </div>
                </div>

                <div v-else class="h-full flex flex-col items-center justify-center text-center opacity-80 mt-16">
                     <h2 class="font-headline text-4xl text-pkl-compliment-blue mb-2">Arsip Data dan Laporan</h2>
                     <p class="font-sub text-gray-500 mt-2 max-w-sm">Pilih dokumen di sidebar kiri untuk melihat detail file dan mengunduh.</p>
                </div>

            </div>
        </main>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>