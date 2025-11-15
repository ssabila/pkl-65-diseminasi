<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Dapatkan URL halaman saat ini dari Inertia
const page = usePage();
const currentUrl = computed(() => page.url);

// Fungsi helper untuk mengecek link aktif
const isActive = (url) => {
  // Bikin 'Beranda' tetep aktif kalo kita di sub-halaman
  if (url === '/' && currentUrl.value !== '/') {
    return false;
  }
  return currentUrl.value.startsWith(url);
};
</script>

<template>
  <nav class="w-full sticky top-0 z-50 py-4 font-sans">
    
    <div class="max-w-7xl mx-auto bg-gradient-to-r from-[#E87A3E] to-[#F5A65B] shadow-lg rounded-full">
      
      <div class="flex justify-between items-center h-16 px-6">
        
        <div class="flex items-center space-x-4">
          <Link href="/" class="flex items-center space-x-3">
            <img 
              class="h-10 w-10 rounded-full bg-white p-1" 
              src="/images/assets/LOGO-PKL_REV8.png" 
              alt="Logo PKL 65"
            >
            <div class="flex flex-col">
              <span class="text-white font-bold text-lg leading-tight">
                Website Hasil PKL 65
              </span>
              <span class="text-white/90 text-xs">
                Tahun Ajaran 2025/2026
              </span>
            </div>
          </Link>
        </div>

        <div class="hidden md:flex items-center space-x-8">
          <Link 
            href="/" 
            :class="[
              'text-base font-semibold transition-colors duration-150',
              isActive('/') ? 'text-white' : 'text-white/80 hover:text-white'
            ]"
          >
            Beranda
          </Link>
          <Link 
            href="/hasil-riset" 
            :class="[
              'text-base font-semibold transition-colors duration-150',
              isActive('/hasil-riset') ? 'text-white' : 'text-white/80 hover:text-white'
            ]"
          >
            Hasil Riset
          </Link>
        </div>

        <div class="flex items-center">
          <Link 
            href="/login" 
            class="inline-flex items-center px-6 py-2.5 text-sm font-semibold rounded-full text-[#E87A3E] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all shadow-md"
          >
            Login
          </Link>
        </div>

      </div>
    </div>
  </nav>
</template>