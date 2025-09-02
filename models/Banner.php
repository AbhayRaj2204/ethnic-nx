<?php
require_once __DIR__ . '/../config/database.php';

class Banner {
    private $db;
    private $filename = 'banners.csv';
    
    public function __construct() {
        $this->db = new CSVDatabase();
        $this->ensureBannersDirectory();
    }
    
    private function ensureBannersDirectory() {
        // Create directories for banner storage
        $directories = [
            __DIR__ . '/../assets/images/banners/',
            $_SERVER['DOCUMENT_ROOT'] . '/assets/images/banners/'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                error_log("Banner: Created directory - " . $dir);
            }
        }
    }
    
    public function getAll() {
        $banners = $this->db->read($this->filename);
        if (!is_array($banners)) {
            error_log("Banner: No banners found or invalid data from CSV");
            return [];
        }
        
        error_log("Banner: Found " . count($banners) . " total banners from CSV");
        
        // Sort by sort_order
        usort($banners, function($a, $b) {
            return intval($a['sort_order'] ?? 999) - intval($b['sort_order'] ?? 999);
        });
        
        return $banners;
    }

    public function getActive() {
        $banners = $this->getAll();
        $activeBanners = array_filter($banners, function($banner) {
            return isset($banner['status']) && $banner['status'] === 'active';
        });
        
        error_log("Banner: Total banners: " . count($banners));
        error_log("Banner: Active banners: " . count($activeBanners));
        
        // Validate image paths for active banners
        foreach ($activeBanners as $index => $banner) {
            $imageExists = $this->validateImagePath($banner['image']);
            if (!$imageExists['exists']) {
                error_log("Banner: WARNING - Active banner image not found: " . $banner['image']);
                error_log("Banner: Searched paths: " . implode(', ', $imageExists['searched_paths']));
            } else {
                error_log("Banner: Active banner image verified: " . $imageExists['path']);
            }
        }
        
        return array_values($activeBanners);
    }

    public function getById($id) {
        $banners = $this->getAll();
        foreach ($banners as $banner) {
            if ($banner['id'] == $id) {
                return $banner;
            }
        }
        return null;
    }
    
    public function create($data) {
        try {
            // Validate required data
            if (empty($data['image'])) {
                error_log("Banner: Create failed - Image is required");
                return ['success' => false, 'message' => 'Image is required'];
            }
            
            // Verify image file exists before creating banner record
            $imageValidation = $this->validateImagePath($data['image']);
            if (!$imageValidation['exists']) {
                error_log("Banner: Create failed - Image file not found: " . $data['image']);
                error_log("Banner: Searched paths: " . implode(', ', $imageValidation['searched_paths']));
                return ['success' => false, 'message' => 'Image file not found. Please re-upload the image.'];
            }
            
            $banners = $this->getAll();
            $newId = $this->db->generateId($this->filename);
            
            $newBanner = [
                'id' => $newId,
                'image' => trim($data['image']),
                'status' => $data['status'] ?? 'active',
                'sort_order' => intval($data['sort_order'] ?? 999),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $banners[] = $newBanner;
            
            error_log("Banner: Creating banner with validated image: " . $data['image']);
            error_log("Banner: Image verified at: " . $imageValidation['path']);
            
            if ($this->db->write($this->filename, $banners)) {
                error_log("Banner: Banner created successfully with ID: " . $newId);
                return ['success' => true, 'message' => 'Banner created successfully', 'data' => $newBanner];
            }
            
            error_log("Banner: Failed to write banner to CSV");
            return ['success' => false, 'message' => 'Failed to save banner to database'];
            
        } catch (Exception $e) {
            error_log("Banner: Exception in create(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating banner: ' . $e->getMessage()];
        }
    }
    
    public function update($id, $data) {
        try {
            if (empty($data['image'])) {
                error_log("Banner: Update failed - Image is required");
                return ['success' => false, 'message' => 'Image is required'];
            }
            
            // Verify image file exists
            $imageValidation = $this->validateImagePath($data['image']);
            if (!$imageValidation['exists']) {
                error_log("Banner: Update failed - Image file not found: " . $data['image']);
                return ['success' => false, 'message' => 'Image file not found. Please re-upload the image.'];
            }
            
            $banners = $this->getAll();
            $updated = false;
            
            foreach ($banners as &$banner) {
                if ($banner['id'] == $id) {
                    $banner['image'] = trim($data['image']);
                    $banner['status'] = $data['status'] ?? $banner['status'];
                    $banner['sort_order'] = intval($data['sort_order'] ?? $banner['sort_order']);
                    $banner['updated_at'] = date('Y-m-d H:i:s');
                    $updated = true;
                    
                    error_log("Banner: Updating banner ID: " . $id . " with validated image: " . $data['image']);
                    break;
                }
            }
            
            if ($updated && $this->db->write($this->filename, $banners)) {
                error_log("Banner: Banner updated successfully: " . $id);
                return ['success' => true, 'message' => 'Banner updated successfully'];
            }
            
            error_log("Banner: Failed to update banner: " . $id);
            return ['success' => false, 'message' => $updated ? 'Failed to save changes' : 'Banner not found'];
            
        } catch (Exception $e) {
            error_log("Banner: Exception in update(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating banner: ' . $e->getMessage()];
        }
    }
    
    public function delete($id) {
        try {
            $banners = $this->getAll();
            $originalCount = count($banners);
            
            // Find the banner to get its image path for cleanup
            $bannerToDelete = null;
            foreach ($banners as $banner) {
                if ($banner['id'] == $id) {
                    $bannerToDelete = $banner;
                    break;
                }
            }
            
            $filteredBanners = array_filter($banners, function($banner) use ($id) {
                return $banner['id'] != $id;
            });
            
            if (count($filteredBanners) < $originalCount) {
                if ($this->db->write($this->filename, array_values($filteredBanners))) {
                    // Optional: Clean up image file
                    if ($bannerToDelete && !empty($bannerToDelete['image'])) {
                        $this->cleanupImageFile($bannerToDelete['image']);
                    }
                    
                    error_log("Banner: Banner deleted successfully: " . $id);
                    return ['success' => true, 'message' => 'Banner deleted successfully'];
                } else {
                    error_log("Banner: Failed to write after delete: " . $id);
                    return ['success' => false, 'message' => 'Failed to save after deletion'];
                }
            }
            
            error_log("Banner: Banner not found for deletion: " . $id);
            return ['success' => false, 'message' => 'Banner not found'];
            
        } catch (Exception $e) {
            error_log("Banner: Exception in delete(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting banner: ' . $e->getMessage()];
        }
    }
    
    public function updateSortOrder($bannerIds) {
        try {
            if (empty($bannerIds) || !is_array($bannerIds)) {
                error_log("Banner: Invalid banner IDs for sort order update");
                return ['success' => false, 'message' => 'Invalid banner order data'];
            }
            
            $banners = $this->getAll();
            error_log("Banner: Updating sort order for IDs: " . json_encode($bannerIds));
            
            foreach ($banners as &$banner) {
                $newOrder = array_search($banner['id'], $bannerIds);
                if ($newOrder !== false) {
                    $banner['sort_order'] = $newOrder + 1;
                    $banner['updated_at'] = date('Y-m-d H:i:s');
                    error_log("Banner: Set banner " . $banner['id'] . " to order " . ($newOrder + 1));
                }
            }
            
            if ($this->db->write($this->filename, $banners)) {
                error_log("Banner: Sort order updated successfully");
                return ['success' => true, 'message' => 'Banner order updated successfully'];
            }
            
            error_log("Banner: Failed to write sort order changes");
            return ['success' => false, 'message' => 'Failed to update banner order'];
            
        } catch (Exception $e) {
            error_log("Banner: Exception in updateSortOrder(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating banner order: ' . $e->getMessage()];
        }
    }
    
    // Helper method to get banner statistics
    public function getStats() {
        $banners = $this->getAll();
        $active = array_filter($banners, function($b) { return $b['status'] === 'active'; });
        $inactive = array_filter($banners, function($b) { return $b['status'] !== 'active'; });
        
        return [
            'total' => count($banners),
            'active' => count($active),
            'inactive' => count($inactive)
        ];
    }
    
    // Helper method to validate image path
    public function validateImagePath($imagePath) {
        if (empty($imagePath)) {
            return ['exists' => false, 'searched_paths' => []];
        }
        
        // Clean the path
        $cleanPath = trim($imagePath);
        
        // Try different possible paths where the image might exist
        $possiblePaths = [
            // Web root relative path
            $_SERVER['DOCUMENT_ROOT'] . $cleanPath,
            // Project relative path
            __DIR__ . '/../' . ltrim($cleanPath, '/'),
            // Direct banner directory path
            __DIR__ . '/../assets/images/banners/' . basename($cleanPath),
            // Public banner directory path
            $_SERVER['DOCUMENT_ROOT'] . '/assets/images/banners/' . basename($cleanPath)
        ];
        
        // Remove duplicates
        $possiblePaths = array_unique($possiblePaths);
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_file($path)) {
                error_log("Banner: Image found at: " . $path);
                return ['exists' => true, 'path' => $path];
            }
        }
        
        error_log("Banner: Image not found. Searched paths: " . implode(', ', $possiblePaths));
        return ['exists' => false, 'searched_paths' => $possiblePaths];
    }
    
    // Helper method to cleanup image files
    private function cleanupImageFile($imagePath) {
        try {
            $imageValidation = $this->validateImagePath($imagePath);
            if ($imageValidation['exists']) {
                unlink($imageValidation['path']);
                error_log("Banner: Cleaned up image file: " . $imageValidation['path']);
            }
        } catch (Exception $e) {
            error_log("Banner: Failed to cleanup image file: " . $e->getMessage());
        }
    }
    
    // Method to fix existing banner image paths
    public function fixImagePaths() {
        try {
            $banners = $this->getAll();
            $fixed = 0;
            
            foreach ($banners as &$banner) {
                $currentPath = $banner['image'];
                $validation = $this->validateImagePath($currentPath);
                
                if (!$validation['exists']) {
                    // Try to find the image with just the filename
                    $filename = basename($currentPath);
                    $newPath = '/assets/images/banners/' . $filename;
                    $newValidation = $this->validateImagePath($newPath);
                    
                    if ($newValidation['exists']) {
                        $banner['image'] = $newPath;
                        $banner['updated_at'] = date('Y-m-d H:i:s');
                        $fixed++;
                        error_log("Banner: Fixed image path from '$currentPath' to '$newPath'");
                    }
                }
            }
            
            if ($fixed > 0 && $this->db->write($this->filename, $banners)) {
                error_log("Banner: Fixed $fixed banner image paths");
                return ['success' => true, 'message' => "Fixed $fixed banner image paths"];
            }
            
            return ['success' => true, 'message' => 'No image paths needed fixing'];
            
        } catch (Exception $e) {
            error_log("Banner: Exception in fixImagePaths(): " . $e->getMessage());
            return ['success' => false, 'message' => 'Error fixing image paths: ' . $e->getMessage()];
        }
    }
}
?>