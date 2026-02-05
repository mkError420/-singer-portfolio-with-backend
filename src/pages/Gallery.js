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
  const [currentPage, setCurrentPage] = useState(1);
  const imagesPerPage = 12;

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

  // Reset page when filters change
  useEffect(() => {
    setCurrentPage(1);
  }, [filter, selectedMonth, selectedYear]);

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

  // Pagination calculations
  const indexOfLastImage = currentPage * imagesPerPage;
  const indexOfFirstImage = indexOfLastImage - imagesPerPage;
  const currentImages = filteredImages.slice(indexOfFirstImage, indexOfLastImage);
  const totalPages = Math.ceil(filteredImages.length / imagesPerPage);

  // Get grouped images for current page
  const currentGroupedImages = groupImagesByMonthYear(currentImages);

  // Pagination functions
  const paginate = (pageNumber) => setCurrentPage(pageNumber);
  const goToPreviousPage = () => setCurrentPage(prev => Math.max(prev - 1, 1));
  const goToNextPage = () => setCurrentPage(prev => Math.min(prev + 1, totalPages));

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

      {/* Filter Dropdowns */}
      <section className="gallery-filters" style={{
        padding: '3rem 0',
        background: 'linear-gradient(135deg, #55565cff 0%, #5a595affff 100%)',
        position: 'relative',
        overflow: 'hidden',
      }}>
        {/* Background Pattern */}
        <div style={{
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
          opacity: 0.3,
        }} />
        
        <div className="container" style={{ position: 'relative', zIndex: 1 }}>
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            style={{
              textAlign: 'center',
              marginBottom: '2rem',
            }}
          >
            <h2 style={{
              color: 'white',
              fontSize: '2.5rem',
              fontWeight: '700',
              margin: '0 0 0.5rem',
              textShadow: '0 2px 10px rgba(0, 0, 0, 0.3)',
            }}>
              Gallery Filters
            </h2>
            <p style={{
              color: 'rgba(255, 255, 255, 0.9)',
              fontSize: '1.1rem',
              margin: 0,
              fontWeight: '400',
            }}>
              Discover moments by category, month, or year
            </p>
          </motion.div>

          {/* Modern Grid Filter Design */}
          <div style={{
            maxWidth: '1200px',
            margin: '0 auto',
            padding: '0 2rem',
          }}>
            {/* Filter Grid Container */}
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              style={{
                background: 'rgba(255, 255, 255, 0.08)',
                backdropFilter: 'blur(20px)',
                borderRadius: '20px',
                border: '1px solid rgba(255, 255, 255, 0.12)',
                padding: '1.5rem',
                boxShadow: '0 20px 40px rgba(0, 0, 0, 0.15)',
              }}
            >
              {/* Filter Header */}
              <div style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                marginBottom: '2rem',
                paddingBottom: '1.5rem',
                borderBottom: '1px solid rgba(255, 255, 255, 0.1)',
              }}>
                <div style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: '1rem',
                }}>
                  <div style={{
                    width: '48px',
                    height: '48px',
                    borderRadius: '12px',
                    background: 'linear-gradient(135deg, #667eea, #764ba2)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.2rem',
                  }}>
                    🎯
                  </div>
                  <div>
                    <h2 style={{
                      color: 'white',
                      fontSize: '1.5rem',
                      fontWeight: '700',
                      margin: '0 0 0.25rem',
                      textShadow: '0 2px 10px rgba(0, 0, 0, 0.3)',
                    }}>
                      Gallery Filters
                    </h2>
                    <p style={{
                      color: 'rgba(255, 255, 255, 0.8)',
                      fontSize: '0.95rem',
                      margin: '0',
                      fontWeight: '400',
                    }}>
                      Filter by category, month, or year
                    </p>
                  </div>
                </div>
                
                {/* Active Filters Display */}
                {(filter !== 'all' || selectedMonth !== 'all' || selectedYear !== 'all') && (
                  <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    background: 'rgba(255, 255, 255, 0.1)',
                    padding: '0.5rem 1rem',
                    borderRadius: '20px',
                    fontSize: '0.85rem',
                    color: 'rgba(255, 255, 255, 0.9)',
                  }}>
                    <span style={{ opacity: '0.7' }}>Active:</span>
                    {filter !== 'all' && (
                      <span style={{
                        background: 'linear-gradient(135deg, #686363ff, #554038ff)',
                        padding: '0.25rem 0.75rem',
                        borderRadius: '12px',
                        marginLeft: '0.5rem',
                        fontSize: '0.8rem',
                        fontWeight: '600',
                      }}>
                        {categories.find(c => c.id === filter)?.label}
                      </span>
                    )}
                    {selectedMonth !== 'all' && (
                      <span style={{
                        background: 'linear-gradient(135deg, #f093fb, #f5576c)',
                        padding: '0.25rem 0.75rem',
                        borderRadius: '12px',
                        marginLeft: '0.5rem',
                        fontSize: '0.8rem',
                        fontWeight: '600',
                      }}>
                        {getMonthName(selectedMonth)}
                      </span>
                    )}
                    {selectedYear !== 'all' && (
                      <span style={{
                        background: 'linear-gradient(135deg, #4facfe, #00f2fe)',
                        padding: '0.25rem 0.75rem',
                        borderRadius: '12px',
                        marginLeft: '0.5rem',
                        fontSize: '0.8rem',
                        fontWeight: '600',
                      }}>
                        {selectedYear}
                      </span>
                    )}
                  </div>
                )}
              </div>

              {/* Filter Grid */}
              <div style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
                gap: '1rem',
              }}>
                {/* Categories Section */}
                <motion.div
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: 0.1 }}
                  style={{
                    background: 'rgba(255, 255, 255, 0.05)',
                    borderRadius: '12px',
                    padding: '1rem',
                    border: '1px solid rgba(255, 255, 255, 0.08)',
                  }}
                >
                  <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    marginBottom: '0.75rem',
                  }}>
                    <div style={{
                      width: '32px',
                      height: '32px',
                      borderRadius: '8px',
                      background: 'linear-gradient(135deg, #ff6b6b, #ee5a24)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '0.9rem',
                    }}>
                      🎨
                    </div>
                    <h3 style={{
                      color: 'white',
                      fontSize: '0.95rem',
                      fontWeight: '600',
                      margin: '0',
                    }}>
                      Categories
                    </h3>
                  </div>
                  
                  <select
                    value={filter}
                    onChange={(e) => setFilter(e.target.value)}
                    style={{
                      width: '100%',
                      padding: '0.6rem',
                      borderRadius: '8px',
                      background: 'rgba(255, 255, 255, 0.1)',
                      border: '1px solid rgba(255, 255, 255, 0.2)',
                      color: 'white',
                      fontSize: '0.85rem',
                      cursor: 'pointer',
                      outline: 'none',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    }}
                  >
                    <option value="all" style={{ background: '#333' }}>All Categories</option>
                    {categories.map((category) => (
                      <option key={category.id} value={category.id} style={{ background: '#333' }}>
                        {category.label} ({categoryCounts[category.id] || 0})
                      </option>
                    ))}
                  </select>
                </motion.div>

                {/* Months Section */}
                <motion.div
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: 0.2 }}
                  style={{
                    background: 'rgba(255, 255, 255, 0.05)',
                    borderRadius: '12px',
                    padding: '1rem',
                    border: '1px solid rgba(255, 255, 255, 0.08)',
                  }}
                >
                  <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    marginBottom: '0.75rem',
                  }}>
                    <div style={{
                      width: '32px',
                      height: '32px',
                      borderRadius: '8px',
                      background: 'linear-gradient(135deg, #f093fb, #f5576c)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '0.9rem',
                    }}>
                      📅
                    </div>
                    <h3 style={{
                      color: 'white',
                      fontSize: '0.95rem',
                      fontWeight: '600',
                      margin: '0',
                    }}>
                      Months
                    </h3>
                  </div>
                  
                  <select
                    value={selectedMonth}
                    onChange={(e) => setSelectedMonth(e.target.value)}
                    style={{
                      width: '100%',
                      padding: '0.6rem',
                      borderRadius: '8px',
                      background: 'rgba(255, 255, 255, 0.1)',
                      border: '1px solid rgba(255, 255, 255, 0.2)',
                      color: 'white',
                      fontSize: '0.85rem',
                      cursor: 'pointer',
                      outline: 'none',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    }}
                  >
                    <option value="all" style={{ background: '#333' }}>All Months</option>
                    {getAvailableMonths().map((month) => (
                      <option key={`month-${month}`} value={month} style={{ background: '#333' }}>
                        {getMonthName(month)}
                      </option>
                    ))}
                  </select>
                </motion.div>

                {/* Years Section */}
                <motion.div
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: 0.3 }}
                  style={{
                    background: 'rgba(255, 255, 255, 0.05)',
                    borderRadius: '12px',
                    padding: '1rem',
                    border: '1px solid rgba(255, 255, 255, 0.08)',
                  }}
                >
                  <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    marginBottom: '0.75rem',
                  }}>
                    <div style={{
                      width: '32px',
                      height: '32px',
                      borderRadius: '8px',
                      background: 'linear-gradient(135deg, #4facfe, #00f2fe)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '0.9rem',
                    }}>
                      📆
                    </div>
                    <h3 style={{
                      color: 'white',
                      fontSize: '0.95rem',
                      fontWeight: '600',
                      margin: '0',
                    }}>
                      Years
                    </h3>
                  </div>
                  
                  <select
                    value={selectedYear}
                    onChange={(e) => setSelectedYear(e.target.value)}
                    style={{
                      width: '100%',
                      padding: '0.6rem',
                      borderRadius: '8px',
                      background: 'rgba(255, 255, 255, 0.1)',
                      border: '1px solid rgba(255, 255, 255, 0.2)',
                      color: 'white',
                      fontSize: '0.85rem',
                      cursor: 'pointer',
                      outline: 'none',
                      transition: 'all 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.15)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                      e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    }}
                  >
                    <option value="all" style={{ background: '#333' }}>All Years</option>
                    {getAvailableYears().map((year) => (
                      <option key={`year-${year}`} value={year} style={{ background: '#333' }}>
                        {year}
                      </option>
                    ))}
                  </select>
                </motion.div>
              </div>

              {/* Clear Filters Button */}
              {(filter !== 'all' || selectedMonth !== 'all' || selectedYear !== 'all') && (
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: 0.4 }}
                  style={{ textAlign: 'center', marginTop: '2rem' }}
                >
                  <motion.button
                    onClick={() => {
                      setFilter('all');
                      setSelectedMonth('all');
                      setSelectedYear('all');
                    }}
                    whileHover={{ scale: 1.05, y: -2 }}
                    whileTap={{ scale: 0.95 }}
                    style={{
                      fontSize: '0.95rem',
                      padding: '1rem 2rem',
                      borderRadius: '16px',
                      background: 'linear-gradient(135deg, #ff6b6b, #ee5a24)',
                      border: 'none',
                      color: 'white',
                      fontWeight: '600',
                      cursor: 'pointer',
                      transition: 'all 0.3s ease',
                      boxShadow: '0 8px 25px rgba(255, 107, 107, 0.4)',
                      backdropFilter: 'blur(10px)',
                      position: 'relative',
                      overflow: 'hidden',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.transform = 'translateY(-2px)';
                      e.currentTarget.style.boxShadow = '0 12px 35px rgba(255, 107, 107, 0.5)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.transform = 'translateY(0)';
                      e.currentTarget.style.boxShadow = '0 8px 25px rgba(255, 107, 107, 0.4)';
                    }}
                  >
                    <span style={{ position: 'relative', zIndex: 1 }}>
                      🔄 Clear All Filters
                    </span>
                    <motion.div
                      initial={{ scale: 0 }}
                      animate={{ scale: 1 }}
                      transition={{ delay: 0.2 }}
                      style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: 'linear-gradient(45deg, rgba(255, 255, 255, 0.1), transparent)',
                        borderRadius: '16px',
                      }}
                    />
                  </motion.button>
                </motion.div>
              )}
            </motion.div>
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
              {currentImages.length === 0 ? (
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
                    {filter === 'all' && selectedMonth === 'all' && selectedYear === 'all'
                      ? 'No images have been uploaded to the gallery yet' 
                      : 'No images found with the selected filters'}
                  </p>
                  {(filter !== 'all' || selectedMonth !== 'all' || selectedYear !== 'all') && (
                    <button
                      onClick={() => {
                        setFilter('all');
                        setSelectedMonth('all');
                        setSelectedYear('all');
                      }}
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
                        marginTop: '1.5rem',
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
                      Clear All Filters
                    </button>
                  )}
                </motion.div>
              ) : (
                <>
                  <motion.div
                    layout
                    style={{
                      display: 'grid',
                      gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
                      gap: '1.5rem',
                    }}
                  >
                    {currentImages.map((image, index) => {
                      // Get month and year for this image
                      const imgMonth = image.upload_month || (image.created_at ? String(new Date(image.created_at).getMonth() + 1).padStart(2, '0') : null);
                      const imgYear = image.upload_year || (image.created_at ? new Date(image.created_at).getFullYear() : null);
                      const monthName = imgMonth ? getMonthName(imgMonth) : '';
                      
                      return (
                        <motion.div
                          key={image.id}
                          layout
                          initial={{ opacity: 0, scale: 0.8 }}
                          animate={{ opacity: 1, scale: 1 }}
                          exit={{ opacity: 0, scale: 0.8 }}
                          transition={{ duration: 0.5, delay: index * 0.1 }}
                          whileHover={{ scale: 1.03 }}
                          className="card"
                          style={{
                            cursor: 'pointer',
                            overflow: 'hidden',
                            padding: 0,
                            position: 'relative',
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
                            
                            {/* Month/Year Badge on Top */}
                            {(imgMonth && imgYear) && (
                              <motion.div
                                initial={{ opacity: 0, y: -10 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.3, delay: index * 0.1 + 0.2 }}
                                style={{
                                  position: 'absolute',
                                  top: '10px',
                                  left: '10px',
                                  background: 'linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9))',
                                  color: 'white',
                                  padding: '0.3rem 0.6rem',
                                  borderRadius: '12px',
                                  fontSize: '0.75rem',
                                  fontWeight: '600',
                                  backdropFilter: 'blur(10px)',
                                  boxShadow: '0 2px 10px rgba(0, 0, 0, 0.3)',
                                  display: 'flex',
                                  alignItems: 'center',
                                  gap: '0.3rem',
                                  zIndex: 2,
                                }}
                              >
                                <span style={{ fontSize: '0.8rem' }}>📅</span>
                                <span>{monthName.slice(0, 3)} {imgYear}</span>
                              </motion.div>
                            )}
                            
                            {/* Category Badge */}
                            <motion.div
                              initial={{ opacity: 0, x: -10 }}
                              animate={{ opacity: 1, x: 0 }}
                              transition={{ duration: 0.3, delay: index * 0.1 + 0.3 }}
                              style={{
                                position: 'absolute',
                                top: '10px',
                                right: '10px',
                                background: 'linear-gradient(135deg, rgba(255, 107, 107, 0.9), rgba(238, 90, 36, 0.9))',
                                color: 'white',
                                padding: '0.3rem 0.6rem',
                                borderRadius: '12px',
                                fontSize: '0.7rem',
                                fontWeight: '600',
                                backdropFilter: 'blur(10px)',
                                boxShadow: '0 2px 10px rgba(0, 0, 0, 0.3)',
                                textTransform: 'capitalize',
                                zIndex: 2,
                              }}
                            >
                              {image.category}
                            </motion.div>
                            
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
                      );
                    })}
                  </motion.div>

                  {/* Pagination */}
                  {totalPages > 1 && (
                    <motion.div
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ duration: 0.6, delay: 0.4 }}
                      style={{
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        gap: '1rem',
                        marginTop: '3rem',
                        flexWrap: 'wrap',
                      }}
                    >
                      {/* Previous Button */}
                      <motion.button
                        onClick={goToPreviousPage}
                        disabled={currentPage === 1}
                        whileHover={{ scale: currentPage === 1 ? 1 : 1.05 }}
                        whileTap={{ scale: currentPage === 1 ? 1 : 0.95 }}
                        style={{
                          padding: '0.75rem 1rem',
                          borderRadius: '12px',
                          background: currentPage === 1 
                            ? 'rgba(255, 255, 255, 0.1)' 
                            : 'linear-gradient(45deg, #667eea, #764ba2)',
                          border: currentPage === 1 
                            ? '1px solid rgba(255, 255, 255, 0.2)' 
                            : 'none',
                          color: currentPage === 1 ? 'rgba(255, 255, 255, 0.5)' : 'white',
                          fontSize: '0.9rem',
                          fontWeight: '500',
                          cursor: currentPage === 1 ? 'not-allowed' : 'pointer',
                          transition: 'all 0.3s ease',
                          display: 'flex',
                          alignItems: 'center',
                          gap: '0.5rem',
                        }}
                      >
                        <span>←</span>
                        Previous
                      </motion.button>

                      {/* Page Numbers */}
                      {Array.from({ length: totalPages }, (_, i) => i + 1).map((pageNumber) => {
                        // Show first 3, last 3, and current with ellipsis
                        if (
                          pageNumber === 1 || 
                          pageNumber === totalPages || 
                          (pageNumber >= currentPage - 1 && pageNumber <= currentPage + 1) ||
                          (totalPages > 5 && (
                            (currentPage <= 3 && pageNumber <= 4) ||
                            (currentPage > totalPages - 3 && pageNumber >= totalPages - 3)
                          ))
                        ) {
                          return (
                            <motion.button
                              key={pageNumber}
                              onClick={() => paginate(pageNumber)}
                              whileHover={{ scale: 1.1 }}
                              whileTap={{ scale: 0.9 }}
                              style={{
                                width: '40px',
                                height: '40px',
                                borderRadius: '50%',
                                background: currentPage === pageNumber 
                                  ? 'linear-gradient(45deg, #ff6b6b, #ee5a24)' 
                                  : 'rgba(255, 255, 255, 0.1)',
                                border: currentPage === pageNumber 
                                  ? 'none' 
                                  : '1px solid rgba(255, 255, 255, 0.2)',
                                color: 'white',
                                fontSize: '0.9rem',
                                fontWeight: currentPage === pageNumber ? '600' : '400',
                                cursor: 'pointer',
                                transition: 'all 0.3s ease',
                              }}
                            >
                              {pageNumber}
                            </motion.button>
                          );
                        } else if (
                          (pageNumber === 2 && currentPage > 4) ||
                          (pageNumber === totalPages - 1 && currentPage < totalPages - 3)
                        ) {
                          return (
                            <span key={pageNumber} style={{ 
                              color: 'rgba(255, 255, 255, 0.5)', 
                              fontSize: '1.2rem',
                              fontWeight: 'bold'
                            }}>
                              ...
                            </span>
                          );
                        }
                        return null;
                      })}

                      {/* Next Button */}
                      <motion.button
                        onClick={goToNextPage}
                        disabled={currentPage === totalPages}
                        whileHover={{ scale: currentPage === totalPages ? 1 : 1.05 }}
                        whileTap={{ scale: currentPage === totalPages ? 1 : 0.95 }}
                        style={{
                          padding: '0.75rem 1rem',
                          borderRadius: '12px',
                          background: currentPage === totalPages 
                            ? 'rgba(255, 255, 255, 0.1)' 
                            : 'linear-gradient(45deg, #667eea, #764ba2)',
                          border: currentPage === totalPages 
                            ? '1px solid rgba(255, 255, 255, 0.2)' 
                            : 'none',
                          color: currentPage === totalPages ? 'rgba(255, 255, 255, 0.5)' : 'white',
                          fontSize: '0.9rem',
                          fontWeight: '500',
                          cursor: currentPage === totalPages ? 'not-allowed' : 'pointer',
                          transition: 'all 0.3s ease',
                          display: 'flex',
                          alignItems: 'center',
                          gap: '0.5rem',
                        }}
                      >
                        Next
                        <span>→</span>
                      </motion.button>
                    </motion.div>
                  )}

                  {/* Page Info */}
                  <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ duration: 0.6, delay: 0.5 }}
                    style={{
                      textAlign: 'center',
                      marginTop: '1rem',
                      color: 'rgba(255, 255, 255, 0.7)',
                      fontSize: '0.9rem',
                    }}
                  >
                    Showing {indexOfFirstImage + 1}-{Math.min(indexOfLastImage, filteredImages.length)} of {filteredImages.length} images
                  </motion.div>
                </>
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
