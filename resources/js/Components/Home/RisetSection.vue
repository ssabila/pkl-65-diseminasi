<script setup>
import { onMounted, ref } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

// --- DATA RISET (5 ITEM) ---
const risetsStatis = [
    {
        id: 1,
        judul: 'Riset 1',
        deskripsi_singkat: 'Analisis mendalam mengenai pola kerja dan demografi pekerja gig di sektor transportasi online dan dampaknya terhadap kesejahteraan.',
        image: '/images/assets/Gundatala_Riset 1.png'
    },
    {
        id: 2,
        judul: 'Riset 2',
        deskripsi_singkat: 'Evaluasi efektivitas program jaminan sosial bagi pekerja lepas dan identifikasi kesenjangan perlindungan yang ada.',
        image: '/images/assets/Gundatala_Riset 2.png'
    },
    {
        id: 3, 
        judul: 'Riset 3',
        deskripsi_singkat: 'Studi komparatif tingkat pendapatan dan stabilitas ekonomi antara pekerja gig berbasis platform dan non-platform di Yogyakarta.',
        image: '/images/assets/Gundatala_Riset 3.png'
    },
    {
        id: 4, 
        judul: 'Riset 4',
        deskripsi_singkat: 'Pembangunan sistem informasi berbasis data untuk mendukung pengambilan keputusan strategis dalam pengembangan ekonomi gig.',
        image: '/images/assets/Gundatala_Riset 4.png'
    },
    {
        id: 5, 
        judul: 'Riset 5',
        deskripsi_singkat: 'Pemetaan potensi ekonomi digital di sektor informal melalui pendekatan survei wilayah terintegrasi.',
        image: '/images/assets/Gundatala_Riset 5.png'
    }
];

// Path Aset
const assets = {
    bgSilhouette: '/images/assets/landmark-prambanan-siluet.svg', 
    wayangKiri: '/images/assets/Gundatala_3 1.png',
    wayangKanan: '/images/assets/Gundatala_3 2.png',
    patternKuning: '/images/assets/pattern kuning 1.png',
    ornamenCard: '/images/assets/batik3.png'
};

const colors = {
    primaryOrange: '#EF874B',
    textRed: '#D94313',
    bgCream: '#FFFBDF',
};

// --- LOGIKA SLIDER ---
const scrollContainer = ref(null);

// Geser sesuai lebar kartu (kira-kira 1/3 layar)
const scrollLeft = () => {
    if (scrollContainer.value) {
        const scrollAmount = scrollContainer.value.clientWidth / (window.innerWidth >= 1024 ? 3 : 1);
        scrollContainer.value.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
};

const scrollRight = () => {
    if (scrollContainer.value) {
        const scrollAmount = scrollContainer.value.clientWidth / (window.innerWidth >= 1024 ? 3 : 1);
        scrollContainer.value.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

onMounted(() => {
    AOS.init({
        duration: 800, 
        once: false,   
        mirror: true,  
        easing: 'ease-out-back',
    });
});
</script>

<template>
    <section 
        id="riset" 
        class="w-full pt-10 pb-32 lg:pb-64 px-6 relative overflow-hidden flex flex-col z-20 -mb-2"
        :style="{ backgroundColor: colors.primaryOrange }"
    >

        <div class="absolute inset-0 z-0 pointer-events-none opacity-90" 
            :style="{
                backgroundImage: `url('${assets.patternKuning}')`,
                backgroundRepeat: 'repeat',
                backgroundSize: '300px',
            }"
            data-aos="fade-in"
            data-aos-duration="1500">
        </div>

        <div class="absolute bottom-0 left-0 right-0 z-10 pointer-events-none">
             <img :src="assets.bgSilhouette" alt="Candi Prambanan"
                class="w-full h-auto object-cover object-bottom opacity-100" />
        </div>

        <img :src="assets.wayangKiri" alt="Wayang Kiri" 
            class="hidden lg:block absolute z-50 pointer-events-none"
            style="width: 280px; height: auto; bottom: -20px; left: -40px;" 
            data-aos="fade-right" 
            data-aos-delay="200" />

        <img :src="assets.wayangKanan" alt="Wayang Kanan" 
            class="hidden lg:block absolute z-50 pointer-events-none"
            style="width: 280px; height: auto; bottom: -20px; right: -40px;"
            data-aos="fade-left"
            data-aos-delay="200" />

        <div class="container mx-auto relative z-40">

            <h2 class="text-4xl md:text-5xl font-headline text-center text-white mb-8 drop-shadow-lg"
                data-aos="fade-down">
                Riset PKL 65
            </h2>

            <div class="relative group-slider px-0 md:px-16">
                
                <button 
                    @click="scrollLeft"
                    class="hidden md:flex absolute left-2 lg:left-0 top-1/2 -translate-y-1/2 z-50 w-14 h-14 items-center justify-center rounded-full bg-white shadow-xl hover:bg-orange-50 hover:scale-110 text-orange-600 transition-all duration-300 border-2 border-orange-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <div 
                    ref="scrollContainer"
                    class="flex overflow-x-auto gap-8 lg:gap-12 py-12 px-4 snap-x snap-mandatory scroll-smooth hide-scrollbar"
                >
                    <div v-for="(riset, index) in risetsStatis" :key="riset.id"
                        class="shrink-0 snap-center w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.33%-2rem)] group rounded-[2rem] shadow-xl p-8 text-center flex flex-col relative transition-all duration-300 ease-out hover:-translate-y-3 hover:shadow-2xl overflow-visible border-b-8 border-orange-600/20 cursor-pointer"
                        :style="{ backgroundColor: colors.bgCream }"
                        data-aos="flip-up"
                        :data-aos-delay="index * 300">
                        
                        <img :src="assets.ornamenCard" 
                            class="absolute top-4 left-4 w-16 opacity-30 transition-transform duration-700 group-hover:rotate-45" />
                        <img :src="assets.ornamenCard" 
                            class="absolute top-4 right-4 w-16 opacity-30 transform -scale-x-100 transition-transform duration-700 group-hover:-rotate-45" />

                        <div class="relative z-10 flex justify-center mb-4 mt-2">
                             <img :src="riset.image" :alt="riset.judul" 
                                  class="h-40 w-auto object-contain drop-shadow-md transition-all duration-300 ease-out 
                                         group-hover:-translate-y-3 group-hover:scale-110 group-hover:rotate-3 group-hover:drop-shadow-xl" />
                        </div>

                        <h3 class="text-2xl font-headline font-bold mb-4 relative z-10 uppercase tracking-wide transition-colors duration-300 group-hover:text-orange-600" 
                            :style="{ color: colors.textRed }">
                            {{ riset.judul }}
                        </h3>

                        <p class="text-gray-700 mb-8 text-sm md:text-base grow leading-relaxed relative z-10 font-sans font-medium line-clamp-4">
                            {{ riset.deskripsi_singkat }}
                        </p>

                        <div class="mt-auto relative z-10">
                            <a href="/hasil-riset"
                                class="inline-block font-bold py-3 px-10 rounded-xl border-2 transition-all duration-300 ease-out text-xs uppercase tracking-widest shadow-sm hover:shadow-lg hover:scale-105"
                                :style="{ 
                                    color: colors.textRed, 
                                    borderColor: colors.textRed, 
                                    backgroundColor: colors.bgCream 
                                }"
                                @mouseover="$event.target.style.backgroundColor = colors.textRed; $event.target.style.color = colors.bgCream"
                                @mouseleave="$event.target.style.backgroundColor = colors.bgCream; $event.target.style.color = colors.textRed"
                            >
                                Lihat Hasil
                            </a>
                        </div>
                    </div>
                </div>

                <button 
                    @click="scrollRight"
                    class="hidden md:flex absolute right-4 md:right-24 top-1/2 -translate-y-1/2 z-50 w-14 h-14 items-center justify-center rounded-full bg-white shadow-xl hover:bg-orange-50 hover:scale-110 text-orange-600 transition-all duration-300 border-2 border-orange-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

            </div>
        </div>
    </section>
</template>

<style scoped>
.font-headline {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}
.font-sans {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.group {
    backface-visibility: hidden;
    transform-style: preserve-3d;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>