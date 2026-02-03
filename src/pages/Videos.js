import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { demoImages } from '../config/demoImages';

const Videos = () => {
  const [selectedVideo, setSelectedVideo] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');

  // Static demo music videos
  const musicVideos = [
    {
      id: 1,
      title: "Echoes of Emotion",
      description: "Official music video for the title track from the latest album",
      thumbnail: demoImages.videos.music1,
      videoId: "dQw4w9WgXcQ", // YouTube video ID
      duration: "4:32",
      views: "1.2M",
      releaseDate: "2024",
      category: "music"
    },
    {
      id: 2,
      title: "Midnight Melodies",
      description: "A visual journey through the night with soulful melodies",
      thumbnail: demoImages.videos.music2,
      videoId: "dQw4w9WgXcQ",
      duration: "3:45",
      views: "856K",
      releaseDate: "2024",
      category: "music"
    },
    {
      id: 3,
      title: "Dancing in the Rain",
      description: "Upbeat track celebrating joy and freedom",
      thumbnail: demoImages.videos.music3,
      videoId: "dQw4w9WgXcQ",
      duration: "3:28",
      views: "2.1M",
      releaseDate: "2024",
      category: "music"
    }
  ];

  // Static demo live performances
  const livePerformances = [
    {
      id: 4,
      title: "Live at Madison Square Garden",
      description: "Full concert performance from the world tour",
      thumbnail: demoImages.videos.live1,
      videoId: "dQw4w9WgXcQ",
      duration: "15:20",
      views: "3.5M",
      releaseDate: "2024",
      category: "live"
    },
    {
      id: 5,
      title: "Acoustic Session at Abbey Road",
      description: "Intimate acoustic performance at the legendary studio",
      thumbnail: demoImages.videos.live2,
      videoId: "dQw4w9WgXcQ",
      duration: "22:15",
      views: "1.8M",
      releaseDate: "2023",
      category: "acoustic"
    },
    {
      id: 6,
      title: "Festival Headliner Performance",
      description: "Electric performance at Summer Music Festival",
      thumbnail: demoImages.videos.live3,
      videoId: "dQw4w9WgXcQ",
      duration: "45:00",
      views: "5.2M",
      releaseDate: "2023",
      category: "live"
    }
  ];

  // Static demo behind the scenes
  const behindTheScenes = [
    {
      id: 7,
      title: "Making of Echoes of Emotion",
      description: "Behind the scenes footage from the album recording sessions",
      thumbnail: demoImages.videos.behind1,
      videoId: "dQw4w9WgXcQ",
      duration: "12:30",
      views: "445K",
      releaseDate: "2024",
      category: "behind"
    },
    {
      id: 8,
      title: "Music Video Shoot",
      description: "The making of 'Whispers of the Soul' video",
      thumbnail: demoImages.videos.behind2,
      videoId: "dQw4w9WgXcQ",
      duration: "8:30",
      views: "189K",
      releaseDate: "2024",
      category: "behind"
    },
    {
      id: 9,
      title: "Tour Diary",
      description: "Life on the road during the world tour",
      thumbnail: demoImages.videos.behind3,
      videoId: "dQw4w9WgXcQ",
      duration: "15:20",
      views: "445K",
      releaseDate: "2023",
      category: "behind"
    }
  ];

  // Categories for filtering
  const categories = [
    { id: 'all', label: 'All Videos', icon: '🎬' },
    { id: 'music', label: 'Music Videos', icon: '🎵' },
    { id: 'live', label: 'Live Performances', icon: '🎤' },
    { id: 'acoustic', label: 'Acoustic', icon: '🎸' },
    { id: 'behind', label: 'Behind Scenes', icon: '🎥' },
  ];

  // Filter individual categories for display
  const filteredMusicVideos = musicVideos.filter(video => {
    const matchesSearch = video.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         video.description.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || video.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const filteredLivePerformances = livePerformances.filter(video => {
    const matchesSearch = video.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         video.description.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || video.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const filteredBehindTheScenes = behindTheScenes.filter(video => {
    const matchesSearch = video.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         video.description.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || video.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });


  const openVideoModal = (video) => {
    setSelectedVideo(video);
  };

  const closeModal = () => {
    setSelectedVideo(null);
  };

  return (
    <div className="videos">
      {/* Hero Section */}
      <section className="videos-hero" style={{
        padding: '8rem 0 4rem',
        background: 'linear-gradient(135deg, rgba(26, 26, 26, 0.85) 0%, rgba(42, 42, 42, 0.9) 100%), url("https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=1920&h=800&fit=crop&crop=entropy&auto=format") center/cover',
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
              Videos
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto 2rem',
              lineHeight: 1.6,
            }}>
              Music videos, live performances, and exclusive behind-the-scenes content
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
                placeholder="Search for videos..."
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

      {/* Music Videos Section */}
      <section className="music-videos" style={{
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
              Music Videos
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Official music videos and visual stories
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {filteredMusicVideos.length > 0 ? (
              filteredMusicVideos.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', paddingBottom: '56.25%', overflow: 'hidden', borderRadius: '12px' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      position: 'absolute',
                      top: 0,
                      left: 0,
                      width: '100%',
                      height: '100%',
                      objectFit: 'cover',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    bottom: '1rem',
                    right: '1rem',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '0.25rem 0.5rem',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </div>
                </div>
                <div style={{ padding: '1.5rem' }}>
                  <h3 style={{ color: 'var(--text-primary)', margin: '0 0 0.5rem' }}>
                    {video.title}
                  </h3>
                  <p style={{ color: 'var(--text-secondary)', margin: '0 0 0.5rem', fontSize: '0.9rem' }}>
                    {video.views} views • {video.releaseDate}
                  </p>
                  <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                    {video.description}
                  </p>
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
                  No music videos found matching "{searchTerm}"
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

      {/* Live Performances Section */}
      <section className="live-performances" style={{
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
              Live Performances
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Concert footage and live recordings
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {filteredLivePerformances.length > 0 ? (
              filteredLivePerformances.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', marginBottom: '1rem' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      width: '100%',
                      borderRadius: '10px',
                      aspectRatio: '16/9',
                      objectFit: 'cover',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '10px',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </div>
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    background: 'rgba(0, 0, 0, 0.7)',
                    color: 'white',
                    width: '60px',
                    height: '60px',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.5rem',
                    transition: 'all 0.3s ease',
                  }}>
                    ▶
                  </div>
                </div>
                <h3 style={{ color: 'var(--text-primary)', margin: '0 0 0.5rem' }}>
                  {video.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', margin: '0 0 0.5rem', fontSize: '0.9rem' }}>
                  {video.views} views • {video.releaseDate}
                </p>
                <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                  {video.description}
                </p>
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
                  No live performances found matching "{searchTerm}"
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

      {/* Behind the Scenes Section */}
      <section className="behind-the-scenes" style={{
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
              Behind the Scenes
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Exclusive content and making-of footage
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {filteredBehindTheScenes.length > 0 ? (
              filteredBehindTheScenes.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', marginBottom: '1rem' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      width: '100%',
                      borderRadius: '10px',
                      aspectRatio: '16/9',
                      objectFit: 'cover',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '10px',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </div>
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    background: 'rgba(0, 0, 0, 0.7)',
                    color: 'white',
                    width: '60px',
                    height: '60px',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.5rem',
                    transition: 'all 0.3s ease',
                  }}>
                    ▶
                  </div>
                </div>
                <h3 style={{ color: 'var(--text-primary)', margin: '0 0 0.5rem' }}>
                  {video.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', margin: '0 0 0.5rem', fontSize: '0.9rem' }}>
                  {video.views} views • {video.releaseDate}
                </p>
                <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                  {video.description}
                </p>
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
                  No behind the scenes videos found matching "{searchTerm}"
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

      {/* Video Modal */}
      {selectedVideo && (
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
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 1000,
          }}
          onClick={closeModal}
        >
          <motion.div
            initial={{ scale: 0.8 }}
            animate={{ scale: 1 }}
            exit={{ scale: 0.8 }}
            style={{
              background: 'black',
              borderRadius: '10px',
              overflow: 'hidden',
              maxWidth: '90vw',
              maxHeight: '90vh',
              position: 'relative',
            }}
            onClick={(e) => e.stopPropagation()}
          >
            <button
              onClick={closeModal}
              style={{
                position: 'absolute',
                top: '10px',
                right: '10px',
                background: 'rgba(255, 255, 255, 0.2)',
                border: 'none',
                color: 'white',
                width: '40px',
                height: '40px',
                borderRadius: '50%',
                cursor: 'pointer',
                fontSize: '1.5rem',
                zIndex: 1001,
              }}
            >
              ×
            </button>
            
            {/* YouTube Embed */}
            <div style={{
              width: '85vw',
              height: '50vw',
              maxWidth: '1200px',
              maxHeight: '675px',
            }}>
              <iframe
                width="100%"
                height="100%"
                src={`https://www.youtube.com/embed/${selectedVideo.videoId}?autoplay=1`}
                title={selectedVideo.title}
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
                style={{ borderRadius: '10px' }}
              />
            </div>
            
            <div style={{
              padding: '1rem',
              color: 'white',
              maxWidth: '85vw',
            }}>
              <h3 style={{ margin: '0 0 0.5rem' }}>{selectedVideo.title}</h3>
              <p style={{ margin: 0, color: '#ccc' }}>{selectedVideo.description}</p>
              <p style={{ margin: '0.5rem 0 0', color: '#999', fontSize: '0.9rem' }}>
                {selectedVideo.views} views • {selectedVideo.releaseDate}
              </p>
            </div>
          </motion.div>
        </motion.div>
      )}
    </div>
  );
};

export default Videos;
