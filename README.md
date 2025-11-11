# 🖼️ Image Processing Tools

A web-based interface for processing images with two powerful tools. Users can upload images through their browser, process them on the server, and download the results.

## 📝 Tool 1: Rename JPEG to JPG
Upload .jpeg files and they will be automatically converted to .jpg format for download.

## 🎨 Tool 2: Resize and Center Images  
Upload images and they will be resized to 800x800 pixels while maintaining aspect ratio and centered on a white background.

## 🚀 How to Use

### Web Interface (Recommended)
1. Open `index.html` in any web browser
2. **Upload files**: Click the upload area or drag & drop your images
3. **Process**: Click the processing button
4. **Download**: Get your processed images as a ZIP file

### Features:
- ✅ **Drag & Drop**: Simply drag files onto the upload areas
- ✅ **Multiple Files**: Process multiple images at once
- ✅ **Progress Tracking**: See upload and processing progress
- ✅ **Automatic Download**: Get results as convenient ZIP files
- ✅ **File Validation**: Only accepts valid image formats
- ✅ **Responsive Design**: Works on desktop and mobile

## 🔧 Server Requirements

### For Apache Server:
- PHP 7.4 or higher
- GD extension enabled
- ZipArchive extension enabled
- File upload enabled (`file_uploads = On`)
- Sufficient upload limits:
  ```php
  upload_max_filesize = 50M
  post_max_size = 50M
  max_execution_time = 300
  memory_limit = 256M
  ```

### Installation on Server:
1. Upload all files to your web directory
2. Ensure PHP has write permissions to:
   - `uploads/` directory
   - `processed/` directory  
   - `downloads/` directory
3. Set up a cron job to run `cleanup.php` periodically:
   ```bash
   # Run every hour to clean old files
   0 * * * * php /path/to/your/site/cleanup.php
   ```

## 📁 File Structure
- `index.html` - Main web interface
- `process.php` - Server-side processing script
- `cleanup.php` - Maintenance script for old files
- `.htaccess` - Security configuration
- `uploads/` - Temporary upload storage
- `processed/` - Processed images storage
- `downloads/` - ZIP files for download
- `change_to_jpg.py` - Standalone Python script (optional)
- `photos.py` - Standalone Python script (optional)

## 🔒 Security Features
- File type validation
- Upload size limits
- Automatic cleanup of old files
- Protected upload directories
- No execution of uploaded files

## 🛠️ Maintenance
- Files older than 1 hour are automatically cleaned up
- Run `cleanup.php` manually if needed
- Monitor disk space in upload directories
- Check Apache error logs for any issues

## 📱 Browser Support
- Chrome, Firefox, Safari, Edge
- Mobile browsers supported
- Requires JavaScript enabled

## 🆘 Troubleshooting

### Common Issues:
- **Upload fails**: Check file size limits in PHP configuration
- **Processing hangs**: Increase PHP execution time limit  
- **Download doesn't work**: Verify write permissions on directories
- **Files not processed**: Check that GD extension is installed

### Server Logs:
Check your Apache error log for detailed error messages.

## 💡 Tips for Users
1. **File Formats**: Supports JPG, PNG, BMP, TIFF, WebP, GIF
2. **File Size**: Keep individual files under 10MB for best performance
3. **Multiple Files**: You can select and process many files at once
4. **Download**: Processed files are delivered as convenient ZIP archives