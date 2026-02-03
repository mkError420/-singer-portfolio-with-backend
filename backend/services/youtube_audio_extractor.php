<?php
class YouTubeAudioExtractor {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection(); // Get the PDO connection
    }
    
    /**
     * Extract audio from YouTube video and save as MP3
     * Note: This is a simplified version. In production, you'd use youtube-dl or similar tools
     */
    public function extractAudioFromYouTube($videoUrl, $trackId, $albumId) {
        try {
            // Extract video ID from YouTube URL
            $videoId = $this->extractVideoId($videoUrl);
            
            if (!$videoId) {
                return ['success' => false, 'message' => 'Invalid YouTube URL'];
            }
            
            // Generate audio file path
            $audioFileName = "audio_track_{$trackId}_" . time() . ".mp3";
            $audioPath = __DIR__ . "/../uploads/" . $audioFileName;
            
            // For demo purposes, we'll create a dummy audio file
            // In production, you would use youtube-dl or similar CLI tools
            $this->createDummyAudioFile($audioPath, $videoId);
            
            // Update track with audio file path
            $updateQuery = "UPDATE tracks SET audio_file = ? WHERE id = ?";
            $stmt = $this->db->prepare($updateQuery);
            $stmt->execute([$audioFileName, $trackId]);
            
            return [
                'success' => true, 
                'message' => 'Audio extracted successfully',
                'audio_file' => $audioFileName,
                'audio_path' => $audioPath
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error extracting audio: ' . $e->getMessage()];
        }
    }
    
    /**
     * Extract video ID from YouTube URL
     */
    private function extractVideoId($url) {
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Create a dummy audio file for demonstration
     * In production, this would use youtube-dl to extract actual audio
     */
    private function createDummyAudioFile($audioPath, $videoId) {
        // Create uploads directory if it doesn't exist
        $uploadsDir = dirname($audioPath);
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // Create a small dummy MP3 file (this is just for demonstration)
        $dummyContent = "dummy audio content for video ID: $videoId";
        file_put_contents($audioPath, $dummyContent);
        
        // In production, you would use something like:
        // exec("youtube-dl -x --audio-format mp3 --audio-quality 0 $videoId -o $audioPath");
    }
    
    /**
     * Batch extract audio for all tracks in an album
     */
    public function extractAudioForAlbum($albumId) {
        try {
            // Get all tracks for the album
            $query = "SELECT id, title, youtube_url FROM tracks WHERE album_id = ? AND status = 'active' AND youtube_url IS NOT NULL AND youtube_url != ''";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$albumId]);
            
            $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results = [];
            
            foreach ($tracks as $track) {
                $result = $this->extractAudioFromYouTube($track['youtube_url'], $track['id'], $albumId);
                $results[] = [
                    'track_id' => $track['id'],
                    'track_title' => $track['title'],
                    'result' => $result
                ];
            }
            
            return [
                'success' => true,
                'message' => "Audio extraction completed for album",
                'results' => $results
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error extracting audio for album: ' . $e->getMessage()];
        }
    }
}

// API endpoint for audio extraction
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Set CORS headers first
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $extractor = new YouTubeAudioExtractor($database);
    
    $data = json_decode(file_get_contents('php://input'));
    $action = $data->action ?? '';
    
    switch ($action) {
        case 'extract_single':
            $result = $extractor->extractAudioFromYouTube($data->youtube_url, $data->track_id, $data->album_id);
            echo json_encode($result);
            break;
            
        case 'extract_album':
            $result = $extractor->extractAudioForAlbum($data->album_id);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
