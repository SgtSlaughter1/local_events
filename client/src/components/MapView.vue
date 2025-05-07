<template>
  <div class="map-container">
    <div id="map" ref="mapContainer" class="map"></div>
  </div>
</template>

<script>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import api from '../services/api';

export default {
  name: 'MapView',
  props: {
    center: {
      type: Array,
      default: () => [0, 0]
    },
    zoom: {
      type: Number,
      default: 13
    },
    markers: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const mapContainer = ref(null);
    let map = null;
    let markers = [];

    const geocodeAddress = async (address) => {
      try {
        if (!address) return null

        // Extract city and country from the address
        const parts = address.split(',')
        const city = parts[0]?.trim()
        const country = parts[1]?.trim()

        if (!city || !country) {
            return null
        }

        const response = await api.get(`/api/geocode?city=${encodeURIComponent(city)}&country=${encodeURIComponent(country)}`)
        return response.data
      } catch (error) {
        return null
      }
    };

    const initMap = async () => {
      try {
        if (!mapContainer.value) return

        map = L.map(mapContainer.value).setView([0, 0], 2)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map)

        // Add markers
        if (props.markers && props.markers.length > 0) {
          for (const marker of props.markers) {
            if (marker.position) {
              // If we have coordinates, use them directly
              const [lat, lng] = marker.position
              L.marker([lat, lng])
                .addTo(map)
                .bindPopup(marker.popup || '')
            } else if (marker.address) {
              // Otherwise try to geocode the address
              const coords = await geocodeAddress(marker.address)
              if (coords) {
                L.marker([coords.latitude, coords.longitude])
                  .addTo(map)
                  .bindPopup(marker.popup || '')
              }
            }
          }
        }

        // Set the view to the center if provided
        if (props.center) {
          map.setView(props.center, props.zoom || 13)
        }
      } catch (error) {
        // Handle error silently
      }
    };

    // Watch for changes in props
    watch(() => props.center, (newCenter) => {
      if (map && newCenter) {
        map.setView(newCenter, props.zoom);
      }
    });

    watch(() => props.markers, async (newMarkers) => {
      if (map) {
        // Remove existing markers
        markers.forEach(marker => marker.remove());
        markers = [];

        // Add new markers
        if (newMarkers && newMarkers.length > 0) {
          for (const marker of newMarkers) {
            if (marker.popup) {
              const coordinates = await geocodeAddress(marker.popup);
              if (coordinates) {
                const newMarker = L.marker([coordinates.latitude, coordinates.longitude])
                  .bindPopup(marker.popup)
                  .addTo(map);
                markers.push(newMarker);
                map.setView([coordinates.latitude, coordinates.longitude], props.zoom);
              }
            }
          }
        }
      }
    });

    onMounted(() => {
      // Initialize map after a short delay to ensure DOM is ready
      setTimeout(() => {
        initMap();
      }, 100);
    });

    onUnmounted(() => {
      if (map) {
        map.remove();
        map = null;
      }
    });

    return {
      mapContainer
    };
  }
};
</script>

<style scoped>
.map-container {
  width: 100%;
  height: 400px;
  position: relative;
  margin: 1rem 0;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  display: block;
}

.map {
  width: 100% !important;
  height: 100% !important;
  z-index: 1;
  background-color: #f8f9fa;
  display: block;
}

.leaflet-container {
  width: 100% !important;
  height: 100% !important;
}
  

/* Ensure Leaflet controls are visible */
:deep(.leaflet-control-container) {
  z-index: 2;
}

:deep(.leaflet-control-zoom) {
  border: 2px solid rgba(0,0,0,0.2);
  background: #fff;
}

:deep(.leaflet-control-attribution) {
  background: rgba(255,255,255,0.8);
}

:deep(.leaflet-container) {
  width: 100% !important;
  height: 100% !important;
}

/* Custom popup styling */
:deep(.leaflet-popup-content-wrapper) {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  padding: 0;
}

:deep(.leaflet-popup-content) {
  margin: 0;
  padding: 12px 16px;
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

:deep(.leaflet-popup-tip) {
  background: #fff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

:deep(.leaflet-popup-close-button) {
  color: #666;
  font-size: 18px;
  padding: 4px 8px;
  right: 0;
  top: 0;
}

:deep(.leaflet-popup-close-button:hover) {
  color: #333;
  background: transparent;
}

/* Custom marker icon */
:deep(.leaflet-marker-icon) {
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}
</style> 