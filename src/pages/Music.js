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
  const [showVideoModal, setShowVideoModal] = useState(false);
  const [currentVideoUrl, setCurrentVideoUrl] = useState('');
  const [currentAudioUrl, setCurrentAudioUrl] = useState('');
  const [isExtractingAudio, setIsExtractingAudio] = useState(false);
  const audioRef = useRef(null);

  // Load data from API
  useEffect(() => {
    const loadData = async () => {
      try {
        setLoading(true);
        console.log('🔄 Loading albums...');
        const albumsData = await musicAPI.getAlbums();
        console.log('📊 Albums data:', albumsData);
        console.log('📊 Albums count:', albumsData.length);
        
        console.log('🔄 Loading singles...');
        const singlesData = await musicAPI.getSingles();
        console.log('📊 Singles data:', singlesData);
        console.log('📊 Singles count:', singlesData.length);
        
        // Load tracks for each album
        const albumsWithTracks = await Promise.all(
          albumsData.map(async (album) => {
            try {
              console.log('🔄 Loading tracks for album:', album.title);
              const response = await fetch(`http://localhost/madam-portfolio/backend/api/albums_fixed.php?album_id=${album.id}&include_tracks=1`);
              const albumWithTracks = await response.json();
              console.log('📊 Album with tracks:', albumWithTracks);
              
              // Log audio file info for each track
              if (albumWithTracks.tracks) {
                albumWithTracks.tracks.forEach((track, index) => {
                  console.log(`🎵 Track ${index + 1}:`, track.title);
                  console.log('🎵 Audio file:', track.audio_file);
                  console.log('📺 YouTube URL:', track.youtube_url);
                });
                
                // Auto-play the first track with audio
                const firstTrackWithAudio = albumWithTracks.tracks.find(track => track.audio_file);
                if (firstTrackWithAudio && !currentTrack) {
                  console.log('🎵 Auto-playing first track with audio:', firstTrackWithAudio.title);
                  setCurrentTrack(firstTrackWithAudio);
                  setIsPlaying(true);
                }
              }
              
              // Fix the cover image URL
              if (albumWithTracks.cover_image) {
                albumWithTracks.cover_image = `http://localhost/madam-portfolio/backend/${albumWithTracks.cover_image}`;
                console.log('📸 Fixed cover image URL:', albumWithTracks.cover_image);
              }
              
              return albumWithTracks;
            } catch (error) {
              console.error(`Error loading tracks for album ${album.id}:`, error);
              return { ...album, tracks: [] };
            }
          })
        );
        
        setAlbums(albumsWithTracks);
        
        // Fix singles cover image URLs
        const singlesWithFixedImages = singlesData.map(single => {
          console.log('📸 Single cover image path:', single.cover_image);
          if (single.cover_image) {
            single.cover_image = `http://localhost/madam-portfolio/backend/${single.cover_image}`;
            console.log('📸 Fixed single cover image URL:', single.cover_image);
          }
          return single;
        });
        
        setSingles(singlesWithFixedImages);
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

  const handleTrackClick = async (track) => {
    setCurrentTrack(track);
    
    // First test CORS with a simple endpoint
    console.log('🔍 Testing CORS...');
    try {
      const corsTest = await fetch('http://localhost/madam-portfolio/backend/test_cors_fix.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ test: 'cors' })
      });
      
      console.log('🔍 CORS Test Response:', corsTest.status, corsTest.ok);
      const corsResult = await corsTest.json();
      console.log('🔍 CORS Test Result:', corsResult);
      
      if (!corsTest.ok) {
        console.error('❌ CORS test failed');
        alert('CORS test failed. Backend may not be configured correctly.');
        setIsExtractingAudio(false);
        return;
      }
    } catch (error) {
      console.error('❌ CORS test error:', error);
      alert('CORS test failed: ' + error.message);
      setIsExtractingAudio(false);
      return;
    }
    
    // If CORS test passes, proceed with audio extraction
    console.log('✅ CORS test passed, proceeding with audio extraction...');
    
    // If track has YouTube URL but no audio file, extract audio first
    if (track.youtube_url && !track.audio_file) {
      console.log('🎬 Extracting audio from YouTube video...');
      setIsExtractingAudio(true);
      
      try {
        // Call the audio extraction API
        const response = await fetch('http://localhost/madam-portfolio/backend/services/youtube_audio_extractor.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            action: 'extract_single',
            youtube_url: track.youtube_url,
            track_id: track.id,
            album_id: track.album_id
          })
        });
        
        console.log('📡 API Response status:', response.status);
        console.log('📡 API Response ok:', response.ok);
        
        const result = await response.json();
        
        if (result.success) {
          console.log('✅ Audio extracted successfully:', result.audio_file);
          
          // Update the track with the new audio file
          track.audio_file = result.audio_file;
          console.log('🔄 Updated track with audio_file:', track.audio_file);
          setCurrentTrack(track);
          console.log('🔄 Set currentTrack:', currentTrack);
          
          // Now play the audio
          setIsPlaying(true);
          
          // Show success message
          alert('Audio extracted successfully! Playing track...');
        } else {
          console.error('❌ Audio extraction failed:', result.message);
          alert('Failed to extract audio: ' + result.message);
        }
      } catch (error) {
        console.error('❌ Error extracting audio:', error);
        console.error('❌ Full error details:', error.message);
        console.error('❌ Error stack:', error.stack);
        console.error('❌ Error type:', error.name);
        
        // Show more detailed error to user
        if (error.message.includes('fetch')) {
          console.error('❌ Network error - checking API URL...');
          console.error('❌ API URL: http://localhost/madam-portfolio/backend/services/youtube_audio_extractor.php');
          alert('Network error: Could not connect to audio extraction service. Please check if the backend is running.');
        } else if (error.message.includes('JSON')) {
          alert('Server error: Invalid response from extraction service');
        } else {
          alert('Error extracting audio: ' + error.message);
        }
      } finally {
        setIsExtractingAudio(false);
      }
    } else if (track.audio_file) {
      // Track already has audio file, just play it
      console.log('🎵 Playing existing audio file');
      setIsPlaying(true);
    } else {
      console.log('No audio file or YouTube URL available for this track');
    }
  };

  const openVideoModal = (youtubeUrl, audioUrl = null) => {
    console.log('🎬 Opening video modal');
    console.log('📹 YouTube URL:', youtubeUrl);
    console.log('🎵 Audio URL:', audioUrl);
    
    // Convert YouTube URL to embed format
    let embedUrl = youtubeUrl;
    
    // Ensure URL starts with https
    if (youtubeUrl.startsWith('http://')) {
      embedUrl = youtubeUrl.replace('http://', 'https://');
    } else if (!youtubeUrl.startsWith('https://')) {
      embedUrl = 'https://' + youtubeUrl;
    }
    
    if (embedUrl.includes('watch?v=')) {
      const videoId = embedUrl.split('watch?v=')[1].split('&')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    } else if (embedUrl.includes('youtu.be/')) {
      const videoId = embedUrl.split('youtu.be/')[1].split('?')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    } else if (embedUrl.includes('youtube.com/embed/')) {
      embedUrl = embedUrl + '?autoplay=1&rel=0';
    }
    
    console.log('📹 Embed URL:', embedUrl);
    
    // Set both video and audio URLs
    setCurrentVideoUrl(embedUrl);
    setCurrentAudioUrl(audioUrl);
    setShowVideoModal(true);
    
    console.log('🎬 Modal opened with video and audio');
  };

  const openYouTubeInNewTab = (youtubeUrl) => {
    // Ensure URL is properly formatted
    let finalUrl = youtubeUrl;
    if (!youtubeUrl.startsWith('http')) {
      finalUrl = 'https://' + youtubeUrl;
    }
    console.log('Opening YouTube in new tab:', finalUrl);
    window.open(finalUrl, '_blank');
  };

  const closeVideoModal = () => {
    setShowVideoModal(false);
    setCurrentVideoUrl('');
    setCurrentAudioUrl('');
    
    // Stop audio if playing
    if (audioRef.current) {
      audioRef.current.pause();
      audioRef.current.currentTime = 0;
    }
  };

  const togglePlayPause = () => {
    const audio = audioRef.current;
    if (!audio || !currentTrack) return;
    
    // Check if current track has audio file
    if (!currentTrack.audio_file) {
      console.log('❌ No audio file available for this track');
      console.log('❌ currentTrack.audio_file:', currentTrack.audio_file);
      return;
    }
    
    console.log('🎵 Toggling play/pause for track:', currentTrack.title);
    console.log('🎵 Audio file:', currentTrack.audio_file);
    console.log('🎵 Audio src:', audio.src);
    
    if (isPlaying) {
      audio.pause();
    } else {
      audio.play();
    }
    setIsPlaying(!isPlaying);
  };

  const handleSeek = (e) => {
    const audio = audioRef.current;
    if (!audio || !currentTrack || !currentTrack.audio_file) return;
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
        src={currentTrack && currentTrack.audio_file ? `http://localhost/madam-portfolio/backend/uploads/${currentTrack.audio_file}` : ''}
        onEnded={() => setIsPlaying(false)}
        onError={(e) => {
          console.error('❌ Audio element error:', e);
          console.error('❌ Audio src:', e.target.src);
          console.error('❌ Current track audio_file:', currentTrack?.audio_file);
        }}
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
                  src={currentTrack?.cover_image || ""}
                  alt="Track"
                  onError={(e) => {
                    e.target.style.display = 'none';
                  }}
                  style={{
                    width: '50px',
                    height: '50px',
                    borderRadius: '8px',
                    backgroundColor: currentTrack?.cover_image ? 'transparent' : '#2a2a2a',
                    display: currentTrack?.cover_image ? 'block' : 'none'
                  }}
                />
                {!currentTrack?.cover_image && (
                  <div style={{
                    width: '50px',
                    height: '50px',
                    borderRadius: '8px',
                    backgroundColor: '#2a2a2a',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: '#ffffff',
                    fontSize: '12px',
                    fontWeight: 'bold'
                  }}>
                    🎵
                  </div>
                )}
                <div>
                  <h4 style={{ color: 'var(--text-primary)', margin: 0, fontSize: '1rem' }}>
                    {currentTrack.title}
                  </h4>
                  <p style={{ color: 'var(--text-secondary)', margin: 0, fontSize: '0.9rem' }}>
                    {currentTrack.artist}
                    {currentTrack.youtube_url && (
                      <span style={{ color: '#ff0000', marginLeft: '0.5rem', fontSize: '0.8rem' }}>
                        • 📺 Video Available
                      </span>
                    )}
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
                      handleTrackClick(allTracks[prevIndex]);
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
                      handleTrackClick(allTracks[nextIndex]);
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
                    src={album.cover_image || `https://via.placeholder.com/120x120/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}`}
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
                  {album.tracks && album.tracks.length > 0 ? (
                    album.tracks.map((track, trackIndex) => (
                      <div
                        key={track.id || trackIndex}
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
                        onClick={() => handleTrackClick(track)}
                      >
                        <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                          <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                            {track.track_number || trackIndex + 1}
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
                              {track.duration || 'Unknown duration'}
                            </p>
                          </div>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                          {track.youtube_url && (
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                openVideoModal(track.youtube_url, track.audio_file);
                              }}
                              style={{
                                background: 'none',
                                border: 'none',
                                color: '#ff0000',
                                fontSize: '0.9rem',
                                cursor: 'pointer',
                                padding: '0.25rem',
                              }}
                              title="Watch on YouTube"
                            >
                              📺
                            </button>
                          )}
                          <button
                            onClick={(e) => {
                              e.stopPropagation();
                              handleTrackClick(track);
                            }}
                            disabled={isExtractingAudio}
                            style={{
                              background: 'none',
                              border: 'none',
                              color: currentTrack?.id === track.id ? 'var(--accent-color)' : 'var(--text-secondary)',
                              fontSize: '1rem',
                              cursor: isExtractingAudio ? 'not-allowed' : 'pointer',
                              opacity: isExtractingAudio ? 0.5 : 1,
                            }}
                          >
                            {isExtractingAudio && currentTrack?.id === track.id ? '⏳' : (currentTrack?.id === track.id ? '👁 Selected' : '▶')}
                          </button>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div style={{ 
                      textAlign: 'center', 
                      color: 'var(--text-muted)', 
                      padding: '2rem',
                      fontStyle: 'italic'
                    }}>
                      No tracks available for this album
                    </div>
                  )}
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
                  src={single.cover_image || `https://via.placeholder.com/250x250/2a2a2a/ffffff?text=${encodeURIComponent(single.title)}`}
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
                  onClick={() => single.youtube_url ? openVideoModal(single.youtube_url, single.audio_file) : handleTrackClick(single)}
                  className="btn btn-secondary btn-sm"
                  style={{ width: '100%' }}
                >
                  {single.youtube_url ? '📺 Watch Video' : (currentTrack?.id === single.id && isPlaying ? '⏸ Playing' : '▶ Play')}
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

      {/* YouTube Video Modal */}
      {showVideoModal && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          backgroundColor: 'rgba(0, 0, 0, 0.9)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 1000,
        }}>
          <div style={{
            position: 'relative',
            width: '90%',
            maxWidth: '800px',
            backgroundColor: '#000',
            borderRadius: '10px',
            overflow: 'hidden',
          }}>
            {/* Close Button */}
            <button
              onClick={closeVideoModal}
              style={{
                position: 'absolute',
                top: '10px',
                right: '10px',
                background: 'rgba(255, 255, 255, 0.2)',
                border: 'none',
                borderRadius: '50%',
                width: '40px',
                height: '40px',
                color: 'white',
                fontSize: '20px',
                cursor: 'pointer',
                zIndex: 1001,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              ×
            </button>
            
            {/* Fallback Button */}
            <button
              onClick={() => {
                const originalUrl = currentVideoUrl.replace('/embed/', '/watch?v=').split('?')[0];
                openYouTubeInNewTab(originalUrl);
              }}
              style={{
                position: 'absolute',
                top: '10px',
                left: '10px',
                background: 'rgba(255, 0, 0, 0.8)',
                border: 'none',
                borderRadius: '5px',
                padding: '8px 12px',
                color: 'white',
                fontSize: '12px',
                cursor: 'pointer',
                zIndex: 1001,
                display: 'flex',
                alignItems: 'center',
                gap: '5px',
              }}
              title="Open in YouTube (if video doesn't play)"
            >
              📺 YouTube
            </button>
            
            {/* YouTube Video */}
            <div style={{
              position: 'relative',
              paddingBottom: '56.25%', // 16:9 aspect ratio
              height: 0,
            }}>
              <iframe
                style={{
                  position: 'absolute',
                  top: 0,
                  left: 0,
                  width: '100%',
                  height: '100%',
                  border: 'none',
                }}
                src={currentVideoUrl}
                title="YouTube video player"
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
              />
            </div>
            
            {/* Audio Player */}
            {currentAudioUrl && (
              <div style={{
                marginTop: '1rem',
                padding: '1rem',
                background: 'rgba(255, 255, 255, 0.1)',
                borderRadius: '8px',
              }}>
                <h4 style={{ color: 'white', margin: '0 0 0.5rem 0', fontSize: '0.9rem' }}>
                  🎵 Audio Track
                </h4>
                <audio
                  ref={audioRef}
                  src={currentAudioUrl ? `http://localhost/madam-portfolio/backend/uploads/${currentAudioUrl}` : ''}
                  controls
                  style={{
                    width: '100%',
                    borderRadius: '4px',
                  }}
                  onEnded={() => {
                    console.log('Audio ended');
                  }}
                />
              </div>
            )}
          </div>
        </div>
      )}
      </div>
    </>
  );
};

export default Music;
