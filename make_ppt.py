"""
Presentation AL Baraime — Espace Enseignant
Reconstruction complete avec proportions correctes
Logo: 1563x628px, ratio 2.49
"""
from pptx import Presentation
from pptx.util import Inches, Pt, Cm
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
import os

# ── Palette ────────────────────────────────────────────────────────────────────
TEAL      = RGBColor(0x00, 0xAE, 0xAA)
TEAL_DK   = RGBColor(0x00, 0x7E, 0x7A)
TEAL_LT   = RGBColor(0xD4, 0xF1, 0xF0)
PURPLE    = RGBColor(0x4B, 0x28, 0x82)
PURPLE_LT = RGBColor(0xEC, 0xE5, 0xF7)
WHITE     = RGBColor(0xFF, 0xFF, 0xFF)
BG        = RGBColor(0xF4, 0xF6, 0xF8)
DARK      = RGBColor(0x1C, 0x1C, 0x2E)
MID       = RGBColor(0x55, 0x5F, 0x6E)
GREY      = RGBColor(0xB8, 0xC0, 0xCC)
GREY_LT   = RGBColor(0xE8, 0xEC, 0xEF)
GREEN     = RGBColor(0x10, 0x9E, 0x60)
GREEN_LT  = RGBColor(0xD4, 0xF4, 0xE3)
RED       = RGBColor(0xC0, 0x39, 0x2B)
RED_LT    = RGBColor(0xFB, 0xE5, 0xE3)
ORANGE    = RGBColor(0xD6, 0x73, 0x1A)
ORANGE_LT = RGBColor(0xFD, 0xF0, 0xE0)

LOGO      = "/Applications/MAMP/htdocs/lesecolesalbaraime/public/assets/img/logo/logo.png"
LOGO_RATIO = 1563 / 628   # 2.49  width/height

# ── Setup ──────────────────────────────────────────────────────────────────────
prs = Presentation()
prs.slide_width  = Inches(13.33)
prs.slide_height = Inches(7.5)
W  = prs.slide_width    # 12 189 000 EMU  ≈ 33.86 cm
H  = prs.slide_height   #  6 858 000 EMU  ≈ 19.05 cm
BLANK = prs.slide_layouts[6]

# Layout constants (all in EMU via Cm())
HDR_H  = Cm(2.55)    # header height  → logo height = 6/2.49 ≈ 2.41 cm  ✓
FTR_H  = Cm(0.80)
LOGO_W = Cm(6.0)     # → logo height ≈ 2.41 cm, fits in HDR_H
TL_X   = Cm(6.8)     # timeline start x
TL_W   = W - TL_X - Cm(0.3)   # ≈ 26.76 cm for 5 pills
CONT_T = HDR_H + Cm(0.15)     # content top
CONT_B = H - FTR_H - Cm(0.1)  # content bottom
CONT_H = CONT_B - CONT_T      # ≈ 15.55 cm

TL_LABELS = ["Connexion", "Mon Profil", "Conge & Absence", "Demandes Admin", "Recapitulatif"]

# ── Primitives ─────────────────────────────────────────────────────────────────
def Rct(slide, l, t, w, h, fill=None, border=None, bw=Pt(1.2), rnd=False):
    sh = slide.shapes.add_shape(5 if rnd else 1, l, t, w, h)
    if rnd:
        sh.adjustments[0] = 0.05
    if fill:
        sh.fill.solid()
        sh.fill.fore_color.rgb = fill
    else:
        sh.fill.background()
    if border:
        sh.line.color.rgb = border
        sh.line.width = bw
    else:
        sh.line.fill.background()
    return sh

def Txt(slide, text, l, t, w, h,
        sz=13, bold=False, color=DARK, align=PP_ALIGN.LEFT, italic=False):
    tb = slide.shapes.add_textbox(l, t, w, h)
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = align
    r = p.add_run()
    r.text = text
    r.font.size = Pt(sz)
    r.font.bold = bold
    r.font.italic = italic
    r.font.color.rgb = color

def Logo(slide, l, t, w):
    if os.path.exists(LOGO):
        slide.shapes.add_picture(LOGO, l, t, width=w)

# ── Header avec timeline ───────────────────────────────────────────────────────
def header(slide, active=-1):
    """
    Barre blanche en haut.
    Logo a gauche.  Timeline 5 pills a droite (active = 0-4, -1 = aucune).
    Trait teal en bas du header.
    """
    Rct(slide, 0, 0, W, HDR_H, fill=WHITE)
    Rct(slide, 0, HDR_H - Cm(0.07), W, Cm(0.07), fill=TEAL)

    # Logo centre verticalement dans le header
    logo_h = LOGO_W / LOGO_RATIO   # ≈ 2.41 cm
    logo_t = (HDR_H - logo_h) / 2
    Logo(slide, Cm(0.3), logo_t, LOGO_W)

    if active < 0:
        return

    n    = len(TL_LABELS)
    p_w  = TL_W / n           # largeur d'un pill ≈ 5.35 cm
    p_h  = Cm(1.55)
    p_t  = (HDR_H - p_h) / 2  # centre verticalement

    for i, label in enumerate(TL_LABELS):
        px = TL_X + i * p_w

        # Couleur du pill
        if i < active:
            fc, tc, nc = TEAL_DK, WHITE, WHITE
        elif i == active:
            fc, tc, nc = TEAL,    WHITE, WHITE
        else:
            fc, tc, nc = GREY_LT, GREY,  GREY

        Rct(slide, px + Cm(0.1), p_t, p_w - Cm(0.2), p_h, fill=fc, rnd=True)

        # Separateur fleche
        if i < n - 1:
            Rct(slide,
                px + p_w - Cm(0.12), p_t + p_h/2 - Cm(0.05),
                Cm(0.14), Cm(0.10), fill=GREY)

        # Cercle numero
        cr = Cm(0.6)
        cl = px + Cm(0.25)
        ct = p_t + (p_h - cr) / 2
        Rct(slide, cl, ct, cr, cr,
            fill=(TEAL_DK if i < active else (WHITE if i == active else GREY)),
            rnd=True)
        Txt(slide, str(i+1), cl, ct, cr, cr,
            sz=9, bold=True,
            color=(WHITE if i <= active else GREY_LT),
            align=PP_ALIGN.CENTER)

        # Label
        Txt(slide, label,
            px + Cm(1.0), p_t + Cm(0.07), p_w - Cm(1.15), p_h - Cm(0.14),
            sz=9, bold=(i == active), color=tc)

# ── Footer ─────────────────────────────────────────────────────────────────────
def footer(slide, page, total):
    Rct(slide, 0, H - FTR_H, W, FTR_H, fill=PURPLE)
    Txt(slide, "Les ecoles AL Baraime  —  Espace Enseignant",
        Cm(0.8), H - FTR_H + Cm(0.05), W * 0.7, FTR_H - Cm(0.1),
        sz=9, color=WHITE)
    Txt(slide, f"{page} / {total}",
        W - Cm(3.0), H - FTR_H + Cm(0.05), Cm(2.8), FTR_H - Cm(0.1),
        sz=9, color=WHITE, align=PP_ALIGN.RIGHT)

# ── Titre de section ───────────────────────────────────────────────────────────
def sec_title(slide, title, sub=None):
    Txt(slide, title,
        Cm(0.5), CONT_T + Cm(0.1), W - Cm(1), Cm(0.85),
        sz=20, bold=True, color=PURPLE)
    Rct(slide, Cm(0.5), CONT_T + Cm(0.95), Cm(4.0), Cm(0.07), fill=TEAL)
    if sub:
        Txt(slide, sub,
            Cm(0.5), CONT_T + Cm(1.05), W - Cm(1), Cm(0.5),
            sz=11, color=MID, italic=True)

# Debut contenu sous sec_title
BODY_T = CONT_T + Cm(1.65)   # avec sous-titre
BODY_H = CONT_B - BODY_T


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 1 — Titre
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)

# Fond split
Rct(s, 0,      0, W * 0.54, H, fill=WHITE)
Rct(s, W*0.54, 0, W * 0.46, H, fill=TEAL_LT)
Rct(s, W*0.54 - Cm(0.2), 0, Cm(0.2), H, fill=TEAL)

# ── Cote gauche ───────────────────────────────────────────────────────────────
# Logo grand (titre)
logo_h_title = Cm(15.0) / LOGO_RATIO   # ≈ 6.02 cm
Logo(s, Cm(1.2), Cm(1.0), Cm(15.0))

Rct(s, Cm(1.2), Cm(1.0) + logo_h_title + Cm(0.4), Cm(12.0), Cm(0.08), fill=TEAL)

Txt(s, "Votre Espace Numerique",
    Cm(1.2), Cm(1.0) + logo_h_title + Cm(0.7), Cm(16.5), Cm(1.8),
    sz=31, bold=True, color=PURPLE)

Txt(s, "Guide de presentation pour les enseignants",
    Cm(1.2), Cm(1.0) + logo_h_title + Cm(2.4), Cm(16.5), Cm(0.7),
    sz=14, color=MID, italic=True)

# Tags programme
tags = [
    ("1  Connexion",        TEAL),
    ("2  Mon Profil",       PURPLE),
    ("3  Conge & Absence",  TEAL),
    ("4  Demandes Admin",   PURPLE),
]
tag_w  = Cm(7.5)
tag_h  = Cm(0.68)
tag_gap = Cm(0.3)
tag_y0 = Cm(1.0) + logo_h_title + Cm(3.3)

for i, (label, col) in enumerate(tags):
    row = i % 2; col_idx = i // 2
    tx = Cm(1.2) + col_idx * (tag_w + Cm(0.4))
    ty = tag_y0 + row * (tag_h + tag_gap)
    Rct(s, tx, ty, tag_w, tag_h,
        fill=TEAL_LT if col == TEAL else PURPLE_LT, rnd=True)
    Rct(s, tx, ty, Cm(0.55), tag_h, fill=col, rnd=True)
    Rct(s, tx + Cm(0.35), ty, Cm(0.2), tag_h, fill=col)
    Txt(s, "  " + label, tx + Cm(0.1), ty, tag_w - Cm(0.1), tag_h,
        sz=11, bold=True, color=col)

# ── Cote droit : infos pratiques ──────────────────────────────────────────────
bx = W * 0.54 + Cm(0.9)
bw = W * 0.46 - Cm(1.5)
by = Cm(1.2)
bh = H - Cm(2.0)

Rct(s, bx, by, bw, bh, fill=WHITE, rnd=True, border=TEAL_LT, bw=Pt(1.5))

# Top couleur
Rct(s, bx, by, bw, Cm(1.1), fill=TEAL, rnd=True)
Rct(s, bx, by + Cm(0.75), bw, Cm(0.35), fill=TEAL)
Txt(s, "Informations pratiques",
    bx + Cm(0.5), by + Cm(0.12), bw - Cm(0.8), Cm(0.88),
    sz=13, bold=True, color=WHITE)

infos = [
    (TEAL,   "Connexion",  "Votre mail de l'ecole + mot de passe"),
    (TEAL,   "Appareil",   "Telephone, tablette ou ordinateur"),
    (GREEN,  "Langue",     "Interface entierement en francais"),
    (GREEN,  "Support",    "L'administration reste disponible"),
    (PURPLE, "Securite",   "Donnees confidentielles et protegees"),
]
for j, (col, k, v) in enumerate(infos):
    iy = by + Cm(1.4) + j * Cm(1.1)
    Rct(s, bx + Cm(0.45), iy + Cm(0.2), Cm(0.2), Cm(0.6), fill=col, rnd=True)
    Txt(s, k, bx + Cm(0.85), iy,          bw - Cm(1.1), Cm(0.5),  sz=12, bold=True, color=DARK)
    Txt(s, v, bx + Cm(0.85), iy + Cm(0.5), bw - Cm(1.1), Cm(0.45), sz=10, color=MID)

Txt(s, "Septembre 2026",
    bx, H - Cm(1.1), bw, Cm(0.5),
    sz=10, color=GREY, align=PP_ALIGN.CENTER)

footer(s, 1, 7)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 2 — Vue d'ensemble
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=-1)
footer(s, 2, 7)
sec_title(s, "Vue d'ensemble  —  Le parcours en 3 etapes",
             "Tout depuis votre navigateur, avec votre mail de l'ecole")

n3   = 3
cw3  = (W - Cm(1.2) - (n3-1) * Cm(0.6)) / n3
ch3  = BODY_H - Cm(0.2)
cx30 = Cm(0.6)

cards3 = [
    ("Etape 1", "Je me connecte",
     ["Ouvrir le navigateur", "Saisir mon mail", "Entrer mon mot de passe", "Cliquer : Connexion"],
     TEAL, TEAL_DK),
    ("Etape 2", "Je fais ma demande",
     ["Choisir le type de demande", "Remplir les dates", "Ajouter un commentaire", "Envoyer la demande"],
     PURPLE, PURPLE),
    ("Etape 3", "Je recois la reponse",
     ["Notification par mail", "Approuve ou refuse", "Motif indique si refuse", "Historique consulable"],
     GREEN, GREEN),
]

for i, (badge, title, pts, col, col_dk) in enumerate(cards3):
    cx = cx30 + i * (cw3 + Cm(0.6))
    cy = BODY_T

    # Ombre
    Rct(s, cx + Cm(0.12), cy + Cm(0.12), cw3, ch3, fill=GREY_LT, rnd=True)
    # Carte blanche
    Rct(s, cx, cy, cw3, ch3, fill=WHITE, rnd=True)

    # Bandeau couleur haut
    Rct(s, cx, cy, cw3, Cm(2.2), fill=col, rnd=True)
    Rct(s, cx, cy + Cm(1.7), cw3, Cm(0.5), fill=col)

    # Badge etape
    Rct(s, cx + Cm(0.3), cy + Cm(0.3), Cm(2.3), Cm(0.55), fill=WHITE, rnd=True)
    Txt(s, badge, cx + Cm(0.3), cy + Cm(0.28), Cm(2.3), Cm(0.55),
        sz=9, bold=True, color=col, align=PP_ALIGN.CENTER)

    # Titre
    Txt(s, title, cx + Cm(0.35), cy + Cm(1.0), cw3 - Cm(0.6), Cm(1.0),
        sz=16, bold=True, color=WHITE)

    # Points
    for j, pt in enumerate(pts):
        py = cy + Cm(2.5) + j * Cm(0.95)
        Rct(s, cx + Cm(0.55), py + Cm(0.32), Cm(0.2), Cm(0.2), fill=col, rnd=True)
        Txt(s, pt, cx + Cm(0.95), py, cw3 - Cm(1.15), Cm(0.88), sz=13, color=DARK)

    # Fleche
    if i < 2:
        ax = cx + cw3 + Cm(0.1)
        ay = cy + ch3 / 2 - Cm(0.3)
        Rct(s, ax, ay + Cm(0.12), Cm(0.38), Cm(0.08), fill=GREY)
        Rct(s, ax + Cm(0.28), ay, Cm(0.16), Cm(0.32), fill=GREY, rnd=True)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 3 — Connexion
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=0)
footer(s, 3, 7)
sec_title(s, "Etape 1  —  Se connecter a l'application",
             "Utilisez votre adresse mail de l'ecole — le meme que vous utilisez au quotidien")

# Deux colonnes
COL_L_W = W * 0.57
COL_R_X = COL_L_W + Cm(0.5)
COL_R_W = W - COL_R_X - Cm(0.4)
CY      = BODY_T
CH      = BODY_H - Cm(0.1)

# ── Gauche : 5 etapes ─────────────────────────────────────────────────────────
steps = [
    ("1", "Ouvrir le navigateur",           "Chrome, Firefox ou Safari"),
    ("2", "Aller sur l'application",         "Adresse fournie par l'administration"),
    ("3", "Saisir votre mail de l'ecole",    "Ex :  prenom.nom@albaraime.ma"),
    ("4", "Saisir votre mot de passe",       "Fourni par l'administration"),
    ("5", "Cliquer sur  < Se connecter >",   "Vous arrivez sur votre tableau de bord"),
]
sh  = (CH - Cm(0.3)) / len(steps) - Cm(0.28)
sgap = Cm(0.28)
sx  = Cm(0.45)

for i, (num, title, sub) in enumerate(steps):
    ry = CY + i * (sh + sgap)
    if i > 0:
        Rct(s, sx + Cm(0.42), ry - sgap, Cm(0.06), sgap, fill=TEAL_LT)
    # Cercle
    Rct(s, sx + Cm(0.1), ry + (sh - Cm(0.8)) / 2, Cm(0.8), Cm(0.8), fill=TEAL, rnd=True)
    Txt(s, num, sx + Cm(0.1), ry + (sh - Cm(0.8)) / 2, Cm(0.8), Cm(0.8),
        sz=11, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    # Carte
    Rct(s, sx + Cm(1.1), ry, COL_L_W - Cm(1.3), sh,
        fill=WHITE, rnd=True, border=GREY_LT, bw=Pt(1))
    Rct(s, sx + Cm(1.1), ry + Cm(0.15), Cm(0.15), sh - Cm(0.3), fill=TEAL, rnd=True)
    Txt(s, title, sx + Cm(1.45), ry + Cm(0.05), COL_L_W - Cm(2.0), Cm(0.58),
        sz=13, bold=True, color=DARK)
    Txt(s, sub,   sx + Cm(1.45), ry + Cm(0.62), COL_L_W - Cm(2.0), Cm(0.45),
        sz=10, color=MID, italic=True)

# ── Droite : encart A retenir ──────────────────────────────────────────────────
Rct(s, COL_R_X, CY, COL_R_W, CH, fill=WHITE, rnd=True, border=GREY_LT, bw=Pt(1))

Rct(s, COL_R_X, CY, COL_R_W, Cm(1.1), fill=ORANGE, rnd=True)
Rct(s, COL_R_X, CY + Cm(0.7), COL_R_W, Cm(0.4), fill=ORANGE)
Txt(s, "A retenir !",
    COL_R_X + Cm(0.5), CY + Cm(0.12), COL_R_W - Cm(0.7), Cm(0.88),
    sz=14, bold=True, color=WHITE)

tips = [
    (TEAL,   "Votre mail = votre identifiant", "Pas de nouveau compte a creer"),
    (ORANGE, "Mot de passe oublie ?",           "Contactez l'administration"),
    (GREEN,  "Premiere connexion ?",            "Changez votre mot de passe"),
    (TEAL,   "Fonctionne partout",              "Telephone, tablette, ordinateur"),
]
for j, (col, k, v) in enumerate(tips):
    ty_ = CY + Cm(1.4) + j * Cm(1.2)
    Rct(s, COL_R_X + Cm(0.4), ty_ + Cm(0.1), Cm(0.18), Cm(0.72), fill=col, rnd=True)
    Txt(s, k, COL_R_X + Cm(0.75), ty_,          COL_R_W - Cm(1.0), Cm(0.52), sz=12, bold=True, color=DARK)
    Txt(s, v, COL_R_X + Cm(0.75), ty_ + Cm(0.5), COL_R_W - Cm(1.0), Cm(0.45), sz=10, color=MID)

# Encart bas
bot_y = CY + CH - Cm(1.3)
Rct(s, COL_R_X + Cm(0.3), bot_y, COL_R_W - Cm(0.6), Cm(1.05),
    fill=TEAL_LT, rnd=True, border=TEAL, bw=Pt(1))
Txt(s, "En cas de probleme, contactez\nl'administration, nous vous aidons.",
    COL_R_X + Cm(0.6), bot_y + Cm(0.05), COL_R_W - Cm(1.0), Cm(0.95),
    sz=10, color=TEAL_DK)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 4 — Mon Profil
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=1)
footer(s, 4, 7)
sec_title(s, "Etape 2  —  Mon Profil  &  Mes Informations",
             "Consultez votre espace personnel en toute securite")

cards4 = [
    ("Mes Informations",    "Nom, prenom, matricule\nPoste, departement, entree",   TEAL,   TEAL_LT),
    ("Mon Bulletin",        "Salaire brut & deductions\nCNSS, AMO  —  Net a recevoir", PURPLE, PURPLE_LT),
    ("Mon Solde Conges",    "Jours acquis  /  pris\nJours restants  &  historique",  TEAL,   TEAL_LT),
    ("Mes Documents",       "Contrat de travail\nAttestations  &  ordres de mission", PURPLE, PURPLE_LT),
]

n4    = 2
cw4   = (W - Cm(1.0) - Cm(0.5)) / 2   # 2 colonnes
ch4   = (BODY_H - Cm(0.5) - Cm(0.5)) / 2
cx4_0 = Cm(0.5)
cy4_0 = BODY_T

for i, (title, desc, col, bg) in enumerate(cards4):
    row = i // 2
    c   = i  % 2
    cx4 = cx4_0 + c * (cw4 + Cm(0.5))
    cy4 = cy4_0 + row * (ch4 + Cm(0.5))

    # Ombre
    Rct(s, cx4 + Cm(0.1), cy4 + Cm(0.1), cw4, ch4, fill=GREY_LT, rnd=True)
    Rct(s, cx4, cy4, cw4, ch4, fill=WHITE, rnd=True)

    # Trait gauche couleur
    Rct(s, cx4, cy4 + Cm(0.2), Cm(0.25), ch4 - Cm(0.4), fill=col, rnd=True)

    # Badge type
    Rct(s, cx4 + Cm(0.55), cy4 + Cm(0.3), Cm(3.0), ch4 - Cm(0.6), fill=bg, rnd=True)
    Txt(s, title.upper(),
        cx4 + Cm(0.55), cy4 + ch4/2 - Cm(0.5), Cm(3.0), Cm(1.0),
        sz=9, bold=True, color=col, align=PP_ALIGN.CENTER)

    # Titre
    Txt(s, title,
        cx4 + Cm(4.0), cy4 + Cm(0.3), cw4 - Cm(4.3), Cm(0.7),
        sz=15, bold=True, color=DARK)
    Rct(s, cx4 + Cm(4.0), cy4 + Cm(1.0), cw4 - Cm(4.5), Cm(0.05), fill=GREY_LT)
    Txt(s, desc,
        cx4 + Cm(4.0), cy4 + Cm(1.15), cw4 - Cm(4.3), ch4 - Cm(1.4),
        sz=12, color=MID)

# Note bas : lecture seule
note_y = BODY_T + 2 * ch4 + Cm(0.5) + Cm(0.1)
note_h = CONT_B - note_y
Rct(s, Cm(0.5), note_y, W - Cm(1.0), note_h,
    fill=ORANGE_LT, rnd=True, border=ORANGE, bw=Pt(1.2))
Txt(s, "Lecture seule  —  Pour modifier vos informations, contactez directement l'administration.",
    Cm(1.0), note_y + Cm(0.08), W - Cm(2.0), note_h,
    sz=11, bold=True, color=ORANGE)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 5 — Conge & Absence
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=2)
footer(s, 5, 7)
sec_title(s, "Etape 3  —  Demander un Conge ou une Absence",
             "Procedure en ligne simple — reponse par mail sous 24 a 48h")

CL5_W = Cm(9.0)
CR5_X = Cm(0.5) + CL5_W + Cm(0.5)
CR5_W = W - CR5_X - Cm(0.4)
BT5   = BODY_T
BH5   = BODY_H - Cm(0.1)

# ── Gauche : types de conge ───────────────────────────────────────────────────
Rct(s, Cm(0.5), BT5, CL5_W, BH5, fill=WHITE, rnd=True)
Rct(s, Cm(0.5), BT5, CL5_W, Cm(1.1), fill=TEAL, rnd=True)
Rct(s, Cm(0.5), BT5 + Cm(0.7), CL5_W, Cm(0.4), fill=TEAL)
Txt(s, "Types de conges disponibles",
    Cm(0.9), BT5 + Cm(0.12), CL5_W - Cm(0.6), Cm(0.88),
    sz=13, bold=True, color=WHITE)

types5 = [
    (TEAL,   "Conge Annuel",    "Jours de repos legaux annuels"),
    (PURPLE, "Conge Maladie",   "Sur certificat medical"),
    (GREEN,  "Conge Maternite", "Selon legislation en vigueur"),
    (ORANGE, "Sans Solde",      "Non remunere — a justifier"),
    (MID,    "Exceptionnel",    "Mariage, deces, naissance..."),
]
t5h = (BH5 - Cm(1.3)) / len(types5)
for j, (col, name, detail) in enumerate(types5):
    ty5 = BT5 + Cm(1.3) + j * t5h
    Rct(s, Cm(0.8), ty5 + Cm(0.12), Cm(0.55), t5h - Cm(0.24), fill=col, rnd=True)
    Txt(s, name,   Cm(1.55), ty5,              CL5_W - Cm(1.3), Cm(0.55), sz=12, bold=True, color=DARK)
    Txt(s, detail, Cm(1.55), ty5 + Cm(0.53),   CL5_W - Cm(1.3), Cm(0.44), sz=10, color=MID)

# ── Droite : flux de demande ──────────────────────────────────────────────────
Rct(s, CR5_X, BT5, CR5_W, BH5, fill=WHITE, rnd=True)
Rct(s, CR5_X, BT5, CR5_W, Cm(1.1), fill=PURPLE, rnd=True)
Rct(s, CR5_X, BT5 + Cm(0.7), CR5_W, Cm(0.4), fill=PURPLE)
Txt(s, "Comment faire une demande",
    CR5_X + Cm(0.5), BT5 + Cm(0.12), CR5_W - Cm(0.7), Cm(0.88),
    sz=13, bold=True, color=WHITE)

flow5 = [
    "Cliquer sur  < Nouvelle demande >  dans le menu",
    "Choisir le type de conge dans la liste",
    "Selectionner les dates de debut et de fin",
    "Ajouter un commentaire ou justificatif",
    "Valider en cliquant sur  < Envoyer >",
]
fh5   = (BH5 - Cm(2.5)) / len(flow5)
for j, step in enumerate(flow5):
    fy5 = BT5 + Cm(1.3) + j * fh5
    if j > 0:
        Rct(s, CR5_X + Cm(0.62), fy5 - fh5 + (fh5 - Cm(0.72))/2 + Cm(0.72),
            Cm(0.06), fy5 - (BT5 + Cm(1.3) + (j-1)*fh5 + (fh5-Cm(0.72))/2 + Cm(0.72)),
            fill=PURPLE_LT)
    Rct(s, CR5_X + Cm(0.3), fy5 + (fh5 - Cm(0.72))/2, Cm(0.72), Cm(0.72), fill=PURPLE, rnd=True)
    Txt(s, str(j+1),
        CR5_X + Cm(0.3), fy5 + (fh5 - Cm(0.72))/2, Cm(0.72), Cm(0.72),
        sz=11, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    Rct(s, CR5_X + Cm(1.2), fy5 + Cm(0.06), CR5_W - Cm(1.5), fh5 - Cm(0.15),
        fill=PURPLE_LT, rnd=True)
    Txt(s, step, CR5_X + Cm(1.45), fy5 + Cm(0.18), CR5_W - Cm(1.9), fh5 - Cm(0.25),
        sz=12, color=DARK)

# Resultats
res_y5 = BT5 + BH5 - Cm(1.35)
hw5    = (CR5_W - Cm(0.7)) / 2
Rct(s, CR5_X + Cm(0.3), res_y5, hw5, Cm(1.1),
    fill=GREEN_LT, rnd=True, border=GREEN, bw=Pt(1.5))
Txt(s, "APPROUVE — confirmation par mail",
    CR5_X + Cm(0.4), res_y5 + Cm(0.15), hw5 - Cm(0.1), Cm(0.8),
    sz=10, bold=True, color=GREEN, align=PP_ALIGN.CENTER)

Rct(s, CR5_X + Cm(0.3) + hw5 + Cm(0.1), res_y5, hw5, Cm(1.1),
    fill=RED_LT, rnd=True, border=RED, bw=Pt(1.5))
Txt(s, "REFUSE — mail avec le motif",
    CR5_X + Cm(0.5) + hw5 + Cm(0.1), res_y5 + Cm(0.15), hw5 - Cm(0.1), Cm(0.8),
    sz=10, bold=True, color=RED, align=PP_ALIGN.CENTER)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 6 — Demandes Administratives
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=3)
footer(s, 6, 7)
sec_title(s, "Etape 4  —  Demandes Administratives",
             "Demandez vos documents directement depuis l'application")

DOCS = [
    ("Attestation\nde Travail",  "Emploi confirme\nBanque, visa, ambassade",   TEAL,   TEAL_LT),
    ("Attestation\nde Salaire",  "Salaire officiel\nDemarches diverses",        PURPLE, PURPLE_LT),
    ("Attestation\nde Conge",    "Conge confirme\nJours accordes",              TEAL,   TEAL_LT),
    ("Ordre de\nMission",        "Deplacement officiel\nPour l'ecole",          PURPLE, PURPLE_LT),
    ("Autre\nDocument",          "Demande speciale\nA preciser",                GREEN,  GREEN_LT),
]

nd   = len(DOCS)
dw   = (W - Cm(1.0) - (nd-1) * Cm(0.45)) / nd
dh   = BODY_H - Cm(1.25)   # laisser place a la barre info bas
dx0  = Cm(0.5)
dt   = BODY_T

for i, (title, desc, col, bg) in enumerate(DOCS):
    dx = dx0 + i * (dw + Cm(0.45))

    Rct(s, dx + Cm(0.1), dt + Cm(0.1), dw, dh, fill=GREY_LT, rnd=True)
    Rct(s, dx, dt, dw, dh, fill=WHITE, rnd=True)

    # Bandeau haut
    Rct(s, dx, dt, dw, Cm(2.0), fill=col, rnd=True)
    Rct(s, dx, dt + Cm(1.55), dw, Cm(0.45), fill=col)

    # Numero rond
    nr = Cm(1.2)
    nl = dx + (dw - nr) / 2
    nt = dt + Cm(0.15)
    Rct(s, nl, nt, nr, nr, fill=WHITE, rnd=True)
    Txt(s, str(i+1), nl, nt, nr, nr,
        sz=14, bold=True, color=col, align=PP_ALIGN.CENTER)

    # Titre
    Txt(s, title, dx + Cm(0.3), dt + Cm(2.1), dw - Cm(0.5), Cm(1.4),
        sz=13, bold=True, color=col, align=PP_ALIGN.CENTER)

    Rct(s, dx + Cm(0.6), dt + Cm(3.5), dw - Cm(1.2), Cm(0.05), fill=GREY_LT)

    Txt(s, desc, dx + Cm(0.3), dt + Cm(3.65), dw - Cm(0.5), dh - Cm(3.9),
        sz=11, color=MID, align=PP_ALIGN.CENTER)

# Barre info bas
bar_t6 = dt + dh + Cm(0.3)
bar_h6 = CONT_B - bar_t6
Rct(s, Cm(0.5), bar_t6, W - Cm(1.0), bar_h6,
    fill=ORANGE_LT, rnd=True, border=ORANGE, bw=Pt(1.5))
Txt(s, "Delai de traitement : 1 a 3 jours ouvrables  —  Reponse par mail de l'ecole",
    Cm(1.2), bar_t6 + Cm(0.1), W - Cm(2.0), bar_h6,
    sz=12, bold=True, color=ORANGE, align=PP_ALIGN.CENTER)


# ═══════════════════════════════════════════════════════════════════════════════
#  SLIDE 7 — Recapitulatif
# ═══════════════════════════════════════════════════════════════════════════════
s = prs.slides.add_slide(BLANK)
Rct(s, 0, 0, W, H, fill=BG)
header(s, active=4)
footer(s, 7, 7)
sec_title(s, "Recapitulatif  —  Toutes vos fonctionnalites",
             "Vue complete de l'espace enseignant")

TABLE_W = W * 0.60
PANEL_X = Cm(0.5) + TABLE_W + Cm(0.5)
PANEL_W = W - PANEL_X - Cm(0.4)

rows7 = [
    ("1", "Connexion",         "Se connecter avec le mail de l'ecole",                         TEAL),
    ("2", "Mon Profil",        "Consulter infos, contrat et bulletin de salaire",               TEAL),
    ("3", "Conge & Absence",   "Faire une demande et suivre son statut en temps reel",          PURPLE),
    ("4", "Demandes Admin",    "Attestations, ordres de mission et autres documents",           PURPLE),
    ("5", "Notifications",     "Toutes les reponses arrivent sur votre mail de l'ecole",        GREEN),
]

rh7  = (BODY_H - Cm(0.1) - (len(rows7)-1) * Cm(0.28)) / len(rows7)
ry7_0 = BODY_T

for i, (num, label, desc, col) in enumerate(rows7):
    ry7 = ry7_0 + i * (rh7 + Cm(0.28))
    bg7 = TEAL_LT if col == TEAL else (PURPLE_LT if col == PURPLE else GREEN_LT)

    Rct(s, Cm(0.5) + Cm(0.08), ry7 + Cm(0.08), TABLE_W, rh7, fill=GREY_LT, rnd=True)
    Rct(s, Cm(0.5), ry7, TABLE_W, rh7, fill=WHITE, rnd=True)

    # Trait couleur gauche
    Rct(s, Cm(0.5), ry7 + Cm(0.18), Cm(0.22), rh7 - Cm(0.36), fill=col, rnd=True)

    # Numero
    nr7 = Cm(0.82)
    nt7 = ry7 + (rh7 - nr7) / 2
    Rct(s, Cm(0.9), nt7, nr7, nr7, fill=col, rnd=True)
    Txt(s, num, Cm(0.9), nt7, nr7, nr7,
        sz=11, bold=True, color=WHITE, align=PP_ALIGN.CENTER)

    # Badge label
    Rct(s, Cm(1.95), ry7 + (rh7 - Cm(0.7)) / 2, Cm(5.0), Cm(0.7), fill=bg7, rnd=True)
    Txt(s, label, Cm(2.05), ry7 + (rh7 - Cm(0.7)) / 2, Cm(4.8), Cm(0.7),
        sz=11, bold=True, color=col, align=PP_ALIGN.CENTER)

    # Description
    Txt(s, desc, Cm(7.2), ry7 + (rh7 - Cm(0.6)) / 2, TABLE_W - Cm(6.8), Cm(0.6),
        sz=11, color=MID)

# Panneau droit
ph7 = BODY_H - Cm(0.1)
Rct(s, PANEL_X, BODY_T, PANEL_W, ph7, fill=WHITE, rnd=True, border=GREY_LT, bw=Pt(1))

logo_pw  = PANEL_W - Cm(1.0)
logo_ph  = logo_pw / LOGO_RATIO
Logo(s, PANEL_X + Cm(0.5), BODY_T + Cm(0.4), logo_pw)

sep_y7 = BODY_T + Cm(0.4) + logo_ph + Cm(0.4)
Rct(s, PANEL_X + Cm(0.5), sep_y7, PANEL_W - Cm(1.0), Cm(0.06), fill=TEAL)

Txt(s, "Des questions ?",
    PANEL_X + Cm(0.3), sep_y7 + Cm(0.3), PANEL_W - Cm(0.5), Cm(0.7),
    sz=13, bold=True, color=PURPLE, align=PP_ALIGN.CENTER)
Txt(s, "Contactez l'administration,\nnous sommes la pour vous aider !",
    PANEL_X + Cm(0.3), sep_y7 + Cm(1.1), PANEL_W - Cm(0.5), Cm(1.2),
    sz=11, color=MID, align=PP_ALIGN.CENTER)

btn_y7 = BODY_T + ph7 - Cm(1.05)
Rct(s, PANEL_X + Cm(0.5), btn_y7, PANEL_W - Cm(1.0), Cm(0.85), fill=TEAL, rnd=True)
Txt(s, "Merci de votre confiance !",
    PANEL_X + Cm(0.5), btn_y7, PANEL_W - Cm(1.0), Cm(0.85),
    sz=11, bold=True, color=WHITE, align=PP_ALIGN.CENTER)


# ── Save ───────────────────────────────────────────────────────────────────────
OUT = "/Applications/MAMP/htdocs/gestion-hr/Presentation_AL_Baraime_Enseignants.pptx"
prs.save(OUT)
print(f"Saved -> {OUT}")
