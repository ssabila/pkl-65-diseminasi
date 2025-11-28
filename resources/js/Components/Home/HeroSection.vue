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
        class="min-h-screen relative flex flex-col justify-between overflow-hidden -mt-30 pt-32"
        :style="{ backgroundColor: colors.bgCream }" 
    >
        
        <div class="absolute inset-0 opacity-10 pointer-events-none z-0"
            :style="{ 
                backgroundImage: `url(${assets.pattern})`, 
                backgroundSize: 'cover',
                backgroundRepeat: 'repeat',
                backgroundPosition: '-250px'
            }">
        </div>

        <div class="container mx-auto px-6 relative z-10 flex-grow flex items-center">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
                
                <div class="lg:col-span-7 space-y-4 text-center lg:text-left order-2 lg:order-1 lg:pl-12 xl:pl-12 -mt-30">
                    <h1 class="text-6xl md:text-[84px] font-headline leading-none" 
                        :style="{ color: colors.text }">
                        Praktik Kerja Lapangan
                    </h1>

                    
                    <h2 class="text-xl md:text-2xl font-sans font-bold"
                        :style="{ color: colors.text }">
                        {{ content.subtitle }}
                    </h2>

                    <p class="md:text-[16px] font-sans font-medium leading-relaxed text-justify md:text-left mx-auto lg:mx-0"
                        :style="{ color: colors.text }">
                        {{ content.desc }}
                    </p>

                    <div class="pt-0 flex justify-center lg:justify-start">
                        <button class="flex items-center gap-3 px-3 py-2 rounded-lg text-white font-semibold shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl"
                                :style="{ backgroundColor: colors.btnGreen }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ content.buttonText }}
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-5 relative flex justify-center lg:justify-end order-1 lg:order-2 z-10">
                        <img :src="assets.gunungan" alt="Gunungan" 
                            class="w-3/4 md:w-full max-w-md lg:max-w-lg drop-shadow-xl transition-transform duration-300 transform scale-[1.3] lg:scale-[1.2] -translate-y-7 lg:translate-x-25" />
                </div>
            </div>
        </div>

        <img 
            :src="assets.corner" 
            v-if="assets.corner"
            alt="Corner Decor"
            class="absolute bottom-0 right-0 z-20 w-auto h-auto max-h-[500px] pointer-events-none translate-x-31 translate-y-7"
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
</style>
