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

    <!-- BACKGROUND (Static Image) -->
    <div class="min-h-screen bg-auth flex items-center justify-center py-6 px-4 relative overflow-hidden">
      
      <!-- CARD Wrapper - Smaller Single Column -->
      <div class="w-full max-w-sm relative z-10">
        
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

          <!-- HEADER SECTION (Consistent with Login) -->
          <div class="bg-gradient-to-br from-[#EF874B] to-[#F9A825] pt-8 pb-6 px-6 text-center relative overflow-hidden">
            
            <!-- Decorative Bubbles (Subtle) -->
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/20 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute top-10 -left-8 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute bottom-4 right-10 w-12 h-12 bg-white/10 rounded-full blur-lg"></div>
            <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>

            <!-- CONTENT LAYER -->
            <div class="relative z-10 flex flex-col items-center">
              
              <!-- 1. MASCOT (Top - Static/HD) -->
              <img 
                src="/images/assets/mascot-pkl.svg" 
                class="w-32 h-auto drop-shadow-xl mb-3 object-contain" 
                alt="Mascot PKL 65" 
              />

              <!-- 2. BADGE (Pill Shape) -->
              <div class="bg-white rounded-full px-5 py-2 shadow-lg flex items-center gap-2 mb-2">
                 <img src="/images/assets/LOGO-PKL_REV8.png" class="w-6 h-6" alt="Logo PKL" />
                 <span class="font-rakkas text-[#EF874B] text-xl leading-none tracking-wide">PKL 65</span>
              </div>

              <!-- 3. SUBTITLE TEXT -->
              <p class="text-white text-sm font-semibold tracking-wide drop-shadow-sm opacity-90">
                  Politeknik Statistika STIS
              </p>
            </div>
          </div>

          <!-- FORM SECTION -->
          <div class="p-8 bg-white relative">
            
            <h1 class="text-xl font-bold text-[#7A2509] mb-4 text-center font-serif">
              Lupa Password
            </h1>
            <p class="text-gray-500 text-xs mb-6 text-center px-2 leading-relaxed">
              Masukkan email Anda untuk menerima link reset password
            </p>

            <form @submit.prevent="submit">

              <div
                v-if="form.errors.email"
                class="mb-4 p-3 bg-red-100 text-red-600 text-xs rounded-lg text-center"
              >
                {{ form.errors.email }}
              </div>

              <!-- EMAIL -->
              <div class="mb-6">
                <label class="block text-xs font-bold text-[#EF874B] mb-2 pl-1">
                  Email
                </label>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#EF874B] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input
                      v-model="form.email"
                      type="email"
                      placeholder="amel@gmail.com"
                      class="w-full pl-11 pr-4 py-2.5 text-sm bg-white border-2 border-orange-100 rounded-xl focus:border-[#EF874B] focus:ring-0 transition-all placeholder-gray-300 text-gray-700 font-medium"
                      required
                    />
                </div>
              </div>

              <!-- BUTTON -->
              <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3 bg-[#EF874B] hover:bg-[#D94313] text-white text-sm font-bold rounded-xl transition-all disabled:opacity-50 shadow-lg hover:shadow-orange-200 transform hover:-translate-y-0.5 active:translate-y-0"
              >
                {{ form.processing ? 'Mohon tunggu...' : 'Kirim Link Reset' }}
              </button>
            </form>

            <div class="mt-8 text-center border-t border-gray-100 pt-6">
              <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-gray-200 text-xs font-semibold text-gray-500 hover:text-[#EF874B] hover:border-[#EF874B] hover:bg-orange-50 transition-all duration-300 group"
              >
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Login
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
