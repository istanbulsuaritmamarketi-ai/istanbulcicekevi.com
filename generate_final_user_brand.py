import os
import base64
import numpy as np
from PIL import Image, ImageDraw, ImageFilter

src_path = r'C:\Users\yigit\.gemini\antigravity\brain\8741e97c-0c3a-4926-9b74-b985af34cb00\.user_uploaded\media_1791487941992_cd98e223.png'
im_raw = Image.open(src_path).convert('RGBA')

# 1. Clean crop 429 x 168 (exclude bottom artifact line)
clean_stacked = im_raw.crop((0, 0, 429, 168))
os.makedirs('public/images', exist_ok=True)
clean_stacked.save('public/images/logo-stacked-dark.png')
clean_stacked.save('public/images/logo-dark.png')
clean_stacked.save('public/images/logo-original.png')

# 2. Extract emblem crop with clean bounds: x=[56, 288], y=[18, 97]
emblem = im_raw.crop((56, 18, 288, 97)) # 232 x 79

# Extract text crop with clean bounds: x=[54, 368], y=[99, 167]
text = im_raw.crop((54, 99, 368, 167))   # 314 x 68

# 3. Create Horizontal Dark Logo (450 x 80)
W, H = 450, 80
horiz_dark = Image.new('RGBA', (W, H), (10, 10, 12, 255)) # Luxury midnight black #0A0A0C

# Scale emblem to height 60
emb_h = 60
emb_w = int(emblem.width * (emb_h / emblem.height)) # ~176 px
emblem_scaled = emblem.resize((emb_w, emb_h), Image.Resampling.LANCZOS)

# Scale text to height 52
txt_h = 52
txt_w = int(text.width * (txt_h / text.height))     # ~240 px
text_scaled = text.resize((txt_w, txt_h), Image.Resampling.LANCZOS)

# Positioning:
x_emb = 12
y_emb = (H - emb_h) // 2
x_txt = x_emb + emb_w + 14
y_txt = (H - txt_h) // 2

horiz_dark.paste(emblem_scaled, (x_emb, y_emb))
horiz_dark.paste(text_scaled, (x_txt, y_txt))

horiz_dark.save('public/images/logo-horizontal-dark.png')
horiz_dark.save('public/images/logo.png')

# 4. Create Horizontal Transparent Logo (for dark backgrounds like Admin sidebar & Footer)
# Turn pure black into transparent alpha channel
arr = np.array(horiz_dark, dtype=np.float32)
r, g, b = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]
max_c = np.maximum(np.maximum(r, g), b)
# Alpha ramp: pixels with brightness < 12 are transparent, > 35 are solid
alpha = np.clip((max_c - 10.0) / 28.0 * 255.0, 0.0, 255.0)
arr[:, :, 3] = alpha
horiz_trans = Image.fromarray(arr.astype(np.uint8), mode='RGBA')
horiz_trans.save('public/images/logo-horizontal-transparent.png')
horiz_trans.save('public/images/logo-white.png')

# 5. Create Base64 strings for SVGs
with open('public/images/logo-horizontal-dark.png', 'rb') as f:
    horiz_dark_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-horizontal-transparent.png', 'rb') as f:
    horiz_trans_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-dark.png', 'rb') as f:
    stacked_dark_b64 = base64.b64encode(f.read()).decode('utf-8')

# 6. Generate Master SVGs
# Main Header Logo SVG: Luxury dark capsule with gold border (#D4AF37)
logo_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 80" width="450" height="80">
  <rect width="450" height="80" rx="16" fill="#0A0A0C" stroke="#D4AF37" stroke-width="1.5"/>
  <image href="data:image/png;base64,{horiz_dark_b64}" x="0" y="0" width="450" height="80"/>
</svg>'''

with open('public/images/logo.svg', 'w', encoding='utf-8') as f:
    f.write(logo_svg)

# White/Transparent Logo SVG: For dark background (Sidebar bg-slate-950 & Footer bg-gray-900)
logo_white_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 80" width="450" height="80">
  <image href="data:image/png;base64,{horiz_trans_b64}" x="0" y="0" width="450" height="80"/>
</svg>'''

with open('public/images/logo-white.svg', 'w', encoding='utf-8') as f:
    f.write(logo_white_svg)

# Stacked Logo SVG (Original square/card layout)
logo_stacked_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 429 168" width="429" height="168">
  <rect width="429" height="168" rx="20" fill="#0A0A0C" stroke="#D4AF37" stroke-width="1.5"/>
  <image href="data:image/png;base64,{stacked_dark_b64}" x="0" y="0" width="429" height="168"/>
</svg>'''

with open('public/images/logo-stacked.svg', 'w', encoding='utf-8') as f:
    f.write(logo_stacked_svg)

# 7. Generate Master 1024x1024 Squircle Icon for Favicons
S = 1024
master = Image.new('RGBA', (S, S), (0, 0, 0, 0))
pad = 48
rad = 220
tile = Image.new('RGBA', (S, S), (0, 0, 0, 0))
t_draw = ImageDraw.Draw(tile)

# Drop shadow
shadow = Image.new('RGBA', (S, S), (0, 0, 0, 0))
s_draw = ImageDraw.Draw(shadow)
s_draw.rounded_rectangle([(pad+10, pad+20), (S-pad-10, S-pad+20)], radius=rad, fill=(0, 0, 0, 160))
shadow = shadow.filter(ImageFilter.GaussianBlur(radius=25))
master.paste(shadow, (0, 0), shadow)

# Dark luxury tile with gold stroke
t_draw.rounded_rectangle(
    [(pad, pad), (S-pad, S-pad)],
    radius=rad,
    fill=(10, 10, 12, 255),
    outline=(212, 175, 55, 230), # #D4AF37 gold border
    width=16
)
master.paste(tile, (0, 0), tile)

# Scale emblem into center of master tile
emb_master_w = 720
emb_master_h = int(emblem.height * (emb_master_w / emblem.width))
emblem_master = emblem.resize((emb_master_w, emb_master_h), Image.Resampling.LANCZOS)

pos_x = (S - emb_master_w) // 2
pos_y = (S - emb_master_h) // 2 - 10
master.paste(emblem_master, (pos_x, pos_y), emblem_master)
master.save('public/images/logo-icon.png')

# Multi-resolution favicons
icon_16 = master.resize((16, 16), Image.Resampling.LANCZOS)
icon_32 = master.resize((32, 32), Image.Resampling.LANCZOS)
icon_48 = master.resize((48, 48), Image.Resampling.LANCZOS)
icon_180 = master.resize((180, 180), Image.Resampling.LANCZOS)
icon_192 = master.resize((192, 192), Image.Resampling.LANCZOS)
icon_512 = master.resize((512, 512), Image.Resampling.LANCZOS)

icon_16.save('public/favicon-16x16.png')
icon_32.save('public/favicon-32x32.png')
icon_48.save('public/favicon-48x48.png')
icon_180.save('public/apple-touch-icon.png')
icon_192.save('public/android-chrome-192x192.png')
icon_512.save('public/android-chrome-512x512.png')

icon_32.save(
    'public/favicon.ico',
    format='ICO',
    sizes=[(16, 16), (32, 32), (48, 48), (64, 64)]
)

# Favicon SVG: 100x100 squircle tile
with open('public/favicon-48x48.png', 'rb') as f:
    fav_b64 = base64.b64encode(f.read()).decode('utf-8')

favicon_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <image href="data:image/png;base64,{fav_b64}" x="0" y="0" width="100" height="100"/>
</svg>'''

with open('public/favicon.svg', 'w', encoding='utf-8') as f:
    f.write(favicon_svg)

print("Brand assets generated successfully from exact user upload!")
