<script setup>
// Data Konten (Tetap sama)
const content = {
    title: 'Tujuan Penelitian',
    list: [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.'
    ]
};

// Assets (Diperbarui sesuai request)
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
        class="relative py-4 px-6 overflow-hidden flex flex-col -mt-6"
        :style="{ backgroundColor: colors.bgCream }" 
    >
        
        <img 
            :src="assets.background" 
            alt="Background Decor"
            class="absolute inset-0 w-full h-full object-cover object-bottom pointer-events-none z-0 translate-y-24"
        />

        <div class="absolute top-0 left-0 w-full h-8 md:h-60 z-0 opacity-80 -translate-y-30"
             :style="{ 
                backgroundImage: `url(${assets.border})`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'contain',
                backgroundPosition: 'top'
             }">
        </div>

        <div class="container mx-auto relative z-10 max-w-6xl mt-8 mb-24">
            
            <div class="flex items-center gap-4 mb-10 translate-x-0 lg:-translate-x-11">
                <img :src="assets.gunungan" alt="Icon" 
                     class="h-12 w-auto lg:scale-[1.6]" /> 
                
                <h2 class="text-4xl md:text-5xl font-headline"
                    :style="{ color: colors.textRed }">
                    {{ content.title }}
                </h2>
            </div>

            <div class="space-y-6">
                <div v-for="(item, index) in content.list" :key="index" 
                     class="flex items-start md:items-center gap-4 md:gap-6 group">
                    
                    <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center shadow-sm transform transition group-hover:scale-110"
                         :style="{ backgroundColor: colors.numberBox }">
                        <span class="text-white font-bold text-lg md:text-xl font-headline">
                            {{ index + 1 }}.
                        </span>
                    </div>

                    <p class="text-base md:text-xl font-sans font-medium"
                       :style="{ color: colors.textRed }">
                        {{ item }}
                    </p>
                </div>
            </div>

        </div>

        <img 
            :src="assets.corner" 
            alt="Corner Left"
            class="absolute bottom-0 left-0 z-20 w-32 md:w-64 pointer-events-none translate-y-1"
        />

        <img 
              :src="assets.corner" 
              alt="Corner Right"
              class="absolute bottom-0 right-0 z-20 w-32 md:w-64 pointer-events-none -scale-x-100 translate-y-1"
          />
    </section>
</template>
