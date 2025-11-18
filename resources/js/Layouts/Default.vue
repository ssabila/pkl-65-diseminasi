<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import NavProfile from '@/Components/NavProfile.vue'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'

const page = usePage()
const isSidebarOpen = ref(true) // Default terbuka
const isLayoutReady = ref(false)

const isMobile = () => window.innerWidth < 1024

const toggleSidebar = () => {
    console.log('Toggle sidebar - before:', isSidebarOpen.value)
    isSidebarOpen.value = !isSidebarOpen.value
    console.log('Toggle sidebar - after:', isSidebarOpen.value)
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const handleClickAway = (event) => {
    // Hanya auto-close di mobile
    if (!isMobile()) return
    
    const sidebar = document.querySelector('[data-sidebar]')
    const sidebarContent = document.querySelector('[data-sidebar-content]')
    const hamburger = event.target.closest('button[aria-label="Toggle sidebar"]')
    
    // Jangan close kalau klik hamburger
    if (hamburger) return

    if (
        isSidebarOpen.value &&
        sidebar &&
        !sidebar.contains(event.target) &&
        !sidebarContent?.contains(event.target)
    ) {
        closeSidebar()
    }
}

const handleKeyDown = (event) => {
    if (event.key === 'Escape' && isSidebarOpen.value) {
        closeSidebar()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    // Load saved state
    const savedState = localStorage.getItem('sidebarOpen')
    if (savedState !== null) {
        isSidebarOpen.value = savedState === 'true'
    } else {
        // Default: terbuka di desktop, tertutup di mobile
        isSidebarOpen.value = !isMobile()
    }
    
    console.log('Initial sidebar state:', isSidebarOpen.value)

    setTimeout(() => {
        isLayoutReady.value = true
    }, 50)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
})

// Watch perubahan sidebar state
watch(isSidebarOpen, (newVal) => {
    console.log('Sidebar state changed to:', newVal)
})
</script>

<template>
    <div
        class="min-h-screen bg-[var(--color-bg)]"
        role="document"
        :class="{ 'opacity-0': !isLayoutReady }">

        <!-- Overlay untuk mobile -->
        <div
            v-if="isSidebarOpen && isMobile()"
            class="fixed inset-0 bg-black/30 z-30"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile navigation menu"
            aria-hidden="true"
            @click.stop="closeSidebar"></div>

        <!-- Sidebar -->
        <NavSidebarDesktop
            data-sidebar
            role="navigation"
            aria-label="Main sidebar"
            :aria-expanded="isSidebarOpen.toString()"
            class="fixed left-0 top-[70px] w-64 h-[calc(100vh-70px)] transition-transform duration-300 z-[60]"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-64'"
            @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <!-- NAVBAR ATAS -->
            <header
                role="banner"
                class="fixed top-0 left-0 right-0 w-full bg-[var(--color-surface)] z-[55] h-[70px] shadow-xs border-b border-[var(--color-border)]">
                <nav
                    class="flex h-full items-center justify-between px-4"
                    role="navigation"
                    aria-label="Primary navigation">
                    
                    <!-- LEFT: Hamburger Button - SELALU TAMPIL -->
                    <button
                        type="button"
                        class="p-2 rounded-md text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                        aria-label="Toggle sidebar"
                        :aria-expanded="isSidebarOpen.toString()"
                        @click.stop="toggleSidebar">
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- RIGHT: Profile Menu -->
                    <NavProfile />
                </nav>
            </header>

            <!-- Main Content - PERBAIKAN: tidak pakai !isMobile() -->
            <main
                class="flex-1 pt-[70px] transition-all duration-300"
                :class="isSidebarOpen ? 'lg:ml-64' : 'ml-0'"
                role="main"
                aria-label="Main content">
                <slot />
            </main>
        </div>
    </div>
</template>