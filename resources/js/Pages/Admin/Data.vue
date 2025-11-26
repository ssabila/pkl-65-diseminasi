<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch, computed, nextTick, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import FormInput from '@/Components/FormInput.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import NotificationDialog from '@/Components/NotificationDialog.vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.heat'
import VueApexCharts from 'vue3-apexcharts'

defineOptions({
    layout: Default
})

const props = defineProps({
    risets: {
        type: Array,
        default: () => []
    },
    visualizationTypes: {
        type: Array,
        default: () => []
    },
    editingVisualization: {
        type: Object,
        default: null
    }
})

const isEditing = computed(() => !!props.editingVisualization)

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
const isSettingUp = ref(false)
const isSubmitting = ref(false)

const confirmState = ref({
    show: false,
    title: '',
    message: '',
    variant: 'primary',
    confirmText: ''
})

const statusState = ref({
    show: false,
    title: '',
    message: '',
    variant: 'success'
})

const risetOptions = computed(() => props.risets.map(r => ({
    value: r.id,
    label: r.name
})))

const topicOptions = computed(() => topics.value.map(t => ({
    value: t.id,
    label: t.name
})))

const visualizationTypeOptions = computed(() => props.visualizationTypes.map(vt => ({
    value: vt.id,
    label: vt.type_name
})))

const isBarOrPie = computed(() => ['bar', 'pie'].includes(selectedVisualizationType.value))
const isPeta = computed(() => selectedVisualizationType.value === 'peta')

const loadTopics = async (risetId) => {
    loadingTopics.value = true
    try {
        const response = await axios.get(route('admin.dashboard.topics'), {
            params: { riset_id: risetId }
        })
        topics.value = response.data.topics
    } catch (error) {
        console.error('Failed to load topics:', error)
    } finally {
        loadingTopics.value = false
    }
}

watch(() => form.riset_id, async (newRisetId) => {
    topics.value = []
    form.topic_id = null

    if (newRisetId) {
        await loadTopics(newRisetId)
    }
})

watch(() => form.visualization_type_id, (newTypeId) => {
    const vizType = props.visualizationTypes.find(vt => vt.id === newTypeId)
    selectedVisualizationType.value = vizType ? vizType.type_code : null

    if (isSettingUp.value) return

    chartCategories.value = []
    mapData.value = []
    mapFile.value = null
    showPreview.value = false
    
    // Initialize with one empty row for charts that need category/value input
    if (newTypeId && ['bar-chart', 'pie-chart', 'donut-chart', 'line-chart', 'area-chart'].includes(selectedVisualizationType.value)) {
        addCategory()
    }
}, { immediate: true })

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
    return selectedVisualizationType.value && ['bar-chart', 'pie-chart', 'donut-chart', 'line-chart', 'area-chart'].includes(selectedVisualizationType.value)
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

        // Format berbeda untuk pie/donut vs line/area/bar
        if (['pie-chart', 'donut-chart'].includes(selectedVisualizationType.value)) {
            form.chart_data = {
                labels: categories,
                datasets: values  // Langsung array nilai untuk pie/donut
            }
        } else {
            form.chart_data = {
                labels: categories,
                datasets: [{
                    name: 'Nilai',
                    data: values
                }]
            }
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
        chartOptions.value = {}
    }

    if (!skipPreview) {
        showPreview.value = true
        nextTick(() => {
            if (isPeta.value) {
                renderMap()
            }
        })
    }

    return true
}

const requestSubmit = () => {
    const ready = generateVisualizationData({ skipPreview: true })
    if (!ready) return

    confirmState.value = {
        show: true,
        title: isEditing.value ? 'Update Visualisasi' : 'Publish Visualisasi',
        message: isEditing.value
            ? 'Apakah Anda yakin ingin menyimpan perubahan pada visualisasi ini?'
            : 'Apakah Anda yakin ingin mempublikasikan visualisasi ini?',
        variant: 'primary',
        confirmText: isEditing.value ? 'Update' : 'Publish'
    }
}

const performSubmit = async () => {
    if (isSubmitting.value) return

    isSubmitting.value = true
    try {
        const payload = {
            riset_id: form.riset_id,
            topic_id: form.topic_id,
            visualization_type_id: form.visualization_type_id,
            title: form.title,
            interpretation: form.interpretation,
            chart_data: form.chart_data,
            chart_options: chartOptions.value
        }

        if (isEditing.value) {
            await axios.put(route('admin.dashboard.update', props.editingVisualization.id), payload)
            statusState.value = {
                show: true,
                title: 'Update Berhasil!',
                message: 'Perubahan telah tersimpan.',
                variant: 'success'
            }
        } else {
            await axios.post(route('admin.dashboard.publish'), payload)
            statusState.value = {
                show: true,
                title: 'Unggah Berhasil!',
                message: 'Visualisasi baru berhasil dipublikasikan.',
                variant: 'success'
            }
            handleReset()
        }
    } catch (error) {
        statusState.value = {
            show: true,
            title: 'Proses Gagal',
            message: error.response?.data?.message || 'Terjadi kesalahan pada server.',
            variant: 'danger'
        }
    } finally {
        confirmState.value.show = false
        isSubmitting.value = false
    }
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

    if (isEditing.value) {
        nextTick(() => initializeEditingState())
    }
}

const initializeEditingState = async () => {
    if (!props.editingVisualization) return

    isSettingUp.value = true
    const editing = props.editingVisualization

    form.riset_id = editing.riset_id ?? null
    form.visualization_type_id = editing.visualization_type_id
    form.title = editing.title
    form.interpretation = editing.interpretation
    form.chart_data = editing.chart_data
    form.chart_options = editing.chart_options ?? null

    if (editing.riset_id) {
        await loadTopics(editing.riset_id)
        form.topic_id = editing.topic_id
    }

    const vizType = props.visualizationTypes.find(vt => vt.id === editing.visualization_type_id)
    selectedVisualizationType.value = vizType ? vizType.type_code : null

    if (isBarOrPie.value) {
        const labels = editing.chart_data?.labels || []
        const dataset = editing.chart_data?.datasets?.[0]?.data || []
        chartCategories.value = labels.map((label, idx) => ({
            category: label,
            value: dataset[idx] ?? ''
        }))

        if (chartCategories.value.length === 0) {
            addCategory()
        } else {
            const numericValues = chartCategories.value.map(c => parseFloat(c.value) || 0)
            prepareApexChart(labels, numericValues)
        }
    } else if (isPeta.value) {
        mapData.value = editing.chart_data?.points || []
        chartCategories.value = []
    } else {
        chartCategories.value = []
        form.chart_data = editing.chart_data ?? {}
    }

    showPreview.value = true
    nextTick(() => {
        if (isPeta.value) {
            renderMap()
        }
    })

    isSettingUp.value = false
}

onMounted(() => {
    if (isEditing.value) {
        initializeEditingState()
    }
})

const prepareApexChart = (categories, values) => {
    if (selectedVisualizationType.value === 'bar-chart') {
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
            colors: ['#ef874b'],
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
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const categoryName = categories[dataPointIndex] || 'Kategori';
                    const value = series[seriesIndex][dataPointIndex];
                    
                    return `
                        <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                            <div class="font-semibold text-gray-800">${categoryName}</div>
                            <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                        </div>
                    `;
                }
            }
        }
    } else if (selectedVisualizationType.value === 'pie-chart') {
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
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const categoryName = categories[seriesIndex] || 'Kategori';
                    const value = series[seriesIndex];
                    const total = series.reduce((sum, val) => sum + val, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    
                    return `
                        <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                            <div class="font-semibold text-gray-800">${categoryName}</div>
                            <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                            <div class="text-xs text-gray-500">${percentage}% dari total</div>
                        </div>
                    `;
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
    } else if (selectedVisualizationType.value === 'donut-chart') {
        chartSeries.value = values
        
        chartOptions.value = {
            chart: {
                type: 'donut',
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
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                showAlways: false,
                                label: 'Total',
                                fontSize: '22px',
                                fontWeight: 600,
                                color: '#373d3f',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                enabled: true,
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const categoryName = categories[seriesIndex] || 'Kategori';
                    const value = series[seriesIndex];
                    const total = series.reduce((sum, val) => sum + val, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    
                    return `
                        <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                            <div class="font-semibold text-gray-800">${categoryName}</div>
                            <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                            <div class="text-xs text-gray-500">${percentage}% dari total</div>
                        </div>
                    `;
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
    } else if (selectedVisualizationType.value === 'line-chart') {
        chartSeries.value = [{
            name: 'Nilai',
            data: values
        }]
        
        chartOptions.value = {
            chart: {
                type: 'line',
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
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0)
                },
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: categories,
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
            colors: ['#ef874b'],
            markers: {
                size: 6,
                colors: ['#ef874b'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 8,
                }
            },
            tooltip: {
                enabled: true,
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const categoryName = categories[dataPointIndex] || 'Kategori';
                    const value = series[seriesIndex][dataPointIndex];
                    
                    return `
                        <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                            <div class="font-semibold text-gray-800">${categoryName}</div>
                            <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                        </div>
                    `;
                }
            }
        }
    } else if (selectedVisualizationType.value === 'area-chart') {
        chartSeries.value = [{
            name: 'Nilai',
            data: values
        }]
        
        chartOptions.value = {
            chart: {
                type: 'area',
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
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0)
                },
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: categories,
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
            colors: ['#ef874b'],
            tooltip: {
                enabled: true,
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const categoryName = categories[dataPointIndex] || 'Kategori';
                    const value = series[seriesIndex][dataPointIndex];
                    
                    return `
                        <div class="px-3 py-2 bg-white shadow-lg rounded-lg border">
                            <div class="font-semibold text-gray-800">${categoryName}</div>
                            <div class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></div>
                        </div>
                    `;
                }
            }
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
    // Template colors matching the design system
    const pklColors = ['#ef874b', '#50829b', '#748d63', '#fcda7b', '#8174a0', '#f69a5c']
    // Repeat colors if more than available
    const colors = []
    for (let i = 0; i < count; i++) {
        colors.push(pklColors[i % pklColors.length])
    }
    return colors
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
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Data Riset' : 'Input Data Riset'" />

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

                    <!-- Judul Visualisasi -->
                    <FormInput 
                        label="Judul Visualisasi" 
                        v-model="form.title"
                        :error="form.errors.title"
                        placeholder="Masukkan judul visualisasi"
                        required 
                    />

                    <!-- Dynamic Form: Chart Data Input -->
                    <div v-if="isBarOrPie" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Kategori dan Nilai</h3>
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
                                class="px-4 py-2 bg-[#FFFBDF] border border-[#7A2509] text-[#7A2509] rounded-lg transition hover:bg-[#7A2509] hover:text-white"                        >
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