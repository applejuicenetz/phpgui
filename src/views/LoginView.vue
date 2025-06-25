<template>
  <!-- CoreUI Login Layout - basierend auf der PHP-Vorlage -->
  <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="row g-0 card-group-custom">
            
            <!-- Login Card (linke Seite) -->
            <div class="col-md-7">
              <div class="card h-100 p-4 mb-0">
                <div class="card-body">
                  <!-- Mobile Logo (nur auf kleinen Bildschirmen sichtbar) -->
                  <div class="text-center mb-4 d-md-none">
                    <div class="mobile-logo mb-2">🍎</div>
                    <h3 class="text-primary fw-bold mb-0">AppleJuice P2P</h3>
                  </div>
                  
                  <h1>Login</h1>
                  <p class="text-body-secondary">Mit deinem AppleJuice P2P-Core verbinden</p>
                
                <!-- Error Alerts -->
                <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Warnung!</strong> {{ errorMessage }}
                  <button type="button" class="btn-close" @click="errorMessage = ''" aria-label="Close"></button>
                </div>
                
                <!-- Success Alert -->
                <div v-if="successMessage" class="alert alert-success" role="alert">
                  <strong>Erfolg!</strong> {{ successMessage }}
                </div>
                
                <!-- Login Form -->
                <form @submit.prevent="handleLogin" autocomplete="off">
                  
                  <!-- Core-URL Input (floating label) -->
                  <div class="form-floating mb-3">
                    <input 
                      type="url" 
                      class="form-control" 
                      placeholder="Core-URL" 
                      id="chost"
                      v-model="serverAddress"
                      required
                    />
                    <label for="chost">Core-URL</label>
                  </div>
                  
                  <!-- Password Input (floating label) -->
                  <div class="form-floating mb-3">
                    <input 
                      type="password" 
                      class="form-control" 
                      id="cpass"
                      v-model="password"
                      placeholder="Passwort"
                      required
                    />
                    <label for="cpass">Passwort</label>
                  </div>
                  
                  <!-- Remember Checkbox -->
                  <div class="row">
                    <div class="col">
                      <div class="checkbox icheck m-l--20">
                        <label>
                          <input type="checkbox" v-model="rememberMe"> Anmeldung merken
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Submit Button -->
                  <div class="row mt-3">
                    <div class="col-6">
                      <button 
                        class="btn btn-primary px-4" 
                        type="submit"
                        :disabled="isLoading"
                      >
                        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        {{ isLoading ? 'Verbinde...' : 'Anmelden' }}
                      </button>
                    </div>
                    <div class="col-6 text-end">
                      <!-- Platz für weitere Buttons falls nötig -->
                    </div>
                  </div>
                  
                </form>
                
                <!-- Connection Info -->
                <div class="alert alert-info mt-4">
                  <h6 class="alert-heading">
                    🔗 Verbindung
                  </h6>
                  <p class="mb-2"><strong>Hinweis:</strong> Stellen Sie sicher, dass:</p>
                  <ul class="mb-0">
                    <li>Ihr AppleJuice Core läuft</li>
                    <li>Die Server-Adresse korrekt ist (z.B. <code>192.168.1.100:9854</code>)</li>
                    <li>Das Passwort stimmt</li>
                  </ul>
                </div>
                  
                </div>
              </div>
            </div>
            
            <!-- Right Side Card (Hintergrund-Bild) -->
            <div class="col-md-5 d-none d-md-block">
              <div class="card text-white bg-danger py-5 right-panel h-100">
                <div class="card-body text-center d-flex align-items-center justify-content-center">
                  <div>
                    <div class="display-1 mb-4">🍎</div>
                    <h2 class="fw-bold">AppleJuice P2P</h2>
                    <p class="lead">Verbinde dich mit deinem P2P-Netzwerk</p>
                    <small class="opacity-75">Sichere Verbindung zu deinem lokalen Core</small>
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import CryptoJS from 'crypto-js'
import { makeProxyRequest } from '../utils/proxy.js'

const router = useRouter()

// Reactive data
const serverAddress = ref('')
const password = ref('')
const rememberMe = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// Load saved server address from localStorage or use default
const savedServer = localStorage.getItem('p2p_server_address')
if (savedServer) {
  serverAddress.value = savedServer
} else {
  serverAddress.value = '192.168.178.222:9854'
}

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    // Validierung der Eingaben
    if (serverAddress.value.trim() === '' || password.value.trim() === '') {
      errorMessage.value = 'Bitte Server-Adresse und Passwort eingeben'
      return
    }
    
    // Hash password with MD5 (check if already 32 chars = already hashed)
    const hashedPassword = password.value.length === 32 ? password.value : CryptoJS.MD5(password.value).toString()
    
    // Ensure host has proper format
    const coreHost = serverAddress.value.startsWith('http://') ? serverAddress.value : `http://${serverAddress.value}`
    
    console.log('Attempting login via simple-proxy to:', coreHost.replace(/:\d+/, ':****'))
    
    // Try to fetch settings.xml via proxy to validate credentials
    const testUrl = `${coreHost}/xml/settings.xml?password=${hashedPassword}`
    
    try {
      const response = await makeProxyRequest(testUrl)
      
      // Check if response contains authentication error
      if (response.includes('wrong password.')) {
        errorMessage.value = 'Falsches Passwort'
        return
      }
      
      // Check if we got valid XML (simple check)
      if (!response.includes('<settings>') && !response.includes('<?xml')) {
        errorMessage.value = 'Unerwartete Antwort vom Core. Prüfen Sie die Server-Adresse.'
        return
      }
      
      // Login successful - save authentication data
      localStorage.setItem('p2p_server_address', serverAddress.value)
      localStorage.setItem('p2p_core_host', coreHost)
      localStorage.setItem('p2p_core_pass', hashedPassword)
      localStorage.setItem('p2p_authenticated', 'true')
      
      // Save to browser session as well (for PHP compatibility if needed)
      if (rememberMe.value) {
        sessionStorage.setItem('core_host', coreHost)
        sessionStorage.setItem('core_pass', hashedPassword)
      }
      
      // Remove demo mode flag if it exists
      localStorage.removeItem('p2p_demo_mode')
      
      console.log('Login successful! Core responded with settings.xml')
      successMessage.value = 'Anmeldung erfolgreich!'
      
      // Short delay to show success message
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Redirect to dashboard
      router.push('/dashboard')
      
    } catch (proxyError) {
      console.error('Proxy login failed:', proxyError.message)
      
      // Set appropriate error message based on the error
      if (proxyError.message.includes('nicht erreichbar') || proxyError.message.includes('Proxy server')) {
        errorMessage.value = 'Proxy server ist nicht erreichbar. Bitte starten Sie den Proxy mit: node simple-proxy.cjs'
      } else if (proxyError.message.includes('timeout') || proxyError.message.includes('Timeout')) {
        errorMessage.value = 'Timeout beim Verbinden zum Core. Prüfen Sie die Server-Adresse.'
      } else if (proxyError.message.includes('Core nicht erreichbar')) {
        errorMessage.value = 'Kann nicht zum AppleJuice Core verbinden. Prüfen Sie ob der Core läuft und die Adresse stimmt.'
      } else {
        errorMessage.value = `Verbindung fehlgeschlagen: ${proxyError.message}`
      }
    }
    
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = `Fehler beim Verbinden zum Server: ${error.message}`
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* CoreUI Login Styles - basierend auf der PHP-Vorlage */

/* Base container styling */
.min-vh-100 {
  min-height: 100vh;
}

.bg-body-tertiary {
  background-color: #f8f9fa;
}

/* Card group improvements */
.card-group-custom {
  margin-bottom: 0;
}

.card {
  border: 1px solid rgba(0, 0, 0, 0.125);
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Card group styling for desktop */
@media (min-width: 768px) {
  .card-group-custom .card:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
  
  .card-group-custom .card:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
}

/* Checkbox styling für "Anmeldung merken" */
.checkbox.icheck {
  margin-left: -20px;
}

.checkbox label {
  font-weight: normal;
  cursor: pointer;
}

.checkbox input[type="checkbox"] {
  margin-right: 8px;
}

/* Card styling für das rechte Panel */
.right-panel {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  position: relative;
  overflow: hidden;
}

.right-panel::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
    linear-gradient(45deg, rgba(255,255,255,0.05) 0%, transparent 100%);
  z-index: 1;
}

.right-panel .card-body {
  position: relative;
  z-index: 2;
}

/* Floating labels enhancement */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
  opacity: 0.65;
  transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

/* Button hover effects */
.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

/* Alert styling */
.alert {
  border: none;
  border-radius: 0.375rem;
}

.alert-warning {
  background-color: #fff3cd;
  border-color: #ffecb5;
  color: #664d03;
}

.alert-danger {
  background-color: #f8d7da;
  border-color: #f5c2c7;
  color: #842029;
}

.alert-success {
  background-color: #d1e7dd;
  border-color: #badbcc;
  color: #0f5132;
}

/* Right panel content styling */
.display-1 {
  font-size: 6rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.lead {
  font-size: 1.25rem;
  font-weight: 300;
}

/* Mobile logo styling */
.mobile-logo {
  font-size: 3rem;
  line-height: 1;
}

/* Mobile responsive adjustments */
@media (max-width: 767.98px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .col-lg-8 {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  
  .card {
    border-radius: 0.375rem !important;
    margin-bottom: 1rem;
  }
  
  .display-1 {
    font-size: 3rem;
  }
}

/* Extra small devices */
@media (max-width: 575.98px) {
  .container {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  
  .card-body {
    padding: 1.5rem !important;
  }
  
  .display-1 {
    font-size: 2.5rem;
  }
  
  .lead {
    font-size: 1rem;
  }
  
  h1 {
    font-size: 1.75rem;
  }
  
  h2 {
    font-size: 1.5rem;
  }
  
  .mobile-logo {
    font-size: 2.5rem;
  }
}

/* Loading spinner */
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Form focus states */
.form-control:focus {
  border-color: #86b7fe;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Card shadow */
.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Animation for alerts */
.alert {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading button animation */
.btn:disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

.spinner-border-sm {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>