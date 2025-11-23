<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import FormInput from '@/Components/FormInput.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import NotificationDialog from '@/Components/NotificationDialog.vue'
import axios from 'axios'

defineOptions({
    layout: Default
})

const props = defineProps({
    risets: Array,
    visualizationTypes: Array,
})

const form = useForm({
    riset_id: null,
    topic_id: null,
    visualization_type_id: null,
    title: '',
    interpretation: '',
    chart_data: null,
    chart_options: null,
})

const topics = ref([])
const loadingTopics = ref(false)
const showPreview = ref(false)
const chartCategories = ref([])
const mapFile = ref(null)
const mapData = ref([])
const uploadingMap = ref(false)
const selectedVisualizationType = ref(null)
const chartOptions = ref({})
const chartSeries = ref([])
const mapInstance = ref(null)

// Dialog states
const showDeleteConfirm = ref(false)
const showPublishConfirm = ref(false)
const showSuccessNotif = ref(false)
const showErrorNotif = ref(false)
const notificationMessage = ref('')
const notificationTitle = ref('')

// Watch for riset selection changes
watch(() => form.riset_id, async (newRisetId) => {
    topics.value = []
    form.topic_id = null
    
    if (newRisetId) {
        loadingTopics.value = true
        try {
            const response = await axios.get(route('admin.dashboard.topics'), {
                params: { riset_id: newRisetId }
            })
            topics.value = response.data.topics
        } catch (error) {
            console.error('Failed to load topics:', error)
        } finally {
            loadingTopics.value = false
        }
    totalDiseminasi: Number,
    totalUpdated: Number,
    totalDeleted: Number,
    visualizations: {
        type: Array,
        default: () => []
    }
})

const handleDelete = async (visualizationId) => {
    if (!confirm('Apakah Anda yakin ingin menghapus visualisasi ini?')) return

    try {
        const response = await axios.post(route('admin.dashboard.upload-map'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        if (response.data.success) {
            mapData.value = response.data.data
            notificationTitle.value = 'Upload Berhasil!'
            notificationMessage.value = `Berhasil memuat ${response.data.total_points} titik data`
            showSuccessNotif.value = true
        }
    } catch (error) {
        console.error('Upload failed:', error)
        notificationTitle.value = 'Upload Gagal!'
        notificationMessage.value = error.response?.data?.message || 'Gagal mengupload file'
        showErrorNotif.value = true
        mapData.value = []
    } finally {
        uploadingMap.value = false
    }
}

const handlePreview = () => {
    // Validation
    if (!form.riset_id || !form.topic_id || !form.visualization_type_id || !form.title || !form.interpretation){
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon lengkapi semua field yang wajib diisi'
        showErrorNotif.value = true
        return
    }

    // Prepare chart data based on type
    if (isBarOrPie.value) {
        const validCategories = chartCategories.value.filter(c => c.category && c.value)
        if (validCategories.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu kategori dan nilai'
            showErrorNotif.value = true
            return
        }

        const categories = validCategories.map(c => c.category)
        const values = validCategories.map(c => parseFloat(c.value) || 0)

        form.chart_data = {
            labels: categories,
            datasets: [{
                data: values,
                backgroundColor: generateColors(values.length)
            }]
        }
        
        // Prepare ApexCharts data
        prepareApexChart(categories, values)
    } else if (isPeta.value) {
        if (mapData.value.length === 0) {
            notificationTitle.value = 'Data Peta Kosong'
            notificationMessage.value = 'Mohon upload file data peta terlebih dahulu'
            showErrorNotif.value = true
            return
        }
        form.chart_data = { points: mapData.value }
        await axios.delete(route('admin.dashboard.delete', visualizationId))
        alert('Visualisasi berhasil dihapus!')
        window.location.reload() // Reload halaman untuk update data, bisa diganti dengan Inertia.reload() jika pakai Inertia
    } catch (error) {
        console.error('Delete failed:', error)
        alert(error.response?.data?.message || 'Gagal menghapus visualisasi')
    }
}

const handleEdit = (visualizationId) => {
    window.location.href = route('admin.dashboard.edit', visualizationId)
}

const getIconColor = (index) => {
    const colors = ['#EF4444', '#10B981', '#F59E0B']
    return colors[index % colors.length]
}

const generateColors = (count) => {
    const colors = [
        '#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0',
        '#546E7A', '#26a69a', '#D10CE8', '#F9A3A4', '#90EE7E'
    ]
    return colors.slice(0, count)
}

const handleReset = () => {
    form.reset()
    topics.value = []
    chartCategories.value = []
    mapData.value = []
    mapFile.value = null
    showPreview.value = false
    selectedVisualizationType.value = null
    chartOptions.value = {}
    chartSeries.value = []
    
    if (mapInstance.value) {
        mapInstance.value.remove()
        mapInstance.value = null
    }
}

const handlePublishClick = () => {
    showPublishConfirm.value = true
}

const handlePublish = async () => {
    showPublishConfirm.value = false

    try {
        const response = await axios.post(route('admin.dashboard.publish'), {
            riset_id: form.riset_id,
            topic_id: form.topic_id,
            visualization_type_id: form.visualization_type_id,
            title: form.title,
            interpretation: form.interpretation,
            chart_data: form.chart_data,
            chart_options: chartOptions.value,
        })

        if (response.data.success) {
            notificationTitle.value = 'Update Berhasil!'
            notificationMessage.value = 'Visualisasi berhasil dipublikasikan!'
            showSuccessNotif.value = true
            handleReset()
        }
    } catch (error) {
        console.error('Publish failed:', error)
        notificationTitle.value = 'Publish Gagal!'
        notificationMessage.value = error.response?.data?.message || 'Gagal mempublikasikan visualisasi'
        showErrorNotif.value = true
    }
const getIconBgColor = (index) => {
    const bgColors = ['#FEE2E2', '#D1FAE5', '#FEF3C7']
    return bgColors[index % bgColors.length]
}
</script>

<template>
    <Head title="Input Data Riset" />

    <div class="min-h-screen bg-pkl-base-cream">
        <div class="max-w-6xl mx-auto sm:p-4">
<h1 class="font-headline mt-3 text-[40px] tracking-wide text-[#7A2509]">
    Kelola Diseminasi
</h1>            <p class="text-[16px]">Kelola Data dan Interpretasi untuk Diseminasi</p>
        </div>
        
        <main class="max-w-6xl mx-auto p-4 sm:p-4">
            <!-- Form Input -->
            <div class="bg-[var(--color-surface)] rounded-lg shadow-md border border-[var(--color-border)] mb-6 overflow-hidden">
                <div class="relative h-32 overflow-hidden">
                    <!-- Batik Pattern -->
                    <div class="absolute inset-0">
                        <img src="/images/assets/pattern-batik.svg" alt="Pattern Batik" class="w-full h-full object-cover" />
                    </div>
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
                
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-[20px] text-[#7A2509]">Form Input Data</h2>                  
                        <p class="text-[16px]">Kelola Data dan Interpretasi untuk Diseminasi</p>
                    </div>

                    <div class="space-y-6">
                    <!-- Riset -->
                    <FormSelect 
                        label="Pilih Riset" 
                        v-model="form.riset_id"
                        :options="risetOptions"
                        :error="form.errors.riset_id"
                        placeholder="-- Pilih Riset --"
                        required 
                    />

                    <!-- Sub Topik -->
                    <FormSelect 
                        label="Pilih Sub Topik" 
                        v-model="form.topic_id"
                        :options="topicOptions"
                        :error="form.errors.topic_id"
                        :disabled="!form.riset_id || loadingTopics"
                        :placeholder="loadingTopics ? 'Loading...' : '-- Pilih Sub Topik --'"
                        required 
                    />

                    <!-- Jenis Grafik -->
                    <FormSelect 
                        label="Pilih Jenis Grafik" 
                        v-model="form.visualization_type_id"
                        :options="visualizationTypeOptions"
                        :error="form.errors.visualization_type_id"
                        placeholder="-- Pilih Jenis Grafik --"
                        required 
                    />
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
                            <p class="text-sm text-gray-600 mb-1">
                                {{ viz.description }}
                            </p>
                            <p class="text-xs text-gray-400">
                                <span class="text-[#DC2626] font-medium">{{ viz.riset_name }}</span>
                                <span class="mx-2">·</span>
                                <span>{{ viz.date }}</span>
                                <span class="mx-2">·</span>
                                <span>{{ viz.file_size }}</span>
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
                                @click="handlePreview"
                                type="button"
                                class="px-4 py-2 bg-[#7A2509] hover:bg-[#5e1d07] text-white rounded-lg transition flex items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                <span>Preview</span>
                            </button>
                    </div>
                </div>
            </div>
        </div>

            <!-- Preview Section -->
            <div 
                v-if="showPreview"
                class="bg-[var(--color-surface)] rounded-lg shadow-md p-6 border border-[var(--color-border)]"
            >
                <div class="mb-6">
                    <h2 class="text-[20px] text-[var(--color-text)]">Preview Visualisasi</h2>
                    <h3 class="text-[24px] font-bold text-[var(--color-text)] mt-2">{{ form.title }}</h3>
                </div>

                        <button 
                            @click="handleDelete(viz.id)"
                            class="px-5 py-2.5 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Publish Button -->
                <div class="flex justify-end">
                    <button
                        @click="handlePublishClick"
                        type="button"
                        class="px-6 py-3 bg-[#7A2509] hover:bg-[#5e1d07] text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        <span>Konfirmasi & Publish</span>
                    </button>
                </div>
            </div>
        </main>

        <!-- Dialogs -->
        <ConfirmDialog
            :show="showPublishConfirm"
            message="Apakah anda yakin ingin mempublish hasil visualisasi berikut?"
            confirm-text="Publish Visualisasi"
            cancel-text="Cancel"
            confirm-color="blue"
            @confirm="handlePublish"
            @close="showPublishConfirm = false"
        />

        <NotificationDialog
            :show="showSuccessNotif"
            type="success"
            :title="notificationTitle"
            :message="notificationMessage"
            button-text="OK"
            @close="showSuccessNotif = false"
        />

        <NotificationDialog
            :show="showErrorNotif"
            type="error"
            :title="notificationTitle"
            :message="notificationMessage"
            button-text="OK"
            @close="showErrorNotif = false"
        />
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