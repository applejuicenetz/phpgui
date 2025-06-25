<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
          
          <div class="card shadow-lg border-0" style="border-radius: 1rem; min-width: 400px; max-width: 500px;">
            <div class="card-body p-5">
              
              <!-- Header -->
              <div class="text-center mb-4">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🍎</div>
                <h1 class="h2 text-primary fw-bold">AppleJuice P2P</h1>
                <p class="text-muted">Mit deinem P2P-Core verbinden</p>
              </div>
              
              <!-- Login Form -->
              <form @submit.prevent="handleLogin">
                
                <!-- Server Address -->
                <div class="mb-3">
                  <label for="serverAddress" class="form-label fw-semibold">Server Adresse</label>
                  <input
                    id="serverAddress"
                    v-model="serverAddress"
                    type="text"
                    class="form-control form-control-lg"
                    placeholder="z.B. 192.168.1.100:9854"
                    required
                    style="border-radius: 0.5rem;"
                  />
                </div>
                
                <!-- Password -->
                <div class="mb-4">
                  <label for="password" class="form-label fw-semibold">Passwort</label>
                  <input
                    id="password"
                    v-model="password"
                    type="password"
                    class="form-control form-control-lg"
                    placeholder="Dein P2P-Core Passwort"
                    required
                    style="border-radius: 0.5rem;"
                  />
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid mb-3">
                  <button 
                    type="submit" 
                    class="btn btn-primary btn-lg"
                    :disabled="isLoading"
                    style="border-radius: 0.5rem; font-weight: 600;"
                  >
                    <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                    {{ isLoading ? 'Verbinde...' : 'Anmelden' }}
                  </button>
                </div>
                
                <!-- Help Button -->
                <div class="text-center">
                  <button type="button" class="btn btn-link" @click="showHelp = !showHelp">
                    Verbindungshilfe
                  </button>
                </div>
                
              </form>
              
              <!-- Help Section -->
              <div v-if="showHelp" class="alert alert-info mt-3" style="border-radius: 0.5rem;">
                <h6 class="alert-heading">Verbindungshilfe</h6>
                <ul class="mb-0">
                  <li><strong>Server-Format:</strong> <code>IP:Port</code> (z.B. <code>192.168.1.100:9854</code>)</li>
                  <li><strong>Standard-Port:</strong> meist <code>9854</code></li>
                  <li><strong>Passwort:</strong> das gleiche wie in deinem P2P-Core</li>
                </ul>
              </div>
              
              <!-- Error Alert -->
              <div 
                v-if="errorMessage" 
                class="alert alert-danger mt-3 alert-dismissible fade show"
                style="border-radius: 0.5rem;"
              >
                <strong>Fehler:</strong> {{ errorMessage }}
                <button type="button" class="btn-close" @click="errorMessage = ''"></button>
              </div>
              
              <!-- Success Alert -->
              <div 
                v-if="successMessage" 
                class="alert alert-success mt-3"
                style="border-radius: 0.5rem;"
              >
                <strong>Erfolg:</strong> {{ successMessage }}
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
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showHelp = ref(false)

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
    // Hash password with MD5 (check if already 32 chars = already hashed)
    const hashedPassword = password.value.length === 32 ? password.value : CryptoJS.MD5(password.value).toString()
    
    // Build authentication URL like in PHP: host/xml/settings.xml?&password=hash
    const coreHost = serverAddress.value.startsWith('http://') ? serverAddress.value : `http://${serverAddress.value}`
    const anfrage = "settings.xml"
    const type = "xml"
    
    // Build URL parameters
    const params = new URLSearchParams()
    params.append('password', hashedPassword)
    
    // Build final URL: host/xml/settings.xml?&password=hash
    const authUrl = `${coreHost}/${type}/${anfrage}?&${params.toString()}`
    
    console.log('Attempting login to:', authUrl.replace(hashedPassword, '***'))
    
    // Make authentication request using proxy utility
    const responseText = await makeProxyRequest(authUrl)
    
    // Check for empty response (can't connect to core)
    if (!responseText || responseText.trim() === '') {
      errorMessage.value = 'Kann nicht zum Core verbinden'
      return
    }
    
    // Check for wrong password message
    if (responseText.includes('wrong password.')) {
      errorMessage.value = 'Falsches Passwort'
      return
    }
    
    // If we get here, login was successful
    // Save authentication data
    localStorage.setItem('p2p_server_address', serverAddress.value)
    localStorage.setItem('p2p_core_host', coreHost)
    localStorage.setItem('p2p_core_pass', hashedPassword)
    localStorage.setItem('p2p_authenticated', 'true')
    
    // Redirect to dashboard
    router.push('/dashboard')
    
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = `Fehler beim Verbinden zum Server: ${error.message}`
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Desktop-optimierte Styles */
@media (min-width: 992px) {
  .card {
    min-width: 500px !important;
    max-width: 600px !important;
  }
  
  .card-body {
    padding: 3rem !important;
  }
  
  .form-control-lg {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
  }
  
  .btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
  }
}

/* Allgemeine Verbesserungen */
.form-control-lg {
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.form-control-lg:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  transform: translateY(-1px);
}

.btn-primary {
  transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
}

.card {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95) !important;
}

/* Responsive Anpassungen */
@media (max-width: 768px) {
  .card-body {
    padding: 2rem !important;
  }
  
  .card {
    margin: 1rem;
    min-width: auto !important;
  }
}
</style>