<script setup>
import { ref, provide } from 'vue'
import AppFooter from '@/components/AppFooter.vue'
import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'

const sidebarVisible = ref(true)

// Provide sidebar state to child components
provide('sidebarVisible', sidebarVisible)

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}

// Listen for sidebar toggle events
window.addEventListener('sidebar-toggle', () => {
  toggleSidebar()
})
</script>

<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <AppSidebar :visible="sidebarVisible" />
    
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'sidebar-open': sidebarVisible }">
      <!-- Header -->
      <AppHeader @toggle-sidebar="toggleSidebar" />
      
      <!-- Page Content -->
      <main class="content-area">
        <div class="container-fluid px-4 py-3">
          <router-view />
        </div>
      </main>
      
      <!-- Footer -->
      <AppFooter />
    </div>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-left: 0;
  transition: margin-left 0.3s ease;
}

.main-content.sidebar-open {
  margin-left: 280px;
}

.content-area {
  flex: 1;
  overflow-y: auto;
}

/* Mobile responsive */
@media (max-width: 991.98px) {
  .main-content.sidebar-open {
    margin-left: 0;
  }
}

/* Container adjustments */
.container-fluid {
  max-width: none;
}
</style>