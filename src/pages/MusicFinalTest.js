import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

const MusicFinalTest = () => {
  const [albums, setAlbums] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  console.log('🎵 MusicFinalTest component rendered');

  useEffect(() => {
    console.log('🔄 useEffect triggered');
    
    const loadData = async () => {
      console.log('🚀 Starting final test...');
      
      try {
        setLoading(true);
        setError(null);

        // Use the exact same approach that works in the browser test
        const fullUrl = 'http://localhost/madam-portfolio/backend/api/albums_fixed.php';
        console.log('📡 Using exact working URL:', fullUrl);
        
        // Use fetch exactly like the working browser test
        const response = await fetch(fullUrl, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', [...response.headers.entries()]);
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const albumsData = await response.json();
        console.log('📊 Albums loaded:', albumsData);
        console.log('📊 Albums count:', albumsData.length);

        setAlbums(albumsData);
        setError(null);
        
        console.log('✅ Final test successful!');
        
      } catch (err) {
        console.error('❌ Final test error:', err);
        setError(err.message);
      } finally {
        setLoading(false);
        console.log('🏁 Final test completed');
      }
    };

    loadData();
  }, []);

  if (loading) {
    return (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h2>🎵 Final Test - Loading...</h2>
        <p>Testing the exact same approach that works in the browser...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h2 style={{ color: 'red' }}>❌ Final Test Failed</h2>
        <p style={{ color: 'red' }}>{error}</p>
        <p><strong>But the same URL works in browser!</strong></p>
        <p>This suggests a React-specific issue.</p>
        <button 
          onClick={() => window.location.reload()} 
          style={{ marginTop: '20px', padding: '10px 20px' }}
        >
          Retry
        </button>
      </div>
    );
  }

  return (
    <div style={{ padding: '20px' }}>
      <h1>🎵 Music Final Test</h1>
      
      <div style={{ background: '#e8f5e8', padding: '15px', borderRadius: '8px', marginBottom: '20px' }}>
        <h3>✅ Success! Final Test Worked</h3>
        <p><strong>Albums Count:</strong> {albums.length}</p>
        <p><strong>Method:</strong> Full URL (same as browser test)</p>
        <p><strong>Status:</strong> React can access backend!</p>
      </div>

      <div style={{ background: '#fff', padding: '20px', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' }}>
        <h2>📀 Albums ({albums.length})</h2>
        
        {albums.length === 0 ? (
          <p style={{ color: '#666', fontStyle: 'italic' }}>No albums found</p>
        ) : (
          albums.map((album, index) => (
            <motion.div
              key={album.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              style={{
                border: '1px solid #ddd',
                borderRadius: '8px',
                padding: '15px',
                margin: '10px 0',
                background: '#fafafa'
              }}
            >
              <div style={{ display: 'flex', gap: '15px', marginBottom: '10px' }}>
                <img
                  src={`http://localhost/madam-portfolio/backend/${album.cover_image}`}
                  alt={album.title}
                  style={{
                    width: '80px',
                    height: '80px',
                    borderRadius: '8px',
                    objectFit: 'cover'
                  }}
                  onError={(e) => {
                    e.target.src = `https://via.placeholder.com/80x80/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}`;
                  }}
                />
                <div>
                  <h3 style={{ margin: '0 0 5px 0', color: '#333' }}>{album.title}</h3>
                  <p style={{ margin: '2px 0', color: '#666' }}><strong>Year:</strong> {album.year}</p>
                  <p style={{ margin: '2px 0', color: '#666' }}><strong>Category:</strong> {album.category || 'No Category'}</p>
                  <p style={{ margin: '2px 0', color: '#666' }}><strong>Tracks:</strong> {album.track_count || 0}</p>
                </div>
              </div>
            </motion.div>
          ))
        )}
      </div>

      <div style={{ marginTop: '30px', textAlign: 'center' }}>
        <button 
          onClick={() => window.location.href = '/music'}
          style={{ padding: '10px 20px', marginRight: '10px' }}
        >
          Go to Original Music Page
        </button>
        <button 
          onClick={() => window.location.href = '/music-debug-full'}
          style={{ padding: '10px 20px', marginRight: '10px' }}
        >
          Go to Full URL Debug
        </button>
        <button 
          onClick={() => window.location.reload()}
          style={{ padding: '10px 20px' }}
        >
          Refresh Final Test
        </button>
      </div>
    </div>
  );
};

export default MusicFinalTest;
