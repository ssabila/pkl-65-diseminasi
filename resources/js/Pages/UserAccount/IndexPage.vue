<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'
import PageHeader from '@/Components/PageHeader.vue'

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

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true
    })
}

const submitPasswordForm = () => {
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
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Update your personal information and email address
                    </p>

                    <form
                        id="profile-form"
                        class="max-w-2xl space-y-8"
                        @submit.prevent="submitProfileForm">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    v-model="profileForm.name"
                                    label="Legal name"
                                    :error="profileForm.errors.name"
                                    required
                                    placeholder="Enter your full name" />
                                <FormInput
                                    v-model="profileForm.email"
                                    label="Email address"
                                    type="email"
                                    autocomplete="email"
                                    :error="profileForm.errors.email"
                                    disabled />
                            </div>
                            <FormInput
                                v-model="profileForm.location"
                                label="Location"
                                :error="profileForm.errors.location"
                                placeholder="Enter your location" />
                        </div>
                    </form>
                </div>
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button
                    type="submit"
                    form="profile-form"
                    class="btn btn-sm btn-primary"
                    :disabled="profileForm.processing"
                    :aria-busy="profileForm.processing">
                    <svg
                        v-if="profileForm.processing"
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
                    {{ profileForm.processing ? 'Saving...' : 'Save profile' }}
                </button>
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
                        @submit.prevent="submitPasswordForm">
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
    </main>
</template>
