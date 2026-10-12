import math
import os
from PIL import Image, ImageDraw, ImageFilter, ImageFont

# Canvas size for super-sampled master icon
S = 1024

def draw_master_icon():
    # 1. Master Image with RGBA
    master = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    
    # 2. Squircle / Rounded Rectangle Tile (Pure clean white to subtle blossom gradient)
    pad = 40
    rad = 220
    tile = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    tile_draw = ImageDraw.Draw(tile)
    
    # Soft drop shadow for tile
    shadow = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow)
    s_draw.rounded_rectangle(
        [(pad + 10, pad + 20), (S - pad - 10, S - pad + 20)],
        radius=rad,
        fill=(225, 29, 72, 40)
    )
    shadow = shadow.filter(ImageFilter.GaussianBlur(radius=25))
    master.paste(shadow, (0, 0), shadow)
    
    # Rounded Tile with luxury gradient / border
    tile_draw.rounded_rectangle(
        [(pad, pad), (S - pad, S - pad)],
        radius=rad,
        fill=(255, 245, 247, 255),
        outline=(251, 113, 133, 220),
        width=16
    )
    master.paste(tile, (0, 0), tile)
    
    flower = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    f_draw = ImageDraw.Draw(flower)
    
    cx, cy = S // 2, S // 2 + 10
    
    # 3. Emerald Leaf Pair at bottom
    leaf_color_dark = (5, 150, 105, 255)   # #059669
    leaf_color_light = (52, 211, 153, 255) # #34d399
    
    # Left Leaf
    left_leaf = [
        (cx - 30, cy + 90),
        (cx - 160, cy + 240),
        (cx - 310, cy + 190),
        (cx - 340, cy + 90),
        (cx - 200, cy + 40),
        (cx - 50, cy + 70)
    ]
    f_draw.polygon(left_leaf, fill=leaf_color_dark)
    
    # Right Leaf
    right_leaf = [
        (cx + 30, cy + 90),
        (cx + 160, cy + 240),
        (cx + 310, cy + 190),
        (cx + 340, cy + 90),
        (cx + 200, cy + 40),
        (cx + 50, cy + 70)
    ]
    f_draw.polygon(right_leaf, fill=leaf_color_light)
    
    # Leaf central veins
    f_draw.line([(cx - 30, cy + 90), (cx - 280, cy + 140)], fill=(16, 185, 129, 255), width=10)
    f_draw.line([(cx + 30, cy + 90), (cx + 280, cy + 140)], fill=(110, 231, 183, 255), width=10)
    
    # 4. Blooming Rose Petals (Multi-layered radial symmetry)
    # Layer 1: Outermost Petals (Deep rich rose red #e11d48)
    r1 = 155
    dist1 = 145
    for angle in [0, 60, 120, 180, 240, 300]:
        rad_ang = math.radians(angle)
        px = cx + dist1 * math.cos(rad_ang)
        py = cy + dist1 * math.sin(rad_ang)
        f_draw.ellipse(
            [(px - r1, py - r1), (px + r1, py + r1)],
            fill=(225, 29, 72, 255)  # #e11d48
        )
        
    # Layer 2: Mid Petals (Vibrant Rose #f43f5e)
    r2 = 135
    dist2 = 95
    for angle in [30, 90, 150, 210, 270, 330]:
        rad_ang = math.radians(angle)
        px = cx + dist2 * math.cos(rad_ang)
        py = cy + dist2 * math.sin(rad_ang)
        f_draw.ellipse(
            [(px - r2, py - r2), (px + r2, py + r2)],
            fill=(244, 63, 94, 255)  # #f43f5e
        )
        
    # Layer 3: Inner Petals (Warm Rose Pink #fb7185)
    r3 = 110
    dist3 = 55
    for angle in [0, 72, 144, 216, 288]:
        rad_ang = math.radians(angle)
        px = cx + dist3 * math.cos(rad_ang)
        py = cy + dist3 * math.sin(rad_ang)
        f_draw.ellipse(
            [(px - r3, py - r3), (px + r3, py + r3)],
            fill=(251, 113, 133, 255)  # #fb7185
        )
        
    # Layer 4: Blossom Core (Crimson Velvet #be123c)
    r4 = 85
    f_draw.ellipse(
        [(cx - r4, cy - r4), (cx + r4, cy + r4)],
        fill=(190, 18, 60, 255)
    )
    
    # Layer 5: Golden Stamen / Heart (Amber & Golden Glow #f59e0b)
    r5 = 62
    f_draw.ellipse(
        [(cx - r5, cy - r5), (cx + r5, cy + r5)],
        fill=(245, 158, 11, 255),
        outline=(255, 255, 255, 255),
        width=12
    )
    
    # Layer 6: Golden Highlights
    r6 = 38
    f_draw.ellipse(
        [(cx - r6, cy - r6), (cx + r6, cy + r6)],
        fill=(254, 240, 138, 255)
    )
    
    # Sparkle / Shimmer point
    f_draw.ellipse(
        [(cx - 24, cy - 24), (cx - 8, cy - 8)],
        fill=(255, 255, 255, 240)
    )
    
    # Paste flower onto master
    master.paste(flower, (0, 0), flower)
    return master

if __name__ == '__main__':
    master = draw_master_icon()
    
    # Save master
    os.makedirs('public/images', exist_ok=True)
    master.save('public/images/logo-icon.png')
    
    # Downsample with Lanczos for crystal-clear icons
    icon_16 = master.resize((16, 16), Image.Resampling.LANCZOS)
    icon_32 = master.resize((32, 32), Image.Resampling.LANCZOS)
    icon_48 = master.resize((48, 48), Image.Resampling.LANCZOS)
    icon_64 = master.resize((64, 64), Image.Resampling.LANCZOS)
    icon_180 = master.resize((180, 180), Image.Resampling.LANCZOS)
    icon_192 = master.resize((192, 192), Image.Resampling.LANCZOS)
    icon_512 = master.resize((512, 512), Image.Resampling.LANCZOS)
    
    # Write PNGs
    icon_16.save('public/favicon-16x16.png')
    icon_32.save('public/favicon-32x32.png')
    icon_48.save('public/favicon-48x48.png')
    icon_180.save('public/apple-touch-icon.png')
    icon_192.save('public/android-chrome-192x192.png')
    icon_512.save('public/android-chrome-512x512.png')
    
    # Write multi-resolution ICO file (16, 32, 48, 64)
    icon_32.save(
        'public/favicon.ico',
        format='ICO',
        sizes=[(16, 16), (32, 32), (48, 48), (64, 64)]
    )
    
    print('All favicon and icon PNG/ICO files generated successfully!')
