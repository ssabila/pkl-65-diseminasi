<script setup>
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
</script>

<template>
    <section 
        class="relative py-16 md:py-24 px-6 overflow-hidden flex flex-col -mt-2"
        :style="{ backgroundColor: colors.bgCream }" 
    >
        
        <div class="absolute inset-0 w-full h-full z-0 pointer-events-none opacity-20 md:opacity-100"
             data-aos="fade-in"
             data-aos-duration="1500">
             <img 
                :src="assets.background" 
                alt="Background Decor"
                class="w-full h-full object-cover object-bottom translate-y-10 md:translate-y-24"
            />
        </div>

        <div class="absolute top-0 left-0 w-full h-12 md:h-24 z-10 pointer-events-none transform -translate-y-1/2"
             :style="{ 
                backgroundImage: `url(${assets.border})`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'cover',
                backgroundPosition: 'center'
             }">
        </div>

        <div class="container mx-auto relative z-10 max-w-6xl mt-4 mb-12 md:mb-24">
            
            <div class="flex items-center justify-center md:justify-start gap-4 mb-12 md:ml-0"
                 data-aos="fade-right"
                 data-aos-duration="1000">
                <div class="relative group-header">
                     <img :src="assets.gunungan" alt="Icon" 
                     class="h-12 w-auto md:h-16 lg:scale-[1.4] transition-transform duration-500 hover:rotate-6" /> 
                </div>
               
                <h2 class="text-3xl md:text-5xl font-headline font-bold"
                    :style="{ color: colors.textRed }">
                    {{ content.title }}
                </h2>
            </div>

            <div class="space-y-4 md:space-y-6">
                <div v-for="(item, index) in content.list" :key="index" 
                     class="group flex items-start md:items-center gap-4 md:gap-6 p-3 md:p-4 rounded-2xl transition-all duration-300 hover:bg-white/40 hover:shadow-sm hover:translate-x-2 cursor-default"
                     data-aos="fade-up"
                     :data-aos-delay="index * 100"
                     data-aos-anchor-placement="top-bottom">
                    
                    <div class="flex-shrink-0 w-10 h-10 md:w-14 md:h-14 rounded-xl flex items-center justify-center shadow-md transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                         :style="{ backgroundColor: colors.numberBox }">
                        <span class="text-white font-bold text-lg md:text-2xl font-headline">
                            {{ index + 1 }}
                        </span>
                    </div>

                    <p class="text-base md:text-xl font-sans font-medium leading-relaxed transition-colors duration-300"
                       :style="{ color: colors.textRed }">
                        {{ item }}
                    </p>
                </div>
            </div>

        </div>

        <img 
            :src="assets.corner" 
            alt="Corner Left"
            class="hidden md:block absolute bottom-0 left-0 z-20 w-32 md:w-64 pointer-events-none translate-y-1 opacity-80"
            data-aos="fade-right"
            data-aos-delay="500"
        />

        <img 
              :src="assets.corner" 
              alt="Corner Right"
              class="hidden md:block absolute bottom-0 right-0 z-20 w-32 md:w-64 pointer-events-none -scale-x-100 translate-y-1 opacity-80"
              data-aos="fade-left"
              data-aos-delay="500"
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
</style>