<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Default from '@/Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import NotificationDialog from '@/Components/NotificationDialog.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    location: props.user.location
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

// Dialog states
const showPasswordConfirm = ref(false)
const showSuccessNotif = ref(false)
const showErrorNotif = ref(false)
const notificationMessage = ref('')
const notificationTitle = ref('')

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        notificationTitle.value = 'Berhasil!'
        notificationMessage.value = flash.success
        showSuccessNotif.value = true
    } else if (flash?.error) {
        notificationTitle.value = 'Gagal!'
        notificationMessage.value = flash.error
        showErrorNotif.value = true
    }
}, { deep: true })

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true
    })
}

const handlePasswordSubmit = () => {
    showPasswordConfirm.value = true
}

const submitPasswordForm = () => {
    showPasswordConfirm.value = false
    passwordForm.put('/user/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset()
    })
}
</script>

<template>
    <Head title="Profile Settings" />
    <main class="max-w-7xl mx-auto" aria-labelledby="profile-settings">
        <div class="container-border">
            <PageHeader
                title="Profil Saya"
                description="Kelola informasi profil dan akun Anda"/>

            <!-- Basic Information Section -->
            <div class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    <div class="relative h-32 overflow-hidden">
                        <!-- Batik Pattern -->
                        <div class="absolute inset-0">
                            <img src="/images/assets/pattern-batik.svg" alt="Pattern Batik" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Profile Info Section -->
                    <div class="relative px-6 pb-6">
                        <!-- Avatar -->
                        <div class="absolute -top-12 left-6">
                            <div class="w-24 h-24 bg-white dark:bg-gray-800 rounded-full border-4 border-white dark:border-gray-800 shadow-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-orange-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Name and Info -->
                        <div class="pt-16 space-y-1">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ user.name }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                NIM: {{ userNIM }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Politeknik Statistika STIS
                            </p>
                        </div>

                        <!-- Form Section -->
                        <div class="mt-8">
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Nama Lengkap
                            </h3>

                            <form class="space-y-6">
                                <!-- Two Column Layout -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nama Lengkap -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Nama Lengkap
                                        </label>
                                        <input
                                            type="text"
                                            :value="user.name"
                                            disabled
                                            class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 cursor-not-allowed"
                                            placeholder="Nama Mahasiswa"
                                        />
                                    </div>

                                    <!-- NIM -->
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                            </svg>
                                            NIM
                                        </label>
                                        <input
                                            type="text"
                                            :value="userNIM"
                                            disabled
                                            class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 cursor-not-allowed"
                                            placeholder="222012345"
                                        />
                                    </div>
                                </div>

                                <!-- Email (Full Width) -->
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        :value="user.email"
                                        disabled
                                        class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 cursor-not-allowed"
                                        placeholder="mahasiswa@stis.ac.id"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Password Section -->
            <div class="p-6 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Password
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Ensure your account is using a secure password
                    </p>

                    <form
                        id="password-form"
                        class="max-w-2xl space-y-8"
                        @submit.prevent="handlePasswordSubmit">
                        <div class="space-y-6">
                            <FormInput
                                v-model="passwordForm.current_password"
                                label="Current password"
                                type="password"
                                autocomplete="current-password"
                                :error="passwordForm.errors.current_password"
                                required />
                            <FormInput
                                v-model="passwordForm.password"
                                label="New password"
                                type="password"
                                autocomplete="new-password"
                                :error="passwordForm.errors.password"
                                required />
                            <FormInput
                                v-model="passwordForm.password_confirmation"
                                label="Confirm new password"
                                type="password"
                                autocomplete="new-password"
                                :error="passwordForm.errors.password_confirmation"
                                required />
                        </div>
                    </form>
                </div>
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button
                    type="submit"
                    form="password-form"
                    class="btn btn-sm btn-primary"
                    :disabled="passwordForm.processing"
                    :aria-busy="passwordForm.processing">
                    <svg
                        v-if="passwordForm.processing"
                        class="animate-spin h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4" />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ passwordForm.processing ? 'Updating...' : 'Update password' }}
                </button>
            </div>
        </div>

        <!-- Dialogs -->
        <ConfirmDialog
            :show="showPasswordConfirm"
            message="Apakah anda yakin ingin menghapus hasil visualisasi berikut?"
            confirm-text="Hapus Visualisasi"
            cancel-text="Cancel"
            confirm-color="red"
            @confirm="submitPasswordForm"
            @close="showPasswordConfirm = false"
        />

        <NotificationDialog
            :show="showSuccessNotif"
            type="success"
            :title="notificationTitle"
            :message="notificationMessage"
            button-text="OK"
            @close="showSuccessNotif = false"
        />

        <NotificationDialog
            :show="showErrorNotif"
            type="error"
            :title="notificationTitle"
            :message="notificationMessage"
            button-text="OK"
            @close="showErrorNotif = false"
        />
    </main>
</template>
