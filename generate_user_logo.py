import os
from PIL import Image

src_path = r'C:\Users\yigit\.gemini\antigravity\brain\8741e97c-0c3a-4926-9b74-b985af34cb00\.user_uploaded\media_1791487941992_cd98e223.png'
im = Image.open(src_path).convert('RGBA')

# 1. Clean stacked logo
clean_stacked = im.crop((0, 0, 429, 168))
os.makedirs('public/images', exist_ok=True)
clean_stacked.save('public/images/logo-stacked-dark.png')
clean_stacked.save('public/images/logo-dark.png')
clean_stacked.save('public/images/logo-original.png')

# 2. Extract emblem and text crops
emblem = im.crop((55, 18, 290, 98)) # 235 x 80
text = im.crop((53, 99, 370, 167))   # 317 x 68

# 3. Create Horizontal Dark Logo (450 x 80)
W, H = 450, 80
horiz_dark = Image.new('RGBA', (W, H), (10, 10, 12, 255)) # Luxury midnight black

# Scale emblem to height 60
emb_h = 60
emb_w = int(emblem.width * (emb_h / emblem.height))
emblem_scaled = emblem.resize((emb_w, emb_h), Image.Resampling.LANCZOS)

# Scale text to height 52
txt_h = 52
txt_w = int(text.width * (txt_h / text.height))
text_scaled = text.resize((txt_w, txt_h), Image.Resampling.LANCZOS)

# Paste into horizontal canvas
horiz_dark.paste(emblem_scaled, (12, (H - emb_h) // 2))
horiz_dark.paste(text_scaled, (12 + emb_w + 14, (H - txt_h) // 2))

horiz_dark.save('public/images/logo-horizontal-dark.png')
print(f"Horizontal dark created: {horiz_dark.size}, emb: {emb_w}x{emb_h}, txt: {txt_w}x{txt_h}")
