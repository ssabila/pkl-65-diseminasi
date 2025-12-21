<script setup>
// Data statis
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

// Path aset
const bgSilhouette = '/images/assets/landmark-prambanan-siluet.png';
const wayangKiri = '/images/assets/Gundatala_3 1.png';
const wayangKanan = '/images/assets/Gundatala_3 2.png';
const patternKuning = '/images/assets/pattern kuning 1.png';
const ornamenCard = '/images/assets/batik3.png';
</script>

<template>
    <section id="riset" class="w-full pt-12 pb-64 px-6 relative overflow-hidden flex flex-col z-20 -mb-2"
        style="background-color: #EF874B;">

        <div class="absolute inset-0 z-0 pointer-events-none" :style="{
            backgroundImage: `url('${patternKuning}')`,
            backgroundRepeat: 'repeat',
            backgroundSize: '300px',
            opacity: 0.8
        }"></div>

        <img :src="bgSilhouette" alt="Candi Prambanan"
            class="absolute left-1/2 transform -translate-x-1/2 z-10 object-cover pointer-events-none"
            style="width: 1100px; height: 800px; bottom: -10px;" />

        <img :src="wayangKiri" alt="Wayang Kiri" class="absolute z-50 hidden md:block pointer-events-none"
            style="width: 300px; height: 300px; bottom: -20px; left: -70px;" />

        <img :src="wayangKanan" alt="Wayang Kanan" class="absolute z-50 hidden md:block pointer-events-none"
            style="width: 300px; height: 300px; bottom: -20px; right: -70px;" />

        <div class="container mx-auto relative z-40">

            <h2 class="text-3xl font-headline text-center text-white mb-10 drop-shadow-md">
                Riset Unggulan PKL 65
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

                <div v-for="riset in risetsStatis" :key="riset.id"
                    class="rounded-xl shadow-lg p-6 text-center flex flex-col relative transition-transform hover:-translate-y-2 duration-300"
                    style="background-color: #FFFBDF;">
                    <img :src="ornamenCard" class="absolute top-3 left-3 opacity-50"
                        style="width: 98px; height: 55px;" />
                    <img :src="ornamenCard" class="absolute top-3 right-3 opacity-50 transform -scale-x-100"
                        style="width: 98px; height: 55px;" />

                    <h3 class="text-2xl font-headline mt-8 mb-4" style="color: #D94313;">
                        {{ riset.judul }}
                    </h3>

                    <p class="text-gray-700 mb-6 text-sm grow leading-relaxed">
                        {{ riset.deskripsi_singkat }}
                    </p>

                    <div class="mt-auto">
                        <a href="#"
                            class="font-semibold py-2 px-6 rounded-md transition duration-300 ease-in-out text-sm inline-block"
                            style="color: #D94313; border: 1px solid #D94313; background-color: #FFFBDF;"
                            onmouseover="this.style.backgroundColor='#D94313'; this.style.color='#FFFBDF';"
                            onmouseout="this.style.backgroundColor='#FFFBDF'; this.style.color='#D94313';">
                            Lihat Hasil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>