<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    email: ''
})

const submit = () => {
    form.post(route('password.request'))
}
</script>

<template>
    <Head title="Lupa Password" />

    <!-- BACKGROUND WITH COLORFUL FLOWERS (same as Login) -->
    <div class="min-h-screen bg-[#FFFBDF] flex items-center justify-center py-6 relative overflow-hidden">
      
      <!-- Floating Colorful Flowers Background (same as Login) -->
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
        
        <!-- CARD (same layout as Login) -->
        <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden">

          <!-- LEFT SECTION - MASCOT (same as Login) -->
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

          <!-- RIGHT SECTION - FORM LUPA PASSWORD -->
          <div class="md:w-[45%] p-8 flex flex-col justify-center">
            <h1 class="text-xl font-bold text-red-600 mb-2 text-center">
              Lupa Password
            </h1>
            <p class="text-gray-600 text-sm mb-5 text-center">
              Masukkan email Anda untuk menerima link reset password
            </p>

            <form @submit.prevent="submit">

              <div
                v-if="form.errors.email"
                class="mb-4 p-3 bg-red-100 text-orange-700 text-sm rounded-lg"
              >
                {{ form.errors.email }}
              </div>

              <!-- EMAIL -->
              <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                  Email
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="Masukkan email Anda"
                  class="w-full px-4 py-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-orange-400"
                  required
                />
              </div>

              <!-- BUTTON -->
              <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3 bg-[#EF874B] hover:bg-[#D94313] text-white font-semibold rounded-xl transition-all disabled:opacity-50"
              >
                {{ form.processing ? 'Mohon tunggu...' : 'Kirim Link Reset' }}
              </button>
            </form>

            <div class="mt-6 text-center">
              <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-[#EF874B] text-[#EF874B] rounded-full font-medium hover:bg-[#EF874B] hover:text-white transition-all duration-200 group"
              >
                Kembali ke Login
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>