<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Shared/Public/NavSidebarDesktop.vue'
import Footer from '@/Shared/Public/Footer.vue'

const sidebarStorageKey = 'sidebarOpen'
const isMobile = () => window.innerWidth < 768
const isSidebarOpen = ref(false)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem(sidebarStorageKey, isSidebarOpen.value)
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem(sidebarStorageKey, false)
}

const handleClickAway = event => {
    const elements = {
        sidebar: document.querySelector('[data-sidebar]'),
        menuButton: document.querySelector('[data-menu-button]'),
        sidebarContent: document.querySelector('[data-sidebar-content]')
    }

    const isClickInside = Object.values(elements).some(el => el?.contains(event.target))

    if (isClickInside) return

    if (isMobile()) {
        closeSidebar()
    }
}

const handleKeyDown = event => {
    if (event.key === 'Escape' && isSidebarOpen.value && isMobile()) {
        closeSidebar()
    }
}


onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const savedSidebarState = localStorage.getItem(sidebarStorageKey)
    isSidebarOpen.value = savedSidebarState ? savedSidebarState === 'true' : !isMobile()

    onUnmounted(() => {
        document.removeEventListener('click', handleClickAway)
        document.removeEventListener('keydown', handleKeyDown)
    })
})
</script>

<template>
    <div class="min-h-screen bg-[var(--color-bg)]" role="document">
        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="isSidebarOpen && isMobile()"
            class="fixed inset-0 bg-black/30 z-30"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile navigation menu"
            aria-hidden="true"
            @click.stop="closeSidebar"></div>

        <!-- Main Sidebar Navigation -->
        <NavSidebarDesktop
            data-sidebar
            role="navigation"
            aria-label="Main sidebar"
            :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed left-0 top-16 w-64 h:[calc(100vh-4rem)] transition-transform duration-200 z-40 bg-[var(--color-surface)] shadow-lg"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']"
            @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header
                role="banner"
                class="fixed w-full top-0 bg-[var(--color-surface)] border-b border-[var(--color-border)] z-40">
                <nav
                    class="flex h-16 items-center px-4 gap-4"
                    role="navigation"
                    aria-label="Primary navigation">
                    <section
                        class="flex items-center gap-4"
                        aria-label="Site logo and menu controls">
                        <!-- Logo -->
                        <Link
                            href="/"
                            class="text-xl font-semibold text-[var(--color-text)] flex items-center"
                            aria-label="Go to homepage">
                            <img
                                src="/images/logo.png"
                                class="h-10 w-auto block "
                                alt="Logo" />
                        </Link>

                        <!-- Mobile Menu Toggle -->
                        <button
                            type="button"
                            data-menu-button
                            class="rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            aria-label="Toggle navigation menu"
                            :aria-expanded="isSidebarOpen"
                            @click="toggleSidebar">
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </section>

                    <section
                        class="flex flex-1 items-center justify-end gap-4"
                        aria-label="Site controls">
                        <!-- Theme Toggle Disabled - Light Mode Only -->

                        <Link
                            href="/login"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            aria-label="View demo">
                            Demo
                        </Link>
                    </section>
                </nav>
            </header>

            <!-- Main Content -->
            <main
                role="main"
                class="flex-1 transition-all duration-200"
                :class="['pt-16', isSidebarOpen ? 'md:ml-64 xl:mr-64' : 'md:ml-0 xl:mr-64']">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <article
                        class="py-8 prose prose-gray prose-headings:scroll-mt-20 max-w-none">
                        <slot />
                    </article>
                </div>
            </main>

            <Footer />
        </div>
    </div>
</template>

<style scoped>
aside div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

aside div::-webkit-scrollbar {
    width: 4px;
}

aside div::-webkit-scrollbar-track {
    background: transparent;
}

aside div::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 2px;
}

html {
    scroll-behavior: smooth;
}
</style>
