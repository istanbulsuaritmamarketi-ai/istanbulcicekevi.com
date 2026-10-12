import os
from PIL import Image, ImageDraw, ImageFont

def create_brand_pngs():
    W, H = 720, 152
    
    # 1. Light Logo (Transparent PNG for light background)
    im_light = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    draw_light = ImageDraw.Draw(im_light)
    
    # Draw flower emblem on the left
    cx, cy = 68, 76
    
    # Stem
    draw_light.line([(cx, cy + 8), (cx, cy + 42)], fill=(5, 150, 105, 255), width=6)
    
    # Leaves
    draw_light.polygon([(cx - 8, cy + 18), (cx - 36, cy + 30), (cx - 24, cy + 44), (cx, cy + 32)], fill=(52, 211, 153, 255))
    draw_light.polygon([(cx + 8, cy + 18), (cx + 36, cy + 30), (cx + 24, cy + 44), (cx, cy + 32)], fill=(5, 150, 105, 255))
    
    # Outer petals
    draw_light.ellipse([cx - 42, cy - 50, cx + 42, cy + 24], fill=(225, 29, 72, 255))
    draw_light.ellipse([cx - 56, cy - 24, cx - 12, cy + 20], fill=(244, 63, 94, 230))
    draw_light.ellipse([cx + 12, cy - 24, cx + 56, cy + 20], fill=(244, 63, 94, 230))
    
    # Inner rose blossom
    draw_light.ellipse([cx - 26, cy - 36, cx + 26, cy + 12], fill=(255, 241, 242, 240))
    draw_light.ellipse([cx - 16, cy - 26, cx + 16, cy + 4], fill=(225, 29, 72, 255))
    # Core
    draw_light.ellipse([cx - 9, cy - 18, cx + 9, cy - 2], fill=(251, 191, 36, 255))

    # Text rendering: try to load a nice font, otherwise default
    font_large = None
    font_small = None
    font_sub = None
    try:
        font_large = ImageFont.truetype('arialbd.ttf', 54)
        font_small = ImageFont.truetype('arialbd.ttf', 20)
        font_sub = ImageFont.truetype('arial.ttf', 17)
    except:
        try:
            font_large = ImageFont.truetype('DejaVuSans-Bold.ttf', 54)
            font_small = ImageFont.truetype('DejaVuSans-Bold.ttf', 20)
            font_sub = ImageFont.truetype('DejaVuSans.ttf', 17)
        except:
            font_large = ImageFont.load_default()
            font_small = ImageFont.load_default()
            font_sub = ImageFont.load_default()

    draw_light.text((148, 16), "İ S T A N B U L", fill=(225, 29, 72, 255), font=font_small)
    draw_light.text((146, 42), "Çiçek Evi", fill=(15, 23, 42, 255), font=font_large)
    draw_light.text((148, 110), "TAZE ÇİÇEK ATÖLYESİ", fill=(100, 116, 139, 255), font=font_sub)

    im_light.save('public/images/logo.png')
    im_light.save('public/images/logo-horizontal-transparent.png')

    # 2. White Logo (Transparent PNG with white/rose text for dark background)
    im_dark = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    draw_dark = ImageDraw.Draw(im_dark)
    
    # Stem
    draw_dark.line([(cx, cy + 8), (cx, cy + 42)], fill=(16, 185, 129, 255), width=6)
    
    # Leaves
    draw_dark.polygon([(cx - 8, cy + 18), (cx - 36, cy + 30), (cx - 24, cy + 44), (cx, cy + 32)], fill=(110, 231, 183, 255))
    draw_dark.polygon([(cx + 8, cy + 18), (cx + 36, cy + 30), (cx + 24, cy + 44), (cx, cy + 32)], fill=(16, 185, 129, 255))
    
    # Outer petals
    draw_dark.ellipse([cx - 42, cy - 50, cx + 42, cy + 24], fill=(244, 63, 94, 255))
    draw_dark.ellipse([cx - 56, cy - 24, cx - 12, cy + 20], fill=(251, 113, 133, 230))
    draw_dark.ellipse([cx + 12, cy - 24, cx + 56, cy + 20], fill=(251, 113, 133, 230))
    
    # Inner rose blossom
    draw_dark.ellipse([cx - 26, cy - 36, cx + 26, cy + 12], fill=(255, 255, 255, 240))
    draw_dark.ellipse([cx - 16, cy - 26, cx + 16, cy + 4], fill=(244, 63, 94, 255))
    # Core
    draw_dark.ellipse([cx - 9, cy - 18, cx + 9, cy - 2], fill=(253, 230, 138, 255))

    draw_dark.text((148, 16), "İ S T A N B U L", fill=(251, 113, 133, 255), font=font_small)
    draw_dark.text((146, 42), "Çiçek Evi", fill=(255, 255, 255, 255), font=font_large)
    draw_dark.text((148, 110), "TAZE ÇİÇEK ATÖLYESİ", fill=(203, 213, 225, 255), font=font_sub)

    im_dark.save('public/images/logo-white.png')
    im_dark.save('public/images/logo-white-transparent.png')

    print("Raster PNGs generated successfully!")

if __name__ == '__main__':
    create_brand_pngs()
