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

const handleLogin = () => {
    form.post(route('login'))
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

  <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-amber-200 to-orange-300">
    <div class="w-[380px] bg-white rounded-2xl shadow-lg overflow-hidden">
      <div class="bg-gradient-to-r from-amber-800 to-orange-700 text-white text-center p-6">
        <div class="w-16 h-16 mx-auto bg-white text-amber-800 rounded-full flex items-center justify-center text-lg font-semibold">
          LOGO
        </div>
        <h1 class="font-rakkas mt-3 text-2xl tracking-wide">PKL 65</h1>
        <p class="text-sm mt-1">Politeknik Statistika STIS<br />D.I. Yogyakarta</p>
      </div>

      <div class="p-6">
        <form @submit.prevent="handleLogin">
          
          <div v-if="form.errors.email || form.errors.password" class="mb-4 p-3 bg-red-100 text-red-700 text-sm rounded-lg">
            {{ form.errors.email || form.errors.password }}
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input
              v-model="form.email"
              type="text"
              placeholder="Masukkan username atau NIM"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Masukkan password"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>

          <div class="flex justify-between items-center mb-6">
            <label class="flex items-center text-sm text-gray-600">
              <input v-model="form.remember" type="checkbox" class="mr-2 rounded border-gray-300" />
              Ingat saya
            </label>
            <Link :href="route('password.request')" class="text-sm text-orange-600 hover:underline">Lupa password?</Link>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold transition-all disabled:opacity-50"
          >
            Login sebagai Administrator
          </button>
        </form>
      </div>
    </div>
  </div>
</template>