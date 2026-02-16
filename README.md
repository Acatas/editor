# 🖼️ Image Processing Tools

[![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/Acatas/editor/blob/main/LICENSE)
[![GitHub contributors](https://img.shields.io/github/contributors/Naereen/StrapDown.js.svg)](https://GitHub.com/Acatas/editor/graphs/contributors/)
[![GitHub issues](https://img.shields.io/github/issues/Naereen/StrapDown.js.svg)](https://GitHub.com/Acatas/editor/issues/)
[![GitHub pull requests](https://img.shields.io/github/issues-pr/Naereen/StrapDown.js.svg)](https://GitHub.com/Acatas/editor/pulls/)

A web-based interface for processing images with two powerful tools. Users can upload images through their browser, process them on the server, and download the results.

## ✨ Features

- ✅ **Drag & Drop**: Simply drag files onto the upload areas
- ✅ **Multiple Files**: Process multiple images at once
- ✅ **Progress Tracking**: See upload and processing progress
- ✅ **Automatic Download**: Get results as convenient ZIP files
- ✅ **File Validation**: Only accepts valid image formats
- ✅ **Responsive Design**: Works on desktop and mobile

## 🚀 Live Demo

[Explore the Live Demo Here!](https://your-live-demo-link.com) (Coming Soon!)

## 🛠️ Tools

### 📝 Tool 1: Rename JPEG to JPG
Upload .jpeg files and they will be automatically converted to .jpg format for download.

### 🎨 Tool 2: Resize and Center Images  
Upload images and they will be resized to 800x800 pixels while maintaining aspect ratio and centered on a white background.

## 📦 Getting Started

### Prerequisites

#### For Apache Server:
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

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/Acatas/editor.git
    cd your-repo
    ```
2.  **Move `src` and `public` to your web server:**
    Upload the contents of the `public/` directory to your web server's root (e.g., `public_html` or `www`).
    Ensure the `src/php/` directory is accessible by your web server, but ideally not directly web-exposed for security.
3.  **Set up permissions:**
    Ensure your web server (PHP) has write permissions to:
    - `public/uploads/` directory
    - `public/processed/` directory  
    - `public/downloads/` directory
    You might need to create these directories manually and set permissions:
    ```bash
    mkdir public/uploads public/processed public/downloads
    chmod -R 775 public/uploads public/processed public/downloads
    ```
4.  **Configure `.htaccess`:**
    The provided `.htaccess` in `public/` helps with security. Ensure it's active on your Apache server.
5.  **Set up a cron job for cleanup:**
    To automatically clean old files, set up a cron job to run `src/php/cleanup.php` periodically:
    ```bash
    # Run every hour to clean old files
    0 * * * * php /path/to/your/site/src/php/cleanup.php
    ```

## 📁 Project Structure

```
.
├── public/                 # Web-facing files (HTML, CSS, JS, .htaccess)
│   ├── index.html          # Main web interface
│   ├── test.html           # For testing purposes
│   ├── .htaccess           # Security configuration for Apache
│   ├── uploads/            # Temporary upload storage (create and set permissions)
│   ├── processed/          # Processed images storage (create and set permissions)
│   └── downloads/          # ZIP files for download (create and set permissions)
├── src/                    # Backend source code
│   └── php/
│       ├── process.php     # Server-side processing script
│       └── cleanup.php     # Maintenance script for old files
├── scripts/                # Standalone Python scripts (optional, not used by web interface)
│   ├── change_to_jpg.py
│   └── photos.py
├── CONTRIBUTING.md         # Guidelines for contributing to the project
├── LICENSE                 # Project license (MIT)
├── README.md               # This file
└── requirements.txt        # Python dependencies (if using scripts directly)
```

## 🔒 Security Features

- File type validation
- Upload size limits
- Automatic cleanup of old files
- Protected upload directories
- No execution of uploaded files

## 🤝 Contributing

We welcome contributions of all kinds! Please see our [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to:

- Report bugs
- Suggest enhancements
- Submit pull requests

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

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
