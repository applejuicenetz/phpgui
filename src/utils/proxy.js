/**
 * Simple proxy utility to bypass CORS using a browser extension approach
 * This will work in development but requires a proper backend in production
 */

/**
 * Make a request through a simple proxy approach
 * @param {string} url - The target URL
 * @returns {Promise<string>} - The response text
 */
export async function makeProxyRequest(url) {
  try {
    // Parse the target URL
    const urlObj = new URL(url)
    const targetHost = urlObj.hostname
    const targetPort = urlObj.port || '9854'
    const targetPath = urlObj.pathname + urlObj.search
    
    console.log('Making proxy request to:', url.replace(/password=[^&]+/, 'password=***'))
    console.log(`Target: ${targetHost}:${targetPort}`)
    
    // First check if proxy server is running
    try {
      const healthCheck = await fetch('http://localhost:3001/', {
        method: 'GET',
        headers: {
          'X-Target-Host': targetHost,
          'X-Target-Port': targetPort
        }
      })
      console.log('Proxy server is running')
    } catch (proxyError) {
      throw new Error('Proxy server ist nicht erreichbar. Bitte starten Sie den Proxy-Server mit: node simple-proxy.cjs')
    }
    
    // Make request through our proxy server
    const proxyUrl = `http://localhost:3001${targetPath}`
    
    const response = await fetch(proxyUrl, {
      method: 'GET',
      headers: {
        'X-Target-Host': targetHost,
        'X-Target-Port': targetPort,
        'Accept': 'text/xml, application/xml, text/plain, */*'
      }
    })
    
    if (response.ok) {
      const responseText = await response.text()
      console.log('Proxy request successful, response length:', responseText.length)
      
      if (responseText.trim() === '') {
        throw new Error('Leere Antwort vom Core erhalten. Prüfen Sie ob der Core läuft.')
      }
      
      return responseText
    } else {
      const errorText = await response.text()
      console.error('Proxy request failed:', response.status, errorText)
      
      if (response.status === 500) {
        throw new Error(`Core nicht erreichbar: ${errorText}`)
      } else if (response.status === 504) {
        throw new Error('Timeout beim Verbinden zum Core. Prüfen Sie die Server-Adresse.')
      } else {
        throw new Error(`HTTP ${response.status}: ${errorText}`)
      }
    }
  } catch (error) {
    console.error('Proxy request error:', error.message)
    
    // Don't try direct request for AppleJuice Core (it doesn't support CORS)
    throw new Error(error.message)
  }
}

/**
 * Alternative approach using JSONP-like technique (if the server supports it)
 * @param {string} url - The target URL
 * @returns {Promise<string>} - The response text
 */
export function makeJSONPRequest(url) {
  return new Promise((resolve, reject) => {
    // Create a script tag to bypass CORS
    const script = document.createElement('script')
    const callbackName = 'jsonp_callback_' + Date.now()
    
    // Set up callback
    window[callbackName] = function(data) {
      document.head.removeChild(script)
      delete window[callbackName]
      resolve(data)
    }
    
    // Add callback parameter to URL
    const separator = url.includes('?') ? '&' : '?'
    script.src = `${url}${separator}callback=${callbackName}`
    
    script.onerror = function() {
      document.head.removeChild(script)
      delete window[callbackName]
      reject(new Error('JSONP request failed'))
    }
    
    // Add script to head
    document.head.appendChild(script)
    
    // Timeout after 10 seconds
    setTimeout(() => {
      if (window[callbackName]) {
        document.head.removeChild(script)
        delete window[callbackName]
        reject(new Error('JSONP request timeout'))
      }
    }, 10000)
  })
}