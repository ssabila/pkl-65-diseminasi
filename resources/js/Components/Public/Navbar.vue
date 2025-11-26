<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

// Dapatkan URL halaman saat ini dari Inertia
const page = usePage();
const currentUrl = computed(() => page.url);

// State untuk dropdown profile & mobile menu
const showDropdown = ref(false);
const isMobileMenuOpen = ref(false); // State baru buat mobile menu
const user = computed(() => page.props.auth?.user);
const isLoggedIn = computed(() => !!user.value);

// Fungsi helper untuk mengecek link aktif
const isActive = (url) => {
  // Bikin 'Beranda' tetep aktif kalo kita di sub-halaman
  if (url === '/' && currentUrl.value !== '/') {
    return false;
  }
  return currentUrl.value.startsWith(url);
};

// Fungsi untuk menutup dropdown ketika klik di luar
const handleClickOutside = (event) => {
  // Tutup profile dropdown
  const dropdown = event.target.closest('.relative-profile');
  if (!dropdown && showDropdown.value) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <nav class="w-full sticky top-0 z-50 py-4 font-sans px-4 md:px-0">
    
    <div class="max-w-7xl mx-auto bg-gradient-to-r from-[#E87A3E] to-[#F5A65B] shadow-lg transition-all duration-300"
         :class="isMobileMenuOpen ? 'rounded-3xl' : 'rounded-full'">
      
      <div class="flex justify-between items-center min-h-[4rem] px-6">
        
        <div class="flex items-center space-x-4 shrink-0">
          <Link href="/" class="flex items-center space-x-3">
            <img 
              class="h-10 w-10 rounded-full bg-white p-1" 
              src="/images/assets/LOGO-PKL_REV8.png" 
              alt="Logo PKL 65"
            >
            <div class="flex flex-col">
              <span class="text-white font-bold text-sm md:text-lg leading-tight">
                Website Hasil PKL 65
              </span>
              <span class="text-white/90 text-[10px] md:text-xs">
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
          <a 
            href="http://localhost:8000/hasil-riset"
            :class="[
              'text-base font-semibold transition-colors duration-150',
              isActive('/hasil-riset') ? 'text-white' : 'text-white/80 hover:text-white'
            ]"
          >
            Hasil Riset
          </a>
        </div>

        <div class="flex items-center gap-3">
          
          <div v-if="isLoggedIn" class="relative relative-profile">
            <button 
              @click="showDropdown = !showDropdown"
              class="inline-flex items-center px-3 py-2 md:px-6 md:py-2.5 text-sm font-semibold rounded-full text-[#E87A3E] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all shadow-md"
            >
              <svg class="w-4 h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="hidden md:inline">{{ user.name }}</span>
              <svg class="w-4 h-4 ml-2 hidden md:block" :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <div v-if="showDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
              <div class="px-4 py-2 md:hidden border-b border-gray-100 mb-1">
                <p class="text-xs text-gray-500">Login sebagai</p>
                <p class="text-sm font-bold text-gray-800 truncate">{{ user.name }}</p>
              </div>

              <Link 
                :href="route('user.index')"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                @click="showDropdown = false"
              >
                Profile
              </Link>
              
              <Link 
                :href="route('dashboard')"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                @click="showDropdown = false"
              >
                Dashboard Administrator
              </Link>
              
              <hr class="my-2 border-gray-200">
              
              <Link 
                :href="route('logout')"
                method="post"
                as="button"
                class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left"
                @click="showDropdown = false"
              >
                Logout
              </Link>
            </div>
          </div>
          
          <Link 
            v-else
            href="/login" 
            class="inline-flex items-center px-4 py-2 md:px-6 md:py-2.5 text-sm font-semibold rounded-full text-[#E87A3E] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all shadow-md"
          >
            Login
          </Link>

          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-full text-white hover:bg-white/20 focus:outline-none transition-colors"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path 
                :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" 
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" 
              />
              <path 
                :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" 
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" 
              />
            </svg>
          </button>

        </div>
      </div>

      <div v-show="isMobileMenuOpen" class="md:hidden px-6 pb-6 pt-2 space-y-3">
        <Link 
          href="/" 
          @click="isMobileMenuOpen = false"
          class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
          :class="isActive('/') ? 'bg-white/20 text-white' : 'text-white/90 hover:bg-white/10 hover:text-white'"
        >
          Beranda
        </Link>
        <a 
          href="#"
          @click.prevent="goToHasilRiset(); isMobileMenuOpen = false;"
          class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
          :class="isActive('/hasil-riset') ? 'bg-white/20 text-white' : 'text-white/90 hover:bg-white/10 hover:text-white'"
        >
          Hasil Riset
        </a>
      </div>

    </div>
  </nav>
</template>