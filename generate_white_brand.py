import os
import base64
import numpy as np
from PIL import Image, ImageDraw, ImageFilter

src_path = r'C:\Users\yigit\.gemini\antigravity\brain\8741e97c-0c3a-4926-9b74-b985af34cb00\.user_uploaded\media_1791487941992_cd98e223.png'
im_raw = Image.open(src_path).convert('RGBA')

# 1. Clean emblem crop: (134, 18, 289, 95)
emblem = im_raw.crop((134, 18, 289, 95)) # 155 x 77
orig_emb = np.array(emblem)

# Petal mask
petal_mask = (orig_emb[:, :, 0] > 170) & (orig_emb[:, :, 1] > 170) & (orig_emb[:, :, 2] > 170)
# Petal stroke (gold outline)
s_img = Image.fromarray((petal_mask * 255).astype(np.uint8)).filter(ImageFilter.MaxFilter(3))
stroke_mask = (np.array(s_img) > 100)

# Un-multiply black for infinity loop
emb_arr = np.array(emblem, dtype=np.float32)
br = np.max(emb_arr[:, :, :3], axis=2)
alpha = np.clip((br - 8.0) / 40.0, 0.0, 1.0)
mask = alpha > 0.05
for c in range(3):
    emb_arr[mask, c] = np.clip(emb_arr[mask, c] / alpha[mask], 0.0, 255.0)
emb_arr[:, :, 3] = alpha * 255.0

# Transparent canvas for emblem (with solid white petals and gold outline)
emb_clean = Image.new('RGBA', emblem.size, (0, 0, 0, 0))
st_layer = np.zeros((emblem.height, emblem.width, 4), dtype=np.uint8)
st_layer[stroke_mask] = [184, 134, 11, 255] # #B8860B gold outline
emb_clean.paste(Image.fromarray(st_layer), (0, 0), Image.fromarray(st_layer))
# Infinity loop on top
emb_img = Image.fromarray(emb_arr.astype(np.uint8))
emb_clean.paste(emb_img, (0, 0), emb_img)
# Enforce pure white fill inside petals
for y in range(emblem.height):
    for x in range(emblem.width):
        if petal_mask[y, x]:
            emb_clean.putpixel((x, y), (255, 255, 255, 255))

# 2. Text (54, 92, 368, 168)
text_crop = im_raw.crop((54, 92, 368, 168))
txt_arr = np.array(text_crop)
for y in range(4):
    for x in range(150, txt_arr.shape[1]):
        txt_arr[y, x] = [0, 0, 0, 255]

br_t = np.max(txt_arr[:, :, :3], axis=2)
text_mask = br_t > 20

t_arr_clean = np.zeros((text_crop.height, text_crop.width, 4), dtype=np.uint8)
for y in range(text_crop.height):
    shine = 1.0 - (y / 58.0) * 0.12
    r_gold = int(210 * shine)
    g_gold = int(168 * shine)
    b_gold = int(72 * shine)
    
    for x in range(text_crop.width):
        if text_mask[y, x]:
            val = float(br_t[y, x])
            alpha_val = min(255, int(val / 100.0 * 255.0))
            if alpha_val < 30:
                continue
            
            if y <= 58: # 'İstanbul Çiçek' -> Brand Gold
                t_arr_clean[y, x] = [r_gold, g_gold, b_gold, alpha_val]
            else: # Second line (y >= 59)
                if x > 240: # 'evi.com' -> Brand Gold
                    t_arr_clean[y, x] = [195, 155, 65, alpha_val]
                else: # ''kurumsal çiçekçiniz'' -> Dark Charcoal #1F2937
                    t_arr_clean[y, x] = [31, 41, 55, alpha_val]

txt_clean = Image.fromarray(t_arr_clean)

# High-Res Master: 2x scale
H = 150 # 75 * 2
emb_h = 116 # 58 * 2
emb_w = int(emb_clean.width * (emb_h / emb_clean.height)) # ~233 px
emb_hi = emb_clean.resize((emb_w, emb_h), Image.Resampling.LANCZOS)

txt_h = 108 # 54 * 2
txt_w = int(txt_clean.width * (txt_h / txt_clean.height)) # ~446 px
txt_hi = txt_clean.resize((txt_w, txt_h), Image.Resampling.LANCZOS)

gap = 28
pad_l = 8
pad_r = 8
W = pad_l + emb_w + gap + txt_w + pad_r # ~723 px

# Pure White Background Master Logo (for Header & Light surfaces)
logo_white_bg = Image.new('RGBA', (W, H), (255, 255, 255, 255))
logo_white_bg.paste(emb_hi, (pad_l, (H - emb_h) // 2), emb_hi)
logo_white_bg.paste(txt_hi, (pad_l + emb_w + gap, (H - txt_h) // 2), txt_hi)

os.makedirs('public/images', exist_ok=True)
logo_white_bg.save('public/images/logo.png')
logo_white_bg.save('public/images/logo-horizontal-white.png')

# Base64 encode for SVG
with open('public/images/logo.png', 'rb') as f:
    white_b64 = base64.b64encode(f.read()).decode('utf-8')

# White Header Logo SVG
logo_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}">
  <image href="data:image/png;base64,{white_b64}" x="0" y="0" width="{W}" height="{H}"/>
</svg>'''

with open('public/images/logo.svg', 'w', encoding='utf-8') as f:
    f.write(logo_svg)

print(f'White background logo generated successfully! W: {W}, H: {H}')
