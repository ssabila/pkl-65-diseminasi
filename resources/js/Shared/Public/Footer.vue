<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const version = ref('v1.0.0')
const personalisation = page.props.personalisation || {}

onMounted(async () => {
    try {
        const response = await fetch(
            'https://api.github.com/repos/otatechie/guacpanel-tailwind/releases/latest'
        )
        const data = await response.json()
        if (data.tag_name) {
            version.value = data.tag_name
        }
    } catch (error) {
        version.value = 'v1.0.0'
    }
})
</script>

<template>
    <footer
        class="bg-[var(--color-surface-muted)] border-t border-[var(--color-border)] text-[var(--color-text-muted)]">
        <div class="mx-auto px-2 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2 text-xs">
                    <p>Web Diseminasi</p>
                    <span class="text-[var(--color-text-muted)]">{{ version }}</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                </div>
            </div>
        </div>
    </footer>
</template>
