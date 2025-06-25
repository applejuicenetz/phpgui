<template>
  <div class="dashboard">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-3">
              <i class="bi bi-speedometer2 me-2"></i>
              Dashboard
            </h4>
            <p class="text-muted mb-0">
              Übersicht über Ihre AppleJuice-Aktivitäten
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Laden...</span>
      </div>
      <p class="mt-3 text-muted">Lade Dashboard-Daten...</p>
    </div>

    <!-- Error State -->
    <div v-if="errorMessage" class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ errorMessage }}
      <button type="button" class="btn-close" @click="errorMessage = ''" aria-label="Close"></button>
    </div>

    <!-- Dashboard Content -->
    <div v-if="!isLoading">
      <!-- Core Information -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-info-circle me-2"></i>
                Core-Information
              </h5>
              <div class="row">
                <div class="col-6">
                  <small class="text-muted">Version</small>
                  <div class="fw-bold">{{ coreData.information.version }}</div>
                </div>
                <div class="col-6">
                  <small class="text-muted">Uptime</small>
                  <div class="fw-bold">{{ coreData.information.uptime }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <small class="text-muted">IP-Adresse</small>
                  <div class="fw-bold">{{ coreData.information.ip }}</div>
                </div>
                <div class="col-6">
                  <small class="text-muted">Port</small>
                  <div class="fw-bold">{{ coreData.information.port }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-server me-2"></i>
                Server-Status
              </h5>
              <div class="row">
                <div class="col-6">
                  <small class="text-muted">Verbunden</small>
                  <div class="fw-bold">
                    <span class="badge bg-success" v-if="coreData.server.connected">
                      <i class="bi bi-check-circle me-1"></i>
                      Ja
                    </span>
                    <span class="badge bg-secondary" v-else>
                      <i class="bi bi-x-circle me-1"></i>
                      Nein
                    </span>
                  </div>
                </div>
                <div class="col-6">
                  <small class="text-muted">Benutzer</small>
                  <div class="fw-bold">{{ formatNumber(coreData.server.users) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-files display-4 text-primary mb-2"></i>
              <h5 class="card-title">{{ formatNumber(coreData.shares.files) }}</h5>
              <p class="card-text text-muted">Geteilte Dateien</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-folder display-4 text-success mb-2"></i>
              <h5 class="card-title">{{ formatNumber(coreData.credits) }}</h5>
              <p class="card-text text-muted">Credits</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-download display-4 text-info mb-2"></i>
              <h5 class="card-title">{{ coreData.downloads.count }}</h5>
              <p class="card-text text-muted">Downloads</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-upload display-4 text-warning mb-2"></i>
              <h5 class="card-title">{{ coreData.uploads.count }}</h5>
              <p class="card-text text-muted">Uploads</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Traffic Information -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-arrow-up-circle me-2"></i>
                Upload-Statistiken
              </h5>
              <div class="row">
                <div class="col-6">
                  <small class="text-muted">Gesamt übertragen</small>
                  <div class="fw-bold">{{ formatFileSize(coreData.traffic.uploadTotal) }}</div>
                </div>
                <div class="col-6">
                  <small class="text-muted">Aktuelle Geschwindigkeit</small>
                  <div class="fw-bold">{{ coreData.traffic.uploadSpeed }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-12">
                  <small class="text-muted">Credits</small>
                  <div class="fw-bold">{{ formatFileSize(coreData.credits.upload) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-arrow-down-circle me-2"></i>
                Download-Statistiken
              </h5>
              <div class="row">
                <div class="col-6">
                  <small class="text-muted">Gesamt übertragen</small>
                  <div class="fw-bold">{{ formatFileSize(coreData.traffic.downloadTotal) }}</div>
                </div>
                <div class="col-6">
                  <small class="text-muted">Aktuelle Geschwindigkeit</small>
                  <div class="fw-bold">{{ coreData.traffic.downloadSpeed }}</div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-12">
                  <small class="text-muted">Credits</small>
                  <div class="fw-bold">{{ formatFileSize(coreData.credits.download) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-download me-2"></i>
                Aktuelle Downloads
              </h5>
              <div v-if="coreData.downloads.items.length === 0" class="text-muted text-center py-3">
                Keine aktiven Downloads
              </div>
              <div v-else>
                <div v-for="download in coreData.downloads.items" :key="download.id" class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-bold">{{ download.filename }}</small>
                    <small class="text-muted">{{ download.progress }}%</small>
                  </div>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar" :style="{ width: download.progress + '%' }"></div>
                  </div>
                  <small class="text-muted">{{ formatFileSize(download.size) }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-upload me-2"></i>
                Aktuelle Uploads
              </h5>
              <div v-if="coreData.uploads.items.length === 0" class="text-muted text-center py-3">
                Keine aktiven Uploads
              </div>
              <div v-else>
                <div v-for="upload in coreData.uploads.items" :key="upload.id" class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-bold">{{ upload.filename }}</small>
                    <small class="text-success">{{ upload.speed }}</small>
                  </div>
                  <small class="text-muted">{{ formatFileSize(upload.size) }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isLoading = ref(false)
const errorMessage = ref('')

// Core data structure
const coreData = ref({
  information: {
    version: '',
    uptime: '',
    networkId: '',
    ip: '',
    port: ''
  },
  shares: {
    files: 0,
    folders: 0,
    totalSize: 0
  },
  credits: {
    upload: 0,
    download: 0
  },
  server: {
    connected: 0,
    users: 0
  },
  traffic: {
    uploadTotal: 0,
    downloadTotal: 0,
    uploadSpeed: '0 KB/s',
    downloadSpeed: '0 KB/s'
  },
  downloads: {
    count: 0,
    items: []
  },
  uploads: {
    count: 0,
    items: []
  }
})

onMounted(() => {
  // Load dashboard data
  loadDashboardData()
})

const loadDashboardData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  console.log('🔄 Loading dashboard data...')
  
  try {
    // Load demo data for now
    loadDemoData()
    
    console.log('✅ Dashboard data loaded successfully')
    
  } catch (error) {
    console.error('❌ Error loading dashboard data:', error)
    errorMessage.value = `Fehler beim Laden der Daten: ${error.message}`
    // Load demo data as fallback
    loadDemoData()
  } finally {
    isLoading.value = false
  }
}

// Load demo data
const loadDemoData = () => {
  // Set demo data to show the interface works
  coreData.value.information.version = '0.30.108 (Demo)'
  coreData.value.information.uptime = '1h 30m'
  coreData.value.information.networkId = 'Demo-Network'
  coreData.value.information.ip = '192.168.1.100'
  coreData.value.information.port = '9851'
  
  // Demo statistics
  coreData.value.shares.files = 1247
  coreData.value.shares.folders = 89
  coreData.value.shares.totalSize = 2847392000 // ~2.8GB
  
  coreData.value.credits.upload = 1500000000 // 1.5GB
  coreData.value.credits.download = 800000000 // 800MB
  
  coreData.value.server.connected = 1
  coreData.value.server.users = 1247
  
  coreData.value.traffic.uploadTotal = 2500000000 // 2.5GB
  coreData.value.traffic.downloadTotal = 1800000000 // 1.8GB
  coreData.value.traffic.uploadSpeed = '45 KB/s'
  coreData.value.traffic.downloadSpeed = '128 KB/s'
  
  // Demo downloads data
  coreData.value.downloads.count = 2
  coreData.value.downloads.items = [
    { id: 1, filename: 'Demo Download 1.zip', size: 1024000000, progress: 45 },
    { id: 2, filename: 'Demo Download 2.mp3', size: 5120000, progress: 78 }
  ]
  
  // Demo uploads data
  coreData.value.uploads.count = 1
  coreData.value.uploads.items = [
    { id: 1, filename: 'Demo Upload.zip', size: 512000000, speed: '25 KB/s' }
  ]
}

// Utility functions
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('de-DE').format(num)
}
</script>

<style scoped>
.dashboard {
  padding: 1rem;
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.progress {
  background-color: #e9ecef;
}

.progress-bar {
  background-color: #0d6efd;
}

.display-4 {
  font-size: 2.5rem;
}

@media (max-width: 768px) {
  .dashboard {
    padding: 0.5rem;
  }
  
  .display-4 {
    font-size: 2rem;
  }
}
</style>