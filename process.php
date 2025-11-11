<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Simple test endpoint
if (isset($_GET['test'])) {
    echo json_encode([
        'status' => 'OK',
        'php_version' => phpversion(),
        'gd_enabled' => extension_loaded('gd'),
        'zip_enabled' => extension_loaded('zip'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit')
    ]);
    exit();
}

// Check required PHP extensions
$required_extensions = ['gd', 'zip'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required PHP extensions: ' . implode(', ', $missing_extensions),
        'required_extensions' => $required_extensions,
        'missing_extensions' => $missing_extensions
    ]);
    exit();
}

// Configuration
$upload_dir = 'uploads/';
$output_dir = 'processed/';
$downloads_dir = 'downloads/';

// Create directories if they don't exist
foreach ([$upload_dir, $output_dir, $downloads_dir] as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Function to clean old files (older than 1 hour)
function cleanOldFiles($directory) {
    $files = glob($directory . '*');
    foreach ($files as $file) {
        if (is_file($file) && (time() - filemtime($file)) > 3600) {
            unlink($file);
        }
    }
}

// Function to generate unique session ID
function generateSessionId() {
    return uniqid('session_', true);
}

// Function to validate image file
function isValidImage($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'webp', 'gif'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowed_extensions);
}

// Function to rename JPEG files
function processRename($files, $session_id) {
    global $upload_dir, $output_dir, $downloads_dir;
    
    $session_upload_dir = $upload_dir . $session_id . '/';
    $session_output_dir = $output_dir . $session_id . '/';
    
    // Create session directories
    mkdir($session_upload_dir, 0755, true);
    mkdir($session_output_dir, 0755, true);
    
    $processed_count = 0;
    $errors = [];
    
    foreach ($files['name'] as $key => $filename) {
        if ($files['error'][$key] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading $filename";
            continue;
        }
        
        // Check if it's a JPEG file
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpeg', 'jpg'])) {
            $errors[] = "$filename is not a JPEG file";
            continue;
        }
        
        $temp_path = $files['tmp_name'][$key];
        $upload_path = $session_upload_dir . $filename;
        
        if (move_uploaded_file($temp_path, $upload_path)) {
            // Rename to .jpg if it's .jpeg
            if ($extension === 'jpeg') {
                $new_filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
            } else {
                $new_filename = $filename;
            }
            
            $output_path = $session_output_dir . $new_filename;
            
            if (copy($upload_path, $output_path)) {
                $processed_count++;
            } else {
                $errors[] = "Failed to process $filename";
            }
        } else {
            $errors[] = "Failed to upload $filename";
        }
    }
    
    if ($processed_count > 0) {
        // Create ZIP file
        $zip_filename = "renamed_files_" . date('Y-m-d_H-i-s') . ".zip";
        $zip_path = $downloads_dir . $zip_filename;
        
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
            $files = glob($session_output_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();
            
            return [
                'success' => true,
                'processed' => $processed_count,
                'download_url' => 'downloads/' . $zip_filename,
                'zip_name' => $zip_filename,
                'errors' => $errors
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create ZIP file',
                'errors' => $errors
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'No files were processed successfully',
            'errors' => $errors
        ];
    }
}

// Function to resize and center images using GD library
function resizeAndCenter($src_path, $dest_path, $target_width = 800, $target_height = 800) {
    // Get image info
    $image_info = getimagesize($src_path);
    if (!$image_info) {
        return false;
    }
    
    $src_width = $image_info[0];
    $src_height = $image_info[1];
    $src_type = $image_info[2];
    
    // Create source image resource
    switch ($src_type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($src_path);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($src_path);
            break;
        case IMAGETYPE_GIF:
            $src_image = imagecreatefromgif($src_path);
            break;
        case IMAGETYPE_BMP:
            $src_image = imagecreatefrombmp($src_path);
            break;
        default:
            return false;
    }
    
    if (!$src_image) {
        return false;
    }
    
    // Calculate scaling
    $scale = min($target_width / $src_width, $target_height / $src_height);
    $new_width = intval($src_width * $scale);
    $new_height = intval($src_height * $scale);
    
    // Calculate positioning for centering
    $x_offset = ($target_width - $new_width) / 2;
    $y_offset = ($target_height - $new_height) / 2;
    
    // Create destination image with white background
    $dest_image = imagecreatetruecolor($target_width, $target_height);
    $white = imagecolorallocate($dest_image, 255, 255, 255);
    imagefill($dest_image, 0, 0, $white);
    
    // Resize and copy image to center
    imagecopyresampled(
        $dest_image, $src_image,
        $x_offset, $y_offset, 0, 0,
        $new_width, $new_height, $src_width, $src_height
    );
    
    // Save the image
    $result = imagejpeg($dest_image, $dest_path, 90);
    
    // Clean up
    imagedestroy($src_image);
    imagedestroy($dest_image);
    
    return $result;
}

// Function to process and resize images
function processResize($files, $session_id) {
    global $upload_dir, $output_dir, $downloads_dir;
    
    $session_upload_dir = $upload_dir . $session_id . '/';
    $session_output_dir = $output_dir . $session_id . '/';
    
    // Create session directories
    mkdir($session_upload_dir, 0755, true);
    mkdir($session_output_dir, 0755, true);
    
    $processed_count = 0;
    $errors = [];
    
    foreach ($files['name'] as $key => $filename) {
        if ($files['error'][$key] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading $filename";
            continue;
        }
        
        // Validate image
        if (!isValidImage($filename)) {
            $errors[] = "$filename is not a valid image file";
            continue;
        }
        
        $temp_path = $files['tmp_name'][$key];
        $upload_path = $session_upload_dir . $filename;
        
        if (move_uploaded_file($temp_path, $upload_path)) {
            // Change extension to .jpg for output
            $output_filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
            $output_path = $session_output_dir . $output_filename;
            
            if (resizeAndCenter($upload_path, $output_path)) {
                $processed_count++;
            } else {
                $errors[] = "Failed to process $filename";
            }
        } else {
            $errors[] = "Failed to upload $filename";
        }
    }
    
    if ($processed_count > 0) {
        // Create ZIP file
        $zip_filename = "resized_images_" . date('Y-m-d_H-i-s') . ".zip";
        $zip_path = $downloads_dir . $zip_filename;
        
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
            $files = glob($session_output_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();
            
            return [
                'success' => true,
                'processed' => $processed_count,
                'download_url' => 'downloads/' . $zip_filename,
                'zip_name' => $zip_filename,
                'errors' => $errors
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create ZIP file',
                'errors' => $errors
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'No files were processed successfully',
            'errors' => $errors
        ];
    }
}

// Main processing logic
try {
    // Clean old files
    cleanOldFiles($upload_dir);
    cleanOldFiles($output_dir);
    cleanOldFiles($downloads_dir);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method is allowed');
    }
    
    if (!isset($_POST['action'])) {
        throw new Exception('No action specified');
    }
    
    if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
        throw new Exception('No files uploaded');
    }
    
    $action = $_POST['action'];
    $session_id = generateSessionId();
    $files = $_FILES['files'];
    
    switch ($action) {
        case 'rename':
            $result = processRename($files, $session_id);
            break;
        case 'resize':
            $result = processResize($files, $session_id);
            break;
        default:
            throw new Exception('Invalid action specified');
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log the error
    error_log("Process.php error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} catch (Error $e) {
    // Log fatal errors
    error_log("Process.php fatal error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>