<script setup>
// Data statis sesuai teks di gambar
const cakupanItems = [
    {
        no: 1,
        text: "Primary sampling unit daftar kabupaten kota beserta jumlah GIGnya di Provinsi DIY dari hasil estimasi GIG untuk Perhitungan Sensus Ekonomi 2026"
    },
    {
        no: 2,
        text: "Secondary sampling unit daftar kabupaten kota beserta jumlah GIGnya di Provinsi DIY dari hasil estimasi GIG untuk Perhitungan Sensus Ekonomi 2026"
    },
    {
        no: 3,
        text: "Lorem ipsum sampling unit daftar kabupaten kota beserta jumlah GIGnya di Provinsi DIY dari hasil estimasi GIG untuk Perhitungan Sensus Ekonomi 2026"
    },
    {
        no: 4,
        text: "Lorem Ipsum sampling unit daftar kabupaten kota beserta jumlah GIGnya di Provinsi DIY dari hasil estimasi GIG untuk Perhitungan Sensus Ekonomi 2026"
    }
];

// Path aset hiasan samping
const bungaCakupan = '/images/assets/bunga-cakupan.svg';
</script>

<template>
    <section 
        id="cakupan" 
        class="min-h-screen py-24 px-6 relative flex flex-col justify-center overflow-hidden -mt-4 z-10" 
        style="background-color: #FFFBDF;"
    >
        
        <div 
            class="absolute top-0 bottom-0 left-0 w-12 md:w-16 z-0"
            :style="{ 
                backgroundImage: `url('${bungaCakupan}')`, 
                backgroundRepeat: 'repeat-y',
                backgroundSize: 'contain'
            }"
        ></div>

        <div 
            class="absolute top-0 bottom-0 right-0 w-12 md:w-16 z-0 transform -scale-x-100"
            :style="{ 
                backgroundImage: `url('${bungaCakupan}')`, 
                backgroundRepeat: 'repeat-y',
                backgroundSize: 'contain'
            }"
        ></div>

        <div class="container mx-auto relative z-10 max-w-5xl px-8 md:px-16">
            
            <h2 
                class="text-4xl md:text-5xl font-headline mb-12 font-bold"
                style="color: #EF874B;"
            >
                Cakupan Penelitian
            </h2>

            <div class="flex flex-col gap-8">
                
                <div 
                    v-for="item in cakupanItems" 
                    :key="item.no" 
                    class="flex gap-6 items-start group"
                >
                    <div 
                        class="text-3xl md:text-4xl font-headline font-bold shrink-0 mt-1"
                        style="color: #EF874B;"
                    >
                        {{ item.no }}
                    </div>

                    <div class="flex flex-col w-full">
                        <p 
                            class="text-lg md:text-xl font-sans leading-relaxed mb-4"
                            style="color: #EF874B;" 
                        >
                            {{ item.text }}
                        </p>
                        
                        <div 
                            class="w-full h-[1px] opacity-50"
                            style="background-color: #EF874B;"
                        ></div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</template>