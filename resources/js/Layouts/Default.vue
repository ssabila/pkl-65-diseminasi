<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import NavProfile from '@/Components/NavProfile.vue'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'
import AdminNavbar from '@/Components/AdminNavbar.vue'

const page = usePage()
const isSidebarOpen = ref(true) // Default terbuka
const isLayoutReady = ref(false)
const isMobile = ref(false)

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
    // Auto close sidebar on mobile, keep open on desktop
    if (!isMobile.value && !isSidebarOpen.value) {
        const savedState = localStorage.getItem('sidebarOpen')
        if (savedState === null || savedState === 'true') {
            isSidebarOpen.value = true
        }
    }
}

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
    if (!isMobile.value) return
    
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
    checkMobile()
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)
    window.addEventListener('resize', checkMobile)

    // Load saved state
    const savedState = localStorage.getItem('sidebarOpen')
    if (savedState !== null) {
        isSidebarOpen.value = savedState === 'true'
    } else {
        // Default: terbuka di desktop, tertutup di mobile
        isSidebarOpen.value = !isMobile.value
    }
    
    console.log('Initial sidebar state:', isSidebarOpen.value)

    setTimeout(() => isLayoutReady.value = true, 50)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
    window.removeEventListener('resize', checkMobile)
})

// Watch perubahan sidebar state
watch(isSidebarOpen, (newVal) => {
    console.log('Sidebar state changed to:', newVal)
})
</script>

<template>
    <div
        class="min-h-screen bg-[#FCEFEA]"
        :class="{ 'opacity-0': !isLayoutReady }"
        role="document">

        <!-- Overlay untuk mobile -->
        <div
            v-if="isMobile && isSidebarOpen"
            class="fixed inset-0 bg-black/30 z-40"
            @click="closeSidebar"></div>

        <!-- Floating Hamburger Button (when sidebar is closed) -->
        <button
            v-if="!isSidebarOpen"
            type="button"
            class="fixed top-4 left-4 z-50 p-3 bg-pkl-base-orange text-white rounded-lg shadow-lg hover:bg-orange-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-300"
            aria-label="Open sidebar"
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



        <!-- Sidebar -->
        <NavSidebarDesktop
            data-sidebar
            role="navigation"
            aria-label="Main sidebar"
            :aria-expanded="isSidebarOpen.toString()"
            :class="[
                'fixed w-64 h-screen transition-transform duration-300 z-[60]',
                'left-0 top-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-64'
            ]"
            @close="closeSidebar"
            @toggle="toggleSidebar" />

        <!-- MAIN WRAPPER -->
        <div class="flex flex-col min-h-screen">
            <!-- Admin Navbar -->
            <AdminNavbar />
            
            <!-- Main Content -->
            <main
                :class="[
                    'flex-1 transition-all duration-300',
                    isSidebarOpen ? 'ml-64' : 'ml-0'
                ]"
                role="main"
                aria-label="Main content">
                <slot />
            </main>
        </div>
    </div>
</template>