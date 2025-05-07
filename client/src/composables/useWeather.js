import { ref } from 'vue';
import api from '@/services/api';

export default function useWeather(event) {
  const weather = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchWeather = async () => {
    if (!event.value || event.value.is_online) {
      return;
    }

    loading.value = true;
    error.value = null;

    try {
      console.log('Fetching weather for:', {
        city: event.value.city,
        date: event.value.start_date
      });

      const { data } = await api.get('/api/weather/forecast', {
        params: {
          city: event.value.city,
          date: event.value.start_date
        }
      });

      console.log('Weather API response:', data);
      
      if (!data || !data.daily) {
        throw new Error('Invalid weather data received');
      }

      weather.value = data;
    } catch (err) {
      console.error('Error fetching weather:', err);
      error.value = err.response?.data?.error || err.message || 'Failed to fetch weather data';
      weather.value = null;
    } finally {
      loading.value = false;
    }
  };

  return { weather, loading, error, fetchWeather };
} 