const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function(app) {
  console.log('🔧 Setting up direct proxy (no path rewrite)...');
  
  app.use(
    '/madam-portfolio/backend/api',
    createProxyMiddleware({
      target: 'http://localhost:80',
      changeOrigin: true,
      secure: false,
      logLevel: 'debug',
      onProxyReq: (proxyReq, req, res) => {
        console.log('📡 [DIRECT PROXY] Original URL:', req.url);
        console.log('📡 [DIRECT PROXY] Full Target:', proxyReq.protocol + '//' + proxyReq.host + proxyReq.path);
      },
      onProxyRes: (proxyRes, req, res) => {
        console.log('📡 [DIRECT PROXY] Response:', proxyRes.statusCode, 'for', req.url);
      },
      onError: (err, req, res) => {
        console.error('❌ [DIRECT PROXY] Error:', err.message);
        if (!res.headersSent) {
          res.status(500).send('Proxy error: ' + err.message);
        }
      }
    })
  );
  
  console.log('✅ Direct proxy configured for /madam-portfolio/backend/api -> http://localhost:80');
};
