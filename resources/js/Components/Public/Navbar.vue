<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

// Dapatkan URL halaman saat ini dari Inertia
const page = usePage();
const currentUrl = computed(() => page.url);

// State untuk dropdown profile & mobile menu
const showDropdown = ref(false);
const isMobileMenuOpen = ref(false); 
const user = computed(() => page.props.auth?.user);
const isLoggedIn = computed(() => !!user.value);

// Fungsi helper untuk mengecek link aktif
const isActive = (url) => {
  if (url === '/' && currentUrl.value !== '/') {
    return false;
  }
  return currentUrl.value.startsWith(url);
};

// Fungsi untuk menutup dropdown ketika klik di luar
const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.relative-profile');
  if (!dropdown && showDropdown.value) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

const goToHasilRiset = () => {
  window.location.href = 'http://localhost:8000/hasil-riset';
};

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <nav class="w-full sticky top-0 z-50 py-3 md:py-4 font-sans px-3 md:px-0">
    
    <div class="max-w-7xl mx-auto bg-[#E87A3E] border border-orange-600/20 shadow-xl transition-all duration-300"
         :class="isMobileMenuOpen ? 'rounded-3xl' : 'rounded-full'">
      
      <div class="flex justify-between items-center min-h-[3.5rem] md:min-h-[4rem] px-4 md:px-6">
        
        <div class="flex items-center space-x-3 md:space-x-4 shrink-0">
          <Link href="/" class="flex items-center space-x-3 group">
            <img 
              class="h-9 w-9 md:h-10 md:w-10 rounded-full bg-white p-0.5 shadow-sm group-hover:scale-105 transition-transform" 
              src="/images/assets/LOGO-PKL_REV8.png" 
              alt="Logo PKL 65"
            >
            
            <span class="block md:hidden text-white font-bold text-lg tracking-wide">
              PKL 65
            </span>

            <div class="hidden md:flex flex-col">
              <span class="text-white font-bold text-sm md:text-lg leading-tight tracking-wide">
                Website Hasil PKL 65
              </span>
              <span class="text-orange-100 text-[10px] md:text-xs font-medium">
                Tahun Ajaran 2025/2026
              </span>
            </div>
          </Link>
        </div>

        <div class="hidden md:flex items-center space-x-2">
          <Link 
            href="/" 
            class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-200"
            :class="[
              isActive('/') 
                ? 'bg-white text-[#E87A3E] shadow-sm' // Aktif: Putih Solid
                : 'text-white hover:bg-black/10'       // Tidak Aktif: Transparan
            ]"
          >
            Beranda
          </Link>

          <a 
            href="http://localhost:8000/hasil-riset"
            class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-200"
            :class="[
              isActive('/hasil-riset') 
                ? 'bg-white text-[#E87A3E] shadow-sm' 
                : 'text-white hover:bg-black/10'
            ]"
          >
            Hasil Riset
          </a>
        </div>

        <div class="flex items-center gap-2 md:gap-3">
          
          <div v-if="isLoggedIn" class="relative relative-profile">
            <button 
              @click="showDropdown = !showDropdown"
              class="inline-flex items-center pl-1 pr-3 py-1 md:px-5 md:py-2 text-sm font-bold rounded-full text-[#E87A3E] bg-white hover:bg-gray-50 transition-all shadow-md"
            >
              <div class="w-7 h-7 md:w-4 md:h-4 md:mr-2 rounded-full bg-gray-100 md:bg-transparent flex items-center justify-center">
                 <svg class="w-4 h-4 text-[#E87A3E] md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                 <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              </div>
              <span class="hidden md:inline">{{ user.name }}</span>
              <svg class="w-4 h-4 ml-1 md:ml-2 hidden md:block transition-transform duration-200" :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <div v-if="showDropdown" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right">
              <div class="px-4 py-3 md:hidden border-b border-gray-100 mb-1">
                <p class="text-xs text-gray-500 font-medium">Login sebagai</p>
                <p class="text-sm font-bold text-gray-800 truncate">{{ user.name }}</p>
              </div>
              <Link :href="route('user.index')" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-[#E87A3E] transition-colors" @click="showDropdown = false">Profile</Link>
              <Link :href="route('dashboard')" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-[#E87A3E] transition-colors" @click="showDropdown = false">Dashboard Administrator</Link>
              <hr class="my-2 border-gray-100">
              <Link :href="route('logout')" method="post" as="button" class="flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors w-full text-left" @click="showDropdown = false">Logout</Link>
            </div>
          </div>
          
          <Link 
            v-else
            href="/login" 
            class="inline-flex items-center px-4 py-1.5 md:px-6 md:py-2 text-sm font-bold rounded-full border-2 border-white text-white bg-transparent hover:bg-white hover:text-[#E87A3E] transition-all duration-300 shadow-sm hover:shadow-md"
          >
            Login
          </Link>

          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-full text-white hover:bg-black/10 focus:outline-none transition-colors"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

        </div>
      </div>

      <div v-show="isMobileMenuOpen" class="md:hidden px-4 pb-4 pt-2 space-y-2 border-t border-white/20 mt-2">
        <Link 
          href="/" 
          @click="isMobileMenuOpen = false"
          class="block px-4 py-3 rounded-xl text-base font-bold transition-colors"
          :class="isActive('/') ? 'bg-white text-[#E87A3E]' : 'text-white hover:bg-black/10'"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            Beranda
          </div>
        </Link>
        <a 
          href="#"
          @click.prevent="goToHasilRiset(); isMobileMenuOpen = false;"
          class="block px-4 py-3 rounded-xl text-base font-bold transition-colors"
          :class="isActive('/hasil-riset') ? 'bg-white text-[#E87A3E]' : 'text-white hover:bg-black/10'"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            Hasil Riset
          </div>
        </a>
      </div>

    </div>
  </nav>
</template>