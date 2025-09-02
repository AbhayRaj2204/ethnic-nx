<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Banner.php';

$auth = new Auth();
$auth->requireAdmin();

$bannerModel = new Banner();
$currentUser = $auth->getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    header('Content-Type: application/json');
    
    try {
        // Create upload directories if they don't exist
        $uploadDir = __DIR__ . '/../assets/images/banners/';
        $publicDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/banners/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (!is_dir($publicDir) && $publicDir !== $uploadDir) {
            mkdir($publicDir, 0755, true);
        }
        
        $file = $_FILES['image'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            throw new Exception('File size too large. Maximum 5MB allowed.');
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
        }
        
        // Generate unique filename
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        
        // Try to save to the project directory first
        $filePath = $uploadDir . $fileName;
        $webPath = '/assets/images/banners/' . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Also copy to public directory if it's different
            if ($publicDir !== $uploadDir && is_dir($publicDir)) {
                copy($filePath, $publicDir . $fileName);
            }
            
            // Verify file was saved
            if (file_exists($filePath)) {
                error_log("Banner Upload: File saved successfully at " . $filePath);
                echo json_encode([
                    'success' => true, 
                    'path' => $webPath,
                    'message' => 'Image uploaded successfully'
                ]);
            } else {
                throw new Exception('File was moved but cannot be verified');
            }
        } else {
            throw new Exception('Failed to move uploaded file');
        }
    } catch (Exception $e) {
        error_log("Banner Upload Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Upload error: ' . $e->getMessage()]);
    }
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'image' => $_POST['image'] ?? '',
                    'status' => $_POST['status'] ?? 'active',
                    'sort_order' => $_POST['sort_order'] ?? 999
                ];
                
                // Validate image path
                if (empty($data['image'])) {
                    echo json_encode(['success' => false, 'message' => 'Image is required']);
                    break;
                }
                
                // Verify image file exists
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $data['image'];
                $altImagePath = __DIR__ . '/../' . ltrim($data['image'], '/');
                
                if (!file_exists($imagePath) && !file_exists($altImagePath)) {
                    error_log("Banner Create: Image file not found at " . $imagePath . " or " . $altImagePath);
                    echo json_encode(['success' => false, 'message' => 'Image file not found. Please re-upload the image.']);
                    break;
                }
                
                $result = $bannerModel->create($data);
                echo json_encode($result);
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $data = [
                    'image' => $_POST['image'] ?? '',
                    'status' => $_POST['status'] ?? 'active',
                    'sort_order' => $_POST['sort_order'] ?? 999
                ];
                
                // Validate image path
                if (empty($data['image'])) {
                    echo json_encode(['success' => false, 'message' => 'Image is required']);
                    break;
                }
                
                $result = $bannerModel->update($id, $data);
                echo json_encode($result);
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $result = $bannerModel->delete($id);
                echo json_encode($result);
                break;
                
            case 'update_order':
                $bannerIds = json_decode($_POST['banner_ids'] ?? '[]', true);
                $result = $bannerModel->updateSortOrder($bannerIds);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        error_log("Banner Action Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Get banners
$banners = $bannerModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .banner-preview {
            width: 150px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .sortable-row {
            cursor: move;
        }
        .sortable-row:hover {
            background-color: #f8f9fa;
        }
        .image-upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: border-color 0.3s;
            cursor: pointer;
        }
        .image-upload-area:hover {
            border-color: #007bff;
        }
        .image-upload-area.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .image-upload-area.uploading {
            pointer-events: none;
            opacity: 0.7;
        }
        .image-error {
            color: #dc3545;
            font-style: italic;
            font-size: 0.9em;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #6c757d;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="page-title">Banner Management</h1>
                                <p class="page-subtitle">Manage homepage banner slider images</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal">
                                <i class="fas fa-plus"></i> Add Banner
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Banners</h5>
                                <small class="text-muted">Drag and drop rows to reorder banners</small>
                            </div>
                            <div class="card-body">
                                <?php if (empty($banners)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No banners found. Add your first banner to get started.
                                    </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="bannersTable">
                                        <thead>
                                            <tr>
                                                <th width="50">Order</th>
                                                <th width="180">Preview</th>
                                                <!-- <th>Image Path</th> -->
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sortableBanners">
                                            <?php foreach ($banners as $banner): ?>
                                            <tr class="sortable-row" data-id="<?php echo $banner['id']; ?>">
                                                <td>
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                    <?php echo $banner['sort_order']; ?>
                                                </td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($banner['image']); ?>" 
                                                         alt="Banner Preview" 
                                                         class="banner-preview"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div class="image-error" style="display: none;">
                                                        <i class="fas fa-exclamation-triangle"></i> Image not found
                                                    </div>
                                                </td>
                                                <!-- <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($banner['image']); ?></div>
                                                </td> -->
                                                <td>
                                                    <span class="badge bg-<?php echo $banner['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($banner['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($banner['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary edit-banner" 
                                                                data-id="<?php echo $banner['id']; ?>"
                                                                data-image="<?php echo htmlspecialchars($banner['image']); ?>"
                                                                data-status="<?php echo $banner['status']; ?>"
                                                                data-sort-order="<?php echo $banner['sort_order']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger delete-banner" 
                                                                data-id="<?php echo $banner['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Banner Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bannerForm" action="banners.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="image" value="">
                        
                        <!-- Image upload section -->
                        <div class="mb-4">
                            <label class="form-label">Banner Image * <small class="text-muted">(Recommended: 1200x400px, Max: 5MB)</small></label>
                            <div class="image-upload-area" id="imageUploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Drop your banner image here</h5>
                                <p class="text-muted">or click to browse files</p>
                                <input type="file" id="imageInput" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-outline-primary" id="chooseFileBtn">
                                    Choose File
                                </button>
                            </div>
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                <br>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                                <div id="uploadStatus" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" name="sort_order" value="999" min="1">
                                    <div class="form-text">Lower numbers appear first</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bannerModal = document.getElementById('bannerModal');
            const bannerForm = document.getElementById('bannerForm');
            const modalTitle = bannerModal.querySelector('.modal-title');
            const imageInput = document.getElementById('imageInput');
            const imageUploadArea = document.getElementById('imageUploadArea');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const chooseFileBtn = document.getElementById('chooseFileBtn');
            const uploadStatus = document.getElementById('uploadStatus');
            const submitBtn = document.getElementById('submitBtn');
            
            // Click events for file selection
            chooseFileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!imageUploadArea.classList.contains('uploading')) {
                    imageInput.click();
                }
            });
            
            imageUploadArea.addEventListener('click', function(e) {
                if (e.target === this || e.target.tagName === 'I' || e.target.tagName === 'H5' || e.target.tagName === 'P') {
                    e.preventDefault();
                    if (!this.classList.contains('uploading')) {
                        imageInput.click();
                    }
                }
            });
            
            // Drag and drop events
            imageUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });
            
            imageUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });
            
            imageUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0 && !this.classList.contains('uploading')) {
                    handleImageUpload(files[0]);
                }
            });
            
            // File input change event
            imageInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleImageUpload(e.target.files[0]);
                }
            });
            
            function handleImageUpload(file) {
                console.log('Uploading file:', file.name, 'Type:', file.type, 'Size:', file.size);
                
                // Clear previous status
                uploadStatus.innerHTML = '';
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showUploadStatus('Please select an image file', 'danger');
                    return;
                }
                
                // Validate file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    showUploadStatus('File size too large. Please select an image under 5MB.', 'danger');
                    return;
                }
                
                // Show immediate preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.onload = function() {
                        imagePreview.style.display = 'block';
                        imageUploadArea.style.display = 'none';
                    };
                };
                reader.readAsDataURL(file);
                
                // Show uploading state
                imageUploadArea.classList.add('uploading');
                showUploadStatus('Uploading...', 'info');
                
                // Upload to server
                const formData = new FormData();
                formData.append('image', file);
                
                fetch('banners.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Upload result:', data);
                    if (data.success) {
                        bannerForm.querySelector('input[name="image"]').value = data.path;
                        showUploadStatus('Image uploaded successfully!', 'success');
                        
                        // Enable submit button
                        submitBtn.disabled = false;
                        
                        console.log('Image path set to:', data.path);
                    } else {
                        showUploadStatus(data.message || 'Upload failed', 'danger');
                        resetUploadArea();
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    showUploadStatus('Upload failed: ' + error.message, 'danger');
                    resetUploadArea();
                })
                .finally(() => {
                    imageUploadArea.classList.remove('uploading');
                });
            }
            
            function showUploadStatus(message, type) {
                uploadStatus.innerHTML = `<div class="alert alert-${type} py-1 px-2 mt-2 small">${message}</div>`;
            }
            
            function resetUploadArea() {
                imageUploadArea.innerHTML = `
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <h5>Drop your banner image here</h5>
                    <p class="text-muted">or click to browse files</p>
                    <button type="button" class="btn btn-outline-primary" id="chooseFileBtn">
                        Choose File
                    </button>
                `;
                imageUploadArea.style.display = 'block';
                imagePreview.style.display = 'none';
                uploadStatus.innerHTML = '';
                submitBtn.disabled = true;
                
                // Re-attach click event
                const newChooseBtn = document.getElementById('chooseFileBtn');
                if (newChooseBtn) {
                    newChooseBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (!imageUploadArea.classList.contains('uploading')) {
                            imageInput.click();
                        }
                    });
                }
            }
            
            // Global function to remove image
            window.removeImage = function() {
                bannerForm.querySelector('input[name="image"]').value = '';
                imagePreview.style.display = 'none';
                resetUploadArea();
                imageInput.value = '';
            };
            
            // Form submission
            bannerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const imageValue = this.querySelector('input[name="image"]').value;
                
                if (!imageValue) {
                    showAlert('Please upload a banner image first', 'danger');
                    return;
                }
                
                if (submitBtn.disabled) {
                    return;
                }
                
                const formData = new FormData(this);
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                submitBtn.disabled = true;
                
                console.log('Submitting form with image path:', imageValue);
                
                fetch('banners.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Form submission result:', data);
                    if (data.success) {
                        showAlert(data.message || 'Banner saved successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(data.message || 'Failed to save banner', 'danger');
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);
                    showAlert('An error occurred. Please try again.', 'danger');
                    submitBtn.disabled = false;
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                });
            });

            // Initialize sortable
            if (document.getElementById('sortableBanners')) {
                const sortable = Sortable.create(document.getElementById('sortableBanners'), {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const bannerIds = Array.from(evt.to.children).map(row => row.dataset.id);
                        
                        const formData = new FormData();
                        formData.append('action', 'update_order');
                        formData.append('banner_ids', JSON.stringify(bannerIds));
                        
                        fetch('banners.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('An error occurred while updating order.', 'danger');
                        });
                    }
                });
            }
            
            // Edit banner
            document.querySelectorAll('.edit-banner').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    
                    modalTitle.textContent = 'Edit Banner';
                    bannerForm.querySelector('input[name="action"]').value = 'update';
                    bannerForm.querySelector('input[name="id"]').value = data.id;
                    bannerForm.querySelector('input[name="image"]').value = data.image;
                    bannerForm.querySelector('select[name="status"]').value = data.status;
                    bannerForm.querySelector('input[name="sort_order"]').value = data.sortOrder;
                    
                    if (data.image) {
                        previewImg.src = data.image;
                        imagePreview.style.display = 'block';
                        imageUploadArea.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                    
                    new bootstrap.Modal(bannerModal).show();
                });
            });
            
            // Delete banner
            document.querySelectorAll('.delete-banner').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    
                    if (confirm('Are you sure you want to delete this banner?')) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', id);
                        
                        fetch('banners.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('An error occurred. Please try again.', 'danger');
                        });
                    }
                });
            });
            
            // Reset form when modal is hidden
            bannerModal.addEventListener('hidden.bs.modal', function() {
                modalTitle.textContent = 'Add Banner';
                bannerForm.reset();
                bannerForm.querySelector('input[name="action"]').value = 'create';
                bannerForm.querySelector('input[name="id"]').value = '';
                bannerForm.querySelector('input[name="image"]').value = '';
                imagePreview.style.display = 'none';
                resetUploadArea();
                imageInput.value = '';
            });
        });
        
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>
</html>