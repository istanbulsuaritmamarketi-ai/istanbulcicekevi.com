import os
from PIL import Image

# 1. Master SVG for Light Background (Header Navbar)
logo_light_svg = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 76" width="360" height="76" fill="none">
  <defs>
    <linearGradient id="roseGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f43f5e"/>
      <stop offset="50%" stop-color="#e11d48"/>
      <stop offset="100%" stop-color="#be123c"/>
    </linearGradient>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fbbf24"/>
      <stop offset="100%" stop-color="#d97706"/>
    </linearGradient>
    <linearGradient id="leafGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#34d399"/>
      <stop offset="100%" stop-color="#059669"/>
    </linearGradient>
    <filter id="glow" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#e11d48" flood-opacity="0.2"/>
    </filter>
  </defs>

  <!-- Flower Emblem Group -->
  <g transform="translate(10, 8)" filter="url(#glow)">
    <!-- Leaves -->
    <path d="M18 42 C10 46 6 56 12 59 C18 62 26 56 26 48 Z" fill="url(#leafGrad)"/>
    <path d="M42 42 C50 46 54 56 48 59 C42 62 34 56 34 48 Z" fill="url(#leafGrad)"/>

    <!-- Stem & Base -->
    <path d="M30 38 Q30 52 30 58" stroke="url(#leafGrad)" stroke-width="3" stroke-linecap="round"/>

    <!-- Outer Petals -->
    <path d="M30 6 C20 18 10 28 16 38 C22 46 38 46 44 38 C50 28 40 18 30 6 Z" fill="url(#roseGrad)" opacity="0.95"/>
    <path d="M14 22 C6 28 8 40 18 44 C26 47 28 38 24 30 C20 24 16 22 14 22 Z" fill="#f43f5e" opacity="0.85"/>
    <path d="M46 22 C54 28 52 40 42 44 C34 47 32 38 36 30 C40 24 44 22 46 22 Z" fill="#f43f5e" opacity="0.85"/>

    <!-- Inner Blossom Layers -->
    <path d="M30 14 C24 22 18 30 22 36 C26 40 34 40 38 36 C42 30 36 22 30 14 Z" fill="url(#roseGrad)"/>
    
    <!-- Rose Heart / Core -->
    <circle cx="30" cy="27" r="5.5" fill="url(#goldGrad)"/>
    <path d="M28 25 Q30 23 32 25 Q33 28 30 30 Q27 28 28 25 Z" fill="#ffffff" opacity="0.75"/>
  </g>

  <!-- Brand Typography -->
  <g transform="translate(76, 0)">
    <!-- Top Micro Tagline -->
    <text x="0" y="24" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="10.5" font-weight="800" fill="#e11d48" letter-spacing="4">İSTANBUL</text>
    
    <!-- Main Brand Title -->
    <text x="0" y="52" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="28" font-weight="900" fill="#0f172a" letter-spacing="-0.5">Çiçek Evi</text>
    
    <!-- Bottom Floral Atelier Subtitle -->
    <text x="1" y="68" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="8.5" font-weight="700" fill="#64748b" letter-spacing="2.8">TAZE ÇİÇEK ATÖLYESİ</text>
  </g>
</svg>'''

# 2. Master SVG for Dark Background (Footer, Admin Sidebar)
logo_dark_svg = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 76" width="360" height="76" fill="none">
  <defs>
    <linearGradient id="roseGradDark" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fb7185"/>
      <stop offset="50%" stop-color="#f43f5e"/>
      <stop offset="100%" stop-color="#e11d48"/>
    </linearGradient>
    <linearGradient id="goldGradDark" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fde68a"/>
      <stop offset="100%" stop-color="#f59e0b"/>
    </linearGradient>
    <linearGradient id="leafGradDark" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#6ee7b7"/>
      <stop offset="100%" stop-color="#10b981"/>
    </linearGradient>
  </defs>

  <!-- Flower Emblem Group -->
  <g transform="translate(10, 8)">
    <!-- Leaves -->
    <path d="M18 42 C10 46 6 56 12 59 C18 62 26 56 26 48 Z" fill="url(#leafGradDark)"/>
    <path d="M42 42 C50 46 54 56 48 59 C42 62 34 56 34 48 Z" fill="url(#leafGradDark)"/>

    <!-- Stem & Base -->
    <path d="M30 38 Q30 52 30 58" stroke="url(#leafGradDark)" stroke-width="3" stroke-linecap="round"/>

    <!-- Outer Petals -->
    <path d="M30 6 C20 18 10 28 16 38 C22 46 38 46 44 38 C50 28 40 18 30 6 Z" fill="url(#roseGradDark)"/>
    <path d="M14 22 C6 28 8 40 18 44 C26 47 28 38 24 30 C20 24 16 22 14 22 Z" fill="#fda4af" opacity="0.85"/>
    <path d="M46 22 C54 28 52 40 42 44 C34 47 32 38 36 30 C40 24 44 22 46 22 Z" fill="#fda4af" opacity="0.85"/>

    <!-- Inner Blossom Layers -->
    <path d="M30 14 C24 22 18 30 22 36 C26 40 34 40 38 36 C42 30 36 22 30 14 Z" fill="#fff" opacity="0.95"/>
    
    <!-- Rose Heart / Core -->
    <circle cx="30" cy="27" r="5.5" fill="url(#goldGradDark)"/>
    <path d="M28 25 Q30 23 32 25 Q33 28 30 30 Q27 28 28 25 Z" fill="#e11d48"/>
  </g>

  <!-- Brand Typography (Light on Dark) -->
  <g transform="translate(76, 0)">
    <!-- Top Micro Tagline -->
    <text x="0" y="24" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="10.5" font-weight="800" fill="#fb7185" letter-spacing="4">İSTANBUL</text>
    
    <!-- Main Brand Title -->
    <text x="0" y="52" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="28" font-weight="900" fill="#ffffff" letter-spacing="-0.5">Çiçek Evi</text>
    
    <!-- Bottom Floral Atelier Subtitle -->
    <text x="1" y="68" font-family="'Figtree', system-ui, -apple-system, sans-serif" font-size="8.5" font-weight="700" fill="#cbd5e1" letter-spacing="2.8">TAZE ÇİÇEK ATÖLYESİ</text>
  </g>
</svg>'''

# 3. Favicon SVG (100x100 App Icon Tile)
favicon_svg = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100" fill="none">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fff1f2"/>
      <stop offset="100%" stop-color="#ffe4e6"/>
    </linearGradient>
    <linearGradient id="favRose" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f43f5e"/>
      <stop offset="50%" stop-color="#e11d48"/>
      <stop offset="100%" stop-color="#be123c"/>
    </linearGradient>
    <linearGradient id="favGold" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fbbf24"/>
      <stop offset="100%" stop-color="#d97706"/>
    </linearGradient>
    <linearGradient id="favLeaf" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#34d399"/>
      <stop offset="100%" stop-color="#059669"/>
    </linearGradient>
  </defs>

  <rect x="2" y="2" width="96" height="96" rx="24" fill="url(#bgGrad)" stroke="#f43f5e" stroke-width="2.5"/>

  <g transform="translate(20, 16)">
    <!-- Leaves -->
    <path d="M18 42 C10 46 6 56 12 59 C18 62 26 56 26 48 Z" fill="url(#favLeaf)"/>
    <path d="M42 42 C50 46 54 56 48 59 C42 62 34 56 34 48 Z" fill="url(#favLeaf)"/>

    <!-- Stem & Base -->
    <path d="M30 38 Q30 52 30 58" stroke="url(#favLeaf)" stroke-width="3" stroke-linecap="round"/>

    <!-- Outer Petals -->
    <path d="M30 6 C20 18 10 28 16 38 C22 46 38 46 44 38 C50 28 40 18 30 6 Z" fill="url(#favRose)"/>
    <path d="M14 22 C6 28 8 40 18 44 C26 47 28 38 24 30 C20 24 16 22 14 22 Z" fill="#fb7185" opacity="0.85"/>
    <path d="M46 22 C54 28 52 40 42 44 C34 47 32 38 36 30 C40 24 44 22 46 22 Z" fill="#fb7185" opacity="0.85"/>

    <!-- Inner Blossom Layers -->
    <path d="M30 14 C24 22 18 30 22 36 C26 40 34 40 38 36 C42 30 36 22 30 14 Z" fill="#fff" opacity="0.95"/>
    
    <!-- Rose Heart / Core -->
    <circle cx="30" cy="27" r="5.5" fill="url(#favGold)"/>
  </g>
</svg>'''

with open('public/images/logo.svg', 'w', encoding='utf-8') as f:
    f.write(logo_light_svg)

with open('public/images/logo-white.svg', 'w', encoding='utf-8') as f:
    f.write(logo_dark_svg)

with open('public/images/logo-stacked.svg', 'w', encoding='utf-8') as f:
    f.write(logo_light_svg)

with open('public/favicon.svg', 'w', encoding='utf-8') as f:
    f.write(favicon_svg)

print("Vector SVGs generated successfully!")
