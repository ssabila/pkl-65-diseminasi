<script setup>
const content = {
    title: 'Praktik Kerja Lapangan',
    subtitle: 'Politeknik Statistika STIS T.A. 2025/2026',
    desc: 'Praktik Kerja Lapangan Mahasiswa Tingkat III Politeknik Statistika STIS Tahun Ajaran 2025/2026 diadakan di seluruh kabupaten/kota terpilih di Provinsi D.I. Yogyakarta mengangkat tema Riset Gig Worker dengan slogan "Sinergi Data, Pekerja Gig Berdaya, Yogyakarta Istimewa".',
    buttonText: 'Lihat Hasil Riset'
};

const assets = {
    logo: '/images/assets/LOGO-PKL_REV8.png',
    gunungan: '/images/assets/gunungan2.png', 
    pattern: '/images/assets/pattern-batik.svg', 
    border: '/images/assets/border-hero.svg', 
    corner: '/images/assets/lanskap-gunung-abstrak.svg'
};

const colors = {
    primaryOrange: '#EF874B',   
    text: '#D94313',            
    bgCream: '#FFFBDF',        
    btnGreen: '#748D63'         
};
</script>

<template>
    <section 
        class="min-h-screen relative flex flex-col justify-between overflow-hidden -mt-25 pt-32"
        :style="{ backgroundColor: colors.bgCream }" 
    >
        
        <div class="absolute inset-0 opacity-10 pointer-events-none z-0 animate-pan-pattern"
            :style="{ 
                backgroundImage: `url(${assets.pattern})`, 
                backgroundSize: '400px', // Sedikit diperkecil biar gerakannya kerasa
                backgroundRepeat: 'repeat',
            }">
        </div>

        <div class="container mx-auto px-6 relative z-10 flex-grow flex items-center">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
                
                <div class="lg:col-span-7 space-y-4 text-center lg:text-left order-2 lg:order-1 lg:pl-12 xl:pl-12 -mt-30">
                    
                    <h1 class="text-6xl md:text-[84px] font-headline leading-none" 
                        :style="{ color: colors.text }"
                        data-aos="fade-down"
                        data-aos-duration="1000">
                        Praktik Kerja Lapangan
                    </h1>

                    <h2 class="text-xl md:text-2xl font-sans font-bold"
                        :style="{ color: colors.text }"
                        data-aos="fade-up"
                        data-aos-delay="100">
                        {{ content.subtitle }}
                    </h2>

                    <p class="md:text-[16px] font-sans font-medium leading-relaxed text-justify md:text-left mx-auto lg:mx-0"
                        :style="{ color: colors.text }"
                        data-aos="fade-up"
                        data-aos-delay="200">
                        {{ content.desc }}
                    </p>

                    <div class="pt-0 flex justify-center lg:justify-start"
                         data-aos="fade-up"
                         data-aos-delay="300">
                        <button class="group relative flex items-center gap-3 px-3 py-2 rounded-lg text-white font-semibold shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl overflow-hidden"
                                :style="{ backgroundColor: colors.btnGreen }">
                            
                            <div class="absolute inset-0 w-full h-full bg-white/20 transform -translate-x-full skew-x-12 transition-transform duration-700 group-hover:translate-x-full"></div>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="relative z-10">{{ content.buttonText }}</span>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-5 relative flex justify-center lg:justify-end order-1 lg:order-2 z-10">
                    <div data-aos="zoom-in-left" data-aos-duration="1200">
                        <img :src="assets.gunungan" alt="Gunungan" 
                            class="w-3/4 md:w-full max-w-md lg:max-w-lg drop-shadow-xl transition-transform duration-300 transform scale-[1.3] lg:scale-[1.2] -translate-y-7 lg:translate-x-25 animate-float" />
                    </div>
                </div>
            </div>
        </div>

        <img 
            :src="assets.corner" 
            v-if="assets.corner"
            alt="Corner Decor"
            class="absolute bottom-0 right-0 z-20 w-auto h-auto max-h-[500px] pointer-events-none translate-x-31 translate-y-7"
            data-aos="fade-left"
            data-aos-delay="500"
            data-aos-duration="1500"
        />
        
        <img 
            :src="assets.border" 
            alt="Border Bawah"
            class="absolute bottom-0 left-0 z-30 w-full origin-bottom-left pointer-events-none translate-y-3"
            :style="{ 
                transform: 'scale(1.1)' 
            }" 
        />

    </section>
</template>

<style scoped>
.font-serif {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}

/* 1. Animasi Floating (Melayang Naik Turun) */
@keyframes float {
    0%, 100% {
        transform: translateY(0) scale(1.3); /* Scale disesuaikan dengan class Tailwind kamu */
    }
    50% {
        transform: translateY(-20px) scale(1.3); /* Naik 20px */
    }
}

/* Override class tailwind khusus untuk animasi ini agar scale tetap terjaga */
.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Jika di layar besar scale beda (lg:scale-[1.2]), kita buat media query khusus */
@media (min-width: 1024px) {
    @keyframes floatDesktop {
        0%, 100% { transform: translateY(-28px) translateX(100px) scale(1.2); } /* Sesuaikan translate awalmu */
        50% { transform: translateY(-48px) translateX(100px) scale(1.2); }
    }
    .animate-float {
        animation: floatDesktop 6s ease-in-out infinite;
    }
}

/* 2. Animasi Background Pattern (Geser Miring) */
@keyframes panPattern {
    0% { background-position: 0% 0%; }
    100% { background-position: 100% 100%; }
}

.animate-pan-pattern {
    animation: panPattern 60s linear infinite;
}
</style>