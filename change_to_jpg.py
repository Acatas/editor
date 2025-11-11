import os

def rename_jpeg_to_jpg(directory):
    for filename in os.listdir(directory):
        if filename.lower().endswith('.jpeg'):
            base = os.path.splitext(filename)[0]
            new_filename = base + '.jpg'
            old_path = os.path.join(directory, filename)
            new_path = os.path.join(directory, new_filename)
            os.rename(old_path, new_path)
            print(f'Renamed: {filename} -> {new_filename}')

# Example usage:
rename_jpeg_to_jpg(r'C:\Users\lucas\Desktop\acatas\bazar')  