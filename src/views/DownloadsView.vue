<template>
  <div class="downloads fade-in-up">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="page-title">
            <i class="bi bi-download me-2"></i>
            Downloads
          </h1>
          <p class="page-subtitle text-muted">
            Verwalte deine aktiven und abgeschlossenen Downloads
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="page-actions">
            <button class="btn btn-success me-2" @click="startAllDownloads">
              <i class="bi bi-play-fill me-2"></i>
              Alle starten
            </button>
            <button class="btn btn-warning me-2" @click="pauseAllDownloads">
              <i class="bi bi-pause-fill me-2"></i>
              Alle pausieren
            </button>
            <button class="btn btn-outline-primary" @click="refreshDownloads">
              <i class="bi bi-arrow-clockwise me-2"></i>
              Aktualisieren
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Filter Tabs -->
    <div class="filter-tabs mb-4">
      <ul class="nav nav-pills">
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: activeTab === 'all' }"
            @click="activeTab = 'all'"
          >
            <i class="bi bi-list me-2"></i>
            Alle ({{ allDownloads.length }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: activeTab === 'active' }"
            @click="activeTab = 'active'"
          >
            <i class="bi bi-download me-2"></i>
            Aktiv ({{ activeDownloads.length }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: activeTab === 'completed' }"
            @click="activeTab = 'completed'"
          >
            <i class="bi bi-check-circle me-2"></i>
            Fertig ({{ completedDownloads.length }})
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: activeTab === 'paused' }"
            @click="activeTab = 'paused'"
          >
            <i class="bi bi-pause-circle me-2"></i>
            Pausiert ({{ pausedDownloads.length }})
          </button>
        </li>
      </ul>
    </div>

    <!-- Downloads Table -->
    <div class="downloads-table">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>
                {{ getTabTitle() }}
              </h5>
            </div>
            <div class="col-auto">
              <div class="input-group input-group-sm">
                <span class="input-group-text">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="Suchen..."
                  v-model="searchQuery"
                >
              </div>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 40px;">
                    <input type="checkbox" class="form-check-input" v-model="selectAll" @change="toggleSelectAll">
                  </th>
                  <th>Datei</th>
                  <th style="width: 120px;">Größe</th>
                  <th style="width: 200px;">Fortschritt</th>
                  <th style="width: 100px;">Speed</th>
                  <th style="width: 100px;">ETA</th>
                  <th style="width: 100px;">Status</th>
                  <th style="width: 120px;">Aktionen</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="download in filteredDownloads" :key="download.id" class="download-row">
                  <td>
                    <input type="checkbox" class="form-check-input" v-model="download.selected">
                  </td>
                  <td>
                    <div class="file-info">
                      <div class="file-icon">
                        <i :class="getFileIcon(download.filename)"></i>
                      </div>
                      <div class="file-details">
                        <div class="filename" :title="download.filename">{{ download.filename }}</div>
                        <div class="file-meta">
                          <span class="source-count">{{ download.sources }} Quellen</span>
                          <span class="separator">•</span>
                          <span class="download-time">{{ formatTime(download.startTime) }}</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="file-size">{{ formatSize(download.size) }}</span>
                  </td>
                  <td>
                    <div class="progress-container">
                      <div class="progress mb-1">
                        <div 
                          class="progress-bar" 
                          :class="getProgressBarClass(download.status)"
                          :style="{ width: download.progress + '%' }"
                        ></div>
                      </div>
                      <div class="progress-text">
                        {{ download.progress }}% ({{ formatSize(download.downloaded) }} / {{ formatSize(download.size) }})
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="download-speed" :class="{ 'text-success': download.speed > 0 }">
                      {{ download.speed > 0 ? formatSpeed(download.speed) : '-' }}
                    </span>
                  </td>
                  <td>
                    <span class="eta">{{ download.eta || '-' }}</span>
                  </td>
                  <td>
                    <span class="status-badge" :class="getStatusClass(download.status)">
                      <i :class="getStatusIcon(download.status)" class="me-1"></i>
                      {{ getStatusText(download.status) }}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button 
                        v-if="download.status === 'downloading' || download.status === 'connecting'"
                        class="btn btn-sm btn-warning me-1" 
                        @click="pauseDownload(download.id)"
                        title="Pausieren"
                      >
                        <i class="bi bi-pause-fill"></i>
                      </button>
                      <button 
                        v-else-if="download.status === 'paused' || download.status === 'waiting'"
                        class="btn btn-sm btn-success me-1" 
                        @click="resumeDownload(download.id)"
                        title="Fortsetzen"
                      >
                        <i class="bi bi-play-fill"></i>
                      </button>
                      <button 
                        class="btn btn-sm btn-danger me-1" 
                        @click="cancelDownload(download.id)"
                        title="Abbrechen"
                        :disabled="download.status === 'completed'"
                      >
                        <i class="bi bi-x-lg"></i>
                      </button>
                      <div class="dropdown d-inline">
                        <button 
                          class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                          type="button" 
                          data-bs-toggle="dropdown"
                        >
                          <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#" @click.prevent="showDetails(download)">
                            <i class="bi bi-info-circle me-2"></i>Details
                          </a></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="openFolder(download)">
                            <i class="bi bi-folder-open me-2"></i>Ordner öffnen
                          </a></li>
                          <li v-if="download.status === 'completed'"><a class="dropdown-item" href="#" @click.prevent="openFile(download)">
                            <i class="bi bi-play me-2"></i>Datei öffnen
                          </a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item text-danger" href="#" @click.prevent="removeDownload(download.id)">
                            <i class="bi bi-trash me-2"></i>Entfernen
                          </a></li>
                        </ul>
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

    <!-- Empty State -->
    <div v-if="filteredDownloads.length === 0" class="empty-state">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-download display-1 text-muted mb-3"></i>
          <h4>Keine Downloads gefunden</h4>
          <p class="text-muted">
            {{ searchQuery ? 'Keine Downloads entsprechen deiner Suche.' : 'Du hast noch keine Downloads gestartet.' }}
          </p>
          <button v-if="!searchQuery" class="btn btn-primary" @click="$router.push('/search')">
            <i class="bi bi-search me-2"></i>
            Dateien suchen
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const activeTab = ref('all')
const searchQuery = ref('')
const selectAll = ref(false)

// Demo downloads data
const downloads = ref([
  {
    id: 1,
    filename: 'Ubuntu-22.04.3-desktop-amd64.iso',
    size: 4700000000, // 4.7 GB
    downloaded: 3760000000, // 3.76 GB
    progress: 80,
    speed: 2500000, // 2.5 MB/s
    eta: '5m 23s',
    status: 'downloading',
    sources: 12,
    startTime: new Date(Date.now() - 1800000), // 30 min ago
    selected: false
  },
  {
    id: 2,
    filename: 'The.Movie.2023.1080p.BluRay.x264.mkv',
    size: 8500000000, // 8.5 GB
    downloaded: 8500000000,
    progress: 100,
    speed: 0,
    eta: null,
    status: 'completed',
    sources: 8,
    startTime: new Date(Date.now() - 7200000), // 2 hours ago
    selected: false
  },
  {
    id: 3,
    filename: 'Adobe.Photoshop.2024.v25.0.zip',
    size: 3200000000, // 3.2 GB
    downloaded: 1600000000, // 1.6 GB
    progress: 50,
    speed: 0,
    eta: null,
    status: 'paused',
    sources: 5,
    startTime: new Date(Date.now() - 3600000), // 1 hour ago
    selected: false
  },
  {
    id: 4,
    filename: 'Music.Album.2023.FLAC.rar',
    size: 450000000, // 450 MB
    downloaded: 90000000, // 90 MB
    progress: 20,
    speed: 800000, // 800 KB/s
    eta: '7m 45s',
    status: 'downloading',
    sources: 3,
    startTime: new Date(Date.now() - 900000), // 15 min ago
    selected: false
  },
  {
    id: 5,
    filename: 'Game.Setup.2023.exe',
    size: 15000000000, // 15 GB
    downloaded: 0,
    progress: 0,
    speed: 0,
    eta: null,
    status: 'waiting',
    sources: 15,
    startTime: new Date(),
    selected: false
  },
  {
    id: 6,
    filename: 'Document.Collection.pdf',
    size: 125000000, // 125 MB
    downloaded: 125000000,
    progress: 100,
    speed: 0,
    eta: null,
    status: 'completed',
    sources: 2,
    startTime: new Date(Date.now() - 10800000), // 3 hours ago
    selected: false
  },
  {
    id: 7,
    filename: 'Software.Development.Kit.v2.1.zip',
    size: 890000000, // 890 MB
    downloaded: 267000000, // 267 MB
    progress: 30,
    speed: 0,
    eta: null,
    status: 'connecting',
    sources: 7,
    startTime: new Date(Date.now() - 600000), // 10 min ago
    selected: false
  }
])

// Computed properties
const allDownloads = computed(() => downloads.value)

const activeDownloads = computed(() => 
  downloads.value.filter(d => ['downloading', 'connecting'].includes(d.status))
)

const completedDownloads = computed(() => 
  downloads.value.filter(d => d.status === 'completed')
)

const pausedDownloads = computed(() => 
  downloads.value.filter(d => ['paused', 'waiting'].includes(d.status))
)

const filteredDownloads = computed(() => {
  let filtered = []
  
  switch (activeTab.value) {
    case 'active':
      filtered = activeDownloads.value
      break
    case 'completed':
      filtered = completedDownloads.value
      break
    case 'paused':
      filtered = pausedDownloads.value
      break
    default:
      filtered = allDownloads.value
  }
  
  if (searchQuery.value) {
    filtered = filtered.filter(d => 
      d.filename.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  return filtered
})

const totalSpeed = computed(() => {
  const speed = activeDownloads.value.reduce((sum, d) => sum + d.speed, 0)
  return formatSpeed(speed)
})

const totalSize = computed(() => {
  const size = downloads.value.reduce((sum, d) => sum + d.size, 0)
  return formatSize(size)
})

// Methods
const formatSize = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatSpeed = (bytesPerSecond) => {
  if (bytesPerSecond === 0) return '0 B/s'
  return formatSize(bytesPerSecond) + '/s'
}

const formatTime = (date) => {
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  
  if (hours > 0) {
    return `vor ${hours}h ${minutes % 60}m`
  } else {
    return `vor ${minutes}m`
  }
}

const getFileIcon = (filename) => {
  const ext = filename.split('.').pop().toLowerCase()
  const iconMap = {
    'iso': 'bi bi-disc',
    'mkv': 'bi bi-camera-video',
    'mp4': 'bi bi-camera-video',
    'avi': 'bi bi-camera-video',
    'zip': 'bi bi-file-zip',
    'rar': 'bi bi-file-zip',
    '7z': 'bi bi-file-zip',
    'exe': 'bi bi-gear',
    'msi': 'bi bi-gear',
    'pdf': 'bi bi-file-pdf',
    'doc': 'bi bi-file-word',
    'docx': 'bi bi-file-word',
    'mp3': 'bi bi-music-note',
    'flac': 'bi bi-music-note',
    'wav': 'bi bi-music-note',
    'jpg': 'bi bi-image',
    'png': 'bi bi-image',
    'gif': 'bi bi-image'
  }
  return iconMap[ext] || 'bi bi-file-earmark'
}

const getProgressBarClass = (status) => {
  const classMap = {
    'downloading': 'bg-primary',
    'completed': 'bg-success',
    'paused': 'bg-warning',
    'connecting': 'bg-info',
    'waiting': 'bg-secondary'
  }
  return classMap[status] || 'bg-secondary'
}

const getStatusClass = (status) => {
  const classMap = {
    'downloading': 'badge bg-primary',
    'completed': 'badge bg-success',
    'paused': 'badge bg-warning',
    'connecting': 'badge bg-info',
    'waiting': 'badge bg-secondary'
  }
  return classMap[status] || 'badge bg-secondary'
}

const getStatusIcon = (status) => {
  const iconMap = {
    'downloading': 'bi bi-download',
    'completed': 'bi bi-check-circle',
    'paused': 'bi bi-pause-circle',
    'connecting': 'bi bi-arrow-repeat',
    'waiting': 'bi bi-clock'
  }
  return iconMap[status] || 'bi bi-question-circle'
}

const getStatusText = (status) => {
  const textMap = {
    'downloading': 'Lädt',
    'completed': 'Fertig',
    'paused': 'Pausiert',
    'connecting': 'Verbindet',
    'waiting': 'Wartet'
  }
  return textMap[status] || 'Unbekannt'
}

const getTabTitle = () => {
  const titleMap = {
    'all': 'Alle Downloads',
    'active': 'Aktive Downloads',
    'completed': 'Abgeschlossene Downloads',
    'paused': 'Pausierte Downloads'
  }
  return titleMap[activeTab.value] || 'Downloads'
}

// Actions
const refreshDownloads = () => {
  console.log('Refreshing downloads...')
  // Simulate refresh
}

const startAllDownloads = () => {
  downloads.value.forEach(d => {
    if (['paused', 'waiting'].includes(d.status)) {
      d.status = 'downloading'
      d.speed = Math.floor(Math.random() * 3000000) + 500000 // Random speed
    }
  })
}

const pauseAllDownloads = () => {
  downloads.value.forEach(d => {
    if (['downloading', 'connecting'].includes(d.status)) {
      d.status = 'paused'
      d.speed = 0
    }
  })
}

const pauseDownload = (id) => {
  const download = downloads.value.find(d => d.id === id)
  if (download) {
    download.status = 'paused'
    download.speed = 0
  }
}

const resumeDownload = (id) => {
  const download = downloads.value.find(d => d.id === id)
  if (download) {
    download.status = 'downloading'
    download.speed = Math.floor(Math.random() * 3000000) + 500000
  }
}

const cancelDownload = (id) => {
  if (confirm('Download wirklich abbrechen?')) {
    const index = downloads.value.findIndex(d => d.id === id)
    if (index !== -1) {
      downloads.value.splice(index, 1)
    }
  }
}

const removeDownload = (id) => {
  if (confirm('Download aus der Liste entfernen?')) {
    const index = downloads.value.findIndex(d => d.id === id)
    if (index !== -1) {
      downloads.value.splice(index, 1)
    }
  }
}

const showDetails = (download) => {
  alert(`Details für: ${download.filename}\nGröße: ${formatSize(download.size)}\nQuellen: ${download.sources}`)
}

const openFolder = (download) => {
  console.log('Opening folder for:', download.filename)
}

const openFile = (download) => {
  console.log('Opening file:', download.filename)
}

const toggleSelectAll = () => {
  filteredDownloads.value.forEach(d => {
    d.selected = selectAll.value
  })
}

// Lifecycle
onMounted(() => {
  // Simulate progress updates
  setInterval(() => {
    downloads.value.forEach(download => {
      if (download.status === 'downloading' && download.progress < 100) {
        const increment = Math.random() * 2
        download.progress = Math.min(100, download.progress + increment)
        download.downloaded = Math.floor((download.progress / 100) * download.size)
        
        if (download.progress >= 100) {
          download.status = 'completed'
          download.speed = 0
          download.eta = null
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
  flex-wrap: wrap;
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

/* Filter Tabs */
.filter-tabs {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.nav-pills .nav-link {
  border-radius: 8px;
  padding: 0.75rem 1.25rem;
  margin-right: 0.5rem;
  border: none;
  background: transparent;
  color: #6c757d;
  transition: all 0.2s ease;
}

.nav-pills .nav-link:hover {
  background: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.nav-pills .nav-link.active {
  background: #0d6efd;
  color: white;
}

/* Downloads Table */
.downloads-table .card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  overflow: hidden;
}

.card-header {
  background: rgba(248, 249, 250, 0.8);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  padding: 1rem 1.5rem;
}

.table {
  margin-bottom: 0;
}

.table th {
  border-top: none;
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  color: #495057;
  padding: 1rem 0.75rem;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
  border-top: 1px solid #f1f3f4;
}

.download-row:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

/* File Info */
.file-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.file-icon {
  width: 40px;
  height: 40px;
  background: rgba(13, 110, 253, 0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0d6efd;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.file-details {
  flex: 1;
  min-width: 0;
}

.filename {
  font-weight: 500;
  color: #2d3748;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 300px;
}

.file-meta {
  font-size: 0.8rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.separator {
  margin: 0 0.5rem;
}

/* Progress */
.progress-container {
  min-width: 180px;
}

.progress {
  height: 8px;
  background-color: #e9ecef;
  border-radius: 4px;
}

.progress-bar {
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.75rem;
  color: #6c757d;
  text-align: center;
}

/* Status and Speed */
.file-size {
  font-weight: 500;
  color: #495057;
}

.download-speed {
  font-weight: 500;
  color: #6c757d;
}

.download-speed.text-success {
  color: #198754 !important;
}

.eta {
  font-weight: 500;
  color: #6c757d;
}

.status-badge {
  font-size: 0.75rem;
  padding: 0.35rem 0.65rem;
  border-radius: 6px;
  font-weight: 500;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.action-buttons .btn {
  padding: 0.375rem 0.5rem;
}

/* Empty State */
.empty-state .card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}

/* Responsive */
@media (max-width: 768px) {
  .page-actions {
    justify-content: center;
    margin-top: 1rem;
  }
  
  .page-actions .btn {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
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
  
  .filename {
    max-width: 200px;
  }
  
  .progress-container {
    min-width: 120px;
  }
  
  .action-buttons .btn {
    padding: 0.25rem 0.375rem;
  }
}

@media (max-width: 576px) {
  .table-responsive {
    font-size: 0.85rem;
  }
  
  .filename {
    max-width: 150px;
  }
  
  .progress-container {
    min-width: 100px;
  }
  
  .progress-text {
    font-size: 0.7rem;
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

/* Loading states */
.progress-bar.bg-info {
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}
</style>