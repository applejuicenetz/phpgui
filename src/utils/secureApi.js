/**
 * Secure API utilities with token-based authentication
 * Enhanced version with JWT support, rate limiting handling, and security features
 */

const PHP_API_BASE_URL = 'http://localhost:8080/api/index.php?path='

/**
 * Enhanced API request with JWT token support
 */
class SecureApiClient {
  constructor() {
    this.tokenKey = 'p2p_jwt_token'
    this.refreshInProgress = false
  }

  /**
   * Get stored JWT token
   */
  getToken() {
    return localStorage.getItem(this.tokenKey)
  }

  /**
   * Store JWT token
   */
  setToken(token) {
    if (token) {
      localStorage.setItem(this.tokenKey, token)
    } else {
      localStorage.removeItem(this.tokenKey)
    }
  }

  /**
   * Check if token is expired (basic check)
   */
  isTokenExpired(token) {
    if (!token) return true
    
    try {
      const payload = JSON.parse(atob(token.split('.')[1]))
      return payload.exp < (Date.now() / 1000)
    } catch (error) {
      return true
    }
  }

  /**
   * Refresh JWT token
   */
  async refreshToken() {
    if (this.refreshInProgress) {
      // Wait for ongoing refresh
      return new Promise((resolve) => {
        const checkRefresh = () => {
          if (!this.refreshInProgress) {
            resolve({ success: true, token: this.getToken() })
          } else {
            setTimeout(checkRefresh, 100)
          }
        }
        checkRefresh()
      })
    }

    this.refreshInProgress = true
    const currentToken = this.getToken()
    
    if (!currentToken) {
      this.refreshInProgress = false
      return { success: false, error: 'No token to refresh' }
    }

    try {
      const response = await fetch(`${PHP_API_BASE_URL}refresh-token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${currentToken}`
        },
        credentials: 'include'
      })

      const data = await response.json()

      if (response.ok && data.token) {
        this.setToken(data.token)
        this.refreshInProgress = false
        return { success: true, token: data.token }
      } else {
        this.setToken(null)
        this.refreshInProgress = false
        return { success: false, error: data.error || 'Token refresh failed' }
      }
    } catch (error) {
      this.setToken(null)
      this.refreshInProgress = false
      return { success: false, error: error.message }
    }
  }

  /**
   * Make authenticated API request
   */
  async request(endpoint, options = {}) {
    const url = `${PHP_API_BASE_URL}${endpoint}`
    
    const defaultOptions = {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include'
    }

    // Add JWT token if available
    const token = this.getToken()
    if (token && !this.isTokenExpired(token)) {
      defaultOptions.headers['Authorization'] = `Bearer ${token}`
    }

    const requestOptions = { ...defaultOptions, ...options }
    
    // Merge headers properly
    if (options.headers) {
      requestOptions.headers = { ...defaultOptions.headers, ...options.headers }
    }

    try {
      console.log(`🔒 Secure API Request: ${requestOptions.method} ${url}`)
      
      const response = await fetch(url, requestOptions)
      let data
      
      try {
        data = await response.json()
      } catch (parseError) {
        data = { error: 'Invalid JSON response' }
      }

      // Handle token expiration
      if (response.status === 401 && data.error && data.error.includes('token')) {
        console.log('🔄 Token expired, attempting refresh...')
        const refreshResult = await this.refreshToken()
        
        if (refreshResult.success) {
          // Retry request with new token
          requestOptions.headers['Authorization'] = `Bearer ${refreshResult.token}`
          const retryResponse = await fetch(url, requestOptions)
          const retryData = await retryResponse.json()
          
          if (retryResponse.ok) {
            // Store new token if provided
            if (retryData.token) {
              this.setToken(retryData.token)
            }
            
            return {
              success: true,
              data: retryData.data || retryData,
              status: retryResponse.status
            }
          }
        } else {
          // Refresh failed, redirect to login
          this.handleAuthFailure()
          throw new Error('Authentication failed. Please login again.')
        }
      }

      // Handle rate limiting
      if (response.status === 429) {
        const retryAfter = data.remaining_time || 60
        const blockedUntil = data.blocked_until || 'unknown'
        
        console.warn(`🚫 Rate limited until: ${blockedUntil}`)
        
        throw new Error(`Too many requests. Please wait ${retryAfter} seconds before trying again.`)
      }

      // Handle other errors
      if (!response.ok) {
        const errorMessage = data.message || data.error || `HTTP ${response.status}`
        throw new Error(errorMessage)
      }

      // Store new token if provided
      if (data.token) {
        this.setToken(data.token)
      }

      return {
        success: true,
        data: data.data || data,
        status: response.status,
        headers: response.headers
      }

    } catch (error) {
      console.error('🔒 Secure API Request failed:', error)
      
      // Clear invalid tokens
      if (error.message.includes('token') || error.message.includes('Authentication')) {
        this.setToken(null)
      }

      return {
        success: false,
        error: error.message,
        status: error.status || 500
      }
    }
  }

  /**
   * Handle authentication failure
   */
  handleAuthFailure() {
    this.setToken(null)
    
    // Clear all auth-related data
    localStorage.removeItem('p2p_host')
    localStorage.removeItem('p2p_authenticated')
    
    // Redirect to login if not already there
    if (window.location.pathname !== '/login') {
      console.log('🔐 Authentication failed, redirecting to login...')
      window.location.href = '/login'
    }
  }

  /**
   * Login with enhanced security
   */
  async login(host, password) {
    try {
      const hashedPassword = await this.hashPassword(password)
      
      const result = await this.request('login', {
        method: 'POST',
        body: JSON.stringify({
          host: host,
          password: hashedPassword
        })
      })

      if (result.success) {
        // Store authentication data
        localStorage.setItem('p2p_host', host)
        localStorage.setItem('p2p_authenticated', 'true')
        
        if (result.data.token) {
          this.setToken(result.data.token)
        }

        console.log('✅ Login successful with enhanced security')
        return result
      } else {
        console.error('❌ Login failed:', result.error)
        return result
      }
    } catch (error) {
      console.error('❌ Login error:', error)
      return {
        success: false,
        error: error.message
      }
    }
  }

  /**
   * Logout with token cleanup
   */
  async logout() {
    try {
      const result = await this.request('logout', {
        method: 'POST'
      })

      // Clear all stored data regardless of API response
      this.setToken(null)
      localStorage.removeItem('p2p_host')
      localStorage.removeItem('p2p_authenticated')

      console.log('✅ Logout completed')
      return result
    } catch (error) {
      // Still clear local data even if API call fails
      this.setToken(null)
      localStorage.removeItem('p2p_host')
      localStorage.removeItem('p2p_authenticated')
      
      return {
        success: false,
        error: error.message
      }
    }
  }

  /**
   * Check authentication status
   */
  async checkAuth() {
    try {
      const result = await this.request('auth')
      
      if (result.success && result.data.authenticated) {
        return {
          success: true,
          authenticated: true,
          data: result.data
        }
      } else {
        this.handleAuthFailure()
        return {
          success: true,
          authenticated: false
        }
      }
    } catch (error) {
      return {
        success: false,
        authenticated: false,
        error: error.message
      }
    }
  }

  /**
   * Hash password (MD5 for AppleJuice compatibility)
   */
  async hashPassword(password) {
    // Use crypto.subtle if available
    if (window.crypto && window.crypto.subtle) {
      try {
        const encoder = new TextEncoder()
        const data = encoder.encode(password)
        const hashBuffer = await crypto.subtle.digest('MD5', data)
        const hashArray = Array.from(new Uint8Array(hashBuffer))
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('')
      } catch (error) {
        // Fallback to simple hash
        return this.simpleHash(password)
      }
    } else {
      return this.simpleHash(password)
    }
  }

  /**
   * Simple hash fallback
   */
  simpleHash(str) {
    let hash = 0
    for (let i = 0; i < str.length; i++) {
      const char = str.charCodeAt(i)
      hash = ((hash << 5) - hash) + char
      hash = hash & hash // Convert to 32bit integer
    }
    return Math.abs(hash).toString(16).padStart(8, '0')
  }

  /**
   * Get security info
   */
  async getSecurityInfo() {
    const token = this.getToken()
    const isAuthenticated = localStorage.getItem('p2p_authenticated') === 'true'
    
    return {
      hasToken: !!token,
      tokenExpired: token ? this.isTokenExpired(token) : true,
      isAuthenticated: isAuthenticated,
      host: localStorage.getItem('p2p_host')
    }
  }
}

// Create singleton instance
const secureApi = new SecureApiClient()

// Export for use in components
export default secureApi

// Also export individual methods for backward compatibility
export const {
  login: secureLogin,
  logout: secureLogout,
  checkAuth: secureCheckAuth,
  request: secureRequest
} = secureApi