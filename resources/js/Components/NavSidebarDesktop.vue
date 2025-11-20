<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, computed, ref } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const openDropdowns = ref(new Set())

const toggleDropdown = (sectionIndex) => {
    if (openDropdowns.value.has(sectionIndex)) {
        openDropdowns.value.delete(sectionIndex)
    } else {
        openDropdowns.value.add(sectionIndex)
    }
}

const isDropdownOpen = (sectionIndex) => {
    return openDropdowns.value.has(sectionIndex)
}

const hasPermission = (permissionName) =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

const isCurrentRoute = (routeName) => {
    if (!routeName) return false

    const currentUrl = page.url.value
    const routeUrl = route(routeName)

    return currentUrl === routeUrl || route().current(routeName)
}

const hasVisibleChildren = (item) => {
    if (!item?.children?.length) return false
    return item.children.some((child) => hasPermission(child.permission))
}

const sectionHasVisibleItems = (section) => {
    if (!section?.items?.length) return false
    return section.items.some(
        (item) =>
            item.type !== 'divider' &&
            (hasPermission(item.permission) ||
                (item.children && hasVisibleChildren(item)))
    )
}

const navigationSections = reactive([
    {
        items: [
            {
                name: 'Data',
                route: 'admin.data',
                icon: `<path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13.125C3 12.504 3.504 12 4.125
                    12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5
                    20.496 6.996 21 6.375 21h-2.25A1.125
                    1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504
                    -1.125 1.125-1.125h2.25c.621 0 1.125.504
                    1.125 1.125v11.25c0 .621-.504 1.125-1.125
                    1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5
                    4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496
                    3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125
                    1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />`
            },
        ]
    },

    {
        items: [
            {
                name: 'Dashboard',
                route: 'dashboard',
                icon: `<path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955c.44-.439 1.152-.439
                    1.591 0L21.75 12M4.5 9.75v10.125c0
                    .621.504 1.125 1.125 1.125H9.75v-4.875c0
                    -.621.504-1.125 1.125-1.125h2.25c.621 0
                    1.125.504 1.125 1.125V21h4.125c.621 0
                    1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />`
            },
        ]
    },

    {
        items: [
            {
                name: 'Profile',
                route: 'user.index',
                icon: `<path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0 0 12
                    15.75a7.488 7.488 0 0 0-5.982
                    2.975m11.963 0a9 9 0 1 0-11.963
                    0m11.963 0A8.966 8.966 0 0 1 12
                    21a8.966 8.966 0 0 1-5.982-2.275M15
                    9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />`
            },
        ]
    }
])
</script>

<template>
    <aside data-sidebar-content class="nav-sidebar flex flex-col h-full" @click.stop>
        <!-- Header Section -->
        <div class="bg-pkl-base-orange px-4 py-6 border-b border-orange-400">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <img src="/images/assets/LOGO-PKL_REV8.png" class="w-10 h-10 object-contain" alt="Logo PKL 65">
                </div>
                <div class="flex-1">
                    <h2 class="text-white font-semibold text-base leading-tight">Admin Panel PKL 65</h2>
                    <p class="text-orange-100 text-xs">Tahun Ajaran 2025/2026</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 bg-pkl-base-orange" aria-labelledby="nav-heading">
            <ul class="space-y-2">
                <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
                    <template v-if="sectionHasVisibleItems(section)">
                        <template v-for="(item, itemIndex) in section.items" :key="itemIndex">
                            <li v-if="item.type === 'divider'" class="my-1.5 px-2" role="separator">
                                <div class="nav-divider"></div>
                            </li>

                            <li v-else>
                                <!-- Regular navigation item -->
                                <Link
                                    v-if="!item.children && hasPermission(item.permission) && item.route"
                                    :href="route(item.route)"
                                    :class="[
                                        'flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-150',
                                        isCurrentRoute(item.route)
                                            ? 'bg-[#F4E4A6] text-[#7A2509] font-medium shadow-sm'
                                            : 'text-white hover:bg-orange-500/50'
                                    ]"
                                >
                                    <svg
                                        class="w-5 h-5 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                        v-html="item.icon"
                                    ></svg>
                                    <span class="text-sm">{{ item.name }}</span>
                                </Link>

                                <!-- Dropdown navigation item -->
                                <button
                                    v-else-if="item.children && hasVisibleChildren(item)"
                                    :class="[
                                        'flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-150 w-full text-left',
                                        isDropdownOpen(sectionIndex)
                                            ? 'bg-[#F4E4A6] text-[#7A2509] font-medium shadow-sm'
                                            : 'text-white hover:bg-orange-500/50'
                                    ]"
                                    @click="toggleDropdown(sectionIndex)"
                                >
                                    <svg
                                        class="w-5 h-5 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                        v-html="item.icon"
                                    ></svg>
                                    <span class="text-sm flex-1">{{ item.name }}</span>
                                    <svg
                                        :class="[
                                            'w-4 h-4 transition-transform duration-200',
                                            isDropdownOpen(sectionIndex) ? 'rotate-180' : ''
                                        ]"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                        />
                                    </svg>
                                </button>
                            </li>

                            <!-- Dropdown children -->
                            <li
                                v-if="item.children && isDropdownOpen(sectionIndex) && hasVisibleChildren(item)"
                            >
                                <ul class="ml-7 mt-1 space-y-1">
                                    <li v-for="child in item.children" :key="child.name">
                                        <Link
                                            v-if="hasPermission(child.permission)"
                                            :href="route(child.route)"
                                            :class="[
                                                'flex items-center px-4 py-2 rounded-lg transition-colors duration-150',
                                                isCurrentRoute(child.route)
                                                    ? 'bg-[#F4E4A6] text-[#7A2509] font-medium shadow-sm'
                                                    : 'text-white hover:bg-orange-500/50'
                                            ]"
                                        >
                                            <span class="text-sm">
                                                {{ child.name }}
                                            </span>
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                    </template>
                </template>
            </ul>
        </nav>

        <!-- Logout Button at Bottom -->
        <div class="bg-pkl-base-orange px-3 py-4 border-t border-orange-400">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-orange-500/50 transition-colors duration-150 w-full"
            >
                <svg
                    class="w-5 h-5 flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
                    />
                </svg>
                <span class="text-sm">Logout</span>
            </Link>
        </div>
    </aside>
</template>
