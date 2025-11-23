<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue' 
import FormCheckbox from '../../Components/FormCheckbox.vue' 
import Modal from '../../Components/Modal.vue'
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

const showMagicLinkModal = ref(false)
const magicLinkForm = useForm({
    email: ''
})

const showPassword = ref(false)

const handleLogin = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

const sendMagicLink = () => {
    magicLinkForm.post(route('magic.login'), {
        onFinish: () => {
            if (!magicLinkForm.hasErrors) {
                showMagicLinkModal.value = false
            }
        }
    })
}
</script>

<template>
  <Head title="Login" />

  <div class="min-h-screen flex items-center justify-center overflow-hidden py-4 md:py-8" style="background-color: #FFFBDF;">
    <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden max-w-3xl max-h-[90vh] md:max-h-[550px] w-full mx-4 md:mx-6">
      <!-- Left side - Mascot and branding -->
      <div class="flex-none md:flex-1 bg-[#FCDA7B] flex flex-col items-center justify-center py-4 px-3 md:p-7 text-center relative overflow-hidden">
        <!-- Batik pattern background -->
        <div class="absolute inset-0" style="background-image: url('/images/assets/pattern-batik.svg'); background-size: 200%; background-repeat: no-repeat; background-position: center; opacity: 0.2;"></div>
        
        <!-- Content with relative positioning -->
        <div class="relative z-10 w-full h-full flex flex-col md:flex-col items-center justify-center">
          <!-- Logo and title -->
          <div class="mb-1 md:mb-2">
            <div class="w-8 h-8 md:w-14 md:h-14 mx-auto bg-white rounded-full flex items-center justify-center shadow-lg mb-1 md:mb-2">
              <img src="/images/assets/LOGO-PKL_REV8.png" class="w-6 h-6 md:w-9 md:h-9" alt="Logo PKL 65">
            </div>
            <h1 class="font-rakkas text-lg md:text-3xl font-bold text-red-600 tracking-wide">PKL 65</h1>
            <p class="text-red-600 text-xs md:text-base mt-0.5 md:mt-1 font-medium">Politeknik Statistika STIS<br />D.I. Yogyakarta</p>
          </div>

          <!-- Mascot -->
          <div class="mt-0 md:mt-1 flex items-start justify-center">
            <div class="transform hover:scale-105 transition-transform duration-300">
              <img src="/images/assets/mascot-pkl.svg" class="w-24 h-32 md:w-56 md:h-72 drop-shadow-lg" alt="PKL Mascot">
            </div>
          </div>
        </div>
      </div>

      <!-- Right side - Login form -->
      <div class="flex-1 p-3 md:p-7 flex flex-col justify-center bg-white">
        <div class="max-w-sm mx-auto w-full">
          <h1 class="text-base md:text-xl font-bold text-red-600 mb-3 md:mb-5 text-center">Selamat Datang, Administrator!</h1>
        <form @submit.prevent="handleLogin">
          
          <div v-if="form.errors.email || form.errors.password" class="mb-4 p-3 bg-red-100 text-orange-700 text-sm rounded-lg">
            {{ form.errors.email || form.errors.password }}
          </div>

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input
              v-model="form.email"
              type="text"
              placeholder="Masukkan username atau NIM"
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200 placeholder-gray-500"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Masukkan password"
                class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200 placeholder-gray-500"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#717182] hover:text-[#4A4A4A] transition-colors"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex justify-between items-center mb-6">
            <label class="flex items-center text-sm text-[#4A4A4A]">
              <input v-model="form.remember" type="checkbox" class="mr-2 rounded border-[#D1D5DC]" />
              Ingat saya
            </label>
            <Link :href="route('password.request')" class="text-sm text-[#D94313] hover:underline">Lupa password?</Link>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <span v-if="!form.processing">Login</span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Memproses...
            </span>
          </button>
        </form>

        <div class="mt-6 text-center">
          <Link :href="route('home')" class="inline-flex items-center text-sm text-gray-600 hover:text-red-600 font-medium transition-colors group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Beranda
          </Link>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>