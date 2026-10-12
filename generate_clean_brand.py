import os
import base64
import numpy as np
from PIL import Image, ImageDraw, ImageFilter

src_path = r'C:\Users\yigit\.gemini\antigravity\brain\8741e97c-0c3a-4926-9b74-b985af34cb00\.user_uploaded\media_1791487941992_cd98e223.png'
im_raw = Image.open(src_path).convert('RGBA')

# 1. Clean emblem crop: x=[134, 289], y=[18, 95]
emblem = im_raw.crop((134, 18, 289, 95)) # 155 x 77

# 2. Clean text crop: x=[54, 92], y=[368, 168]
text_crop = im_raw.crop((54, 92, 368, 168)) # 314 x 76
text_arr = np.array(text_crop)
# Clear any petal tip above text (y < 4 in text_crop, x > 150)
for y in range(4):
    for x in range(150, text_arr.shape[1]):
        text_arr[y, x] = [0, 0, 0, 255]
text_clean = Image.fromarray(text_arr)

# 3. Create High-Res Horizontal Dark Capsule Logo for Header (W=440, H=90)
H = 90
emb_h = 64
emb_w = int(emblem.width * (emb_h / emblem.height)) # ~129 px
emb_scaled = emblem.resize((emb_w, emb_h), Image.Resampling.LANCZOS)

txt_h = 60
txt_w = int(text_clean.width * (txt_h / text_clean.height)) # ~248 px
txt_scaled = text_clean.resize((txt_w, txt_h), Image.Resampling.LANCZOS)

pad_l = 22
pad_r = 24
gap = 16
W = pad_l + emb_w + gap + txt_w + pad_r # ~439 px -> 440
rad = 18

horiz_dark = Image.new('RGBA', (W, H), (0, 0, 0, 0))
draw = ImageDraw.Draw(horiz_dark)
# Pure black fill with luxury gold border #D4AF37
draw.rounded_rectangle([(1, 1), (W-2, H-2)], radius=rad, fill=(0, 0, 0, 255), outline=(212, 175, 55, 230), width=2)

y_emb = (H - emb_h) // 2
horiz_dark.paste(emb_scaled, (pad_l, y_emb))

y_txt = (H - txt_h) // 2
horiz_dark.paste(txt_scaled, (pad_l + emb_w + gap, y_txt))

os.makedirs('public/images', exist_ok=True)
horiz_dark.save('public/images/logo-horizontal-dark.png')
horiz_dark.save('public/images/logo.png')

# 4. Create Seamless Borderless Transparent Horizontal Logo for dark backgrounds (Footer & Admin)
H_white = 80
emb_h_w = 60
emb_w_w = int(emblem.width * (emb_h_w / emblem.height))
emb_scaled_w = emblem.resize((emb_w_w, emb_h_w), Image.Resampling.LANCZOS)

txt_h_w = 56
txt_w_w = int(text_clean.width * (txt_h_w / text_clean.height))
txt_scaled_w = text_clean.resize((txt_w_w, txt_h_w), Image.Resampling.LANCZOS)

pad_w = 8
gap_w = 14
W_white = pad_w * 2 + emb_w_w + gap_w + txt_w_w

canvas_white = Image.new('RGBA', (W_white, H_white), (0, 0, 0, 255))
canvas_white.paste(emb_scaled_w, (pad_w, (H_white - emb_h_w) // 2))
canvas_white.paste(txt_scaled_w, (pad_w + emb_w_w + gap_w, (H_white - txt_h_w) // 2))

arr_w = np.array(canvas_white, dtype=np.float32)
br_w = np.max(arr_w[:, :, :3], axis=2)
alpha_w = np.clip((br_w - 10.0) / 28.0 * 255.0, 0.0, 255.0)
arr_w[:, :, 3] = alpha_w
horiz_trans = Image.fromarray(arr_w.astype(np.uint8), mode='RGBA')
horiz_trans.save('public/images/logo-horizontal-transparent.png')
horiz_trans.save('public/images/logo-white.png')

# 5. Base64 encode for SVGs
with open('public/images/logo.png', 'rb') as f:
    dark_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-white.png', 'rb') as f:
    trans_b64 = base64.b64encode(f.read()).decode('utf-8')

# Stacked clean logo
clean_stacked = im_raw.crop((0, 0, 429, 168))
clean_stacked.save('public/images/logo-stacked-dark.png')
clean_stacked.save('public/images/logo-dark.png')
clean_stacked.save('public/images/logo-original.png')

with open('public/images/logo-stacked-dark.png', 'rb') as f:
    stacked_b64 = base64.b64encode(f.read()).decode('utf-8')

# 6. Write SVGs
# Main Header Logo SVG (Vector wrapper for crisp scaling)
logo_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}">
  <image href="data:image/png;base64,{dark_b64}" x="0" y="0" width="{W}" height="{H}"/>
</svg>'''
with open('public/images/logo.svg', 'w', encoding='utf-8') as f:
    f.write(logo_svg)

# White/Transparent Logo SVG
logo_white_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W_white} {H_white}" width="{W_white}" height="{H_white}">
  <image href="data:image/png;base64,{trans_b64}" x="0" y="0" width="{W_white}" height="{H_white}"/>
</svg>'''
with open('public/images/logo-white.svg', 'w', encoding='utf-8') as f:
    f.write(logo_white_svg)

# Stacked Logo SVG
stacked_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 429 168" width="429" height="168">
  <rect width="429" height="168" rx="16" fill="#000000" stroke="#D4AF37" stroke-width="1.5"/>
  <image href="data:image/png;base64,{stacked_b64}" x="0" y="0" width="429" height="168"/>
</svg>'''
with open('public/images/logo-stacked.svg', 'w', encoding='utf-8') as f:
    f.write(stacked_svg)

# 7. Generate Favicons (Master 1024x1024 Squircle)
S = 1024
master = Image.new('RGBA', (S, S), (0, 0, 0, 0))
pad = 48
rad_sq = 220
tile = Image.new('RGBA', (S, S), (0, 0, 0, 0))
t_draw = ImageDraw.Draw(tile)

# Drop shadow
shadow = Image.new('RGBA', (S, S), (0, 0, 0, 0))
s_draw = ImageDraw.Draw(shadow)
s_draw.rounded_rectangle([(pad+10, pad+20), (S-pad-10, S-pad+20)], radius=rad_sq, fill=(0, 0, 0, 160))
shadow = shadow.filter(ImageFilter.GaussianBlur(radius=25))
master.paste(shadow, (0, 0), shadow)

# Tile
t_draw.rounded_rectangle(
    [(pad, pad), (S-pad, S-pad)],
    radius=rad_sq,
    fill=(0, 0, 0, 255),
    outline=(212, 175, 55, 230),
    width=16
)
master.paste(tile, (0, 0), tile)

# Emblem in center
emb_master_w = 720
emb_master_h = int(emblem.height * (emb_master_w / emblem.width))
emblem_master = emblem.resize((emb_master_w, emb_master_h), Image.Resampling.LANCZOS)
pos_x = (S - emb_master_w) // 2
pos_y = (S - emb_master_h) // 2
master.paste(emblem_master, (pos_x, pos_y), emblem_master)
master.save('public/images/logo-icon.png')

# Save favicons
master.resize((16, 16), Image.Resampling.LANCZOS).save('public/favicon-16x16.png')
master.resize((32, 32), Image.Resampling.LANCZOS).save('public/favicon-32x32.png')
master.resize((48, 48), Image.Resampling.LANCZOS).save('public/favicon-48x48.png')
master.resize((180, 180), Image.Resampling.LANCZOS).save('public/apple-touch-icon.png')
master.resize((192, 192), Image.Resampling.LANCZOS).save('public/android-chrome-192x192.png')
master.resize((512, 512), Image.Resampling.LANCZOS).save('public/android-chrome-512x512.png')

# Multi-resolution ICO
master.resize((32, 32), Image.Resampling.LANCZOS).save(
    'public/favicon.ico',
    format='ICO',
    sizes=[(16, 16), (32, 32), (48, 48), (64, 64)]
)

with open('public/favicon-48x48.png', 'rb') as f:
    fav_b64 = base64.b64encode(f.read()).decode('utf-8')
favicon_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <image href="data:image/png;base64,{fav_b64}" x="0" y="0" width="100" height="100"/>
</svg>'''
with open('public/favicon.svg', 'w', encoding='utf-8') as f:
    f.write(favicon_svg)

print('Brand assets generation COMPLETE!')
