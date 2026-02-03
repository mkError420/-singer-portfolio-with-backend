import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { galleryAPI } from '../services/api';

const Gallery = () => {
  const [selectedImage, setSelectedImage] = useState(null);
  const [filter, setFilter] = useState('all');
  const [selectedMonth, setSelectedMonth] = useState('all');
  const [selectedYear, setSelectedYear] = useState('all');
  const [galleryImages, setGalleryImages] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchGalleryImages();
  }, []);

  const fetchGalleryImages = async () => {
    try {
      setLoading(true);
      console.log('🔄 Fetching gallery images...');
      
      const data = await galleryAPI.getAllImages();
      console.log('🖼️ Gallery data from API:', data);
      console.log('🖼️ Data type:', typeof data);
      console.log('🖼️ Data length:', data?.length);
      
      if (!Array.isArray(data)) {
        console.error('❌ Gallery API did not return an array:', data);
        setGalleryImages([]);
        return;
      }
      
      // Fix image URLs to include full backend path
      const imagesWithFixedUrls = data.map((image, index) => {
        console.log(`🖼️ Processing image ${index}:`, image);
        
        if (image.image) {
          const originalUrl = image.image;
          image.image = `http://localhost/madam-portfolio/backend/${image.image}`;
          console.log('🖼️ Fixed gallery image URL:', image.title, 'from', originalUrl, 'to', image.image);
        } else {
          console.warn('⚠️ Image has no image property:', image);
        }
        return image;
      });
      
      console.log('🖼️ Final processed images:', imagesWithFixedUrls);
      setGalleryImages(imagesWithFixedUrls);
      
    } catch (error) {
      console.error('❌ Error fetching gallery images:', error);
      console.error('❌ Error details:', error.response?.data || error.message);
      setGalleryImages([]);
    } finally {
      setLoading(false);
    }
  };

  const categories = [
    { id: 'all', label: 'All Photos' },
    { id: 'performance', label: 'Performances' },
    { id: 'studio', label: 'Studio' },
    { id: 'behind', label: 'Behind Scenes' }
  ];

  // Count images in each category
  const categoryCounts = categories.reduce((counts, category) => {
    if (category.id === 'all') {
      counts[category.id] = galleryImages.length;
    } else {
      counts[category.id] = galleryImages.filter(img => img.category === category.id).length;
    }
    return counts;
  }, {});

  // Get available months from gallery data
  const getAvailableMonths = () => {
    const months = new Set();
    galleryImages.forEach(image => {
      const month = image.upload_month || (image.created_at ? String(new Date(image.created_at).getMonth() + 1).padStart(2, '0') : null);
      if (month) months.add(month);
    });
    return Array.from(months).sort((a, b) => b.localeCompare(a)); // Newest first
  };

  // Get available years from gallery data
  const getAvailableYears = () => {
    const years = new Set();
    galleryImages.forEach(image => {
      const year = image.upload_year || (image.created_at ? new Date(image.created_at).getFullYear() : null);
      if (year) years.add(year.toString());
    });
    return Array.from(years).sort((a, b) => b.localeCompare(a)); // Newest first
  };

  // Get month name from month number
  const getMonthName = (month) => {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];
    return months[parseInt(month) - 1] || 'Unknown';
  };
  const groupImagesByMonthYear = (images) => {
    const grouped = {};
    
    images.forEach(image => {
      // Use upload_month and upload_year if available, otherwise fall back to created_at
      const year = image.upload_year || (image.created_at ? new Date(image.created_at).getFullYear() : new Date().getFullYear());
      const month = image.upload_month || (image.created_at ? String(new Date(image.created_at).getMonth() + 1).padStart(2, '0') : String(new Date().getMonth() + 1).padStart(2, '0'));
      
      const monthYear = `${year}-${month}`;
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                        'July', 'August', 'September', 'October', 'November', 'December'];
      const monthName = monthNames[parseInt(month) - 1];
      
      if (!grouped[monthYear]) {
        grouped[monthYear] = {
          year: parseInt(year),
          month: month,
          monthName: monthName,
          images: []
        };
      }
      
      grouped[monthYear].images.push(image);
    });
    
    // Sort by year and month (newest first)
    return Object.keys(grouped)
      .sort((a, b) => b.localeCompare(a))
      .map(key => grouped[key]);
  };

  const filteredImages = galleryImages.filter(img => {
    // Category filter
    const categoryMatch = filter === 'all' || img.category === filter;
    
    // Month filter
    const imgMonth = img.upload_month || (img.created_at ? String(new Date(img.created_at).getMonth() + 1).padStart(2, '0') : null);
    const monthMatch = selectedMonth === 'all' || imgMonth === selectedMonth;
    
    // Year filter
    const imgYear = img.upload_year || (img.created_at ? new Date(img.created_at).getFullYear() : null);
    const yearMatch = selectedYear === 'all' || imgYear.toString() === selectedYear;
    
    return categoryMatch && monthMatch && yearMatch;
  });
    
  // Get grouped images for display
  const groupedImages = groupImagesByMonthYear(filteredImages);

  const openLightbox = (image) => {
    setSelectedImage(image);
  };

  const closeLightbox = () => {
    setSelectedImage(null);
  };

  const navigateImage = (direction) => {
    const currentIndex = filteredImages.findIndex(img => img.id === selectedImage.id);
    let newIndex;
    
    if (direction === 'next') {
      newIndex = currentIndex < filteredImages.length - 1 ? currentIndex + 1 : 0;
    } else {
      newIndex = currentIndex > 0 ? currentIndex - 1 : filteredImages.length - 1;
    }
    
    setSelectedImage(filteredImages[newIndex]);
  };

  return (
    <div className="gallery">
      {/* Hero Section */}
      <section className="gallery-hero" style={{
        padding: '8rem 0 4rem',
        background: 'linear-gradient(135deg, rgba(26, 26, 26, 0.85) 0%, rgba(42, 42, 42, 0.9) 100%), url("https://images.unsplash.com/photo-1516280440614-37939bbacd81?w=1920&h=800&fit=crop&crop=entropy&auto=format") center/cover',
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
              Gallery
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto 2rem',
              lineHeight: 1.6,
            }}>
              Visual memories from performances, studio sessions, and behind the scenes
            </p>
            
            {/* Refresh Button */}
            <motion.button
              onClick={fetchGalleryImages}
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              style={{
                padding: '0.75rem 2rem',
                background: 'linear-gradient(45deg, #ff6b6b, #ee5a24)',
                border: 'none',
                borderRadius: '25px',
                color: 'white',
                fontSize: '1rem',
                fontWeight: '500',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
                margin: '0 auto',
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
              🔄 Refresh Gallery
            </motion.button>
          </motion.div>
        </div>
      </section>

      {/* Filter Buttons */}
      <section className="gallery-filters" style={{
        padding: '2rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            gap: '1rem',
            flexWrap: 'wrap',
          }}>
            {categories.map((category) => (
              <motion.button
                key={category.id}
                onClick={() => setFilter(category.id)}
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                style={{
                  fontSize: '0.9rem',
                  padding: '0.75rem 1.5rem',
                  borderRadius: '25px',
                  background: filter === category.id 
                    ? 'linear-gradient(45deg, #ff6b6b, #ee5a24)' 
                    : 'rgba(255, 255, 255, 0.1)',
                  border: filter === category.id 
                    ? '2px solid #ff6b6b' 
                    : '1px solid rgba(255, 255, 255, 0.2)',
                  color: filter === category.id ? '#ffffff' : 'var(--text-primary)',
                  fontWeight: filter === category.id ? '600' : '400',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                  boxShadow: filter === category.id 
                    ? '0 4px 15px rgba(255, 107, 107, 0.4)' 
                    : 'none',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '0.5rem',
                }}
                onMouseEnter={(e) => {
                  if (filter !== category.id) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                  }
                }}
                onMouseLeave={(e) => {
                  if (filter !== category.id) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                  }
                }}
              >
                <span>{category.label}</span>
                <span style={{
                  background: filter === category.id 
                    ? 'rgba(255, 255, 255, 0.2)' 
                    : 'rgba(255, 255, 255, 0.1)',
                  padding: '0.2rem 0.5rem',
                  borderRadius: '12px',
                  fontSize: '0.8rem',
                  minWidth: '20px',
                  textAlign: 'center',
                }}>
                  {categoryCounts[category.id] || 0}
                </span>
                {filter === category.id && (
                  <motion.span
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    style={{
                      marginLeft: '0.25rem',
                      fontSize: '0.8rem',
                    }}
                  >
                    ✓
                  </motion.span>
                )}
              </motion.button>
            ))}
          </div>
          
          {/* Month Filters */}
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            gap: '0.5rem',
            flexWrap: 'wrap',
            marginTop: '1.5rem',
          }}>
            <motion.button
              key="month-all"
              onClick={() => setSelectedMonth('all')}
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              style={{
                fontSize: '0.85rem',
                padding: '0.5rem 1rem',
                borderRadius: '20px',
                background: selectedMonth === 'all' 
                  ? 'linear-gradient(45deg, #ff6b6b, #ee5a24)' 
                  : 'rgba(255, 255, 255, 0.1)',
                border: selectedMonth === 'all' 
                  ? '2px solid #ff6b6b' 
                  : '1px solid rgba(255, 255, 255, 0.2)',
                color: selectedMonth === 'all' ? '#ffffff' : 'var(--text-primary)',
                fontWeight: selectedMonth === 'all' ? '600' : '400',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                boxShadow: selectedMonth === 'all' 
                  ? '0 4px 15px rgba(255, 107, 107, 0.4)' 
                  : 'none',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
              }}
              onMouseEnter={(e) => {
                if (selectedMonth !== 'all') {
                  e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                  e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                }
              }}
              onMouseLeave={(e) => {
                if (selectedMonth !== 'all') {
                  e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                  e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                }
              }}
            >
              <span>📅 All Months</span>
              {selectedMonth === 'all' && (
                <motion.span
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  style={{ fontSize: '0.8rem' }}
                >
                  ✓
                </motion.span>
              )}
            </motion.button>
            
            {getAvailableMonths().map((month) => (
              <motion.button
                key={`month-${month}`}
                onClick={() => setSelectedMonth(month)}
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                style={{
                  fontSize: '0.85rem',
                  padding: '0.5rem 1rem',
                  borderRadius: '20px',
                  background: selectedMonth === month 
                    ? 'linear-gradient(45deg, #ff6b6b, #ee5a24)' 
                    : 'rgba(255, 255, 255, 0.1)',
                  border: selectedMonth === month 
                    ? '2px solid #ff6b6b' 
                    : '1px solid rgba(255, 255, 255, 0.2)',
                  color: selectedMonth === month ? '#ffffff' : 'var(--text-primary)',
                  fontWeight: selectedMonth === month ? '600' : '400',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                  boxShadow: selectedMonth === month 
                    ? '0 4px 15px rgba(255, 107, 107, 0.4)' 
                    : 'none',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '0.5rem',
                }}
                onMouseEnter={(e) => {
                  if (selectedMonth !== month) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                  }
                }}
                onMouseLeave={(e) => {
                  if (selectedMonth !== month) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                  }
                }}
              >
                <span>{getMonthName(month)}</span>
                {selectedMonth === month && (
                  <motion.span
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    style={{ fontSize: '0.8rem' }}
                  >
                    ✓
                  </motion.span>
                )}
              </motion.button>
            ))}
          </div>
          
          {/* Year Filters */}
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            gap: '0.5rem',
            flexWrap: 'wrap',
            marginTop: '1rem',
          }}>
            <motion.button
              key="year-all"
              onClick={() => setSelectedYear('all')}
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              style={{
                fontSize: '0.85rem',
                padding: '0.5rem 1rem',
                borderRadius: '20px',
                background: selectedYear === 'all' 
                  ? 'linear-gradient(45deg, #9b59b6, #8e44ad)' 
                  : 'rgba(255, 255, 255, 0.1)',
                border: selectedYear === 'all' 
                  ? '2px solid #9b59b6' 
                  : '1px solid rgba(255, 255, 255, 0.2)',
                color: selectedYear === 'all' ? '#ffffff' : 'var(--text-primary)',
                fontWeight: selectedYear === 'all' ? '600' : '400',
                cursor: 'pointer',
                transition: 'all 0.3s ease',
                boxShadow: selectedYear === 'all' 
                  ? '0 4px 15px rgba(155, 89, 182, 0.4)' 
                  : 'none',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
              }}
              onMouseEnter={(e) => {
                if (selectedYear !== 'all') {
                  e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                  e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                }
              }}
              onMouseLeave={(e) => {
                if (selectedYear !== 'all') {
                  e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                  e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                }
              }}
            >
              <span>📆 All Years</span>
              {selectedYear === 'all' && (
                <motion.span
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  style={{ fontSize: '0.8rem' }}
                >
                  ✓
                </motion.span>
              )}
            </motion.button>
            
            {getAvailableYears().map((year) => (
              <motion.button
                key={`year-${year}`}
                onClick={() => setSelectedYear(year)}
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                style={{
                  fontSize: '0.85rem',
                  padding: '0.5rem 1rem',
                  borderRadius: '20px',
                  background: selectedYear === year 
                    ? 'linear-gradient(45deg, #9b59b6, #8e44ad)' 
                    : 'rgba(255, 255, 255, 0.1)',
                  border: selectedYear === year 
                    ? '2px solid #9b59b6' 
                    : '1px solid rgba(255, 255, 255, 0.2)',
                  color: selectedYear === year ? '#ffffff' : 'var(--text-primary)',
                  fontWeight: selectedYear === year ? '600' : '400',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                  boxShadow: selectedYear === year 
                    ? '0 4px 15px rgba(155, 89, 182, 0.4)' 
                    : 'none',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '0.5rem',
                }}
                onMouseEnter={(e) => {
                  if (selectedYear !== year) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                  }
                }}
                onMouseLeave={(e) => {
                  if (selectedYear !== year) {
                    e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                  }
                }}
              >
                <span>{year}</span>
                {selectedYear === year && (
                  <motion.span
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    style={{ fontSize: '0.8rem' }}
                  >
                    ✓
                  </motion.span>
                )}
              </motion.button>
            ))}
          </div>
        </div>
      </section>

      {/* Gallery Grid */}
      <section className="gallery-grid" style={{
        padding: '3rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          {loading ? (
            // Loading state
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
              gap: '1.5rem',
            }}>
              {Array.from({ length: 6 }).map((_, index) => (
                <div
                  key={`loading-${index}`}
                  className="card"
                  style={{
                    cursor: 'pointer',
                    overflow: 'hidden',
                    padding: 0,
                  }}
                >
                  <div style={{
                    width: '100%',
                    height: '250px',
                    background: 'linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%)',
                    backgroundSize: '200% 100%',
                    animation: 'shimmer 1.5s infinite',
                  }} />
                </div>
              ))}
            </div>
          ) : (
            <AnimatePresence>
              {groupedImages.length === 0 ? (
                <motion.div
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6 }}
                  style={{
                    textAlign: 'center',
                    padding: '4rem 2rem',
                  }}
                >
                  <div style={{ 
                    fontSize: '4rem', 
                    marginBottom: '1rem', 
                    opacity: 0.5 
                  }}>
                    🖼️
                  </div>
                  <h3 style={{ 
                    color: 'var(--text-primary)', 
                    marginBottom: '1rem',
                    fontSize: '1.5rem'
                  }}>
                    No images found
                  </h3>
                  <p style={{ 
                    color: 'var(--text-secondary)', 
                    fontSize: '1.1rem'
                  }}>
                    {filter === 'all' 
                      ? 'No images have been uploaded to the gallery yet' 
                      : `No images found in "${categories.find(c => c.id === filter)?.label}" category`}
                  </p>
                </motion.div>
              ) : (
                groupedImages.map((group, groupIndex) => (
                  <motion.div
                    key={`${group.year}-${group.month}`}
                    initial={{ opacity: 0, y: 30 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: groupIndex * 0.1 }}
                    style={{
                      marginBottom: '3rem',
                    }}
                  >
                    {/* Month/Year Header */}
                    <motion.div
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      transition={{ duration: 0.5, delay: groupIndex * 0.1 + 0.2 }}
                      style={{
                        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        color: 'white',
                        padding: '1.5rem',
                        borderRadius: '15px',
                        marginBottom: '1.5rem',
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        boxShadow: '0 4px 20px rgba(102, 126, 234, 0.3)',
                      }}
                    >
                      <div>
                        <h2 style={{
                          margin: '0',
                          fontSize: '1.8rem',
                          fontWeight: '600',
                        }}>
                          {group.monthName} {group.year}
                        </h2>
                        <p style={{
                          margin: '0.25rem 0 0 0',
                          opacity: 0.9,
                          fontSize: '0.9rem',
                        }}>
                          {group.images.length} image{group.images.length !== 1 ? 's' : ''}
                        </p>
                      </div>
                      <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '0.5rem',
                        background: 'rgba(255, 255, 255, 0.2)',
                        padding: '0.5rem 1rem',
                        borderRadius: '20px',
                      }}>
                        <span style={{ fontSize: '1.2rem' }}>📅</span>
                        <span style={{ fontSize: '0.9rem' }}>
                          {group.month}/{group.year}
                        </span>
                      </div>
                    </motion.div>

                    {/* Images Grid */}
                    <motion.div
                      layout
                      style={{
                        display: 'grid',
                        gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
                        gap: '1.5rem',
                      }}
                    >
                      {group.images.map((image, index) => (
                        <motion.div
                          key={image.id}
                          layout
                          initial={{ opacity: 0, scale: 0.8 }}
                          animate={{ opacity: 1, scale: 1 }}
                          exit={{ opacity: 0, scale: 0.8 }}
                          transition={{ duration: 0.5, delay: groupIndex * 0.1 + index * 0.05 }}
                          whileHover={{ scale: 1.03 }}
                          className="card"
                          style={{
                            cursor: 'pointer',
                            overflow: 'hidden',
                            padding: 0,
                          }}
                          onClick={() => openLightbox(image)}
                        >
                          <div style={{ position: 'relative', overflow: 'hidden' }}>
                            <img
                              src={image.image || ""}
                              alt={image.title}
                              onLoad={() => {
                                console.log('✅ Gallery image loaded successfully:', image.title);
                              }}
                              onError={(e) => {
                                console.error('❌ Failed to load gallery image:', image.title, image.image);
                                // Create a fallback div with gradient and text
                                e.target.style.display = 'none';
                                const parent = e.target.parentElement;
                                if (parent && !parent.querySelector('.gallery-fallback')) {
                                  const fallback = document.createElement('div');
                                  fallback.className = 'gallery-fallback';
                                  fallback.style.cssText = `
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 250px;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                    background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
                                    color: #ffffff;
                                    font-size: 2rem;
                                    font-weight: bold;
                                    text-align: center;
                                    padding: 1rem;
                                  `;
                                  fallback.innerHTML = `
                                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🖼️</div>
                                    <div style="font-size: 0.9rem; font-weight: 500; opacity: 0.8;">${image.title}</div>
                                  `;
                                  parent.appendChild(fallback);
                                }
                              }}
                              style={{
                                width: '100%',
                                height: '250px',
                                objectFit: 'cover',
                                transition: 'transform 0.3s ease',
                              }}
                            />
                            <div style={{
                              position: 'absolute',
                              inset: 0,
                              background: 'linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%)',
                              opacity: 0,
                              transition: 'opacity 0.3s ease',
                            }} />
                            <div style={{
                              position: 'absolute',
                              bottom: 0,
                              left: 0,
                              right: 0,
                              padding: '1.5rem',
                              transform: 'translateY(20px)',
                              transition: 'transform 0.3s ease',
                            }}>
                              <h3 style={{
                                color: 'var(--text-primary)',
                                margin: '0 0 0.5rem',
                                fontSize: '1.1rem',
                              }}>
                                {image.title}
                              </h3>
                              <p style={{
                                color: 'var(--text-secondary)',
                                margin: 0,
                                fontSize: '0.9rem',
                              }}>
                                {image.description}
                              </p>
                            </div>
                          </div>
                        </motion.div>
                      ))}
                    </motion.div>
                  </motion.div>
                ))
              )}
            </AnimatePresence>
          )}
        </div>
      </section>

      {/* Lightbox */}
      <AnimatePresence>
        {selectedImage && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={closeLightbox}
            style={{
              position: 'fixed',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              background: 'rgba(0, 0, 0, 0.95)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              zIndex: 2000,
              padding: '2rem',
            }}
          >
            <motion.div
              initial={{ scale: 0.8 }}
              animate={{ scale: 1 }}
              exit={{ scale: 0.8 }}
              onClick={(e) => e.stopPropagation()}
              style={{
                width: '100%',
                maxWidth: '900px',
                maxHeight: '90vh',
                display: 'flex',
                flexDirection: 'column',
              }}
            >
              {/* Image */}
              <div style={{
                position: 'relative',
                flex: 1,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}>
                <img
                  src={selectedImage.image || ""}
                  alt={selectedImage.title}
                  onLoad={() => {
                    console.log('✅ Lightbox image loaded successfully:', selectedImage.title);
                  }}
                  onError={(e) => {
                    console.error('❌ Failed to load lightbox image:', selectedImage.title, selectedImage.image);
                    e.target.src = `https://via.placeholder.com/800x600/2a2a2a/ffffff?text=${encodeURIComponent(selectedImage.title)}`;
                  }}
                  style={{
                    maxWidth: '100%',
                    maxHeight: '70vh',
                    objectFit: 'contain',
                    borderRadius: '10px',
                  }}
                />

                {/* Navigation Buttons */}
                <button
                  onClick={() => navigateImage('prev')}
                  style={{
                    position: 'absolute',
                    left: '20px',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '2rem',
                    width: '50px',
                    height: '50px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ‹
                </button>
                <button
                  onClick={() => navigateImage('next')}
                  style={{
                    position: 'absolute',
                    right: '20px',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '2rem',
                    width: '50px',
                    height: '50px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ›
                </button>

                {/* Close Button */}
                <button
                  onClick={closeLightbox}
                  style={{
                    position: 'absolute',
                    top: '20px',
                    right: '20px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '1.5rem',
                    width: '40px',
                    height: '40px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ×
                </button>
              </div>

              {/* Image Info */}
              <div style={{
                padding: '1.5rem',
                textAlign: 'center',
                background: 'rgba(26, 26, 26, 0.9)',
                borderRadius: '0 0 10px 10px',
                backdropFilter: 'blur(10px)',
              }}>
                <h3 style={{
                  color: 'var(--text-primary)',
                  margin: '0 0 0.5rem',
                  fontSize: '1.3rem',
                }}>
                  {selectedImage.title}
                </h3>
                <p style={{
                  color: 'var(--text-secondary)',
                  margin: 0,
                  fontSize: '1rem',
                }}>
                  {selectedImage.description}
                </p>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default Gallery;
