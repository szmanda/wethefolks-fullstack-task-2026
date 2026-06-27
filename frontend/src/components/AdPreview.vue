<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps({
  text: { type: String, required: true },
  imageUrl: { type: String, required: false, default: '' },
  textColor: { type: String, required: true },
  textSize: { type: Number, required: true },
})
</script>

<template>
  <section class="preview-shell">
    <div class="preview-card">
      <div class="preview-image" v-if="imageUrl">
        <img :src="imageUrl" alt="Ad preview image" />
      </div>

      <div class="preview-body">
        <p class="preview-text" :style="{ color: textColor, fontSize: `${textSize}px` }">
          {{ text || 'Preview your ad copy here' }}
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.preview-shell {
  display: flex;
  justify-content: center;
  padding: 16px;
}

.preview-card {
  width: min(100%, 680px);
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  overflow: hidden;
  background: #ffffff;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}

.preview-image img {
  width: 100%;
  display: block;
  object-fit: cover;
  aspect-ratio: 16 / 9;
}

.preview-body {
  padding: 20px;
}

.preview-text {
  margin: 0;
  line-height: 1.2;
  font-weight: 700;
}
</style>
