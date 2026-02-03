import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const Preloader = () => {
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Show preloader for 3.5 seconds on first visit
    const timer = setTimeout(() => {
      setIsLoading(false);
    }, 3500);

    // Check if user has already visited (to only show on first visit)
    const hasVisited = sessionStorage.getItem('hasVisited');
    if (hasVisited) {
      setIsLoading(false);
    } else {
      sessionStorage.setItem('hasVisited', 'true');
    }

    return () => clearTimeout(timer);
  }, []);

  return (
    <AnimatePresence>
      {isLoading && (
        <motion.div
          initial={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.5 }}
          style={{
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100vw',
            height: '100vh',
            background: 'linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%)',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 9999,
            overflow: 'hidden',
          }}
        >
          {/* Animated Background Particles */}
          <div style={{
            position: 'absolute',
            width: '100%',
            height: '100%',
            overflow: 'hidden',
          }}>
            {[...Array(20)].map((_, i) => (
              <motion.div
                key={i}
                initial={{
                  x: Math.random() * 100 - 50,
                  y: Math.random() * 100 - 50,
                  scale: 0,
                }}
                animate={{
                  x: Math.random() * 200 - 100,
                  y: Math.random() * 200 - 100,
                  scale: [0, 1, 0],
                }}
                transition={{
                  duration: 2 + Math.random() * 2,
                  repeat: Infinity,
                  delay: Math.random() * 2,
                }}
                style={{
                  position: 'absolute',
                  width: 4,
                  height: 4,
                  background: 'linear-gradient(45deg, #667eea, #764ba2)',
                  borderRadius: '50%',
                  left: `${Math.random() * 100}%`,
                  top: `${Math.random() * 100}%`,
                }}
              />
            ))}
          </div>

          {/* Logo Animation */}
          <motion.div
            initial={{ scale: 0, rotate: -180 }}
            animate={{ scale: 1, rotate: 0 }}
            transition={{
              duration: 1,
              ease: "easeInOut",
            }}
            style={{
              width: '80px',
              height: '80px',
              background: 'linear-gradient(135deg, #667eea, #764ba2)',
              borderRadius: '20px',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              fontSize: '2.5rem',
              color: 'white',
              marginBottom: '2rem',
              boxShadow: '0 10px 40px rgba(102, 126, 234, 0.3)',
              position: 'relative',
              zIndex: 2,
            }}
          >
            🎵
          </motion.div>

          {/* Artist Name */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{
              duration: 0.8,
              delay: 0.5,
            }}
            style={{
              fontFamily: "'Playfair Display', serif",
              fontSize: '2.5rem',
              fontWeight: '700',
              background: 'linear-gradient(135deg, #ffffff, #e0e0e0)',
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
              backgroundClip: 'text',
              marginBottom: '1rem',
              position: 'relative',
              zIndex: 2,
            }}
          >
            Artist Name
          </motion.div>

          {/* Loading Text */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{
              duration: 0.8,
              delay: 1,
            }}
            style={{
              fontSize: '1.1rem',
              color: 'rgba(255, 255, 255, 0.7)',
              marginBottom: '2rem',
              position: 'relative',
              zIndex: 2,
            }}
          >
            Loading amazing music experience...
          </motion.div>

          {/* Progress Bar */}
          <motion.div
            initial={{ width: 0 }}
            animate={{ width: '300px' }}
            transition={{
              duration: 3,
              ease: "easeInOut",
            }}
            style={{
              width: '300px',
              height: '4px',
              background: 'rgba(255, 255, 255, 0.1)',
              borderRadius: '2px',
              position: 'relative',
              zIndex: 2,
              overflow: 'hidden',
            }}
          >
            <motion.div
              initial={{ width: 0 }}
              animate={{ width: '100%' }}
              transition={{
                duration: 3,
                ease: "easeInOut",
              }}
              style={{
                height: '100%',
                background: 'linear-gradient(90deg, #667eea, #764ba2)',
                borderRadius: '2px',
                boxShadow: '0 0 10px rgba(102, 126, 234, 0.5)',
              }}
            />
          </motion.div>

          {/* Loading Dots */}
          <motion.div
            style={{
              display: 'flex',
              gap: '0.5rem',
              marginTop: '2rem',
              position: 'relative',
              zIndex: 2,
            }}
          >
            {[...Array(3)].map((_, i) => (
              <motion.div
                key={i}
                animate={{
                  scale: [1, 1.5, 1],
                  opacity: [0.5, 1, 0.5],
                }}
                transition={{
                  duration: 1.5,
                  repeat: Infinity,
                  delay: i * 0.2,
                }}
                style={{
                  width: '8px',
                  height: '8px',
                  background: 'linear-gradient(135deg, #667eea, #764ba2)',
                  borderRadius: '50%',
                }}
              />
            ))}
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
};

export default Preloader;
