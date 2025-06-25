<template>
  <CContainer fluid class="p2p-dashboard">
    
    <!-- Header -->
    <CRow class="mb-4">
      <CCol md="8">
        <h1 class="h2 mb-0">🍎 AppleJuice P2P Dashboard</h1>
        <p class="text-muted mb-0">Verbunden mit: {{ serverAddress }}</p>
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
      
      <div class="col-sm-6 col-lg-3">
        <div class="card mb-4 text-white bg-success">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeDownloads }}</div>
              <div>Aktive Downloads</div>
            </div>
            <div class="fs-1">📥</div>
          </div>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Laufende Downloads</small>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-sm-6 col-lg-3">
        <div class="card mb-4 text-white bg-warning">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.activeUploads }}</div>
              <div>Aktive Uploads</div>
            </div>
            <div class="fs-1">📤</div>
          </div>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Geteilte Dateien</small>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-sm-6 col-lg-3">
        <div class="card mb-4 text-white bg-info">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold">{{ p2pStats.networkSpeed }}</div>
              <div>Netzwerk-Geschwindigkeit</div>
            </div>
            <div class="fs-1">⚡</div>
          </div>
          <div class="mt-3 mx-3" style="height:70px;">
            <div class="text-center text-white-50 pt-3">
              <small>Aktuelle Geschwindigkeit</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Chart Card -->
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-5">
                <h4 class="card-title mb-0">Transfer-Aktivität</h4>
                <div class="small text-muted">Letzte 7 Tage</div>
              </div>
              <div class="col-sm-7 d-none d-md-block">
                <button class="btn btn-primary float-end">
                  💾 Export
                </button>
                <div class="btn-group float-end me-3" role="group">
                  <button class="btn btn-outline-secondary">Tag</button>
                  <button class="btn btn-outline-secondary active">Woche</button>
                  <button class="btn btn-outline-secondary">Monat</button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="mt-4 p-4 bg-light rounded text-center">
                <div class="fs-1 text-muted mb-3">📊</div>
                <h5 class="text-muted">Transfer-Chart wird hier angezeigt</h5>
                <p class="text-muted">Implementierung folgt mit Chart.js Integration</p>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="row mb-2 text-center">
              <div class="col">
                <div class="text-muted">Downloads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeDownloads }} Aktiv (65%)</div>
                <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar bg-success" style="width: 65%"></div>
                </div>
              </div>
              <div class="col">
                <div class="text-muted">Uploads</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.activeUploads }} Aktiv (35%)</div>
                <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar bg-info" style="width: 35%"></div>
                </div>
              </div>
              <div class="col">
                <div class="text-muted">Geteilte Dateien</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.totalShared }} Dateien</div>
                <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar bg-warning" style="width: 80%"></div>
                </div>
              </div>
              <div class="col">
                <div class="text-muted">Verbindungen</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.connectedUsers }} Benutzer</div>
                <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar bg-danger" style="width: 90%"></div>
                </div>
              </div>
              <div class="col d-none d-xl-block">
                <div class="text-muted">Uptime</div>
                <div class="fw-semibold text-truncate">{{ p2pStats.uptime }}</div>
                <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar" style="width: 95%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Transfers Table -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header">
            <strong>Aktive Transfers</strong>
            <small class="ms-2 text-muted">Downloads &amp; Uploads</small>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Status</th>
                    <th>Datei</th>
                    <th class="text-center">Typ</th>
                    <th>Größe</th>
                    <th class="text-center">Fortschritt</th>
                    <th>Geschwindigkeit</th>
                    <th>ETA</th>
                    <th>Peers</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in activeTransfers" :key="index">
                    <td>
                      <div class="fs-4">{{ item.type === 'Download' ? '📥' : '📤' }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ item.file }}</div>
                      <div class="small text-muted">
                        <span :class="'badge rounded-pill bg-' + getStatusColor(item.status)">
                          {{ item.status }}
                        </span>
                      </div>
                    </td>
                    <td class="text-center">
                      <span :class="'badge rounded-pill bg-' + getTypeColor(item.type)">
                        {{ item.type }}
                      </span>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ item.size }}</div>
                    </td>
                    <td class="text-center">
                      <div class="fw-semibold">{{ item.progress }}%</div>
                      <div class="progress mt-1" style="height: 4px;">
                        <div 
                          :class="'progress-bar bg-' + getStatusColor(item.status)" 
                          :style="'width: ' + item.progress + '%'"
                        ></div>
                      </div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ item.speed }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ item.eta }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ item.peers }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
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
const serverAddress = ref('')

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