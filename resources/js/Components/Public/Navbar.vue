<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

// Dapatkan URL halaman saat ini dari Inertia
const page = usePage();
const currentUrl = computed(() => page.url);

// State untuk dropdown profile
const showDropdown = ref(false);
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
  const dropdown = event.target.closest('.relative');
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

        <div class="flex items-center relative">
          <!-- Profile Dropdown untuk user yang sudah login -->
          <div v-if="isLoggedIn" class="relative">
            <button 
              @click="showDropdown = !showDropdown"
              class="inline-flex items-center px-6 py-2.5 text-sm font-semibold rounded-full text-[#E87A3E] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ user.name }}
              <svg class="w-4 h-4 ml-2" :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div v-if="showDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
              <Link 
                :href="route('user.index')"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                @click="showDropdown = false"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
              </Link>
              
              <Link 
                :href="route('dashboard')"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                @click="showDropdown = false"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                </svg>
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
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
              </Link>
            </div>
          </div>
          
          <!-- Login button untuk user yang belum login -->
          <Link 
            v-else
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