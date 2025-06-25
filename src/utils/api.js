/**
 * API utility functions for P2P Core communication
 * Includes both direct core communication and PHP API backend
 * Includes both direct core communication and PHP API backend
 */

// PHP API Base URL - adjust this to match your PHP API location
let PHP_API_BASE_URL = 'http://localhost:8080/api'


/**
 * Make an authenticated request to the P2P Core
 * @param {string} endpoint - The endpoint (e.g., 'settings.xml', 'downloads.xml')
 * @param {string} type - The type (usually 'xml')
 * @param {Object} additionalParams - Additional URL parameters
 * @returns {Promise<string>} - The response text
 */
export async function makeCoreRequest(endpoint, type = 'xml', additionalParams = {}) {
  const coreHost = localStorage.getItem('p2p_core_host')
  const corePass = localStorage.getItem('p2p_core_pass')
  
  if (!coreHost || !corePass) {
    throw new Error('Nicht authentifiziert')
  }
  
  // Build URL parameters
  const params = new URLSearchParams()
  params.append('password', corePass)
  
  // Add additional parameters
  Object.entries(additionalParams).forEach(([key, value]) => {
    params.append(key, value)
  })
  
  // Use proxy to avoid CORS issues
  const proxyUrl = `/api/${type}/${endpoint}?&${params.toString()}`
  const serverAddress = localStorage.getItem('p2p_server_address')
  
  console.log(`API Request via proxy to: ${coreHost}/${type}/${endpoint}?&password=***`) // Log without password
  
  const response = await fetch(proxyUrl, {
    headers: {
      'X-Target-Host': serverAddress
    }
  })
  
  if (!response.ok) {
    throw new Error(`HTTP ${response.status}: ${response.statusText}`)
  }
  
  const responseText = await response.text()
  
  if (!responseText || responseText.trim() === '') {
    throw new Error('Leere Antwort vom Core')
  }
  
  if (responseText.includes('wrong password.')) {
    // Clear authentication and redirect to login
    localStorage.removeItem('p2p_authenticated')
    localStorage.removeItem('p2p_core_host')
    localStorage.removeItem('p2p_core_pass')
    throw new Error('Authentifizierung abgelaufen')
  }
  
  return responseText
}

/**
 * Parse XML response to JavaScript object
 * @param {string} xmlString - XML string to parse
 * @returns {Document} - Parsed XML document
 */
export function parseXML(xmlString) {
  const parser = new DOMParser()
  const xmlDoc = parser.parseFromString(xmlString, 'text/xml')
  
  // Check for parsing errors
  const parserError = xmlDoc.querySelector('parsererror')
  if (parserError) {
    throw new Error('XML Parsing Error: ' + parserError.textContent)
  }
  
  return xmlDoc
}

/**
 * Check if user is authenticated (check both localStorage and proxy session)
 * @returns {Promise<boolean>}
 */
export async function isAuthenticated() {
  // First check localStorage
  const localStorage_auth = !!(
    localStorage.getItem('p2p_authenticated') &&
    localStorage.getItem('p2p_core_host') &&
    localStorage.getItem('p2p_core_pass')
  );
  
  if (localStorage_auth) {
    return true;
  }
  
  // Check proxy session
  try {
    const response = await fetch('http://localhost:3001/session/status', {
      credentials: 'include'
    });
    const data = await response.json();
    return data.authenticated || false;
  } catch (error) {
    console.warn('Could not check proxy session:', error);
    return false;
  }
}

/**
 * Check if user is authenticated (synchronous version for backwards compatibility)
 * @returns {boolean}
 */
export function isAuthenticatedSync() {
  return !!(
    localStorage.getItem('p2p_authenticated') &&
    localStorage.getItem('p2p_core_host') &&
    localStorage.getItem('p2p_core_pass')
  )
}

/**
 * Get core connection info
 * @returns {Object} - Core connection details
 */
export function getCoreInfo() {
  return {
    serverAddress: localStorage.getItem('p2p_server_address'),
    coreHost: localStorage.getItem('p2p_core_host'),
    isAuthenticated: isAuthenticated()
  }
}

/**
 * Make request to PHP API backend
 * @param {string} endpoint - API endpoint
 * @param {Object} options - Fetch options
 * @returns {Promise<Object>} - API response
 */
async function makePhpApiRequest(endpoint, options = {}) {
  const url = `${PHP_API_BASE_URL}/${endpoint.replace(/^\//, '')}`
  
  const defaultOptions = {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'include', // Include cookies for session handling
  }
  
  const requestOptions = { ...defaultOptions, ...options }
  
  try {
    console.log(`PHP API Request: ${requestOptions.method} ${url}`)
    
    const response = await fetch(url, requestOptions)
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || data.error || `HTTP ${response.status}`)
    }
    
    return {
      success: true,
      data: data.data || data,
      status: response.status
    }
    
  } catch (error) {
    console.error('PHP API Request failed:', error)
    return {
      success: false,
      error: error.message,
      status: error.status || 500
    }
  }
}

/**
 * PHP API Authentication functions
 */
export const phpApi = {
  /**
   * Login to AppleJuice Core via PHP API
   * @param {string} host - Core host address
   * @param {string} password - Core password (will be hashed)
   * @returns {Promise<Object>} - Login response
   */
  async login(host, password) {
    // Hash password with MD5 if not already hashed
    const hashedPassword = password.length === 32 ? password : await hashPassword(password)
    
    return await makePhpApiRequest('login', {
      method: 'POST',
      body: JSON.stringify({
        host: host,
        password: hashedPassword
      })
    })
  },
  
  /**
   * Logout from AppleJuice Core
   */
  async logout() {
    return await makePhpApiRequest('logout', {
      method: 'POST'
    })
  },
  
  /**
   * Check authentication status
   */
  async checkAuthStatus() {
    return await makePhpApiRequest('auth')
  },
  
  /**
   * Get core status
   */
  async getStatus() {
    return await makePhpApiRequest('status')
  },
  
  /**
   * Get servers list
   */
  async getServers() {
    return await makePhpApiRequest('servers')
  },
  
  /**
   * Get downloads
   */
  async getDownloads() {
    return await makePhpApiRequest('downloads')
  },
  
  /**
   * Get uploads
   */
  async getUploads() {
    return await makePhpApiRequest('uploads')
  }
}

/**
 * Hash password using MD5 (for compatibility with AppleJuice Core)
 * @param {string} password - Plain text password
 * @returns {Promise<string>} - MD5 hashed password
 */
async function hashPassword(password) {
  // Import CryptoJS dynamically if not available
  if (typeof CryptoJS === 'undefined') {
    const CryptoJS = await import('crypto-js')
    return CryptoJS.default.MD5(password).toString()
  } else {
    return CryptoJS.MD5(password).toString()
  }
}

/**
 * Set PHP API base URL (useful for different environments)
 * @param {string} url - New base URL
 */
export function setPhpApiBaseUrl(url) {
  PHP_API_BASE_URL = url.replace(/\/$/, '') // Remove trailing slash
}

/**
 * Get current PHP API base URL
 * @returns {string} - Current base URL
 */
export function getPhpApiBaseUrl() {
  return PHP_API_BASE_URL
}
