<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const navItems = [
    { id: 'hero', label: 'Beranda', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { id: 'latar-belakang', label: 'Latar Belakang', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
    { id: 'tujuan', label: 'Tujuan', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { id: 'riset', label: 'Riset', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
    { id: 'cakupan', label: 'Cakupan', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { id: 'metode-sampel', label: 'Sampel', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
    { id: 'metode-data', label: 'Metode Data', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4' },
    { id: 'hasil', label: 'Hasil', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
];

const activeId = ref('hero');
let observer = null;

const scrollToSection = (id) => {
    const element = document.getElementById(id);
    if (element) {
        // Scroll dengan sedikit offset agar tidak tertutup header fixed (jika ada)
        const yOffset = -80; 
        const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({top: y, behavior: 'smooth'});
    }
};

onMounted(() => {
    // KONFIGURASI BARU YANG LEBIH AKURAT
    const observerOptions = {
        root: null,
        // rootMargin: '-45% 0px -45% 0px' artinya:
        // "Abaikan 45% layar bagian atas" dan "Abaikan 45% layar bagian bawah".
        // Jadi area deteksi hanyalah GARIS TIPIS (10%) di tengah-tengah layar.
        // Begitu elemen menyentuh garis tengah ini, dia langsung dianggap aktif.
        rootMargin: '-45% 0px -45% 0px', 
        threshold: 0
    };

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                activeId.value = entry.target.id;
            }
        });
    }, observerOptions);

    navItems.forEach((item) => {
        const section = document.getElementById(item.id);
        if (section) observer.observe(section);
    });
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});
</script>

<template>
    <div class="fixed right-4 lg:right-6 top-1/2 -translate-y-1/2 z-50 hidden lg:flex flex-col gap-3">
        
        <button 
            v-for="item in navItems" 
            :key="item.id"
            @click="scrollToSection(item.id)"
            class="group relative flex items-center justify-center rounded-full transition-all duration-300 ease-out border border-transparent"
            :class="[
                activeId === item.id 
                    ? 'w-10 h-10 bg-[#EF874B] text-white shadow-lg scale-110 opacity-100' 
                    : 'w-8 h-8 bg-white/40 backdrop-blur-sm text-gray-600 hover:bg-white hover:scale-105 opacity-60 hover:opacity-100'
            ]"
        >
            <svg xmlns="http://www.w3.org/2000/svg" 
                class="transition-transform duration-300"
                :class="activeId === item.id ? 'w-5 h-5' : 'w-4 h-4'"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
            </svg>

            <span 
                class="absolute right-12 py-1 px-3 rounded-md text-[10px] uppercase font-bold tracking-wider 
                       bg-gray-900/80 backdrop-blur-md text-white opacity-0 -translate-x-2 pointer-events-none transition-all duration-300 
                       group-hover:opacity-100 group-hover:translate-x-0"
            >
                {{ item.label }}
            </span>
        </button>

    </div>
</template>