<script setup>
import Navbar from '@/Components/Public/Navbar.vue';
import Footer from '@/Components/Public/Footer.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <Navbar />

        <main class="flex-grow w-full block">
            <slot />
        </main>

        <Footer />
    </div>
</template>
