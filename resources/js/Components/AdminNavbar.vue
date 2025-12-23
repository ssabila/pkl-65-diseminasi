<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const showDropdown = ref(false)

const handleClickOutside = (event) => {
    const dropdown = event.target.closest('.relative-profile')
    if (!dropdown && showDropdown.value) {
        showDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <nav class="bg-pkl-base-orange border-b border-orange-400 shadow-sm sticky top-0 z-40">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end items-center h-16">
                <!-- Right side - Profile Dropdown -->
                <div class="flex items-center">
                    <div class="relative relative-profile">
                        <button
                            @click="showDropdown = !showDropdown"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-orange-500/50 transition-colors"
                        >
                            <!-- User Icon -->
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-pkl-base-orange" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-white hidden sm:block">
                                {{ user?.name || 'Admin' }}
                            </span>
                            <svg
                                class="w-4 h-4 text-white transition-transform duration-200"
                                :class="{ 'rotate-180': showDropdown }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="showDropdown"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50"
                        >
                            <!-- Edit Profile -->
                            <Link
                                :href="route('user.index')"
                                class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-[#EF874B] transition-colors"
                                @click="showDropdown = false"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Edit Profile
                            </Link>

                            <hr class="my-2 border-gray-100">

                            <!-- Logout -->
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors w-full text-left"
                                @click="showDropdown = false"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
