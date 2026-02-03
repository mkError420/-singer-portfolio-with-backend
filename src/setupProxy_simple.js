const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function(app) {
  console.log('🔧 Setting up proxy for backend API...');
  
  app.use(
    '/api',
    createProxyMiddleware({
      target: 'http://localhost:80443',
      changeOrigin: true,
      secure: false,
      ws: true,
      logLevel: 'debug',
      onProxyReq: (proxyReq, req, res) => {
        console.log('📡 [PROXY] Forwarding:', req.method, req.url, 'to', proxyReq.protocol + '//' + proxyReq.host + proxyReq.path);
      },
      onProxyRes: (proxyRes, req, res) => {
        console.log('📡 [PROXY] Response:', proxyRes.statusCode, 'for', req.url);
      },
      onError: (err, req, res) => {
        console.error('❌ [PROXY] Error:', err.message);
        console.error('❌ [PROXY] Full error:', err);
        if (!res.headersSent) {
          res.status(500).send('Proxy error: ' + err.message);
        }
      }
    })
  );
  
  console.log('✅ Proxy configured for /api -> http://localhost:80443');
};
