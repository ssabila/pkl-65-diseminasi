<script setup>
import { ref } from "vue";
import HistoryTable from "@/Components/HistoryTable.vue";

const props = defineProps({
    rows: {
        type: Array,
        default: () => []
    }
});

const rows = ref(props.rows);
const loading = ref(false);
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Riwayat Aktivitas</h1>

    <HistoryTable :rows="rows" :loading="loading" />
  </div>
</template>
