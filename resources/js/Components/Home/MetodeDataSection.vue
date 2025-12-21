<script setup>
import { onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const content = {
    intro: {
        source: 'ILO, 2025',
        text: 'Pengumpulan data menggunakan pendekatan adaptif berdasarkan klasifikasi pekerja gig:'
    },
    modes: [
        {
            id: 'capi',
            title: 'CAPI',
            subtitle: 'Tatap Muka (Location Based)',
            desc: 'Wawancara langsung dibantu gawai untuk pekerja yang terikat lokasi fisik (Ojol, Kurir).',
            icon: 'tablet',
            color: 'text-orange-600',
            bg: 'bg-orange-50',
            border: 'border-orange-200'
        },
        {
            id: 'cawi',
            title: 'CAWI',
            subtitle: 'Mandiri (Online Based)',
            desc: 'Pengisian mandiri via web untuk pekerja remote/freelance yang tidak terikat lokasi.',
            icon: 'globe',
            color: 'text-[#748D63]', // Hijau Sage
            bg: 'bg-[#748D63]/10',
            border: 'border-[#748D63]/30'
        }
    ],
    tech: {
        name: 'FASIH',
        fullName: 'Flexible Authentically Survey in Harmony',
        desc: 'Seluruh moda terintegrasi dalam satu instrumen digital standar BPS (Mobile & Web) untuk menjamin kualitas data real-time.',
    },
    alur: {
        title: 'Timeline Kegiatan',
        steps: [
            { id: '01', label: 'Persiapan', desc: 'Desain instrumen & pelatihan petugas' },
            { id: '02', label: 'Lapangan', desc: 'Pelaksanaan CAPI & CAWI serentak' },
            { id: '03', label: 'Pengolahan', desc: 'Cleaning, Validasi & Tabulasi Data' },
            { id: '04', label: 'Analisis', desc: 'Interpretasi statistik & visualisasi' },
            { id: '05', label: 'Diseminasi', desc: 'Seminar hasil & publikasi laporan' }
        ]
    }
};

const assets = {
    wayang: '/images/assets/22.png',
    gunungan: '/images/assets/gunungan3.svg',
    daun: '/images/assets/ornamen-daun-sudut.svg',
    batik: '/images/assets/pattern-batik.png', 
    abstrak: '/images/assets/lanskap-gunung-abstrak.svg',
    fasihLogo: '/images/assets/fasih.png' // Logo FASIH
};

const colors = {
    bgCream: '#FFFBDF',
    textDark: '#D94313',
    primaryOrange: '#EF874B',
    primaryGreen: '#748D63', 
    white: '#FFFFFF'
};

onMounted(() => {
    AOS.init({ duration: 800, once: false, easing: 'ease-out-cubic' });
});
</script>

<template>
    <section 
        class="relative py-24 lg:py-32 px-6 overflow-hidden font-sans z-10"
        :style="{ backgroundColor: colors.bgCream }"
    >
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-5 mix-blend-multiply"
                 :style="{ backgroundImage: `url('${assets.batik}')`, backgroundSize: '300px' }">
            </div>
            <img :src="assets.gunungan" class="absolute -bottom-10 -right-20 w-[400px] lg:w-[600px] opacity-10 rotate-6" />
        </div>

        <div class="container mx-auto relative z-20 max-w-7xl">
            
            <div class="text-center mb-16 lg:mb-20" data-aos="fade-down">
                <div class="inline-flex flex-col items-center gap-2">
                    <img :src="assets.wayang" class="w-16 h-auto drop-shadow-md mb-2 animate-float" />
                    <span class="text-orange-600 font-bold tracking-[0.2em] text-xs uppercase border-b-2 border-orange-400 pb-1">Metodologi Riset</span>
                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-headline font-bold leading-tight" :style="{ color: colors.textDark }">
                        Metode Pengumpulan Data
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                
                <div class="lg:col-span-7 flex flex-col gap-8">
                    
                    <p class="text-lg text-gray-700 font-medium leading-relaxed border-l-4 border-orange-400 pl-4" data-aos="fade-right">
                        {{ content.intro.text }} <span class="font-bold text-[#D94313]">(ILO, 2025)</span>
                    </p>

                    <div class="grid md:grid-cols-2 gap-6" data-aos="fade-up">
                        <div v-for="(mode, idx) in content.modes" :key="idx" 
                             class="group relative bg-white p-6 lg:p-8 rounded-[2rem] shadow-lg hover:shadow-2xl transition-all duration-300 border-2 transform hover:-translate-y-2 z-10"
                             :class="mode.border">
                            
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110"
                                 :class="mode.bg">
                                <svg v-if="mode.icon === 'tablet'" class="w-7 h-7" :class="mode.color" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                <svg v-else class="w-7 h-7" :class="mode.color" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                            </div>

                            <h3 class="text-3xl font-headline font-bold text-gray-800 mb-1">{{ mode.title }}</h3>
                            <p class="text-xs font-bold uppercase tracking-wider mb-4" :class="mode.color">{{ mode.subtitle }}</p>
                            
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ mode.desc }}
                            </p>

                            <span class="absolute top-4 right-6 text-6xl font-headline font-bold opacity-5 pointer-events-none select-none">
                                0{{ idx + 1 }}
                            </span>
                        </div>
                    </div>

                    <div class="relative rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6 overflow-hidden shadow-md group z-10 border border-green-200"
                         :style="{ backgroundColor: colors.primaryGreen }"
                         data-aos="fade-up" data-aos-delay="100">
                         
                         <div class="absolute inset-0 opacity-10 bg-[url('/images/assets/pattern-batik.png')] bg-cover mix-blend-overlay"></div>
                         
                         <div class="shrink-0 w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-lg transform transition-transform group-hover:scale-105">
                            <img :src="assets.fasihLogo" alt="Logo FASIH" class="w-14 h-auto object-contain" />
                         </div>

                         <div class="flex-1 text-center md:text-left relative z-10 text-white">
                            <div class="flex flex-col md:flex-row md:items-end gap-2 mb-2">
                                <h4 class="text-xl font-bold font-headline leading-none">Didukung oleh FASIH</h4>
                                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-md text-white/90 border border-white/20 uppercase tracking-wider">Instrumen Resmi BPS</span>
                            </div>
                            <p class="text-sm text-green-50 leading-snug">
                                {{ content.tech.desc }}
                            </p>
                         </div>
                    </div>

                </div>

                <div class="lg:col-span-5 relative" data-aos="fade-left" data-aos-delay="200">
                    
                    <div class="bg-white/60 backdrop-blur-md rounded-[2.5rem] p-8 lg:p-10 border border-white shadow-xl relative z-10">
                        <h3 class="text-2xl font-headline font-bold mb-8 flex items-center gap-3" :style="{ color: colors.textDark }">
                            <span class="w-8 h-1 bg-[#EF874B] rounded-full"></span>
                            {{ content.alur.title }}
                        </h3>

                        <div class="relative">
                            <div class="absolute left-[19px] top-4 bottom-4 w-0.5 bg-gradient-to-b from-[#EF874B] via-[#748D63] to-[#EF874B] opacity-40"></div>

                            <div class="space-y-8">
                                <div v-for="(step, index) in content.alur.steps" :key="step.id" 
                                     class="relative flex gap-5 group">
                                    
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-white border-4 border-[#EF874B] group-hover:border-[#748D63] flex items-center justify-center z-10 shadow-sm group-hover:scale-110 transition-all duration-300">
                                        <span class="text-[10px] font-bold text-[#D94313] group-hover:text-[#748D63]">{{ step.id }}</span>
                                    </div>

                                    <div class="pt-1">
                                        <h4 class="text-base font-bold text-gray-800 group-hover:text-[#D94313] transition-colors">
                                            {{ step.label }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mt-0.5">
                                            {{ step.desc }}
                                        </p>
                                    </div>
                                </div>
                            </div>
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

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
.animate-float {
    animation: float 5s ease-in-out infinite;
}
</style>