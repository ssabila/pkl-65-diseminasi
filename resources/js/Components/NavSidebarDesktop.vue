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
    <aside data-sidebar-content class="nav-sidebar" @click.stop>
        <nav class="flex-1 overflow-y-auto py-2 px-2 bg-pkl-base-orange" aria-labelledby="nav-heading">
            <ul class="space-y-1">
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
                                        'nav-item',
                                        isCurrentRoute(item.route)
                                            ? 'nav-item-active'
                                            : 'nav-item-default'
                                    ]"
                                >
                                    <svg
                                        :class="[
                                            'nav-icon',
                                            isCurrentRoute(item.route)
                                                ? 'nav-icon-active'
                                                : 'nav-icon-default'
                                        ]"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                        v-html="item.icon"
                                    ></svg>
                                    <span class="text-sm font-medium">{{ item.name }}</span>
                                </Link>

                                <!-- Dropdown navigation item -->
                                <button
                                    v-else-if="item.children && hasVisibleChildren(item)"
                                    :class="[
                                        'nav-item w-full text-left cursor-pointer',
                                        isDropdownOpen(sectionIndex)
                                            ? 'nav-item-active'
                                            : 'nav-item-default'
                                    ]"
                                    @click="toggleDropdown(sectionIndex)"
                                >
                                    <svg
                                        :class="[
                                            'nav-icon',
                                            isDropdownOpen(sectionIndex)
                                                ? 'nav-icon-active'
                                                : 'nav-icon-default'
                                        ]"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                        v-html="item.icon"
                                    ></svg>
                                    <span class="text-sm font-medium flex-1">{{ item.name }}</span>
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
                                <ul class="ml-7 space-y-1">
                                    <li v-for="child in item.children" :key="child.name">
                                        <Link
                                            v-if="hasPermission(child.permission)"
                                            :href="route(child.route)"
                                            :class="[
                                                'nav-item pl-4',
                                                isCurrentRoute(child.route)
                                                    ? 'nav-item-active'
                                                    : 'nav-item-default'
                                            ]"
                                        >
                                            <span class="text-sm font-medium">
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
    </aside>
</template>
