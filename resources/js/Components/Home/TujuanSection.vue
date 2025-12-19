<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const content = {
    title: 'Tujuan Penelitian',
    list: [
        'Mengidentifikasi karakteristik demografi dan sosial ekonomi pekerja gig di D.I. Yogyakarta.',
        'Menganalisis tingkat kesejahteraan pekerja gig dilihat dari aspek pendapatan dan pengeluaran.',
        'Mengukur tingkat kerentanan pekerja gig terhadap guncangan ekonomi dan sosial.',
        'Mengevaluasi efektivitas program perlindungan sosial bagi pekerja gig.',
        'Menganalisis pola hubungan kerja dan kondisi kerja di sektor gig economy.',
        'Menyusun rekomendasi kebijakan untuk peningkatan kesejahteraan pekerja gig.',
        'Menyediakan basis data pekerja gig yang akurat dan terkini untuk pemerintah daerah.'
    ]
};

// Assets
const assets = {
    gunungan: '/images/assets/gunungan2.svg', 
    background: '/images/assets/lanskap-monjali.svg',
    corner: '/images/assets/corner-tujuan.svg',
    border: '/images/assets/border23.svg' ,
};

// Warna
const colors = {
    primaryOrange: '#EF874B',   
    textRed: '#D94313',         
    bgCream: '#FFFBDF',
    numberBox: '#E87A3E'      
};

onMounted(() => {
    AOS.init({
        duration: 700, // Kecepatan transisi sedang (snappy)
        once: false,   // Animasi berulang saat scroll bawah
        mirror: true,  // Animasi berulang saat scroll atas
        easing: 'ease-out',
    });
});
</script>

<template>
    <section 
        class="relative py-16 md:py-24 px-6 overflow-hidden flex flex-col -mt-2"
        :style="{ backgroundColor: colors.bgCream }" 
    >
        
        <div class="absolute inset-0 w-full h-full z-0 pointer-events-none opacity-20 md:opacity-40"
             data-aos="fade-in"
             data-aos-duration="1000">
             <img 
                :src="assets.background" 
                alt="Background Decor"
                class="w-full h-full object-cover object-bottom translate-y-10 md:translate-y-20"
            />
        </div>

        <div class="absolute top-0 left-0 w-full h-12 md:h-20 z-10 pointer-events-none transform -translate-y-1/2 opacity-80"
             :style="{ 
                backgroundImage: `url(${assets.border})`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'cover',
                backgroundPosition: 'center'
             }">
        </div>

        <div class="container mx-auto relative z-10 max-w-5xl mt-4 mb-12 md:mb-20">
            
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-4 md:gap-6 mb-12"
                 data-aos="fade-down">
                
                <div class="relative shrink-0">
                     <img :src="assets.gunungan" alt="Icon" 
                     class="w-16 h-auto md:w-20 lg:scale-[1.2] transition-transform duration-500 hover:rotate-6 drop-shadow-md" /> 
                </div>
               
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-center md:text-left leading-tight"
                    :style="{ color: colors.textRed }">
                    {{ content.title }}
                </h2>
            </div>

            <div class="space-y-4 md:space-y-5">
                <div v-for="(item, index) in content.list" :key="index" 
                     class="group flex items-start md:items-center gap-4 md:gap-6 p-3 md:p-4 rounded-2xl transition-all duration-300 hover:bg-white/60 hover:shadow-md hover:translate-x-2 cursor-default"
                     data-aos="fade-right"
                     :data-aos-delay="index * 50">
                    
                    <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center shadow-sm transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                         :style="{ backgroundColor: colors.numberBox }">
                        <span class="text-white font-bold text-lg md:text-xl font-headline">
                            {{ index + 1 }}
                        </span>
                    </div>

                    <p class="text-base md:text-lg lg:text-xl font-sans font-bold leading-tight transition-colors duration-300"
                       :style="{ color: colors.textRed }">
                        {{ item }}
                    </p>
                </div>
            </div>

        </div>

        <img 
            :src="assets.corner" 
            alt="Corner Left"
            class="hidden lg:block absolute bottom-0 left-0 z-20 w-48 pointer-events-none translate-y-2 opacity-60"
            data-aos="fade-right"
        />

        <img 
              :src="assets.corner" 
              alt="Corner Right"
              class="hidden lg:block absolute bottom-0 right-0 z-20 w-48 pointer-events-none -scale-x-100 translate-y-2 opacity-60"
              data-aos="fade-left"
          />
    </section>
</template>

<style scoped>
.font-headline {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}
.font-sans {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Transisi halus untuk hover background */
.group {
    backface-visibility: hidden;
}
</style>