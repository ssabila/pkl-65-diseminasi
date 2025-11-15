<script setup>
import { Head } from '@inertiajs/vue3';
import PKL65Layout from '@/Layouts/PKL65Layout.vue';

defineOptions({ layout: PKL65Layout });
</script>

<template>
    <Head title="Beranda" />

    <!-- SECTION 1 -->
    <section id="hero" class="bg-pkl-base-cream min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">HUSNA: HERO</h2>
    </section>

    <!-- SECTION 2 -->
    <section id="latar-belakang" class="bg-pkl-base-orange min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">HUSNA: LATAR BELAKANG</h2>
    </section>

    <!-- SECTION 3 -->
    <section id="tujuan" class="bg-pkl-base-cream min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">HUSNA: TUJUAN PENELITIAN</h2>
    </section>

    <!-- SECTION 4 -->
    <section id="riset" class="bg-pkl-base-orange min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">ARIS: RISET</h2>
    </section>

    <!-- SECTION 5 -->
    <section id="cakupan" class="bg-pkl-base-cream min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">ARIS: CAKUPAN PENELITIAN</h2>
    </section>

    <!-- SECTION 6 -->
    <section id="metode-sampel" class="bg-pkl-base-orange min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">ARIS: METODE PENARIKAN SAMPEL</h2>
    </section>

    <!-- SECTION 7 -->
    <section id="metode-data" class="bg-pkl-base-cream min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">SANCHA: METODE PENGUMPULAN DATA</h2>
    </section>

    <!-- SECTION 8 -->
    <section id="hasil" class="bg-pkl-base-cream min-h-screen py-24 px-6">
        <h2 class="text-3xl font-headline">SANCHA: HASIL PENELITIAN</h2>
    </section>
</template>
