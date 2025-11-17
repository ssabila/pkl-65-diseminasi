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

  <div class="min-h-screen flex items-center justify-center bg-auth overflow-hidden">
    <div class="w-[380px] bg-white rounded-2xl shadow-lg overflow-hidden">
      <div class="bg-[#EF874B] text-[#FDFADD] text-center p-6">
        <div class="w-16 h-16 mx-auto bg-white text-amber-800 rounded-full flex items-center justify-center text-lg font-semibold">
          <img src="/images/assets/LOGO-PKL_REV8.png" class="w-full h-full" alt="Logo PKL 65">
        </div>
        <h1 class="font-rakkas mt-3 text-2xl tracking-wide">PKL 65</h1>
        <p class="text-sm mt-1">Politeknik Statistika STIS<br />D.I. Yogyakarta</p>
      </div>

      <div class="p-6">
        <h1 class="font-rakkas text-base mt-1 text-[#4A4A4A] mb-4 text-center">Login untuk Administrator</h1>
        <form @submit.prevent="handleLogin">
          
          <div v-if="form.errors.email || form.errors.password" class="mb-4 p-3 bg-red-100 text-orange-700 text-sm rounded-lg">
            {{ form.errors.email || form.errors.password }}
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Email</label>
            <input
              v-model="form.email"
              type="text"
              placeholder="Masukkan username atau NIM"
              class="w-full px-3 py-2 border bg-[#F3F3F5] text-[#717182] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Masukkan password"
                class="w-full px-3 py-2 pr-10 border bg-[#F3F3F5] text-[#717182] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
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
            class="w-full py-2 bg-[#EF874B] hover:bg-orange-600 text-white rounded-lg font-semibold transition-all disabled:opacity-50"
          >
            Login
          </button>
        </form>

        <div class="mt-4 text-center">
          <Link :href="route('home')" class="text-sm text-[#4A4A4A] hover:text-[#D94313] transition-colors">
            ← Kembali ke Beranda
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>