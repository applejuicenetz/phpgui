<script setup>
import { ref, inject } from 'vue'
import { CFooter, CContainer } from '@coreui/vue'

const lang = inject('language')

// App info
const appInfo = ref({
  name: 'AppleJuice WebUI',
  version: '1.0.0',
  author: 'KDDK22',
  year: new Date().getFullYear(),
  coreUIVersion: '5.1.1'
})

// Get current time
const currentTime = ref(new Date().toLocaleTimeString())

// Update time every second
setInterval(() => {
  currentTime.value = new Date().toLocaleTimeString()
}, 1000)
</script>

<template>
  <CFooter class="footer">
    <CContainer fluid class="px-4">
      <div class="footer-content">
        
        <!-- Left side - App info -->
        <div class="footer-left">
          <div class="app-info">
            <strong>{{ appInfo.name }}</strong>
            <span class="version">v{{ appInfo.version }}</span>
          </div>
          <div class="copyright">
            © {{ appInfo.year }} {{ appInfo.author }}. 
            <span class="d-none d-sm-inline">Alle Rechte vorbehalten.</span>
          </div>
        </div>
        
        <!-- Center - Status info -->
        <div class="footer-center d-none d-md-flex">
          <div class="status-info">
            <i class="bi bi-clock me-1"></i>
            {{ currentTime }}
          </div>
        </div>
        
        <!-- Right side - Links -->
        <div class="footer-right">
          <div class="footer-links">
            <a href="#" @click.prevent="$router.push('/about')" class="footer-link">
              <i class="bi bi-info-circle me-1"></i>
              {{ $t('nav.about') }}
            </a>
            <span class="separator">|</span>
            <a href="https://github.com/applejuicenetz" target="_blank" class="footer-link">
              <i class="bi bi-github me-1"></i>
              GitHub
            </a>
          </div>
          <div class="tech-info d-none d-lg-block">
            <small class="text-muted">
              Powered by Vue.js & CoreUI v{{ appInfo.coreUIVersion }}
            </small>
          </div>
        </div>
        
      </div>
    </CContainer>
  </CFooter>
</template>

<style scoped>
.footer {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
  margin-top: auto;
  padding: 1rem 0;
}

.footer-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  min-height: 40px;
}

/* Left side */
.footer-left {
  flex: 1;
}

.app-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.app-info strong {
  color: var(--cui-primary);
  font-size: 0.9rem;
}

.version {
  background: var(--cui-primary);
  color: white;
  padding: 0.1rem 0.4rem;
  border-radius: 10px;
  font-size: 0.7rem;
  font-weight: 500;
}

.copyright {
  font-size: 0.8rem;
  color: var(--cui-secondary);
  line-height: 1.2;
}

/* Center */
.footer-center {
  flex: 0 0 auto;
}

.status-info {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: var(--cui-secondary);
  background: rgba(var(--cui-primary-rgb), 0.1);
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
}

/* Right side */
.footer-right {
  flex: 1;
  text-align: right;
}

.footer-links {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.footer-link {
  color: var(--cui-secondary);
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.2s ease;
  display: flex;
  align-items: center;
}

.footer-link:hover {
  color: var(--cui-primary);
  text-decoration: none;
}

.separator {
  color: var(--cui-border-color);
  font-size: 0.8rem;
}

.tech-info {
  font-size: 0.75rem;
  color: var(--cui-secondary);
  opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 767.98px) {
  .footer-content {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
  }
  
  .footer-left,
  .footer-right {
    flex: none;
    text-align: center;
  }
  
  .footer-links {
    justify-content: center;
  }
  
  .app-info {
    justify-content: center;
  }
}

@media (max-width: 575.98px) {
  .footer {
    padding: 0.75rem 0;
  }
  
  .footer-links {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .separator {
    display: none;
  }
  
  .version {
    font-size: 0.65rem;
    padding: 0.05rem 0.3rem;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .footer {
    background: rgba(45, 55, 72, 0.95);
    border-top-color: rgba(255, 255, 255, 0.1);
  }
  
  .status-info {
    background: rgba(66, 153, 225, 0.2);
  }
}

/* Animation for time */
.status-info {
  transition: all 0.3s ease;
}

.status-info:hover {
  background: rgba(var(--cui-primary-rgb), 0.15);
  transform: scale(1.02);
}

/* Pulse animation for version badge */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(var(--cui-primary-rgb), 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(var(--cui-primary-rgb), 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(var(--cui-primary-rgb), 0);
  }
}

.version:hover {
  animation: pulse 1.5s infinite;
}
</style>