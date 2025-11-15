<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PKL65Layout from '@/Layouts/PKL65Layout.vue';

// Definisikan layout
defineOptions({ layout: PKL65Layout });

// Nanti lu bisa terima data dari controller di sini
// defineProps({
//   risets: Array,
//   topics: Array,
//   visualizations: Array
// });
</script>

<template>
  <Head title="Hasil Riset" />

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
        <div>
          <h3 class="font-sub text-lg text-orange-100">Riset 2</h3>
          <ul class="mt-2 space-y-1 pl-4">
            <li><Link href="#" class="text-white hover:underline">Sub Topik 2.1</Link></li>
          </ul>
        </div>
        </nav>
    </aside>

    <section class="w-3/4 bg-pkl-light-yellow p-10">
      <h1 class="font-sub text-4xl text-pkl-dark-blue">
        Judul Sub Menu
      </h1>

      <div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
        <h2 class="font-sub text-2xl text-pkl-dark-blue">
          Distribusi Berdasarkan Variabel 1
        </h2>
        <div class="mt-4">
          <div class="bg-gray-200 h-64 w-full flex items-center justify-center">
            (Chart.js Canvas di sini)
          </div>
        </div>
        <div class="mt-4">
          <h3 class="font-sub text-lg text-pkl-dark-blue">Interpretasi:</h3>
          <p class="mt-2 text-gray-700">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit...
          </p>
        </div>
      </div>

      </section>
  </main>
</template>