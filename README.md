# 🖼️ Image Processing Tools

A simple web interface for processing images with two useful tools:

## 📝 Tool 1: Rename JPEG to JPG
Automatically renames all `.jpeg` files to `.jpg` files in a directory.

## 🎨 Tool 2: Resize and Center Images  
Resizes images to fit within 800x800 pixels while maintaining aspect ratio and centers them on a white background.

## 🚀 How to Use

### Option 1: Use the Web Interface (Recommended for non-technical users)
1. Open `index.html` in any web browser (Chrome, Firefox, Edge, etc.)
2. Enter the folder paths where your images are located
3. Click the buttons to generate custom scripts or get instructions
4. Follow the on-screen instructions

### Option 2: Use Python Scripts Directly
1. Make sure Python is installed on your computer
2. Install required packages:
   ```bash
   pip install opencv-python numpy
   ```
3. Edit the paths in the Python files:
   - `change_to_jpg.py` - for renaming JPEG files
   - `photos.py` - for resizing and centering images
4. Run the scripts:
   ```bash
   python change_to_jpg.py
   python photos.py
   ```

## 📁 File Structure
- `index.html` - Web interface (open this in your browser)
- `change_to_jpg.py` - Script to rename JPEG files to JPG
- `photos.py` - Script to resize and center images
- `README.md` - This instruction file

## 💡 Tips for Non-Technical Users

1. **Finding folder paths**: 
   - Open File Explorer
   - Navigate to your folder
   - Click on the address bar
   - Copy the path (e.g., `C:\Users\YourName\Desktop\photos`)

2. **Installing Python** (if needed):
   - Download from [python.org](https://python.org)
   - Make sure to check "Add Python to PATH" during installation

3. **If you get errors**:
   - Make sure the folder paths exist
   - Check that you have images in the input folder
   - Ensure you have write permissions to the output folder

## 🆘 Common Issues

- **"No such file or directory"**: Check that your folder path is correct
- **"Permission denied"**: Make sure the folder isn't read-only
- **Images not processing**: Ensure the images are in supported formats (JPG, PNG, etc.)

## 📞 Support
If you need help, check that:
1. Your folder paths are correct
2. You have the necessary permissions
3. Python and required packages are installed (for direct script usage)