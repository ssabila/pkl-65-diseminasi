<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const safeUserName = computed(() =>
    user.value?.name ? String(user.value.name).replace(/[<>]/g, '') : ''
)
const primaryRole = computed(() => user.value?.roles?.[0] || '')

const menuOpen = ref(false)
const menuWrapper = ref(null)

const menuItems = [
    {
        type: 'link',
        label: 'Account & Password',
        icon: 'user',
        href: route('user.index')
    },
    { type: 'separator' },
    {
        type: 'link',
        label: 'Sign Out',
        icon: 'logout',
        href: route('logout'),
        method: 'post',
        as: 'button',
        class: 'text-red-500 hover:bg-red-50 cursor-pointer w-full',
        iconClass: 'text-red-400 group-hover:text-red-500'
    }
]

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
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleEscapeKey)
})

const icons = {
    user: '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
    logout: '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3h15" />'
}
</script>

<template>
    <nav ref="menuWrapper" class="relative">
        <button
            id="user-menu-button"
            type="button"
            class="relative flex items-center rounded-full text-sm focus:outline-none lg:rounded-md uppercase lg:p-1.5 lg:hover:bg-gray-100 cursor-pointer group"
            :aria-expanded="menuOpen.toString()"
            @click="toggleMenu">

            <span v-if="!avatarUrl" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs font-semibold text-white ring-2 ring-white">
                {{ safeUserName.charAt(0).toUpperCase() }}
            </span>

            <img
                v-else
                :src="avatarUrl"
                :alt="`${safeUserName}'s avatar`"
                class="size-5 rounded-full ring-2 ring-white" />

            <span
                class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap capitalize bg-gray-800 text-white">
                Profile
            </span>

            <svg
                class="ml-1 hidden h-3.5 w-3.5 text-gray-400 lg:block"
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
            class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-xl bg-white py-1.5 shadow-md ring-1 ring-gray-300 transition-all duration-200 ease-in-out transform"
            :class="menuOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">

            <li class="px-4 py-2 border-b border-gray-200 bg-gray-50">
                <div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ safeUserName }}
                    </div>
                    <p class="mt-1 inline-flex items-center rounded-md bg-gray-200 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-300 font-mono capitalize">
                        <svg
                            class="mr-1 h-3 w-3 text-gray-600"
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
                    class="my-1 mx-4 border-t border-gray-200" />

                <li v-else-if="item.type === 'link' && hasPermission(item.permissions?.[0])">
                    <Link
                        :href="item.href"
                        :method="item.method"
                        :as="item.as"
                        :target="item.target"
                        class="group flex items-center px-3.5 py-1.5 text-sm text-gray-700 hover:bg-gray-100"
                        :class="item.class"
                        @click="closeMenu">
                        <svg
                            class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-600"
                            v-html="icons[item.icon]" />
                        <span class="group-hover:text-blue-600">
                            {{ item.label }}
                        </span>
                    </Link>
                </li>
            </template>
        </menu>
    </nav>
</template>