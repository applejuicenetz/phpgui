/**
 * AppleJuice API Service
 * 
 * Handles all communication with the PHP API backend
 */

import axios from 'axios'

// Create axios instance with default configuration
const apiClient = axios.create({
  baseURL: 'http://localhost:8080/beta/aj-vue/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor for debugging
apiClient.interceptors.request.use(
  (config) => {
    console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`)
    return config
  },
  (error) => {
    console.error('❌ API Request Error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => {
    console.log(`✅ API Response: ${response.status} ${response.config.url}`)
    return response
  },
  (error) => {
    console.error('❌ API Response Error:', error.response?.data || error.message)
    
    // Handle specific error cases
    if (error.response?.status === 401) {
      console.warn('🔐 Authentication required')
    } else if (error.response?.status === 404) {
      console.warn('🔍 Resource not found')
    } else if (error.response?.status >= 500) {
      console.error('🔥 Server error')
    }
    
    return Promise.reject(error)
  }
)

// API Service class
class ApiService {
  
  // Server Management
  async getServers() {
    try {
      const response = await apiClient.get('/servers')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to fetch servers')
    }
  }
  
  async getServerInfo(serverId) {
    try {
      const response = await apiClient.get(`/server/${serverId}`)
      return response.data
    } catch (error) {
      throw this.handleError(error, `Failed to fetch server ${serverId}`)
    }
  }
  
  async connectToServer(serverId) {
    try {
      const response = await apiClient.post('/server/connect', {
        server_id: serverId
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, `Failed to connect to server ${serverId}`)
    }
  }
  
  async disconnectFromServer() {
    try {
      const response = await apiClient.post('/server/disconnect')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to disconnect from server')
    }
  }
  
  // Downloads Management
  async getDownloads() {
    try {
      const response = await apiClient.get('/downloads')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to fetch downloads')
    }
  }
  
  async startDownload(fileId) {
    try {
      const response = await apiClient.post('/download', {
        file_id: fileId
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, `Failed to start download ${fileId}`)
    }
  }
  
  // Uploads Management
  async getUploads() {
    try {
      const response = await apiClient.get('/uploads')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to fetch uploads')
    }
  }
  
  // Search
  async getSearchResults(query) {
    try {
      const response = await apiClient.get('/search', {
        params: { q: query }
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, `Failed to search for "${query}"`)
    }
  }
  
  async performSearch(query) {
    try {
      const response = await apiClient.post('/search', {
        query: query
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, `Failed to start search for "${query}"`)
    }
  }
  
  // Status & Info
  async getStatus() {
    try {
      const response = await apiClient.get('/status')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to fetch status')
    }
  }
  
  async getCoreInfo() {
    try {
      const response = await apiClient.get('/info')
      return response.data
    } catch (error) {
      throw this.handleError(error, 'Failed to fetch core info')
    }
  }
  
  // Utility Methods
  handleError(error, defaultMessage) {
    const apiError = error.response?.data?.error
    
    if (apiError) {
      return new Error(`${defaultMessage}: ${apiError.message}`)
    }
    
    if (error.code === 'ECONNABORTED') {
      return new Error(`${defaultMessage}: Request timeout`)
    }
    
    if (error.code === 'ERR_NETWORK') {
      return new Error(`${defaultMessage}: Network error - API server not reachable`)
    }
    
    return new Error(`${defaultMessage}: ${error.message}`)
  }
  
  // Format file size for display
  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes'
    
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }
  
  // Format transfer speed for display
  formatSpeed(bytesPerSecond) {
    return this.formatFileSize(bytesPerSecond) + '/s'
  }
  
  // Format time duration
  formatDuration(seconds) {
    if (seconds < 60) {
      return `${seconds}s`
    } else if (seconds < 3600) {
      const minutes = Math.floor(seconds / 60)
      const remainingSeconds = seconds % 60
      return `${minutes}m ${remainingSeconds}s`
    } else {
      const hours = Math.floor(seconds / 3600)
      const minutes = Math.floor((seconds % 3600) / 60)
      return `${hours}h ${minutes}m`
    }
  }
  
  // Get server status color
  getServerStatusColor(status) {
    const colors = {
      'connected': 'success',
      'available': 'primary',
      'busy': 'warning',
      'offline': 'danger'
    }
    return colors[status] || 'secondary'
  }
  
  // Get server status icon
  getServerStatusIcon(status) {
    const icons = {
      'connected': 'bi-check-circle-fill',
      'available': 'bi-circle-fill',
      'busy': 'bi-exclamation-circle-fill',
      'offline': 'bi-x-circle-fill'
    }
    return icons[status] || 'bi-question-circle-fill'
  }
  
  // Test API connection
  async testConnection() {
    try {
      const response = await apiClient.get('/status')
      return {
        success: true,
        message: 'API connection successful',
        data: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: this.handleError(error, 'API connection failed').message,
        error: error
      }
    }
  }
}

// Create and export singleton instance
const apiService = new ApiService()
export default apiService

// Export individual methods for convenience
export const {
  getServers,
  getServerInfo,
  connectToServer,
  disconnectFromServer,
  getDownloads,
  startDownload,
  getUploads,
  getSearchResults,
  performSearch,
  getStatus,
  getCoreInfo,
  formatFileSize,
  formatSpeed,
  formatDuration,
  getServerStatusColor,
  getServerStatusIcon,
  testConnection
} = apiService