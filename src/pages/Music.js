import React, { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';
import { musicAPI } from '../services/api';
import ButtonStyles from '../styles/ButtonStyles';

const Music = () => {
  const [currentTrack, setCurrentTrack] = useState(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);
  const [volume, setVolume] = useState(0.7);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [albums, setAlbums] = useState([]);
  const [singles, setSingles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const audioRef = useRef(null);

  // Load data from API
  useEffect(() => {
    const loadData = async () => {
      try {
        setLoading(true);
        const albumsData = await musicAPI.getAlbums();
        const singlesData = await musicAPI.getSingles();
        setAlbums(albumsData);
        setSingles(singlesData);
        setError(null);
      } catch (err) {
        console.error('Error loading music data:', err);
        setError('Failed to load music data');
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, []);

  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;

    const updateTime = () => setCurrentTime(audio.currentTime);
    const updateDuration = () => setDuration(audio.duration);

    audio.addEventListener('timeupdate', updateTime);
    audio.addEventListener('loadedmetadata', updateDuration);

    return () => {
      audio.removeEventListener('timeupdate', updateTime);
      audio.removeEventListener('loadedmetadata', updateDuration);
    };
  }, [currentTrack]);

  const playTrack = (track) => {
    setCurrentTrack(track);
    setIsPlaying(true);
  };

  const togglePlayPause = () => {
    const audio = audioRef.current;
    if (!audio || !currentTrack) return;

    if (isPlaying) {
      audio.pause();
    } else {
      audio.play();
    }
    setIsPlaying(!isPlaying);
  };

  const handleSeek = (e) => {
    const audio = audioRef.current;
    if (!audio) return;
    const newTime = (e.target.value / 100) * duration;
    audio.currentTime = newTime;
    setCurrentTime(newTime);
  };

  const handleVolumeChange = (e) => {
    const audio = audioRef.current;
    if (!audio) return;
    const newVolume = e.target.value / 100;
    audio.volume = newVolume;
    setVolume(newVolume);
  };

  const formatTime = (time) => {
    if (!time || isNaN(time)) return "0:00";
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60);
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
  };

  const allTracks = [
    ...albums.flatMap(album => album.tracks),
    ...singles
  ];

  // Categories for filtering
  const categories = [
    { id: 'all', label: 'All Music', icon: '🎵' },
    { id: 'album', label: 'Albums', icon: '💿' },
    { id: 'single', label: 'Singles', icon: '🎤' },
    { id: 'acoustic', label: 'Acoustic', icon: '🎸' },
  ];

  // Filter albums based on search term and category
  const filteredAlbums = albums.filter(album => {
    const matchesSearch = album.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         album.year.includes(searchTerm) ||
                         (album.tracks && album.tracks.some(track => 
                           track.title.toLowerCase().includes(searchTerm.toLowerCase())
                         ));
    const matchesCategory = selectedCategory === 'all' || album.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  // Filter singles based on search term and category
  const filteredSingles = singles.filter(single => {
    const matchesSearch = single.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         single.artist.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         single.release_date.includes(searchTerm);
    const matchesCategory = selectedCategory === 'all' || single.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  // Show loading state
  if (loading) {
    return (
      <div className="music" style={{ textAlign: 'center', padding: '4rem' }}>
        <h2>Loading music...</h2>
      </div>
    );
  }

  // Show error state
  if (error) {
    return (
      <div className="music" style={{ textAlign: 'center', padding: '4rem' }}>
        <h2>Error loading music</h2>
        <p>{error}</p>
      </div>
    );
  }

  return (
    <>
      <style jsx>{`
        ${ButtonStyles}
      `}</style>
      <div className="music">
      {/* Hidden Audio Element */}
      <audio
        ref={audioRef}
        src={currentTrack ? `https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3` : ''}
        onEnded={() => setIsPlaying(false)}
      />

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
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
          >
            <h1 style={{
              fontFamily: "'Playfair Display', serif",
              fontSize: 'clamp(2.5rem, 5vw, 4rem)',
              color: 'var(--text-primary)',
              marginBottom: '1rem',
            }}>
              Music
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto 2rem',
              lineHeight: 1.6,
            }}>
              Explore my discography, from intimate acoustic sessions to powerful orchestral arrangements
            </p>
            
            {/* Search Bar */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              style={{
                maxWidth: '500px',
                margin: '0 auto',
                position: 'relative',
              }}
            >
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
                fontSize: '1.2rem',
              }}>
                🔍
              </div>
            </motion.div>
            
            {/* Category Filter */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.3 }}
              style={{
                maxWidth: '600px',
                margin: '2rem auto 0',
              }}
            >
              <div style={{
                display: 'flex',
                flexWrap: 'wrap',
                gap: '0.5rem',
                justifyContent: 'center',
              }}>
                {categories.map((category) => (
                  <motion.button
                    key={category.id}
                    onClick={() => setSelectedCategory(category.id)}
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    style={{
                      padding: '0.5rem 1rem',
                      background: selectedCategory === category.id 
                        ? 'var(--accent-color)' 
                        : 'rgba(255, 255, 255, 0.1)',
                      border: selectedCategory === category.id 
                        ? '1px solid var(--accent-color)' 
                        : '1px solid rgba(255, 255, 255, 0.2)',
                      borderRadius: '25px',
                      color: selectedCategory === category.id 
                        ? 'white' 
                        : 'var(--text-secondary)',
                      cursor: 'pointer',
                      fontSize: '0.9rem',
                      backdropFilter: 'blur(10px)',
                      transition: 'all 0.3s ease',
                      display: 'flex',
                      alignItems: 'center',
                      gap: '0.5rem',
                    }}
                  >
                    <span>{category.icon}</span>
                    <span>{category.label}</span>
                  </motion.button>
                ))}
              </div>
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Music Player */}
      {currentTrack && (
        <motion.section
          initial={{ opacity: 0, y: 50 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          style={{
            position: 'fixed',
            bottom: 0,
            left: 0,
            right: 0,
            background: 'rgba(26, 26, 26, 0.95)',
            backdropFilter: 'blur(10px)',
            borderTop: '1px solid var(--border-color)',
            padding: '1rem 0',
            zIndex: 1000,
          }}
        >
          <div className="container">
            <div style={{
              display: 'grid',
              gridTemplateColumns: '1fr 2fr 1fr',
              gap: '2rem',
              alignItems: 'center',
            }}>
              {/* Track Info */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                <img
                  src="https://via.placeholder.com/50x50/2a2a2a/ffffff?text=Track"
                  alt="Track"
                  style={{
                    width: '50px',
                    height: '50px',
                    borderRadius: '8px',
                  }}
                />
                <div>
                  <h4 style={{ color: 'var(--text-primary)', margin: 0, fontSize: '1rem' }}>
                    {currentTrack.title}
                  </h4>
                  <p style={{ color: 'var(--text-secondary)', margin: 0, fontSize: '0.9rem' }}>
                    {currentTrack.artist}
                  </p>
                </div>
              </div>

              {/* Player Controls */}
              <div>
                <div style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: '1rem',
                  marginBottom: '0.5rem',
                }}>
                  <button
                    onClick={() => {
                      const currentIndex = allTracks.findIndex(t => t.id === currentTrack.id);
                      const prevIndex = currentIndex > 0 ? currentIndex - 1 : allTracks.length - 1;
                      playTrack(allTracks[prevIndex]);
                    }}
                    style={{
                      background: 'none',
                      border: 'none',
                      color: 'var(--text-primary)',
                      fontSize: '1.2rem',
                      cursor: 'pointer',
                    }}
                  >
                    ⏮
                  </button>
                  <button
                    onClick={togglePlayPause}
                    className="btn btn-primary"
                    style={{
                      width: '40px',
                      height: '40px',
                      borderRadius: '50%',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      padding: 0,
                    }}
                  >
                    {isPlaying ? '⏸' : '▶'}
                  </button>
                  <button
                    onClick={() => {
                      const currentIndex = allTracks.findIndex(t => t.id === currentTrack.id);
                      const nextIndex = currentIndex < allTracks.length - 1 ? currentIndex + 1 : 0;
                      playTrack(allTracks[nextIndex]);
                    }}
                    style={{
                      background: 'none',
                      border: 'none',
                      color: 'var(--text-primary)',
                      fontSize: '1.2rem',
                      cursor: 'pointer',
                    }}
                  >
                    ⏭
                  </button>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                  <span style={{ color: 'var(--text-secondary)', fontSize: '0.8rem' }}>
                    {formatTime(currentTime)}
                  </span>
                  <input
                    type="range"
                    min="0"
                    max="100"
                    value={duration ? (currentTime / duration) * 100 : 0}
                    onChange={handleSeek}
                    style={{
                      flex: 1,
                      height: '4px',
                      background: 'rgba(255, 255, 255, 0.1)',
                      borderRadius: '2px',
                      outline: 'none',
                    }}
                  />
                  <span style={{ color: 'var(--text-secondary)', fontSize: '0.8rem' }}>
                    {formatTime(duration)}
                  </span>
                </div>
              </div>

              {/* Volume Control */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                <span style={{ color: 'var(--text-primary)' }}>🔊</span>
                <input
                  type="range"
                  min="0"
                  max="100"
                  value={volume * 100}
                  onChange={handleVolumeChange}
                  style={{
                    width: '80px',
                    height: '4px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    borderRadius: '2px',
                    outline: 'none',
                  }}
                />
              </div>
            </div>
          </div>
        </motion.section>
      )}

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
                    src={album.cover_image ? `/${album.cover_image}` : `https://via.placeholder.com/120x120/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}`}
                    alt={album.title}
                    onError={(e) => {
                      e.target.src = `https://via.placeholder.com/120x120/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}`;
                    }}
                    style={{
                      width: '120px',
                      height: '120px',
                      borderRadius: '10px',
                      objectFit: 'cover',
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
                      Play Album
                    </button>
                  </div>
                </div>

                <div style={{ borderTop: '1px solid var(--border-color)', paddingTop: '1rem' }}>
                  {album.tracks && album.tracks.map((track, trackIndex) => (
                    <div
                      key={track.id}
                      style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        padding: '0.5rem 0',
                        cursor: 'pointer',
                        borderRadius: '8px',
                        transition: 'background 0.3s ease',
                      }}
                      onMouseEnter={(e) => {
                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.05)';
                      }}
                      onMouseLeave={(e) => {
                        e.currentTarget.style.background = 'transparent';
                      }}
                      onClick={() => playTrack(track)}
                    >
                      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                        <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                          {trackIndex + 1}
                        </span>
                        <div>
                          <p style={{ 
                            color: currentTrack?.id === track.id ? 'var(--accent-color)' : 'var(--text-primary)', 
                            margin: 0,
                            fontSize: '0.95rem'
                          }}>
                            {track.title}
                          </p>
                          <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                            {track.artist}
                          </p>
                        </div>
                      </div>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                        <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                          {track.duration}
                        </span>
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            playTrack(track);
                          }}
                          style={{
                            background: 'none',
                            border: 'none',
                            color: currentTrack?.id === track.id && isPlaying ? 'var(--accent-color)' : 'var(--text-secondary)',
                            fontSize: '1rem',
                            cursor: 'pointer',
                          }}
                        >
                          {currentTrack?.id === track.id && isPlaying ? '⏸' : '▶'}
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </motion.div>
            ))
            ) : (
              <div style={{
                textAlign: 'center',
                padding: '3rem',
                color: 'var(--text-secondary)',
                gridColumn: '1 / -1'
              }}>
                <p style={{ fontSize: '1.1rem', marginBottom: '1rem' }}>
                  No albums found matching "{searchTerm}"
                </p>
                <button
                  onClick={() => setSearchTerm('')}
                  style={{
                    padding: '0.5rem 1rem',
                    background: 'var(--accent-color)',
                    border: 'none',
                    borderRadius: '20px',
                    color: 'white',
                    cursor: 'pointer',
                    fontSize: '0.9rem'
                  }}
                >
                  Clear Search
                </button>
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
              Standalone releases and special tracks
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
            gap: '2rem',
          }}>
            {filteredSingles.length > 0 ? (
              filteredSingles.map((single, index) => (
              <motion.div
                key={single.id}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ textAlign: 'center' }}
              >
                <img
                  src={single.cover_image ? `/${single.cover_image}` : `https://via.placeholder.com/250x250/2a2a2a/ffffff?text=${encodeURIComponent(single.title)}`}
                  alt={single.title}
                  onError={(e) => {
                    e.target.src = `https://via.placeholder.com/250x250/2a2a2a/ffffff?text=${encodeURIComponent(single.title)}`;
                  }}
                  style={{
                    width: '100%',
                    borderRadius: '10px',
                    marginBottom: '1rem',
                  }}
                />
                <h3 style={{ color: 'var(--text-primary)', margin: '0.5rem 0' }}>
                  {single.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', margin: '0.5rem 0', fontSize: '0.9rem' }}>
                  {single.release_date} • {single.duration}
                </p>
                <button
                  onClick={() => playTrack(single)}
                  className="btn btn-secondary btn-sm"
                  style={{ width: '100%' }}
                >
                  {currentTrack?.id === single.id && isPlaying ? '⏸ Playing' : '▶ Play'}
                </button>
              </motion.div>
            ))
            ) : (
              <div style={{
                textAlign: 'center',
                padding: '3rem',
                color: 'var(--text-secondary)',
                gridColumn: '1 / -1'
              }}>
                <p style={{ fontSize: '1.1rem', marginBottom: '1rem' }}>
                  No singles found matching "{searchTerm}"
                </p>
                <button
                  onClick={() => setSearchTerm('')}
                  style={{
                    padding: '0.5rem 1rem',
                    background: 'var(--accent-color)',
                    border: 'none',
                    borderRadius: '20px',
                    color: 'white',
                    cursor: 'pointer',
                    fontSize: '0.9rem'
                  }}
                >
                  Clear Search
                </button>
              </div>
            )}
          </div>
        </div>
      </section>
      </div>
    </>
  );
};

export default Music;
