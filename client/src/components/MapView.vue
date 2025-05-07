<template>
  <div class="map-container">
    <div id="map" ref="mapContainer" class="map"></div>
  </div>
</template>

<script>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

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

    const initMap = () => {
      if (!mapContainer.value) {
        return;
      }

      try {
        // Ensure the container is visible
        mapContainer.value.style.display = 'block';
        mapContainer.value.style.visibility = 'visible';
        
        map = L.map(mapContainer.value, {
          center: props.center,
          zoom: props.zoom,
          zoomControl: true,
          attributionControl: true
        });
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 19
        }).addTo(map);

        // Force a resize event after a short delay
        setTimeout(() => {
          map.invalidateSize();
        }, 100);

        // Add markers if provided
        if (props.markers && props.markers.length > 0) {
          props.markers.forEach(marker => {
            const newMarker = L.marker(marker.position)
              .bindPopup(marker.popup || '')
              .addTo(map);
            markers.push(newMarker);
          });
        }
      } catch (error) {
        console.error('Error initializing map:', error);
      }
    };

    // Watch for changes in props
    watch(() => props.center, (newCenter) => {
      if (map && newCenter) {
        map.setView(newCenter, props.zoom);
      }
    });

    watch(() => props.markers, (newMarkers) => {
      if (map) {
        // Remove existing markers
        markers.forEach(marker => marker.remove());
        markers = [];

        // Add new markers
        if (newMarkers && newMarkers.length > 0) {
          newMarkers.forEach(marker => {
            const newMarker = L.marker(marker.position)
              .bindPopup(marker.popup || '')
              .addTo(map);
            markers.push(newMarker);
          });
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