const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function(app) {
  app.use(
    '/api',
    createProxyMiddleware({
      target: 'http://localhost:80',
      changeOrigin: true,
      secure: false,
      logLevel: 'debug',
      pathRewrite: {
        '^/api': '/madam-portfolio/backend/api'
      },
      onProxyReq: (proxyReq, req, res) => {
        console.log('📡 [PROXY] Original URL:', req.url);
        console.log('📡 [PROXY] Rewritten URL:', proxyReq.path);
        console.log('📡 [PROXY] Full Target:', proxyReq.protocol + '//' + proxyReq.host + proxyReq.path);
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
  
  console.log('✅ Proxy configured for /api -> http://localhost:80/madam-portfolio/backend/api');
};
