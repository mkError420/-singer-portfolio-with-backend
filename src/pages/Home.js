import React from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { demoImages } from '../config/demoImages';

const Home = () => {
  return (
    <div className="home">
      {/* Hero Section */}
      <section className="hero" style={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        position: 'relative',
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%)',
        overflow: 'hidden',
      }}>
        {/* Background Video */}
        <video
          autoPlay
          loop
          muted
          playsInline
          style={{
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            zIndex: 1,
            opacity: 1.5,
          }}
        >
          <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4" />
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4" type="video/mp4" />
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
        
        {/* Dark Overlay for better text readability */}
        <div style={{
          position: 'absolute',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'linear-gradient(135deg, rgba(26, 26, 26, 0.6) 0%, rgba(42, 42, 42, 0.7) 100%)',
          zIndex: 2,
        }} />

        <div className="container" style={{ position: 'relative', zIndex: 3 }}>
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 1 }}
            style={{
              textAlign: 'center',
              maxWidth: '800px',
              margin: '0 auto',
            }}
          >
            <motion.h1
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.2 }}
              style={{
                fontFamily: "'Playfair Display', serif",
                fontSize: 'clamp(3rem, 8vw, 5rem)',
                marginBottom: '1rem',
                background: 'var(--gradient-primary)',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text',
              }}
            >
              Artist Name
            </motion.h1>

            <motion.p
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.4 }}
              style={{
                fontSize: 'clamp(1.2rem, 3vw, 1.5rem)',
                color: 'var(--text-secondary)',
                marginBottom: '2rem',
                lineHeight: 1.6,
              }}
            >
              Soulful melodies that touch the heart and inspire the soul
            </motion.p>

            <motion.div
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.6 }}
              style={{
                display: 'flex',
                gap: '1rem',
                justifyContent: 'center',
                flexWrap: 'wrap',
              }}
            >
              <Link to="/music" className="btn btn-primary">
                Listen Now
              </Link>
              <Link to="/videos" className="btn btn-secondary">
                Watch Videos
              </Link>
              <Link to="/contact" className="btn btn-secondary">
                Get in Touch
              </Link>
            </motion.div>
          </motion.div>
        </div>

        {/* Scroll Indicator */}
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 1, delay: 1 }}
          style={{
            position: 'absolute',
            bottom: '30px',
            left: '50%',
            transform: 'translateX(-50%)',
            zIndex: 2,
          }}
        >
          <motion.div
            animate={{ y: [0, 10, 0] }}
            transition={{ duration: 2, repeat: Infinity }}
            style={{
              width: '30px',
              height: '50px',
              border: '2px solid var(--accent-color)',
              borderRadius: '25px',
              position: 'relative',
            }}
          >
            <motion.div
              animate={{ y: [0, 15, 0] }}
              transition={{ duration: 2, repeat: Infinity }}
              style={{
                width: '4px',
                height: '10px',
                background: 'var(--accent-color)',
                borderRadius: '2px',
                position: 'absolute',
                top: '10px',
                left: '50%',
                transform: 'translateX(-50%)',
              }}
            />
          </motion.div>
        </motion.div>
      </section>

      {/* Latest Release Section */}
      <section className="latest-release" style={{
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
              Latest Release
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Experience the newest sound from my musical journey
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            className="card"
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
              gap: '2rem',
              alignItems: 'center',
            }}
          >
            <div>
              <img
                src={demoImages.albums[1]}
                alt="Latest Album"
                style={{
                  width: '100%',
                  maxWidth: '400px',
                  borderRadius: '15px',
                  boxShadow: '0 20px 40px rgba(0, 0, 0, 0.3)',
                }}
              />
            </div>
            <div>
              <h3 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
                Album Title
              </h3>
              <p style={{ color: 'var(--text-secondary)', marginBottom: '1.5rem' }}>
                A collection of songs that represent a new chapter in my musical journey. 
                Each track tells a story, conveying emotions through melody and lyrics.
              </p>
              <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
                <Link to="/music" className="btn btn-primary">
                  Listen Now
                </Link>
                <button className="btn btn-secondary">
                  Add to Playlist
                </button>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Featured Music Section */}
      <section className="featured-music" style={{
        padding: '5rem 0',
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '3rem' }}
          >
            <h2 style={{ 
              color: 'var(--text-primary)', 
              marginBottom: '1rem',
              fontFamily: "'Playfair Display', serif",
              fontSize: '2.5rem'
            }}>
              Featured Music
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Discover my latest tracks and albums
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
            gap: '2rem',
          }}>
            {[
              {
                title: "Echoes of Emotion",
                artist: "Artist Name",
                image: demoImages.albums[1],
                year: "2024",
                type: "Album"
              },
              {
                title: "Midnight Melodies",
                artist: "Artist Name", 
                image: demoImages.singles[1],
                year: "2024",
                type: "Single"
              },
              {
                title: "Acoustic Sessions",
                artist: "Artist Name",
                image: demoImages.albums[3],
                year: "2023",
                type: "EP"
              }
            ].map((music, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                whileHover={{ y: -5 }}
                className="card"
                style={{
                  background: 'rgba(255, 255, 255, 0.05)',
                  borderRadius: '15px',
                  overflow: 'hidden',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                }}
              >
                <div style={{ position: 'relative', paddingBottom: '100%', overflow: 'hidden' }}>
                  <img
                    src={music.image}
                    alt={music.title}
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
                    top: '1rem',
                    right: '1rem',
                    background: 'var(--accent-color)',
                    color: 'white',
                    padding: '0.25rem 0.75rem',
                    borderRadius: '20px',
                    fontSize: '0.8rem',
                    fontWeight: '500',
                  }}>
                    {music.type}
                  </div>
                </div>
                <div style={{ padding: '1.5rem' }}>
                  <h3 style={{ 
                    color: 'var(--text-primary)', 
                    marginBottom: '0.5rem',
                    fontSize: '1.2rem'
                  }}>
                    {music.title}
                  </h3>
                  <p style={{ 
                    color: 'var(--text-secondary)', 
                    marginBottom: '0.5rem',
                    fontSize: '0.9rem'
                  }}>
                    {music.artist} • {music.year}
                  </p>
                  <Link 
                    to="/music"
                    style={{
                      display: 'inline-flex',
                      alignItems: 'center',
                      gap: '0.5rem',
                      color: 'var(--accent-color)',
                      textDecoration: 'none',
                      fontSize: '0.9rem',
                      fontWeight: '500',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.target.style.color = '#ffffff';
                      e.target.style.transform = 'translateX(5px)';
                    }}
                    onMouseLeave={(e) => {
                      e.target.style.color = 'var(--accent-color)';
                      e.target.style.transform = 'translateX(0)';
                    }}
                  >
                    Listen Now →
                  </Link>
                </div>
              </motion.div>
            ))}
          </div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.4 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginTop: '3rem' }}
          >
            <Link 
              to="/music"
              className="btn btn-primary"
              style={{
                padding: '1rem 2rem',
                background: 'var(--accent-color)',
                border: 'none',
                borderRadius: '50px',
                color: 'white',
                textDecoration: 'none',
                fontSize: '1rem',
                fontWeight: '500',
                transition: 'all 0.3s ease',
                display: 'inline-block',
              }}
              onMouseEnter={(e) => {
                e.target.style.background = '#ffffff';
                e.target.style.color = 'var(--accent-color)';
                e.target.style.transform = 'translateY(-2px)';
              }}
              onMouseLeave={(e) => {
                e.target.style.background = 'var(--accent-color)';
                e.target.style.color = 'white';
                e.target.style.transform = 'translateY(0)';
              }}
            >
              View All Music
            </Link>
          </motion.div>
        </div>
      </section>

      {/* Featured Videos Section */}
      <section className="featured-videos" style={{
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
            <h2 style={{ 
              color: 'var(--text-primary)', 
              marginBottom: '1rem',
              fontFamily: "'Playfair Display', serif",
              fontSize: '2.5rem'
            }}>
              Featured Videos
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Watch music videos, live performances, and exclusive content
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {[
              {
                title: "Echoes of Emotion",
                description: "Official music video for the title track",
                thumbnail: demoImages.videos.music1,
                duration: "4:32",
                views: "1.2M",
                type: "Music Video"
              },
              {
                title: "Live at Madison Square",
                description: "Full concert performance from the world tour",
                thumbnail: demoImages.videos.live1,
                duration: "15:20",
                views: "3.5M",
                type: "Live Performance"
              },
              {
                title: "Behind the Scenes",
                description: "Making of the latest album",
                thumbnail: demoImages.videos.behind1,
                duration: "12:30",
                views: "445K",
                type: "Behind Scenes"
              }
            ].map((video, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                whileHover={{ y: -5 }}
                className="card"
                style={{
                  background: 'rgba(255, 255, 255, 0.05)',
                  borderRadius: '15px',
                  overflow: 'hidden',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                }}
              >
                <div style={{ position: 'relative', paddingBottom: '56.25%', overflow: 'hidden' }}>
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
                    top: '1rem',
                    right: '1rem',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '0.25rem 0.5rem',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </div>
                  <div style={{
                    position: 'absolute',
                    bottom: '1rem',
                    left: '1rem',
                    background: 'var(--accent-color)',
                    color: 'white',
                    padding: '0.25rem 0.75rem',
                    borderRadius: '20px',
                    fontSize: '0.8rem',
                    fontWeight: '500',
                  }}>
                    {video.type}
                  </div>
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255, 255, 255, 0.9)',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    cursor: 'pointer',
                    transition: 'all 0.3s ease',
                  }}
                  onMouseEnter={(e) => {
                    e.target.style.background = 'var(--accent-color)';
                    e.target.style.transform = 'translate(-50%, -50%) scale(1.1)';
                  }}
                  onMouseLeave={(e) => {
                    e.target.style.background = 'rgba(255, 255, 255, 0.9)';
                    e.target.style.transform = 'translate(-50%, -50%) scale(1)';
                  }}
                  >
                    <div style={{
                      width: 0,
                      height: 0,
                      borderLeft: '15px solid var(--primary-color)',
                      borderTop: '10px solid transparent',
                      borderBottom: '10px solid transparent',
                      marginLeft: '3px',
                    }} />
                  </div>
                </div>
                <div style={{ padding: '1.5rem' }}>
                  <h3 style={{ 
                    color: 'var(--text-primary)', 
                    marginBottom: '0.5rem',
                    fontSize: '1.2rem'
                  }}>
                    {video.title}
                  </h3>
                  <p style={{ 
                    color: 'var(--text-secondary)', 
                    marginBottom: '0.5rem',
                    fontSize: '0.9rem',
                    lineHeight: 1.4
                  }}>
                    {video.description}
                  </p>
                  <p style={{ 
                    color: 'var(--text-muted)', 
                    fontSize: '0.85rem',
                    marginBottom: '1rem'
                  }}>
                    {video.views} views
                  </p>
                  <Link 
                    to="/videos"
                    style={{
                      display: 'inline-flex',
                      alignItems: 'center',
                      gap: '0.5rem',
                      color: 'var(--accent-color)',
                      textDecoration: 'none',
                      fontSize: '0.9rem',
                      fontWeight: '500',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.target.style.color = '#ffffff';
                      e.target.style.transform = 'translateX(5px)';
                    }}
                    onMouseLeave={(e) => {
                      e.target.style.color = 'var(--accent-color)';
                      e.target.style.transform = 'translateX(0)';
                    }}
                  >
                    Watch Now →
                  </Link>
                </div>
              </motion.div>
            ))}
          </div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.4 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginTop: '3rem' }}
          >
            <Link 
              to="/videos"
              className="btn btn-primary"
              style={{
                padding: '1rem 2rem',
                background: 'var(--accent-color)',
                border: 'none',
                borderRadius: '50px',
                color: 'white',
                textDecoration: 'none',
                fontSize: '1rem',
                fontWeight: '500',
                transition: 'all 0.3s ease',
                display: 'inline-block',
              }}
              onMouseEnter={(e) => {
                e.target.style.background = '#ffffff';
                e.target.style.color = 'var(--accent-color)';
                e.target.style.transform = 'translateY(-2px)';
              }}
              onMouseLeave={(e) => {
                e.target.style.background = 'var(--accent-color)';
                e.target.style.color = 'white';
                e.target.style.transform = 'translateY(0)';
              }}
            >
              View All Videos
            </Link>
          </motion.div>
        </div>
      </section>

      {/* Upcoming Events Preview */}
      <section className="events-preview" style={{
        padding: '5rem 0',
        background: 'var(--secondary-color)',
        position: 'relative',
        overflow: 'hidden',
      }}>
        {/* Video Background */}
        <video
          autoPlay
          loop
          muted
          playsInline
          style={{
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            zIndex: 1,
            opacity: 0.3,
          }}
        >
          <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4" />
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4" type="video/mp4" />
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
        
        {/* Overlay for better text readability */}
        <div style={{
          position: 'absolute',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: 'linear-gradient(135deg, rgba(26, 26, 26, 0.7) 0%, rgba(42, 42, 42, 0.8) 100%)',
          zIndex: 2,
        }} />
        <div className="container" style={{ position: 'relative', zIndex: 3 }}>
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '3rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
              Upcoming Events
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Join me live at these upcoming performances
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
            gap: '2rem',
          }}>
            {[1, 2, 3].map((event) => (
              <motion.div
                key={event}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: event * 0.1 }}
                viewport={{ once: true }}
                className="card"
              >
                <h4 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                  Venue Name {event}
                </h4>
                <p style={{ color: 'var(--accent-color)', marginBottom: '0.5rem' }}>
                  {new Date(Date.now() + event * 7 * 24 * 60 * 60 * 1000).toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                  })}
                </p>
                <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem' }}>
                  City, Country
                </p>
                <Link to="/tour" className="btn btn-secondary" style={{ width: '100%' }}>
                  Get Tickets
                </Link>
              </motion.div>
            ))}
          </div>

          <div style={{ textAlign: 'center', marginTop: '2rem' }}>
            <Link to="/tour" className="btn btn-primary">
              View All Events
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
