# KASWA Tech — Company Website

Chemical manufacturing & lab equipment company site for KASWA Tech, with a full MySQL-backed admin panel.

## What's included
- Public site: Home (**full-screen hero slider** + sliding client logos), About, Products (by category), Product detail, Services (**image cards, database-driven**), Contact (saves to DB)
- Floating WhatsApp and LinkedIn buttons on every public page (bottom-right, numbers/links editable in Admin → Site Settings)
- Product photos are displayed uncropped (contain-fit on a soft padded background) on both listing cards and the product detail page, so equipment photos never get cut off regardless of their original size
- Admin panel (`/admin`): login-protected, collapsible sidebar, centered/consistent page layout across every screen, manage Categories (with image), Products (image upload), **Services** (image upload, add/edit/reorder/hide), **Hero Slider** (upload/reorder/edit full-screen homepage slides), **Clients / Logos** (upload/reorder/edit the homepage client strip), Inquiries, Site Settings, change password
- `database/kaswa.sql` — the single, complete database file: full schema + all seed data (categories with images, products, services with images, sample clients, default hero slides with images, default admin, site settings) in one file

## Setup (XAMPP / any LAMP stack)
1. Copy this folder into `htdocs/kaswa` (or your web root).
2. Create the database by importing `database/kaswa.sql` in phpMyAdmin (or `mysql -u root -p kaswa_db < database/kaswa.sql` after creating an empty `kaswa_db` database). This one file creates every table and all seed data.
3. Open `config/db.php` and update `DB_USER` / `DB_PASS` if your MySQL isn't the default XAMPP `root` with no password.
4. Visit `http://localhost/kaswa/` for the public site.
5. Visit `http://localhost/kaswa/admin/` to log in to the admin panel.

## New in this update
- **Category cards now show an image**: the homepage "What We Build" section displays an uploaded image per category (6 original icon-style images ship pre-linked). If a category has no image, it falls back to the original numbered hex badge — nothing else about the section changed.
- **Services are now a real database table with images**: previously hardcoded in `services.php`, services are now stored in a `services` table and manageable from Admin → Services (add/edit/reorder/hide, with image upload). 6 original icon-style images ship pre-linked to the seeded services. Services with no image fall back to the numbered hex badge, same as categories.
- **Single consolidated database file**: `database/kaswa.sql` is now the only SQL file in the project — it contains the complete schema and all seed data in one place (the previous separate upgrade/export files have been removed).
- **Homepage hero shows only real, database-linked images**: the hero slider renders exclusively from whatever image is uploaded per slide in Admin → Hero Slider — no placeholder/fallback graphic is shown. 4 original illustrated images ship pre-linked in `uploads/hero/`.
- **"Trusted By" client logos show the company name too**: every logo in the homepage client strip has its name printed underneath it, always.
- **No address/contact bar on the homepage**: the thin top bar (address, phone, email) is hidden only on the homepage; it still appears on every other page.
- **Perfect product photos**: cards and the product detail page show the full, uncropped photo on a clean padded background instead of a cropped square.
- **Floating WhatsApp / LinkedIn buttons**: appear on every public page. Set the numbers/links under Admin → Site Settings.
- **Admin layout**: every admin page's content is centered with a consistent max-width, and narrower forms are centered on the page instead of hugging the left edge.

## Default admin login
- Username: `admin`
- Password: `KaswaAdmin@123`

**Change this password immediately** after first login (Admin → Password), and update the seeded contact/company details under Admin → Site Settings.

## Notes
- Product images upload to `uploads/products/` — make sure that folder is writable by the web server.
- Contact form submissions are stored in the `inquiries` table and viewable/manageable under Admin → Inquiries.
- Colors/typography follow the green hexagon "TK" logo you supplied (`assets/images/logo.png`).
