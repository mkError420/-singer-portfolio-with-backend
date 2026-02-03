import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

const MusicDebug = () => {
  const [albums, setAlbums] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [debugInfo, setDebugInfo] = useState({});

  console.log('🎵 MusicDebug component rendered');
  console.log('📊 Current state:', { albums: albums.length, loading, error });

  useEffect(() => {
    console.log('🔄 useEffect triggered');
    
    const loadData = async () => {
      console.log('🚀 Starting data load...');
      
      try {
        setLoading(true);
        setError(null);
        setDebugInfo({});

        // Test 1: Direct fetch test with proxy
        console.log('📡 Testing fetch with proxy...');
        
        // Use direct URL (no proxy path rewriting)
        const directUrl = '/madam-portfolio/backend/api/albums.php';
        console.log('📡 Using direct URL:', directUrl);
        
        const response = await fetch(directUrl);
        console.log('📡 Response status:', response.status);
        console.log('📡 Used URL:', directUrl);
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const albumsData = await response.json();
        console.log('📊 Albums loaded directly:', albumsData);
        console.log('📊 Albums count:', albumsData.length);

        setDebugInfo({
          directFetchSuccess: true,
          albumsCount: albumsData.length,
          firstAlbum: albumsData[0]?.title || 'None',
          apiUrl: directUrl
        });

        setAlbums(albumsData);
        setError(null);
        
        console.log('✅ Data loaded successfully');
        
      } catch (err) {
        console.error('❌ Error loading music data:', err);
        setError(err.message);
        setDebugInfo({
          error: err.message,
          stack: err.stack
        });
      } finally {
        setLoading(false);
        console.log('🏁 Data load completed');
      }
    };

    loadData();
  }, []);

  console.log('🎯 About to render, albums:', albums.length);

  if (loading) {
    return (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h2>Loading Music Data...</h2>
        <div style={{ marginTop: '20px' }}>
          <div>🔄 Debug Info:</div>
          <pre style={{ background: '#f5f5f5', padding: '10px', textAlign: 'left' }}>
            {JSON.stringify(debugInfo, null, 2)}
          </pre>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div style={{ padding: '20px', textAlign: 'center' }}>
        <h2 style={{ color: 'red' }}>Error Loading Music Data</h2>
        <p style={{ color: 'red' }}>{error}</p>
        <div style={{ marginTop: '20px' }}>
          <div>🔍 Debug Info:</div>
          <pre style={{ background: '#f5f5f5', padding: '10px', textAlign: 'left' }}>
            {JSON.stringify(debugInfo, null, 2)}
          </pre>
        </div>
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
      <h1>🎵 Music Debug Page</h1>
      
      <div style={{ background: '#e8f5e8', padding: '15px', borderRadius: '8px', marginBottom: '20px' }}>
        <h3>✅ Success! Albums Loaded</h3>
        <p><strong>Albums Count:</strong> {albums.length}</p>
        <p><strong>Loading State:</strong> {loading ? 'Loading' : 'Loaded'}</p>
        <p><strong>Error:</strong> {error || 'None'}</p>
      </div>

      <div style={{ background: '#f0f8ff', padding: '15px', borderRadius: '8px', marginBottom: '20px' }}>
        <h3>🔍 Debug Information</h3>
        <pre style={{ background: '#f5f5f5', padding: '10px', fontSize: '12px' }}>
          {JSON.stringify(debugInfo, null, 2)}
        </pre>
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
                  src={`http://localhost:80443/madam-portfolio/backend/${album.cover_image}`}
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
                  <p style={{ margin: '2px 0', color: '#666' }}><strong>ID:</strong> {album.id}</p>
                </div>
              </div>
              {album.description && (
                <p style={{ margin: '10px 0', color: '#555', fontSize: '14px' }}>
                  <strong>Description:</strong> {album.description}
                </p>
              )}
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
          onClick={() => window.location.reload()}
          style={{ padding: '10px 20px' }}
        >
          Refresh Debug Page
        </button>
      </div>
    </div>
  );
};

export default MusicDebug;
