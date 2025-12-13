<script setup>
import { Head } from '@inertiajs/vue3';
import PKL65Layout from '@/Layouts/PKL65Layout.vue';

// Import SideNav (Scroll Indicator)
import SideNav from '@/Components/Home/SideNav.vue';

// Husna :
import HeroSection from '@/Components/Home/HeroSection.vue';
import LatarBelakangSection from '@/Components/Home/LatarBelakangSection.vue';
import TujuanSection from '@/Components/Home/TujuanSection.vue';

// Aris:
import RisetSection from '@/Components/Home/RisetSection.vue';
import CakupanSection from '@/Components/Home/CakupanSection.vue';
import MetodeSampelSection from '@/Components/Home/MetodeSampelSection.vue';

// Sancha:
import MetodeDataSection from '@/Components/Home/MetodeDataSection.vue';
import HasilSection from '@/Components/Home/HasilSection.vue';

defineOptions({ layout: PKL65Layout });
</script>

<template>
   <Head title="Beranda" />

   <SideNav />

   <HeroSection id="hero" />
   <LatarBelakangSection id="latar-belakang" />
   <TujuanSection id="tujuan" />

   <RisetSection id="riset" />
   <CakupanSection id="cakupan" />
   <MetodeSampelSection id="metode-sampel" />

   <MetodeDataSection id="metode-data" />
   <HasilSection id="hasil" />
</template>