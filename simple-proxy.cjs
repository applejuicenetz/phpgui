const net = require('net');
const http = require('http');
const url = require('url');

const PORT = 3001;

// Allowed origins for CORS - can be configured via environment variables
const ALLOWED_ORIGINS = process.env.ALLOWED_ORIGINS 
  ? process.env.ALLOWED_ORIGINS.split(',')
  : [
      'http://localhost:3000',
      'http://localhost:8080',
      'http://127.0.0.1:3000',
      'http://127.0.0.1:8080'
    ];

// Function to check if origin is allowed
function isOriginAllowed(origin) {
  if (!origin) return false;
  
  // Allow localhost and 127.0.0.1 with any port for development
  if (origin.match(/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/)) {
    return true;
  }
  
  // Allow specific configured origins
  return ALLOWED_ORIGINS.includes(origin);
}

const server = http.createServer((req, res) => {
  // Enable CORS
  const requestOrigin = req.headers.origin;
  const allowedOrigin = isOriginAllowed(requestOrigin) ? requestOrigin : 'http://localhost:3000';
  
  res.setHeader('Access-Control-Allow-Origin', allowedOrigin);
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, X-Target-Host, X-Target-Port, Cookie, Set-Cookie');
  
  // Handle preflight requests
  if (req.method === 'OPTIONS') {
    res.writeHead(200);
    res.end();
    return;
  }
  
  // Simple session handling
  let sessionId = null;
  if (req.headers.cookie) {
    const cookies = req.headers.cookie.split(';').reduce((acc, cookie) => {
      const [key, value] = cookie.trim().split('=');
      acc[key] = value;
      return acc;
    }, {});
    sessionId = cookies.session_id;
  }
  
  if (!sessionId) {
    sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    res.setHeader('Set-Cookie', `session_id=${sessionId}; HttpOnly; SameSite=Lax; Path=/`);
  }
  
  if (!sessions.has(sessionId)) {
    sessions.set(sessionId, {});
  }
  
  // Parse request URL
  const parsedUrl = url.parse(req.url, true);
  console.log('Raw proxy request:', req.method, req.url);
  
  // Handle special session endpoints
  if (parsedUrl.pathname === '/session/status') {
    const session = sessions.get(sessionId);
    res.writeHead(200, {
      'Access-Control-Allow-Origin': allowedOrigin,
      'Access-Control-Allow-Credentials': 'true',
      'Content-Type': 'application/json'
    });
    res.end(JSON.stringify({
      authenticated: !!(session.core_host && session.core_pass),
      core_host: session.core_host || null
    }));
    return;
  }
  
  // Extract target from headers
  const targetHost = req.headers['x-target-host'] || 'localhost';
  const targetPort = parseInt(req.headers['x-target-port']) || 9851;
  const targetPath = parsedUrl.pathname;
  const targetQuery = parsedUrl.search || '';
  
  const targetUrl = `http://${targetHost}:${targetPort}${targetPath}${targetQuery}`;
  console.log('Raw proxying to:', targetUrl);
  
  // Check if this is a settings.xml request and handle session accordingly
  const session = sessions.get(sessionId);
  const isSettingsRequest = targetPath.includes('settings.xml');
  
  if (isSettingsRequest && targetQuery.includes('password=')) {
    // This is a login attempt - we'll validate and store in session if successful
    console.log('Login attempt detected for session:', sessionId);
  } else if (!isSettingsRequest && !targetQuery.includes('password=')) {
    // For non-login requests, check if we have stored credentials in session
    if (session.core_host && session.core_pass) {
      // Add password from session to query
      const separator = targetQuery ? '&' : '?';
      targetQuery = targetQuery + separator + 'password=' + session.core_pass;
      console.log('Using session credentials for request:', targetUrl.replace(/password=[^&]+/, 'password=***'));
    } else {
      console.log('No session credentials available for request:', targetUrl);
    }
  }
  
  // Create raw HTTP request (using updated targetQuery)
  const httpRequest = `GET ${targetPath}${targetQuery} HTTP/1.1\r\nHost: ${targetHost}:${targetPort}\r\nUser-Agent: AppleJuice-WebUI/1.0\r\nAccept: text/xml, application/xml, */*\r\nConnection: close\r\n\r\n`;
  
  console.log('Sending raw HTTP request:', httpRequest.replace(/\r\n/g, '\\r\\n'));
  
  // Create TCP connection
  const socket = new net.Socket();
  let responseData = '';
  
  socket.connect(targetPort, targetHost, () => {
    console.log('TCP connection established');
    socket.write(httpRequest);
  });
  
  socket.on('data', (data) => {
    responseData += data.toString();
  });
  
  socket.on('close', () => {
    console.log('TCP connection closed');
    console.log('Raw response length:', responseData.length);
    console.log('Raw response preview:', responseData.substring(0, 300).replace(/\r\n/g, '\\r\\n'));
    
    try {
      // Parse HTTP response - handle both \r\n and \n line endings
      let headerEndIndex = responseData.indexOf('\r\n\r\n');
      let headerSeparator = '\r\n\r\n';
      
      if (headerEndIndex === -1) {
        headerEndIndex = responseData.indexOf('\n\n');
        headerSeparator = '\n\n';
      }
      
      if (headerEndIndex === -1) {
        throw new Error('Invalid HTTP response format');
      }
      
      const headers = responseData.substring(0, headerEndIndex);
      const body = responseData.substring(headerEndIndex + headerSeparator.length);
      
      console.log('Response headers:', headers.replace(/\r\n/g, '\\r\\n'));
      console.log('Response body length:', body.length);
      console.log('Response body preview:', body.substring(0, 200));
      
      // Extract status code
      const statusMatch = headers.match(/HTTP\/1\.[01] (\d+)/);
      const statusCode = statusMatch ? parseInt(statusMatch[1]) : 200;
      
      // Handle login success for settings.xml requests
      if (isSettingsRequest && statusCode === 200 && !body.includes('wrong password.')) {
        // Extract password from query string
        const passwordMatch = targetQuery.match(/password=([^&]+)/);
        if (passwordMatch) {
          // Store successful login in session
          session.core_host = `http://${targetHost}:${targetPort}`;
          session.core_pass = passwordMatch[1];
          console.log('Login successful! Stored in session:', sessionId);
        }
      }
      
      // Send response with CORS headers
      res.writeHead(statusCode, {
        'Access-Control-Allow-Origin': allowedOrigin,
        'Access-Control-Allow-Credentials': 'true',
        'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers': 'Origin, X-Requested-With, Content-Type, Accept',
        'Content-Type': 'text/xml; charset=utf-8',
        'Content-Length': Buffer.byteLength(body)
      });
      
      res.end(body);
    } catch (parseError) {
      console.error('Error parsing response:', parseError.message);
      res.writeHead(500, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ 
        error: 'Response parse error', 
        message: parseError.message,
        rawResponse: responseData.substring(0, 500)
      }));
    }
  });
  
  socket.on('error', (err) => {
    console.error('TCP socket error:', err.message);
    res.writeHead(500, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ 
      error: 'Connection error', 
      message: err.message,
      target: targetUrl
    }));
  });
  
  socket.setTimeout(10000, () => {
    console.error('TCP socket timeout');
    socket.destroy();
    res.writeHead(504, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: 'Timeout' }));
  });
});

server.listen(PORT, () => {
  console.log(`Raw TCP proxy server running on http://localhost:${PORT}`);
  console.log('Usage: Send requests with headers:');
  console.log('  X-Target-Host: 192.168.178.222');
  console.log('  X-Target-Port: 9854');
});