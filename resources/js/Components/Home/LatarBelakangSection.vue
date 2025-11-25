<script setup>

const content = {
    title: 'Latar Belakang',
    mainDesc: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    cardTitle: 'Fenomena Gig Economy',
    points: [
        'Lorem ipsum dolor sit amet consectetur adipiscing elit',
        'Sed do eiusmod tempor incididunt ut labore',
        'Quis nostrud exercitation ullamco laboris',
        'Excepteur sint occaecat cupidatat non proident'
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
        class="relative py-20 px-6 overflow-hidden flex flex-col justify-center -mt-8"
        :style="{ backgroundColor: colors.primaryOrange }" 
    >
        
        <div class="container mx-auto relative z-10 max-w-6xl">
            
            <div class="flex items-center gap-4 mb-6 -translate-x-11">
                <img :src="assets.gunungan3" alt="Icon" 
                    class="h-12 w-auto lg:scale-[1.6]" /> 
                    <h2 class="text-4xl md:text-5xl font-headline"
                    :style="{ color: colors.textWhite }">
                    {{ content.title }}
                </h2>
            </div>

            <p class="text-base font-sans md:text-lg leading-relaxed text-justify mb-12 opacity-90"
               :style="{ color: colors.textWhite }">
                {{ content.mainDesc }}
            </p>

            <div class="font-sans rounded-3xl p-4 md:p-12 shadow-lg"
                 :style="{ backgroundColor: colors.bgCream }">
                
                <h3 class="text-2xl md:text-3xl font-headline mb-8"
                    :style="{ color: colors.textOrange }">
                    {{ content.cardTitle }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div v-for="(point, index) in content.points" :key="index" 
                        class="flex items-start gap-3">
                        
                        <div class="mt-2 flex-shrink-0">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11" stroke="#D94313" stroke-width="2"/>
                                <path d="M7 12L10.5 15.5L17 9" stroke="#D94313" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <p class="font-medium text-lg leading-tight mt-2"
                          :style="{ color: colors.textRed}" >
                            {{ point }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="absolute bottom-0 left-0 w-full h-8 md:h-60 z-0 opacity-80 translate-y-30"
             :style="{ 
                backgroundImage: `url(${assets.border})`,
                backgroundRepeat: 'repeat-x',
                backgroundSize: 'contain',
                backgroundPosition: 'bottom'
             }">
        </div>

    </section>
</template>
