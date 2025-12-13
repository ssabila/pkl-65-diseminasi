<script setup>
const content = {
    wawancara: {
        title: 'Metode Wawancara',
        desc: 'Pengumpulan data dilakukan melalui wawancara tatap muka (face-to-face) menggunakan kuesioner terstruktur (CAPI) untuk menjamin validitas data langsung dari responden.'
    },
    alur: {
        title: 'Alur Pengumpulan',
        steps: [
            { id: 1, label: 'Persiapan', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
            { id: 2, label: 'Lapangan', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z' },
            { id: 3, label: 'Pengolahan', icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
            { id: 4, label: 'Analisis', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
            { id: 5, label: 'Diseminasi', icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z' }
        ]
    }
};

const assets = {
    wayang: '/images/assets/22.png',
    titleText: '/images/assets/metodologi-data.svg',
    flowerIcon: '/images/assets/bunga1.png',
    batikPattern: '/images/Group 11 (1).png' 
};

const colors = {
    bgCream: '#FFFBDF',
    textDark: '#D94313',
    primaryOrange: '#EF874B',
    white: '#FFFFFF'
};
</script>

<template>
    <section 
        class="relative py-20 lg:py-28 px-6 overflow-hidden"
        :style="{ backgroundColor: colors.bgCream }"
    >
        <div class="container mx-auto relative z-20 max-w-7xl">
            
            <div class="flex flex-col items-center justify-center mb-16 text-center"
                 data-aos="fade-down"
                 data-aos-duration="1000">
                <div class="flex items-end gap-4 mb-2">
                    <img :src="assets.wayang" alt="Wayang Icon" 
                        class="w-16 h-16 md:w-20 md:h-20 drop-shadow-lg animate-bounce-slow" />
                    <h2 class="text-4xl md:text-5xl font-headline font-bold mb-2" :style="{ color: colors.textDark }">
                        Metode Pengumpulan Data
                    </h2>
                </div>
                <div class="h-1 w-32 rounded-full" :style="{ backgroundColor: colors.primaryOrange }"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
                
                <div class="lg:col-span-4 flex"
                     data-aos="fade-right"
                     data-aos-delay="200"
                     data-aos-duration="1000">
                    <div class="w-full bg-white/60 rounded-3xl p-8 border border-orange-100 shadow-lg hover:shadow-xl transition-shadow duration-300 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-orange-100 rounded-full opacity-50 transition-transform group-hover:scale-150 duration-500"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="p-3 rounded-xl bg-orange-100 text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-headline font-bold" :style="{ color: colors.textDark }">
                                    {{ content.wawancara.title }}
                                </h3>
                            </div>
                            
                            <p class="text-base font-sans leading-relaxed text-gray-700 text-justify">
                                {{ content.wawancara.desc }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8 flex flex-col justify-center"
                     data-aos="fade-left"
                     data-aos-delay="400"
                     data-aos-duration="1000">
                    <div class="bg-white/40 rounded-3xl p-8 border border-orange-50 shadow-md">
                        <h3 class="text-2xl font-headline font-bold mb-10 text-center lg:text-left ml-2" :style="{ color: colors.textDark }">
                            {{ content.alur.title }}
                        </h3>

                        <div class="relative px-2">
                            
                            <div class="hidden lg:block absolute top-8 left-10 right-10 h-1 bg-orange-200 z-0"
                                 data-aos="fade-in" data-aos-delay="600"></div>
                            
                            <div class="lg:hidden absolute left-8 top-8 bottom-8 w-1 bg-orange-200 z-0"
                                 data-aos="fade-in" data-aos-delay="600"></div>

                            <div class="flex flex-col lg:flex-row justify-between gap-8 lg:gap-0 relative z-10">
                                
                                <div v-for="(step, index) in content.alur.steps" :key="step.id" 
                                     class="flex lg:flex-col items-center gap-6 lg:gap-4 group cursor-default"
                                     data-aos="zoom-in"
                                     :data-aos-delay="600 + (index * 150)">
                                    
                                    <div class="w-16 h-16 shrink-0 rounded-full flex items-center justify-center shadow-md transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 border-4 border-[#FFFBDF]"
                                         :style="{ backgroundColor: colors.primaryOrange }">
                                        <div class="relative w-full h-full flex items-center justify-center">
                                            <span class="text-2xl font-bold text-white font-headline group-hover:opacity-0 transition-opacity duration-300 absolute">{{ step.id }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="text-left lg:text-center pt-2">
                                        <p class="text-lg font-bold transition-colors duration-300 group-hover:text-orange-600"
                                           :style="{ color: colors.textDark }">
                                            {{ step.label }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="absolute bottom-0 left-0 w-full h-16 lg:h-24 z-10 opacity-30 pointer-events-none"
             :style="{ 
                backgroundImage: `url('${assets.batikPattern}')`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'contain',
                backgroundPosition: 'bottom'
             }">
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

/* Animasi Bounce Halus */
@keyframes bounceSlow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.animate-bounce-slow {
    animation: bounceSlow 3s ease-in-out infinite;
}
</style>