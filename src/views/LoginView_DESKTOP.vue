<template>
  <div class="desktop-login">
    <div class="login-container">
      
      <!-- Login Card -->
      <div class="login-card">
        
        <!-- Header -->
        <div class="login-header">
          <div class="logo">🍎</div>
          <h1>AppleJuice P2P</h1>
          <p>Mit deinem P2P-Core verbinden</p>
        </div>
        
        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="login-form">
          
          <!-- Server Address -->
          <div class="form-group">
            <label>Server Adresse</label>
            <input
              v-model="serverAddress"
              type="text"
              placeholder="z.B. 192.168.1.100:9854"
              required
              class="form-input"
            />
          </div>
          
          <!-- Password -->
          <div class="form-group">
            <label>Passwort</label>
            <input
              v-model="password"
              type="password"
              placeholder="Dein P2P-Core Passwort"
              required
              class="form-input"
            />
          </div>
          
          <!-- Submit Button -->
          <button 
            type="submit" 
            class="login-button"
            :disabled="isLoading"
          >
            <span v-if="isLoading" class="spinner"></span>
            {{ isLoading ? 'Verbinde...' : 'Anmelden' }}
          </button>
          
          <!-- Help Button -->
          <button type="button" class="help-button" @click="showHelp = !showHelp">
            Verbindungshilfe
          </button>
          
        </form>
        
        <!-- Help Section -->
        <div v-if="showHelp" class="help-section">
          <h4>Verbindungshilfe</h4>
          <ul>
            <li><strong>Server-Format:</strong> IP:Port (z.B. 192.168.1.100:9854)</li>
            <li><strong>Standard-Port:</strong> meist 9854</li>
            <li><strong>Passwort:</strong> das gleiche wie in deinem P2P-Core</li>
          </ul>
        </div>
        
        <!-- Error Alert -->
        <div v-if="errorMessage" class="error-alert">
          <strong>Fehler:</strong> {{ errorMessage }}
          <button @click="errorMessage = ''" class="close-btn">×</button>
        </div>
        
        <!-- Success Alert -->
        <div v-if="successMessage" class="success-alert">
          <strong>Erfolg:</strong> {{ successMessage }}
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
.desktop-login {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.login-container {
  width: 100%;
  max-width: 600px;
  min-width: 400px;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.login-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo {
  font-size: 80px;
  margin-bottom: 20px;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.login-header h1 {
  color: #0d6efd;
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 10px;
}

.login-header p {
  color: #6c757d;
  font-size: 1.1rem;
  margin: 0;
}

.login-form {
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #495057;
  margin-bottom: 8px;
  font-size: 1.1rem;
}

.form-input {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  background: white;
}

.form-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
  transform: translateY(-2px);
}

.login-button {
  width: 100%;
  padding: 15px;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1.2rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.login-button:hover:not(:disabled) {
  background: #0b5ed7;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.help-button {
  width: 100%;
  padding: 10px;
  background: transparent;
  color: #0d6efd;
  border: 2px solid #0d6efd;
  border-radius: 10px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.help-button:hover {
  background: #0d6efd;
  color: white;
}

.help-section {
  background: #e7f3ff;
  border: 1px solid #b8daff;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
}

.help-section h4 {
  color: #0c5460;
  margin-bottom: 15px;
}

.help-section ul {
  margin: 0;
  padding-left: 20px;
}

.help-section li {
  margin-bottom: 8px;
  color: #0c5460;
}

.error-alert {
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 20px;
  position: relative;
}

.success-alert {
  background: #d1edff;
  border: 1px solid #b8daff;
  color: #0c5460;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 20px;
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #721c24;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #ffffff;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive für kleinere Bildschirme */
@media (max-width: 768px) {
  .login-container {
    min-width: auto;
    margin: 10px;
  }
  
  .login-card {
    padding: 30px 20px;
  }
  
  .logo {
    font-size: 60px;
  }
  
  .login-header h1 {
    font-size: 2rem;
  }
}
</style>
</template>