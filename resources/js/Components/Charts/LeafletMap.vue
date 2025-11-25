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

    // Handle point data (markers)
    if (data.pointData && Array.isArray(data.pointData)) {
        addPointMarkers(data.pointData)
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
            const regionName = feature.properties.NAME_2 || feature.properties.NAMOBJ || feature.properties.name
            const value = dataMap[regionName] || 0
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
            const regionName = feature.properties.NAME_2 || feature.properties.NAMOBJ || feature.properties.name
            const value = dataMap[regionName] || 0
            
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
</script>

<template>
    <div class="w-full">
        <div 
            ref="mapContainer" 
            :id="mapId"
            :style="{ height: height + 'px' }"
            class="w-full rounded-lg border border-gray-200 shadow-sm"
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
</style>