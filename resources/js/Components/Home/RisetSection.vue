<script setup>
// import { Link } from '@inertiajs/vue3'; // Dinonaktifkan sementara

const risetsStatis = [
    {
        id: 1,
        judul: 'Riset 1',
        deskripsi_singkat: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida risus commodo viverra maecenas accumsan lacus vel facilisis'
    },
    {
        id: 2,
        judul: 'Riset 2',
        deskripsi_singkat: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida risus commodo viverra maecenas accumsan lacus vel facilisis'
    },
    {
        id: 5,
        judul: 'Riset 5',
        deskripsi_singkat: 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida risus commodo viverra maecenas accumsan lacus vel facilisis'
    }
];

// Aset baru dan path yang diperbarui
const bgSilhouette = '/images/assets/landmark-prambanan-siluet.svg'; // Tetap
const wayangKiri = '/images/assets/Gundatala_3 1.svg'; // Path baru
const wayangKanan = '/images/assets/Gundatala_3 2.png'; // Asumsi sama
const daunBatik = '/images/assets/batik3.svg'; // Aset daun baru
const patternKuning = '/images/assets/pattern kuning 1.svg'; // Aset pattern baru
const ornamenCard = '/images/assets/batik3.svg'; // Asumsi ornamen di card juga batik3.svg
</script>

<template>
    <section 
        id="riset" 
        class="min-h-screen py-24 px-6 relative overflow-hidden flex flex-col justify-center"
        :style="{ backgroundColor: '#EF874B' }" >
        
        <div 
            class="absolute inset-x-0 bottom-1/3 top-0 z-0" 
            :style="{ 
                backgroundImage: `url(${patternKuning})`, 
                backgroundRepeat: 'repeat', 
                backgroundSize: '200px',
                opacity: 0.5 }"
        ></div>

        <img 
            :src="bgSilhouette" 
            alt="Candi Prambanan" 
            class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full max-w-5xl h-auto object-cover z-10 opacity-90" 
        />

        <img 
            :src="wayangKiri" 
            alt="Wayang Kiri" 
            class="absolute bottom-0 left-0 w-[20%] max-w-[200px] h-auto z-30 hidden md:block" 
        />
        <img 
            :src="wayangKiri" alt="Wayang Kanan" 
            class="absolute bottom-0 right-0 w-[20%] max-w-[200px] h-auto z-30 hidden md:block transform -scale-x-100" 
        />

        <div class="container mx-auto relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                
                <div 
                    v-for="riset in risetsStatis" 
                    :key="riset.id" 
                    class="rounded-xl shadow-lg p-6 text-center flex flex-col relative"
                    :style="{ backgroundColor: '#FFFBDF' }" >
                    <img 
                        :src="ornamenCard" 
                        alt="Ornamen" 
                        class="absolute top-3 left-3 h-10 w-10 opacity-50" 
                    />
                    <img 
                        :src="ornamenCard" 
                        alt="Ornamen" 
                        class="absolute top-3 right-3 h-10 w-10 opacity-50 transform -scale-x-100" 
                    />
                    
                    <h3 
                        class="text-2xl font-headline mt-8 mb-4" :style="{ color: '#D94313' }" >
                        {{ riset.judul }}
                    </h3>

                    <p class="text-gray-700 mb-6 text-sm grow">
                        {{ riset.deskripsi_singkat }}
                    </p>

                    <div class="mt-auto">
                        <a 
                            href="#" 
                            class="font-semibold py-2 px-5 rounded-md transition duration-300 ease-in-out text-sm"
                            :style="{ 
                                color: '#D94313', 
                                border: '1px solid #D94313', 
                                backgroundColor: '#FFFBDF' 
                            }"
                            @mouseover="($event.target.style.backgroundColor = '#D94313', $event.target.style.color = '#FFFBDF')"
                            @mouseleave="($event.target.style.backgroundColor = '#FFFBDF', $event.target.style.color = '#D94313')"
                        >
                            Lihat Hasil
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>