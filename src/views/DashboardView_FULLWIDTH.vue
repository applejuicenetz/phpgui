<template>
  <div class="dashboard-fullwidth">
    
    <!-- Header -->
    <header class="header-fullwidth">
      <div class="header-content">
        <h1 class="header-title">🍎 AppleJuice P2P Dashboard</h1>
        <div class="header-actions">
          <span class="server-info">{{ serverAddress }}</span>
          <button @click="logout" class="logout-btn">Abmelden</button>
        </div>
      </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-fullwidth">
      
      <!-- Welcome Section -->
      <section class="welcome-fullwidth">
        <div class="welcome-content">
          <h2>Willkommen im AppleJuice P2P Dashboard!</h2>
          <p>Verwalte deine Downloads, Uploads und Einstellungen über diese moderne Web-Oberfläche.</p>
          <div class="status-badge">
            ✅ Erfolgreich mit {{ serverAddress }} verbunden
          </div>
        </div>
      </section>
      
      <!-- Status Cards -->
      <section class="status-fullwidth">
        <div class="cards-grid-3">
          
          <div class="status-card">
            <div class="card-icon status-success">🌐</div>
            <h3>Verbindungsstatus</h3>
            <p class="status-text">Aktiv verbunden</p>
          </div>
          
          <div class="status-card">
            <div class="card-icon status-info">🖥️</div>
            <h3>P2P-Core Server</h3>
            <p class="server-text">{{ serverAddress }}</p>
          </div>
          
          <div class="status-card">
            <div class="card-icon status-warning">⚙️</div>
            <h3>System Status</h3>
            <p class="status-text">Bereit für Entwicklung</p>
          </div>
          
        </div>
      </section>
      
      <!-- Feature Cards -->
      <section class="features-fullwidth">
        <div class="cards-grid-4">
          
          <div class="feature-card">
            <div class="feature-icon">📥</div>
            <h4>Downloads</h4>
            <p>Verwalte deine aktiven und abgeschlossenen Downloads</p>
            <button class="feature-btn" disabled>Bald verfügbar</button>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">📤</div>
            <h4>Uploads</h4>
            <p>Übersicht über geteilte Dateien und Upload-Statistiken</p>
            <button class="feature-btn" disabled>Bald verfügbar</button>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h4>Benutzer</h4>
            <p>Verbundene Benutzer und Netzwerk-Statistiken</p>
            <button class="feature-btn" disabled>Bald verfügbar</button>
          </div>
          
          <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h4>Statistiken</h4>
            <p>Detaillierte Übersicht über Netzwerk-Performance</p>
            <button class="feature-btn" disabled>Bald verfügbar</button>
          </div>
          
        </div>
      </section>
      
    </main>
    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const serverAddress = ref('')
const coreHost = ref('')
const corePass = ref('')

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
  // Clear all authentication data
  localStorage.removeItem('p2p_authenticated')
  localStorage.removeItem('p2p_core_host')
  localStorage.removeItem('p2p_core_pass')
  // Keep server address for convenience
  
  // Redirect to login
  router.push('/login')
}
</script>

<style scoped>
/* Reset and Base */
* {
  box-sizing: border-box;
}

.dashboard-fullwidth {
  min-height: 100vh;
  width: 100vw;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  margin: 0;
  padding: 0;
}

/* Header - Full Width */
.header-fullwidth {
  width: 100%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 2rem 3rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: none;
}

.header-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #0d6efd;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.server-info {
  color: #6c757d;
  font-size: 1.1rem;
  font-weight: 500;
  padding: 0.75rem 1.5rem;
  background: rgba(108, 117, 125, 0.1);
  border-radius: 25px;
  font-family: 'Courier New', monospace;
}

.logout-btn {
  padding: 0.75rem 1.5rem;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #c82333;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

/* Main Content - Full Width */
.main-fullwidth {
  width: 100%;
  padding: 3rem;
}

/* Welcome Section - Full Width */
.welcome-fullwidth {
  width: 100%;
  margin-bottom: 4rem;
}

.welcome-content {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  padding: 4rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.welcome-content h2 {
  font-size: 3rem;
  color: #0d6efd;
  margin-bottom: 1.5rem;
  font-weight: bold;
}

.welcome-content p {
  font-size: 1.3rem;
  color: #6c757d;
  margin-bottom: 2rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.status-badge {
  display: inline-block;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1.2rem;
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

/* Status Cards - Full Width Grid */
.status-fullwidth {
  width: 100%;
  margin-bottom: 4rem;
}

.cards-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  width: 100%;
}

.status-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.status-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  width: 80px;
  height: 80px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem auto;
}

.status-success {
  background: linear-gradient(135deg, #28a745, #20c997);
}

.status-info {
  background: linear-gradient(135deg, #17a2b8, #007bff);
}

.status-warning {
  background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.status-card h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #495057;
  margin-bottom: 1rem;
}

.status-text {
  color: #28a745;
  font-weight: 600;
  font-size: 1.1rem;
}

.server-text {
  color: #007bff;
  font-family: 'Courier New', monospace;
  font-weight: 600;
  font-size: 1rem;
  background: rgba(0, 123, 255, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 10px;
  display: inline-block;
}

/* Feature Cards - Full Width Grid */
.features-fullwidth {
  width: 100%;
}

.cards-grid-4 {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  width: 100%;
}

.feature-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.feature-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.feature-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.feature-card h4 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #495057;
  margin-bottom: 1rem;
}

.feature-card p {
  color: #6c757d;
  margin-bottom: 2rem;
  flex-grow: 1;
  font-size: 1rem;
  line-height: 1.5;
}

.feature-btn {
  padding: 0.75rem 1.5rem;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: not-allowed;
  opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .cards-grid-4 {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .header-fullwidth {
    padding: 2rem;
  }
  
  .main-fullwidth {
    padding: 2rem;
  }
}

@media (max-width: 768px) {
  .cards-grid-3 {
    grid-template-columns: 1fr;
  }
  
  .cards-grid-4 {
    grid-template-columns: 1fr;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }
  
  .header-title {
    font-size: 2rem;
    text-align: center;
  }
  
  .welcome-content {
    padding: 2rem;
  }
  
  .welcome-content h2 {
    font-size: 2rem;
  }
  
  .status-card, .feature-card {
    padding: 2rem;
  }
  
  .header-fullwidth {
    padding: 1.5rem;
  }
  
  .main-fullwidth {
    padding: 1.5rem;
  }
}

/* Extra Large Screens */
@media (min-width: 1600px) {
  .header-fullwidth {
    padding: 2rem 4rem;
  }
  
  .main-fullwidth {
    padding: 4rem;
  }
  
  .welcome-content {
    padding: 5rem;
  }
  
  .status-card, .feature-card {
    padding: 4rem;
  }
}

/* Animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.status-card, .feature-card {
  animation: slideInUp 0.6s ease-out;
}

.status-card:nth-child(2) {
  animation-delay: 0.1s;
}

.status-card:nth-child(3) {
  animation-delay: 0.2s;
}

.feature-card:nth-child(2) {
  animation-delay: 0.1s;
}

.feature-card:nth-child(3) {
  animation-delay: 0.2s;
}

.feature-card:nth-child(4) {
  animation-delay: 0.3s;
}
</style>
</template>