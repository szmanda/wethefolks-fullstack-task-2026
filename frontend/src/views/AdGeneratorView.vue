<script setup lang="ts">
import { ref, reactive } from 'vue'
import AdPreview from '../components/AdPreview.vue'

const form = reactive({
  text: '',
  targetUrl: '',
  textColor: '#111827',
  textSize: 24,
  image: null as File | null,
})

const imagePreviewUrl = ref('')
const errors = reactive<Record<string, string[]>>({})
const submitting = ref(false)
const successMessage = ref('')

function onFileChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0] ?? null
  errors.image = []

  if (file) {
    form.image = file
    imagePreviewUrl.value = URL.createObjectURL(file)
  } else {
    form.image = null
    imagePreviewUrl.value = ''
  }
}

async function submitForm() {
  errors.text = []
  errors.targetUrl = []
  errors.image = []
  errors.textColor = []
  errors.textSize = []
  successMessage.value = ''

  if (!form.image) {
    errors.image = ['Please select an image.']
    return
  }

  submitting.value = true

  const formData = new FormData()
  formData.append('text', form.text)
  formData.append('target_url', form.targetUrl)
  formData.append('text_color', form.textColor)
  formData.append('text_size', String(form.textSize))
  formData.append('image', form.image)

  try {
    const response = await fetch('/api/ads', {
      method: 'POST',
      body: formData,
    })

    if (!response.ok) {
      const payload = await response.json().catch(() => null)
      if (payload?.errors) {
        Object.assign(errors, payload.errors)
      } else {
        errors.text = ['Unable to generate the ad package.']
      }
      return
    }

    const blob = await response.blob()
    const objectUrl = window.URL.createObjectURL(blob)
    const anchor = document.createElement('a')
    anchor.href = objectUrl
    anchor.download = 'ad-package.zip'
    anchor.click()
    window.URL.revokeObjectURL(objectUrl)
    successMessage.value = 'Ad package generated successfully. Your download should begin shortly.'
  } catch (error) {
    errors.text = ['Unexpected network error. Please try again later.']
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="page-shell">
    <div class="page-header">
      <h1>Ad Generator</h1>
      <p>Upload an image, choose styling, and download a packaged ad ZIP file with tracking hooks.</p>
    </div>

    <form class="generator-form" @submit.prevent="submitForm">
      <div class="field-group">
        <label for="text">Ad Title</label>
        <input id="text" v-model="form.text" type="text" placeholder="Enter ad title" />
        <p v-if="errors.text?.length" class="field-error">{{ errors.text[0] }}</p>
      </div>

      <div class="field-group">
        <label for="targetUrl">Target URL</label>
        <input id="targetUrl" v-model="form.targetUrl" type="url" placeholder="https://example.com" />
        <p v-if="errors.targetUrl?.length" class="field-error">{{ errors.targetUrl[0] }}</p>
      </div>

      <div class="field-row">
        <div class="field-group half">
          <label for="textColor">Text Color</label>
          <input id="textColor" v-model="form.textColor" type="color" />
          <p v-if="errors.textColor?.length" class="field-error">{{ errors.textColor[0] }}</p>
        </div>

        <div class="field-group half">
          <label for="textSize">Text Size</label>
          <input id="textSize" v-model.number="form.textSize" type="number" min="10" max="100" />
          <p v-if="errors.textSize?.length" class="field-error">{{ errors.textSize[0] }}</p>
        </div>
      </div>

      <div class="field-group">
        <label for="image">Upload Image</label>
        <input id="image" type="file" accept="image/*" @change="onFileChange" />
        <p v-if="errors.image?.length" class="field-error">{{ errors.image[0] }}</p>
      </div>

      <button class="primary-button" type="submit" :disabled="submitting">
        {{ submitting ? 'Generating...' : 'Generate ZIP' }}
      </button>

      <p v-if="successMessage" class="success-text">{{ successMessage }}</p>
    </form>

    <AdPreview
      :text="form.text"
      :image-url="imagePreviewUrl"
      :text-color="form.textColor"
      :text-size="form.textSize"
    />
  </div>
</template>

<style scoped>
.page-shell {
  max-width: 980px;
  margin: 0 auto;
  padding: 24px;
}

.page-header {
  margin-bottom: 24px;
}

.generator-form {
  display: grid;
  gap: 18px;
  border: 1px solid #e2e8f033;
  border-radius: 18px;
  padding: 24px;
  margin-bottom: 24px;
}

.field-group {
  display: grid;
  gap: 8px;
  margin-right: 24px;
}

.field-row {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.field-group label {
  font-weight: 600;
}

.field-group input[type='text'],
.field-group input[type='url'],
.field-group input[type='number'],
.field-group input[type='color'],
.field-group input[type='file'] {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #cbd5e133;
  border-radius: 12px;
  font-size: 1rem;
}

.field-error {
  margin: 0;
  color: #b91c1c;
  font-size: 0.9rem;
}

.primary-button {
  border: none;
  background: #1d4ed8;
  color: white;
  padding: 14px 20px;
  border-radius: 9999px;
  font-size: 1rem;
  cursor: pointer;
}

.primary-button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.success-text {
  margin: 0;
  color: #16a34a;
  font-weight: 600;
}

@media (max-width: 720px) {
  .field-row {
    grid-template-columns: 1fr;
  }
}
</style>
