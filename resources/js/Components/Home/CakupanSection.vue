<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

// KONTEN CAKUPAN PENELITIAN
const cakupanItems = [
    {
        no: '01',
        title: 'Wilayah Penelitian',
        text: "Mencakup 5 Kabupaten/Kota di Provinsi D.I. Yogyakarta: Sleman (42 Tim), Kota Yogyakarta (42 Tim), Bantul (12 Tim), Kulon Progo (6 Tim), dan Gunung Kidul (6 Tim)."
    },
    {
        no: '02',
        title: 'Subjek Penelitian',
        text: "Berfokus pada Pekerja Gig (Gig Workers), baik yang berbasis lokasi (Location Based) seperti pengemudi transportasi online, maupun berbasis daring (Online Based)."
    },
    {
        no: '03',
        title: 'Tenaga Lapangan',
        text: "Melibatkan total 512 mahasiswa Politeknik Statistika STIS yang terbagi ke dalam 108 tim pencacah lapangan, terdiri dari Koordinator Tim (Kortim) dan Petugas Cacah Lapangan (PCL)."
    }
];

const assets = {
    bungaCakupan: '/images/assets/bunga-cakupan.svg'
};

// PALET WARNA (Dipakai untuk Background Section & Teks Statis)
const colors = {
    bgCream: '#FFFBDF',
    darkOrange: '#D94313',     
    textBrown: '#5D4037',      
    sageGreen: '#748D63'
};

onMounted(() => {
    AOS.init({
        duration: 700, 
        once: false,  
        mirror: true,  
        easing: 'ease-out',
    });
});
</script>

<template>
    <section 
        id="cakupan" 
        class="relative py-20 lg:py-32 px-6 overflow-hidden z-10" 
        :style="{ backgroundColor: colors.bgCream }"
    >
        
        <div 
            class="hidden lg:block absolute top-0 bottom-0 left-0 w-16 z-0 opacity-60 pointer-events-none"
            :style="{ 
                backgroundImage: `url('${assets.bungaCakupan}')`, 
                backgroundRepeat: 'repeat-y',
                backgroundSize: 'contain'
            }"
            data-aos="fade-right"
        ></div>

        <div 
            class="hidden lg:block absolute top-0 bottom-0 right-0 w-16 z-0 transform -scale-x-100 opacity-60 pointer-events-none"
            :style="{ 
                backgroundImage: `url('${assets.bungaCakupan}')`, 
                backgroundRepeat: 'repeat-y',
                backgroundSize: 'contain'
            }"
            data-aos="fade-left"
        ></div>

        <div class="container mx-auto relative z-10 max-w-5xl px-2 md:px-12 lg:px-20">
            
            <h2 
                class="text-4xl md:text-5xl lg:text-6xl font-headline mb-12 md:mb-20 font-bold text-center md:text-left leading-tight"
                :style="{ color: colors.darkOrange }"
                data-aos="fade-down"
            >
                Cakupan Penelitian
            </h2>

            <div class="flex flex-col gap-8 md:gap-12">
                
                <div 
                    v-for="(item, index) in cakupanItems" 
                    :key="index" 
                    class="group flex gap-4 md:gap-10 items-start relative p-4 md:p-6 rounded-3xl transition-all duration-300 hover:bg-white/40 border-2 border-transparent hover:border-orange-200/50"
                    data-aos="fade-right"
                    :data-aos-delay="index * 100"
                >
                    <div 
                        class="text-4xl md:text-6xl lg:text-7xl font-headline font-bold shrink-0 leading-none opacity-40 transition-all duration-500 group-hover:opacity-100 group-hover:scale-110 text-[#EF874B] group-hover:text-[#748D63]"
                    >
                        {{ item.no }}
                    </div>

                    <div class="flex flex-col w-full pt-1 md:pt-4">
                        <h3 class="text-xl md:text-2xl font-bold font-headline mb-2 transition-colors"
                            :style="{ color: colors.darkOrange }">
                            {{ item.title }}
                        </h3>

                        <p 
                            class="text-base md:text-lg font-sans leading-relaxed mb-6 font-medium"
                            :style="{ color: colors.textBrown }"
                        >
                            {{ item.text }}
                        </p>
                        
                        <div class="relative h-[3px] w-full bg-orange-900/10 overflow-hidden rounded-full">
                            <div 
                                class="absolute top-0 left-0 h-full w-full transform -translate-x-full group-hover:translate-x-0 transition-transform duration-700 ease-out-expo"
                                :style="{ backgroundColor: colors.sageGreen }"
                            ></div>
                        </div>
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

.ease-out-expo {
    transition-timing-function: cubic-bezier(0.19, 1, 0.22, 1);
}

.group div {
    backface-visibility: hidden;
}
</style>