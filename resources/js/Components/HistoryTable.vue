<template>
  <div>
    <!-- LOADING STATE -->
    <div v-if="loading" class="p-6 text-center">
      <div class="animate-pulse text-gray-500">Loading data...</div>
    </div>

    <!-- EMPTY STATE -->
    <div v-else-if="rows.length === 0" class="p-6 text-center text-gray-500">
      Tidak ada data history.
    </div>

    <!-- DESKTOP TABLE -->
    <div v-else class="hidden md:block">
      <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="text-left px-4 py-3 border-r">#</th>
            <th class="text-left px-4 py-3 border-r">User</th>
            <th class="text-left px-4 py-3 border-r">Aksi</th>
            <th class="text-left px-4 py-3 border-r">Tanggal</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, i) in rows"
            :key="row.id"
            class="border-b hover:bg-gray-50"
          >
            <td class="px-4 py-3 border-r">{{ i + 1 }}</td>
            <td class="px-4 py-3 border-r">{{ row.user_name }}</td>
            <td class="px-4 py-3 border-r">{{ row.action }}</td>
            <td class="px-4 py-3 border-r">{{ formatDate(row.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE CARD LIST -->
    <div v-else class="md:hidden flex flex-col gap-3">
      <div
        v-for="row in rows"
        :key="row.id"
        class="border rounded-xl p-4 shadow-sm"
      >
        <div class="font-semibold text-blue-700">{{ row.user_name }}</div>
        <div class="text-gray-600 text-sm mt-1">{{ row.action }}</div>
        <div class="text-gray-400 text-xs mt-2">{{ formatDate(row.created_at) }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    rows: Array,
    loading: Boolean,
  },

  emits: ["load-more"],

  methods: {
    formatDate(date) {
      if (!date) return "-";
      return new Date(date).toLocaleString("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
      });
    },
  },
};
</script>

<style scoped>
/* style kecil jika butuh */
</style>
