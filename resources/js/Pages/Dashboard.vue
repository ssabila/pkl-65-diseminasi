<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch, computed, nextTick } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import FormInput from '@/Components/FormInput.vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.heat'
import VueApexCharts from 'vue3-apexcharts'

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

// Watch for riset selection changes
watch(() => form.riset_id, async (newRisetId) => {
    topics.value = []
    form.topic_id = null
    
    if (newRisetId) {
        loadingTopics.value = true
        try {
            const response = await axios.get(route('dashboard.topics'), {
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

// Watch visualization type changes
watch(() => form.visualization_type_id, (newTypeId) => {
    const vizType = props.visualizationTypes.find(vt => vt.id === newTypeId)
    selectedVisualizationType.value = vizType ? vizType.type_code : null
    
    // Reset data when type changes
    chartCategories.value = []
    mapData.value = []
    mapFile.value = null
    showPreview.value = false
    
    // Initialize with one empty row for bar/pie
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
        const response = await axios.post(route('dashboard.upload-map'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        if (response.data.success) {
            mapData.value = response.data.data
            alert(`Berhasil memuat ${response.data.total_points} titik data`)
        }
    } catch (error) {
        console.error('Upload failed:', error)
        alert(error.response?.data?.message || 'Gagal mengupload file')
        mapData.value = []
    } finally {
        uploadingMap.value = false
    }
}

const handlePreview = () => {
    // Validation
    if (!form.riset_id || !form.topic_id || !form.visualization_type_id || !form.interpretation) {
        alert('Mohon lengkapi semua field yang wajib diisi')
        return
    }

    // Prepare chart data based on type
    if (isBarOrPie.value) {
        const validCategories = chartCategories.value.filter(c => c.category && c.value)
        if (validCategories.length === 0) {
            alert('Mohon isi minimal satu kategori dan nilai')
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
            alert('Mohon upload file data peta terlebih dahulu')
            return
        }
        form.chart_data = { points: mapData.value }
    }

    showPreview.value = true
    
    // Render preview after DOM update
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
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0)
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
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
                title: {
                    text: 'Nilai'
                },
                labels: {
                    formatter: function (val) {
                        return val.toFixed(0)
                    }
                }
            },
            colors: ['#008FFB'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    shadeIntensity: 0.25,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [50, 0, 100]
                },
            },
            tooltip: {
                enabled: true,
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val.toFixed(2)
                    }
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
                    show: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            labels: categories,
            colors: generateColors(values.length),
            legend: {
                position: 'bottom',
                fontSize: '14px',
                formatter: function(seriesName, opts) {
                    return seriesName + ": " + opts.w.globals.series[opts.seriesIndex]
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    const name = opts.w.globals.labels[opts.seriesIndex]
                    return [name, val.toFixed(1) + '%']
                },
                style: {
                    fontSize: '12px',
                }
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        labels: {
                            show: false
                        }
                    }
                }
            },
            tooltip: {
                enabled: true,
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val.toFixed(2)
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        }
    }
}

const renderMap = () => {
    const mapContainer = document.getElementById('previewMap')
    if (!mapContainer || mapData.value.length === 0) return

    // Clear existing map
    if (mapInstance.value) {
        mapInstance.value.remove()
    }
    mapContainer.innerHTML = ''

    // Calculate center
    const avgLat = mapData.value.reduce((sum, p) => sum + p.lat, 0) / mapData.value.length
    const avgLng = mapData.value.reduce((sum, p) => sum + p.lng, 0) / mapData.value.length

    // Create map
    mapInstance.value = L.map('previewMap').setView([avgLat, avgLng], 10)

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(mapInstance.value)

    // Prepare heatmap data
    const heatData = mapData.value.map(point => [
        point.lat,
        point.lng,
        point.density/100
    ])

    // Add heatmap layer
    L.heatLayer(heatData, {
        radius: 25,
        blur: 15,
        maxZoom: 17,
        max: 1.0,
        gradient: {
            0.0: 'blue',
            0.5: 'lime',
            0.7: 'yellow',
            1.0: 'red'
        }
    }).addTo(mapInstance.value)

    // Add markers for reference (optional)
    mapData.value.forEach(point => {
        L.circleMarker([point.lat, point.lng], {
            radius: 3,
            fillColor: '#ff7800',
            color: '#000',
            weight: 1,
            opacity: 0.5,
            fillOpacity: 0.5
        })
        .bindPopup(`Density: ${point.density}`)
        .addTo(mapInstance.value)
    })
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

const handlePublish = async () => {
    if (!confirm('Apakah Anda yakin ingin mempublikasikan visualisasi ini?')) {
        return
    }

    try {
        const response = await axios.post(route('dashboard.publish'), {
            riset_id: form.riset_id,
            topic_id: form.topic_id,
            visualization_type_id: form.visualization_type_id,
            interpretation: form.interpretation,
            chart_data: form.chart_data,
            chart_options: chartOptions.value,
        })

        if (response.data.success) {
            alert('Visualisasi berhasil dipublikasikan!')
            handleReset()
        }
    } catch (error) {
        console.error('Publish failed:', error)
        alert(error.response?.data?.message || 'Gagal mempublikasikan visualisasi')
    }
}
</script>

<template>
    <Head title="Input Data Riset" />

    <div class="min-h-screen bg-pkl-base-cream">
        <div class="max-w-6xl mx-auto sm:p-4">
            <h1 class="font-headline mt-3 text-[40px] tracking-wide">Kelola Diseminasi</h1>
            <p class="text-[16px]">Kelola Data dan Interpretasi untuk Diseminasi</p>
        </div>
        
        <main class="max-w-6xl mx-auto p-4 sm:p-4">
            <!-- Form Input -->
            <div class="bg-[var(--color-surface)] rounded-lg shadow-md p-6 border border-[var(--color-border)] mb-6">
                <div class="mb-6">
                    <h2 class="text-[20px] text-[var(--color-text)]">Form Input Data</h2>
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

                    <!-- Dynamic Form: Bar/Pie Chart -->
                    <div v-if="isBarOrPie" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Kategori</h3>
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
                                    v-if="chartCategories.length > 1"
                                    @click="removeCategory(index)"
                                    type="button"
                                    class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <button
                            @click="addCategory"
                            type="button"
                            class="mt-3 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                        >
                            + Tambah Kategori
                        </button>
                    </div>

                    <!-- Dynamic Form: Peta -->
                    <div v-if="isPeta" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Upload Data Peta</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    File Excel/CSV
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    accept=".csv,.xlsx,.xls"
                                    @change="handleMapFileChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Format: latitude, longitude, density
                                </p>
                            </div>
                            <div v-if="uploadingMap" class="text-sm text-gray-600">
                                Memproses file...
                            </div>
                            <div v-if="mapData.length > 0" class="text-sm text-green-600">
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
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition"
                        >
                            Reset
                        </button>
                        <button 
                            @click="handlePreview"
                            type="button"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                        >
                            Preview
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
                        @click="handlePublish"
                        type="button"
                        class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg"
                    >
                        📤 Publish
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Leaflet fixes */
:deep(.leaflet-container) {
    z-index: 1;
}

/* ApexCharts customization */
:deep(.apexcharts-canvas) {
    margin: 0 auto;
}

:deep(.apexcharts-menu) {
    background: #fff;
    border: 1px solid #ddd;
}
</style>