import os
import sys
import glob

# Auto-include user site-packages so PIL and qrcode work across webserver and CLI users
for site_pkg in glob.glob("/home/*/.local/lib/python*/site-packages") + glob.glob("/root/.local/lib/python*/site-packages") + glob.glob("/var/www/.local/lib/python*/site-packages"):
    if site_pkg not in sys.path and os.path.exists(site_pkg):
        sys.path.insert(0, site_pkg)

import json
import argparse
import qrcode
from PIL import Image, ImageDraw, ImageFont, ImageOps

def find_font(candidates, default_size=40):
    for path in candidates:
        if os.path.exists(path):
            try:
                return ImageFont.truetype(path, default_size)
            except Exception:
                continue
    try:
        return ImageFont.load_default()
    except Exception:
        return None

def get_font(path_list, size):
    for p in path_list:
        if p and os.path.exists(p):
            try:
                return ImageFont.truetype(p, size)
            except Exception:
                pass
    try:
        return ImageFont.load_default(size=size)
    except TypeError:
        return ImageFont.load_default()

def generate_id_card(data):
    template_front_path = data.get("template_front")
    template_back_path = data.get("template_back")
    output_dir = data.get("output_dir")

    os.makedirs(output_dir, exist_ok=True)
    try:
        os.chmod(output_dir, 0o777)
    except Exception:
        pass

    out_front_path = os.path.join(output_dir, "id_card_front.png")
    out_back_path = os.path.join(output_dir, "id_card_back.png")
    out_combined_path = os.path.join(output_dir, "id_card_combined.png")

    script_dir = os.path.dirname(os.path.abspath(__file__))
    bold_fonts = [
        # Linux standard font paths (Debian / Ubuntu / RHEL / CentOS / Alpine)
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
        "/usr/share/fonts/liberation/LiberationSans-Bold.ttf",
        "/usr/share/fonts/truetype/freefont/FreeSansBold.ttf",
        "/usr/share/fonts/truetype/noto/NotoSans-Bold.ttf",
        "/usr/share/fonts/opentype/noto/NotoSansCJK-Bold.ttc",
        "/usr/share/fonts/truetype/ubuntu/Ubuntu-B.ttf",
        "/usr/share/fonts/TTF/DejaVuSans-Bold.ttf",
        # Windows standard font paths
        r"C:\Windows\Fonts\arialbd.ttf",
        r"C:\Windows\Fonts\segoeuib.ttf",
        r"C:\Windows\Fonts\calibrib.ttf",
        # Project local fonts
        os.path.join(script_dir, "fonts", "DejaVuSans-Bold.ttf"),
        os.path.join(os.path.dirname(script_dir), "fonts", "DejaVuSans-Bold.ttf"),
    ]

    # --- 1. FRONT CARD ---
    if not os.path.exists(template_front_path):
        raise FileNotFoundError(f"Front template not found at {template_front_path}")

    front = Image.open(template_front_path).convert("RGBA")
    fdraw = ImageDraw.Draw(front)

    # Photo Box: (570, 884, 1313, 1720) -> w: 743, h: 836
    photo_box = (570, 884, 1313, 1720)
    pw = photo_box[2] - photo_box[0]
    ph = photo_box[3] - photo_box[1]

    photo_path = data.get("photo_path")
    if photo_path and os.path.exists(photo_path):
        try:
            raw_photo = Image.open(photo_path).convert("RGBA")
            photo = ImageOps.fit(raw_photo, (pw, ph), method=Image.Resampling.LANCZOS)
        except Exception:
            photo = None
    else:
        photo = None

    if photo is None:
        # Elegant monogram avatar placeholder
        photo = Image.new("RGBA", (pw, ph), (230, 238, 245, 255))
        pdraw = ImageDraw.Draw(photo)
        name_parts = [p for p in data.get("name", "M").strip().split() if p]
        initials = "".join([part[0].upper() for part in name_parts[:2]]) or "G"
        pfont = get_font(bold_fonts, 220)
        bbox = pdraw.textbbox((0, 0), initials, font=pfont)
        tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
        pdraw.text(((pw - tw) / 2, (ph - th) / 2 - 20), initials, font=pfont, fill=(19, 66, 97, 255))

    # Rounded photo mask (radius 110)
    pmask = Image.new("L", (pw, ph), 0)
    ImageDraw.Draw(pmask).rounded_rectangle([0, 0, pw, ph], radius=110, fill=255)
    front.paste(photo, (photo_box[0], photo_box[1]), pmask)

    # Crisp outer photo border
    fdraw.rounded_rectangle([552, 866, 1331, 1738], radius=125, outline=(19, 66, 97, 255), width=18)

    # Member Name
    raw_name = data.get("name", "GNAT MEMBER").strip().upper()
    # Adapt font size if name is long
    name_size = 84
    if len(raw_name) > 28:
        name_size = 64
    elif len(raw_name) > 22:
        name_size = 72
    name_font = get_font(bold_fonts, name_size)
    nbbox = fdraw.textbbox((0, 0), raw_name, font=name_font)
    nw = nbbox[2] - nbbox[0]
    fdraw.text(((1845 - nw) / 2, 1850), raw_name, font=name_font, fill=(14, 43, 69, 255))

    # Designation Pill
    designation = (data.get("designation") or "MEMBER").strip().upper()
    desig_size = 70
    if len(designation) > 24:
        desig_size = 56
    desig_font = get_font(bold_fonts, desig_size)
    dbbox = fdraw.textbbox((0, 0), designation, font=desig_font)
    dw, dh = dbbox[2] - dbbox[0], dbbox[3] - dbbox[1]
    pill_w = max(dw + 160, 600)
    pill_h = 146
    pill_x1 = (1845 - pill_w) / 2
    pill_y1 = 1995
    pill_x2 = pill_x1 + pill_w
    pill_y2 = pill_y1 + pill_h
    fdraw.rounded_rectangle([pill_x1, pill_y1, pill_x2, pill_y2], radius=73, fill=(14, 53, 92, 255))
    fdraw.text((pill_x1 + (pill_w - dw) / 2, pill_y1 + (pill_h - dh) / 2 - 8), designation, font=desig_font, fill=(255, 255, 255, 255))

    # QR Code
    qr_data = data.get("qr_data") or data.get("member_id", "GNAT-MEMBER")
    qr = qrcode.QRCode(version=1, box_size=10, border=0, error_correction=qrcode.constants.ERROR_CORRECT_M)
    qr.add_data(qr_data)
    qr.make(fit=True)
    qr_img = qr.make_image(fill_color="black", back_color="white").convert("RGBA")
    qr_size = 480
    qr_img = qr_img.resize((qr_size, qr_size), Image.Resampling.NEAREST)
    qr_x = int((1845 - qr_size) / 2)
    qr_y = 2190
    front.paste(qr_img, (qr_x, qr_y))

    # Member ID Code
    member_id = data.get("member_id", "").strip().upper()
    id_font = get_font(bold_fonts, 54)
    ibbox = fdraw.textbbox((0, 0), member_id, font=id_font)
    iw = ibbox[2] - ibbox[0]
    fdraw.text(((1845 - iw) / 2, 2715), member_id, font=id_font, fill=(10, 10, 10, 255))

    front.save(out_front_path, "PNG", optimize=True)

    # --- 2. BACK CARD ---
    if not os.path.exists(template_back_path):
        raise FileNotFoundError(f"Back template not found at {template_back_path}")

    back = Image.open(template_back_path).convert("RGBA")
    bdraw = ImageDraw.Draw(back)

    info_font = get_font(bold_fonts, 64)

    # Blood Group
    blood_group = data.get("blood_group") or "—"
    bg_text = f"BLOOD GROUP: {blood_group}".upper()
    bbox1 = bdraw.textbbox((0, 0), bg_text, font=info_font)
    w1 = bbox1[2] - bbox1[0]
    bdraw.text(((1845 - w1) / 2, 750), bg_text, font=info_font, fill=(14, 43, 69, 255))

    # Mobile
    mobile = data.get("mobile") or "—"
    mob_text = f"MOBILE: {mobile}".upper()
    bbox2 = bdraw.textbbox((0, 0), mob_text, font=info_font)
    w2 = bbox2[2] - bbox2[0]
    bdraw.text(((1845 - w2) / 2, 845), mob_text, font=info_font, fill=(14, 43, 69, 255))

    # RN / RM NO
    rnrm = data.get("rnrm_no") or "—"
    rnrm_text = f"RN / RM NO: {rnrm}".upper()
    bbox3 = bdraw.textbbox((0, 0), rnrm_text, font=info_font)
    w3 = bbox3[2] - bbox3[0]
    bdraw.text(((1845 - w3) / 2, 940), rnrm_text, font=info_font, fill=(14, 43, 69, 255))

    # VALID TILL
    valid_till = data.get("valid_till") or "—"
    valid_text = f"VALID TILL: {valid_till}".upper()
    bbox4 = bdraw.textbbox((0, 0), valid_text, font=info_font)
    w4 = bbox4[2] - bbox4[0]
    bdraw.text(((1845 - w4) / 2, 1035), valid_text, font=info_font, fill=(14, 43, 69, 255))

    back.save(out_back_path, "PNG", optimize=True)

    # --- 3. COMBINED CARD (Side by Side Presentation) ---
    gap = 80
    combined = Image.new("RGBA", (1845 * 2 + gap, 3137), (245, 247, 250, 255))
    combined.paste(front, (0, 0))
    combined.paste(back, (1845 + gap, 0))
    combined.save(out_combined_path, "PNG", optimize=True)

    for fpath in [out_front_path, out_back_path, out_combined_path]:
        if os.path.exists(fpath):
            try:
                os.chmod(fpath, 0o666)
            except Exception:
                pass

    return {
        "success": True,
        "front": out_front_path,
        "back": out_back_path,
        "combined": out_combined_path
    }

def main():
    parser = argparse.ArgumentParser(description="Generate GNAT Member ID Cards")
    parser.add_argument("--payload", help="JSON string payload")
    parser.add_argument("--payload-file", help="Path to JSON file containing payload")
    args = parser.parse_args()

    try:
        if args.payload_file:
            with open(args.payload_file, "r", encoding="utf-8") as f:
                data = json.load(f)
        elif args.payload:
            data = json.loads(args.payload)
        else:
            raise ValueError("Either --payload or --payload-file must be provided")

        result = generate_id_card(data)
        print(json.dumps(result))
        sys.exit(0)
    except Exception as e:
        err_res = {"success": False, "error": str(e)}
        print(json.dumps(err_res))
        sys.exit(1)

if __name__ == "__main__":
    main()
