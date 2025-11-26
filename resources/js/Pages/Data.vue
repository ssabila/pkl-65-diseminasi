<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch, computed, nextTick } from 'vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import FormInput from '@/Components/FormInput.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import NotificationDialog from '@/Components/NotificationDialog.vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.heat'
import VueApexCharts from 'vue3-apexcharts'

defineOptions({ layout: Default })

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
    }
})

watch(() => form.visualization_type_id, (newTypeId) => {
    const vizType = props.visualizationTypes.find(vt => vt.id === newTypeId)
    selectedVisualizationType.value = vizType ? vizType.type_code : null
    
    chartCategories.value = []
    mapData.value = []
    mapFile.value = null
    showPreview.value = false
    
    if (['bar', 'pie'].includes(selectedVisualizationType.value)) {
        addCategory()
    }
})

const risetOptions = props.risets.map(r => ({
    value: r.id,
    label: r.name
}))

const topicOptions = computed(() => {
    return topics.value.map(t => ({
        value: t.id,
        label: t.name
    }))
})

const visualizationTypeOptions = props.visualizationTypes.map(vt => ({
    value: vt.id,
    label: vt.type_name
}))

const isBarOrPie = computed(() => {
    return ['bar', 'pie'].includes(selectedVisualizationType.value)
})

const isPeta = computed(() => {
    return selectedVisualizationType.value === 'peta'
})

const addCategory = () => {
    chartCategories.value.push({ category: '', value: '' })
}

const removeCategory = (index) => {
    chartCategories.value.splice(index, 1)
}

const handleMapFileChange = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    uploadingMap.value = true
    const formData = new FormData()
    formData.append('file', file)

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
        
        prepareApexChart(categories, values)
    } else if (isPeta.value) {
        if (mapData.value.length === 0) {
            notificationTitle.value = 'Data Peta Kosong'
            notificationMessage.value = 'Mohon upload file data peta terlebih dahulu'
            showErrorNotif.value = true
            return
        }
        form.chart_data = { points: mapData.value }
    }

    showPreview.value = true
    
    nextTick(() => {
        if (isPeta.value) {
            renderMap()
        }
    })
}

const prepareApexChart = (categories, values) => {
    if (selectedVisualizationType.value === 'bar') {
        chartSeries.value = [{
            name: 'Nilai',
            data: values
        }]
        
        chartOptions.value = {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    dataLabels: {
                        position: 'top',
                    },
                    horizontal: false,
                    columnWidth: '50%',
                }
            },
            colors: ['#F97316', '#FB923C'],
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ['#304758']
                }
            },
            xaxis: {
                categories: categories,
                position: 'bottom',
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            title: {
                text: form.title,
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 600,
                }
            }
        }
    } else if (selectedVisualizationType.value === 'pie') {
        chartSeries.value = values
        
        chartOptions.value = {
            chart: {
                type: 'pie',
                height: 400,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                    }
                }
            },
            labels: categories,
            colors: generateColors(values.length),
            legend: {
                position: 'bottom',
                fontSize: '12px'
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px'
                }
            },
            title: {
                text: form.title,
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 600,
                }
            }
        }
    }
}

const generateColors = (count) => {
    const baseColors = [
        '#F97316', '#FB923C', '#FDBA74', '#FED7AA',
        '#EA580C', '#C2410C', '#9A3412', '#7C2D12'
    ]
    return Array.from({ length: count }, (_, i) => baseColors[i % baseColors.length])
}

const renderMap = () => {
    nextTick(() => {
        const mapElement = document.getElementById('previewMap')
        if (!mapElement) return

        if (mapInstance.value) {
            mapInstance.value.remove()
            mapInstance.value = null
        }

        const map = L.map('previewMap').setView([-2.5, 118], 5)
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map)

        if (mapData.value.length > 0) {
            const points = mapData.value.map(point => [
                point.latitude,
                point.longitude,
                point.density || 1
            ])
            
            L.heatLayer(points, {
                radius: 25,
                blur: 15,
                maxZoom: 10,
            }).addTo(map)
        }

        mapInstance.value = map
    })
}

const handleReset = () => {
    form.reset()
    chartCategories.value = []
    mapData.value = []
    mapFile.value = null
    showPreview.value = false
    selectedVisualizationType.value = null
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
            notificationTitle.value = 'Publikasi Berhasil!'
            notificationMessage.value = 'Visualisasi berhasil dipublikasikan dan sekarang dapat dilihat di halaman hasil riset publik!'
            showSuccessNotif.value = true
            handleReset()
            
            // Optional: Auto-open public dashboard in new tab to show result
            // setTimeout(() => {
            //     window.open(`/hasil-riset?topic_id=${form.topic_id}`, '_blank');
            // }, 2000);
        }
    } catch (error) {
        console.error('Publish failed:', error)
        notificationTitle.value = 'Publish Gagal!'
        notificationMessage.value = error.response?.data?.message || 'Gagal mempublikasikan visualisasi'
        showErrorNotif.value = true
    }
}
</script>

<template>
    <div>
        <Head title="Kelola Data Diseminasi" />

        <main class="max-w-7xl mx-auto">
            <div class="container-border">
                <!-- Page Header -->
                <PageHeader
                    title="Kelola Diseminasi"
                    description="Kelola Data dan Interpretasi untuk Diseminasi"
                    :breadcrumbs="[

                        { label: 'Dashboard', href: route('dashboard') },
                        { label: 'Data' }
                    ]"
                />

                <div class="p-6 bg-[var(--color-bg)]">
                    <!-- Batik Pattern Header -->
                    <div class="mb-8 relative h-32 rounded-xl overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600">
                        <div class="absolute inset-0 opacity-40">
                            <img 
                                src="/images/assets/pattern-batik.svg" 
                                alt="Batik Pattern"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div v-if="!showPreview" class="bg-[var(--color-surface)] rounded-xl shadow-sm border border-[var(--color-border)] p-8">
                        <!-- Form Title -->
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-[var(--color-text)] mb-1">Form Input Data</h2>
                            <p class="text-sm text-[var(--color-text-muted)]">Kelola Data dan Interpretasi untuk Diseminasi</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Pilih Riset -->
                            <FormSelect 
                                label="Pilih Riset" 
                                v-model="form.riset_id"
                                :options="risetOptions"
                                :error="form.errors.riset_id"
                                placeholder="Pilih Riset"
                                required 
                            />

                            <!-- Pilih Topik -->
                            <FormSelect 
                                label="Pilih Topik" 
                                v-model="form.topic_id"
                                :options="topicOptions"
                                :error="form.errors.topic_id"
                                placeholder="Pilih Topik"
                                :disabled="!form.riset_id || loadingTopics"
                                required 
                            />

                            <!-- Pilih Jenis Visualisasi -->
                            <FormSelect 
                                label="Jenis Visualisasi" 
                                v-model="form.visualization_type_id"
                                :options="visualizationTypeOptions"
                                :error="form.errors.visualization_type_id"
                                placeholder="Pilih Jenis Visualisasi"
                                required 
                            />

                            <!-- Judul Visualisasi -->
                            <FormInput 
                                label="Judul Visualisasi" 
                                v-model="form.title"
                                :error="form.errors.title"
                                placeholder="Masukkan judul visualisasi"
                                required 
                            />

                            <!-- Dynamic Form: Bar/Pie Chart -->
                            <div v-if="isBarOrPie" class="border-t border-[var(--color-border)] pt-6">
                                <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Input Data Kategori</h3>
                                <div class="space-y-3">
                                    <div 
                                        v-for="(category, index) in chartCategories" 
                                        :key="index"
                                        class="flex gap-3 items-end"
                                    >
                                        <div class="flex-1">
                                            <FormInput
                                                :label="index === 0 ? 'Kategori' : ''"
                                                v-model="category.category"
                                                placeholder="Nama kategori"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <FormInput
                                                :label="index === 0 ? 'Nilai' : ''"
                                                v-model="category.value"
                                                type="number"
                                                step="0.01"
                                                placeholder="Nilai"
                                            />
                                        </div>
                                        <button
                                            v-if="index === chartCategories.length - 1"
                                            @click="addCategory"
                                            type="button"
                                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                                        >
                                            + Tambah
                                        </button>
                                        <button
                                            v-if="chartCategories.length > 1"
                                            @click="removeCategory(index)"
                                            type="button"
                                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Form: Peta -->
                            <div v-if="isPeta" class="border-t border-[var(--color-border)] pt-6">
                                <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Upload Data Peta</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                            File Excel/CSV
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="file"
                                            accept=".csv,.xlsx,.xls"
                                            @change="handleMapFileChange"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                        />
                                        <p class="mt-2 text-sm text-[var(--color-text-muted)]">
                                            Format: latitude, longitude, density
                                        </p>
                                    </div>
                                    <div v-if="uploadingMap" class="text-sm text-[var(--color-text-muted)]">
                                        Memproses file...
                                    </div>
                                    <div v-if="mapData.length > 0" class="text-sm text-green-600 font-medium">
                                        ✓ {{ mapData.length }} titik data berhasil dimuat
                                    </div>
                                </div>
                            </div>

                            <!-- Interpretasi -->
                            <FormTextarea
                                label="Interpretasi"
                                v-model="form.interpretation"
                                :error="form.errors.interpretation"
                                placeholder="Masukkan interpretasi visualisasi"
                                :rows="4"
                                required
                            />

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-3 pt-4">
                                <button 
                                    @click="handleReset"
                                    type="button"
                                    class="px-4 py-2 bg-[#FFFBDF] border border-[#7A2509] text-[#7A2509] rounded-lg transition hover:bg-[#7A2509] hover:text-white"
                                >
                                    Reset
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

                    <!-- Preview Section -->
                    <div 
                        v-if="showPreview"
                        class="bg-[var(--color-surface)] rounded-lg shadow-md p-6 border border-[var(--color-border)]"
                    >
                        <div class="mb-6">
                            <h2 class="text-[20px] text-[var(--color-text)]">Preview Visualisasi</h2>
                            <h3 class="text-[24px] font-bold text-[var(--color-text)] mt-2">{{ form.title }}</h3>
                        </div>

                        <!-- ApexChart Preview (Bar/Pie) -->
                        <div v-if="isBarOrPie" class="mb-6 bg-white rounded-lg p-4">
                            <VueApexCharts 
                                :options="chartOptions" 
                                :series="chartSeries"
                                :type="selectedVisualizationType"
                                height="400"
                            />
                        </div>

                        <!-- Map Preview -->
                        <div v-if="isPeta" class="mb-6">
                            <div 
                                id="previewMap" 
                                class="w-full h-[500px] border rounded-lg"
                            ></div>
                        </div>

                        <!-- Interpretation -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Interpretasi</h3>
                            <div class="p-4 bg-gray-50 rounded-lg text-justify leading-relaxed">
                                {{ form.interpretation }}
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
    </div>
</template>

<style scoped>
:deep(.leaflet-container) {
    z-index: 1;
}

:deep(.apexcharts-canvas) {
    margin: 0 auto;
}

:deep(.apexcharts-menu) {
    background: #fff;
    border: 1px solid #ddd;
}
</style>