<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const cakupanItems = [
    {
        no: '01',
        text: "Primary sampling unit mencakup seluruh daftar kabupaten/kota terpilih beserta estimasi jumlah Gig Worker di Provinsi D.I. Yogyakarta untuk Sensus Ekonomi 2026."
    },
    {
        no: '02',
        text: "Secondary sampling unit meliputi blok sensus terpilih yang merepresentasikan karakteristik wilayah perkotaan dan perdesaan secara proporsional."
    },
    {
        no: '03',
        text: "Unit observasi mencakup seluruh pekerja gig yang teridentifikasi dalam kerangka sampel terpilih, baik berbasis platform maupun non-platform."
    },
    {
        no: '04',
        text: "Lingkup variabel penelitian meliputi karakteristik demografi, pola kerja, pendapatan, pengeluaran, perlindungan sosial, dan persepsi kelayakan kerja."
    }
];

const assets = {
    bungaCakupan: '/images/assets/bunga-cakupan.svg'
};

const colors = {
    bgCream: '#FFFBDF',
    primaryOrange: '#EF874B',
    textDark: '#D94313'
};

onMounted(() => {
    AOS.init({
        duration: 700, // Kecepatan transisi snappy
        once: false,   // Berulang saat scroll bawah
        mirror: true,  // Berulang saat scroll atas
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
            class="hidden lg:block absolute top-0 bottom-0 left-0 w-16 z-0 opacity-40 pointer-events-none"
            :style="{ 
                backgroundImage: `url('${assets.bungaCakupan}')`, 
                backgroundRepeat: 'repeat-y',
                backgroundSize: 'contain'
            }"
            data-aos="fade-right"
        ></div>

        <div 
            class="hidden lg:block absolute top-0 bottom-0 right-0 w-16 z-0 transform -scale-x-100 opacity-40 pointer-events-none"
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
                :style="{ color: colors.primaryOrange }"
                data-aos="fade-down"
            >
                Cakupan Penelitian
            </h2>

            <div class="flex flex-col gap-8 md:gap-12">
                
                <div 
                    v-for="(item, index) in cakupanItems" 
                    :key="index" 
                    class="group flex gap-4 md:gap-10 items-start relative p-3 md:p-0 rounded-2xl transition-all duration-300 hover:bg-orange-100/30 md:hover:bg-transparent"
                    data-aos="fade-right"
                    :data-aos-delay="index * 100"
                >
                    <div 
                        class="text-4xl md:text-6xl lg:text-7xl font-headline font-bold shrink-0 leading-none opacity-30 transition-all duration-500 group-hover:opacity-100 group-hover:scale-110 group-hover:text-orange-600"
                        :style="{ color: colors.primaryOrange }"
                    >
                        {{ item.no }}
                    </div>

                    <div class="flex flex-col w-full pt-1 md:pt-4">
                        <p 
                            class="text-base md:text-xl lg:text-2xl font-sans font-bold leading-relaxed mb-6 transition-colors duration-300 group-hover:text-[#D94313]"
                            :style="{ color: colors.primaryOrange }" 
                        >
                            {{ item.text }}
                        </p>
                        
                        <div class="relative h-[2px] w-full bg-orange-200/40 overflow-hidden rounded-full">
                            <div 
                                class="absolute top-0 left-0 h-full w-full transform -translate-x-full group-hover:translate-x-0 transition-transform duration-700 ease-out-expo"
                                :style="{ backgroundColor: colors.primaryOrange }"
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

/* Custom easing untuk divider agar lebih elegan */
.ease-out-expo {
    transition-timing-function: cubic-bezier(0.19, 1, 0.22, 1);
}

/* Pastikan transisi scale lancar */
.group div {
    backface-visibility: hidden;
}
</style>