import os
import math
import numpy as np
from PIL import Image, ImageDraw, ImageFilter

src_path = r'C:\Users\yigit\.gemini\antigravity\brain\8741e97c-0c3a-4926-9b74-b985af34cb00\.user_uploaded\media_1791487941992_cd98e223.png'
os.makedirs('public/images', exist_ok=True)

# 1. Load source image and crop clean bounding box
raw_img = Image.open(src_path).convert('RGBA')
# Content is between y=0 and y=168, x=0 to 429
img = raw_img.crop((0, 0, raw_img.width, 168))

# Save clean logo-dark.png (exact original user logo)
img.save('public/images/logo-dark.png')
img.save('public/images/logo-original.png')

# 2. Extract emblem for icon/favicon
# Emblem in this crop is located around x: 130 to 300, y: 8 to 82
emblem_crop = img.crop((128, 8, 302, 82))
emblem_crop.save('public/images/emblem-raw.png')

# 3. Create high-res 1024x1024 master emblem icon (Squircle dark luxury app tile)
S = 1024
master = Image.new('RGBA', (S, S), (0, 0, 0, 0))

# Squircle background (rich midnight black/slate with subtle gold trim)
pad = 48
rad = 220
tile = Image.new('RGBA', (S, S), (0, 0, 0, 0))
t_draw = ImageDraw.Draw(tile)

# Soft shadow
shadow = Image.new('RGBA', (S, S), (0, 0, 0, 0))
s_draw = ImageDraw.Draw(shadow)
s_draw.rounded_rectangle([(pad+10, pad+20), (S-pad-10, S-pad+20)], radius=rad, fill=(0, 0, 0, 160))
shadow = shadow.filter(ImageFilter.GaussianBlur(radius=25))
master.paste(shadow, (0, 0), shadow)

# Main dark luxury tile
t_draw.rounded_rectangle(
    [(pad, pad), (S-pad, S-pad)],
    radius=rad,
    fill=(10, 10, 12, 255),
    outline=(212, 175, 55, 230), # luxury gold border
    width=16
)
master.paste(tile, (0, 0), tile)

# Scale emblem into center of master tile
# Emblem aspect ratio: 174x74 -> width should be ~720, height ~306
emb_w = 720
emb_h = int(emblem_crop.height * (emb_w / emblem_crop.width))
emblem_scaled = emblem_crop.resize((emb_w, emb_h), Image.Resampling.LANCZOS)

# Position in center
pos_x = (S - emb_w) // 2
pos_y = (S - emb_h) // 2 - 10
master.paste(emblem_scaled, (pos_x, pos_y), emblem_scaled)

# Save master icon
master.save('public/images/logo-icon.png')

# 4. Generate All Favicon resolutions
icon_16 = master.resize((16, 16), Image.Resampling.LANCZOS)
icon_32 = master.resize((32, 32), Image.Resampling.LANCZOS)
icon_48 = master.resize((48, 48), Image.Resampling.LANCZOS)
icon_64 = master.resize((64, 64), Image.Resampling.LANCZOS)
icon_180 = master.resize((180, 180), Image.Resampling.LANCZOS)
icon_192 = master.resize((192, 192), Image.Resampling.LANCZOS)
icon_512 = master.resize((512, 512), Image.Resampling.LANCZOS)

icon_16.save('public/favicon-16x16.png')
icon_32.save('public/favicon-32x32.png')
icon_48.save('public/favicon-48x48.png')
icon_180.save('public/apple-touch-icon.png')
icon_192.save('public/android-chrome-192x192.png')
icon_512.save('public/android-chrome-512x512.png')

# Save multi-resolution favicon.ico
icon_32.save(
    'public/favicon.ico',
    format='ICO',
    sizes=[(16, 16), (32, 32), (48, 48), (64, 64)]
)

print('Master icon and favicons generated successfully!')
