<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()

const navigationSections = ref([
    {
        items: [
            {
                name: 'Dashboard',
                href: route('dashboard'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />'
            },
            {
                type: 'divider'
            }
        ]
    }
])

const isActive = href => {
    const path = new URL(href).pathname
    return page.url === path
}
</script>

<template>
    <aside
        data-sidebar-content
        class="h-full flex flex-col bg-[var(--color-surface)] shadow-lg transition-all duration-300 ease-in-out">
        <nav class="flex-1 overflow-y-auto py-2 px-2" aria-labelledby="nav-heading">
            <ul class="space-y-1">
                <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
                    <li v-if="sectionIndex > 0" class="my-1.5 px-2" role="separator">
                        <div class="h-px w-full bg-[var(--color-border)]"></div>
                    </li>
                    <template v-for="(item, itemIndex) in section.items" :key="itemIndex">
                        <li v-if="item.type === 'divider'" class="my-1.5 px-2" role="separator">
                            <div class="h-px w-full bg-[var(--color-border)]"></div>
                        </li>
                        <li v-else>
                            <Link
                                :href="item.href"
                                :class="{
                                    'flex items-center px-2.5 py-2 rounded-lg transition-all duration-200 ease-in-out': true,
                                    'text-teal-600 dark:text-teal-400': isActive(item.href),
                                    'text-[var(--color-text-muted)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-muted)]':
                                        !isActive(item.href)
                                }">
                                <svg
                                    class="w-[18px] h-[18px] mr-2.5 transition-colors duration-200"
                                    :class="{
                                        'text-teal-600 dark:text-teal-400': isActive(item.href),
                                        'text-[var(--color-text-muted)]': !isActive(item.href)
                                    }"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                    v-html="item.icon"></svg>
                                <span class="text-sm font-medium">{{ item.name }}</span>
                            </Link>
                        </li>
                    </template>
                </template>
            </ul>
        </nav>
    </aside>
</template>