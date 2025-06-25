import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import DefaultLayout from '../layouts/DefaultLayout.vue'
import AuthLayout from '../layouts/AuthLayout.vue'

// Views
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import AboutView from '../views/AboutView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    
    // Auth routes (without layout)
    {
      path: '/auth',
      component: AuthLayout,
      children: [
        {
          path: '/login',
          name: 'login',
          component: LoginView,
        }
      ]
    },
    
    // Main app routes (with default layout)
    {
      path: '/',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: DashboardView,
        },
        {
          path: '/downloads',
          name: 'downloads',
          component: () => import('../views/DownloadsView.vue'),
        },
        {
          path: '/uploads',
          name: 'uploads',
          component: () => import('../views/UploadsView.vue'),
        },
        {
          path: '/search',
          name: 'search',
          component: () => import('../views/SearchView.vue'),
        },
        {
          path: '/server',
          name: 'server',
          component: () => import('../views/ServerStatusView.vue'),
        },
        {
          path: '/server/connections',
          name: 'server-connections',
          component: () => import('../views/ServerConnectionsView.vue'),
        },
        {
          path: '/settings',
          name: 'settings',
          component: () => import('../views/SettingsView.vue'),
        },
        {
          path: '/about',
          name: 'about',
          component: AboutView,
        }
      ]
    },
    
    // Catch all route
    {
      path: '/:pathMatch(.*)*',
      redirect: '/dashboard'
    }
  ],
})

// Navigation guard for authentication
router.beforeEach(async (to, from, next) => {
  // Check authentication via localStorage first (fast)
  const localAuth = localStorage.getItem('p2p_authenticated')
  
  if (to.meta.requiresAuth) {
    if (!localAuth) {
      // Check session authentication via proxy
      try {
        const response = await fetch('http://localhost:3001/session/status', {
          credentials: 'include'
        })
        const sessionStatus = await response.json()
        
        if (sessionStatus.authenticated) {
          // Session is authenticated, allow access
          next()
        } else {
          // Not authenticated, redirect to login
          next('/login')
        }
      } catch (error) {
        console.warn('Could not check session status:', error)
        next('/login')
      }
    } else {
      next()
    }
  } else if (to.path === '/login' && localAuth) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router

