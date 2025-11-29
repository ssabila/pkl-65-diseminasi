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
const choroplethData = ref([])
const choroplethVariables = ref([])
const choroplethGeojson = ref(null)
const selectedVariable = ref('')
const csvPreviewData = ref([])
const csvHeaders = ref([])
const uploadingMap = ref(false)
const selectedVisualizationType = ref(null)
const chartOptions = ref({})
const chartSeries = ref([])
const mapInstance = ref(null)

// Dialog states
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

// Watch for selectedVariable changes to update map
watch(() => selectedVariable.value, (newVariable) => {
    if (isChoropleth.value && showPreview.value && newVariable) {
        nextTick(() => {
            renderChoroplethMap()
        })
    }
})

watch(() => form.visualization_type_id, (newTypeId) => {
    const vizType = props.visualizationTypes.find(vt => vt.id === newTypeId)
    selectedVisualizationType.value = vizType ? vizType.type_code : null

    if (isSettingUp.value) return

    chartCategories.value = []
    mapData.value = []
    choroplethData.value = []
    choroplethVariables.value = []
    selectedVariable.value = ''
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

const isChoropleth = computed(() => {
    return selectedVisualizationType.value === 'choropleth' || 
           selectedVisualizationType.value === 'chloropleth' // fallback for any typo
})

const addCategory = () => {
    chartCategories.value.push({ category: '', value: '' })
}

const removeCategory = (index) => {
    chartCategories.value.splice(index, 1)
}

const formatVariableName = (variable) => {
    const names = {
        'population': 'Populasi',
        'density': 'Kepadatan Penduduk',
        'area': 'Luas Wilayah',
        'poverty': 'Tingkat Kemiskinan',
        'education': 'Tingkat Pendidikan',
        'income': 'Pendapatan Rata-rata',
        'unemployment': 'Tingkat Pengangguran'
    }
    return names[variable] || variable.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const handleMapFileChange = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    uploadingMap.value = true
    const formData = new FormData()
    formData.append('file', file)
    
    // Add map type parameter to distinguish between heatmap and choropleth
    if (isChoropleth.value) {
        formData.append('map_type', 'choropleth')
    } else {
        formData.append('map_type', 'heatmap')
    }

    try {
        const response = await axios.post(route('admin.dashboard.upload-map'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        if (response.data.success) {
            if (isChoropleth.value) {
                choroplethData.value = response.data.data
                choroplethVariables.value = response.data.variables || []
                choroplethGeojson.value = response.data.geojson
                
                // CSV Preview data
                if (response.data.preview_data && response.data.preview_headers) {
                    csvPreviewData.value = response.data.preview_data
                    csvHeaders.value = response.data.preview_headers
                }
                
                if (choroplethVariables.value.length > 0) {
                    selectedVariable.value = choroplethVariables.value[0]
                }
                notificationTitle.value = 'Upload Berhasil!'
                notificationMessage.value = `Berhasil memuat ${response.data.total_regions} data daerah dengan ${response.data.variables.length} variabel`
                showSuccessNotif.value = true
            } else {
                mapData.value = response.data.data
                notificationTitle.value = 'Upload Berhasil!'
                notificationMessage.value = `Berhasil memuat ${response.data.total_points} titik data`
                showSuccessNotif.value = true
            }
        }
    } catch (error) {
        console.error('Upload failed:', error)
        notificationTitle.value = 'Upload Gagal!'
        notificationMessage.value = error.response?.data?.message || 'Gagal mengupload file'
        showErrorNotif.value = true
        mapData.value = []
        choroplethData.value = []
        choroplethVariables.value = []
        choroplethGeojson.value = null
        selectedVariable.value = ''
    } finally {
        uploadingMap.value = false
    }
}

const handlePreview = () => {
    // Validation
    if (!form.riset_id || !form.topic_id || !form.visualization_type_id || !form.title) {
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon lengkapi semua field yang wajib diisi'
        showErrorNotif.value = true
        return
    }
    
    // Validation for interpretation
    if (!form.interpretation) {
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon isi interpretasi visualisasi'
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
    } else if (isChoropleth.value) {
        if (choroplethData.value.length === 0 || !selectedVariable.value) {
            notificationTitle.value = 'Data choropleth Kosong'
            notificationMessage.value = 'Mohon upload file data choropleth dan pilih variabel terlebih dahulu'
            showErrorNotif.value = true
            return
        }
        form.chart_data = { 
            regions: choroplethData.value, 
            selectedVariable: selectedVariable.value,
            variables: choroplethVariables.value,
            geojson: choroplethGeojson.value
        }
        chartOptions.value = {}
    }

    showPreview.value = true
    nextTick(() => {
        if (isPeta.value) {
            renderMap()
        } else if (isChoropleth.value) {
            renderChoroplethMap()
        }
    })

    return true
}

const requestSubmit = () => {
    // Validate and prepare data
    if (!form.riset_id || !form.topic_id || !form.visualization_type_id || !form.title) {
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon lengkapi semua field yang wajib diisi'
        showErrorNotif.value = true
        return
    }
    
    // Validation for non-choropleth charts
    if (!isChoropleth.value && !form.interpretation) {
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon isi interpretasi visualisasi'
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
    } else if (isPeta.value) {
        if (mapData.value.length === 0) {
            notificationTitle.value = 'Data Peta Kosong'
            notificationMessage.value = 'Mohon upload file data peta terlebih dahulu'
            showErrorNotif.value = true
            return
        }
    } else if (isChoropleth.value) {
        if (choroplethData.value.length === 0) {
            notificationTitle.value = 'Data choropleth Kosong'
            notificationMessage.value = 'Mohon upload file data choropleth terlebih dahulu'
            showErrorNotif.value = true
            return
        }
    }

    // Prepare data similar to handlePreview but don't show preview
    if (isBarOrPie.value) {
        const validCategories = chartCategories.value.filter(c => c.category && c.value)
        const categories = validCategories.map(c => c.category)
        const values = validCategories.map(c => parseFloat(c.value) || 0)

        if (['pie-chart', 'donut-chart'].includes(selectedVisualizationType.value)) {
            form.chart_data = {
                labels: categories,
                datasets: values
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
        form.chart_data = { points: mapData.value }
        chartOptions.value = {}
    } else if (isChoropleth.value) {
        form.chart_data = { 
            regions: choroplethData.value, 
            selectedVariable: selectedVariable.value,
            variables: choroplethVariables.value,
            geojson: choroplethGeojson.value
        }
        chartOptions.value = {}
    }

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
    choroplethData.value = []
    choroplethVariables.value = []
    selectedVariable.value = ''
    csvPreviewData.value = []
    csvHeaders.value = []
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
    } else if (isChoropleth.value) {
        choroplethData.value = editing.chart_data?.regions || []
        choroplethVariables.value = editing.chart_data?.variables || []
        selectedVariable.value = editing.chart_data?.selectedVariable || ''
        chartCategories.value = []
    } else {
        chartCategories.value = []
        form.chart_data = editing.chart_data ?? {}
    }

    showPreview.value = true
    nextTick(() => {
        if (isPeta.value) {
            renderMap()
        } else if (isChoropleth.value) {
            renderChoroplethMap()
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

const renderChoroplethMap = () => {
    const mapContainer = document.getElementById('previewchoroplethMap')
    if (!mapContainer || choroplethData.value.length === 0 || !selectedVariable.value) return

    // Clear existing map
    if (mapInstance.value) {
        mapInstance.value.remove()
    }
    mapContainer.innerHTML = ''

    // Create map centered on Yogyakarta
    mapInstance.value = L.map('previewchoroplethMap').setView([-7.7956, 110.3695], 9)

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(mapInstance.value)

    // Get values for the selected variable
    const values = choroplethData.value.map(region => parseFloat(region[selectedVariable.value]) || 0)
    const minValue = Math.min(...values)
    const maxValue = Math.max(...values)

    // Enhanced color scale function with better colors
    const getColor = (value) => {
        if (maxValue === minValue) return '#ef874b' // Single color if all values are the same
        
        const ratio = (value - minValue) / (maxValue - minValue)
        
        // Blue to red gradient for choropleth
        if (ratio < 0.1) return '#f7fbff'
        else if (ratio < 0.2) return '#deebf7'
        else if (ratio < 0.4) return '#c6dbef'
        else if (ratio < 0.6) return '#9ecae1'
        else if (ratio < 0.8) return '#6baed6'
        else return '#3182bd'
    }

    // Use GeoJSON data if available
    if (choroplethGeojson.value && choroplethGeojson.value.features) {
        const geoJsonLayer = L.geoJSON(choroplethGeojson.value, {
            style: (feature) => {
                // Find matching data for this region by ID
                const regionData = choroplethData.value.find(region => 
                    String(region.id) === String(feature.properties.id)
                )
                
                const value = regionData ? parseFloat(regionData[selectedVariable.value]) || 0 : 0
                const color = getColor(value)
                
                return {
                    fillColor: color,
                    weight: 2,
                    opacity: 1,
                    color: '#666',
                    dashArray: '',
                    fillOpacity: 0.7
                }
            },
            onEachFeature: (feature, layer) => {
                // Find matching data for popup by ID
                const regionData = choroplethData.value.find(region => 
                    String(region.id) === String(feature.properties.id)
                )
                
                if (regionData) {
                    // Create popup content with all variables
                    let popupContent = `
                        <div class="p-3 min-w-[200px]">
                            <h4 class="font-semibold text-lg mb-2 text-[#7A2509]">${feature.properties.name}</h4>
                            <div class="space-y-1">
                    `
                    
                    // Add all variables to popup
                    choroplethVariables.value.forEach(variable => {
                        const varValue = parseFloat(regionData[variable]) || 0
                        const isSelected = variable === selectedVariable.value
                        popupContent += `
                            <div class="flex justify-between items-center ${
                                isSelected ? 'bg-blue-100 px-2 py-1 rounded font-medium' : ''
                            }">
                                <span class="text-sm">${variable}:</span>
                                <span class="font-semibold ${isSelected ? 'text-[#7A2509]' : 'text-gray-700'}">${varValue.toLocaleString()}</span>
                            </div>
                        `
                    })
                    
                    popupContent += `
                            </div>
                            <div class="mt-2 pt-2 border-t text-xs text-gray-500">
                                Area: ${feature.properties.name}
                            </div>
                        </div>
                    `
                    
                    layer.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'custom-popup'
                    })
                }
                
                // Add hover effects
                layer.on({
                    mouseover: (e) => {
                        const layer = e.target
                        layer.setStyle({
                            weight: 3,
                            color: '#333',
                            dashArray: '',
                            fillOpacity: 0.9
                        })
                        layer.bringToFront()
                    },
                    mouseout: (e) => {
                        geoJsonLayer.resetStyle(e.target)
                    }
                })
            }
        }).addTo(mapInstance.value)

        // Fit map to GeoJSON bounds
        mapInstance.value.fitBounds(geoJsonLayer.getBounds())
    }

    // Add enhanced legend
    addchoroplethLegend(minValue, maxValue)
    
    // Add variable info panel
    addVariableInfoPanel()
}

const addchoroplethLegend = (minValue, maxValue) => {
    const legend = L.control({ position: 'bottomright' })
    
    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'info legend')
        div.style.background = 'white'
        div.style.padding = '12px'
        div.style.border = '1px solid #ccc'
        div.style.borderRadius = '8px'
        div.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)'
        div.style.fontSize = '13px'
        
        const grades = [0, 0.1, 0.2, 0.4, 0.6, 0.8, 1]
        const colors = ['#f7fbff', '#deebf7', '#c6dbef', '#9ecae1', '#6baed6', '#3182bd']
        
        div.innerHTML = `
            <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #7A2509; font-weight: bold;">
                ${selectedVariable.value}
            </h4>
        `
        
        for (let i = 0; i < grades.length - 1; i++) {
            const from = minValue + (grades[i] * (maxValue - minValue))
            const to = minValue + (grades[i + 1] * (maxValue - minValue))
            
            div.innerHTML += `
                <div style="display: flex; align-items: center; margin: 3px 0;">
                    <div style="width: 24px; height: 16px; background: ${colors[i]}; margin-right: 8px; border: 1px solid #ddd; border-radius: 2px;"></div>
                    <span style="font-size: 12px; color: #374151;">${from.toLocaleString()} - ${to.toLocaleString()}</span>
                </div>
            `
        }
        
        div.innerHTML += `
            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e5e5; font-size: 11px; color: #6b7280;">
                ${choroplethData.value.length} daerah
            </div>
        `
        
        return div
    }
    
    legend.addTo(mapInstance.value)
}

const addVariableInfoPanel = () => {
    const info = L.control({ position: 'topleft' })
    
    info.onAdd = function() {
        const div = L.DomUtil.create('div', 'info variable-info')
        div.style.background = 'white'
        div.style.padding = '10px'
        div.style.border = '1px solid #ccc'
        div.style.borderRadius = '8px'
        div.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)'
        div.style.fontSize = '12px'
        div.style.maxWidth = '200px'
        
        div.innerHTML = `
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #7A2509; font-weight: bold;">
                Variabel Tersedia
            </h4>
            <div style="color: #6b7280; line-height: 1.4;">
                ${choroplethVariables.value.length} variabel dapat dipilih.<br>
                Klik tombol variabel di bawah peta untuk mengganti tampilan.
            </div>
        `
        
        return div
    }
    
    info.addTo(mapInstance.value)
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

                    <!-- Dynamic Form: Choropleth -->
                    <div v-if="isChoropleth" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Upload Data Peta Choropleth</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    File Excel/CSV
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    ref="choroplethFileInput"
                                    type="file"
                                    accept=".csv,.xlsx,.xls"
                                    @change="handleMapFileChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Format: ID, Nama Daerah, Variable1, Variable2, ...
                                </p>
                            </div>
                            
                            <!-- Variable Selection -->
                            <div v-if="choroplethVariables.length > 0">
                                <FormSelect
                                    label="Pilih Variabel untuk Ditampilkan"
                                    v-model="selectedVariable"
                                    :options="choroplethVariables.map(v => ({ value: v, label: v }))"
                                    placeholder="-- Pilih Variabel --"
                                    required
                                />
                            </div>
                            
                            <div v-if="uploadingMap" class="text-sm text-gray-600">
                                Memproses file...
                            </div>
                            <div v-if="choroplethData.length > 0" class="text-sm text-green-600">
                                ✓ {{ choroplethData.length }} data daerah berhasil dimuat dengan {{ choroplethVariables.length }} variabel
                            </div>
                        </div>
                        
                        <!-- CSV Preview Table -->
                        <div v-if="csvPreviewData.length > 0" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Preview Data (5 baris pertama):</h4>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th v-for="header in csvHeaders" :key="header" 
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ header }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(row, index) in csvPreviewData.slice(0, 5)" :key="index">
                                            <td v-for="(value, colIndex) in row" :key="colIndex" 
                                                class="px-4 py-2 whitespace-nowrap text-gray-900">
                                                {{ value }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Menampilkan 5 dari {{ csvPreviewData.length }} baris data
                            </p>
                        </div>
                    </div>

                    <!-- Interpretasi untuk semua tipe visualisasi -->
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

                <!-- choropleth Map Preview -->
                <div v-if="isChoropleth" class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold">Peta Choropleth Interaktif</h4>
                        <div v-if="choroplethVariables.length > 1" class="w-64">
                            <FormSelect
                                label="Variabel Aktif"
                                v-model="selectedVariable"
                                :options="choroplethVariables.map(v => ({ value: v, label: v }))"
                                placeholder="Pilih Variabel"
                            />
                        </div>
                    </div>
                    
                    <!-- Interactive Controls -->
                    <div v-if="choroplethVariables.length > 1" class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium mb-3 text-gray-700">Pilih Variabel untuk Ditampilkan:</h5>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            <button
                                v-for="variable in choroplethVariables"
                                :key="variable"
                                @click="selectedVariable = variable"
                                :class="[
                                    'px-3 py-2 text-sm rounded-lg border transition-all duration-200',
                                    selectedVariable === variable
                                        ? 'bg-[#7A2509] text-white border-[#7A2509] shadow-md'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#7A2509] hover:bg-[#7A2509] hover:text-white'
                                ]"
                            >
                                {{ variable }}
                            </button>
                        </div>
                    </div>
                    
                    <div 
                        id="previewchoroplethMap" 
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
                        @click="requestSubmit"
                        type="button"
                        :disabled="isSubmitting"
                        class="px-6 py-3 bg-[#7A2509] hover:bg-[#5e1d07] text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg v-if="!isSubmitting" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ isSubmitting ? 'Processing...' : (isEditing ? 'Update Visualisasi' : 'Konfirmasi & Publish') }}</span>
                    </button>
                </div>
            </div>
        </main>

        <!-- Dialogs -->
        <ConfirmDialog
            :show="confirmState.show"
            :message="confirmState.message"
            :confirm-text="confirmState.confirmText"
            cancel-text="Batal"
            confirm-color="blue"
            @confirm="performSubmit"
            @close="confirmState.show = false"
        />

        <NotificationDialog
            :show="statusState.show"
            :type="statusState.variant"
            :title="statusState.title"
            :message="statusState.message"
            button-text="OK"
            @close="statusState.show = false"
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

/* Custom Leaflet Popup Styling */
:deep(.leaflet-popup-content-wrapper) {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

:deep(.leaflet-popup-content) {
    margin: 0;
    line-height: 1.4;
}

:deep(.custom-popup .leaflet-popup-content-wrapper) {
    background: white;
    border: 1px solid #e5e7eb;
}

:deep(.custom-popup .leaflet-popup-tip) {
    background: white;
    border-left: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}

/* ApexCharts customization */
:deep(.apexcharts-canvas) {
    margin: 0 auto;
}

:deep(.apexcharts-menu) {
    background: #fff;
    border: 1px solid #ddd;
}

/* Variable selector buttons */
.variable-button {
    transition: all 0.2s ease-in-out;
}

.variable-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(122, 37, 9, 0.2);
}
</style>
