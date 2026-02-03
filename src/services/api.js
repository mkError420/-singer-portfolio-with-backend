import axios from 'axios';

// Create axios instance with base configuration
const api = axios.create({
  baseURL: '/madam-portfolio/backend/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    console.log(`API Request: ${config.method?.toUpperCase()} ${config.url}`);
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    console.error('API Error:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

// API service functions - using full URL for albums (temporary fix)
export const musicAPI = {
  getAlbums: () => {
    // Use full URL to bypass proxy issues
    return axios.get('http://localhost/madam-portfolio/backend/api/albums_fixed.php');
  },
  getSingles: () => api.get('/singles.php'),
  getAllMusic: async () => {
    const [albums, singles] = await Promise.all([
      axios.get('http://localhost/madam-portfolio/backend/api/albums_fixed.php'),
      api.get('/singles.php')
    ]);
    return { albums, singles };
  },
};

export const videosAPI = {
  getAllVideos: () => api.get('/videos'),
  getVideosByCategory: (category) => api.get(`/videos?category=${category}`),
};

export const galleryAPI = {
  getAllImages: () => api.get('/gallery'),
  getImagesByCategory: (category) => api.get(`/gallery?category=${category}`),
};

export const tourAPI = {
  getAllTourDates: () => api.get('/tour'),
  getTourDatesByStatus: (status) => api.get(`/tour?status=${status}`),
};

export const contactAPI = {
  submitForm: (formData) => api.post('/contact', formData),
};

export const uploadAPI = {
  uploadFile: (file) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
};

export default api;
