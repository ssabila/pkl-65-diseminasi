<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import { computed } from 'vue'

const page = usePage()
const personalisation = page.props.personalisation || {}
const hasCustomBranding = computed(() => personalisation.appLogo)
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow bg-[var(--color-bg)] py-0">
            <FlashMessage />
            <article>
                <slot></slot>
            </article>
        </main>
    </div>
</template>
