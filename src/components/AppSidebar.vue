<script setup>
import { inject } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  visible: {
    type: Boolean,
    default: true
  }
})

const route = useRoute()
const lang = inject('language')

// Navigation items
const navigationItems = [
  {
    name: 'nav.dashboard',
    to: '/dashboard',
    icon: 'bi bi-speedometer2',
    badge: 'NEW'
  },
  {
    type: 'divider',
    name: 'P2P'
  },
  {
    name: 'nav.downloads',
    to: '/downloads',
    icon: 'bi bi-download'
  },
  {
    name: 'nav.uploads',
    to: '/uploads',
    icon: 'bi bi-upload'
  },
  {
    name: 'nav.search',
    to: '/search',
    icon: 'bi bi-search'
  },
  {
    name: 'nav.server',
    to: '/server',
    icon: 'bi bi-hdd-network'
  },
  {
    name: 'nav.connections',
    to: '/server/connections',
    icon: 'bi bi-diagram-3'
  },
  {
    type: 'divider',
    name: 'System'
  },
  {
    name: 'nav.settings',
    to: '/settings',
    icon: 'bi bi-gear'
  },
  {
    name: 'nav.about',
    to: '/about',
    icon: 'bi bi-info-circle'
  }
]
</script>

<template>
  <aside class="sidebar" :class="{ 'sidebar-visible': visible }">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
      <router-link to="/dashboard" class="brand-link">
        <div class="brand-icon">🍎</div>
        <div class="brand-text">
          <div class="brand-title">AppleJuice</div>
          <div class="brand-subtitle">P2P WebUI</div>
        </div>
      </router-link>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
      <template v-for="item in navigationItems" :key="item.name">
        
        <!-- Divider -->
        <div v-if="item.type === 'divider'" class="nav-divider">
          {{ $t ? $t(item.name) : item.name }}
        </div>
        
        <!-- Group -->
        <div v-else-if="item.type === 'group'" class="nav-group">
          <div class="nav-group-header">
            <i :class="item.icon" class="nav-icon"></i>
            {{ $t ? $t(item.name) : item.name }}
          </div>
          <div class="nav-group-items">
            <router-link
              v-for="subItem in item.items"
              :key="subItem.name"
              :to="subItem.to"
              class="nav-link nav-sub-link"
              :class="{ 'active': route.path === subItem.to }"
            >
              <i :class="subItem.icon" class="nav-icon"></i>
              {{ subItem.name }}
            </router-link>
          </div>
        </div>
        
        <!-- Regular Nav Item -->
        <router-link 
          v-else
          :to="item.to"
          class="nav-link"
          :class="{ 'active': route.path === item.to }"
        >
          <i :class="item.icon" class="nav-icon"></i>
          {{ $t ? $t(item.name) : item.name }}
          <span v-if="item.badge" class="nav-badge">{{ item.badge }}</span>
        </router-link>
        
      </template>
    </nav>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 280px;
  position: fixed;
  top: 0;
  left: -280px;
  height: 100vh;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-right: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  overflow-y: auto;
  transition: left 0.3s ease;
}

.sidebar-visible {
  left: 0;
}

/* Sidebar Brand */
.sidebar-brand {
  padding: 1.5rem 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  background: rgba(13, 110, 253, 0.05);
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
}

.brand-icon {
  font-size: 2rem;
  line-height: 1;
}

.brand-text {
  flex: 1;
}

.brand-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: #0d6efd;
  line-height: 1.2;
  margin: 0;
}

.brand-subtitle {
  font-size: 0.8rem;
  color: #6c757d;
  line-height: 1;
  margin: 0;
}

/* Navigation */
.sidebar-nav {
  padding: 1rem 0;
  flex: 1;
}

.nav-icon {
  width: 1.25rem;
  margin-right: 0.75rem;
  text-align: center;
  font-size: 1rem;
}

/* Navigation Dividers */
.nav-divider {
  padding: 0.75rem 1rem 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6c757d;
  margin-top: 1rem;
}

.nav-divider:first-child {
  margin-top: 0;
}

/* Navigation Links */
.nav-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #495057;
  text-decoration: none;
  transition: all 0.2s ease;
  border-radius: 0;
}

.nav-link:hover {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.nav-link.active {
  background-color: #0d6efd;
  color: white;
  font-weight: 500;
}

.nav-link.active .nav-icon {
  color: white;
}

/* Navigation Groups */
.nav-group {
  margin: 0.5rem 0;
}

.nav-group-header {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #495057;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.nav-group-header:hover {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.nav-group-items {
  background-color: rgba(0, 0, 0, 0.02);
}

.nav-sub-link {
  padding-left: 3rem;
  font-size: 0.9rem;
}

/* Badges */
.nav-badge {
  margin-left: auto;
  background-color: #17a2b8;
  color: white;
  font-size: 0.65rem;
  padding: 0.25em 0.5em;
  border-radius: 10px;
}

/* Mobile responsive */
@media (max-width: 991.98px) {
  .sidebar {
    left: -280px;
  }
  
  .sidebar-visible {
    left: 0;
  }
}

/* Desktop - always show sidebar */
@media (min-width: 992px) {
  .sidebar {
    left: 0;
  }
}
</style>