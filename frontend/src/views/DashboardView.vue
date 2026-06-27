<script setup lang="ts">
import { onMounted, ref } from 'vue'

type AdRecord = {
  id: number
  text: string
  target_url: string
  clicks: number
  impressions: number
  created_at: string
}

type Summary = {
  total_ads: number
  total_clicks: number
  total_impressions: number
}

const summary = ref<Summary>({
  total_ads: 0,
  total_clicks: 0,
  total_impressions: 0,
})
const ads = ref<AdRecord[]>([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await fetch('/api/dashboard')
    if (!response.ok) {
      throw new Error('Unable to load dashboard data')
    }

    const payload = await response.json()
    summary.value = payload.summary
    ads.value = payload.ads
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unexpected error'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section class="dashboard-shell">
    <div class="page-heading">
      <h1>Dashboard</h1>
      <p>Track the ads you have generated and how they are performing.</p>
    </div>

    <div v-if="loading" class="status-card">Loading dashboard…</div>
    <div v-else-if="error" class="status-card error">{{ error }}</div>
    <div v-else>
      <div class="summary-grid">
        <article class="summary-card">
          <span class="summary-label">Ads created</span>
          <strong>{{ summary.total_ads }}</strong>
        </article>
        <article class="summary-card">
          <span class="summary-label">Total clicks</span>
          <strong>{{ summary.total_clicks }}</strong>
        </article>
        <article class="summary-card">
          <span class="summary-label">Total impressions</span>
          <strong>{{ summary.total_impressions }}</strong>
        </article>
      </div>

      <div class="table-card">
        <h2>Recent ads</h2>
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Target URL</th>
              <th>Clicks</th>
              <th>Impressions</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ad in ads" :key="ad.id">
              <td>{{ ad.text }}</td>
              <td><a :href="ad.target_url" target="_blank" rel="noopener noreferrer">{{ ad.target_url }}</a></td>
              <td>{{ ad.clicks }}</td>
              <td>{{ ad.impressions }}</td>
              <td>{{ new Date(ad.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<style scoped>
.dashboard-shell {
  display: grid;
  gap: 20px;
}

.page-heading h1 {
  margin: 0 0 6px;
  font-size: 2rem;
}

.page-heading p {
  margin: 0;
  color: #475569;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.summary-card,
.table-card,
.status-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04);
}

.summary-card strong {
  display: block;
  margin-top: 8px;
  font-size: 1.6rem;
}

.summary-label {
  color: #64748b;
  font-size: 0.95rem;
}

.table-card h2 {
  margin-top: 0;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  text-align: left;
  padding: 10px 8px;
  border-bottom: 1px solid #e2e8f0;
}

.error {
  color: #b91c1c;
}
</style>
