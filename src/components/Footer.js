import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import ButtonStyles from '../styles/ButtonStyles';

const Footer = () => {
  const socialLinks = [
    { name: 'Facebook', url: '#', icon: 'f' },
    { name: 'Twitter', url: '#', icon: '𝕏' },
    { name: 'Instagram', url: '#', icon: '📷' },
    { name: 'YouTube', url: '#', icon: '▶' },
    { name: 'Spotify', url: '#', icon: '♫' },
  ];

  return (
    <>
      <style>
        {`
          ${ButtonStyles}
          
          .footer-container {
            background: var(--secondary-color);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
          }

          .footer-grid {
            display: grid;
            gap: 2rem;
            margin-bottom: 2rem;
          }

          /* Mobile: 1 column */
          @media (max-width: 575px) {
            .footer-grid {
              grid-template-columns: 1fr;
            }
            
            .artist-info,
            .newsletter-section {
              grid-column: 1;
            }
            
            .subscribe-form {
              flex-direction: column;
            }
            
            .email-input {
              border-radius: 50px !important;
              margin-bottom: 0.5rem;
            }
            
            .subscribe-btn {
              border-radius: 50px !important;
              width: 100%;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Small: 2 columns */
          @media (min-width: 576px) and (max-width: 767px) {
            .footer-grid {
              grid-template-columns: 1fr 1fr;
            }
            
            .artist-info,
            .newsletter-section {
              grid-column: span 2;
            }
            
            .subscribe-form {
              flex-direction: row;
            }
            
            .email-input {
              border-radius: 50px 0 0 50px !important;
              margin-bottom: 0;
            }
            
            .subscribe-btn {
              border-radius: 0 50px 50px 0 !important;
              width: auto;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Medium: 3 columns */
          @media (min-width: 768px) and (max-width: 991px) {
            .footer-grid {
              grid-template-columns: 2fr 1fr 1fr;
            }
            
            .artist-info {
              grid-column: span 1;
            }
            
            .newsletter-section {
              grid-column: span 1;
            }
            
            .subscribe-form {
              flex-direction: column;
            }
            
            .email-input {
              border-radius: 50px !important;
              margin-bottom: 0.5rem;
            }
            
            .subscribe-btn {
              border-radius: 50px !important;
              width: 100%;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Large: 4 columns */
          @media (min-width: 992px) {
            .footer-grid {
              grid-template-columns: 2fr 1fr 1fr 1fr;
            }
            
            .artist-info {
              grid-column: span 1;
            }
            
            .newsletter-section {
              grid-column: span 1;
            }
            
            .subscribe-form {
              flex-direction: row;
            }
            
            .email-input {
              border-radius: 50px 0 0 50px !important;
              margin-bottom: 0;
              min-width: 200px;
            }
            
            .subscribe-btn {
              border-radius: 0 50px 50px 0 !important;
              width: auto;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          .artist-info h3 {
            font-family: 'Playfair Display', serif;
            color: var(--text-primary);
            margin-bottom: 1rem;
          }

          .artist-info p {
            color: var(--text-secondary);
            line-height: 1.6;
          }

          .section-title {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
          }

          .quick-links {
            list-style: none;
            padding: 0;
          }

          .quick-links li {
            margin-bottom: 0.5rem;
          }

          .quick-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
          }

          .quick-links a:hover {
            color: var(--accent-color);
          }

          .social-grid {
            display: grid;
            gap: 0.5rem;
          }

          .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
          }

          .social-link:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
          }

          .social-link span {
            font-size: 1.2rem;
          }

          .newsletter-section h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
          }

          .newsletter-section p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
          }

          .subscribe-form {
            display: flex;
            gap: 0.5rem;
          }

          .email-input {
            flex: 1;
            padding: 0.8rem 1rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease;
          }

          .email-input:focus {
            border-color: var(--accent-color);
          }

          .subscribe-btn {
            padding: 0.8rem 1.5rem;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
          }

          .copyright {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
          }

          .copyright p {
            margin: 0;
          }

          /* Latest Release Section */
          .latest-release-section {
            grid-column: span 1;
          }

          .release-card {
            background: linear-gradient(135deg, var(--card-bg), rgba(102, 126, 234, 0.05));
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
          }

          .release-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-color), #764ba2, var(--accent-color));
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
          }

          @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
          }

          .release-cover {
            position: relative;
            flex-shrink: 0;
          }

          .release-cover-placeholder {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-color), #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
          }

          .release-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, #ff6b6b, #ff8e53);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
            animation: pulse 2s ease-in-out infinite;
          }

          @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
          }

          .release-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
          }

          .release-title {
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            font-family: 'Playfair Display', serif;
          }

          .release-artist {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin: 0 0 0.5rem 0;
          }

          .release-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
          }

          .release-year,
          .release-tracks {
            color: var(--text-muted);
            font-size: 0.8rem;
          }

          .release-separator {
            color: var(--border-color);
            font-size: 0.8rem;
          }

          .release-actions {
            display: flex;
            gap: 0.5rem;
          }

          .release-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
          }

          .release-btn.primary {
            background: linear-gradient(135deg, var(--accent-color), #764ba2);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
          }

          .release-btn.primary:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transform: translateY(-1px);
          }

          .release-btn.secondary {
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
          }

          .release-btn.secondary:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
          }

          /* Responsive adjustments for Latest Release */
          @media (max-width: 575px) {
            .release-card {
              flex-direction: column;
              text-align: center;
            }

            .release-cover-placeholder {
              width: 80px;
              height: 80px;
              margin: 0 auto;
            }

            .release-actions {
              flex-direction: column;
            }

            .release-btn {
              width: 100%;
            }
          }

          @media (min-width: 576px) and (max-width: 767px) {
            .latest-release-section {
              grid-column: span 2;
            }
          }
        `}
      </style>

      <motion.footer
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.6 }}
        className="footer-container"
      >
        <div className="container">
          {/* Main Footer Content */}
          <div className="footer-grid">
            {/* Artist Info */}
            <div className="artist-info">
              <h3>Artist Name</h3>
              <p>
                Professional singer and performer, bringing soulful music to audiences worldwide.
              </p>
            </div>

            {/* Quick Links */}
            <div>
              <h4 className="section-title">Quick Links</h4>
              <ul className="quick-links">
                {['Home', 'About', 'Music', 'Videos', 'Gallery', 'Tour', 'Contact'].map((item) => (
                  <li key={item}>
                    <Link
                      to={`/${item.toLowerCase() === 'home' ? '' : item.toLowerCase()}`}
                    >
                      {item}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            {/* Latest Release */}
            <div className="latest-release-section">
              <h4 className="section-title">Latest Release</h4>
              <motion.div 
                className="release-card"
                whileHover={{ 
                  scale: 1.02,
                  boxShadow: '0 10px 30px rgba(102, 126, 234, 0.3)'
                }}
                transition={{ type: 'spring', stiffness: 300 }}
              >
                <div className="release-cover">
                  <div className="release-cover-placeholder">
                    <span className="release-icon">🎵</span>
                  </div>
                  <div className="release-badge">NEW</div>
                </div>
                <div className="release-info">
                  <h5 className="release-title">Midnight Dreams</h5>
                  <p className="release-artist">Artist Name</p>
                  <div className="release-meta">
                    <span className="release-year">2024</span>
                    <span className="release-separator">•</span>
                    <span className="release-tracks">12 Tracks</span>
                  </div>
                  <div className="release-actions">
                    <motion.button 
                      className="release-btn primary"
                      whileHover={{ scale: 1.05 }}
                      whileTap={{ scale: 0.95 }}
                    >
                      Listen Now
                    </motion.button>
                    <motion.button 
                      className="release-btn secondary"
                      whileHover={{ scale: 1.05 }}
                      whileTap={{ scale: 0.95 }}
                    >
                      Add to Playlist
                    </motion.button>
                  </div>
                </div>
              </motion.div>
            </div>

            {/* Social Media */}
            <div>
              <h4 className="section-title">Follow Me</h4>
              <div className="social-grid">
                {socialLinks.map((social) => (
                  <motion.a
                    key={social.name}
                    href={social.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.95 }}
                    className="social-link"
                  >
                    <span>{social.icon}</span>
                  </motion.a>
                ))}
              </div>
            </div>

            {/* Newsletter */}
            <div className="newsletter-section">
              <h4>Stay Updated</h4>
              <p>
                Subscribe to get the latest updates on new music and events.
              </p>
              <form onSubmit={(e) => e.preventDefault()} className="subscribe-form">
                <input
                  type="email"
                  placeholder="Your email"
                  className="email-input"
                  required
                />
                <button type="submit" className="btn btn-primary btn-gradient-text">
                  Subscribe
                </button>
              </form>
            </div>
          </div>

          {/* Copyright */}
          <div className="copyright">
            <p>&copy; {new Date().getFullYear()} Artist Name. All rights reserved.</p>
          </div>
        </div>
      </motion.footer>
    </>
  );
};

export default Footer;
