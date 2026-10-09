#!/usr/bin/env python3
"""Render a destination route image on real satellite imagery (Esri World Imagery).

Usage: python3 scripts/route_map.py <route-slug> [--base]
  --base  write only the stitched base map with a lat/lon grid (for plotting sea waypoints)
Routes are defined in ROUTES below. `path` is the boat track: stop names are
resolved to their coordinates; [lat, lon] pairs are sea waypoints that keep the line off land.
"""
import io, math, os, sys, urllib.request
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CACHE = os.path.join(os.environ.get('TMPDIR', '/tmp'), 'route-tiles')
FONTS = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'fonts')
TILE_URL = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
# Flat canvas map used only as a land/water mask: its sea is one exact colour.
MASK_URL = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}'
MASK_WATER = (208, 207, 212)
OUT_W, OUT_H = 1600, 1000

ROUTES = {
    'limassol': {
        'title': 'Limassol Coastal Route',
        'subtitle': 'Dasoudi to Akrotiri Bay · 6 stops',
        'zoom': 14,
        'bbox': (32.935, 34.535, 33.125, 34.715),  # lon_min, lat_min, lon_max, lat_max
        'stops': [
            # name, lat, lon, swim stop?, label side (l/r/t/b)
            ('Dasoudi Beach',         34.6905, 33.0862, False, 'r'),
            ('Miami Beach',           34.6862, 33.0712, False, 'b'),
            ('Limassol Marina',       34.6680, 33.0395, False, 'l'),
            ("Lady's Mile Beach",     34.6150, 33.0125, True,  'r'),
            ('Cape Gata',             34.5615, 33.0345, False, 'r'),
            ('Akrotiri Bay',          34.5610, 32.9780, True,  'b'),
        ],
        'sea_seed': (34.60, 33.10),
        'path': ['Dasoudi Beach', 'Miami Beach', 'Limassol Marina', (34.650, 33.036), "Lady's Mile Beach", (34.588, 33.029), 'Cape Gata', 'Akrotiri Bay'],
    },
    'ayia-napa': {
        'title': 'Cape Greco Sea Caves & Blue Lagoon',
        'subtitle': 'Ayia Napa Harbour to Nissi Beach · 5 stops',
        'bbox': (33.955, 34.952, 34.100, 34.995),
        'stops': [
            ('Ayia Napa Harbour',     34.9805, 34.0045, False, 't'),
            ('Cape Greco Sea Caves',  34.9625, 34.0735, True,  'l'),
            ('Blue Lagoon',           34.9690, 34.0870, True,  'l'),
            ('Konnos Bay',            34.9835, 34.0735, True,  't'),
            ('Nissi Beach',           34.9845, 33.9680, True,  't'),
        ],
        'sea_seed': (34.955, 34.00),
        'path': ['Ayia Napa Harbour', (34.966, 34.040), (34.957, 34.062), 'Cape Greco Sea Caves', (34.954, 34.080), (34.959, 34.092), 'Blue Lagoon',
                 (34.977, 34.084), 'Konnos Bay', (34.975, 34.086), (34.960, 34.095), (34.951, 34.078), (34.955, 34.060), (34.970, 34.020), (34.978, 33.990), 'Nissi Beach'],
    },
    'paphos': {
        'title': "Paphos Sea Caves & Aphrodite's Rock",
        'subtitle': "Paphos Harbour to Aphrodite's Rock · 4 stops",
        'card': 'tr',
        'bbox': (32.270, 34.640, 32.660, 34.970),
        'stops': [
            ('Paphos Harbour',        34.7530, 32.4020, False, 'l'),
            ('Coral Bay Sea Caves',   34.8840, 32.3270, True,  'l'),
            ('Lara Bay',              34.9520, 32.3040, True,  'l'),
            ("Aphrodite's Rock",      34.6625, 32.6260, True,  'l'),
        ],
        'sea_seed': (34.70, 32.40),
        'path': ['Paphos Harbour', 'Coral Bay Sea Caves', 'Lara Bay', (34.880, 32.300), (34.770, 32.380), (34.700, 32.480), "Aphrodite's Rock",
                 (34.688, 32.495), (34.742, 32.392), 'Paphos Harbour'],
    },
    'latsi': {
        'title': 'Latsi to the Blue Lagoon & Akamas',
        'subtitle': 'Latsi Harbour & the Akamas coast · 4 stops',
        'bbox': (32.290, 35.020, 32.410, 35.100),
        'stops': [
            ('Latsi Harbour',         35.0410, 32.3935, False, 'b'),
            ('Blue Lagoon',           35.0830, 32.3105, True,  'r'),
            ('Akamas Coves',          35.0680, 32.3380, True,  'r'),
            ('Manijin Island',        35.0560, 32.3640, True,  'r'),
        ],
        'sea_seed': (35.08, 32.38),
        'path': ['Latsi Harbour', (35.065, 32.370), 'Blue Lagoon', 'Akamas Coves', 'Manijin Island', 'Latsi Harbour'],
    },
    'larnaca': {
        'title': 'Larnaca & the Zenobia Coast',
        'subtitle': 'Larnaca Marina to Cape Kiti · 4 stops',
        'bbox': (33.560, 34.805, 33.700, 34.925),
        'stops': [
            ('Larnaca Marina',        34.9165, 33.6445, False, 'r'),
            ('Zenobia Wreck',         34.8994, 33.6581, True,  'l'),
            ('Mackenzie Beach',       34.8895, 33.6410, True,  'l'),
            ('Cape Kiti',             34.8150, 33.6080, True,  'r'),
        ],
        'sea_seed': (34.86, 33.67),
        'path': ['Larnaca Marina', 'Zenobia Wreck', 'Mackenzie Beach', (34.875, 33.646), (34.850, 33.634), 'Cape Kiti', (34.850, 33.655), (34.900, 33.668), 'Larnaca Marina'],
    },
    'protaras': {
        'title': 'Protaras & Fig Tree Bay Family Cruise',
        'subtitle': 'Pernera to Cape Greco · 5 stops',
        'card': 'bl',
        'bbox': (34.020, 34.950, 34.110, 35.045),
        'stops': [
            ('Pernera',               35.0330, 34.0500, False, 'r'),
            ('Fig Tree Bay',          35.0125, 34.0610, True,  'r'),
            ('Green Bay',             34.9990, 34.0700, True,  'r'),
            ('Cape Greco',            34.9620, 34.0920, True,  'r'),
            ('Konnos Bay',            34.9835, 34.0735, True,  'l'),
        ],
        'sea_seed': (35.00, 34.10),
        'path': ['Pernera', 'Fig Tree Bay', 'Green Bay', (34.985, 34.090), 'Cape Greco', 'Konnos Bay', (34.995, 34.080), 'Pernera'],
    },
}

def lonlat_to_px(lon, lat, z):
    n = 256 * 2 ** z
    x = (lon + 180) / 360 * n
    y = (1 - math.log(math.tan(math.radians(lat)) + 1 / math.cos(math.radians(lat))) / math.pi) / 2 * n
    return x, y

def tile(z, x, y, url=TILE_URL):
    os.makedirs(CACHE, exist_ok=True)
    p = os.path.join(CACHE, f'{"m" if url == MASK_URL else ""}{z}_{x}_{y}.img')
    if not os.path.exists(p):
        req = urllib.request.Request(url.format(z=z, x=x, y=y), headers={'User-Agent': 'boatrent-route-map/1.0'})
        with urllib.request.urlopen(req, timeout=30) as r, open(p, 'wb') as f:
            f.write(r.read())
    return Image.open(p).convert('RGB')

def pick_zoom(cfg):
    if 'zoom' in cfg: return cfg['zoom']
    lon0, lat0, lon1, lat1 = cfg['bbox']
    for z in range(16, 9, -1):  # highest zoom whose crop stays near output size
        x0, y0 = lonlat_to_px(lon0, lat1, z); x1, y1 = lonlat_to_px(lon1, lat0, z)
        if max((x1 - x0) / OUT_W, (y1 - y0) / OUT_H) <= 1.5: return z
    return 10

def base_map(cfg, url=TILE_URL):
    z = pick_zoom(cfg)
    lon0, lat0, lon1, lat1 = cfg['bbox']
    x0, y0 = lonlat_to_px(lon0, lat1, z)
    x1, y1 = lonlat_to_px(lon1, lat0, z)
    # Expand the shorter side so the crop matches the output aspect ratio.
    w, h = x1 - x0, y1 - y0
    target = OUT_W / OUT_H
    if w / h < target:
        d = (h * target - w) / 2; x0 -= d; x1 += d
    else:
        d = (w / target - h) / 2; y0 -= d; y1 += d
    tx0, ty0, tx1, ty1 = int(x0 // 256), int(y0 // 256), int(x1 // 256), int(y1 // 256)
    canvas = Image.new('RGB', ((tx1 - tx0 + 1) * 256, (ty1 - ty0 + 1) * 256))
    from concurrent.futures import ThreadPoolExecutor
    coords = [(tx, ty) for tx in range(tx0, tx1 + 1) for ty in range(ty0, ty1 + 1)]
    with ThreadPoolExecutor(8) as pool:
        for (tx, ty), t in zip(coords, pool.map(lambda c: tile(z, c[0], c[1], url), coords)):
            canvas.paste(t, ((tx - tx0) * 256, (ty - ty0) * 256))
    ox, oy = tx0 * 256, ty0 * 256
    crop = canvas.crop((int(x0 - ox), int(y0 - oy), int(x1 - ox), int(y1 - oy)))
    scale = OUT_W / crop.width
    img = crop.resize((OUT_W, OUT_H), Image.NEAREST if url == MASK_URL else Image.LANCZOS)
    def proj(lat, lon):
        px, py = lonlat_to_px(lon, lat, z)
        return (px - x0) * scale, (py - y0) * scale
    def unproj(x, y):
        px, py = x / scale + x0, y / scale + y0
        n = 256 * 2 ** z
        lon = px / n * 360 - 180
        lat = math.degrees(math.atan(math.sinh(math.pi * (1 - 2 * py / n))))
        return lat, lon
    return img, proj, unproj

def restyle_sea(sat, mask_img, seed):
    """Replace the (patchy) satellite sea with a clean gradient, keeping real land."""
    from PIL import ImageChops
    m = Image.new('L', mask_img.size, 0)
    px_in, px_out = mask_img.load(), m.load()
    W, H = mask_img.size
    for y in range(H):
        for x in range(W):
            r, g, b = px_in[x, y]
            if abs(r - MASK_WATER[0]) + abs(g - MASK_WATER[1]) + abs(b - MASK_WATER[2]) < 12:
                px_out[x, y] = 128
    # Keep only water connected to the open sea (inland lakes stay as imagery).
    ImageDraw.floodfill(m, (int(seed[0]), int(seed[1])), 255)
    sea = m.point(lambda v: 255 if v == 255 else 0).filter(ImageFilter.MaxFilter(3)).filter(ImageFilter.GaussianBlur(1.2))
    # Deep-to-mid blue gradient + a turquoise shallow band hugging the coast.
    grad = Image.linear_gradient('L').resize((W, H))  # lighter toward the shore (top), deeper offshore
    deep = Image.merge('RGB', [grad.point(lambda v: int(22 - v * 0.05)), grad.point(lambda v: int(96 - v * 0.14)),
                               grad.point(lambda v: int(150 - v * 0.22))])
    land = ImageChops.invert(m.point(lambda v: 255 if v == 255 else 0))
    shallow = land.filter(ImageFilter.MaxFilter(9)).filter(ImageFilter.GaussianBlur(22))
    deep = Image.composite(Image.new('RGB', (W, H), (24, 150, 170)), deep, shallow.point(lambda v: int(v * 0.55)))
    rim = land.filter(ImageFilter.MaxFilter(5)).filter(ImageFilter.GaussianBlur(3))
    deep = Image.composite(Image.new('RGB', (W, H), (120, 215, 220)), deep, rim.point(lambda v: int(v * 0.35)))
    return Image.composite(deep, sat, sea)

def font(name, size, weight=None):
    f = ImageFont.truetype(os.path.join(FONTS, name), size)
    if weight:
        try: f.set_variation_by_axes([weight])
        except Exception: pass
    return f

def catmull(points, steps=16):
    """Smooth the track through its points so the line reads as a boat course."""
    if len(points) < 3: return points
    pts = [points[0]] + points + [points[-1]]
    out = []
    for i in range(1, len(pts) - 2):
        p0, p1, p2, p3 = pts[i - 1], pts[i], pts[i + 1], pts[i + 2]
        for s in range(steps):
            t = s / steps
            out.append(tuple(0.5 * ((2 * p1[k]) + (-p0[k] + p2[k]) * t + (2 * p0[k] - 5 * p1[k] + 4 * p2[k] - p3[k]) * t * t
                                   + (-p0[k] + 3 * p1[k] - 3 * p2[k] + p3[k]) * t ** 3) for k in range(2)))
    out.append(points[-1])
    return out

def dashed(draw, pts, fill, width, dash=22, gap=14):
    acc, on = 0.0, True
    for (xa, ya), (xb, yb) in zip(pts, pts[1:]):
        seg = math.hypot(xb - xa, yb - ya)
        pos = 0.0
        while pos < seg:
            lim = dash if on else gap
            step = min(lim - acc, seg - pos)
            if on:
                t0, t1 = pos / seg, (pos + step) / seg
                draw.line([(xa + (xb - xa) * t0, ya + (yb - ya) * t0), (xa + (xb - xa) * t1, ya + (yb - ya) * t1)], fill=fill, width=width)
            pos += step; acc += step
            if acc >= lim - 1e-6: acc, on = 0.0, not on

def render(slug, base_only=False):
    cfg = ROUTES[slug]
    img, proj, unproj = base_map(cfg)
    if base_only:
        d = ImageDraw.Draw(img)
        f = font('plus-jakarta-sans-latin.ttf', 14, 600)
        for gx in range(0, OUT_W, 100):
            d.line([(gx, 0), (gx, OUT_H)], fill=(255, 255, 0), width=1)
            d.text((gx + 2, 2), f'{unproj(gx, 0)[1]:.3f}', font=f, fill=(255, 255, 0))
        for gy in range(0, OUT_H, 100):
            d.line([(0, gy), (OUT_W, gy)], fill=(255, 255, 0), width=1)
            d.text((2, gy + 2), f'{unproj(0, gy)[0]:.3f}', font=f, fill=(255, 255, 0))
        for i, (name, lat, lon, *_ ) in enumerate(cfg['stops']):
            x, y = proj(lat, lon)
            d.ellipse([x - 6, y - 6, x + 6, y + 6], fill=(255, 0, 0))
            d.text((x + 8, y - 8), f'{i+1} {name}', font=f, fill=(255, 255, 255))
        return img

    img = restyle_sea(img, base_map(cfg, MASK_URL)[0], proj(*cfg['sea_seed']))
    SS = 2  # supersample the overlay for smooth lines
    W, H = OUT_W * SS, OUT_H * SS
    # Gentle grade + vignette so the route pops over the imagery.
    img = Image.blend(img, Image.new('RGB', img.size, (13, 26, 51)), 0.18)
    overlay = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    by_name = {s[0]: (s[1], s[2]) for s in cfg['stops']}
    track = [by_name[p] if isinstance(p, str) else p for p in cfg['path']]
    pts = [tuple(c * SS for c in proj(lat, lon)) for lat, lon in track]
    smooth = catmull(pts)

    glow = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(glow).line(smooth, fill=(18, 164, 201, 150), width=22 * SS // 2, joint='curve')
    glow = glow.filter(ImageFilter.GaussianBlur(8 * SS))
    overlay = Image.alpha_composite(overlay, glow)
    d = ImageDraw.Draw(overlay)
    dashed(d, smooth, (255, 255, 255, 255), 5 * SS, dash=18 * SS, gap=11 * SS)

    # Direction arrows at segment midpoints.
    for a, b in zip(pts, pts[1:]):
        L = math.hypot(b[0] - a[0], b[1] - a[1])
        if L < 90 * SS: continue
        # locate the midpoint on the smoothed curve nearest the chord midpoint
        mx, my = (a[0] + b[0]) / 2, (a[1] + b[1]) / 2
        i = min(range(len(smooth) - 1), key=lambda k: (smooth[k][0] - mx) ** 2 + (smooth[k][1] - my) ** 2)
        (x1, y1), (x2, y2) = smooth[i], smooth[min(i + 2, len(smooth) - 1)]
        ang = math.atan2(y2 - y1, x2 - x1)
        s = 13 * SS
        tri = [(x1 + math.cos(ang) * s, y1 + math.sin(ang) * s),
               (x1 + math.cos(ang + 2.5) * s, y1 + math.sin(ang + 2.5) * s),
               (x1 + math.cos(ang - 2.5) * s, y1 + math.sin(ang - 2.5) * s)]
        d.polygon(tri, fill=(70, 194, 224, 255), outline=(255, 255, 255, 255))

    f_num = font('plus-jakarta-sans-latin.ttf', 22 * SS, 800)
    f_lbl = font('plus-jakarta-sans-latin.ttf', 21 * SS, 700)
    f_tag = font('plus-jakarta-sans-latin.ttf', 13 * SS, 700)
    for i, (name, lat, lon, swim, side) in enumerate(cfg['stops']):
        x, y = proj(lat, lon); x *= SS; y *= SS
        r = 21 * SS
        sh = Image.new('RGBA', (W, H), (0, 0, 0, 0))
        ImageDraw.Draw(sh).ellipse([x - r, y - r + 4 * SS, x + r, y + r + 4 * SS], fill=(0, 0, 0, 140))
        overlay = Image.alpha_composite(overlay, sh.filter(ImageFilter.GaussianBlur(5 * SS)))
        d = ImageDraw.Draw(overlay)
        d.ellipse([x - r, y - r, x + r, y + r], fill=(18, 164, 201, 255), outline=(255, 255, 255, 255), width=3 * SS)
        d.text((x, y + SS), str(i + 1), font=f_num, fill='white', anchor='mm')

        # Label pill
        tag = 'SWIM & SNORKEL' if swim else None
        tw = d.textlength(name, font=f_lbl)
        gw = d.textlength(tag, font=f_tag) if tag else 0
        pad_x, pad_y = 16 * SS, 10 * SS
        bw = max(tw, gw) + pad_x * 2
        bh = (26 * SS if not tag else 46 * SS) + pad_y * 2 - 6 * SS
        gapd = r + 12 * SS
        if side == 'r': bx, by = x + gapd, y - bh / 2
        elif side == 'l': bx, by = x - gapd - bw, y - bh / 2
        elif side == 't': bx, by = x - bw / 2, y - gapd - bh
        else: bx, by = x - bw / 2, y + gapd
        sh = Image.new('RGBA', (W, H), (0, 0, 0, 0))
        ImageDraw.Draw(sh).rounded_rectangle([bx, by + 4 * SS, bx + bw, by + bh + 4 * SS], radius=12 * SS, fill=(0, 0, 0, 120))
        overlay = Image.alpha_composite(overlay, sh.filter(ImageFilter.GaussianBlur(6 * SS)))
        d = ImageDraw.Draw(overlay)
        d.rounded_rectangle([bx, by, bx + bw, by + bh], radius=12 * SS, fill=(13, 26, 51, 225))
        d.text((bx + pad_x, by + pad_y - 2 * SS), name, font=f_lbl, fill='white', anchor='la')
        if tag:
            d.text((bx + pad_x, by + pad_y + 28 * SS), tag, font=f_tag, fill=(30, 206, 182), anchor='la')

    # Title card
    f_t = font('playfair-display-latin.ttf', 40 * SS, 700)
    f_s = font('plus-jakarta-sans-latin.ttf', 17 * SS, 600)
    f_k = font('plus-jakarta-sans-latin.ttf', 13 * SS, 800)
    tx, ty = 40 * SS, 36 * SS
    kick = 'THE ROUTE'
    tw = max(d.textlength(cfg['title'], font=f_t), d.textlength(cfg['subtitle'], font=f_s)) + 56 * SS
    corner = cfg.get('card', 'tl')  # title card corner: tl / tr / bl
    if corner == 'tr': tx = W - 40 * SS - tw
    if corner == 'bl': ty = H - 60 * SS - 142 * SS
    d.rounded_rectangle([tx, ty, tx + tw, ty + 142 * SS], radius=18 * SS, fill=(13, 26, 51, 230))
    d.text((tx + 28 * SS, ty + 22 * SS), kick, font=f_k, fill=(70, 194, 224))
    d.text((tx + 28 * SS, ty + 42 * SS), cfg['title'], font=f_t, fill='white')
    d.text((tx + 28 * SS, ty + 100 * SS), cfg['subtitle'], font=f_s, fill=(231, 247, 250, 210))

    # Scale bar (nautical miles) + north arrow, bottom-right
    lat_c = (cfg['bbox'][1] + cfg['bbox'][3]) / 2
    xa, _ = proj(lat_c, cfg['bbox'][0]); xb, _ = proj(lat_c, cfg['bbox'][0] + 1 / 60 / math.cos(math.radians(lat_c)))
    nm_px = (xb - xa) * SS  # 1 nautical mile = 1 arc-minute of latitude
    nm = min((0.5, 1, 2, 5, 10), key=lambda v: abs(v * nm_px - W / 7))
    sx1, sy = W - 40 * SS, H - 44 * SS
    sx0 = sx1 - nm_px * nm
    d.rounded_rectangle([sx0 - 18 * SS, sy - 40 * SS, sx1 + 18 * SS, sy + 20 * SS], radius=12 * SS, fill=(13, 26, 51, 210))
    d.line([(sx0, sy), (sx1, sy)], fill='white', width=3 * SS)
    for xx in (sx0, (sx0 + sx1) / 2, sx1):
        d.line([(xx, sy - 7 * SS), (xx, sy)], fill='white', width=3 * SS)
    d.text(((sx0 + sx1) / 2, sy - 14 * SS), f'{nm:g} nautical mile{"" if nm == 1 else "s"}', font=f_tag, fill='white', anchor='ms')
    nx, ny = sx0 - 60 * SS, sy - 10 * SS
    d.ellipse([nx - 26 * SS, ny - 30 * SS, nx + 26 * SS, ny + 22 * SS], fill=(13, 26, 51, 210))
    d.polygon([(nx, ny - 22 * SS), (nx - 9 * SS, ny + 6 * SS), (nx, ny), (nx + 9 * SS, ny + 6 * SS)], fill='white')
    d.text((nx, ny + 17 * SS), 'N', font=f_tag, fill='white', anchor='ms')

    f_a = font('plus-jakarta-sans-latin.ttf', 11 * SS, 500)
    d.text((14 * SS, H - 12 * SS), 'Imagery © Esri, Maxar, Earthstar Geographics · Route is indicative', font=f_a, fill=(255, 255, 255, 170), anchor='ls')

    overlay = overlay.resize((OUT_W, OUT_H), Image.LANCZOS)
    out = img.convert('RGBA'); out.alpha_composite(overlay)
    return out.convert('RGB')

if __name__ == '__main__':
    if sys.argv[1] == 'all':
        import subprocess
        for sl in ROUTES:
            subprocess.run([sys.executable, __file__, sl] + sys.argv[2:], check=True)
        sys.exit()
    slug = sys.argv[1]
    base = '--base' in sys.argv
    im = render(slug, base)
    outdir = os.path.join(ROOT, 'images', 'routes')
    os.makedirs(outdir, exist_ok=True)
    path = os.path.join(os.environ.get('OUT_DIR', outdir), f'{slug}{"-base" if base else ""}.' + ('png' if base else 'webp'))
    im.save(path, quality=86, method=6) if not base else im.save(path)
    print(path)
