<script setup>
import { ref, computed, inject } from 'vue'
import { useRouter } from 'vue-router'

const emit = defineEmits(['toggle-sidebar'])

const router = useRouter()
const lang = inject('language')

// Reactive data
const serverAddress = ref('')
const isConnected = ref(false)

// Load connection status
const loadConnectionStatus = () => {
  const savedServer = localStorage.getItem('p2p_server_address')
  const isAuthenticated = localStorage.getItem('p2p_authenticated')
  
  if (savedServer) {
    serverAddress.value = savedServer
  }
  
  isConnected.value = !!isAuthenticated
}

// Initialize
loadConnectionStatus()

// Computed
const connectionStatus = computed(() => {
  return isConnected.value ? 'connected' : 'disconnected'
})

const connectionBadgeVariant = computed(() => {
  return isConnected.value ? 'success' : 'danger'
})

// Methods
const toggleSidebar = () => {
  emit('toggle-sidebar')
}

const logout = () => {
  // Clear authentication
  localStorage.removeItem('p2p_authenticated')
  localStorage.removeItem('p2p_core_host')
  localStorage.removeItem('p2p_core_pass')
  
  // Redirect to login
  router.push('/login')
}

const switchLanguage = (langCode) => {
  lang.setLanguage(langCode)
}

const refreshData = () => {
  // Emit refresh event
  window.dispatchEvent(new CustomEvent('refresh-data'))
}
</script>

<template>
  <header class="app-header">
    <div class="header-content">
      <!-- Mobile Menu Toggle -->
      <button 
        class="sidebar-toggle d-lg-none"
        @click="toggleSidebar"
        type="button"
      >
        <i class="bi bi-list fs-4"></i>
      </button>
      
      <!-- Brand (Mobile) -->
      <div class="header-brand d-lg-none">
        🍎 AppleJuice
      </div>
      
      <!-- Header Actions -->
      <div class="header-actions">
       
        <!-- Language Dropdown -->
        <div class="dropdown me-2">
          <button 
            class="btn btn-outline-secondary btn-sm dropdown-toggle" 
            type="button" 
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-translate"></i>
            <span class="d-none d-sm-inline ms-1">{{ lang?.getCurrentLanguage()?.toUpperCase() || 'DE' }}</span>
          </button>
          <ul class="dropdown-menu">
            <li v-for="language in (lang?.getAvailableLanguages() || [])" :key="language.code">
              <a 
                class="dropdown-item" 
                href="#" 
                @click.prevent="switchLanguage(language.code)"
                :class="{ active: lang?.getCurrentLanguage() === language.code }"
              >
                <i class="bi bi-check me-2" v-if="lang?.getCurrentLanguage() === language.code"></i>
                <span v-else class="me-4"></span>
                {{ language.name }}
              </a>
            </li>
          </ul>
        </div>
        
        <!-- User Dropdown -->
        <div class="dropdown">
          <button 
            class="btn btn-outline-secondary btn-sm dropdown-toggle" 
            type="button" 
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-sm-inline ms-1">Menü</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#" @click.prevent="refreshData">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Aktualisieren
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="#" @click.prevent="$router.push('/settings')">
                <i class="bi bi-gear me-2"></i>
                Einstellungen
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#" @click.prevent="$router.push('/about')">
                <i class="bi bi-info-circle me-2"></i>
                Über
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
                <i class="bi bi-box-arrow-right me-2"></i>
                Abmelden
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.app-header {
  position: sticky;
  top: 0;
  z-index: 999;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  height: 60px;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  padding: 0 1rem;
}

.sidebar-toggle {
  background: none;
  border: none;
  color: #495057;
  padding: 0.5rem;
  cursor: pointer;
  transition: color 0.2s ease;
}

.sidebar-toggle:hover {
  color: #0d6efd;
}

.header-brand {
  font-size: 1.25rem;
  font-weight: bold;
  color: #0d6efd;
  margin: 0 auto;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: auto;
}

.connection-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-weight: 500;
}

.status-badge.connected {
  background-color: #d4edda;
  color: #155724;
}

.status-badge.disconnected {
  background-color: #f8d7da;
  color: #721c24;
}

.server-address {
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
  color: #6c757d;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Dropdown Styles */
.dropdown-menu {
  border: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: rgba(13, 110, 253, 0.1);
}

.dropdown-item.active {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

/* Mobile Optimierungen */
@media (max-width: 767.98px) {
  .connection-status {
    display: none !important;
  }
  
  .header-actions .d-none.d-md-inline {
    display: none !important;
  }
}

/* Animation für Refresh Button */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.btn:active .bi-arrow-clockwise {
  animation: spin 0.5s ease-in-out;
}
</style>