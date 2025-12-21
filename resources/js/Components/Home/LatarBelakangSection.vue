<script setup>
const content = {
    title: 'Latar Belakang',
    mainDesc: 'Praktik Kerja Lapangan (PKL) adalah bentuk penyelenggaraan kegiatan pendidikan dan pengajaran yang memadukan secara sistematis dan sinkron antara program pendidikan di kampus dengan program penguasaan keahlian yang diperoleh melalui kegiatan bekerja secara langsung di dunia kerja yang sesungguhnya.',
    cardTitle: 'Fenomena Gig Economy',
    points: [
        'Fleksibilitas waktu kerja yang tinggi',
        'Ketergantungan pada platform digital',
        'Ketidakpastian pendapatan dan jaminan sosial',
        'Perubahan pola hubungan kerja industrial'
    ]
};

// Assets
const assets = {
    gunungan3: '/images/assets/gunungan3.svg', 
    border: '/images/assets/border23.svg' 
};

// Warna
const colors = {
    primaryOrange: '#EF874B',   
    textWhite: '#FFFBDF',
    textOrange: '#D94313', 
    textRed: '#7A2509',     
    bgCream: '#FFFBDF',        
};
</script>

<template>
    <section 
        class="relative py-16 md:py-24 px-6 overflow-hidden flex flex-col justify-center -mt-2 z-20"
        :style="{ backgroundColor: colors.primaryOrange }" 
    >
        
        <div class="container mx-auto relative z-10 max-w-6xl">
            
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-4 mb-8 md:mb-10"
                 data-aos="fade-right"
                 data-aos-duration="1000">
                <div class="relative group">
                    <img :src="assets.gunungan3" alt="Icon" 
                        class="h-16 w-auto md:h-20 lg:scale-[1.5] transition-transform duration-500 group-hover:rotate-12" /> 
                </div>
                <h2 class="text-3xl md:text-5xl font-headline font-bold text-center md:text-left"
                    :style="{ color: colors.textWhite }">
                    {{ content.title }}
                </h2>
            </div>

            <p class="text-base font-sans md:text-lg leading-relaxed text-justify md:text-left mb-12 opacity-95 max-w-4xl"
               :style="{ color: colors.textWhite }"
               data-aos="fade-up"
               data-aos-delay="200"
               data-aos-duration="1000">
                {{ content.mainDesc }}
            </p>

            <div class="font-sans rounded-3xl p-6 md:p-12 shadow-lg transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                 :style="{ backgroundColor: colors.bgCream }"
                 data-aos="zoom-in-up"
                 data-aos-delay="400"
                 data-aos-duration="1000">
                
                <h3 class="text-2xl md:text-3xl font-headline font-bold mb-8 text-center md:text-left"
                    :style="{ color: colors.textOrange }">
                    {{ content.cardTitle }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div v-for="(point, index) in content.points" :key="index" 
                        class="group flex items-start gap-4 p-2 rounded-lg transition-colors duration-200 hover:bg-orange-50/50"
                        data-aos="fade-up"
                        :data-aos-delay="600 + (index * 100)">
                        
                        <div class="mt-1 flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11" stroke="#D94313" stroke-width="2" class="opacity-80"/>
                                <path d="M7 12L10.5 15.5L17 9" stroke="#D94313" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <p class="font-medium text-base md:text-lg leading-snug transition-transform duration-300 group-hover:translate-x-1"
                          :style="{ color: colors.textRed}" >
                            {{ point }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="absolute bottom-0 left-0 w-full h-10 md:h-24 lg:h-32 z-0 opacity-80 pointer-events-none"
             :style="{ 
                backgroundImage: `url(${assets.border})`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'cover',
                backgroundPosition: 'top center'
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
</style>