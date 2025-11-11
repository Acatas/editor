import cv2
import numpy as np
import os
from glob import glob

def resize_and_center(image, target_size=(800, 800)):
    """Redimensiona la imagen manteniendo su proporción y la centra en un lienzo de 800x800."""
    h, w = image.shape[:2]
    scale = min(target_size[0] / w, target_size[1] / h)
    new_w, new_h = int(w * scale), int(h * scale)
    
    resized_image = cv2.resize(image, (new_w, new_h), interpolation=cv2.INTER_AREA)
    canvas = np.ones((target_size[1], target_size[0], 3), dtype=np.uint8) * 255
    
    x_offset = (target_size[0] - new_w) // 2
    y_offset = (target_size[1] - new_h) // 2
    
    canvas[y_offset:y_offset + new_h, x_offset:x_offset + new_w] = resized_image
    return canvas

def process_images(input_folder, output_folder):
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)
    
    for root, _, files in os.walk(input_folder):
        relative_path = os.path.relpath(root, input_folder)
        target_folder = os.path.join(output_folder, relative_path)
        
        if not os.path.exists(target_folder):
            os.makedirs(target_folder)
        
        for file in files:
            img_path = os.path.join(root, file)
            image = cv2.imread(img_path)
            if image is None:
                print(f"❌ Error al cargar: {img_path}")
                continue
            
            processed_image = resize_and_center(image)
            output_path = os.path.join(target_folder, file)
            cv2.imwrite(output_path, processed_image)
            print(f"✅ Imagen procesada guardada: {output_path}")

# Rutas de carpetas (ajustar según tu sistema)
input_folder = r"C:\Users\lucas\Desktop\acatas\a"

output_folder= r"C:\Users\lucas\Desktop\acatas\bazar_arregladas"


process_images(input_folder, output_folder)
