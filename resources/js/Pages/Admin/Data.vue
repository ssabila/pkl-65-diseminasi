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
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexStackedBarChart from '@/Components/Charts/ApexStackedBarChart.vue'
import ApexGroupedBarChart from '@/Components/Charts/ApexGroupedBarChart.vue'
import ApexGroupedStackedBarChart from '@/Components/Charts/ApexGroupedStackedBarChart.vue'
import ApexStackedBar100Chart from '@/Components/Charts/ApexStackedBar100Chart.vue'
import ApexClusteredBarChart from '@/Components/Charts/ApexClusteredBarChart.vue'
import ApexPopulationPyramidChart from '@/Components/Charts/ApexPopulationPyramidChart.vue'
import ApexBoxPlotChart from '@/Components/Charts/ApexBoxPlotChart.vue'
import ApexHeatmapChart from '@/Components/Charts/ApexHeatmapChart.vue'
import ApexDensityChart from '@/Components/Charts/ApexDensityChart.vue'
import VennDiagramChart from '@/Components/Charts/VennDiagramChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'
import ApexHistogramChart from '@/Components/Charts/ApexHistogramChart.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'

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
    },
    accessError: {
        type: String,
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
    chart_data_json: ''
})

const topics = ref([])
const loadingTopics = ref(false)
const showPreview = ref(false)
const chartCategories = ref([])
const multiSeriesData = ref([]) // For stacked/grouped bar charts
const boxPlotData = ref([]) // For box plot
const vennSets = ref([]) // For venn diagram sets
const vennOverlaps = ref([]) // For venn diagram overlaps
const heatmapRows = ref([]) // For heatmap matrix
const populationPyramidData = ref([]) // For population pyramid
const mapFile = ref(null)
const mapData = ref([])
const choroplethData = ref([])
const choroplethVariables = ref([])
const choroplethGeojson = ref(null)
const selectedVariable = ref('')
const csvPreviewData = ref([])
const csvHeaders = ref([])
const uploadingMap = ref(false)
// Grouped Stacked Bar Chart data structure
const groupedStackedData = ref([]) // { groupName: '', subgroups: [{ subgroupName: '', stacks: [{ stackName: '', value: '' }] }] }
// Histogram data (raw numeric values)
const histogramRawData = ref('')
const histogramBinCount = ref(10)
const histogramBinMethod = ref('manual') // 'manual', 'sturges', 'sqrt', 'freedman'
const selectedVisualizationType = ref(null)
const chartOptions = ref({})
const chartSeries = ref([])
const mapInstance = ref(null)
const chartKey = ref(0) // Force chart re-render
const variableInterpretations = ref({}) // Store interpretations per variable

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

// Watch vennSets to automatically generate overlap combinations
watch(() => vennSets.value.map(s => s.name).filter(Boolean), (setNames) => {
    if (setNames.length < 2 || !isVennDiagram.value) {
        vennOverlaps.value = []
        return
    }

    // Generate all possible overlap combinations
    const newOverlaps = []
    
    // Pairwise overlaps (2 sets)
    for (let i = 0; i < setNames.length; i++) {
        for (let j = i + 1; j < setNames.length; j++) {
            newOverlaps.push({
                sets: [setNames[i], setNames[j]],
                size: ''
            })
        }
    }
    
    // Three-way overlap (all 3 sets) if we have 3 sets
    if (setNames.length === 3) {
        newOverlaps.push({
            sets: [setNames[0], setNames[1], setNames[2]],
            size: ''
        })
    }
    
    // Preserve existing overlap values where possible
    newOverlaps.forEach(newOverlap => {
        const existing = vennOverlaps.value.find(existing => 
            existing.sets.length === newOverlap.sets.length &&
            existing.sets.every(s => newOverlap.sets.includes(s))
        )
        if (existing) {
            newOverlap.size = existing.size
        }
    })
    
    vennOverlaps.value = newOverlaps
}, { deep: true })

watch(() => form.visualization_type_id, (newTypeId) => {
    const vizType = props.visualizationTypes.find(vt => vt.id === newTypeId)
    selectedVisualizationType.value = vizType ? vizType.type_code : null

    // Skip reset if we're in setup mode or if we're loading existing data
    if (isSettingUp.value || (props.editingVisualization && form.chart_data)) return

    chartCategories.value = []
    multiSeriesData.value = []
    boxPlotData.value = []
    vennSets.value = []
    vennOverlaps.value = []
    heatmapRows.value = []
    populationPyramidData.value = []
    mapData.value = []
    choroplethData.value = []
    choroplethVariables.value = []
    selectedVariable.value = ''
    mapFile.value = null
    showPreview.value = false
    form.chart_data_json = ''
    groupedStackedData.value = []
    histogramRawData.value = ''
    histogramBinCount.value = 10
    histogramBinMethod.value = 'manual'
    
    // Initialize with one empty row for charts that need category/value input
    const simpleChartTypes = ['bar-chart', 'pie-chart', 'donut-chart', 'line-chart', 'area-chart']
    const multiSeriesChartTypes = ['stacked-bar-chart', 'grouped-bar-chart', '100-stacked-bar-chart', 'clustered-bar-chart']
    const matrixChartTypes = ['heatmap-matrix']
    
    if (newTypeId && simpleChartTypes.includes(selectedVisualizationType.value)) {
        addCategory()
    } else if (newTypeId && multiSeriesChartTypes.includes(selectedVisualizationType.value)) {
        addSeriesCategory()
    } else if (newTypeId && selectedVisualizationType.value === 'grouped-stacked-bar-chart') {
        addGroupedStackedGroup()
    } else if (newTypeId && selectedVisualizationType.value === 'histogram') {
        // Histogram doesn't need initialization - uses raw data input
    } else if (newTypeId && selectedVisualizationType.value === 'box-plot') {
        addBoxPlotGroup()
    } else if (newTypeId && selectedVisualizationType.value === 'venn-diagram') {
        addVennSet()
        addVennSet()
    } else if (newTypeId && selectedVisualizationType.value === 'heatmap-matrix') {
        heatmapRows.value = [{
            rowName: '',
            columns: [
                { columnName: 'Col 1', value: '' },
                { columnName: 'Col 2', value: '' },
                { columnName: 'Col 3', value: '' },
                { columnName: 'Col 4', value: '' }
            ]
        }]
    } else if (newTypeId && selectedVisualizationType.value === 'population-pyramid') {
        addPopulationPyramidRow()
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

const isHistogram = computed(() => {
    return selectedVisualizationType.value === 'histogram'
})

const isMultiSeries = computed(() => {
    return selectedVisualizationType.value && ['stacked-bar-chart', 'grouped-bar-chart', '100-stacked-bar-chart', 'clustered-bar-chart'].includes(selectedVisualizationType.value)
})

const isGroupedStackedBar = computed(() => {
    return selectedVisualizationType.value === 'grouped-stacked-bar-chart'
})

const isBoxPlot = computed(() => {
    return selectedVisualizationType.value === 'box-plot'
})

const isVennDiagram = computed(() => {
    return selectedVisualizationType.value === 'venn-diagram'
})

const isHeatmapMatrix = computed(() => {
    return selectedVisualizationType.value === 'heatmap-matrix'
})

const isDensityPlot = computed(() => {
    return selectedVisualizationType.value === 'density-plot'
})

const isPeta = computed(() => {
    return selectedVisualizationType.value === 'peta'
})

const isChoropleth = computed(() => {
    return selectedVisualizationType.value === 'choropleth'
})

const isPopulationPyramid = computed(() => {
    return selectedVisualizationType.value === 'population-pyramid'
})

const addCategory = () => {
    chartCategories.value.push({ category: '', value: '' })
}

const removeCategory = (index) => {
    chartCategories.value.splice(index, 1)
}

// Multi-series charts functions
const addSeriesCategory = () => {
    if (multiSeriesData.value.length === 0) {
        multiSeriesData.value.push({
            seriesName: '',
            categories: [{ category: '', value: '' }]
        })
    } else {
        multiSeriesData.value.push({
            seriesName: '',
            categories: multiSeriesData.value[0].categories.map(() => ({ category: '', value: '' }))
        })
    }
}

const removeSeriesCategory = (index) => {
    multiSeriesData.value.splice(index, 1)
}

const addCategoryToSeries = () => {
    multiSeriesData.value.forEach(series => {
        series.categories.push({ category: '', value: '' })
    })
}

const removeCategoryFromSeries = (catIndex) => {
    multiSeriesData.value.forEach(series => {
        series.categories.splice(catIndex, 1)
    })
}

// Box Plot functions
const addBoxPlotGroup = () => {
    boxPlotData.value.push({
        groupName: '',
        min: '',
        q1: '',
        median: '',
        q3: '',
        max: ''
    })
}

const removeBoxPlotGroup = (index) => {
    boxPlotData.value.splice(index, 1)
}

// Venn Diagram functions
const addVennSet = () => {
    vennSets.value.push({ name: '', size: '' })
}

const removeVennSet = (index) => {
    vennSets.value.splice(index, 1)
}

const addVennOverlap = () => {
    vennOverlaps.value.push({ sets: [], size: '' })
}

const removeVennOverlap = (index) => {
    vennOverlaps.value.splice(index, 1)
}

// Heatmap Matrix functions
const addHeatmapRow = () => {
    const numCols = heatmapRows.value[0]?.columns?.length || 4
    const columns = []
    for (let i = 0; i < numCols; i++) {
        columns.push({ columnName: `Col ${i + 1}`, value: '' })
    }
    heatmapRows.value.push({ rowName: '', columns: columns })
}

const removeHeatmapRow = (index) => {
    if (heatmapRows.value.length > 1) {
        heatmapRows.value.splice(index, 1)
    }
}

const addHeatmapColumn = () => {
    const newColIndex = heatmapRows.value[0]?.columns?.length || 0
    heatmapRows.value.forEach(row => {
        row.columns.push({ columnName: `Col ${newColIndex + 1}`, value: '' })
    })
}

const removeHeatmapColumn = () => {
    if (heatmapRows.value[0]?.columns?.length > 1) {
        heatmapRows.value.forEach(row => {
            row.columns.pop()
        })
    }
}

// Population Pyramid functions
const addPopulationPyramidRow = () => {
    // Preserve labels from first row if exists
    const firstRow = populationPyramidData.value[0] || {}
    populationPyramidData.value.push({
        ageGroup: '',
        leftValue: '',
        rightValue: '',
        leftLabel: firstRow.leftLabel || 'Left',
        rightLabel: firstRow.rightLabel || 'Right'
    })
}

const removePopulationPyramidRow = (index) => {
    if (populationPyramidData.value.length > 1) {
        populationPyramidData.value.splice(index, 1)
    }
}

// Grouped Stacked Bar Chart functions
const addGroupedStackedGroup = () => {
    groupedStackedData.value.push({
        groupName: '',
        subgroups: [
            {
                subgroupName: '',
                stacks: [{ stackName: '', value: '' }]
            }
        ]
    })
}

const removeGroupedStackedGroup = (groupIndex) => {
    if (groupedStackedData.value.length > 1) {
        groupedStackedData.value.splice(groupIndex, 1)
    }
}

const addSubgroupToGroup = (groupIndex) => {
    // Copy stack structure from first subgroup
    const firstSubgroup = groupedStackedData.value[groupIndex].subgroups[0]
    const newStacks = firstSubgroup ? firstSubgroup.stacks.map(s => ({ stackName: s.stackName, value: '' })) : [{ stackName: '', value: '' }]
    groupedStackedData.value[groupIndex].subgroups.push({
        subgroupName: '',
        stacks: newStacks
    })
}

const removeSubgroupFromGroup = (groupIndex, subgroupIndex) => {
    if (groupedStackedData.value[groupIndex].subgroups.length > 1) {
        groupedStackedData.value[groupIndex].subgroups.splice(subgroupIndex, 1)
    }
}

const addStackToAllSubgroups = (groupIndex) => {
    groupedStackedData.value[groupIndex].subgroups.forEach(subgroup => {
        subgroup.stacks.push({ stackName: '', value: '' })
    })
}

const removeStackFromAllSubgroups = (groupIndex, stackIndex) => {
    groupedStackedData.value[groupIndex].subgroups.forEach(subgroup => {
        if (subgroup.stacks.length > 1) {
            subgroup.stacks.splice(stackIndex, 1)
        }
    })
}

// Sync stack names across all subgroups in a group
const syncStackNames = (groupIndex) => {
    const firstSubgroup = groupedStackedData.value[groupIndex].subgroups[0]
    if (!firstSubgroup) return
    
    const stackNames = firstSubgroup.stacks.map(s => s.stackName)
    groupedStackedData.value[groupIndex].subgroups.forEach((subgroup, idx) => {
        if (idx === 0) return
        subgroup.stacks.forEach((stack, sIdx) => {
            if (stackNames[sIdx]) {
                stack.stackName = stackNames[sIdx]
            }
        })
    })
}

// Histogram functions
const calculateHistogramBins = (data, method, manualBinCount) => {
    if (!data || data.length === 0) return { bins: [], labels: [] }
    
    const n = data.length
    const min = Math.min(...data)
    const max = Math.max(...data)
    const range = max - min
    
    let binCount = manualBinCount
    
    if (method === 'sturges') {
        // Sturges' formula: k = ceil(log2(n) + 1)
        binCount = Math.ceil(Math.log2(n) + 1)
    } else if (method === 'sqrt') {
        // Square-root rule: k = ceil(sqrt(n))
        binCount = Math.ceil(Math.sqrt(n))
    } else if (method === 'freedman') {
        // Freedman-Diaconis rule: bin width = 2 * IQR / n^(1/3)
        const sorted = [...data].sort((a, b) => a - b)
        const q1Index = Math.floor(n * 0.25)
        const q3Index = Math.floor(n * 0.75)
        const iqr = sorted[q3Index] - sorted[q1Index]
        const binWidth = 2 * iqr / Math.pow(n, 1/3)
        binCount = binWidth > 0 ? Math.ceil(range / binWidth) : manualBinCount
    }
    
    // Ensure reasonable bin count
    binCount = Math.max(1, Math.min(binCount, 50))
    
    const binWidth = range / binCount
    const bins = new Array(binCount).fill(0)
    const labels = []
    
    // Create bin labels and count frequencies
    for (let i = 0; i < binCount; i++) {
        const binStart = min + i * binWidth
        const binEnd = min + (i + 1) * binWidth
        labels.push(`${binStart.toFixed(1)} - ${binEnd.toFixed(1)}`)
        
        data.forEach(value => {
            // Last bin is inclusive on the right
            if (i === binCount - 1) {
                if (value >= binStart && value <= binEnd) bins[i]++
            } else {
                if (value >= binStart && value < binEnd) bins[i]++
            }
        })
    }
    
    return { bins, labels, binWidth, min, max }
}

const parseHistogramData = () => {
    // Parse comma/newline/space separated numeric values
    const text = histogramRawData.value.trim()
    if (!text) return []
    
    const values = text
        .split(/[\s,;\n]+/)
        .map(v => parseFloat(v.trim()))
        .filter(v => !isNaN(v))
    
    return values
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

const getChartType = (typeCode) => {
    const typeMap = {
        'bar-chart': 'bar',
        'pie-chart': 'pie',
        'donut-chart': 'donut',
        'line-chart': 'line',
        'area-chart': 'area',
        'histogram': 'bar'
    }
    return typeMap[typeCode] || typeCode
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
                
                // Initialize variable interpretations
                choroplethVariables.value.forEach(variable => {
                    if (!variableInterpretations.value[variable]) {
                        variableInterpretations.value[variable] = ''
                    }
                })
                
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
    if (!isChoropleth.value && !form.interpretation) {
        notificationTitle.value = 'Data Tidak Lengkap'
        notificationMessage.value = 'Mohon isi interpretasi visualisasi'
        showErrorNotif.value = true
        return
    }
    
    // Validation for choropleth variable-specific interpretations
    if (isChoropleth.value && choroplethVariables.value.length > 0) {
        const missingInterpretations = choroplethVariables.value.filter(
            variable => !variableInterpretations.value[variable] || variableInterpretations.value[variable].trim() === ''
        )
        if (missingInterpretations.length > 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = `Mohon isi interpretasi untuk variabel: ${missingInterpretations.join(', ')}`
            showErrorNotif.value = true
            return
        }
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
            geojson: choroplethGeojson.value,
            interpretations: variableInterpretations.value
        }
        // Store all interpretations as a combined interpretation
        form.interpretation = Object.entries(variableInterpretations.value)
            .map(([variable, interpretation]) => `${variable}: ${interpretation}`)
            .join('\n\n')
        chartOptions.value = {}
    }

    // Handle Histogram - calculate bins from raw data
    if (isHistogram.value) {
        const rawValues = parseHistogramData()
        if (rawValues.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon masukkan data numerik untuk histogram'
            showErrorNotif.value = true
            return
        }

        const { bins, labels, binWidth, min, max } = calculateHistogramBins(
            rawValues, 
            histogramBinMethod.value, 
            parseInt(histogramBinCount.value) || 10
        )

        form.chart_data = {
            labels: labels,
            datasets: [{
                name: 'Frekuensi',
                data: bins
            }],
            rawData: rawValues,
            binMethod: histogramBinMethod.value,
            binCount: bins.length,
            binWidth: binWidth,
            min: min,
            max: max
        }
        
        // Prepare chart options for histogram preview
        chartSeries.value = [{
            name: 'Frekuensi',
            data: bins
        }]
        
        chartOptions.value = {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false } },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '95%',
                    borderRadius: 0
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: { fontSize: '11px', colors: ["#304758"] }
            },
            xaxis: {
                categories: labels,
                labels: { style: { fontSize: '10px' }, rotate: -45, rotateAlways: labels.length > 8 },
                title: { text: 'Interval', style: { fontSize: '12px' } }
            },
            yaxis: {
                title: { text: 'Frekuensi', style: { fontSize: '12px' } },
                labels: { formatter: val => val.toFixed(0) }
            },
            colors: ['#ef874b'],
            tooltip: {
                enabled: true,
                y: { formatter: val => `${val} data` }
            }
        }
    }

    // Handle Grouped Stacked Bar Chart
    if (isGroupedStackedBar.value) {
        if (groupedStackedData.value.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon tambahkan minimal satu group'
            showErrorNotif.value = true
            return
        }

        // Validate all groups have data
        const hasValidData = groupedStackedData.value.every(group => 
            group.groupName && 
            group.subgroups.length > 0 && 
            group.subgroups.every(sg => sg.subgroupName && sg.stacks.length > 0)
        )

        if (!hasValidData) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Semua group harus memiliki nama, subgroup, dan stack category'
            showErrorNotif.value = true
            return
        }

        // Build the grouped stacked data structure
        // Labels = Group names (x-axis main categories)
        const groups = groupedStackedData.value.map(g => g.groupName)
        
        // Get all unique subgroup names across all groups
        const allSubgroups = [...new Set(
            groupedStackedData.value.flatMap(g => g.subgroups.map(sg => sg.subgroupName))
        )]
        
        // Get all unique stack names (should be consistent across subgroups)
        const allStacks = [...new Set(
            groupedStackedData.value.flatMap(g => 
                g.subgroups.flatMap(sg => sg.stacks.map(s => s.stackName))
            )
        )].filter(Boolean)

        // Build datasets: for ApexCharts grouped stacked, we need series per stack category
        // Each series represents one stack category across all subgroups of all groups
        const datasets = []
        
        allStacks.forEach(stackName => {
            allSubgroups.forEach(subgroupName => {
                const seriesData = groups.map(groupName => {
                    const group = groupedStackedData.value.find(g => g.groupName === groupName)
                    const subgroup = group?.subgroups.find(sg => sg.subgroupName === subgroupName)
                    const stack = subgroup?.stacks.find(s => s.stackName === stackName)
                    return parseFloat(stack?.value) || 0
                })
                
                datasets.push({
                    name: `${subgroupName} - ${stackName}`,
                    group: subgroupName,
                    data: seriesData
                })
            })
        })

        form.chart_data = {
            labels: groups,
            datasets: datasets,
            subgroups: allSubgroups,
            stacks: allStacks,
            rawGroupedData: groupedStackedData.value
        }
        chartOptions.value = {}
    }

    // Handle Multi-Series Charts
    if (isMultiSeries.value) {
        if (multiSeriesData.value.length === 0 || multiSeriesData.value.some(s => !s.seriesName)) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu series dengan nama'
            showErrorNotif.value = true
            return
        }

        const labels = multiSeriesData.value[0].categories.map(c => c.category).filter(Boolean)
        const datasets = multiSeriesData.value.map(series => ({
            name: series.seriesName,
            data: series.categories.map(c => parseFloat(c.value) || 0)
        }))

        form.chart_data = { labels, datasets }
        chartOptions.value = {}
    }

    // Handle Box Plot
    if (isBoxPlot.value) {
        if (boxPlotData.value.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu group box plot'
            showErrorNotif.value = true
            return
        }

        const labels = boxPlotData.value.map(g => g.groupName)
        const datasets = [{
            type: 'boxPlot',
            data: boxPlotData.value.map(g => ({
                x: g.groupName,
                y: [
                    parseFloat(g.min) || 0,
                    parseFloat(g.q1) || 0,
                    parseFloat(g.median) || 0,
                    parseFloat(g.q3) || 0,
                    parseFloat(g.max) || 0
                ]
            }))
        }]

        form.chart_data = { labels, datasets }
        chartOptions.value = {}
    }

    // Handle Venn Diagram
    if (isVennDiagram.value) {
        if (vennSets.value.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu set untuk Venn diagram'
            showErrorNotif.value = true
            return
        }

        const sets = vennSets.value.map(s => ({
            name: s.name,
            size: parseFloat(s.size) || 0
        }))

        const overlaps = vennOverlaps.value
            .filter(o => o.sets.length >= 2 && o.size)
            .map(o => ({
                sets: o.sets,
                size: parseFloat(o.size) || 0
            }))

        form.chart_data = { vennData: { sets, overlaps } }
        chartOptions.value = {}
    }

    // Handle Heatmap Matrix
    if (isHeatmapMatrix.value) {
        if (heatmapRows.value.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu baris untuk heatmap'
            showErrorNotif.value = true
            return
        }

        const categories = heatmapRows.value[0].columns.map(col => col.columnName)
        const datasets = heatmapRows.value.map(row => ({
            name: row.rowName || 'Row',
            data: row.columns.map(col => parseFloat(col.value) || 0)
        }))

        form.chart_data = { categories, datasets }
        chartOptions.value = {}
    }

    // Handle Density Plot (use same as histogram/bar)
    if (isDensityPlot.value) {
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
                name: 'Density',
                data: values
            }]
        }
        prepareApexChart(categories, values)
    }

    // Handle Population Pyramid
    if (isPopulationPyramid.value) {
        if (populationPyramidData.value.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi minimal satu kategori untuk population pyramid'
            showErrorNotif.value = true
            return
        }

        const validData = populationPyramidData.value.filter(d => d.ageGroup && d.leftValue && d.rightValue)
        if (validData.length === 0) {
            notificationTitle.value = 'Data Tidak Lengkap'
            notificationMessage.value = 'Mohon isi kategori dan nilai untuk kedua sisi'
            showErrorNotif.value = true
            return
        }

        const labels = validData.map(d => d.ageGroup)
        const leftValues = validData.map(d => parseFloat(d.leftValue) || 0)
        const rightValues = validData.map(d => parseFloat(d.rightValue) || 0)

        form.chart_data = {
            labels: labels,
            datasets: [
                { name: populationPyramidData.value[0]?.leftLabel || 'Left', data: leftValues },
                { name: populationPyramidData.value[0]?.rightLabel || 'Right', data: rightValues }
            ]
        }
        chartOptions.value = {}
    }

    // Increment chart key to force re-render
    chartKey.value++
    
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
    } else if (isHistogram.value) {
        // Histogram validation will be done below
    } else if (isGroupedStackedBar.value) {
        // Grouped Stacked Bar validation will be done below  
    } else if (isBoxPlot.value || isVennDiagram.value || isHeatmapMatrix.value || isDensityPlot.value) {
        // These will be validated below with proper data validation
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
    } else if (isMultiSeries.value) {
        // Validate Multi-Series Charts (Stacked Bar, Grouped Bar, Grouped Stacked Bar)
        if (multiSeriesData.value.length === 0) {
            notificationTitle.value = 'Data Series Kosong'
            notificationMessage.value = 'Mohon tambahkan minimal satu series untuk grafik ini'
            showErrorNotif.value = true
            return
        }
        
        const hasSeriesName = multiSeriesData.value.every(s => s.seriesName && s.seriesName.trim())
        if (!hasSeriesName) {
            notificationTitle.value = 'Nama Series Kosong'
            notificationMessage.value = 'Semua series harus memiliki nama'
            showErrorNotif.value = true
            return
        }

        const categories = []
        const seriesData = []

        multiSeriesData.value.forEach(series => {
            const seriesValues = []
            series.categories.forEach(cat => {
                if (!categories.includes(cat.category)) {
                    categories.push(cat.category)
                }
                seriesValues.push(parseFloat(cat.value) || 0)
            })
            
            seriesData.push({
                name: series.seriesName,
                data: seriesValues
            })
        })

        form.chart_data = {
            labels: categories,
            datasets: seriesData
        }
        chartOptions.value = {}
    } else if (isPopulationPyramid.value) {
        // Validate Population Pyramid
        if (populationPyramidData.value.length === 0) {
            notificationTitle.value = 'Data Population Pyramid Kosong'
            notificationMessage.value = 'Mohon tambahkan minimal satu kelompok usia'
            showErrorNotif.value = true
            return
        }

        const hasValidData = populationPyramidData.value.every(d => {
            return d.ageGroup && d.leftValue !== '' && d.rightValue !== ''
        })

        if (!hasValidData) {
            notificationTitle.value = 'Data Population Pyramid Tidak Lengkap'
            notificationMessage.value = 'Semua kelompok harus memiliki nama dan nilai untuk kedua sisi'
            showErrorNotif.value = true
            return
        }

        const labels = populationPyramidData.value.map(d => d.ageGroup)
        const leftData = populationPyramidData.value.map(d => parseFloat(d.leftValue) || 0)
        const rightData = populationPyramidData.value.map(d => parseFloat(d.rightValue) || 0)

        form.chart_data = {
            labels: labels,
            datasets: [
                { name: 'Male', data: leftData },
                { name: 'Female', data: rightData }
            ]
        }
        chartOptions.value = {}
    } else if (isBoxPlot.value) {
        // Validate Box Plot
        if (boxPlotData.value.length === 0) {
            notificationTitle.value = 'Data Box Plot Kosong'
            notificationMessage.value = 'Mohon tambahkan minimal satu kelompok untuk box plot'
            showErrorNotif.value = true
            return
        }

        const hasValidData = boxPlotData.value.every(group => {
            return group.groupName && group.min !== '' && group.q1 !== '' && 
                   group.median !== '' && group.q3 !== '' && group.max !== ''
        })

        if (!hasValidData) {
            notificationTitle.value = 'Data Box Plot Tidak Lengkap'
            notificationMessage.value = 'Semua kelompok harus memiliki nama dan 5 nilai (Min, Q1, Median, Q3, Max)'
            showErrorNotif.value = true
            return
        }

        const categories = boxPlotData.value.map(g => g.groupName)
        const boxData = boxPlotData.value.map(group => ({
            x: group.groupName,
            y: [
                parseFloat(group.min),
                parseFloat(group.q1),
                parseFloat(group.median),
                parseFloat(group.q3),
                parseFloat(group.max)
            ]
        }))

        form.chart_data = {
            labels: categories,
            datasets: [{
                type: 'boxPlot',
                data: boxData
            }]
        }
        chartOptions.value = {}
    } else if (isVennDiagram.value) {
        // Validate Venn Diagram
        if (vennSets.value.length < 2 || vennSets.value.length > 3) {
            notificationTitle.value = 'Jumlah Set Tidak Valid'
            notificationMessage.value = 'Venn Diagram membutuhkan 2-3 set'
            showErrorNotif.value = true
            return
        }

        const hasValidSets = vennSets.value.every(s => s.name && s.size)
        if (!hasValidSets) {
            notificationTitle.value = 'Data Set Tidak Lengkap'
            notificationMessage.value = 'Semua set harus memiliki nama dan ukuran'
            showErrorNotif.value = true
            return
        }

        form.chart_data = {
            vennData: {
                sets: vennSets.value.map(s => ({ name: s.name, size: parseFloat(s.size) || 0 })),
                overlaps: vennOverlaps.value
                    .filter(o => o.sets.length >= 2 && o.size)
                    .map(o => ({ 
                        sets: o.sets, 
                        size: parseFloat(o.size) || 0 
                    }))
            }
        }
        chartOptions.value = {}
    } else if (isHistogram.value) {
        // Validate Histogram
        const rawValues = parseHistogramData()
        if (rawValues.length === 0) {
            notificationTitle.value = 'Data Histogram Kosong'
            notificationMessage.value = 'Mohon masukkan data numerik untuk histogram'
            showErrorNotif.value = true
            return
        }

        const { bins, labels, binWidth, min, max } = calculateHistogramBins(
            rawValues, 
            histogramBinMethod.value, 
            parseInt(histogramBinCount.value) || 10
        )

        form.chart_data = {
            labels: labels,
            datasets: [{
                name: 'Frekuensi',
                data: bins
            }],
            rawData: rawValues,
            binMethod: histogramBinMethod.value,
            binCount: bins.length,
            binWidth: binWidth,
            min: min,
            max: max
        }
        chartOptions.value = {}
    } else if (isGroupedStackedBar.value) {
        // Validate Grouped Stacked Bar Chart
        if (groupedStackedData.value.length === 0) {
            notificationTitle.value = 'Data Grouped Stacked Kosong'
            notificationMessage.value = 'Mohon tambahkan minimal satu group'
            showErrorNotif.value = true
            return
        }

        const hasValidData = groupedStackedData.value.every(group => 
            group.groupName && 
            group.subgroups.length > 0 && 
            group.subgroups.every(sg => sg.subgroupName && sg.stacks.length > 0)
        )

        if (!hasValidData) {
            notificationTitle.value = 'Data Grouped Stacked Tidak Lengkap'
            notificationMessage.value = 'Semua group harus memiliki nama, subgroup, dan stack category'
            showErrorNotif.value = true
            return
        }

        // Build the grouped stacked data structure
        const groups = groupedStackedData.value.map(g => g.groupName)
        const allSubgroups = [...new Set(
            groupedStackedData.value.flatMap(g => g.subgroups.map(sg => sg.subgroupName))
        )]
        const allStacks = [...new Set(
            groupedStackedData.value.flatMap(g => 
                g.subgroups.flatMap(sg => sg.stacks.map(s => s.stackName))
            )
        )].filter(Boolean)

        const datasets = []
        allStacks.forEach(stackName => {
            allSubgroups.forEach(subgroupName => {
                const seriesData = groups.map(groupName => {
                    const group = groupedStackedData.value.find(g => g.groupName === groupName)
                    const subgroup = group?.subgroups.find(sg => sg.subgroupName === subgroupName)
                    const stack = subgroup?.stacks.find(s => s.stackName === stackName)
                    return parseFloat(stack?.value) || 0
                })
                
                datasets.push({
                    name: `${subgroupName} - ${stackName}`,
                    group: subgroupName,
                    data: seriesData
                })
            })
        })

        form.chart_data = {
            labels: groups,
            datasets: datasets,
            subgroups: allSubgroups,
            stacks: allStacks,
            rawGroupedData: groupedStackedData.value
        }
        chartOptions.value = {}
    } else if (isHeatmapMatrix.value) {
        // Validate Heatmap Matrix
        if (heatmapRows.value.length === 0) {
            notificationTitle.value = 'Data Heatmap Kosong'
            notificationMessage.value = 'Mohon tambahkan minimal satu baris untuk heatmap'
            showErrorNotif.value = true
            return
        }

        const hasValidRows = heatmapRows.value.every(r => {
            return r.rowName && r.columns.length > 0 && r.columns.every(c => c.value !== '')
        })

        if (!hasValidRows) {
            notificationTitle.value = 'Data Heatmap Tidak Lengkap'
            notificationMessage.value = 'Semua baris harus memiliki nama dan nilai kolom yang lengkap'
            showErrorNotif.value = true
            return
        }

        const categories = heatmapRows.value[0].columns.map(c => c.columnName)
        const seriesData = heatmapRows.value.map(row => ({
            name: row.rowName,
            data: row.columns.map(c => parseFloat(c.value) || 0)
        }))

        form.chart_data = {
            labels: categories,
            datasets: seriesData
        }
        chartOptions.value = {}
    } else if (isDensityPlot.value) {
        // Validate Density Plot (uses same structure as histogram)
        const validCategories = chartCategories.value.filter(c => c.category && c.value)
        
        if (validCategories.length === 0) {
            notificationTitle.value = 'Data Density Plot Kosong'
            notificationMessage.value = 'Mohon isi data untuk density plot'
            showErrorNotif.value = true
            return
        }

        const categories = validCategories.map(c => c.category)
        const values = validCategories.map(c => parseFloat(c.value) || 0)

        form.chart_data = {
            labels: categories,
            datasets: [{
                name: 'Density',
                data: values
            }]
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
    multiSeriesData.value = []
    boxPlotData.value = []
    vennSets.value = []
    vennOverlaps.value = []
    heatmapRows.value = []
    populationPyramidData.value = []
    mapData.value = []
    choroplethData.value = []
    choroplethVariables.value = []
    selectedVariable.value = ''
    variableInterpretations.value = {}
    csvPreviewData.value = []
    csvHeaders.value = []
    mapFile.value = null
    showPreview.value = false
    selectedVisualizationType.value = null
    chartOptions.value = {}
    chartSeries.value = []
    chartKey.value = 0
    groupedStackedData.value = []
    histogramRawData.value = ''
    histogramBinCount.value = 10
    histogramBinMethod.value = 'manual'

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
        choroplethGeojson.value = editing.chart_data?.geojson || null
        
        // Load variable-specific interpretations
        if (editing.chart_data?.interpretations) {
            variableInterpretations.value = editing.chart_data.interpretations
        } else {
            // Parse from combined interpretation if available
            choroplethVariables.value.forEach(variable => {
                variableInterpretations.value[variable] = ''
            })
        }
        
        chartCategories.value = []
    } else if (isMultiSeries.value) {
        // Load multi-series data from saved format
        const labels = editing.chart_data?.labels || []
        const datasets = editing.chart_data?.datasets || []
        
        multiSeriesData.value = datasets.map(dataset => ({
            seriesName: dataset.name || '',
            categories: labels.map((label, idx) => ({
                category: label,
                value: dataset.data?.[idx] ?? ''
            }))
        }))

        if (multiSeriesData.value.length === 0) {
            addSeriesCategory()
        }
        chartCategories.value = []
    } else if (isBoxPlot.value) {
        // Load box plot data
        const datasets = editing.chart_data?.datasets || []
        const boxData = datasets[0]?.data || []
        
        boxPlotData.value = boxData.map(item => ({
            groupName: item.x || '',
            min: item.y?.[0] ?? '',
            q1: item.y?.[1] ?? '',
            median: item.y?.[2] ?? '',
            q3: item.y?.[3] ?? '',
            max: item.y?.[4] ?? ''
        }))

        if (boxPlotData.value.length === 0) {
            addBoxPlotGroup()
        }
        chartCategories.value = []
    } else if (isVennDiagram.value) {
        // Load venn diagram data
        vennSets.value = (editing.chart_data?.vennData?.sets || []).map(s => ({
            name: s.name || '',
            size: s.size ?? ''
        }))
        vennOverlaps.value = (editing.chart_data?.vennData?.overlaps || []).map(o => ({
            sets: o.sets || [],
            size: o.size ?? ''
        }))

        if (vennSets.value.length === 0) {
            addVennSet()
            addVennSet()
        }
        chartCategories.value = []
    } else if (isHistogram.value) {
        // Load histogram data
        const rawData = editing.chart_data?.rawData
        if (rawData && Array.isArray(rawData) && rawData.length > 0) {
            histogramRawData.value = rawData.join(', ')
            histogramBinMethod.value = editing.chart_data?.binMethod || 'manual'
            histogramBinCount.value = editing.chart_data?.binCount || 10
        }
        chartCategories.value = []
    } else if (isGroupedStackedBar.value) {
        // Load grouped stacked bar data
        const rawData = editing.chart_data?.rawGroupedData
        if (rawData && rawData.length > 0) {
            groupedStackedData.value = rawData
        } else {
            addGroupedStackedGroup()
        }
        chartCategories.value = []
    } else if (isHeatmapMatrix.value) {
        // Load heatmap data
        const labels = editing.chart_data?.labels || []
        const datasets = editing.chart_data?.datasets || []
        
        heatmapRows.value = datasets.map(dataset => ({
            rowName: dataset.name || '',
            columns: labels.map((label, idx) => ({
                columnName: label,
                value: dataset.data?.[idx] ?? ''
            }))
        }))

        if (heatmapRows.value.length === 0) {
            addHeatmapRow()
        }
        chartCategories.value = []
    } else if (isDensityPlot.value) {
        // Load density plot data (same format as bar/pie)
        const labels = editing.chart_data?.labels || []
        const dataset = editing.chart_data?.datasets?.[0]?.data || []
        chartCategories.value = labels.map((label, idx) => ({
            category: label,
            value: dataset[idx] ?? ''
        }))

        if (chartCategories.value.length === 0) {
            addCategory()
        }
    } else if (isPopulationPyramid.value) {
        // Load population pyramid data
        const labels = editing.chart_data?.labels || []
        const datasets = editing.chart_data?.datasets || []
        
        const leftDataset = datasets[0] || {}
        const rightDataset = datasets[1] || {}
        
        populationPyramidData.value = labels.map((label, idx) => ({
            ageGroup: label,
            leftValue: leftDataset.data?.[idx] ?? '',
            rightValue: rightDataset.data?.[idx] ?? ''
        }))

        if (populationPyramidData.value.length === 0) {
            addPopulationPyramidRow()
        }
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
    // Check if there's an access error
    if (props.accessError) {
        notificationTitle.value = 'Akses Ditolak'
        notificationMessage.value = props.accessError
        showErrorNotif.value = true
        
        // Redirect to dashboard after 3 seconds
        setTimeout(() => {
            window.location.href = route('dashboard')
        }, 3000)
    } else if (isEditing.value) {
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
    } else if (selectedVisualizationType.value === 'histogram') {
        chartSeries.value = [{
            name: 'Frekuensi',
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
                    horizontal: false,
                    columnWidth: '70%',
                    distributed: false,
                    dataLabels: {
                        position: 'top'
                    }
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
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Kategori'
                }
            },
            yaxis: {
                title: {
                    text: 'Frekuensi'
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
                            <div class="text-sm text-gray-600">Frekuensi: <span class="font-bold text-[#ef874b]">${value}</span></div>
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

                    <!-- Dynamic Form: Histogram -->
                    <div v-if="isHistogram" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Histogram</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Histogram mengelompokkan data numerik ke dalam interval (bins) dan menghitung frekuensi per interval.
                            Masukkan nilai-nilai numerik yang akan dikelompokkan.
                        </p>
                        
                        <!-- Bin Method Selection -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Metode Penentuan Bin</label>
                                <select 
                                    v-model="histogramBinMethod"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="manual">Manual</option>
                                    <option value="sturges">Sturges (log₂(n) + 1)</option>
                                    <option value="sqrt">Square Root (√n)</option>
                                    <option value="freedman">Freedman-Diaconis (IQR)</option>
                                </select>
                            </div>
                            <div v-if="histogramBinMethod === 'manual'">
                                <FormInput
                                    label="Jumlah Bin"
                                    v-model="histogramBinCount"
                                    type="number"
                                    min="2"
                                    max="50"
                                    placeholder="10"
                                />
                            </div>
                        </div>
                        
                        <!-- Raw Data Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">
                                Data Numerik
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="histogramRawData"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-32"
                                placeholder="Masukkan nilai numerik dipisahkan dengan koma, spasi, atau enter.&#10;Contoh: 12, 15, 18, 22, 25, 28, 30, 35, 40..."
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Format: nilai dipisahkan dengan koma, spasi, titik koma, atau baris baru
                            </p>
                        </div>
                        
                        <!-- Data Preview -->
                        <div v-if="parseHistogramData().length > 0" class="p-3 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-700">
                                ✓ {{ parseHistogramData().length }} nilai terdeteksi
                                <span v-if="parseHistogramData().length > 0">
                                    (Min: {{ Math.min(...parseHistogramData()).toFixed(2) }}, 
                                     Max: {{ Math.max(...parseHistogramData()).toFixed(2) }})
                                </span>
                            </p>
                        </div>
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

                    <!-- Dynamic Form: Multi-Series Charts (Stacked, Grouped) -->
                    <div v-if="isMultiSeries" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Multi-Series</h3>
                        <div class="space-y-6">
                            <!-- Series List -->
                            <div v-for="(series, seriesIndex) in multiSeriesData" :key="seriesIndex" 
                                 class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <FormInput
                                        label="Nama Series"
                                        v-model="series.seriesName"
                                        placeholder="Contoh: Produk A"
                                        class="flex-1 mr-3"
                                    />
                                    <button
                                        v-if="multiSeriesData.length > 1"
                                        @click="removeSeriesCategory(seriesIndex)"
                                        type="button"
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition self-end"
                                    >
                                        Hapus Series
                                    </button>
                                </div>

                                <!-- Categories for this series -->
                                <div class="space-y-2 mt-3">
                                    <h4 class="text-sm font-medium text-gray-700">Data Kategori:</h4>
                                    <div v-for="(cat, catIndex) in series.categories" :key="catIndex"
                                         class="flex gap-3 items-end">
                                        <div class="flex-1">
                                            <FormInput
                                                :label="catIndex === 0 ? 'Kategori' : ''"
                                                v-model="cat.category"
                                                placeholder="Nama kategori"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <FormInput
                                                :label="catIndex === 0 ? 'Nilai' : ''"
                                                v-model="cat.value"
                                                type="number"
                                                step="0.01"
                                                placeholder="Nilai"
                                            />
                                        </div>
                                        <button
                                            v-if="series.categories.length > 1"
                                            @click="removeCategoryFromSeries(catIndex)"
                                            type="button"
                                            class="px-3 py-2 bg-red-400 hover:bg-red-500 text-white rounded-lg transition"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                    <button
                                        @click="addCategoryToSeries"
                                        type="button"
                                        class="mt-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition"
                                    >
                                        + Tambah Kategori
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="addSeriesCategory"
                                type="button"
                                class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                            >
                                + Tambah Series Baru
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Form: Grouped Stacked Bar Chart -->
                    <div v-if="isGroupedStackedBar" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Grouped Stacked Bar Chart</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            <strong>Struktur 3 level:</strong><br>
                            • <strong>Group</strong>: Kategori utama (mis: Tahun 2023, 2024)<br>
                            • <strong>Sub-group</strong>: Kategori dalam group (mis: Online, Offline)<br>
                            • <strong>Stack</strong>: Komponen yang di-stack dalam bar (mis: Produk A, B, C)
                        </p>
                        
                        <div class="space-y-6">
                            <!-- Groups -->
                            <div v-for="(group, groupIndex) in groupedStackedData" :key="groupIndex" 
                                 class="border-2 border-blue-200 rounded-lg p-4 bg-blue-50">
                                <div class="flex items-center justify-between mb-4">
                                    <FormInput
                                        label="Nama Group"
                                        v-model="group.groupName"
                                        placeholder="Contoh: 2023, Wilayah A, dll"
                                        class="flex-1 mr-3"
                                    />
                                    <button
                                        v-if="groupedStackedData.length > 1"
                                        @click="removeGroupedStackedGroup(groupIndex)"
                                        type="button"
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition self-end"
                                    >
                                        Hapus Group
                                    </button>
                                </div>

                                <!-- Subgroups within this group -->
                                <div class="space-y-4 ml-4">
                                    <div v-for="(subgroup, subgroupIndex) in group.subgroups" :key="subgroupIndex"
                                         class="border border-green-200 rounded-lg p-3 bg-green-50">
                                        <div class="flex items-center justify-between mb-3">
                                            <FormInput
                                                label="Nama Sub-group"
                                                v-model="subgroup.subgroupName"
                                                placeholder="Contoh: Online, Offline, dll"
                                                class="flex-1 mr-3"
                                            />
                                            <button
                                                v-if="group.subgroups.length > 1"
                                                @click="removeSubgroupFromGroup(groupIndex, subgroupIndex)"
                                                type="button"
                                                class="px-3 py-2 bg-red-400 hover:bg-red-500 text-white rounded-lg transition self-end text-sm"
                                            >
                                                Hapus
                                            </button>
                                        </div>

                                        <!-- Stacks within this subgroup -->
                                        <div class="space-y-2 ml-4">
                                            <h5 class="text-sm font-medium text-gray-600">Stack Categories:</h5>
                                            <div v-for="(stack, stackIndex) in subgroup.stacks" :key="stackIndex"
                                                 class="flex gap-3 items-end">
                                                <div class="flex-1">
                                                    <FormInput
                                                        :label="stackIndex === 0 && subgroupIndex === 0 ? 'Nama Stack' : ''"
                                                        v-model="stack.stackName"
                                                        placeholder="Contoh: Produk A"
                                                        @blur="syncStackNames(groupIndex)"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <FormInput
                                                        :label="stackIndex === 0 && subgroupIndex === 0 ? 'Nilai' : ''"
                                                        v-model="stack.value"
                                                        type="number"
                                                        step="0.01"
                                                        placeholder="Nilai"
                                                    />
                                                </div>
                                                <button
                                                    v-if="subgroup.stacks.length > 1 && subgroupIndex === 0"
                                                    @click="removeStackFromAllSubgroups(groupIndex, stackIndex)"
                                                    type="button"
                                                    class="px-2 py-1 bg-red-300 hover:bg-red-400 text-white rounded transition text-sm"
                                                >
                                                    ×
                                                </button>
                                            </div>
                                            <button
                                                v-if="subgroupIndex === 0"
                                                @click="addStackToAllSubgroups(groupIndex)"
                                                type="button"
                                                class="mt-2 px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg transition"
                                            >
                                                + Tambah Stack Category
                                            </button>
                                        </div>
                                    </div>

                                    <button
                                        @click="addSubgroupToGroup(groupIndex)"
                                        type="button"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition"
                                    >
                                        + Tambah Sub-group
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="addGroupedStackedGroup"
                                type="button"
                                class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                            >
                                + Tambah Group Baru
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Form: Box Plot -->
                    <div v-if="isBoxPlot" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Box Plot</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan 5 nilai statistik untuk setiap kelompok: Minimum, Q1 (Kuartil 1), Median, Q3 (Kuartil 3), Maximum
                        </p>
                        <div class="space-y-4">
                            <div v-for="(group, index) in boxPlotData" :key="index" 
                                 class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <FormInput
                                        label="Nama Kelompok"
                                        v-model="group.groupName"
                                        placeholder="Contoh: Kelompok A"
                                        class="flex-1 mr-3"
                                    />
                                    <button
                                        v-if="boxPlotData.length > 1"
                                        @click="removeBoxPlotGroup(index)"
                                        type="button"
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition self-end"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-5 gap-3">
                                    <FormInput
                                        label="Min"
                                        v-model="group.min"
                                        type="number"
                                        step="0.01"
                                        placeholder="Min"
                                    />
                                    <FormInput
                                        label="Q1"
                                        v-model="group.q1"
                                        type="number"
                                        step="0.01"
                                        placeholder="Q1"
                                    />
                                    <FormInput
                                        label="Median"
                                        v-model="group.median"
                                        type="number"
                                        step="0.01"
                                        placeholder="Median"
                                    />
                                    <FormInput
                                        label="Q3"
                                        v-model="group.q3"
                                        type="number"
                                        step="0.01"
                                        placeholder="Q3"
                                    />
                                    <FormInput
                                        label="Max"
                                        v-model="group.max"
                                        type="number"
                                        step="0.01"
                                        placeholder="Max"
                                    />
                                </div>
                            </div>

                            <button
                                @click="addBoxPlotGroup"
                                type="button"
                                class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                            >
                                + Tambah Kelompok
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Form: Population Pyramid -->
                    <div v-if="isPopulationPyramid" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Population Pyramid</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan kategori dan nilai untuk kedua sisi (contoh: Laki-laki dan Perempuan)
                        </p>
                        
                        <!-- Label Input for Left and Right -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <FormInput
                                label="Label Sisi Kiri"
                                v-model="populationPyramidData[0].leftLabel"
                                placeholder="Contoh: Laki-laki"
                            />
                            <FormInput
                                label="Label Sisi Kanan"
                                v-model="populationPyramidData[0].rightLabel"
                                placeholder="Contoh: Perempuan"
                            />
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div v-for="(row, index) in populationPyramidData" :key="index" 
                                 class="border rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-3 gap-3">
                                    <FormInput
                                        :label="index === 0 ? 'Kategori' : ''"
                                        v-model="row.ageGroup"
                                        placeholder="Contoh: 0-4 tahun, Kategori A, dll"
                                    />
                                    <FormInput
                                        :label="index === 0 ? 'Nilai Kiri' : ''"
                                        v-model="row.leftValue"
                                        type="number"
                                        step="0.01"
                                        placeholder="Nilai"
                                    />
                                    <div class="flex gap-2">
                                        <FormInput
                                            :label="index === 0 ? 'Nilai Kanan' : ''"
                                            v-model="row.rightValue"
                                            type="number"
                                            step="0.01"
                                            placeholder="Nilai"
                                            class="flex-1"
                                        />
                                        <button
                                            v-if="populationPyramidData.length > 1"
                                            @click="removePopulationPyramidRow(index)"
                                            type="button"
                                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition self-end"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="addPopulationPyramidRow"
                            type="button"
                            class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                        >
                            + Tambah Kategori
                        </button>
                    </div>

                    <!-- Dynamic Form: Venn Diagram -->
                    <div v-if="isVennDiagram" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Venn Diagram</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan 2-3 set dan tentukan nilai irisan (overlap) antar set
                        </p>
                        
                        <!-- Sets Input -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Set:</h4>
                            <div class="space-y-3">
                                <div v-for="(set, index) in vennSets" :key="index" 
                                     class="flex gap-3 items-end">
                                    <div class="flex-1">
                                        <FormInput
                                            :label="index === 0 ? 'Nama Set' : ''"
                                            v-model="set.name"
                                            placeholder="Contoh: Grup A"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <FormInput
                                            :label="index === 0 ? 'Ukuran Set' : ''"
                                            v-model="set.size"
                                            type="number"
                                            step="1"
                                            placeholder="Jumlah elemen"
                                        />
                                    </div>
                                    <button
                                        v-if="vennSets.length > 2"
                                        @click="removeVennSet(index)"
                                        type="button"
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            <button
                                v-if="vennSets.length < 3"
                                @click="addVennSet"
                                type="button"
                                class="mt-3 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                            >
                                + Tambah Set
                            </button>
                        </div>

                        <!-- Overlaps Input -->
                        <div v-if="vennOverlaps.length > 0">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Irisan (Overlap):</h4>
                            <div class="space-y-3">
                                <div v-for="(overlap, index) in vennOverlaps" :key="index" 
                                     class="flex gap-3 items-center bg-gray-50 p-3 rounded-lg">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium">{{ overlap.sets.join(' ∩ ') }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <FormInput
                                            v-model="overlap.size"
                                            type="number"
                                            step="1"
                                            placeholder="Jumlah irisan"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Form: Heatmap Matrix -->
                    <div v-if="isHeatmapMatrix" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Heatmap Matrix</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Buat matriks data dengan baris dan kolom, setiap sel berisi nilai numerik
                        </p>
                        
                        <div class="space-y-4">
                            <div v-for="(row, rowIndex) in heatmapRows" :key="rowIndex" 
                                 class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <FormInput
                                        label="Nama Baris"
                                        v-model="row.rowName"
                                        placeholder="Contoh: Produk A"
                                        class="flex-1 mr-3"
                                    />
                                    <button
                                        v-if="heatmapRows.length > 1"
                                        @click="removeHeatmapRow(rowIndex)"
                                        type="button"
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition self-end"
                                    >
                                        Hapus Baris
                                    </button>
                                </div>

                                <div class="grid grid-cols-4 gap-3">
                                    <div v-for="(col, colIndex) in row.columns" :key="colIndex">
                                        <FormInput
                                            :label="rowIndex === 0 ? col.columnName : ''"
                                            v-model="col.value"
                                            type="number"
                                            step="0.01"
                                            :placeholder="col.columnName"
                                        />
                                    </div>
                                </div>

                                <div v-if="rowIndex === 0" class="mt-3 flex gap-2">
                                    <button
                                        @click="addHeatmapColumn"
                                        type="button"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition"
                                    >
                                        + Tambah Kolom
                                    </button>
                                    <button
                                        v-if="heatmapRows[0].columns.length > 1"
                                        @click="removeHeatmapColumn"
                                        type="button"
                                        class="px-3 py-1.5 bg-red-400 hover:bg-red-500 text-white text-sm rounded-lg transition"
                                    >
                                        - Hapus Kolom Terakhir
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="addHeatmapRow"
                                type="button"
                                class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                            >
                                + Tambah Baris
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Form: Density Plot -->
                    <div v-if="isDensityPlot" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Input Data Density Plot</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan titik data (x) dan densitas/probabilitas (y) untuk menggambarkan distribusi
                        </p>
                        <div class="space-y-3">
                            <div 
                                v-for="(category, index) in chartCategories" 
                                :key="index"
                                class="flex gap-3 items-end"
                            >
                                <div class="flex-1">
                                    <FormInput
                                        :label="index === 0 ? 'Titik Data (X)' : ''"
                                        v-model="category.category"
                                        placeholder="Nilai x"
                                    />
                                </div>
                                <div class="flex-1">
                                    <FormInput
                                        :label="index === 0 ? 'Densitas (Y)' : ''"
                                        v-model="category.value"
                                        type="number"
                                        step="0.01"
                                        placeholder="Densitas"
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
                            + Tambah Titik Data
                        </button>
                    </div>

                    <!-- Interpretasi untuk semua tipe visualisasi -->
                    <div v-if="!isChoropleth">
                        <FormTextarea
                            label="Interpretasi"
                            v-model="form.interpretation"
                            :error="form.errors.interpretation"
                            placeholder="Masukkan interpretasi visualisasi"
                            :rows="4"
                            required
                        />
                    </div>
                    
                    <!-- Variable-specific interpretations for Choropleth -->
                    <div v-if="isChoropleth && choroplethVariables.length > 0" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Interpretasi per Variabel</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Setiap variabel memerlukan interpretasi yang spesifik
                        </p>
                        <div class="space-y-4">
                            <div v-for="variable in choroplethVariables" :key="variable" class="border rounded-lg p-4 bg-gray-50">
                                <FormTextarea
                                    :label="`Interpretasi untuk ${variable}`"
                                    v-model="variableInterpretations[variable]"
                                    :placeholder="`Masukkan interpretasi untuk variabel ${variable}`"
                                    :rows="3"
                                    required
                                />
                            </div>
                        </div>
                    </div>

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
                        :key="'simple-chart-' + chartKey"
                        :options="chartOptions" 
                        :series="chartSeries"
                        :type="getChartType(selectedVisualizationType)"
                        height="400"
                    />
                </div>

                <!-- Histogram Preview -->
                <div v-if="isHistogram" class="mb-6 bg-white rounded-lg p-4">
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <strong>Statistik:</strong> 
                            {{ form.chart_data?.rawData?.length || 0 }} data | 
                            {{ form.chart_data?.binCount || 0 }} bins | 
                            Bin width: {{ (form.chart_data?.binWidth || 0).toFixed(2) }} |
                            Range: {{ (form.chart_data?.min || 0).toFixed(2) }} - {{ (form.chart_data?.max || 0).toFixed(2) }}
                        </p>
                    </div>
                    <ApexHistogramChart 
                        :key="'histogram-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Multi-Series Charts Preview -->
                <div v-if="selectedVisualizationType === 'stacked-bar-chart'" class="mb-6 bg-white rounded-lg p-4">
                    <ApexStackedBarChart 
                        :key="'stacked-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <div v-if="selectedVisualizationType === 'grouped-bar-chart'" class="mb-6 bg-white rounded-lg p-4">
                    <ApexGroupedBarChart 
                        :key="'grouped-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <div v-if="selectedVisualizationType === 'grouped-stacked-bar-chart'" class="mb-6 bg-white rounded-lg p-4">
                    <ApexGroupedStackedBarChart 
                        :key="'grouped-stacked-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <div v-if="selectedVisualizationType === '100-stacked-bar-chart'" class="mb-6 bg-white rounded-lg p-4">
                    <ApexStackedBar100Chart 
                        :key="'stacked100-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <div v-if="selectedVisualizationType === 'clustered-bar-chart'" class="mb-6 bg-white rounded-lg p-4">
                    <ApexClusteredBarChart 
                        :key="'clustered-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Population Pyramid Preview -->
                <div v-if="isPopulationPyramid" class="mb-6 bg-white rounded-lg p-4">
                    <ApexPopulationPyramidChart 
                        :key="'pyramid-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Box Plot Preview -->
                <div v-show="isBoxPlot && showPreview" class="mb-6 bg-white rounded-lg p-4">
                    <ApexBoxPlotChart 
                        v-if="form.chart_data && form.chart_data.labels"
                        :key="'boxplot-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Heatmap Matrix Preview -->
                <div v-if="isHeatmapMatrix" class="mb-6 bg-white rounded-lg p-4">
                    <ApexHeatmapChart 
                        :key="'heatmap-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Density Plot Preview -->
                <div v-if="isDensityPlot" class="mb-6 bg-white rounded-lg p-4">
                    <ApexDensityChart 
                        :key="'density-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
                    />
                </div>

                <!-- Venn Diagram Preview -->
                <div v-if="isVennDiagram" class="mb-6 bg-white rounded-lg p-4">
                    <VennDiagramChart 
                        :key="'venn-' + chartKey"
                        :chartData="form.chart_data" 
                        :title="form.title"
                        :height="400"
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
                    
                    <!-- For Choropleth with multiple variables -->
                    <div v-if="isChoropleth && choroplethVariables.length > 1">
                        <div class="mb-4">
                            <h4 class="text-md font-medium mb-2 text-gray-700">Interpretasi untuk: {{ selectedVariable }}</h4>
                            <div class="p-4 bg-gray-50 rounded-lg text-justify leading-relaxed">
                                {{ variableInterpretations[selectedVariable] || 'Belum ada interpretasi untuk variabel ini' }}
                            </div>
                        </div>
                        
                        <!-- Show all interpretations in tabs or accordion -->
                        <details class="border rounded-lg p-4 bg-white">
                            <summary class="cursor-pointer font-medium text-gray-700 hover:text-[#7A2509]">
                                Lihat interpretasi semua variabel
                            </summary>
                            <div class="mt-4 space-y-3">
                                <div v-for="variable in choroplethVariables" :key="variable" class="border-l-4 border-[#7A2509] pl-3">
                                    <h5 class="font-semibold text-gray-800">{{ variable }}</h5>
                                    <p class="text-sm text-gray-600 mt-1">{{ variableInterpretations[variable] || 'Belum ada interpretasi' }}</p>
                                </div>
                            </div>
                        </details>
                    </div>
                    
                    <!-- For other visualizations or single variable choropleth -->
                    <div v-else class="p-4 bg-gray-50 rounded-lg text-justify leading-relaxed">
                        <template v-if="isChoropleth && choroplethVariables.length === 1">
                            {{ variableInterpretations[choroplethVariables[0]] || form.interpretation }}
                        </template>
                        <template v-else>
                            {{ form.interpretation }}
                        </template>
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
