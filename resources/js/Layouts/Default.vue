<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'

const page = usePage()
const isSidebarOpen = ref(true)
const isLayoutReady = ref(false)

const isMobile = () => window.innerWidth < 1024

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const handleClickAway = (event) => {
    if (!isMobile()) return
    const sidebar = document.querySelector('[data-sidebar]')
    const hamburger = event.target.closest('button[aria-label="Toggle sidebar"]')

    if (hamburger) return

    if (isSidebarOpen.value && sidebar && !sidebar.contains(event.target)) {
        closeSidebar()
    }
}

const handleKeyDown = (event) => {
    if (event.key === 'Escape' && isSidebarOpen.value) closeSidebar()
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const saved = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = saved ? saved === 'true' : !isMobile()

    setTimeout(() => isLayoutReady.value = true, 50)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>
    <div
        class="min-h-screen bg-[#FCEFEA]"
        :class="{ 'opacity-0': !isLayoutReady }"
        role="document">

        <!-- Mobile Overlay -->
        <div
            v-if="isMobile() && isSidebarOpen"
            class="fixed inset-0 bg-black/30 z-30"
            @click="closeSidebar"></div>

        <!-- SIDEBAR ADMIN -->
        <NavSidebarDesktop
            data-sidebar
            class="fixed left-0 top-0 w-64 h-screen
                   bg-[#EF874B] text-white shadow-lg
                   transition-transform duration-300 z-[60]"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-64'"
        />

        <!-- MAIN WRAPPER -->
        <div class="flex flex-col min-h-screen">

            <!-- FLOATING HAMBURGER BUTTON (Only when sidebar is closed) -->
            <button
                v-if="!isSidebarOpen"
                type="button"
                class="fixed top-4 left-4 z-[70] p-3 rounded-lg bg-[#EF874B] text-white
                       hover:bg-[#e07638] transition shadow-lg"
                aria-label="Toggle sidebar"
                @click.stop="toggleSidebar">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- CONTENT -->
            <main
                class="flex-1 transition-all duration-300"
                :class="isSidebarOpen ? 'lg:ml-64' : 'ml-0'">

                <slot />
            </main>
        </div>
    </div>
</template>
