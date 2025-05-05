<template>
  <div class="event-banner-form">
    <div class="banner-upload">
      <label class="banner-label">Event Banner</label>
      <div v-if="form.bannerUrl" class="banner-preview">
        <img :src="form.bannerUrl" alt="Event Banner Preview" />
        <button type="button" class="remove-btn" @click="removeImage">Remove</button>
      </div>
      <input
        type="file"
        accept="image/*"
        @change="onFileChange"
        class="banner-input"
        :disabled="!!form.bannerUrl"
      />
      <div class="banner-hint">Recommended size: 1200x400px. JPG or PNG.</div>
    </div>
    <BaseInput
      v-model="form.bannerAlt"
      label="Banner Alt Text (optional)"
      placeholder="Describe the banner image for accessibility"
    />
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import BaseInput from '../Base/BaseInput.vue'

const props = defineProps({
  modelValue: Object,
})
const emit = defineEmits(['update:modelValue'])

const form = reactive({
  bannerUrl: props.modelValue?.bannerUrl || '',
  bannerAlt: props.modelValue?.bannerAlt || '',
})

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (ev) => {
      form.bannerUrl = ev.target.result
    }
    reader.readAsDataURL(file)
  }
}

function removeImage() {
  form.bannerUrl = ''
}

watch(form, (val) => {
  emit('update:modelValue', { ...val })
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
}
.banner-hint {
  font-size: 0.9rem;
  color: #888;
}
</style>
