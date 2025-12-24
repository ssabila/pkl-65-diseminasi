<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const content = {
    title: 'Tujuan Penelitian',
    groups: [
        {
            name: 'Riset 1: Ketahanan Ekonomi',
            theme: 'orange', // Penanda tema warna
            items: [
                'Mengidentifikasi ketahanan ekonomi keluarga pekerja gig.',
                'Mengkaji pekerja gig menurut status ketahanan ekonomi keluarga dan jenis pekerjaan gig.',
                'Mengkaji Pengaruh Take Home Income dan tingkat pendidikan pekerja gig terhadap ketahanan ekonomi keluarga pekerja gig.'
            ]
        },
        {
            name: 'Riset 2: Sosial-Demografi & Retensi',
            theme: 'green',
            items: [
                'Mengeksplorasi karakteristik sosial-demografi pekerja gig di DI Yogyakarta.',
                'Mengidentifikasi faktor-faktor yang memengaruhi retensi pada pekerja gig di DI Yogyakarta.'
            ]
        },
        // Contoh Riset 5 (Nanti tinggal uncomment/isi jika data sudah ada)
        /*
        {
            name: 'Riset 5: Big Data & Machine Learning',
            theme: 'purple',
            items: [
                'Membangun model prediksi...',
                'Analisis sentimen...'
            ]
        }
        */
    ]
};

// Assets
const assets = {
    gunungan: '/images/assets/gunungan2.svg', 
    background: '/images/assets/lanskap-monjali.svg',
    corner: '/images/assets/corner-tujuan.svg',
    border: '/images/assets/border23.svg' ,
};

// Palet Warna Dinamis
const themeColors = {
    orange: {
        main: '#D94313',     // Dark Orange (Text Judul)
        accent: '#EF874B',   // Primary Orange (Bg Number)
        bg: 'bg-orange-50',  // Background Card Halus
        border: 'border-orange-200'
    },
    green: {
        main: '#556B2F',     // Dark Olive/Sage (Text Judul)
        accent: '#748D63',   // Sage Green (Bg Number)
        bg: 'bg-green-50',
        border: 'border-green-200'
    },
    purple: {
        main: '#512DA8',     // Deep Purple
        accent: '#6D5B97',   // Lighter Purple
        bg: 'bg-purple-50',
        border: 'border-purple-200'
    }
};

// Warna Global
const globalColors = {
    bgCream: '#FFFBDF',
    textBody: '#4A4A4A' // Abu tua warm (Lebih enak dibaca daripada merah bata)
};

onMounted(() => {
    AOS.init({ duration: 700, once: false, mirror: true, easing: 'ease-out' });
});
</script>

<template>
    <section 
        class="relative py-16 md:py-24 px-6 overflow-hidden flex flex-col -mt-2"
        :style="{ backgroundColor: globalColors.bgCream }" 
    >
        <div class="absolute inset-0 w-full h-full z-0 pointer-events-none opacity-20 md:opacity-40"
             data-aos="fade-in">
             <img :src="assets.background" class="w-full h-full object-cover object-bottom translate-y-10 md:translate-y-20" />
        </div>

        <div class="absolute top-0 left-0 w-full h-12 md:h-20 z-10 pointer-events-none transform -translate-y-1/2 opacity-80"
             :style="{ backgroundImage: `url(${assets.border})`, backgroundSize: 'cover', backgroundPosition: 'center' }">
        </div>

        <div class="container mx-auto relative z-10 max-w-5xl mt-4 mb-12 md:mb-20">
            
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-4 md:gap-6 mb-12"
                 data-aos="fade-down">
                <div class="relative shrink-0">
                     <img :src="assets.gunungan" class="w-16 h-auto md:w-20 lg:scale-[1.2] hover:rotate-6 transition-transform duration-500 drop-shadow-md" /> 
                </div>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-center md:text-left leading-tight text-[#D94313]">
                    {{ content.title }}
                </h2>
            </div>

            <div class="space-y-12">
                <div v-for="(group, gIndex) in content.groups" :key="gIndex">
                    
                    <div class="flex items-center gap-3 mb-6" data-aos="fade-right">
                        <div class="h-8 w-1 rounded-full" 
                             :style="{ backgroundColor: themeColors[group.theme].main }"></div>
                        <h3 class="text-xl md:text-2xl font-headline font-bold"
                            :style="{ color: themeColors[group.theme].main }">
                            {{ group.name }}
                        </h3>
                    </div>

                    <div class="grid gap-4">
                        <div v-for="(item, index) in group.items" :key="index" 
                             class="group flex items-start gap-4 md:gap-6 p-4 rounded-2xl transition-all duration-300 hover:shadow-md hover:translate-x-1 border border-transparent"
                             :class="[`hover:${themeColors[group.theme].bg}`, `hover:${themeColors[group.theme].border}`]"
                             data-aos="fade-up"
                             :data-aos-delay="index * 100">
                            
                            <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center shadow-sm transform transition-transform duration-300 group-hover:scale-110"
                                 :style="{ backgroundColor: themeColors[group.theme].accent }">
                                <span class="text-white font-bold text-lg md:text-xl font-headline">
                                    {{ index + 1 }}
                                </span>
                            </div>

                            <p class="text-base md:text-lg lg:text-xl font-sans font-medium leading-relaxed transition-colors duration-300 text-justify md:text-left"
                               :style="{ color: globalColors.textBody }">
                                {{ item }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <img :src="assets.corner" class="hidden lg:block absolute bottom-0 left-0 z-20 w-48 pointer-events-none translate-y-2 opacity-60" data-aos="fade-right" />
        <img :src="assets.corner" class="hidden lg:block absolute bottom-0 right-0 z-20 w-48 pointer-events-none -scale-x-100 translate-y-2 opacity-60" data-aos="fade-left" />
    </section>
</template>

<style scoped>
.font-headline {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}
.font-sans {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>