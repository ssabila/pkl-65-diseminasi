<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

// Data Tahapan Sampling (Disesuaikan dengan standar survei BPS/STIS)
const steps = [
    {
        title: "Populasi Target",
        desc: "Seluruh pekerja sektor Gig Economy di wilayah Provinsi D.I. Yogyakarta yang menjadi cakupan penelitian.",
        icon: "population"
    },
    {
        title: "Kerangka Sampel",
        desc: "Daftar blok sensus dari Sensus Penduduk terbaru dan hasil pemutakhiran muatan wilayah (listing).",
        icon: "frame"
    },
    {
        title: "Metode Sampling",
        desc: "Menggunakan Stratified Two-Stage Sampling untuk memastikan keterwakilan wilayah perkotaan dan perdesaan.",
        icon: "method"
    },
    {
        title: "Unit Sampel",
        desc: "Terpilihnya Blok Sensus sebagai Primary Sampling Unit (PSU) dan Rumah Tangga sebagai Secondary Sampling Unit (SSU).",
        icon: "unit"
    }
];

const assets = {
    bgPattern: '/images/assets/pattern-batik.svg' 
};

const colors = {
    primaryOrange: '#EF874B',
    secondaryCream: '#FFFBDF',
    textDark: '#D94313'
};

onMounted(() => {
    AOS.init({
        duration: 700, // Kecepatan snappy
        once: false,   // Berulang saat scroll bawah
        mirror: true,  // Berulang saat scroll atas
        easing: 'ease-out',
    });
});
</script>

<template>
    <section 
        class="relative py-20 lg:py-28 px-6 overflow-hidden z-10"
        :style="{ backgroundColor: colors.primaryOrange }"
    >
        <div class="absolute inset-0 opacity-10 pointer-events-none"
            :style="{ 
                backgroundImage: `url(${assets.bgPattern})`, 
                backgroundRepeat: 'repeat',
                backgroundSize: '200px'
            }"
            data-aos="fade-in"
            data-aos-duration="1200">
        </div>

        <div class="container mx-auto relative z-10 max-w-7xl">
            
            <div class="text-center mb-16 lg:mb-24">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-white mb-6 drop-shadow-md"
                    data-aos="fade-down">
                    Metodologi Sampel
                </h2>
                <p class="text-white/90 text-base md:text-xl max-w-2xl mx-auto font-sans font-medium leading-relaxed"
                   data-aos="fade-up"
                   data-aos-delay="100">
                    Pendekatan statistik yang digunakan untuk menjamin akurasi dan keterwakilan data Gig Worker secara ilmiah.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-white/30 -translate-y-16 z-0 transform scale-x-90"
                     data-aos="zoom-in"
                     data-aos-delay="400"></div>

                <div v-for="(step, index) in steps" :key="index" 
                    class="group relative z-10 flex flex-col items-center"
                    data-aos="fade-up"
                    :data-aos-delay="index * 100">
                    
                    <div 
                        class="w-full bg-[#FFFBDF] p-8 rounded-[2rem] shadow-xl transition-all duration-500 transform group-hover:-translate-y-4 group-hover:shadow-2xl border-b-8 border-transparent group-hover:border-[#D94313] h-full flex flex-col items-center text-center"
                    >
                        <div class="w-20 h-20 mb-8 rounded-2xl bg-[#EF874B] flex items-center justify-center text-white shadow-lg transition-all duration-500 group-hover:rotate-12 group-hover:scale-110 group-hover:rounded-full">
                            <svg v-if="step.icon === 'population'" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <svg v-if="step.icon === 'frame'" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <svg v-if="step.icon === 'method'" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <svg v-if="step.icon === 'unit'" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-headline font-bold mb-4 tracking-wide" :style="{ color: colors.textDark }">
                            {{ step.title }}
                        </h3>
                        
                        <p class="text-base font-sans font-medium leading-relaxed opacity-90" :style="{ color: colors.textDark }">
                            {{ step.desc }}
                        </p>
                    </div>

                    <div v-if="index < steps.length - 1" class="lg:hidden mt-6 mb-2 text-white/50 animate-bounce">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7-7-7" />
                        </svg>
                    </div>

                </div>
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

/* Transisi lembut untuk group hover */
.group {
    backface-visibility: hidden;
}
</style>