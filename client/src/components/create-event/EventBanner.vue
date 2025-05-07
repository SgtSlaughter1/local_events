<template>
  <div class="event-banner-form">
    <div class="banner-upload">
      <label class="banner-label">Event Image</label>
      <div v-if="form.imageUrl" class="banner-preview">
        <img :src="form.imageUrl" alt="Event Image Preview" />
        <button type="button" class="remove-btn" @click="removeImage">Remove</button>
      </div>
      <div v-if="uploadError" class="upload-error">
        {{ uploadError }}
      </div>
      <div v-if="isUploading" class="upload-progress">
        <div class="progress-bar">
          <div class="progress" :style="{ width: uploadProgress + '%' }"></div>
        </div>
        <span>Uploading... {{ uploadProgress }}%</span>
      </div>
      <input
        type="file"
        accept="image/*"
        @change="onFileChange"
        class="banner-input"
        :disabled="isUploading"
      />
      <div class="banner-hint">Recommended size: 1200x400px. JPG or PNG.</div>
    </div>
    <BaseInput
      v-model="form.imageAlt"
      label="Image Alt Text (optional)"
      placeholder="Describe the image for accessibility"
    />
  </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue'
import BaseInput from '../Base/BaseInput.vue'
import api from '@/services/api'

const props = defineProps({
  modelValue: Object,
})

const emit = defineEmits(['update:modelValue'])

const form = reactive({
  imageUrl: props.modelValue?.image || '',
  imageAlt: props.modelValue?.image_alt || '',
  imageFile: null
})

const isUploading = ref(false)
const uploadProgress = ref(0)
const uploadError = ref('')

async function uploadImage(file) {
  try {
    isUploading.value = true
    uploadError.value = ''
    uploadProgress.value = 0

    const formData = new FormData()
    formData.append('image', file)

    const response = await api.post('/api/events/upload-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        uploadProgress.value = Math.round(
          (progressEvent.loaded * 100) / progressEvent.total
        )
      }
    })

    // Update the form with the returned image URL
    if (response.data && response.data.url) {
      form.imageUrl = response.data.url
      form.imageFile = file
      // Emit the update to parent
      emit('update:modelValue', {
        image: response.data.url,
        image_alt: form.imageAlt
      })
    } else {
      throw new Error('No image URL received from server')
    }
  } catch (error) {
    console.error('Upload error:', error)
    uploadError.value = error.response?.data?.message || 'Failed to upload image'
    form.imageUrl = ''
    form.imageFile = null
  } finally {
    isUploading.value = false
  }
}

async function onFileChange(e) {
  const file = e.target.files[0]
  if (!file) return

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    uploadError.value = 'File size must be less than 2MB'
    return
  }

  // Validate file type
  if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
    uploadError.value = 'Only JPG and PNG files are allowed'
    return
  }

  // Create a preview
    const reader = new FileReader()
    reader.onload = (ev) => {
    form.imageUrl = ev.target.result
    }
    reader.readAsDataURL(file)

  // Upload the file
  await uploadImage(file)
}

function removeImage() {
  form.imageUrl = ''
  form.imageFile = null
  uploadError.value = ''
}

watch(form, (val) => {
  emit('update:modelValue', { 
    image: val.imageUrl,
    image_alt: val.imageAlt
  })
}, { deep: true })
</script>

<style scoped>
.event-banner-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.banner-upload {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.banner-label {
  font-weight: 500;
  margin-bottom: 0.25rem;
  color: var(--text-color);
}

.banner-input {
  margin-top: 0.5rem;
}

.banner-preview {
  margin-bottom: 0.5rem;
  position: relative;
  max-width: 100%;
}

.banner-preview img {
  max-width: 100%;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.remove-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: #fff;
  border: 1px solid #dc3545;
  color: #dc3545;
  border-radius: 4px;
  padding: 0.25rem 0.5rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.remove-btn:hover {
  background: #dc3545;
  color: #fff;
}

.banner-hint {
  font-size: 0.9rem;
  color: #888;
}

.upload-error {
  color: #dc3545;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.upload-progress {
  margin-top: 0.5rem;
}

.progress-bar {
  width: 100%;
  height: 4px;
  background: #e9ecef;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress {
  height: 100%;
  background: #4299e1;
  transition: width 0.3s ease;
}

.upload-progress span {
  font-size: 0.9rem;
  color: #718096;
}
</style>
