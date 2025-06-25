/**
 * Configuration utilities for AppleJuice WebUI
 * Equivalent to PHP config.js functionality
 */

// WebUI Configuration
export const WEBUI_CONFIG = {
  TITLE: 'AppleJuice WebUI',
  VERSION: '1.0.0',
  AUTHOR: 'KDDK22',
  DESCRIPTION: 'AppleJuice P2P WebUI - Modern Vue.js Interface',
  
  // Theme Configuration
  THEME: {
    PRIMARY_COLOR: '#0d6efd',
    SECONDARY_COLOR: '#6c757d',
    SUCCESS_COLOR: '#28a745',
    DANGER_COLOR: '#dc3545',
    WARNING_COLOR: '#ffc107',
    INFO_COLOR: '#17a2b8',
    
    // Gradient Backgrounds
    GRADIENT_PRIMARY: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
    GRADIENT_CARD: 'rgba(255, 255, 255, 0.95)',
    
    // Animation Settings
    TRANSITION_DURATION: '0.3s',
    HOVER_TRANSFORM: 'translateY(-2px)',
    CARD_SHADOW: '0 8px 25px rgba(0, 0, 0, 0.1)',
    CARD_SHADOW_HOVER: '0 12px 35px rgba(0, 0, 0, 0.15)'
  },
  
  // API Configuration
  API: {
    DEFAULT_PORT: '9854',
    TIMEOUT: 10000,
    RETRY_ATTEMPTS: 3,
    RETRY_DELAY: 1000
  },
  
  // UI Settings
  UI: {
    ITEMS_PER_PAGE: 25,
    REFRESH_INTERVAL: 30000, // 30 seconds
    ANIMATION_DELAY: 100,
    LOADING_MIN_TIME: 1000
  }
}

// Color Mode Management (like PHP color-modes.js)
export class ColorModeManager {
  constructor() {
    this.storageKey = 'aj-theme'
    this.defaultTheme = 'light'
    this.init()
  }
  
  init() {
    // Get stored theme or use default
    const storedTheme = this.getStoredTheme()
    const preferredTheme = storedTheme || this.getPreferredTheme()
    
    this.setTheme(preferredTheme)
    this.showActiveTheme(preferredTheme)
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if (!this.getStoredTheme()) {
        this.setTheme(this.getPreferredTheme())
      }
    })
  }
  
  getStoredTheme() {
    return localStorage.getItem(this.storageKey)
  }
  
  setStoredTheme(theme) {
    localStorage.setItem(this.storageKey, theme)
  }
  
  getPreferredTheme() {
    const storedTheme = this.getStoredTheme()
    if (storedTheme) {
      return storedTheme
    }
    
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }
  
  setTheme(theme) {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-bs-theme', 'dark')
    } else {
      document.documentElement.setAttribute('data-bs-theme', theme)
    }
  }
  
  showActiveTheme(theme, focus = false) {
    const themeSwitcher = document.querySelector('#bd-theme')
    
    if (!themeSwitcher) {
      return
    }
    
    const themeSwitcherText = document.querySelector('#bd-theme-text')
    const activeThemeIcon = document.querySelector('.theme-icon-active use')
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
    const svgOfActiveBtn = btnToActive?.querySelector('svg use')?.getAttribute('href')
    
    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
      element.classList.remove('active')
      element.setAttribute('aria-pressed', 'false')
    })
    
    if (btnToActive) {
      btnToActive.classList.add('active')
      btnToActive.setAttribute('aria-pressed', 'true')
    }
    
    if (activeThemeIcon && svgOfActiveBtn) {
      activeThemeIcon.setAttribute('href', svgOfActiveBtn)
    }
    
    const themeSwitcherLabel = `${themeSwitcherText?.textContent} (${btnToActive?.dataset.bsThemeValue})`
    themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)
    
    if (focus) {
      themeSwitcher.focus()
    }
  }
  
  // Public methods for theme switching
  switchToLight() {
    this.setStoredTheme('light')
    this.setTheme('light')
    this.showActiveTheme('light', true)
  }
  
  switchToDark() {
    this.setStoredTheme('dark')
    this.setTheme('dark')
    this.showActiveTheme('dark', true)
  }
  
  switchToAuto() {
    this.setStoredTheme('auto')
    this.setTheme('auto')
    this.showActiveTheme('auto', true)
  }
}

// Animation utilities
export const AnimationUtils = {
  // Fade in animation
  fadeIn(element, duration = 300) {
    element.style.opacity = '0'
    element.style.display = 'block'
    
    let start = null
    const animate = (timestamp) => {
      if (!start) start = timestamp
      const progress = timestamp - start
      
      element.style.opacity = Math.min(progress / duration, 1)
      
      if (progress < duration) {
        requestAnimationFrame(animate)
      }
    }
    
    requestAnimationFrame(animate)
  },
  
  // Fade out animation
  fadeOut(element, duration = 300) {
    let start = null
    const animate = (timestamp) => {
      if (!start) start = timestamp
      const progress = timestamp - start
      
      element.style.opacity = Math.max(1 - (progress / duration), 0)
      
      if (progress < duration) {
        requestAnimationFrame(animate)
      } else {
        element.style.display = 'none'
      }
    }
    
    requestAnimationFrame(animate)
  },
  
  // Slide up animation
  slideUp(element, duration = 300) {
    element.style.transform = 'translateY(20px)'
    element.style.opacity = '0'
    element.style.display = 'block'
    
    let start = null
    const animate = (timestamp) => {
      if (!start) start = timestamp
      const progress = timestamp - start
      const easeProgress = this.easeOutCubic(progress / duration)
      
      element.style.transform = `translateY(${20 * (1 - easeProgress)}px)`
      element.style.opacity = Math.min(easeProgress, 1)
      
      if (progress < duration) {
        requestAnimationFrame(animate)
      }
    }
    
    requestAnimationFrame(animate)
  },
  
  // Easing function
  easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3)
  }
}

// Utility functions
export const Utils = {
  // Format file size
  formatFileSize(bytes) {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  },
  
  // Format uptime
  formatUptime(seconds) {
    const days = Math.floor(seconds / 86400)
    const hours = Math.floor((seconds % 86400) / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    
    if (days > 0) {
      return `${days}d ${hours}h ${minutes}m`
    } else if (hours > 0) {
      return `${hours}h ${minutes}m`
    } else {
      return `${minutes}m`
    }
  },
  
  // Format credits
  formatCredits(credits) {
    if (credits >= 1000000) {
      return (credits / 1000000).toFixed(1) + 'M'
    } else if (credits >= 1000) {
      return (credits / 1000).toFixed(1) + 'K'
    }
    return credits.toString()
  },
  
  // Debounce function
  debounce(func, wait) {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  },
  
  // Throttle function
  throttle(func, limit) {
    let inThrottle
    return function() {
      const args = arguments
      const context = this
      if (!inThrottle) {
        func.apply(context, args)
        inThrottle = true
        setTimeout(() => inThrottle = false, limit)
      }
    }
  }
}

// Initialize color mode manager
export const colorModeManager = new ColorModeManager()