<template>
  <div class="app-shell">
    <nav class="top-nav">
      <router-link to="/generator" class="brand">Ad Studio</router-link>
      <div class="nav-links">
        <router-link to="/generator">Generator</router-link>
        <router-link to="/dashboard">Dashboard</router-link>
      </div>
    </nav>

    <main class="page-content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.app-shell {
  min-height: 100vh;
}

.top-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  border-bottom: 1px solid #e2e8f099;
}

.brand {
  font-weight: 700;
  font-size: 1.1rem;
}

.nav-links {
  display: flex;
  gap: 16px;
}

.nav-links a {
  color: #334155;
  font-weight: 600;
}

.nav-links a.router-link-active {
  color: #2563eb;
}

.page-content {
  padding: 24px;
}
</style>
