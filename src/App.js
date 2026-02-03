import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { motion } from 'framer-motion';
import './styles/global.css';

// Components
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import Preloader from './components/Preloader';

// Pages
import Home from './pages/Home';
import About from './pages/About';
import Music from './pages/Music';
import MusicDebug from './pages/MusicDebug';
import MusicFinalTest from './pages/MusicFinalTest';
import Videos from './pages/Videos';
import Gallery from './pages/Gallery';
import Tour from './pages/Tour';
import Contact from './pages/Contact';

function App() {
  return (
    <Router>
      <div className="App">
        <Preloader />
        <Navbar />
        <motion.main
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/about" element={<About />} />
            <Route path="/music" element={<Music />} />
            <Route path="/music-debug" element={<MusicDebug />} />
            <Route path="/music-final-test" element={<MusicFinalTest />} />
            <Route path="/videos" element={<Videos />} />
            <Route path="/gallery" element={<Gallery />} />
            <Route path="/tour" element={<Tour />} />
            <Route path="/contact" element={<Contact />} />
          </Routes>
        </motion.main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
