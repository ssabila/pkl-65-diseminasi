<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import axios from 'axios'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import StatusDialog from '@/Components/StatusDialog.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    totalDiseminasi: { type: Number, default: 0 },
    totalUpdated: { type: Number, default: 0 },
    totalDeleted: { type: Number, default: 0 },
    visualizations: {
        type: Array,
        default: () => []
    }
})

const confirmState = ref({
    show: false,
    title: '',
    message: '',
    variant: 'danger',
    confirmText: '',
    targetId: null
})

const statusState = ref({
    show: false,
    title: '',
    message: '',
    variant: 'success'
})

const isProcessing = ref(false)

const requestDelete = (visualization) => {
    confirmState.value = {
        show: true,
        title: 'Hapus Visualisasi',
        message: `Apakah Anda yakin ingin menghapus visualisasi "${visualization.title}"?`,
        variant: 'danger',
        confirmText: 'Hapus Visualisasi',
        targetId: visualization.id
    }
}

const performDelete = async () => {
    if (!confirmState.value.targetId || isProcessing.value) return

    isProcessing.value = true
    try {
        await axios.delete(route('admin.dashboard.delete', confirmState.value.targetId))
        statusState.value = {
            show: true,
            title: 'Hapus Berhasil!',
            message: 'Visualisasi berhasil dihapus.',
            variant: 'success'
        }
        router.reload({ only: ['visualizations', 'totalDiseminasi', 'totalDeleted'] })
    } catch (error) {
        statusState.value = {
            show: true,
            title: 'Gagal Menghapus',
            message: error.response?.data?.message || 'Terjadi kesalahan pada server.',
            variant: 'danger'
        }
    } finally {
        confirmState.value.show = false
        isProcessing.value = false
    }
}

const handleEdit = (visualizationId) => {
    router.visit(route('admin.dashboard.edit', visualizationId))
}

const getIconColor = (index) => {
    const colors = ['#EF4444', '#10B981', '#F59E0B']
    return colors[index % colors.length]
}

const getIconBgColor = (index) => {
    const bgColors = ['#FEE2E2', '#D1FAE5', '#FEF3C7']
    return bgColors[index % bgColors.length]
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="min-h-screen bg-[#FFF5F0]">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-[#7A2509] mb-2">
                    Dashboard Admin
                </h1>            
                <p class="text-gray-600">Riwayat diseminasi yang telah dibuat admin</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="text-gray-500 text-sm mb-2">Total Diseminasi</div>
                    <div class="text-4xl font-bold text-gray-900">{{ totalDiseminasi }}</div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="text-gray-500 text-sm mb-2">Diseminasi Diperbarui</div>
                    <div class="text-4xl font-bold text-[#F97316]">{{ totalUpdated }}</div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="text-gray-500 text-sm mb-2">Diseminasi Dihapus</div>
                    <div class="text-4xl font-bold text-[#EAB308]">{{ totalDeleted }}</div>
                </div>
            </div>

            <!-- Visualizations List -->
            <div class="space-y-4">
                <div 
                    v-for="(viz, index) in visualizations"
                    :key="viz.id"
                    class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200 flex justify-between items-center"
                >
                    <div class="flex items-center gap-5">
                        <!-- Icon -->
                        <div 
                            class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                            :style="{ backgroundColor: getIconBgColor(index) }"
                        >
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke-width="2" 
                                class="w-7 h-7"
                                :style="{ stroke: getIconColor(index) }"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>

                        <!-- Info -->
                        <div>
                            <h3 class="font-semibold text-[#7A2509] text-lg mb-1">
                                {{ viz.title }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">
                                {{ viz.topic_name }} &middot; {{ viz.type_name }}
                            </p>
                            <p class="text-sm text-gray-500 mb-1 line-clamp-2">
                                {{ viz.interpretation }}
                            </p>
                            <p class="text-xs text-gray-400">
                                <span class="text-[#DC2626] font-medium">{{ viz.riset_name }}</span>
                                <span class="mx-2">·</span>
                                <span>{{ viz.updated_at }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 flex-shrink-0">
                        <button 
                            @click="handleEdit(viz.id)"
                            class="px-5 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 flex items-center gap-2 font-medium"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit
                        </button>

                        <button 
                            @click="requestDelete(viz)"
                            class="px-5 py-2.5 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow"
                        >
                            Hapus
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div 
                    v-if="visualizations.length === 0"
                    class="bg-white rounded-xl p-12 text-center shadow-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="text-gray-500 text-lg">Belum ada visualisasi yang dibuat</p>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="confirmState.show"
            :title="confirmState.title"
            :message="confirmState.message"
            :confirm-text="confirmState.confirmText"
            cancel-text="Batal"
            :variant="confirmState.variant"
            @cancel="confirmState.show = false"
            @confirm="performDelete" />

        <StatusDialog
            :show="statusState.show"
            :title="statusState.title"
            :message="statusState.message"
            :variant="statusState.variant"
            @close="statusState.show = false" />
    </div>
</template>

<style scoped>
/* Smooth transitions */
button {
    transition: all 0.2s ease-in-out;
}

button:active {
    transform: scale(0.98);
}
</style>