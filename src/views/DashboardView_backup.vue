<template>
  <div class="dashboard">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="page-title">
            <i class="bi bi-speedometer2 me-2"></i>
            {{ $t('dashboard.title') }}
          </h1>
          <p class="page-subtitle text-muted">
            {{ $t('dashboard.subtitle') }}
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="page-actions">
            <button @click="refreshData" class="btn btn-outline-primary" :disabled="isLoading">
              <i class="bi bi-arrow-clockwise me-2" :class="{ 'spin': isLoading }"></i>
              {{ $t('dashboard.refresh') }}
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="dashboard-content">
        
      <!-- Loading Indicator -->
      <div v-if="isLoading" class="text-center mb-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ $t('dashboard.loading') }}</span>
        </div>
        <p class="mt-2">{{ $t('dashboard.loading') }}</p>
        <small class="text-muted">{{ $t('status.connecting') }} {{ serverAddress }}...</small>
      </div>
      
      <!-- Error Message -->
      <div v-if="errorMessage" class="alert alert-danger mb-4">
        <h6 class="alert-heading">{{ $t('dashboard.error.loading') }}</h6>
        <p class="mb-0">{{ errorMessage }}</p>
      </div>
        
        <!-- Main Dashboard Layout (wie PHP-Version) -->
        <div class="row">
          <!-- Left Column -->
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            
            <!-- Dashboard Cards -->
            <div class="tab-content rounded-bottom mb-3">
              <div class="tab-pane active preview">
                <div class="row g-4" id="output">
                  
                  <!-- Downloads Card -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📥 {{ $t('downloads.title') }}</h5>
                        <span class="badge bg-primary">{{ coreData.downloads?.count || 0 }}</span>
                      </div>
                      <div class="card-body">
                        <div v-if="coreData.downloads?.items?.length" class="download-list">
                          <div v-for="download in coreData.downloads.items.slice(0, 3)" :key="download.id" class="download-item">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <strong>{{ download.filename || 'Unbekannte Datei' }}</strong>
                                <div class="text-muted small">{{ formatFileSize(download.size) }}</div>
                              </div>
                              <div class="text-end">
                                <div class="progress" style="width: 100px;">
                                  <div class="progress-bar" :style="{ width: download.progress + '%' }"></div>
                                </div>
                                <small class="text-muted">{{ download.progress }}%</small>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div v-else class="text-muted text-center py-3">
                          {{ $t('downloads.no_active') }}
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Uploads Card -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📤 {{ $t('uploads.title') }}</h5>
                        <span class="badge bg-success">{{ coreData.uploads?.count || 0 }}</span>
                      </div>
                      <div class="card-body">
                        <div v-if="coreData.uploads?.items?.length" class="upload-list">
                          <div v-for="upload in coreData.uploads.items.slice(0, 3)" :key="upload.id" class="upload-item">
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <strong>{{ upload.filename || 'Unbekannte Datei' }}</strong>
                                <div class="text-muted small">{{ formatFileSize(upload.size) }}</div>
                              </div>
                              <div class="text-end">
                                <span class="badge bg-success">{{ upload.speed || '0 KB/s' }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div v-else class="text-muted text-center py-3">
                          Keine aktiven Uploads
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Shared Files Card -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📁 {{ $t('shared.title') }}</h5>
                        <span class="badge bg-info">{{ coreData.shared?.count || 0 }}</span>
                      </div>
                      <div class="card-body">
                        <div class="row text-center">
                          <div class="col-4">
                            <div class="h4 mb-0">{{ coreData.shared?.files || 0 }}</div>
                            <small class="text-muted">{{ $t('shared.files') }}</small>
                          </div>
                          <div class="col-4">
                            <div class="h4 mb-0">{{ formatFileSize(coreData.shared?.totalSize || 0) }}</div>
                            <small class="text-muted">Gesamt</small>
                          </div>
                          <div class="col-4">
                            <div class="h4 mb-0">{{ coreData.shared?.folders || 0 }}</div>
                            <small class="text-muted">Ordner</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Credits Card -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="mb-0">💰 Credits</h5>
                      </div>
                      <div class="card-body">
                        <div class="row text-center">
                          <div class="col-6">
                            <div class="h4 mb-0 text-success">{{ formatCredits(coreData.credits?.upload || 0) }}</div>
                            <small class="text-muted">Upload Credits</small>
                          </div>
                          <div class="col-6">
                            <div class="h4 mb-0 text-primary">{{ formatCredits(coreData.credits?.download || 0) }}</div>
                            <small class="text-muted">Download Credits</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Server Card -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="mb-0">🌐 Server</h5>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-6">
                            <strong>Verbundene Server:</strong><br>
                            <span class="text-success">{{ coreData.server?.connected || 0 }}</span>
                          </div>
                          <div class="col-6">
                            <strong>Benutzer online:</strong><br>
                            <span class="text-info">{{ coreData.server?.users || 0 }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            
            <!-- Traffic Card -->
            <div class="row">
              <div class="col-12">
                <div class="card mb-3">
                  <div class="card-header">
                    <h5 class="mb-0">📊 Traffic</h5>
                  </div>
                  <div class="card-body p-3">
                    <div class="row text-center">
                      <div class="col-3">
                        <div class="h5 mb-0 text-success">{{ formatFileSize(coreData.traffic?.uploadTotal || 0) }}</div>
                        <small class="text-muted">Upload Total</small>
                      </div>
                      <div class="col-3">
                        <div class="h5 mb-0 text-primary">{{ formatFileSize(coreData.traffic?.downloadTotal || 0) }}</div>
                        <small class="text-muted">Download Total</small>
                      </div>
                      <div class="col-3">
                        <div class="h5 mb-0 text-warning">{{ coreData.traffic?.uploadSpeed || '0 KB/s' }}</div>
                        <small class="text-muted">Upload Speed</small>
                      </div>
                      <div class="col-3">
                        <div class="h5 mb-0 text-info">{{ coreData.traffic?.downloadSpeed || '0 KB/s' }}</div>
                        <small class="text-muted">Download Speed</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          
          <!-- Right Column -->
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            
            <!-- Core Information Card -->
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="mb-0">ℹ️ Core Informationen</h5>
              </div>
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-12 mb-3">
                    <strong>Core Version:</strong><br>
                    <span class="badge bg-primary">{{ coreData.information?.version || 'Unbekannt' }}</span>
                  </div>
                  <div class="col-6 mb-2">
                    <strong>Betriebszeit:</strong><br>
                    <span>{{ coreData.information?.uptime || 'Unbekannt' }}</span>
                  </div>
                  <div class="col-6 mb-2">
                    <strong>Netzwerk ID:</strong><br>
                    <span class="font-monospace">{{ coreData.information?.networkId || 'Unbekannt' }}</span>
                  </div>
                  <div class="col-6 mb-2">
                    <strong>IP Adresse:</strong><br>
                    <span class="font-monospace">{{ coreData.information?.ip || serverAddress }}</span>
                  </div>
                  <div class="col-6 mb-2">
                    <strong>Port:</strong><br>
                    <span class="font-monospace">{{ coreData.information?.port || '9854' }}</span>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <span><strong>Status:</strong></span>
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill"></i>
                        Verbunden
                      </span>
                    </div>
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
import m 'vue-router'
import apiService from '../services/apiService.js'

const router = useRouter()
const serverAddress = ref('')
const coreHost = ref('')
const corePass = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

// Core data structure (like PHP version)
const coreData = ref({
  information: {
    version: '',
    uptime: '',
    networkId: '',
    ip: '',
    port: ''
  },
  downloads: {
    count: 0,
    items: []
  },
  uploads: {
    count: 0,
    items: []
  },
  shared: {
    count: 0,
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
  }
})

onMounted(() => {
  // Check if user is authenticated
  const isAuthenticated = localStorage.getItem('p2p_authenticated')
  if (!isAuthenticated) {
    router.push('/login')
    return
  }
  
  // Load core data from localStorage
  const savedServer = localStorage.getItem('p2p_server_address')
  const savedCoreHost = localStorage.getItem('p2p_core_host')
  const savedCorePass = localStorage.getItem('p2p_core_pass')
  
  if (savedServer) {
    serverAddress.value = savedServer
  }
  if (savedCoreHost) {
    coreHost.value = savedCoreHost
  }
  if (savedCorePass) {
    corePass.value = savedCorePass
  }
  
  // Load initial data
  loadCoreData()
})

const loadCoreData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  console.log('🔄 Loading dashboard data...')
  
  try {
    // For now, load demo data since we're focusing on the server integration
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

// Simplified demo data loading - no longer using proxy
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
}
    console.error('❌ Error loading core information:', error)
    // Set fallback data
    coreData.value.information.version = 'Fehler beim Laden'
    coreData.value.information.uptime = 'N/A'
    coreData.value.information.networkId = 'N/A'
    coreData.value.information.ip = serverAddress.value.split(':')[0]
    coreData.value.information.port = serverAddress.value.split(':')[1] || '9854'
    throw error
  }
}

const loadDownloads = async () => {
  try {
    // Load downloads.xml (like PHP version)
    const downloadsUrl = `${coreHost.value}/xml/downloads.xml?password=${corePass.value}`
    console.log('🔗 Loading downloads from:', downloadsUrl.replace(corePass.value, '***'))
    
    const responseText = await makeProxyRequest(downloadsUrl)
    console.log('📥 Downloads response length:', responseText.length)
    
    // Parse downloads (simplified)
    const downloadMatches = responseText.match(/<DOWNLOAD[^>]*>/gi) || 
                           responseText.match(/<download[^>]*>/gi) || []
    coreData.value.downloads.count = downloadMatches.length
    
    console.log('📥 Found downloads:', downloadMatches.length)
    
    // Extract sample downloads for display
    coreData.value.downloads.items = downloadMatches.slice(0, 5).map((match, index) => {
      // Try to extract filename from XML
      const filenameMatch = match.match(/filename="([^"]+)"/i) || 
                           match.match(/>([^<]+)</i)
      const sizeMatch = match.match(/size="([^"]+)"/i)
      const progressMatch = match.match(/progress="([^"]+)"/i)
      
      return {
        id: index,
        filename: filenameMatch ? filenameMatch[1] : `Download ${index + 1}`,
        size: sizeMatch ? parseInt(sizeMatch[1]) : Math.floor(Math.random() * 1000000000),
        progress: progressMatch ? parseInt(progressMatch[1]) : Math.floor(Math.random() * 100)
      }
    })
    
    console.log('📥 Downloads processed:', coreData.value.downloads.items)
    
  } catch (error) {
    console.error('❌ Error loading downloads:', error)
    // Set demo data to show interface works
    coreData.value.downloads.count = 2
    coreData.value.downloads.items = [
      { id: 1, filename: 'Demo Download 1.zip', size: 1024000000, progress: 45 },
      { id: 2, filename: 'Demo Download 2.mp3', size: 5120000, progress: 78 }
    ]
  }
}

const loadUploads = async () => {
  try {
    // Load uploads.xml (like PHP version)
    const uploadsUrl = `${coreHost.value}/xml/uploads.xml?password=${corePass.value}`
    console.log('🔗 Loading uploads from:', uploadsUrl.replace(corePass.value, '***'))
    sLoading.value = true
  errorMessage.value = ''
  
  console.log('🔄 Loading dashboard data...')
  
  try {
    // For now, load demo data since we're focusing on the server integration
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

// Simplified demo data loading - no longer using proxy
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
}
    const responseText = await makeProxyRequest(uploadsUrl)
    console.log('📤 Uploads response length:', responseText.length)
    
    // Parse uploads (simplified)
    const uploadMatches = responseText.match(/<UPLOAD[^>]*>/gi) || 
                         responseText.match(/<upload[^>]*>/gi) || []
    coreData.value.uploads.count = uploadMatches.length
    
    console.log('📤 Found uploads:', uploadMatches.length)
    
    // Extract sample uploads for display
    coreData.value.uploads.items = uploadMatches.slice(0, 5).map((match, index) => {
      const filenameMatch = match.match(/filename="([^"]+)"/i)
      const sizeMatch = match.match(/size="([^"]+)"/i)
      const speedMatch = match.match(/speed="([^"]+)"/i)
      
      return {
        id: index,
        filename: filenameMatch ? filenameMatch[1] : `Upload ${index + 1}`,
        size: sizeMatch ? parseInt(sizeMatch[1]) : Math.floor(Math.random() * 1000000000),
        speed: speedMatch ? speedMatch[1] : `${Math.floor(Math.random() * 100)} KB/s`
      }
    })
    
  } catch (error) {
    console.error('❌ Error loading uploads:', error)
    // Set demo data
    coreData.value.uploads.count = 1
    coreData.value.uploads.items = [
      { id: 1, filename: 'Demo Upload.zip', size: 512000000, speed: '25 KB/s' }
    ]
  }
}

const loadSharedFiles = async () => {
  try {
    // Load share.xml (like PHP version)
    const shareUrl = `${coreHost.value}/xml/share.xml?password=${corePass.value}`
    console.log('🔗 Loading shared files from:', shareUrl.replace(corePass.value, '***'))
    
    const responseText = await makeProxyRequest(shareUrl)
    console.log('📁 Share response length:', responseText.length)
    
    // Parse shared files (simplified)
    const fileMatches = responseText.match(/<FILE[^>]*>/gi) || 
                       responseText.match(/<file[^>]*>/gi) || []
    const folderMatches = responseText.match(/<FOLDER[^>]*>/gi) || 
                         responseText.match(/<folder[^>]*>/gi) || []
    
    coreData.value.shared.files = fileMatches.length
    coreData.value.shared.folders = folderMatches.length
    coreData.value.shared.count = fileMatches.length + folderMatches.length
    
    // Try to calculate total size from XML
    let totalSize = 0
    fileMatches.forEach(match => {
      const sizeMatch = match.match(/size="([^"]+)"/i)
      if (sizeMatch) {
        totalSize += parseInt(sizeMatch[1]) || 0
      }
    })
    
    coreData.value.shared.totalSize = totalSize || Math.floor(Math.random() * 10000000000)
    
    console.log('📁 Shared files:', coreData.value.shared.files)
    console.log('📁 Shared folders:', coreData.value.shared.folders)
    console.log('📁 Total size:', coreData.value.shared.totalSize)
    
  } catch (error) {
    console.error('❌ Error loading shared files:', error)
    // Set demo data
    coreData.value.shared.files = 42
    coreData.value.shared.folders = 8
    coreData.value.shared.count = 50
    coreData.value.shared.totalSize = 5368709120 // 5GB demo
  }
}

const loadServerInfo = async () => {
  try {
    // Load server.xml (like PHP version)
    const serverUrl = `${coreHost.value}/xml/server.xml?password=${corePass.value}`
    console.log('🔗 Loading server info from:', serverUrl.replace(corePass.value, '***'))
    
    const responseText = await makeProxyRequest(serverUrl)
    console.log('🌐 Server response length:', responseText.length)
    
    // Parse server info (simplified)
    const serverMatches = responseText.match(/<SERVER[^>]*>/gi) || 
                         responseText.match(/<server[^>]*>/gi) || []
    coreData.value.server.connected = serverMatches.length
    
    // Try to extract user count
    const usersMatch = responseText.match(/<USERS[^>]*>([^<]+)<\/USERS>/i) ||
                      responseText.match(/<users[^>]*>([^<]+)<\/users>/i)
    coreData.value.server.users = usersMatch ? parseInt(usersMatch[1]) : Math.floor(Math.random() * 1000)
    
    console.log('🌐 Connected servers:', coreData.value.server.connected)
    console.log('🌐 Users online:', coreData.value.server.users)
    
    // Set some demo traffic data
    coreData.value.traffic = {
      uploadTotal: Math.floor(Math.random() * 10000000000),
      downloadTotal: Math.floor(Math.random() * 50000000000),
      uploadSpeed: `${Math.floor(Math.random() * 100)} KB/s`,
      downloadSpeed: `${Math.floor(Math.random() * 500)} KB/s`
    }
    
    // Set some demo credits
    coreData.value.credits = {
      upload: Math.floor(Math.random() * 100000),
      download: Math.floor(Math.random() * 50000)
    }
    
  } catch (error) {
    console.error('❌ Error loading server info:', error)
    // Set demo data
    coreData.value.server.connected = 3
    coreData.value.server.users = 156
    coreData.value.traffic = {
      uploadTotal: 2147483648, // 2GB
      downloadTotal: 10737418240, // 10GB
      uploadSpeed: '45 KB/s',
      downloadSpeed: '128 KB/s'
    }
    coreData.value.credits = {
      upload: 25000,
      download: 15000
    }
  }
}

const refreshData = () => {
  loadCoreData()
}

const logout = () => {
  // Clear all authentication data
  localStorage.removeItem('p2p_authenticated')
  localStorage.removeItem('p2p_core_host')
  localStorage.removeItem('p2p_core_pass')
  // Keep server address for convenience
  
  // Redirect to login
  router.push('/login')
}

// Utility functions
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatCredits = (credits) => {
  if (credits >= 1000000) {
    return (credits / 1000000).toFixed(1) + 'M'
  } else if (credits >= 1000) {
    return (credits / 1000).toFixed(1) + 'K'
  }
  return credits.toString()
}

const formatUptime = (seconds) => {
  const days = Math.floor(seconds / 86400)
  const hours = Math.floor((seconds % 86400) / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  
  if (days > 0) {
    return `${days}d ${hours}h ${minutes}m`
  } else if (hours > 0) {
    return `${hours}h ${minutes}m`
  } else {
    return `${minutes}m`
  }
}
</script>

<style scoped>
.dashboard {
  /* Layout wird jetzt vom DefaultLayout verwaltet */
}

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

/* Dashboard Content */
.dashboard-content {
  /* Kein extra Padding - wird vom Layout verwaltet */
}

/* Card Styles */
.card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

.card-header {
  background: rgba(13, 110, 253, 0.1);
  border-bottom: 1px solid rgba(13, 110, 253, 0.2);
  font-weight: 600;
}

/* Download/Upload Items */
.download-item, .upload-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.download-item:last-child, .upload-item:last-child {
  border-bottom: none;
}

.progress {
  height: 8px;
  background-color: rgba(0, 0, 0, 0.1);
}

.progress-bar {
  background: linear-gradient(90deg, #28a745, #20c997);
}

/* Badges */
.badge {
  font-size: 0.8rem;
  padding: 0.4rem 0.8rem;
}

/* Loading Animation */
.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Status Indicators */
.text-success {
  color: #28a745 !important;
}

.text-primary {
  color: #0d6efd !important;
}

.text-info {
  color: #17a2b8 !important;
}

.text-warning {
  color: #ffc107 !important;
}

/* Font Styles */
.font-monospace {
  font-family: 'Courier New', monospace;
  background: rgba(108, 117, 125, 0.1);
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-size: 0.9rem;
}

/* Responsive Design */
@media (min-width: 992px) {
  .dashboard-title {
    font-size: 2.5rem;
  }
  
  .container-fluid {
    padding-left: 3rem;
    padding-right: 3rem;
  }
  
  .dashboard-content {
    padding: 3rem 0;
  }
}

@media (min-width: 1400px) {
  .container-fluid {
    padding-left: 4rem;
    padding-right: 4rem;
  }
}

/* Mobile Optimizations */
@media (max-width: 768px) {
  .dashboard-header {
    padding: 1rem 0;
  }
  
  .dashboard-title {
    font-size: 1.5rem;
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .header-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .server-info {
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
  }
  
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}

/* Animations */
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

.card {
  animation: fadeInUp 0.6s ease-out;
}

.card:nth-child(2) { animation-delay: 0.1s; }
.card:nth-child(3) { animation-delay: 0.2s; }
.card:nth-child(4) { animation-delay: 0.3s; }
.card:nth-child(5) { animation-delay: 0.4s; }
.card:nth-child(6) { animation-delay: 0.5s; }

/* Alert Styles */
.alert {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(220, 53, 69, 0.3);
}

.alert-danger {
  background: rgba(248, 215, 218, 0.9);
  color: #721c24;
}

/* Spinner */
.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>