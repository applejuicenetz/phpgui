<template>
  <div class="settings">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="page-title">
            <i class="bi bi-gear me-2"></i>
            {{ $t('settings.title') }}
          </h1>
          <p class="page-subtitle text-muted">
            Konfiguriere deinen P2P-Core und Web-Interface
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="page-actions">
            <button class="btn btn-outline-primary">
              <i class="bi bi-arrow-clockwise me-2"></i>
              {{ $t('dashboard.refresh') }}
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Content -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="bi bi-gear display-1 text-muted mb-3"></i>
            <h4>Einstellungen Bereich</h4>
            <p class="text-muted">Diese Seite wird bald verfügbar sein.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Settings View - Placeholder
</script>

<style scoped>
/* Page Header */
.page-header {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: var(--cui-primary);
  margin: 0;
  display: flex;
  align-items: center;
}

.page-subtitle {
  font-size: 1.1rem;
  margin: 0.5rem 0 0 0;
  line-height: 1.4;
}

.page-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
</style>