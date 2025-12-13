<script setup>
const researchCards = [
    {
        id: '01',
        title: 'Karakteristik Pekerja',
        desc: 'Mayoritas pekerja gig didominasi oleh generasi Z dan Milenial dengan motivasi utama fleksibilitas waktu.',
        link: '#'
    },
    {
        id: '02',
        title: 'Tingkat Pendapatan',
        desc: 'Rata-rata pendapatan bersih pekerja gig sektor transportasi masih berada di bawah UMP D.I. Yogyakarta.',
        link: '#'
    },
    {
        id: '03',
        title: 'Perlindungan Sosial',
        desc: 'Hanya 15% pekerja gig yang memiliki jaminan sosial ketenagakerjaan aktif secara mandiri.',
        link: '#'
    }
];

const conclusionPoints = {
    main: [
        'Sektor Gig Economy menjadi penyangga penting ekonomi DIY.',
        'Fleksibilitas adalah daya tarik utama, namun kerentanan ekonomi tinggi.',
        'Perlu adanya regulasi khusus yang mengatur standar kerja mitra.',
        'Literasi keuangan digital pekerja gig sudah cukup baik.'
    ],
    recommendation: [
        'Pemerintah perlu mendorong skema subsidi jaminan sosial (BPU).',
        'Platform digital wajib transparansi dalam skema insentif.',
        'Pembentukan serikat pekerja gig untuk posisi tawar yang lebih kuat.',
        'Pelatihan vokasi untuk peningkatan skill pekerja.'
    ]
};

const assets = {
    wayang: '/images/assets/gunungan3.png',
    candi: '/images/assets/landmark-candi-tiga.svg'
};

const colors = {
    primaryOrange: '#EF874B',
    bgCream: '#FFFBDF',
    textWhite: '#FFFFFF',
    textOrange: '#D94313'
};
</script>

<template>
    <section 
        class="relative py-20 lg:py-28 px-6 overflow-hidden flex flex-col z-10"
        :style="{ backgroundColor: colors.primaryOrange }"
    >
        
        <div class="absolute -right-20 -bottom-10 z-0 pointer-events-none opacity-20 lg:opacity-30"
             data-aos="fade-left"
             data-aos-duration="1500"
             data-aos-offset="0">
            <img :src="assets.candi" alt="Candi Decor" 
                class="w-[300px] md:w-[500px] lg:w-[700px] h-auto object-contain" />
        </div>

        <div class="container mx-auto relative z-10 max-w-7xl">
            
            <div class="flex flex-col items-center lg:items-start mb-16 text-center lg:text-left"
                 data-aos="fade-down"
                 data-aos-duration="1000">
                
                <div class="flex items-center gap-4 mb-6">
                    <img :src="assets.wayang" alt="Wayang Icon" 
                         class="w-14 h-14 lg:w-20 lg:h-20 drop-shadow-md animate-pulse-slow" />
                    <h2 class="text-4xl md:text-5xl font-headline font-bold text-white drop-shadow-sm">
                        Hasil & Kesimpulan
                    </h2>
                </div>
                
                <p class="text-white/90 text-base md:text-lg max-w-2xl font-sans leading-relaxed"
                   data-aos="fade-up" data-aos-delay="200">
                    Berikut adalah ringkasan temuan strategis dari analisis data survei yang telah dilakukan terhadap pekerja Gig Economy di D.I. Yogyakarta.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
                <div v-for="(card, index) in researchCards" :key="card.id" 
                     class="group relative bg-white/10 backdrop-blur-sm border-l-4 border-[#FFFBDF] p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2 rounded-r-xl"
                     data-aos="fade-up"
                     :data-aos-delay="index * 200"
                     data-aos-duration="1000">
                    
                    <div class="text-5xl font-headline font-bold text-white/30 mb-4 group-hover:text-white/50 transition-colors">
                        {{ card.id }}
                    </div>
                    
                    <h3 class="text-2xl font-headline font-bold text-white mb-4 h-16 flex items-center">
                        {{ card.title }}
                    </h3>
                    
                    <p class="text-white/90 font-sans text-sm leading-relaxed mb-8 min-h-[80px]">
                        {{ card.desc }}
                    </p>

                    <button class="flex items-center gap-2 text-[#FFFBDF] font-bold text-sm uppercase tracking-wider group-hover:gap-4 transition-all duration-300">
                        Lihat Detail
                        <span class="text-lg">→</span>
                    </button>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 md:p-12 border border-white/20 shadow-xl relative overflow-hidden"
                 data-aos="zoom-in"
                 data-aos-duration="1000"
                 data-aos-delay="200">
                
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                <h2 class="text-3xl font-headline font-bold text-white text-center mb-12 relative z-10"
                    data-aos="fade-down" data-aos-delay="400">
                    Kesimpulan Umum
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 relative z-10">
                    
                    <div data-aos="fade-right" data-aos-delay="600">
                        <div class="flex items-center gap-3 mb-6 border-b border-white/20 pb-4">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white">✓</span>
                            <h3 class="text-xl font-bold text-white font-headline">Temuan Kunci</h3>
                        </div>
                        <ul class="space-y-4">
                            <li v-for="(point, idx) in conclusionPoints.main" :key="idx" 
                                class="flex gap-4 text-white/90 font-sans text-sm md:text-base items-start">
                                <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#FFFBDF] shrink-0"></div>
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="800">
                        <div class="flex items-center gap-3 mb-6 border-b border-white/20 pb-4">
                            <span class="w-8 h-8 rounded-full bg-[#FFFBDF]/20 flex items-center justify-center text-[#FFFBDF]">✦</span>
                            <h3 class="text-xl font-bold text-[#FFFBDF] font-headline">Rekomendasi Kebijakan</h3>
                        </div>
                        <ul class="space-y-4">
                            <li v-for="(point, idx) in conclusionPoints.recommendation" :key="idx" 
                                class="flex gap-4 text-white/90 font-sans text-sm md:text-base items-start">
                                <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#FFFBDF] shrink-0"></div>
                                <span>{{ point }}</span>
                            </li>
                        </ul>
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

/* Animasi Denyut Halus untuk Wayang */
@keyframes pulseSlow {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.9; }
}
.animate-pulse-slow {
    animation: pulseSlow 3s ease-in-out infinite;
}
</style>