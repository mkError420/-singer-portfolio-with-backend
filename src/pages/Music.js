import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { musicAPI } from '../services/api';
import ButtonStyles from '../styles/ButtonStyles';

const Music = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [albums, setAlbums] = useState([]);
  const [singles, setSingles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [showVideoModal, setShowVideoModal] = useState(false);
  const [currentVideoUrl, setCurrentVideoUrl] = useState('');

  // Categories for filtering
  const categories = [
    { id: 'all', label: 'All', icon: '🎵' },
    { id: 'albums', label: 'Albums', icon: '💿' },
    { id: 'singles', label: 'Singles', icon: '🎤' },
  ];

  // Load data from API
  useEffect(() => {
    const loadData = async () => {
      try {
        setLoading(true);
        console.log('🔄 Loading albums...');
        const albumsData = await musicAPI.getAlbums();
        console.log('📊 Albums data:', albumsData);
        
        console.log('🔄 Loading singles...');
        const singlesData = await musicAPI.getSingles();
        console.log('📊 Singles data:', singlesData);
        
        // Load tracks for each album
        const albumsWithTracks = await Promise.all(
          albumsData.map(async (album) => {
            try {
              console.log('🔄 Loading tracks for album:', album.title);
              const response = await fetch(`http://localhost/madam-portfolio/backend/api/albums_fixed.php?album_id=${album.id}&include_tracks=1`);
              const albumWithTracks = await response.json();
              
              // Fix the cover image URL
              if (albumWithTracks.cover_image) {
                albumWithTracks.cover_image = `http://localhost/madam-portfolio/backend/${albumWithTracks.cover_image}`;
              }
              
              return albumWithTracks;
            } catch (error) {
              console.error(`Error loading tracks for album ${album.id}:`, error);
              return { ...album, tracks: [] };
            }
          })
        );
        
        setAlbums(albumsWithTracks);
        
        // Fix cover image URLs for singles
        const singlesWithFixedImages = singlesData.map(single => {
          if (single.cover_image) {
            single.cover_image = `http://localhost/madam-portfolio/backend/${single.cover_image}`;
            console.log('🖼️ Single cover image fixed:', single.title, single.cover_image);
          } else {
            console.log('🖼️ Single has no cover image:', single.title);
          }
          return single;
        });
        
        setSingles(singlesWithFixedImages);
        setLoading(false);
      } catch (error) {
        console.error('Error loading music data:', error);
        setError('Failed to load music data');
        setLoading(false);
      }
    };

    loadData();
  }, []);

  // Open video modal
  const openVideoModal = (youtubeUrl) => {
    console.log('🎬 Opening video modal');
    console.log('📹 Original YouTube URL:', youtubeUrl);
    console.log('📹 URL type:', typeof youtubeUrl);
    console.log('📹 URL length:', youtubeUrl?.length);
    
    if (!youtubeUrl) {
      console.error('❌ No YouTube URL provided');
      return;
    }
    
    // Convert YouTube URL to embed format
    let embedUrl = youtubeUrl.trim();
    
    // Ensure URL starts with https
    if (embedUrl.startsWith('http://')) {
      embedUrl = embedUrl.replace('http://', 'https://');
      console.log('🔄 Converted to HTTPS:', embedUrl);
    } else if (!embedUrl.startsWith('https://')) {
      embedUrl = 'https://' + embedUrl;
      console.log('🔄 Added HTTPS prefix:', embedUrl);
    }
    
    // Handle different YouTube URL formats
    if (embedUrl.includes('youtube.com/watch?v=')) {
      const videoId = embedUrl.split('v=')[1]?.split('&')[0];
      if (videoId) {
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
        console.log('🔄 Converted watch URL to embed:', embedUrl);
      }
    } else if (embedUrl.includes('youtu.be/')) {
      const videoId = embedUrl.split('youtu.be/')[1]?.split('?')[0];
      if (videoId) {
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
        console.log('🔄 Converted youtu.be URL to embed:', embedUrl);
      }
    } else if (embedUrl.includes('youtube.com/embed/')) {
      console.log('📹 URL is already in embed format:', embedUrl);
    } else {
      console.error('❌ Unsupported YouTube URL format:', embedUrl);
      return;
    }
    
    // Add autoplay parameter
    if (embedUrl.includes('?')) {
      embedUrl += '&autoplay=1&mute=0&rel=0';
    } else {
      embedUrl += '?autoplay=1&mute=0&rel=0';
    }
    
    console.log('📹 Final embed URL with autoplay:', embedUrl);
    setCurrentVideoUrl(embedUrl);
    setShowVideoModal(true);
  };

  // Close video modal
  const closeVideoModal = () => {
    setShowVideoModal(false);
    setCurrentVideoUrl('');
  };

  // Filter albums and singles based on search and category
  const filteredAlbums = albums.filter(album => {
    const matchesSearch = album.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         album.artist.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || selectedCategory === 'albums';
    return matchesSearch && matchesCategory;
  });

  const filteredSingles = singles.filter(single => {
    const matchesSearch = single.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         single.artist.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || selectedCategory === 'singles';
    return matchesSearch && matchesCategory;
  });

  if (loading) {
    return (
      <div style={{ 
        display: 'flex', 
        justifyContent: 'center', 
        alignItems: 'center', 
        minHeight: '100vh',
        background: 'var(--primary-color)',
        color: 'var(--text-primary)'
      }}>
        <div style={{ textAlign: 'center' }}>
          <div style={{ fontSize: '2rem', marginBottom: '1rem' }}>🎵</div>
          <h2>Loading Music...</h2>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div style={{ 
        display: 'flex', 
        justifyContent: 'center', 
        alignItems: 'center', 
        minHeight: '100vh',
        background: 'var(--primary-color)',
        color: 'var(--text-primary)'
      }}>
        <div style={{ textAlign: 'center' }}>
          <div style={{ fontSize: '2rem', marginBottom: '1rem' }}>❌</div>
          <h2>Error Loading Music</h2>
          <p>{error}</p>
        </div>
      </div>
    );
  }

  return (
    <>
      <style jsx>{`
        ${ButtonStyles}
      `}</style>
      <div className="music">
        {/* Hero Section */}
        <section className="music-hero" style={{
          padding: '8rem 0 4rem',
          background: 'linear-gradient(135deg, rgba(26, 26, 26, 0.85) 0%, rgba(42, 42, 42, 0.9) 100%), url("https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=1920&h=800&fit=crop&crop=entropy&auto=format") center/cover',
          textAlign: 'center',
          position: 'relative',
        }}>
          <div className="container">
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
            >
              <h1 style={{ 
                fontSize: '3.5rem', 
                fontWeight: 'bold',
                marginBottom: '1rem',
                background: 'linear-gradient(45deg, #ff6b6b, #4ecdc4)',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text'
              }}>
                Music Collection
              </h1>
              <p style={{ 
                fontSize: '1.2rem', 
                marginBottom: '2rem',
                color: 'var(--text-secondary)',
                maxWidth: '600px',
                margin: '0 auto 2rem'
              }}>
                Explore our complete collection of albums and singles
              </p>
            </motion.div>

            {/* Search and Filter */}
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              viewport={{ once: true }}
              style={{ maxWidth: '600px', margin: '0 auto' }}
            >
              <div style={{
                position: 'relative',
              }}>
                <input
                  type="text"
                  placeholder="Search for songs, albums, or artists..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  style={{
                    width: '100%',
                    padding: '1rem 3rem 1rem 1rem',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: '1px solid rgba(255, 255, 255, 0.2)',
                    borderRadius: '50px',
                    color: 'var(--text-primary)',
                    fontSize: '1rem',
                    outline: 'none',
                    backdropFilter: 'blur(10px)',
                    transition: 'all 0.3s ease',
                  }}
                  onFocus={(e) => {
                    e.target.style.background = 'rgba(255, 255, 255, 0.15)';
                    e.target.style.borderColor = 'var(--accent-color)';
                  }}
                  onBlur={(e) => {
                    e.target.style.background = 'rgba(255, 255, 255, 0.1)';
                    e.target.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                  }}
                />
                <div style={{
                  position: 'absolute',
                  right: '1rem',
                  top: '50%',
                  transform: 'translateY(-50%)',
                  color: 'var(--text-secondary)',
                }}>
                  🔍
                </div>
              </div>

              {/* Category Filter */}
              <div style={{
                display: 'flex',
                justifyContent: 'center',
                gap: '1rem',
                marginTop: '2rem',
                flexWrap: 'wrap',
              }}>
                {categories.map((category) => (
                  <motion.button
                    key={category.id}
                    onClick={() => setSelectedCategory(category.id)}
                    className={`btn ${selectedCategory === category.id ? 'btn-primary' : 'btn-secondary'}`}
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: '0.5rem',
                      padding: '0.5rem 1rem',
                      borderRadius: '25px',
                      background: selectedCategory === category.id 
                        ? 'var(--accent-color)' 
                        : 'rgba(255, 255, 255, 0.1)',
                      border: selectedCategory === category.id 
                        ? '1px solid var(--accent-color)' 
                        : '1px solid rgba(255, 255, 255, 0.2)',
                      color: 'var(--text-primary)',
                      fontSize: '0.9rem',
                      cursor: 'pointer',
                      transition: 'all 0.3s ease',
                    }}
                  >
                    <span>{category.icon}</span>
                    <span>{category.label}</span>
                  </motion.button>
                ))}
              </div>
            </motion.div>
          </div>
        </section>

        {/* Albums Section */}
        <section className="albums" style={{
          padding: '5rem 0',
          background: 'var(--primary-color)',
        }}>
          <div className="container">
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
              style={{ textAlign: 'center', marginBottom: '3rem' }}
            >
              <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
                Albums
              </h2>
              <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
                Complete collections of musical stories
              </p>
            </motion.div>

            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
              gap: '2rem',
            }}>
              {filteredAlbums.length > 0 ? (
                filteredAlbums.map((album, index) => (
                  <motion.div
                    key={album.id}
                    initial={{ opacity: 0, y: 50 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.8, delay: index * 0.1 }}
                    viewport={{ once: true }}
                    className="card"
                  >
                    <div style={{ display: 'flex', gap: '1.5rem', marginBottom: '1.5rem' }}>
                      <img
                        src={album.cover_image || ""}
                        alt={album.title}
                        onError={(e) => {
                          e.target.style.display = 'none';
                          const parent = e.target.parentElement;
                          if (parent && !parent.querySelector('.album-fallback')) {
                            const fallback = document.createElement('div');
                            fallback.className = 'album-fallback';
                            fallback.style.cssText = `
                              width: 120px;
                              height: 120px;
                              border-radius: 10px;
                              background: #2a2a2a;
                              display: flex;
                              align-items: center;
                              justify-content: center;
                              color: #ffffff;
                              font-size: 0.8rem;
                              font-weight: bold;
                              text-align: center;
                              padding: 0.5rem;
                            `;
                            fallback.textContent = '🎵';
                            parent.appendChild(fallback);
                          }
                        }}
                        style={{
                          width: '120px',
                          height: '120px',
                          borderRadius: '10px',
                          objectFit: 'cover',
                          display: album.cover_image ? 'block' : 'none'
                        }}
                      />
                      <div>
                        <h3 style={{ color: 'var(--text-primary)', margin: '0 0 0.5rem' }}>
                          {album.title}
                        </h3>
                        <p style={{ color: 'var(--text-secondary)', margin: '0 0 0.5rem' }}>
                          {album.year} • {album.tracks ? album.tracks.length : 0} tracks
                        </p>
                        <button className="btn btn-gradient-text btn-sm">
                          View Tracks
                        </button>
                      </div>
                    </div>

                    {/* Tracks */}
                    {album.tracks && album.tracks.length > 0 && (
                      <div style={{
                        maxHeight: '300px',
                        overflowY: 'auto',
                        padding: '1rem',
                        background: 'rgba(255, 255, 255, 0.05)',
                        borderRadius: '10px',
                      }}>
                        {album.tracks.map((track, trackIndex) => (
                          <div
                            key={track.id}
                            style={{
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'space-between',
                              padding: '0.75rem',
                              borderRadius: '8px',
                              marginBottom: '0.5rem',
                              background: 'rgba(255, 255, 255, 0.02)',
                              transition: 'all 0.2s ease',
                            }}
                            onMouseEnter={(e) => {
                              e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                            }}
                            onMouseLeave={(e) => {
                              e.currentTarget.style.background = 'rgba(255, 255, 255, 0.02)';
                            }}
                          >
                            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                              <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                                {track.track_number || trackIndex + 1}
                              </span>
                              <div>
                                <p style={{ 
                                  color: 'var(--text-primary)', 
                                  margin: 0,
                                  fontSize: '0.95rem'
                                }}>
                                  {track.title}
                                </p>
                                <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                                  {track.duration || 'Unknown duration'}
                                </p>
                              </div>
                            </div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                              {track.youtube_url && (
                                <button
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    openVideoModal(track.youtube_url);
                                  }}
                                  style={{
                                    background: 'none',
                                    border: 'none',
                                    color: '#ff0000',
                                    fontSize: '0.9rem',
                                    cursor: 'pointer',
                                    padding: '0.25rem',
                                    borderRadius: '4px',
                                    transition: 'all 0.2s ease',
                                  }}
                                  onMouseEnter={(e) => {
                                    e.currentTarget.style.background = 'rgba(255, 0, 0, 0.1)';
                                    e.currentTarget.style.transform = 'scale(1.1)';
                                  }}
                                  onMouseLeave={(e) => {
                                    e.currentTarget.style.background = 'transparent';
                                    e.currentTarget.style.transform = 'scale(1)';
                                  }}
                                  title="Watch Video"
                                >
                                  📺
                                </button>
                              )}
                              {!track.youtube_url && (
                                <span style={{ color: 'var(--text-muted)', fontSize: '0.8rem' }}>
                                  🎵
                                </span>
                              )}
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </motion.div>
                ))
              ) : (
                <div style={{ 
                  textAlign: 'center', 
                  color: 'var(--text-muted)', 
                  padding: '3rem',
                  gridColumn: '1 / -1'
                }}>
                  <p style={{ fontSize: '1.1rem', marginBottom: '1rem' }}>
                    No albums found
                  </p>
                  <p style={{ fontSize: '0.9rem' }}>
                    Try adjusting your search or filters
                  </p>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* Singles Section */}
        <section className="singles" style={{
          padding: '5rem 0',
          background: 'var(--secondary-color)',
        }}>
          <div className="container">
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
              style={{ textAlign: 'center', marginBottom: '3rem' }}
            >
              <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
                Singles
              </h2>
              <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
                Individual releases and standalone tracks
              </p>
            </motion.div>

            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
              gap: '2.5rem',
            }}>
              {filteredSingles.length > 0 ? (
                filteredSingles.map((single, index) => (
                  <motion.div
                    key={single.id}
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: index * 0.1 }}
                    viewport={{ once: true }}
                    className="card"
                    style={{
                      background: 'rgba(255, 255, 255, 0.03)',
                      borderRadius: '20px',
                      overflow: 'hidden',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.transform = 'translateY(-5px)';
                      e.currentTarget.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.transform = 'translateY(0)';
                      e.currentTarget.style.boxShadow = 'none';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    }}
                  >
                    {/* Cover Photo Section */}
                    <div style={{
                      position: 'relative',
                      width: '100%',
                      height: '280px',
                      overflow: 'hidden',
                      background: 'linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%)',
                    }}>
                      <img
                        src={single.cover_image || ""}
                        alt={single.title}
                        onLoad={() => {
                          console.log('✅ Single cover image loaded successfully:', single.title);
                        }}
                        onError={(e) => {
                          console.error('❌ Failed to load single cover image:', single.title, single.cover_image);
                          e.target.style.display = 'none';
                          const parent = e.target.parentElement;
                          if (parent && !parent.querySelector('.singles-fallback')) {
                            const fallback = document.createElement('div');
                            fallback.className = 'singles-fallback';
                            fallback.style.cssText = `
                              position: absolute;
                              top: 0;
                              left: 0;
                              width: 100%;
                              height: 100%;
                              display: flex;
                              flex-direction: column;
                              align-items: center;
                              justify-content: center;
                              background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
                              color: #ffffff;
                              font-size: 3rem;
                              font-weight: bold;
                              text-align: center;
                              padding: 2rem;
                            `;
                            fallback.innerHTML = `
                              <div style="font-size: 3rem; margin-bottom: 1rem;">🎵</div>
                              <div style="font-size: 1.2rem; font-weight: 500; opacity: 0.8;">${single.title}</div>
                            `;
                            parent.appendChild(fallback);
                          }
                        }}
                        style={{
                          width: '100%',
                          height: '100%',
                          objectFit: 'cover',
                          display: single.cover_image ? 'block' : 'none'
                        }}
                      />
                      
                      {/* Overlay Gradient */}
                      <div style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: 'linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%)',
                        pointerEvents: 'none',
                      }} />
                      
                      {/* Play Button Overlay */}
                      {single.youtube_url && (
                        <div style={{
                          position: 'absolute',
                          bottom: '1rem',
                          right: '1rem',
                          background: 'rgba(255, 0, 0, 0.9)',
                          color: 'white',
                          width: '50px',
                          height: '50px',
                          borderRadius: '50%',
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'center',
                          fontSize: '1.2rem',
                          cursor: 'pointer',
                          transition: 'all 0.3s ease',
                          border: '2px solid rgba(255, 255, 255, 0.3)',
                        }}
                        onClick={() => openVideoModal(single.youtube_url)}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.transform = 'scale(1.1)';
                          e.currentTarget.style.background = 'rgba(255, 0, 0, 1)';
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.transform = 'scale(1)';
                          e.currentTarget.style.background = 'rgba(255, 0, 0, 0.9)';
                        }}
                        title="Watch Video"
                      >
                        ▶
                      </div>
                      )}
                    </div>

                    {/* Content Section */}
                    <div style={{ padding: '1.5rem' }}>
                      <h3 style={{ 
                        color: 'var(--text-primary)', 
                        margin: '0 0 0.5rem 0',
                        fontSize: '1.3rem',
                        fontWeight: '600',
                        lineHeight: '1.3',
                      }}>
                        {single.title}
                      </h3>
                      
                      <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '1rem',
                        marginBottom: '1rem',
                        color: 'var(--text-secondary)',
                        fontSize: '0.9rem',
                      }}>
                        <span style={{ display: 'flex', alignItems: 'center', gap: '0.3rem' }}>
                          📅 {single.release_date}
                        </span>
                        <span style={{ display: 'flex', alignItems: 'center', gap: '0.3rem' }}>
                          ⏱️ {single.duration}
                        </span>
                      </div>

                      {/* Action Buttons */}
                      <div style={{
                        display: 'flex',
                        gap: '0.75rem',
                        marginTop: '1rem',
                      }}>
                        {single.youtube_url && (
                          <button
                            onClick={() => openVideoModal(single.youtube_url)}
                            className="btn btn-primary"
                            style={{
                              flex: 1,
                              padding: '0.75rem 1rem',
                              borderRadius: '10px',
                              background: 'linear-gradient(45deg, #ff6b6b, #ee5a24)',
                              border: 'none',
                              color: 'white',
                              fontSize: '0.9rem',
                              fontWeight: '500',
                              cursor: 'pointer',
                              transition: 'all 0.3s ease',
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              gap: '0.5rem',
                            }}
                            onMouseEnter={(e) => {
                              e.currentTarget.style.transform = 'translateY(-2px)';
                              e.currentTarget.style.boxShadow = '0 5px 15px rgba(255, 107, 107, 0.4)';
                            }}
                            onMouseLeave={(e) => {
                              e.currentTarget.style.transform = 'translateY(0)';
                              e.currentTarget.style.boxShadow = 'none';
                            }}
                          >
                            📺 Watch Video
                          </button>
                        )}
                        
                        {!single.youtube_url && (
                          <div style={{
                            flex: 1,
                            padding: '0.75rem 1rem',
                            borderRadius: '10px',
                            background: 'rgba(255, 255, 255, 0.1)',
                            border: '1px solid rgba(255, 255, 255, 0.2)',
                            color: 'var(--text-secondary)',
                            fontSize: '0.9rem',
                            fontWeight: '500',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            gap: '0.5rem',
                          }}>
                            🎵 Audio Only
                          </div>
                        )}
                      </div>
                    </div>
                  </motion.div>
                ))
              ) : (
                <div style={{
                  textAlign: 'center',
                  padding: '4rem 2rem',
                  color: 'var(--text-secondary)',
                  gridColumn: '1 / -1'
                }}>
                  <div style={{ fontSize: '3rem', marginBottom: '1rem', opacity: 0.5 }}>
                    🎵
                  </div>
                  <p style={{ fontSize: '1.2rem', marginBottom: '0.5rem' }}>
                    No singles found
                  </p>
                  <p style={{ fontSize: '0.9rem', opacity: 0.7 }}>
                    Try adjusting your search or filters
                  </p>
                </div>
              )}
            </div>
          </div>
        </section>

        {/* Video Modal */}
        {showVideoModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            style={{
              position: 'fixed',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              background: 'rgba(0, 0, 0, 0.9)',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              zIndex: 1000,
              padding: '2rem',
            }}
            onClick={closeVideoModal}
          >
            <motion.div
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.8, opacity: 0 }}
              style={{
                background: 'var(--primary-color)',
                borderRadius: '15px',
                padding: '2rem',
                maxWidth: '800px',
                width: '100%',
                maxHeight: '90vh',
                overflow: 'auto',
                position: 'relative',
              }}
              onClick={(e) => e.stopPropagation()}
            >
              <button
                onClick={closeVideoModal}
                style={{
                  position: 'absolute',
                  top: '1rem',
                  right: '1rem',
                  background: 'none',
                  border: 'none',
                  color: 'var(--text-primary)',
                  fontSize: '1.5rem',
                  cursor: 'pointer',
                  padding: '0.5rem',
                  borderRadius: '50%',
                  transition: 'all 0.2s ease',
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.background = 'transparent';
                }}
              >
                ✕
              </button>
              
              <h3 style={{ color: 'var(--text-primary)', margin: '0 0 1.5rem 0', textAlign: 'center' }}>
                🎬 Video Player
              </h3>
              
              <div style={{
                position: 'relative',
                paddingBottom: '56.25%',
                height: 0,
                overflow: 'hidden',
                borderRadius: '10px',
                background: '#000',
              }}>
                <iframe
                  src={currentVideoUrl}
                  style={{
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    border: 'none',
                    borderRadius: '10px',
                  }}
                  allowFullScreen
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowAutoplay="true"
                  frameBorder="0"
                />
              </div>
            </motion.div>
          </motion.div>
        )}
      </div>
    </>
  );
};

export default Music;
