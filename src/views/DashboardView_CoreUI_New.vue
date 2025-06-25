<template>
  <CContainer fluid class="p2p-dashboard">
    
    <!-- Header -->
    <CRow class="mb-4">
      <CCol md="8">
        <h1 class="h2 mb-0">🍎 AppleJuice P2P Dashboard</h1>
        <p class="text-muted mb-0">Verbunden mit: {{ serverAddress }}</p>
        <div v-if="isDemoMode" class="mt-2">
          <span class="badge bg-warning text-dark">
            🎭 Demo-Modus - Beispieldaten
          </span>
        </div>
      </CCol>
      <CCol md="4" class="text-md-end">
        <CButton color="danger" @click="logout">
          ⚙️ Abmelden
        </CButton>
      </CCol>
    </CRow>

    <!-- Stats Cards -->
    <CRow class="mb-4">
      <CCol sm="6" lg="3">
        <CCard class="mb-4 text-white bg-primary">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.connectedUsers }}</div>
              <div>Verbundene Benutzer</div>
            </div>
            <div class="fs-1">👥</div>
          </CCardBody>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Online Benutzer</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol sm="6" lg="3">
        <CCard class="mb-4 text-white bg-success">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeDownloads }}</div>
              <div>Aktive Downloads</div>
            </div>
            <div class="fs-1">📥</div>
          </CCardBody>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Laufende Downloads</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol sm="6" lg="3">
        <CCard class="mb-4 text-white bg-warning">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeUploads }}</div>
              <div>Aktive Uploads</div>
            </div>
            <div class="fs-1">📤</div>
          </CCardBody>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Geteilte Dateien</small>
            </div>
          </div>
        </CCard>
      </CCol>
      
      <CCol sm="6" lg="3">
        <CCard class="mb-4 text-white bg-info">
          <CCardBody class="pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.networkSpeed }}</div>
              <div>Netzwerk-Geschwindigkeit</div>
            </div>
            <div class="fs-1">⚡</div>
          </CCardBody>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Aktuelle Geschwindigkeit</small>
            </div>
          </div>
        </CCard>
      </CCol>
    </CRow>

    <!-- Main Chart Card -->
    <CRow>
      <CCol md="12">
        <CCard class="mb-4">
          <CCardBody>
            <CRow>
              <CCol sm="5">
                <CCardTitle class="mb-0">Transfer-Aktivität</CCardTitle>
                <div class="small text-muted">Letzte 7 Tage</div>
              </CCol>
              <CCol sm="7" class="d-none d-md-block">
                <CButton color="primary" class="float-end">
                  💾 Export
                </CButton>
                <CButtonGroup class="float-end me-3" role="group">
                  <CButton color="outline-secondary">Tag</CButton>
                  <CButton color="outline-secondary" active>Woche</CButton>
                  <CButton color="outline-secondary">Monat</CButton>
                </CButtonGroup>
              </CCol>
            </CRow>
            <CRow>
              <div class="mt-4 p-4 bg-light rounded text-center">
                <div class="fs-1 text-muted mb-3">📊</div>
                <h5 class="text-muted">Transfer-Chart wird hier angezeigt</h5>
                <p class="text-muted">Implementierung folgt mit Chart.js Integration</p>
              </div>
            </CRow>
          </CCardBody>
          <CCardFooter>
            <CRow class="mb-2 text-center">
              <CCol>
                <div class="text-muted">Downloads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeDownloads }} Aktiv (65%)</div>
                <CProgress class="mt-2" height="4">
                  <CProgressBar color="success" :value="65"></CProgressBar>
                </CProgress>
              </CCol>
              <CCol>
                <div class="text-muted">Uploads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeUploads }} Aktiv (35%)</div>
                <CProgress class="mt-2" height="4">
                  <CProgressBar color="info" :value="35"></CProgressBar>
                </CProgress>
              </CCol>
              <CCol>
                <div class="text-muted">Geteilte Dateien</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.totalShared }} Dateien</div>
                <CProgress class="mt-2" height="4">
                  <CProgressBar color="warning" :value="80"></CProgressBar>
                </CProgress>
              </CCol>
              <CCol>
                <div class="text-muted">Verbindungen</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.connectedUsers }} Benutzer</div>
                <CProgress class="mt-2" height="4">
                  <CProgressBar color="danger" :value="90"></CProgressBar>
                </CProgress>
              </CCol>
              <CCol class="d-none d-xl-block">
                <div class="text-muted">Uptime</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.uptime }}</div>
                <CProgress class="mt-2" height="4">
                  <CProgressBar :value="95"></CProgressBar>
                </CProgress>
              </CCol>
            </CRow>
          </CCardFooter>
        </CCard>
      </CCol>
    </CRow>

    <!-- Active Transfers Table -->
    <CRow>
      <CCol>
        <CCard class="mb-4">
          <CCardHeader>
            <strong>Aktive Transfers</strong>
            <small class="ms-2 text-muted">Downloads &amp; Uploads</small>
          </CCardHeader>
          <CCardBody>
            <CTable responsive hover>
              <CTableHead>
                <CTableRow>
                  <CTableHeaderCell>Status</CTableHeaderCell>
                  <CTableHeaderCell>Datei</CTableHeaderCell>
                  <CTableHeaderCell class="text-center">Typ</CTableHeaderCell>
                  <CTableHeaderCell>Größe</CTableHeaderCell>
                  <CTableHeaderCell class="text-center">Fortschritt</CTableHeaderCell>
                  <CTableHeaderCell>Geschwindigkeit</CTableHeaderCell>
                  <CTableHeaderCell>ETA</CTableHeaderCell>
                  <CTableHeaderCell>Peers</CTableHeaderCell>
                </CTableRow>
              </CTableHead>
              <CTableBody>
                <CTableRow v-for="(item, index) in activeTransfers" :key="index">
                  <CTableDataCell>
                    <div class="fs-4">{{ item.type === 'Download' ? '📥' : '📤' }}</div>
                  </CTableDataCell>
                  <CTableDataCell>
                    <div class="fw-semibold">{{ item.file }}</div>
                    <div class="small text-muted">
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
                    <CProgress class="mt-1" height="4">
                      <CProgressBar 
                        :color="getStatusColor(item.status)" 
                        :value="item.progress"
                      ></CProgressBar>
                    </CProgress>
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

  </CContainer>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  CContainer,
  CRow,
  CCol,
  CCard,
  CCardBody,
  CCardHeader,
  CCardFooter,
  CCardTitle,
  CButton,
  CButtonGroup,
  CTable,
  CTableHead,
  CTableBody,
  CTableRow,
  CTableHeaderCell,
  CTableDataCell,
  CBadge,
  CProgress,
  CProgressBar
} from '@coreui/vue'

const router = useRouter()
const serverAddress = ref('')
const isDemoMode = ref(false)

// P2P-spezifische Daten
const p2pStats = ref({
  connectedUsers: 1247,
  activeDownloads: 23,
  activeUploads: 8,
  totalShared: 156,
  networkSpeed: '2.4 MB/s',
  uptime: '5d 12h 34m'
})

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
  if (savedServer) {
    serverAddress.value = savedServer
  }
  
  // Check if in demo mode
  const demoMode = localStorage.getItem('p2p_demo_mode')
  if (demoMode) {
    isDemoMode.value = true
    console.log('🎭 Demo-Modus aktiv - Verwende Beispieldaten')
    // In demo mode, we use the static data already defined above
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

<style scoped>
.p2p-dashboard {
  padding: 1.5rem;
}

@media (max-width: 768px) {
  .p2p-dashboard {
    padding: 1rem;
  }
}
</style>