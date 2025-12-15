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

  <!-- BACKGROUND (Static Image) -->
  <div class="min-h-screen bg-auth flex items-center justify-center py-6 px-4 relative overflow-hidden">
    
    <!-- CARD Wrapper - Smaller Single Column -->
    <div class="w-full max-w-sm relative z-10">
      
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

        <!-- HEADER SECTION (Orange Gradient with Bubbles) -->
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
           
          <h1 class="text-xl font-bold text-[#7A2509] mb-6 text-center font-serif">
            Selamat Datang, Administrator!
          </h1>

          <form @submit.prevent="handleLogin">

            <div
              v-if="form.errors.email || form.errors.password"
              class="mb-4 p-3 bg-red-100 text-red-600 text-xs rounded-lg text-center"
            >
              {{ form.errors.email || form.errors.password }}
            </div>

            <!-- EMAIL -->
            <div class="mb-5">
              <label class="block text-xs font-bold text-[#EF874B] mb-2 pl-1">
                Email
              </label>
              <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#EF874B] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <input
                    v-model="form.email"
                    type="text"
                    placeholder="amel@gmail.com"
                    class="w-full pl-11 pr-4 py-2.5 text-sm bg-white border-2 border-orange-100 rounded-xl focus:border-[#EF874B] focus:ring-0 transition-all placeholder-gray-300 text-gray-700 font-medium"
                />
              </div>
            </div>

            <!-- PASSWORD -->
            <div class="mb-6">
              <label class="block text-xs font-bold text-[#EF874B] mb-2 pl-1">
                Password
              </label>
              <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#EF874B] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  class="w-full pl-11 pr-11 py-2.5 text-sm bg-white border-2 border-orange-100 rounded-xl focus:border-[#EF874B] focus:ring-0 transition-all placeholder-gray-300 text-gray-700 font-medium"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#EF874B] transition-colors"
                >
                  <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- REMEMBER + FORGOT -->
            <div class="flex justify-between items-center mb-8 text-xs font-medium">
              <label class="flex items-center gap-2 cursor-pointer group text-gray-500 hover:text-gray-700">
                <input 
                    v-model="form.remember" 
                    type="checkbox" 
                    class="w-3.5 h-3.5 rounded border-gray-300 text-[#EF874B] focus:ring-[#EF874B]"
                />
                <span>Ingat saya</span>
              </label>

              <Link
                :href="route('password.request')"
                class="text-[#EF874B] hover:text-[#D94313] transition-colors"
              >
                Lupa password?
              </Link>
            </div>

            <!-- BUTTON -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3 bg-[#EF874B] hover:bg-[#D94313] text-white text-sm font-bold rounded-xl transition-all disabled:opacity-50 shadow-lg hover:shadow-orange-200 transform hover:-translate-y-0.5 active:translate-y-0"
            >
              {{ form.processing ? 'Memproses...' : 'Login' }}
            </button>
          </form>

          <!-- Back to Home Button (Styled) -->
          <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <Link
              :href="route('home')"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-gray-200 text-xs font-semibold text-gray-500 hover:text-[#EF874B] hover:border-[#EF874B] hover:bg-orange-50 transition-all duration-300 group"
            >
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>