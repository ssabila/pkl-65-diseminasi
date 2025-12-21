<script setup>
// Data Riset
const risetsStatis = [
    {
        id: 1,
        judul: 'Riset 1',
        deskripsi_singkat: 'Analisis mendalam mengenai pola kerja dan demografi pekerja gig di sektor transportasi online dan dampaknya terhadap kesejahteraan.'
    },
    {
        id: 2,
        judul: 'Riset 2',
        deskripsi_singkat: 'Evaluasi efektivitas program jaminan sosial bagi pekerja lepas dan identifikasi kesenjangan perlindungan yang ada.'
    },
    {
        id: 3, 
        judul: 'Riset 3',
        deskripsi_singkat: 'Studi komparatif tingkat pendapatan dan stabilitas ekonomi antara pekerja gig berbasis platform dan non-platform di Yogyakarta.'
    }
];

// Path Aset
const assets = {
    bgSilhouette: '/images/assets/landmark-prambanan-siluet.png',
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
</script>

<template>
    <section 
        id="riset" 
        class="w-full pt-20 pb-32 lg:pb-64 px-6 relative overflow-hidden flex flex-col z-20 -mb-2"
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

        <div class="absolute bottom-0 left-0 right-0 z-10 flex justify-center pointer-events-none translate-y-10 lg:translate-y-20"
             data-aos="zoom-in-up"
             data-aos-duration="1200"
             data-aos-offset="0">
             <img :src="assets.bgSilhouette" alt="Candi Prambanan"
                class="w-full max-w-[800px] lg:max-w-[1200px] h-auto object-contain opacity-90" />
        </div>

        <img :src="assets.wayangKiri" alt="Wayang Kiri" 
            class="hidden lg:block absolute z-50 pointer-events-none"
            style="width: 300px; height: auto; bottom: -20px; left: -50px;" 
            data-aos="fade-right"
            data-aos-duration="1000"
            data-aos-delay="200" />

        <img :src="assets.wayangKanan" alt="Wayang Kanan" 
            class="hidden lg:block absolute z-50 pointer-events-none"
            style="width: 300px; height: auto; bottom: -20px; right: -50px;"
            data-aos="fade-left"
            data-aos-duration="1000"
            data-aos-delay="200" />

        <div class="container mx-auto relative z-40">

            <h2 class="text-3xl md:text-4xl font-headline text-center text-white mb-12 drop-shadow-md"
                data-aos="fade-down"
                data-aos-duration="800">
                Riset Unggulan PKL 65
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">

                <div v-for="(riset, index) in risetsStatis" :key="riset.id"
                    class="group rounded-2xl shadow-lg p-8 text-center flex flex-col relative transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl overflow-hidden"
                    :style="{ backgroundColor: colors.bgCream }"
                    data-aos="flip-up"
                    :data-aos-delay="index * 150"
                    data-aos-duration="800">
                    
                    <img :src="assets.ornamenCard" 
                        class="absolute top-4 left-4 w-16 md:w-24 opacity-40 transition-transform duration-700 group-hover:rotate-45" />
                    <img :src="assets.ornamenCard" 
                        class="absolute top-4 right-4 w-16 md:w-24 opacity-40 transform -scale-x-100 transition-transform duration-700 group-hover:-rotate-45" />

                    <h3 class="text-2xl font-headline font-bold mt-6 mb-4 relative z-10" 
                        :style="{ color: colors.textRed }">
                        {{ riset.judul }}
                    </h3>

                    <p class="text-gray-700 mb-8 text-sm md:text-base grow leading-relaxed relative z-10">
                        {{ riset.deskripsi_singkat }}
                    </p>

                    <div class="mt-auto relative z-10">
                        <a href="#"
                            class="inline-block font-semibold py-2.5 px-8 rounded-lg border-2 transition-all duration-300 ease-in-out text-sm shadow-sm hover:shadow-md"
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
        </div>
    </section>
</template>

<style scoped>
.font-headline {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
}
</style>