<template>
  <div class="server-status fade-in-up">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="page-title">
            <i class="bi bi-hdd-network me-2"></i>
            Server
          </h1>
          <p class="page-subtitle text-muted">
            Verfügbare Server und Verbindungsstatus verwalten
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="page-actions">
            <button class="btn btn-success me-2" @click="connectToServer" :disabled="isConnecting || isLoading">
              <i class="bi bi-plug me-2" :class="{ 'spin': isConnecting }"></i>
              {{ isConnecting ? 'Verbinde...' : 'Jetzt verbinden' }}
            </button>
            <button class="btn btn-outline-primary" @click="refreshServers" :disabled="isLoading">
              <i class="bi bi-arrow-clockwise me-2" :class="{ 'spin': isLoading }"></i>
              {{ isLoading ? 'Lädt...' : 'Aktualisieren' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>API-Warnung:</strong> {{ error }}
      <button type="button" class="btn-close" @click="error = null"></button>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading && servers.length === 0" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Lädt...</span>
      </div>
      <p class="mt-3 text-muted">Server werden geladen...</p>
    </div>

    <!-- Connection Status -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="connection-status-card">
          <div class="row align-items-center">
            <div class="col-md-8">
              <div class="current-connection">
                <div class="connection-icon" :class="connectionStatusClass">
                  <i :class="connectionStatusIcon"></i>
                </div>
                <div class="connection-details">
                  <h4 class="connection-title">{{ connectionStatusText }}</h4>
                  <p class="connection-info" v-if="currentServer">
                    <strong>{{ currentServer.name }}</strong> - {{ currentServer.address }}:{{ currentServer.port }}
                  </p>
                  <p class="connection-info" v-else>
                    Keine aktive Serververbindung
                  </p>
                  <div class="connection-stats" v-if="isConnected">
                    <span class="stat-item">
                      <i class="bi bi-clock me-1"></i>
                      Verbunden seit: {{ formatUptime(connectionUptime) }}
                    </span>
                    <span class="stat-item">
                      <i class="bi bi-people me-1"></i>
                      {{ currentServer?.users || 0 }} Benutzer
                    </span>
                    <span class="stat-item">
                      <i class="bi bi-files me-1"></i>
                      {{ formatNumber(currentServer?.files || 0) }} Dateien
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-md-end">
              <div class="connection-actions">
                <button 
                  v-if="isConnected" 
                  class="btn btn-danger me-2" 
                  @click="disconnectFromServer"
                  :disabled="isDisconnecting"
                >
                  <i class="bi bi-plug-fill me-2"></i>
                  {{ isDisconnecting ? 'Trenne...' : 'Trennen' }}
                </button>
                <button 
                  v-else 
                  class="btn btn-success me-2" 
                  @click="connectToServer"
                  :disabled="isConnecting"
                >
                  <i class="bi bi-plug me-2"></i>
                  {{ isConnecting ? 'Verbinde...' : 'Verbinden' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="bi bi-check-circle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ availableServers.length }}</h3>
            <p>Verfügbare Server</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="bi bi-wifi"></i>
          </div>
          <div class="stat-content">
            <h3>{{ connectedServers.length }}</h3>
            <p>Verbundene Server</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
          <i class="bi bi-people"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalUsers }}</h3>
            <p>Gesamt-Benutzer</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="bi bi-files"></i>
          </div>
          <div class="stat-content">
            <h3>{{ formatNumber(totalFiles) }}</h3>
            <p>Gesamt-Dateien</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Server List -->
    <div class="server-list">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Verfügbare Server
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
                  placeholder="Server suchen..."
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
                  <th style="width: 40px;">Status</th>
                  <th>Server</th>
                  <th style="width: 120px;">Benutzer</th>
                  <th style="width: 120px;">Dateien</th>
                  <th style="width: 100px;">Ping</th>
                  <th style="width: 120px;">Version</th>
                  <th style="width: 150px;">Aktionen</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="server in filteredServers" :key="server.id" class="server-row">
                  <td>
                    <div class="status-indicator" :class="getServerStatusClass(server.status)">
                      <i :class="getServerStatusIcon(server.status)"></i>
                    </div>
                  </td>
                  <td>
                    <div class="server-info">
                      <div class="server-name" :class="{ 'current-server': server.id === currentServer?.id }">
                        {{ server.name }}
                        <i v-if="server.id === currentServer?.id" class="bi bi-check-circle-fill text-success ms-2"></i>
                      </div>
                      <div class="server-address">
                        {{ server.address }}:{{ server.port }}
                      </div>
                      <div class="server-location" v-if="server.location">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ server.location }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-count">{{ formatNumber(server.users) }}</span>
                  </td>
                  <td>
                    <span class="file-count">{{ formatNumber(server.files) }}</span>
                  </td>
                  <td>
                    <span class="ping" :class="getPingClass(server.ping)">
                      {{ server.ping }}ms
                    </span>
                  </td>
                  <td>
                    <span class="version">{{ server.version }}</span>
                  </td>
                  <td>
                    <div class="server-actions">
                      <button 
                        v-if="server.id !== currentServer?.id"
                        class="btn btn-sm btn-success me-1" 
                        @click="connectToSpecificServer(server)"
                        :disabled="isConnecting"
                        title="Verbinden"
                      >
                        <i class="bi bi-plug"></i>
                      </button>
                      <button 
                        v-else
                        class="btn btn-sm btn-danger me-1" 
                        @click="disconnectFromServer"
                        :disabled="isDisconnecting"
                        title="Trennen"
                      >
                        <i class="bi bi-plug-fill"></i>
                      </button>
                      <button 
                        class="btn btn-sm btn-outline-primary me-1" 
                        @click="pingServer(server)"
                        title="Ping testen"
                      >
                        <i class="bi bi-wifi"></i>
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
                          <li><a class="dropdown-item" href="#" @click.prevent="showServerInfo(server)">
                            <i class="bi bi-info-circle me-2"></i>Server-Info
                          </a></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="testConnection(server)">
                            <i class="bi bi-speedometer2 me-2"></i>Verbindung testen
                          </a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="#" @click.prevent="addToFavorites(server)">
                            <i class="bi bi-star me-2"></i>Zu Favoriten
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

    <!-- Add Server Modal -->
    <div class="modal fade" id="addServerModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-plus-circle me-2"></i>
              Server hinzufügen
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addNewServer">
              <div class="mb-3">
                <label for="serverName" class="form-label">Server-Name</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="serverName"
                  v-model="newServer.name"
                  placeholder="Mein Server"
                  required
                >
              </div>
              <div class="mb-3">
                <label for="serverAddress" class="form-label">Adresse</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="serverAddress"
                  v-model="newServer.address"
                  placeholder="server.example.com"
                  required
                >
              </div>
              <div class="mb-3">
                <label for="serverPort" class="form-label">Port</label>
                <input 
                  type="number" 
                  class="form-control" 
                  id="serverPort"
                  v-model="newServer.port"
                  placeholder="9851"
                  min="1"
                  max="65535"
                  required
                >
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Abbrechen
            </button>
            <button type="button" class="btn btn-primary" @click="addNewServer">
              <i class="bi bi-plus me-2"></i>
              Server hinzufügen
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Server Button (Floating) -->
    <button 
      class="btn btn-primary btn-floating" 
      data-bs-toggle="modal" 
      data-bs-target="#addServerModal"
      title="Server hinzufügen"
    >
      <i class="bi bi-plus-lg"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import apiService from '../services/apiService'

// Reactive data
const searchQuery = ref('')
const isConnecting = ref(false)
const isDisconnecting = ref(false)
const isLoading = ref(true)
const error = ref(null)
const connectionUptime = ref(0)
const currentServerId = ref(null) // Currently connected server

// New server form
const newServer = ref({
  name: '',
  address: '',
  port: 9851
})

// Servers data (will be loaded from API)
const servers = ref([
  {
    id: 1,
    name: 'AppleJuice Server DE',
    address: 'server1.applejuice.eu',
    port: 9851,
    status: 'connected',
    users: 1247,
    files: 2847392,
    ping: 23,
    version: '0.30.108',
    location: 'Deutschland',
    uptime: 3600000 // 1 hour
  },
  {
    id: 2,
    name: 'AppleJuice Server US',
    address: 'us.applejuice.net',
    port: 9851,
    status: 'available',
    users: 892,
    files: 1923847,
    ping: 156,
    version: '0.30.105',
    location: 'USA',
    uptime: 0
  },
  {
    id: 3,
    name: 'AppleJuice Server FR',
    address: 'fr.applejuice.org',
    port: 9851,
    status: 'available',
    users: 634,
    files: 1456789,
    ping: 67,
    version: '0.30.108',
    location: 'Frankreich',
    uptime: 0
  },
  {
    id: 4,
    name: 'AppleJuice Server UK',
    address: 'uk.applejuice.co.uk',
    port: 9851,
    status: 'available',
    users: 423,
    files: 987654,
    ping: 89,
    version: '0.30.107',
    location: 'Großbritannien',
    uptime: 0
  },
  {
    id: 5,
    name: 'AppleJuice Server NL',
    address: 'nl.applejuice.nl',
    port: 9851,
    status: 'busy',
    users: 1500,
    files: 3456789,
    ping: 45,
    version: '0.30.108',
    location: 'Niederlande',
    uptime: 0
  },
  {
    id: 6,
    name: 'AppleJuice Server AT',
    address: 'at.applejuice.at',
    port: 9851,
    status: 'offline',
    users: 0,
    files: 0,
    ping: 999,
    version: '0.30.104',
    location: 'Österreich',
    uptime: 0
  }
])

// Computed properties
const filteredServers = computed(() => {
  if (!searchQuery.value) return servers.value
  
  return servers.value.filter(server => 
    server.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    server.address.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    server.location.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const currentServer = computed(() => 
  servers.value.find(s => s.id === currentServerId.value)
)

const isConnected = computed(() => 
  currentServer.value?.status === 'connected'
)

const availableServers = computed(() => 
  servers.value.filter(s => s.status !== 'offline')
)

const connectedServers = computed(() => 
  servers.value.filter(s => s.status === 'connected')
)

const totalUsers = computed(() => 
  servers.value.reduce((sum, s) => sum + s.users, 0)
)

const totalFiles = computed(() => 
  servers.value.reduce((sum, s) => sum + s.files, 0)
)

const connectionStatusClass = computed(() => {
  if (isConnected.value) return 'connected'
  if (isConnecting.value) return 'connecting'
  return 'disconnected'
})

const connectionStatusIcon = computed(() => {
  if (isConnected.value) return 'bi bi-wifi'
  if (isConnecting.value) return 'bi bi-arrow-repeat spin'
  return 'bi bi-wifi-off'
})

const connectionStatusText = computed(() => {
  if (isConnected.value) return 'Verbunden'
  if (isConnecting.value) return 'Verbinde...'
  return 'Nicht verbunden'
})

// Methods
const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}

const formatUptime = (milliseconds) => {
  const seconds = Math.floor(milliseconds / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  
  if (days > 0) return `${days}d ${hours % 24}h`
  if (hours > 0) return `${hours}h ${minutes % 60}m`
  if (minutes > 0) return `${minutes}m ${seconds % 60}s`
  return `${seconds}s`
}

const getServerStatusClass = (status) => {
  const classMap = {
    'connected': 'status-connected',
    'available': 'status-available',
    'busy': 'status-busy',
    'offline': 'status-offline'
  }
  return classMap[status] || 'status-offline'
}

const getServerStatusIcon = (status) => {
  const iconMap = {
    'connected': 'bi bi-check-circle-fill',
    'available': 'bi bi-circle-fill',
    'busy': 'bi bi-exclamation-circle-fill',
    'offline': 'bi bi-x-circle-fill'
  }
  return iconMap[status] || 'bi bi-question-circle-fill'
}

const getPingClass = (ping) => {
  if (ping < 50) return 'ping-excellent'
  if (ping < 100) return 'ping-good'
  if (ping < 200) return 'ping-fair'
  return 'ping-poor'
}

// API Methods
const fetchServers = async () => {
  try {
    isLoading.value = true
    error.value = null
    
    console.log('🔄 Fetching servers from API...')
    const response = await apiService.getServers()
    
    if (response.success) {
      servers.value = response.data.servers
      console.log(`✅ Loaded ${response.data.total} servers`)
    } else {
      throw new Error('API returned unsuccessful response')
    }
  } catch (err) {
    console.error('❌ Failed to fetch servers:', err)
    error.value = err.message
    
    // Keep existing demo data if API fails
    if (servers.value.length === 0) {
      loadDemoData()
    }
  } finally {
    isLoading.value = false
  }
}

const loadDemoData = () => {
  console.log('📋 Loading demo data as fallback...')
  // Keep existing demo servers as fallback
}

// Actions
const refreshServers = async () => {
  console.log('🔄 Refreshing servers...')
  await fetchServers()
}

const connectToServer = () => {
  // Connect to best available server
  const bestServer = availableServers.value
    .filter(s => s.status === 'available')
    .sort((a, b) => a.ping - b.ping)[0]
  
  if (bestServer) {
    connectToSpecificServer(bestServer)
  }
}

const connectToSpecificServer = async (server) => {
  if (isConnecting.value || server.status === 'offline') return
  
  isConnecting.value = true
  error.value = null
  
  try {
    console.log(`🔌 Connecting to ${server.name}...`)
    const response = await apiService.connectToServer(server.id)
    
    if (response.success) {
      // Disconnect from current server first
      if (currentServer.value) {
        currentServer.value.status = 'available'
      }
      
      // Connect to selected server
      server.status = 'connected'
      currentServerId.value = server.id
      connectionUptime.value = 0
      
      console.log(`✅ Connected to ${server.name}`)
    } else {
      throw new Error('Connection failed')
    }
  } catch (err) {
    console.error('❌ Connection failed:', err)
    error.value = err.message
  } finally {
    isConnecting.value = false
  }
}

const disconnectFromServer = async () => {
  if (isDisconnecting.value || !currentServer.value) return
  
  isDisconnecting.value = true
  error.value = null
  
  try {
    console.log('🔌 Disconnecting from server...')
    const response = await apiService.disconnectFromServer()
    
    if (response.success) {
      if (currentServer.value) {
        currentServer.value.status = 'available'
        currentServerId.value = null
        connectionUptime.value = 0
      }
      
      console.log('✅ Disconnected from server')
    } else {
      throw new Error('Disconnection failed')
    }
  } catch (err) {
    console.error('❌ Disconnection failed:', err)
    error.value = err.message
  } finally {
    isDisconnecting.value = false
  }
}

const pingServer = (server) => {
  console.log(`Pinging ${server.name}...`)
  // Simulate ping test
  server.ping = Math.floor(Math.random() * 300) + 10
}

const showServerInfo = (server) => {
  alert(`Server-Info:\nName: ${server.name}\nAdresse: ${server.address}:${server.port}\nBenutzer: ${server.users}\nDateien: ${server.files}\nPing: ${server.ping}ms\nVersion: ${server.version}`)
}

const testConnection = (server) => {
  console.log(`Testing connection to ${server.name}...`)
  // Simulate connection test
}

const addToFavorites = (server) => {
  console.log(`Added ${server.name} to favorites`)
}

const addNewServer = () => {
  if (!newServer.value.name || !newServer.value.address || !newServer.value.port) {
    alert('Bitte alle Felder ausfüllen!')
    return
  }
  
  const newId = Math.max(...servers.value.map(s => s.id)) + 1
  
  servers.value.push({
    id: newId,
    name: newServer.value.name,
    address: newServer.value.address,
    port: newServer.value.port,
    status: 'available',
    users: 0,
    files: 0,
    ping: 999,
    version: 'Unknown',
    location: 'Custom',
    uptime: 0
  })
  
  // Reset form
  newServer.value = {
    name: '',
    address: '',
    port: 9851
  }
  
  // Close modal (would need Bootstrap JS for this)
  console.log('Server added successfully')
}

// Lifecycle
onMounted(async () => {
  console.log('🚀 ServerStatusView mounted - initializing...')
  
  // Test API connection first
  const connectionTest = await apiService.testConnection()
  if (!connectionTest.success) {
    console.warn('⚠️ API connection failed, using demo data')
    error.value = `API Error: ${connectionTest.message}`
  }
  
  // Fetch servers from API
  await fetchServers()
  
  console.log('✅ ServerStatusView initialized')
  
  // Update connection uptime
  setInterval(() => {
    if (isConnected.value) {
      connectionUptime.value += 1000
    }
  }, 1000)
  
  // Auto-refresh servers every 30 seconds
  setInterval(async () => {
    if (!isLoading.value) {
      await fetchServers()
    }
  }, 30000)
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

/* Connection Status Card */
.connection-status-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  margin-bottom: 1rem;
}

.current-connection {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.connection-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  flex-shrink: 0;
}

.connection-icon.connected {
  background: linear-gradient(135deg, #28a745, #20c997);
  animation: pulse-success 2s infinite;
}

.connection-icon.connecting {
  background: linear-gradient(135deg, #17a2b8, #6f42c1);
}

.connection-icon.disconnected {
  background: linear-gradient(135deg, #6c757d, #495057);
}

.connection-details {
  flex: 1;
}

.connection-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0 0 0.5rem 0;
  color: #2d3748;
}

.connection-info {
  margin: 0 0 0.5rem 0;
  color: #6c757d;
  font-size: 1rem;
}

.connection-stats {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}

.stat-item {
  display: flex;
  align-items: center;
  color: #6c757d;
  font-size: 0.9rem;
}

.connection-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
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

/* Server List */
.server-list .card {
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

.server-row:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

/* Status Indicators */
.status-indicator {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

.status-connected {
  background-color: #28a745;
  color: white;
}

.status-available {
  background-color: #17a2b8;
  color: white;
}

.status-busy {
  background-color: #ffc107;
  color: #212529;
}

.status-offline {
  background-color: #6c757d;
  color: white;
}

/* Server Info */
.server-info {
  min-width: 0;
}

.server-name {
  font-weight: 500;
  color: #2d3748;
  display: flex;
  align-items: center;
  margin-bottom: 0.25rem;
}

.server-name.current-server {
  color: #28a745;
  font-weight: 600;
}

.server-address {
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
}

.server-location {
  font-size: 0.8rem;
  color: #6c757d;
  display: flex;
  align-items: center;
}

/* Counts and Stats */
.user-count, .file-count {
  font-weight: 500;
  color: #495057;
}

.ping {
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
}

.ping-excellent {
  background-color: #d4edda;
  color: #155724;
}

.ping-good {
  background-color: #d1ecf1;
  color: #0c5460;
}

.ping-fair {
  background-color: #fff3cd;
  color: #856404;
}

.ping-poor {
  background-color: #f8d7da;
  color: #721c24;
}

.version {
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  color: #6c757d;
}

/* Server Actions */
.server-actions {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.server-actions .btn {
  padding: 0.375rem 0.5rem;
}

/* Dropdown */
.dropdown-menu {
  border: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  min-width: 180px;
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

/* Floating Add Button */
.btn-floating {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-floating:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
}

/* Modal Styles */
.modal-content {
  border: none;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  border-bottom: 1px solid #e9ecef;
  padding: 1.5rem;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  border-top: 1px solid #e9ecef;
  padding: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-actions {
    justify-content: center;
    margin-top: 1rem;
  }
  
  .current-connection {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .connection-stats {
    justify-content: center;
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
  
  .connection-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .connection-title {
    font-size: 1.2rem;
  }
  
  .btn-floating {
    bottom: 1rem;
    right: 1rem;
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
}

@media (max-width: 576px) {
  .table-responsive {
    font-size: 0.85rem;
  }
  
  .server-name {
    font-size: 0.9rem;
  }
  
  .server-address {
    font-size: 0.75rem;
  }
  
  .connection-stats {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .server-actions .btn {
    padding: 0.25rem 0.375rem;
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

@keyframes pulse-success {
  0% {
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
  }
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Connection status transitions */
.connection-icon {
  transition: all 0.3s ease;
}

.status-indicator {
  transition: all 0.2s ease;
}

/* Hover effects */
.server-row {
  transition: background-color 0.2s ease;
}

.btn {
  transition: all 0.2s ease;
}
</style>