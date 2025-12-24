<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavProfile from '@/Components/NavProfile.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const pageTitle = computed(() => {
    const route = page.url
    if (route.includes('admin')) {
        if (route.includes('admin/users')) return 'User Management'
        if (route.includes('admin')) return 'Admin Dashboard'
    }
    return 'Dashboard'
})
</script>

<template>
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex justify-between items-center">
                <!-- Left: Page Title -->
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-semibold text-gray-900">{{ pageTitle }}</h1>
                </div>

                <!-- Right: User Profile -->
                <div class="flex items-center gap-4">
                    <!-- User Info Text -->
                    <div v-if="user" class="hidden md:block text-right">
                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ user.roles?.[0] || 'User' }}</p>
                    </div>

                    <!-- Profile Menu -->
                    <NavProfile />
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
nav {
    background: linear-gradient(to right, #ffffff 0%, #fafafa 100%);
}
</style>
