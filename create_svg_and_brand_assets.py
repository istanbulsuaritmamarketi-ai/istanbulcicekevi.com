import base64
import os
from PIL import Image

os.makedirs('public/images', exist_ok=True)

# Read image files for base64 encoding
with open('public/images/emblem-clean-transparent.png', 'rb') as f:
    emblem_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-horizontal-dark.png', 'rb') as f:
    horiz_dark_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-horizontal-transparent.png', 'rb') as f:
    horiz_trans_b64 = base64.b64encode(f.read()).decode('utf-8')

with open('public/images/logo-dark.png', 'rb') as f:
    stacked_dark_b64 = base64.b64encode(f.read()).decode('utf-8')

# 1. Favicon SVG (100x100 squircle tile)
favicon_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <rect x="3" y="3" width="94" height="94" rx="22" fill="#0A0A0C" stroke="#D4AF37" stroke-width="2.5"/>
  <image href="data:image/png;base64,{emblem_b64}" x="12" y="26" width="76" height="48" preserveAspectRatio="xMidYMid meet"/>
</svg>'''
with open('public/favicon.svg', 'w', encoding='utf-8') as f:
    f.write(favicon_svg)

# 2. Main Logo SVG (Horizontal layout, 436x80)
logo_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 436 80" width="436" height="80">
  <rect width="436" height="80" rx="16" fill="#0A0A0C" stroke="#D4AF37" stroke-width="1.5"/>
  <image href="data:image/png;base64,{horiz_dark_b64}" x="0" y="0" width="436" height="80"/>
</svg>'''
with open('public/images/logo.svg', 'w', encoding='utf-8') as f:
    f.write(logo_svg)

# 3. Logo White SVG (for dark backgrounds like admin sidebar & footer)
logo_white_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 436 80" width="436" height="80">
  <image href="data:image/png;base64,{horiz_trans_b64}" x="0" y="0" width="436" height="80"/>
</svg>'''
with open('public/images/logo-white.svg', 'w', encoding='utf-8') as f:
    f.write(logo_white_svg)

# 4. Logo Stacked SVG
logo_stacked_svg = f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 429 168" width="429" height="168">
  <rect width="429" height="168" rx="20" fill="#0A0A0C" stroke="#D4AF37" stroke-width="1.5"/>
  <image href="data:image/png;base64,{stacked_dark_b64}" x="0" y="0" width="429" height="168"/>
</svg>'''
with open('public/images/logo-stacked.svg', 'w', encoding='utf-8') as f:
    f.write(logo_stacked_svg)

# Save default logo.png as horizontal logo
Image.open('public/images/logo-horizontal-dark.png').save('public/images/logo.png')
Image.open('public/images/logo-horizontal-transparent.png').save('public/images/logo-white.png')

print("All SVGs and PNG brand assets generated successfully!")
