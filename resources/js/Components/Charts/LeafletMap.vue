<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.heat'

const props = defineProps({
    chartData: Object,
    title: String,
    height: { type: [String, Number], default: 400 },
    colors: { type: Array, default: () => ['#ef874b', '#50829b', '#748d63'] }
})

const mapContainer = ref(null)
let map = null
let heatLayer = null
let geojsonLayer = null

const mapId = computed(() => `map-${Math.random().toString(36).substring(2, 9)}`)

// Reactive data for variable selection and visualization
const selectedVar = ref(null)
const availableVariables = ref([])
const currentData = ref(null)
const showLegend = ref(false)
const legendItems = ref([])
const interpretation = ref('')
const currentValues = ref([])

onMounted(() => {
    initializeMap()
})

onUnmounted(() => {
    if (map) {
        map.remove()
        map = null
    }
})

const initializeMap = () => {
    console.log('=== InitializeMap Debug ===')
    console.log('Map container:', mapContainer.value)
    console.log('Props chart data:', props.chartData)
    
    if (!mapContainer.value) return

    // Initialize map centered on DI Yogyakarta
    map = L.map(mapContainer.value).setView([-7.7956, 110.3695], 9)

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map)

    // Load data based on chart data
    if (props.chartData) {
        loadMapData()
    }
}

const loadMapData = () => {
    const data = props.chartData
    console.log('=== LoadMapData Debug ===')
    console.log('Raw chart data:', data)
    console.log('Data type:', typeof data)
    console.log('Is array?', Array.isArray(data))
    console.log('Has point data?', !!(data?.pointData))
    console.log('Has geojson?', !!(data?.geojson))
    console.log('Has loadGeojson?', !!(data?.loadGeojson))
    console.log('Selected variable:', data?.selectedVariable)
    
    if (data?.pointData) {
        console.log('Point data:', data.pointData)
    }

    // Handle data from Excel upload (original system)
    // Data format: [{ lat: x, lng: y, density: z }, ...]
    if (Array.isArray(data) && data.length > 0 && data[0].lat && data[0].lng) {
        addHeatmapLayer(data)
        return
    }

    // Handle standard chart data format (convert to point markers)
    if (data && data.labels && data.datasets && data.datasets[0]) {
        const pointData = data.labels.map((label, index) => ({
            name: label,
            lat: getCoordinatesForRegion(label).lat,
            lng: getCoordinatesForRegion(label).lng,
            value: data.datasets[0].data[index] || 0
        }))
        addPointMarkers(pointData)
        return
    }

    // Handle heatmap data (array of {lat, lng, density})
    if (data.heatmapData && Array.isArray(data.heatmapData)) {
        addHeatmapLayer(data.heatmapData)
    }

    // Handle choropleth data (administrative boundaries with values)
    if (data.choroplethData && data.geojson) {
        addChoroplethLayer(data.geojson, data.choroplethData)
    }

    // Handle saved choropleth data with regions structure
    if (data.regions && data.geojson && data.selectedVariable) {
        console.log('Rendering saved choropleth with regions structure')
        addChoroplethWithGeojson(data.geojson, data.regions, data.selectedVariable)
        availableVariables.value = data.variables || []
        selectedVar.value = data.selectedVariable
        showLegend.value = true
        return
    }

    // Handle point data (markers) - including choropleth point data
    if (data.pointData && Array.isArray(data.pointData)) {
        addPointMarkers(data.pointData)
    }

    // Handle choropleth data with GeoJSON if available
    if (data.geojson && data.pointData) {
        addChoroplethWithGeojson(data.geojson, data.pointData, data.selectedVariable)
        return
    }
    
    // Handle choropleth data with async GeoJSON loading
    if (data.pointData && data.loadGeojson && typeof data.loadGeojson === 'function') {
        console.log('Loading GeoJSON asynchronously...')
        
        // Initialize variables and data
        currentData.value = data
        availableVariables.value = data.variables || []
        selectedVar.value = data.selectedVariable || availableVariables.value[0]
        
        data.loadGeojson().then(geojson => {
            if (geojson) {
                console.log('GeoJSON loaded successfully:', geojson)
                
                // Initial render with first variable
                const initialValueMap = {}
                const initialValues = []
                
                if (data.pointData && Array.isArray(data.pointData) && selectedVar.value) {
                    data.pointData.forEach(point => {
                        if (point && (point.allData || point.value !== undefined)) {
                            const value = point.allData && selectedVar.value ? 
                                parseFloat(point.allData[selectedVar.value]) || 0 : 
                                parseFloat(point.value) || 0
                            initialValueMap[point.id] = value
                            initialValues.push(value)
                        }
                    })
                }
                
                addChoroplethWithGeojson(geojson, data.pointData, selectedVar.value, initialValueMap)
                
                // Initialize legend and interpretation from database
                currentValues.value = initialValues
                legendItems.value = generateLegend(initialValues)
                interpretation.value = generateInterpretation(selectedVar.value, initialValues)
                showLegend.value = true
                
            } else {
                console.warn('Failed to load GeoJSON, falling back to point markers')
                addPointMarkers(data.pointData)
            }
        }).catch(error => {
            console.error('Error loading GeoJSON:', error)
            addPointMarkers(data.pointData)
        })
        return
    }
    
    // Handle choropleth data without GeoJSON (fallback to markers)
    if (data.pointData && Array.isArray(data.pointData) && data.selectedVariable) {
        console.log('Rendering choropleth as point markers')
        addPointMarkers(data.pointData)
        return
    }
}

// Format variable names for display
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

// Generate interpretation based on data
const generateInterpretation = (variable, values) => {
    if (!values || values.length === 0) return ''
    
    const max = Math.max(...values)
    const min = Math.min(...values)
    const avg = values.reduce((a, b) => a + b, 0) / values.length
    
    const interpretations = {
        'population': `Wilayah dengan populasi tertinggi mencapai ${max.toLocaleString()} jiwa, sedangkan terendah ${min.toLocaleString()} jiwa. Rata-rata populasi di DIY adalah ${Math.round(avg).toLocaleString()} jiwa.`,
        'density': `Kepadatan penduduk tertinggi mencapai ${max.toLocaleString()} jiwa/km², sedangkan terendah ${min.toLocaleString()} jiwa/km². Rata-rata kepadatan di DIY adalah ${Math.round(avg).toLocaleString()} jiwa/km².`,
        'area': `Wilayah terluas mencapai ${max.toLocaleString()} km², sedangkan terkecil ${min.toLocaleString()} km². Rata-rata luas wilayah adalah ${Math.round(avg).toLocaleString()} km².`,
        'poverty': `Tingkat kemiskinan tertinggi mencapai ${max.toFixed(1)}%, sedangkan terendah ${min.toFixed(1)}%. Rata-rata tingkat kemiskinan di DIY adalah ${avg.toFixed(1)}%.`,
        'education': `Tingkat pendidikan tertinggi mencapai ${max.toFixed(1)}%, sedangkan terendah ${min.toFixed(1)}%. Rata-rata tingkat pendidikan di DIY adalah ${avg.toFixed(1)}%.`
    }
    
    return interpretations[variable] || `Nilai tertinggi: ${max.toLocaleString()}, terendah: ${min.toLocaleString()}, rata-rata: ${Math.round(avg).toLocaleString()}`
}

// Generate legend items
const generateLegend = (values) => {
    if (!values || values.length === 0) return []
    
    const max = Math.max(...values)
    const min = Math.min(...values)
    const range = max - min
    
    if (range === 0) {
        return [{ color: '#EF874B', range: max.toLocaleString() }]
    }
    
    return [
        { color: '#FFFBDF', range: `${min.toLocaleString()} - ${Math.round(min + range * 0.2).toLocaleString()}` },
        { color: '#FFF4CC', range: `${Math.round(min + range * 0.2).toLocaleString()} - ${Math.round(min + range * 0.4).toLocaleString()}` },
        { color: '#FFEBB3', range: `${Math.round(min + range * 0.4).toLocaleString()} - ${Math.round(min + range * 0.6).toLocaleString()}` },
        { color: '#FFD580', range: `${Math.round(min + range * 0.6).toLocaleString()} - ${Math.round(min + range * 0.8).toLocaleString()}` },
        { color: '#EF874B', range: `${Math.round(min + range * 0.8).toLocaleString()} - ${max.toLocaleString()}` }
    ]
}

// Update visualization when variable changes
const updateVisualization = () => {
    if (!currentData.value || !selectedVar.value) return
    
    const data = currentData.value
    const valueMap = {}
    const values = []
    
    if (data.pointData && Array.isArray(data.pointData)) {
        data.pointData.forEach(point => {
            if (point && point.allData && selectedVar.value) {
                const value = parseFloat(point.allData[selectedVar.value]) || 0
                valueMap[point.id] = value
                values.push(value)
            }
        })
    }
    
    currentValues.value = values
    legendItems.value = generateLegend(values)
    interpretation.value = generateInterpretation(selectedVar.value, values)
    showLegend.value = true
    
    // Re-render the choropleth with new data
    if (data.loadGeojson && typeof data.loadGeojson === 'function') {
        data.loadGeojson().then(geojson => {
            if (geojson) {
                addChoroplethWithGeojson(geojson, data.pointData, selectedVar.value, valueMap)
            }
        }).catch(error => {
            console.error('Error re-loading GeoJSON:', error)
        })
    }
}

// Helper function to get coordinates for DI Yogyakarta regions
const getCoordinatesForRegion = (regionName) => {
    const coordinates = {
        'Kulon Progo': { lat: -7.8, lng: 110.2 },
        'Bantul': { lat: -7.9, lng: 110.4 },
        'Gunung Kidul': { lat: -7.8, lng: 110.65 },
        'Sleman': { lat: -7.6, lng: 110.35 },
        'Yogyakarta': { lat: -7.785, lng: 110.375 },
        'Kota Yogyakarta': { lat: -7.785, lng: 110.375 },
        // Default fallback for any other region names
        'default': { lat: -7.7956, lng: 110.3695 }
    }
    
    return coordinates[regionName] || coordinates['default']
}

const addHeatmapLayer = (heatData) => {
    if (heatLayer) {
        map.removeLayer(heatLayer)
    }

    const heatPoints = heatData.map(point => [point.lat, point.lng, point.density])
    heatLayer = L.heatLayer(heatPoints, {
        radius: 25,
        blur: 15,
        maxZoom: 17,
        gradient: {
            0.2: props.colors[2] || '#748d63',
            0.5: props.colors[1] || '#50829b', 
            0.8: props.colors[0] || '#ef874b'
        }
    }).addTo(map)
}

const addChoroplethLayer = (geojson, data) => {
    if (geojsonLayer) {
        map.removeLayer(geojsonLayer)
    }

    // Create a mapping of region names to values
    const dataMap = {}
    if (data.labels && data.datasets && data.datasets[0]) {
        data.labels.forEach((label, index) => {
            dataMap[label] = data.datasets[0].data[index] || 0
        })
    }

    geojsonLayer = L.geoJSON(geojson, {
        style: (feature) => {
            const regionId = feature.properties.id || feature.properties.ID || feature.properties.name
            const value = dataMap[regionId] || 0
            const maxValue = Math.max(...Object.values(dataMap))
            const opacity = maxValue > 0 ? (value / maxValue) * 0.8 + 0.2 : 0.2

            return {
                fillColor: props.colors[0] || '#ef874b',
                weight: 2,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: opacity
            }
        },
        onEachFeature: (feature, layer) => {
            const regionId = feature.properties.id || feature.properties.ID
            const regionName = feature.properties.name || feature.properties.NAME_2 || feature.properties.NAMOBJ
            const value = dataMap[regionId] || 0
            
            layer.bindPopup(`
                <div class="p-3">
                    <h3 class="font-bold text-gray-800">${regionName}</h3>
                    <p class="text-sm text-gray-600">Nilai: <span class="font-bold text-[#ef874b]">${value}</span></p>
                </div>
            `)
            
            layer.on({
                mouseover: (e) => {
                    const layer = e.target
                    layer.setStyle({
                        weight: 5,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 0.7
                    })
                    layer.bringToFront()
                },
                mouseout: (e) => {
                    geojsonLayer.resetStyle(e.target)
                }
            })
        }
    }).addTo(map)

    // Fit map to GeoJSON bounds
    map.fitBounds(geojsonLayer.getBounds())
}

// Download function for map
const downloadChart = () => {
    if (!map) {
        console.error('Map not initialized')
        return
    }

    // Use leaflet-image or html2canvas to capture map
    import('html2canvas').then(html2canvas => {
        const mapElement = map.getContainer()
        
        html2canvas.default(mapElement, {
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            scale: 2, // Higher quality
            width: mapElement.offsetWidth,
            height: mapElement.offsetHeight
        }).then(canvas => {
            // Create download link
            const link = document.createElement('a')
            link.download = `peta-choropleth-${props.title || 'visualization'}-${new Date().toISOString().split('T')[0]}.png`
            link.href = canvas.toDataURL('image/png')
            link.click()
        }).catch(error => {
            console.error('Error downloading map:', error)
            // Fallback: open map in new window for manual save
            const mapElement = map.getContainer()
            const newWindow = window.open('', '_blank')
            newWindow.document.write(`
                <html>
                    <head><title>Download Peta - ${props.title}</title></head>
                    <body style="margin:0;padding:20px;">
                        <h3>${props.title || 'Peta Choropleth'}</h3>
                        <p>Klik kanan pada peta di bawah dan pilih "Save image as..." untuk download</p>
                        ${mapElement.outerHTML}
                    </body>
                </html>
            `)
        })
    }).catch(error => {
        console.error('html2canvas not available:', error)
        // Simple fallback - print dialog
        window.print()
    })
}

// Expose download function
defineExpose({ downloadChart })

const addPointMarkers = (pointData) => {
    pointData.forEach(point => {
        // Create custom marker with color based on value
        const maxValue = Math.max(...pointData.map(p => p.value))
        const intensity = point.value / maxValue
        const color = intensity > 0.7 ? '#ef874b' : intensity > 0.4 ? '#50829b' : '#748d63'
        
        // Create circle marker instead of default marker
        const marker = L.circleMarker([point.lat, point.lng], {
            radius: 8 + (intensity * 12), // Size based on value
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).bindPopup(`
            <div class="p-3 min-w-[120px]">
                <h4 class="font-bold text-gray-800 mb-1">${point.name || 'Lokasi'}</h4>
                <p class="text-sm text-gray-600">Nilai: <span class="font-bold" style="color: ${color}">${point.value || 0}</span></p>
            </div>
        `)
        
        marker.addTo(map)
        
        // Add hover effects
        marker.on('mouseover', function(e) {
            this.setStyle({
                radius: this.options.radius + 3,
                fillOpacity: 1
            })
        })
        
        marker.on('mouseout', function(e) {
            this.setStyle({
                radius: this.options.radius - 3,
                fillOpacity: 0.8
            })
        })
    })
}

const addChoroplethWithGeojson = (geojson, pointData, selectedVariable, customValueMap = null) => {
    console.log('=== GeoJSON Polygon Debug ===')
    console.log('GeoJSON data:', geojson)
    console.log('Point data:', pointData)
    console.log('Features count:', geojson?.features?.length)
    
    if (geojson && geojson.features) {
        geojson.features.forEach((feature, index) => {
            const coords = feature.geometry.coordinates[0]
            console.log(`Feature ${index + 1}:`, {
                name: feature.properties.name,
                geometryType: feature.geometry.type,
                coordinatesCount: coords.length,
                boundingBox: {
                    minLng: Math.min(...coords.map(c => c[0])),
                    maxLng: Math.max(...coords.map(c => c[0])),
                    minLat: Math.min(...coords.map(c => c[1])),
                    maxLat: Math.max(...coords.map(c => c[1]))
                }
            })
        })
    }

    if (geojsonLayer) {
        map.removeLayer(geojsonLayer)
    }

    // Create mapping from point data or use custom map
    const valueMap = customValueMap || {}
    const values = []
    
    if (!customValueMap) {
        pointData.forEach(point => {
            // Get value directly from point object (flat structure)
            const value = selectedVariable && point[selectedVariable] ? 
                parseFloat(point[selectedVariable]) : 
                parseFloat(point.value) || 0
            valueMap[point.id] = value
            values.push(value)
        })
    } else {
        Object.values(customValueMap).forEach(value => values.push(value))
    }

    const minValue = Math.min(...values)
    const maxValue = Math.max(...values)

    // PKL theme color scale function
    const getColor = (value) => {
        if (maxValue === minValue) return '#ef874b' // PKL orange
        
        const ratio = (value - minValue) / (maxValue - minValue)
        
        // PKL theme colors: light cream to dark brown
        if (ratio < 0.1) return '#FFFBDF'      // Very light cream
        else if (ratio < 0.2) return '#FFF4CC' // Light cream
        else if (ratio < 0.4) return '#FFEBB3' // Cream
        else if (ratio < 0.6) return '#FFD580' // Light orange
        else if (ratio < 0.8) return '#EF874B' // PKL orange
        else return '#7A2509'                   // PKL dark brown
    }

    geojsonLayer = L.geoJSON(geojson, {
        style: (feature) => {
            const regionId = feature.properties.id || feature.properties.ID  
            const regionName = feature.properties.name || feature.properties.NAME_2 || feature.properties.NAMOBJ
            // Try exact match first, then partial match
            let value = valueMap[regionId] || 0
            if (value === 0) {
                // Try to find partial matches for common name variations
                const matchKey = Object.keys(valueMap).find(key => 
                    key.toLowerCase().includes(regionName.toLowerCase()) ||
                    regionName.toLowerCase().includes(key.toLowerCase())
                )
                value = matchKey ? valueMap[matchKey] : 0
            }
            
            return {
                fillColor: getColor(value),
                weight: 2.5,
                opacity: 1,
                color: '#5e1d07',    // Darker PKL brown border
                dashArray: '',
                fillOpacity: 0.75,
                smoothFactor: 1.0    // Better polygon smoothing
            }
        },
        onEachFeature: (feature, layer) => {
            const regionId = feature.properties.id || feature.properties.ID
            const regionName = feature.properties.name || feature.properties.NAME_2 || feature.properties.NAMOBJ
            const value = valueMap[regionId] || 0
            
            layer.bindPopup(`
                <div class="p-3">
                    <h3 class="font-bold text-gray-800">${regionName}</h3>
                    <p class="text-sm text-gray-600">${selectedVariable || 'Nilai'}: <span class="font-bold text-[#3182bd]">${value.toLocaleString()}</span></p>
                </div>
            `)
            
            layer.on({
                mouseover: (e) => {
                    const layer = e.target
                    layer.setStyle({
                        weight: 4,
                        color: '#3d1305',    // Very dark PKL brown on hover
                        dashArray: '',
                        fillOpacity: 0.85
                    })
                    layer.bringToFront()
                },
                mouseout: (e) => {
                    geojsonLayer.resetStyle(e.target)
                }
            })
        }
    }).addTo(map)

    // Fit map to GeoJSON bounds
    map.fitBounds(geojsonLayer.getBounds())
}
</script>

<template>
    <div class="w-full relative">
        <!-- Variable Selector -->
        <div 
            v-if="availableVariables && availableVariables.length > 1" 
            class="variable-selector absolute top-4 left-4 z-50 control-panel rounded-lg shadow-lg border border-gray-200 p-3"
        >
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Variabel:</label>
            <select 
                v-model="selectedVar" 
                @change="updateVisualization" 
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 text-sm"
            >
                <option v-for="variable in availableVariables" :key="variable" :value="variable">
                    {{ formatVariableName(variable) }}
                </option>
            </select>
        </div>

        <!-- Legend -->
        <div 
            v-if="showLegend && legendItems.length > 0" 
            class="legend-container absolute top-4 right-4 z-50 control-panel rounded-lg shadow-lg border border-gray-200 p-4"
        >
            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ formatVariableName(selectedVar) }}</h4>
            <div class="space-y-2">
                <div 
                    v-for="(item, index) in legendItems" 
                    :key="index" 
                    class="flex items-center gap-2 text-xs"
                >
                    <div class="w-4 h-4 rounded border border-gray-300" :style="{ backgroundColor: item.color }"></div>
                    <span class="text-gray-600 whitespace-nowrap">{{ item.range }}</span>
                </div>
            </div>
        </div>

        <!-- Interpretation Panel -->
        <div 
            v-if="interpretation" 
            class="interpretation-panel absolute bottom-4 left-4 right-4 z-50 control-panel rounded-lg shadow-lg border border-gray-200 p-4"
        >
            <h4 class="text-sm font-medium text-gray-700 mb-2">Interpretasi:</h4>
            <p class="text-sm text-gray-600 leading-relaxed">{{ interpretation }}</p>
        </div>

        <div 
            ref="mapContainer" 
            :id="mapId"
            :style="{ height: height + 'px' }"
            class="w-full rounded-lg border border-gray-200 shadow-sm bg-gray-50"
        ></div>
    </div>
</template>

<style scoped>
:deep(.leaflet-container) {
    z-index: 1;
}

:deep(.leaflet-popup-content-wrapper) {
    border-radius: 8px;
}

:deep(.leaflet-popup-content) {
    margin: 0;
}

/* Control panels styling */
.control-panel {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
}

.variable-selector select {
    min-width: 180px;
}

.legend-container {
    min-width: 200px;
}

.interpretation-panel {
    max-width: calc(100% - 2rem);
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .variable-selector {
        top: 0.5rem;
        left: 0.5rem;
        right: 0.5rem;
        width: auto;
    }
    
    .legend-container {
        top: 4rem;
        right: 0.5rem;
        min-width: auto;
    }
    
    .interpretation-panel {
        bottom: 0.5rem;
        left: 0.5rem;
        right: 0.5rem;
    }
}
</style>