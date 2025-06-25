<template>
  <div class="uploads fade-in-up">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="page-title">
            <i class="bi bi-upload me-2"></i>
            Uploads
          </h1>
          <p class="page-subtitle text-muted">
            Übersicht über geteilte Dateien und Upload-Statistiken
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="page-actions">
            <button class="btn btn-outline-primary" @click="refreshUploads">
              <i class="bi bi-arrow-clockwise me-2"></i>
              Aktualisieren
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="bi bi-upload"></i>
          </div>
          <div class="stat-content">
            <h3>{{ activeUploads.length }}</h3>
            <p>Aktive Uploads</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="bi bi-clock"></i>
          </div>
          <div class="stat-content">
            <h3>{{ queuedUploads.length }}</h3>
            <p>In Warteschlange</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="bi bi-speedometer2"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalUploadSpeed }}</h3>
            <p>Upload-Speed</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="bi bi-people"></i>
          </div>
          <div class="stat-content">
            <h3>{{ uploadUserPercent }}%</h3>
            <p>Slots belegt</p>
          </div>
        </div>
      </div>
    </div>

    <!-- No Uploads State -->
    <div v-if="allUploads.length === 0" class="empty-state">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-emoji-frown display-1 text-danger mb-3"></i>
          <h4>Keine Uploads zur Zeit!</h4>
          <p class="text-muted">Aktuell werden keine Dateien hochgeladen.</p>
        </div>
      </div>
    </div>

    <!-- Uploads Table -->
    <div v-else class="uploads-table">
      <div class="card">
        <div class="card-body">
          <!-- Upload Limit Info -->
          <div class="upload-limit-info mb-3">
            <div class="alert alert-info d-flex align-items-center">
              <i class="bi bi-info-circle me-2"></i>
              Upload-Limit: {{ uploadUserPercent }}% ({{ uploadUserCount }}/{{ maxUploadPositions }} Slots belegt)
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive">
            <table class="table border mb-0">
              <thead class="fw-semibold text-nowrap">
                <tr class="align-middle">
                  <th class="bg-body-secondary"></th>
                  <th class="bg-body-secondary">Dateien</th>
                  <th class="bg-body-secondary">Status</th>
                  <th class="bg-body-secondary">Fortschritt</th>
                  <th class="bg-body-secondary">Geschwindigkeit</th>
                  <th class="bg-body-secondary"></th>
                </tr>
              </thead>
              <tbody>
                <!-- Active Uploads -->
                <tr v-for="upload in activeUploads" :key="upload.id" class="align-middle">
                  <td>
                    <i :class="getDirectStateIcon(upload.directState)" class="direct-state-icon"></i>
                  </td>
                  <td>
                    <div class="text-nowrap file-name">
                      {{ upload.filename }}
                    </div>
                    <div class="small text-body-secondary text-nowrap file-meta">
                      <span>Benutzer: {{ upload.username }}</span>
                      <span v-if="upload.priorityBonus"> | PDL: ({{ upload.priorityBonus }}) {{ upload.priority }}</span>
                      <span v-else> | PDL: {{ upload.priority }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge" :class="getStatusClass(upload.status)">
                      {{ getStatusText(upload.status) }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex justify-content-between align-items-baseline">
                      <div class="fw-semibold">{{ upload.progress }}%</div>
                      <div class="text-nowrap small text-body-secondary ms-3">
                        {{ formatSize(upload.uploaded) }} - {{ formatSize(upload.size) }}
                      </div>
                    </div>
                    <div class="progress progress-thin">
                      <div 
                        class="progress-bar bg-success" 
                        role="progressbar" 
                        :style="{ width: upload.progress + '%' }"
                        :aria-valuenow="upload.progress"
                        aria-valuemin="0" 
                        aria-valuemax="100"
                      ></div>
                    </div>
                  </td>
                  <td>
                    {{ formatSize(upload.speed) }}/s
                  </td>
                  <td>
                    <div class="dropdown">
                      <button 
                        class="btn btn-transparent p-0" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                      >
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#" @click.prevent="showUploadInfo(upload)">
                          <i class="bi bi-info-circle me-2"></i>Info
                        </a>
                        <a class="dropdown-item" href="#" @click.prevent="editUpload(upload)">
                          <i class="bi bi-pencil me-2"></i>Bearbeiten
                        </a>
                        <a class="dropdown-item text-danger" href="#" @click.prevent="deleteUpload(upload)">
                          <i class="bi bi-trash me-2"></i>Löschen
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>

                <!-- Queued Uploads -->
                <tr v-for="upload in queuedUploads" :key="upload.id" class="align-middle">
                  <td>
                    <i class="bi bi-clock-history direct-state-icon text-warning"></i>
                  </td>
                  <td>
                    <div class="text-nowrap file-name">
                      {{ upload.filename }}
                    </div>
                    <div class="small text-body-secondary text-nowrap file-meta">
                      <span>Benutzer: {{ upload.username }}</span>
                      <span v-if="upload.priorityBonus"> | PDL: ({{ upload.priorityBonus }}) {{ upload.priority }}</span>
                      <span v-else> | PDL: {{ upload.priority }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge" :class="getStatusClass(upload.status)">
                      {{ getStatusText(upload.status) }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex justify-content-between align-items-baseline">
                      <div class="fw-semibold">{{ formatWaitTime(upload.waitTime) }}</div>
                      <div class="text-nowrap small text-body-secondary ms-3">
                        {{ formatSize(upload.size) }}
                      </div>
                    </div>
                    <div class="progress progress-thin">
                      <div 
                        class="progress-bar bg-warning" 
                        role="progressbar" 
                        style="width: 0%" 
                        aria-valuenow="0"
                        aria-valuemin="0" 
                        aria-valuemax="100"
                      ></div>
                    </div>
                  </td>
                  <td>
                    {{ formatSize(upload.speed) }}/s
                  </td>
                  <td>
                    <div class="dropdown">
                      <button 
                        class="btn btn-transparent p-0" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                      >
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#" @click.prevent="showUploadInfo(upload)">
                          <i class="bi bi-info-circle me-2"></i>Info
                        </a>
                        <a class="dropdown-item" href="#" @click.prevent="editUpload(upload)">
                          <i class="bi bi-pencil me-2"></i>Bearbeiten
                        </a>
                        <a class="dropdown-item text-danger" href="#" @click.prevent="deleteUpload(upload)">
                          <i class="bi bi-trash me-2"></i>Löschen
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const maxUploadPositions = ref(10)

// Demo uploads data
const uploads = ref([
  {
    id: 1,
    filename: 'Ubuntu-22.04.3-desktop-amd64.iso',
    username: 'LinuxFan2023',
    size: 4700000000, // 4.7 GB
    uploaded: 2350000000, // 2.35 GB
    progress: 50,
    speed: 1500000, // 1.5 MB/s
    status: 'UPLOAD',
    directState: 'UPLOAD',
    priority: 200,
    priorityBonus: null,
    type: 'active'
  },
  {
    id: 2,
    filename: 'The.Movie.2023.1080p.BluRay.x264.mkv',
    username: 'MovieCollector',
    size: 8500000000, // 8.5 GB
    uploaded: 6800000000, // 6.8 GB
    progress: 80,
    speed: 2200000, // 2.2 MB/s
    status: 'UPLOAD',
    directState: 'UPLOAD',
    priority: 150,
    priorityBonus: 2,
    type: 'active'
  },
  {
    id: 3,
    filename: 'Adobe.Photoshop.2024.v25.0.zip',
    username: 'DesignPro',
    size: 3200000000, // 3.2 GB
    uploaded: 960000000, // 960 MB
    progress: 30,
    speed: 800000, // 800 KB/s
    status: 'UPLOAD',
    directState: 'UPLOAD',
    priority: 100,
    priorityBonus: null,
    type: 'active'
  },
  {
    id: 4,
    filename: 'Music.Album.2023.FLAC.rar',
    username: 'AudioPhile',
    size: 450000000, // 450 MB
    uploaded: 0,
    progress: 0,
    speed: 0,
    status: 'WAIT',
    directState: 'WAIT',
    priority: 120,
    priorityBonus: null,
    waitTime: 180, // 3 minutes
    type: 'queue'
  },
  {
    id: 5,
    filename: 'Game.Setup.2023.exe',
    username: 'GamerXYZ',
    size: 15000000000, // 15 GB
    uploaded: 0,
    progress: 0,
    speed: 0,
    status: 'WAIT',
    directState: 'WAIT',
    priority: 180,
    priorityBonus: 1,
    waitTime: 420, // 7 minutes
    type: 'queue'
  },
  {
    id: 6,
    filename: 'Document.Collection.pdf',
    username: 'StudentHelper',
    size: 125000000, // 125 MB
    uploaded: 0,
    progress: 0,
    speed: 0,
    status: 'WAIT',
    directState: 'WAIT',
    priority: 90,
    priorityBonus: null,
    waitTime: 600, // 10 minutes
    type: 'queue'
  }
])

// Computed properties
const allUploads = computed(() => uploads.value)

const activeUploads = computed(() => 
  uploads.value.filter(u => u.type === 'active')
)

const queuedUploads = computed(() => 
  uploads.value.filter(u => u.type === 'queue')
)

const uploadUserCount = computed(() => activeUploads.value.length)

const uploadUserPercent = computed(() => {
  if (maxUploadPositions.value === 0) return 0
  return Math.round((uploadUserCount.value / maxUploadPositions.value) * 100)
})

const totalUploadSpeed = computed(() => {
  const speed = activeUploads.value.reduce((sum, u) => sum + u.speed, 0)
  return formatSize(speed) + '/s'
})

// Methods
const formatSize = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatWaitTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}min ${remainingSeconds.toString().padStart(2, '0')}s`
}

const getDirectStateIcon = (directState) => {
  const iconMap = {
    'UPLOAD': 'bi bi-arrow-up-circle text-success',
    'WAIT': 'bi bi-clock-history text-warning',
    'CONNECTING': 'bi bi-arrow-repeat text-info',
    'ERROR': 'bi bi-exclamation-triangle text-danger'
  }
  return iconMap[directState] || 'bi bi-question-circle text-secondary'
}

const getStatusClass = (status) => {
  const classMap = {
    'UPLOAD': 'badge bg-success',
    'WAIT': 'badge bg-warning',
    'CONNECTING': 'badge bg-info',
    'ERROR': 'badge bg-danger',
    'PAUSED': 'badge bg-secondary'
  }
  return classMap[status] || 'badge bg-secondary'
}

const getStatusText = (status) => {
  const textMap = {
    'UPLOAD': 'Upload läuft',
    'WAIT': 'Wartet',
    'CONNECTING': 'Verbindet',
    'ERROR': 'Fehler',
    'PAUSED': 'Pausiert'
  }
  return textMap[status] || 'Unbekannt'
}

// Actions
const refreshUploads = () => {
  console.log('Refreshing uploads...')
  // Simulate refresh
}

const showUploadInfo = (upload) => {
  alert(`Upload-Info:\nDatei: ${upload.filename}\nBenutzer: ${upload.username}\nGröße: ${formatSize(upload.size)}\nPriorität: ${upload.priority}`)
}

const editUpload = (upload) => {
  console.log('Editing upload:', upload.filename)
}

const deleteUpload = (upload) => {
  if (confirm(`Upload "${upload.filename}" wirklich löschen?`)) {
    const index = uploads.value.findIndex(u => u.id === upload.id)
    if (index !== -1) {
      uploads.value.splice(index, 1)
    }
  }
}

// Lifecycle
onMounted(() => {
  // Simulate progress updates for active uploads
  setInterval(() => {
    uploads.value.forEach(upload => {
      if (upload.type === 'active' && upload.progress < 100) {
        const increment = Math.random() * 1.5
        upload.progress = Math.min(100, upload.progress + increment)
        upload.uploaded = Math.floor((upload.progress / 100) * upload.size)
        
        if (upload.progress >= 100) {
          // Remove completed upload
          const index = uploads.value.findIndex(u => u.id === upload.id)
          if (index !== -1) {
            uploads.value.splice(index, 1)
          }
        }
      }
      
      // Update wait times for queued uploads
      if (upload.type === 'queue' && upload.waitTime > 0) {
        upload.waitTime = Math.max(0, upload.waitTime - 2)
        
        // Move to active when wait time is up
        if (upload.waitTime === 0 && activeUploads.value.length < maxUploadPositions.value) {
          upload.type = 'active'
          upload.status = 'UPLOAD'
          upload.directState = 'UPLOAD'
          upload.speed = Math.floor(Math.random() * 2000000) + 500000 // Random speed
        }
      }
    })
  }, 2000)
})
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
  color: #0d6efd;
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

/* Statistics Cards */
.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  margin-bottom: 1rem;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.stat-content h3 {
  font-size: 2rem;
  font-weight: bold;
  margin: 0;
  color: #2d3748;
}

.stat-content p {
  margin: 0;
  color: #6c757d;
  font-size: 0.9rem;
}

/* Empty State */
.empty-state .card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}

/* Uploads Table */
.uploads-table .card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  overflow: hidden;
}

.upload-limit-info {
  margin-bottom: 1rem;
}

.upload-limit-info .alert {
  border-radius: 8px;
  border: none;
  background: rgba(13, 202, 240, 0.1);
  color: #0c63e4;
}

/* Table Styles */
.table {
  margin-bottom: 0;
}

.table th {
  border-top: none;
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  color: #495057;
  padding: 1rem 0.75rem;
  background-color: #f8f9fa !important;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
  border-top: 1px solid #f1f3f4;
}

.table tbody tr:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

/* Direct State Icons */
.direct-state-icon {
  font-size: 1.2rem;
  width: 24px;
  text-align: center;
}

/* File Info */
.file-name {
  font-weight: 500;
  color: #2d3748;
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-meta {
  color: #6c757d;
  margin-top: 0.25rem;
}

/* Status Badges */
.status-badge {
  font-size: 0.75rem;
  padding: 0.35rem 0.65rem;
  border-radius: 6px;
  font-weight: 500;
}

/* Progress Bars */
.progress {
  height: 6px;
  background-color: #e9ecef;
  border-radius: 3px;
}

.progress-thin {
  height: 4px;
}

.progress-bar {
  border-radius: 3px;
  transition: width 0.3s ease;
}

/* Dropdown Buttons */
.btn-transparent {
  background: transparent;
  border: none;
  color: #6c757d;
  padding: 0.375rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.btn-transparent:hover {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.dropdown-menu {
  border: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  min-width: 150px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
  transition: background-color 0.2s ease;
  display: flex;
  align-items: center;
}

.dropdown-item:hover {
  background-color: rgba(13, 110, 253, 0.1);
}

.dropdown-item.text-danger:hover {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-actions {
    justify-content: center;
    margin-top: 1rem;
  }
  
  .stat-card {
    padding: 1rem;
  }
  
  .stat-icon {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
  
  .stat-content h3 {
    font-size: 1.5rem;
  }
  
  .file-name {
    max-width: 200px;
  }
  
  .table-responsive {
    font-size: 0.85rem;
  }
}

@media (max-width: 576px) {
  .file-name {
    max-width: 150px;
  }
  
  .file-meta {
    font-size: 0.7rem;
  }
  
  .table th,
  .table td {
    padding: 0.75rem 0.5rem;
  }
}

/* Animations */
.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Progress Animation */
.progress-bar.bg-success {
  background: linear-gradient(45deg, #198754, #20c997) !important;
}

.progress-bar.bg-warning {
  background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
}

/* Loading states */
.progress-bar {
  position: relative;
  overflow: hidden;
}

.progress-bar::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  background-image: linear-gradient(
    -45deg,
    rgba(255, 255, 255, 0.2) 25%,
    transparent 25%,
    transparent 50%,
    rgba(255, 255, 255, 0.2) 50%,
    rgba(255, 255, 255, 0.2) 75%,
    transparent 75%,
    transparent
  );
  background-size: 1rem 1rem;
  animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
  0% {
    background-position: 1rem 0;
  }
  100% {
    background-position: 0 0;
  }
}

/* Upload limit indicator */
.upload-limit-info .alert-info {
  border-left: 4px solid #0dcaf0;
}
</style>