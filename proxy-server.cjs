const express = require('express');
const cors = require('cors');
const http = require('http');
const url = require('url');

const app = express();
const PORT = 3001;

// Enable CORS for all routes
app.use(cors());

// Custom proxy handler for P2P Core API
app.use('/proxy', (req, res) => {
  // Parse URL: /proxy/host/port/path
  const urlParts = req.path.split('/').filter(part => part);
  console.log('URL parts:', urlParts);
  console.log('Full URL:', req.url);
  
  if (urlParts.length < 3) {
    return res.status(400).json({ 
      error: 'Invalid proxy URL format. Use: /proxy/host/port/path',
      received: req.path,
      parts: urlParts
    });
  }
  
  // urlParts[0] = "proxy", urlParts[1] = host, urlParts[2] = port, urlParts[3+] = path
  const host = urlParts[1];
  const port = urlParts[2];
  const path = urlParts.slice(3).join('/');
  const queryString = req.url.split('?').slice(1).join('?');
  
  const targetUrl = `http://${host}:${port}/${path}${queryString ? '?' + queryString : ''}`;
  
  console.log(`Proxying request to: ${targetUrl}`);
  
  // Make raw HTTP request to handle non-standard responses
  const options = {
    hostname: host,
    port: parseInt(port),
    path: `/${path}${queryString ? '?' + queryString : ''}`,
    method: 'GET',
    timeout: 10000
  };
  
  const proxyReq = http.request(options, (proxyRes) => {
    console.log(`Response status: ${proxyRes.statusCode}`);
    
    // Set CORS headers
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    
    // Forward status code
    res.status(proxyRes.statusCode || 200);
    
    // Collect response data
    let data = '';
    proxyRes.on('data', (chunk) => {
      data += chunk;
    });
    
    proxyRes.on('end', () => {
      console.log(`Response received: ${data.length} bytes`);
      res.send(data);
    });
  });
  
  proxyReq.on('error', (err) => {
    console.error('Proxy request error:', err.message);
    res.status(500).json({ 
      error: 'Connection failed', 
      message: err.message,
      target: targetUrl
    });
  });
  
  proxyReq.on('timeout', () => {
    console.error('Proxy request timeout');
    proxyReq.destroy();
    res.status(504).json({ 
      error: 'Request timeout',
      target: targetUrl
    });
  });
  
  proxyReq.end();
});

app.listen(PORT, () => {
  console.log(`P2P Proxy server running on http://localhost:${PORT}`);
  console.log(`Usage: http://localhost:${PORT}/proxy/[host]/[port]/[path]`);
  console.log(`Example: http://localhost:${PORT}/proxy/192.168.178.222/9854/xml/settings.xml?password=hash`);
});