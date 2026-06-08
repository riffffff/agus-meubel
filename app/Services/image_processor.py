import sys
import os
from PIL import Image

def process_image(input_path, output_path, max_width=1400, quality=75):
    try:
        if not os.path.exists(input_path):
            print(f"Error: Input file {input_path} does not exist.")
            sys.exit(1)
            
        with Image.open(input_path) as img:
            # Convert to RGB if it's RGBA/LA or has palette to avoid issues with WebP
            if img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info):
                # Keep transparency if possible, or convert to RGB
                # WebP supports RGBA
                if img.mode != 'RGBA':
                    img = img.convert('RGBA')
            else:
                img = img.convert('RGB')
                
            width, height = img.size
            if width > max_width:
                # Calculate new height maintaining aspect ratio
                new_height = int((max_width / width) * height)
                img = img.resize((max_width, new_height), Image.Resampling.LANCZOS)
                
            # Save as WebP
            img.save(output_path, 'WEBP', quality=quality)
            print(f"Success: Image processed and saved to {output_path}")
            
    except Exception as e:
        print(f"Error: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python3 image_processor.py <input_path> <output_path> [max_width] [quality]")
        sys.argv = ["", "test.jpg", "test.webp"]
        
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    max_width = int(sys.argv[3]) if len(sys.argv) > 3 else 1400
    quality = int(sys.argv[4]) if len(sys.argv) > 4 else 75
    
    process_image(input_path, output_path, max_width, quality)
