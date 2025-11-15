<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PKL65Layout from '@/Layouts/PKL65Layout.vue';

// Definisikan layout
defineOptions({ layout: PKL65Layout });

// Nanti lu bisa terima data dari controller di sini
// defineProps({
//   documents: Array
// });
</script>

<template>
  <Head title="Dokumen" />

  <main class="flex min-h-screen">
    
    <aside class="w-1/4 bg-pkl-base-orange p-8">
      <h2 class="font-sub text-2xl text-white">Website Hasil PKL 65</h2>
      
      <nav class="mt-8 space-y-4">
        <div>
          <h3 class="font-sub text-lg text-orange-100">Riset 1</h3>
          <ul class="mt-2 space-y-1 pl-4">
            <li><Link href="#" class="text-white hover:underline">Sub Topik 1.1</Link></li>
            <li><Link href="#" class="text-white hover:underline">Sub Topik 1.2</Link></li>
          </ul>
        </div>
        </nav>
    </aside>

    <section class="w-3/4 bg-pkl-light-yellow p-10">
      <h1 class="font-sub text-4xl text-pkl-dark-blue">
        Dokumen PKL 65
      </h1>
      <p class="mt-2 text-lg text-gray-700">
        Koleksi dokumen, laporan, dan dataset hasil riset gig worker
      </p>

      <div class="mt-8 space-y-4">
        
        <div class="bg-white p-5 rounded-lg shadow-lg flex items-center justify-between">
          <div>
            <h3 class="font-sub text-xl text-pkl-dark-blue">
              Laporan Lengkap PKL 65
            </h3>
            <p class="text-sm text-gray-500">
              Laporan lengkap hasil riset gig worker di DI Yogyakarta.
            </p>
          </div>
          <div class="flex space-x-2">
            <Link href="#" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold">Lihat</Link>
            <Link href="#" class="bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Download</Link>
          </div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-lg flex items-center justify-between">
          <div>
            <h3 class="font-sub text-xl text-pkl-dark-blue">
              Buku Hasil PKL 65
            </h3>
            <p class="text-sm text-gray-500">
              Buku hasil diseminasi visualisasi data...
            </p>
          </div>
          <div class="flex space-x-2">
            <Link href="#" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold">Lihat</Link>
            <Link href="#" class="bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">Download</Link>
          </div>
        </div>

        </div>

    </section>
  </main>
</template>