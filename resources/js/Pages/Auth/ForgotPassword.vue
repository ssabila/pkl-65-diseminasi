<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

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
          <h1 class="font-rakkas text-base mt-1 text-[#4A4A4A] mb-4 text-center">Lupa Password</h1>
          
          <p class="text-[#4A4A4A] text-sm mb-4 text-center" role="note">
            Masukkan email Anda untuk menerima link reset password
          </p>

          <form @submit.prevent="submit">
            <div v-if="form.errors.email" class="mb-4 p-3 bg-red-100 text-orange-700 text-sm rounded-lg">
              {{ form.errors.email }}
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="Masukkan email Anda"
                class="w-full px-3 py-2 border bg-[#F3F3F5] text-[#717182] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                required
              />
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2 bg-[#EF874B] hover:bg-orange-600 text-white rounded-lg font-semibold transition-all disabled:opacity-50"
            >
              {{ form.processing ? 'Mohon tunggu...' : 'Kirim Link Reset' }}
            </button>
          </form>

          <div class="mt-4 text-center">
            <Link :href="route('login')" class="text-sm text-[#4A4A4A] hover:text-[#D94313] transition-colors">
              ← Kembali ke Login
            </Link>
          </div>
        </div>
      </div>
    </div>
</template>
