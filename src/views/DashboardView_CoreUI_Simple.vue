<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  CCard,
  CCardBody,
  CCardHeader,
  CCardFooter,
  CCol,
  CRow,
  CButton,
  CButtonGroup,
  CProgress,
  CTable,
  CTableBody,
  CTableDataCell,
  CTableHead,
  CTableHeaderCell,
  CTableRow,
  CBadge,
  CAvatar
} from '@coreui/vue'

const router = useRouter()
const serverAddress = ref('')
const coreHost = ref('')
const corePass = ref('')

// P2P-spezifische Daten
const p2pStats = ref({
  connectedUsers: 1247,
  activeDownloads: 23,
  activeUploads: 8,
  totalShared: 156,
  networkSpeed: '2.4 MB/s',
  uptime: '5d 12h 34m'
})

// Download-Statistiken für die Woche
const weeklyDownloads = [
  { title: 'Montag', downloads: 34, uploads: 12 },
  { title: 'Dienstag', downloads: 56, uploads: 18 },
  { title: 'Mittwoch', downloads: 23, uploads: 9 },
  { title: 'Donnerstag', downloads: 43, uploads: 15 },
  { title: 'Freitag', downloads: 67, uploads: 22 },
  { title: 'Samstag', downloads: 89, uploads: 31 },
  { title: 'Sonntag', downloads: 45, uploads: 16 },
]

// Benutzer-Statistiken
const userStats = [
  { title: 'Aktive Benutzer', icon: '👤', value: 78 },
  { title: 'Neue Benutzer', icon: '👥', value: 22 },
]

// Netzwerk-Quellen
const networkSources = [
  {
    title: 'Direkte Verbindungen',
    icon: '📡',
    percent: 65,
    value: '812 Benutzer',
  },
  { 
    title: 'Server-Hubs', 
    icon: '🖥️', 
    percent: 25, 
    value: '312 Benutzer' 
  },
  { 
    title: 'Proxy-Verbindungen', 
    icon: '🏠', 
    percent: 10, 
    value: '123 Benutzer' 
  },
]

// Aktuelle Downloads/Uploads Tabelle
const activeTransfers = [
  {
    file: 'Ubuntu_22.04.iso',
    type: 'Download',
    size: '4.2 GB',
    progress: 67,
    speed: '1.2 MB/s',
    eta: '12 min',
    status: 'active',
    peers: 8
  },
  {
    file: 'Movie_Collection.zip',
    type: 'Upload',
    size: '8.9 GB',
    progress: 100,
    speed: '850 KB/s',
    eta: 'Fertig',
    status: 'seeding',
    peers: 15
  },
  {
    file: 'Software_Pack.rar',
    type: 'Download',
    size: '2.1 GB',
    progress: 23,
    speed: '650 KB/s',
    eta: '45 min',
    status: 'active',
    peers: 4
  },
  {
    file: 'Music_Album.zip',
    type: 'Download',
    size: '156 MB',
    progress: 89,
    speed: '2.1 MB/s',
    eta: '2 min',
    status: 'active',
    peers: 12
  },
]

onMounted(() => {
  // Check if user is authenticated
  const isAuthenticated = localStorage.getItem('p2p_authenticated')
  if (!isAuthenticated) {
    router.push('/login')
    return
  }
  
  // Load saved data
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
})

const logout = () => {
  localStorage.removeItem('p2p_authenticated')
  localStorage.removeItem('p2p_core_host')
  localStorage.removeItem('p2p_core_pass')
  router.push('/login')
}

const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'success'
    case 'seeding': return 'info'
    case 'paused': return 'warning'
    case 'error': return 'danger'
    default: return 'secondary'
  }
}

const getTypeColor = (type) => {
  return type === 'Download' ? 'primary' : 'success'
}
</script>

<template>
  <div class="p2p-dashboard">
    
    <!-- Header -->
    <CRow class="mb-4">
      <CCol :md="8">
        <h1 class="h2 mb-0">🍎 AppleJuice P2P Dashboard</h1>
        <p class="text-muted mb-0">Verbunden mit: {{ serverAddress }}</p>
      </CCol>
      <CCol :md="4" class="text-md-end">
        <CButton color="danger" @click="logout">
          ⚙️ Abmelden
        </CButton>
      </CCol>
    </CRow>

    <!-- Stats Cards -->
    <CRow class="mb-4">
      <CCol :sm="6" :lg="3">
        <CCard class="mb-4 text-white bg-primary">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.connectedUsers }}</div>
              <div>Verbundene Benutzer</div>
            </div>
            <div class="fs-1">👥</div>
          </CCardBody>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Online Benutzer</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol :sm="6" :lg="3">
        <CCard class="mb-4 text-white bg-success">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeDownloads }}</div>
              <div>Aktive Downloads</div>
            </div>
            <div class="fs-1">📥</div>
          </CCardBody>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Laufende Downloads</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol :sm="6" :lg="3">
        <CCard class="mb-4 text-white bg-warning">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeUploads }}</div>
              <div>Aktive Uploads</div>
            </div>
            <div class="fs-1">📤</div>
          </CCardBody>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Geteilte Dateien</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol :sm="6" :lg="3">
        <CCard class="mb-4 text-white bg-info">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.networkSpeed }}</div>
              <div>Netzwerk-Geschwindigkeit</div>
            </div>
            <div class="fs-1">⚡</div>
          </CCardBody>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Aktuelle Geschwindigkeit</small>
            </div>
          </div>
        </CCard>
      </CCol>
    </CRow>

    <!-- Main Chart Card -->
    <CRow>
      <CCol :md="12">
        <CCard class="mb-4">
          <CCardBody>
            <CRow>
              <CCol :sm="5">
                <h4 id="traffic" class="card-title mb-0">Transfer-Aktivität</h4>
                <div class="small text-body-secondary">Letzte 7 Tage</div>
              </CCol>
              <CCol :sm="7" class="d-none d-md-block">
                <CButton color="primary" class="float-end">
                  💾 Export
                </CButton>
                <CButtonGroup
                  class="float-end me-3"
                  role="group"
                  aria-label="Zeitraum auswählen"
                >
                  <CButton color="secondary" variant="outline">Tag</CButton>
                  <CButton color="secondary" variant="outline" active>Woche</CButton>
                  <CButton color="secondary" variant="outline">Monat</CButton>
                </CButtonGroup>
              </CCol>
            </CRow>
            <CRow>
              <!-- Chart Placeholder -->
              <div class="mt-4 p-4 bg-light rounded text-center">
                <div class="fs-1 text-muted mb-3">📊</div>
                <h5 class="text-muted">Transfer-Chart wird hier angezeigt</h5>
                <p class="text-muted">Implementierung folgt mit Chart.js Integration</p>
              </div>
            </CRow>
          </CCardBody>
          <CCardFooter>
            <CRow
              :xs="{ cols: 1, gutter: 4 }"
              :sm="{ cols: 2 }"
              :lg="{ cols: 4 }"
              :xl="{ cols: 5 }"
              class="mb-2 text-center"
            >
              <CCol>
                <div class="text-body-secondary">Downloads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeDownloads }} Aktiv (65%)</div>
                <CProgress class="mt-2" color="success" thin :precision="1" :value="65" />
              </CCol>
              <CCol>
                <div class="text-body-secondary">Uploads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeUploads }} Aktiv (35%)</div>
                <CProgress class="mt-2" color="info" thin :precision="1" :value="35" />
              </CCol>
              <CCol>
                <div class="text-body-secondary">Geteilte Dateien</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.totalShared }} Dateien</div>
                <CProgress class="mt-2" color="warning" thin :precision="1" :value="80" />
              </CCol>
              <CCol>
                <div class="text-body-secondary">Verbindungen</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.connectedUsers }} Benutzer</div>
                <CProgress class="mt-2" color="danger" thin :precision="1" :value="90" />
              </CCol>
              <CCol class="d-none d-xl-block">
                <div class="text-body-secondary">Uptime</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.uptime }}</div>
                <CProgress class="mt-2" :value="95" thin :precision="1" />
              </CCol>
            </CRow>
          </CCardFooter>
        </CCard>
      </CCol>
    </CRow>

    <!-- Statistics Row -->
    <CRow>
      <CCol :md="12">
        <CCard class="mb-4">
          <CCardHeader>P2P Netzwerk &amp; Transfer-Statistiken</CCardHeader>
          <CCardBody>
            <CRow>
              <CCol :sm="12" :lg="6">
                <CRow>
                  <CCol :xs="6">
                    <div class="border-start border-start-4 border-start-info py-1 px-3 mb-3">
                      <div class="text-body-secondary small">Neue Verbindungen</div>
                      <div class="fs-5 fw-semibold">{{ Math.floor(p2pStats.connectedUsers * 0.15) }}</div>
                    </div>
                  </CCol>
                  <CCol :xs="6">
                    <div class="border-start border-start-4 border-start-danger py-1 px-3 mb-3">
                      <div class="text-body-secondary small">Stabile Verbindungen</div>
                      <div class="fs-5 fw-semibold">{{ Math.floor(p2pStats.connectedUsers * 0.85) }}</div>
                    </div>
                  </CCol>
                </CRow>
                <hr class="mt-0" />
                <div
                  v-for="item in weeklyDownloads"
                  :key="item.title"
                  class="progress-group mb-4"
                >
                  <div class="progress-group-prepend">
                    <span class="text-body-secondary small">{{ item.title }}</span>
                  </div>
                  <div class="progress-group-bars">
                    <CProgress thin color="info" :value="item.downloads" />
                    <CProgress thin color="success" :value="item.uploads" />
                  </div>
                </div>
              </CCol>
              <CCol :sm="12" :lg="6">
                <CRow>
                  <CCol :xs="6">
                    <div class="border-start border-start-4 border-start-warning py-1 px-3 mb-3">
                      <div class="text-body-secondary small">Gesamt-Traffic</div>
                      <div class="fs-5 fw-semibold">2.4 TB</div>
                    </div>
                  </CCol>
                  <CCol :xs="6">
                    <div class="border-start border-start-4 border-start-success py-1 px-3 mb-3">
                      <div class="text-body-secondary small">Heute übertragen</div>
                      <div class="fs-5 fw-semibold">156 GB</div>
                    </div>
                  </CCol>
                </CRow>
                <hr class="mt-0" />
                
                <div v-for="item in userStats" :key="item.title" class="progress-group mb-4">
                  <div class="progress-group-header">
                    <span class="me-2 fs-5">{{ item.icon }}</span>
                    <span class="title">{{ item.title }}</span>
                    <span class="ms-auto fw-semibold">{{ item.value }}%</span>
                  </div>
                  <div class="progress-group-bars">
                    <CProgress thin :value="item.value" color="warning" />
                  </div>
                </div>

                <div class="mb-5"></div>

                <div v-for="item in networkSources" :key="item.title" class="progress-group mb-3">
                  <div class="progress-group-header">
                    <span class="me-2 fs-5">{{ item.icon }}</span>
                    <span class="title">{{ item.title }}</span>
                    <span class="ms-auto fw-semibold">{{ item.percent }}%</span>
                  </div>
                  <div class="progress-group-bars">
                    <CProgress thin :value="item.percent" color="primary" />
                  </div>
                  <div class="progress-group-footer">
                    <span class="small text-body-secondary">{{ item.value }}</span>
                  </div>
                </div>
              </CCol>
            </CRow>
          </CCardBody>
        </CCard>
      </CCol>
    </CRow>

    <!-- Active Transfers Table -->
    <CRow>
      <CCol :xs="12">
        <CCard class="mb-4">
          <CCardHeader>
            <strong>Aktive Transfers</strong>
            <small class="ms-2 text-body-secondary">Downloads & Uploads</small>
          </CCardHeader>
          <CCardBody>
            <CTable align="middle" class="mb-0 border" hover responsive>
              <CTableHead class="text-nowrap">
                <CTableRow>
                  <CTableHeaderCell class="bg-body-tertiary">Status</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary">Datei</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary text-center">Typ</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary">Größe</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary text-center">Fortschritt</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary">Geschwindigkeit</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary">ETA</CTableHeaderCell>
                  <CTableHeaderCell class="bg-body-tertiary">Peers</CTableHeaderCell>
                </CTableRow>
              </CTableHead>
              <CTableBody>
                <CTableRow v-for="(item, index) in activeTransfers" :key="index">
                  <CTableDataCell>
                    <div class="fs-4">{{ item.type === 'Download' ? '📥' : '📤' }}</div>
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.file }}</div>
                    <div class="small text-body-secondary text-nowrap">
                      <CBadge :color="getStatusColor(item.status)" shape="rounded-pill">
                        {{ item.status }}
                      </CBadge>
                    </div>
                  </CTableDataCell>
                  <CTableDataCell class="text-center">
                    <CBadge :color="getTypeColor(item.type)" shape="rounded-pill">
                      {{ item.type }}
                    </CBadge>
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.size }}</div>
                  </CTableDataCell>
                  <CTableDataCell class="text-center">
                    <div class="fw-semibold">{{ item.progress }}%</div>
                    <CProgress 
                      thin 
                      :color="getStatusColor(item.status)" 
                      :value="item.progress" 
                      class="mt-1"
                    />
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.speed }}</div>
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.eta }}</div>
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.peers }}</div>
                  </CTableDataCell>
                </CTableRow>
              </CTableBody>
            </CTable>
          </CCardBody>
        </CCard>
      </CCol>
    </CRow>

  </div>
</template>

<style scoped>
.p2p-dashboard {
  padding: 1.5rem;
}

.progress-group {
  position: relative;
}

.progress-group-prepend {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-group-header {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-group-header .title {
  flex: 1;
}

.progress-group-bars {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.progress-group-footer {
  margin-top: 0.25rem;
}

@media (max-width: 768px) {
  .p2p-dashboard {
    padding: 1rem;
  }
}
</style>
</template>