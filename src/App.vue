<script setup>
import { onBeforeMount } from 'vue'
import { useColorModes } from '@coreui/vue'

// Theme store (erstellen wir eine einfache Version)
const useThemeStore = () => ({
  theme: 'dark'
})

const { isColorModeSet, setColorMode } = useColorModes(
  'applejuice-p2p-theme',
)
const currentTheme = useThemeStore()

onBeforeMount(() => {
  const urlParams = new URLSearchParams(window.location.href.split('?')[1])
  let theme = urlParams.get('theme')

  if (theme !== null && theme.match(/^[A-Za-z0-9\s]+/)) {
    theme = theme.match(/^[A-Za-z0-9\s]+/)[0]
  }

  if (theme) {
    setColorMode(theme)
    return
  }

  if (isColorModeSet()) {
    return
  }

  setColorMode(currentTheme.theme)
})
</script>

<template>
  <div id="app">
    <router-view />
  </div>
</template>

<style lang="scss">
// Custom App Styles (CoreUI wird bereits in main.js geladen)
body {
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

* {
  box-sizing: border-box;
}

#app {
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

// Zusätzliche CoreUI-Fixes
.bg-body-tertiary {
  background-color: #f8f9fa !important;
}

.card {
  border: 1px solid rgba(0, 0, 0, 0.125);
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-floating > .form-control {
  height: calc(3.5rem + 2px);
  line-height: 1.25;
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

// Layout System Fixes
body {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  margin: 0;
  padding: 0;
}

#app {
  min-height: 100vh;
}

// CoreUI Sidebar Fixes
.sidebar {
  --cui-sidebar-width: 280px;
}

// Page Header Styles (global für alle Views)
.page-header {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-subtitle {
  font-size: 1.1rem;
  margin: 0.5rem 0 0 0;
}

// Card Enhancements
.card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

// Animation Classes
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

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

// Responsive Utilities
@media (max-width: 768px) {
  .page-header {
    padding: 1.5rem;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .page-subtitle {
    font-size: 1rem;
  }
}

// Loading Animation
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
}

// Layout System Fixes
body {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  margin: 0;
  padding: 0;
}

#app {
  min-height: 100vh;
}

// CoreUI Sidebar Fixes
.sidebar {
  --cui-sidebar-width: 280px;
}

// Page Header Styles (global für alle Views)
.page-header {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-subtitle {
  font-size: 1.1rem;
  margin: 0.5rem 0 0 0;
}

// Card Enhancements
.card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

// Animation Classes
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

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

// Responsive Utilities
@media (max-width: 768px) {
  .page-header {
    padding: 1.5rem;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .page-subtitle {
    font-size: 1rem;
  }
}
</style>
