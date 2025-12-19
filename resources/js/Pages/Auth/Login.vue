<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import { ref } from 'vue'

defineOptions({
    layout: Auth
})

const { settings: { passwordlessLogin = true } = {} } = usePage().props

const form = useForm({
    email: '',
    password: '',
    remember: false
})

const showPassword = ref(false)

const handleLogin = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
  <Head title="Login" />

  <!-- BACKGROUND WITH COLORFUL FLOWERS -->
  <div class="min-h-screen bg-[#FFFBDF] flex items-center justify-center py-6 relative overflow-hidden">
    
    <!-- Floating Colorful Flowers Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <!-- Bunga besar - sudut kiri atas -->
      <img src="/images/assets/bunga2.png" class="absolute -top-8 -left-8 w-40 opacity-70 animate-spin-slow" style="filter: hue-rotate(0deg);" alt="" />
      
      <!-- Bunga kanan atas - warna oranye -->
      <img src="/images/assets/bunga2.png" class="absolute top-[5%] right-[5%] w-32 opacity-60 animate-float" style="filter: hue-rotate(25deg) saturate(1.2);" alt="" />
      
      <!-- Bunga kecil kanan tengah - warna oranye terang -->
      <img src="/images/assets/bunga2.png" class="absolute top-[35%] right-[3%] w-24 opacity-65 animate-spin-reverse" style="filter: hue-rotate(20deg) saturate(1.3);" alt="" />
      
      <!-- Bunga tengah atas -->
      <img src="/images/assets/bunga2.png" class="absolute top-[8%] left-[30%] w-20 opacity-45 animate-float" style="filter: hue-rotate(0deg);" alt="" />
      
      <!-- Bunga besar kiri bawah - warna kuning -->
      <img src="/images/assets/bunga2.png" class="absolute bottom-[10%] -left-6 w-36 opacity-60 animate-spin-slow" style="filter: hue-rotate(40deg) saturate(1.2);" alt="" />
      
      <!-- Bunga kanan bawah - warna coral -->
      <img src="/images/assets/bunga2.png" class="absolute -bottom-10 right-[15%] w-44 opacity-55 animate-float-delay" style="filter: hue-rotate(10deg);" alt="" />
      
      <!-- Bunga kecil bawah tengah -->
      <img src="/images/assets/bunga2.png" class="absolute bottom-[5%] left-[40%] w-16 opacity-50 animate-spin-reverse" style="filter: hue-rotate(35deg) saturate(1.1);" alt="" />
      
      <!-- Bunga tambahan -->
      <img src="/images/assets/bunga2.png" class="absolute top-[55%] left-[8%] w-18 opacity-40 animate-float" style="filter: hue-rotate(15deg);" alt="" />
    </div>
    
    <!-- WRAPPER KONTEN -->
    <div class="relative z-10 w-full max-w-4xl mx-4">
      
      <!-- CARD -->
      <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- LEFT SECTION - MASCOT (larger) -->
        <div class="md:w-[55%] bg-[#FCDA7B] flex flex-col items-center justify-center p-8 relative overflow-hidden">

          <!-- BATIK DALAM CARD -->
          <div class="absolute inset-0 bg-[url('/images/assets/pattern-batik.svg')] bg-no-repeat bg-center bg-[length:200%] opacity-20"></div>

          <div class="relative z-10 text-center">
            <div class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center shadow mb-2">
              <img src="/images/assets/LOGO-PKL_REV8.png" class="w-10 h-10" alt="Logo PKL">
            </div>

            <h1 class="font-rakkas text-2xl text-red-600">PKL 65</h1>
            <p class="text-red-600 text-sm mb-4">
              Politeknik Statistika STIS<br />D.I. Yogyakarta
            </p>

            <!-- Mascot dengan animasi naik turun -->
            <img
              src="/images/assets/mascot-pkl.svg"
              class="w-52 mx-auto drop-shadow-lg animate-bounce-gentle"
              alt="Mascot"
            />
          </div>
        </div>

        <!-- RIGHT SECTION - FORM LOGIN -->
        <div class="md:w-[45%] p-8 flex flex-col justify-center">
          <h1 class="text-xl font-bold text-red-600 mb-5 text-center">
            Selamat Datang, Administrator!
          </h1>

          <form @submit.prevent="handleLogin">

            <div
              v-if="form.errors.email || form.errors.password"
              class="mb-4 p-3 bg-red-100 text-orange-700 text-sm rounded-lg"
            >
              {{ form.errors.email || form.errors.password }}
            </div>

            <!-- EMAIL -->
            <div class="mb-4">
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Email
              </label>
              <input
                v-model="form.email"
                type="text"
                placeholder="Masukkan email"
                class="w-full px-4 py-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-orange-400"
              />
            </div>

            <!-- PASSWORD -->
            <div class="mb-4">
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Password
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Masukkan password"
                  class="w-full px-4 py-3 pr-12 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-orange-400"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- REMEMBER + FORGOT -->
            <div class="flex justify-between items-center mb-5 text-sm">
              <label class="flex items-center gap-2">
                <input v-model="form.remember" type="checkbox" />
                Ingat saya
              </label>

              <Link
                :href="route('password.request')"
                class="text-orange-600 hover:underline"
              >
                Lupa password?
              </Link>
            </div>

            <!-- BUTTON -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3 bg-[#EF874B] hover:bg-[#D94313] text-white font-semibold rounded-xl transition-all disabled:opacity-50"
            >
              {{ form.processing ? 'Memproses...' : 'Login' }}
            </button>
          </form>

          <div class="mt-6 text-center">
            <Link
              :href="route('home')"
              class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-[#EF874B] text-[#EF874B] rounded-full font-medium hover:bg-[#EF874B] hover:text-white transition-all duration-200 group"
            >
              Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>