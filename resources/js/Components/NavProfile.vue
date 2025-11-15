<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { cycleTheme, getCurrentThemeState, getEffectiveTheme } from '@/darkMode'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const safeUserName = computed(() =>
    user.value?.name ? String(user.value.name).replace(/[<>]/g, '') : ''
)
const primaryRole = computed(() => user.value?.roles?.[0] || '')

const menuOpen = ref(false)
const menuWrapper = ref(null)
const isDark = ref(getEffectiveTheme() === 'dark')
const themeState = ref(getCurrentThemeState())

// --- INI BAGIAN YANG DIPERBAIKI ---
// Saya telah menghapus semua rute yang rusak (two.factor, session.index, setting.index, etc.)
// Kita hanya menyisakan 'user.index' (untuk Edit Profile) dan 'logout'
const menuItems = [
    {
        type: 'theme',
        label: computed(() => `${themeState.value.nextThemeText}`),
        icon: computed(() => themeState.value.nextThemeIcon),
        action: () => {
            const result = cycleTheme()
            themeState.value = getCurrentThemeState()
            isDark.value = result.effectiveTheme === 'dark'
            closeMenu()
        }
    },
    { type: 'separator' },

    {
        type: 'link',
        label: 'Account & Password', // Ini adalah link "Edit Profile" Anda
        icon: 'user',
        href: route('user.index') // KITA AKAN BUAT RUTE INI ADA LAGI
    },
    // Rute 'Two-Factor' dan 'Browser Sessions' telah dihapus

    { type: 'separator' },
    // Rute 'System Settings' dan 'Help' telah dihapus

    {
        type: 'link',
        label: 'Sign Out',
        icon: 'logout',
        href: route('logout'),
        method: 'post',
        as: 'button',
        class: 'text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer w-full',
        iconClass: 'text-red-400 group-hover:text-red-500'
    }
]
// --- AKHIR BAGIAN YANG DIPERBAIKI ---

const handleClickOutside = event => {
    if (
        menuWrapper.value &&
        !menuWrapper.value.contains(event.target) &&
        event.target.id !== 'user-menu-button'
    ) {
        menuOpen.value = false
    }
}

const handleEscapeKey = event => {
    if (event.key === 'Escape') menuOpen.value = false
}

const toggleMenu = () => (menuOpen.value = !menuOpen.value)
const closeMenu = () => (menuOpen.value = false)

const hasPermission = permissionName =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleEscapeKey)

    const handleThemeChange = event => {
        themeState.value = getCurrentThemeState()
        isDark.value = event.detail.effectiveTheme === 'dark'
    }

    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'class') isDark.value = getEffectiveTheme() === 'dark'
        })
    })

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    })

    window.addEventListener('themeChanged', handleThemeChange)

    onBeforeUnmount(() => {
        observer.disconnect()
        document.removeEventListener('click', handleClickOutside)
        document.removeEventListener('keydown', handleEscapeKey)
        window.removeEventListener('themeChanged', handleThemeChange)
    })
})

const icons = {
    user: '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
    logout: '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3h15" />',
    sun: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />',
    moon: '<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />',
    system: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />',
    check: '<path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />',
}
</script>

<template>
    <nav ref="menuWrapper" class="relative">
        <button
            id="user-menu-button"
            type="button"
            class="relative flex items-center rounded-full text-sm focus:outline-none lg:rounded-md uppercase lg:p-1.5 lg:hover:bg-[var(--color-surface-muted)] cursor-pointer group"
            :aria-expanded="menuOpen.toString()"
            @click="toggleMenu">
            
            <!-- Fallback Inisial jika avatarUrl tidak ada -->
            <span v-if="!avatarUrl" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs font-semibold text-white ring-2 ring-white dark:ring-gray-800">
                {{ safeUserName.charAt(0).toUpperCase() }}
            </span>
            <!-- Tampilkan Avatar jika ada -->
            <img
                v-else
                :src="avatarUrl"
                :alt="`${safeUserName}'s avatar`"
                class="size-5 rounded-full ring-2 ring-white dark:ring-gray-800" />

            <span
                class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap capitalize"
                :style="{ backgroundColor: 'var(--color-text)', color: 'var(--color-bg)' }"
                >Profile</span>
            <svg
                class="ml-1 hidden h-3.5 w-3.5 text-gray-400 dark:text-gray-400 lg:block"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <menu
            v-show="menuOpen"
            class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-xl bg-white dark:bg-gray-800 py-1.5 shadow-md ring-1 ring-gray-300 dark:ring-gray-700 ring-opacity-5 transition-all duration-200 ease-in-out transform"
            :class="menuOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
            <li class="px-4 py-2 border-b border-[var(--color-border)] bg-[var(--color-surface)]">
                <div>
                    <div class="text-sm font-medium text-[var(--color-text)]">
                        {{ safeUserName }}
                    </div>
                    <p
                        class="mt-1 inline-flex items-center rounded-md bg-[var(--color-surface-muted)] px-2 py-1 text-xs font-medium text-[var(--color-text-muted)] ring-1 ring-inset ring-[var(--color-border)] font-mono capitalize">
                        <svg
                            class="mr-1 h-3 w-3 text-[var(--color-text-muted)]"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            v-html="icons.check" />
                        {{ primaryRole }}
                    </p>
                </div>
            </li>

            <template v-for="(item, index) in menuItems" :key="index">
                <li
                    v-if="item.type === 'separator'"
                    role="separator"
                    class="my-1 mx-4 border-t border-[var(--color-border)]" />

                <li v-else-if="item.type === 'theme'">
                    <button
                        class="group flex w-full items-center px-3.5 py-1.5 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] cursor-pointer"
                        role="menuitem"
                        @click="item.action">
                        <svg
                            class="mr-3 h-5 w-5 text-[var(--color-text-muted)] group-hover:text-[var(--primary-color)]"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            v-html="icons[item.icon.value]" />
                        <span class="group-hover:text-[var(--primary-color)]">{{ item.label }}</span>
                    </button>
                </li>

                <li v-else-if="item.type === 'link' && hasPermission(item.permissions?.[0])">
                    <Link
                        :href="item.href"
                        :method="item.method"
                        :as="item.as"
                        :target="item.target"
                        class="group flex items-center px-3.5 py-1.5 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface-muted)]"
                        :class="item.class"
                        @click="closeMenu">
                        <svg
                            class="mr-3 h-5 w-5 text-[var(--color-text-muted)]"
                            :class="[item.label === 'Sign out' ? '' : 'group-hover:text-[var(--primary-color)]', item.iconClass]"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            v-html="icons[item.icon]" />
                        <span :class="item.label === 'Sign out' ? '' : 'group-hover:text-[var(--primary-color)]'">
                            {{ item.label }}
                        </span>
                    </Link>
                </li>
            </template>
        </menu>
    </nav>
</template>