-- =========================================================
-- KASWA Tech Database Schema
-- Chemical Manufacturing & Lab Equipment company website
-- =========================================================

CREATE DATABASE IF NOT EXISTS kaswa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kaswa_db;

-- -----------------------------------------------------
-- Admin users
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin login: username = admin | password = KaswaAdmin@123
-- (hash generated with PHP password_hash, bcrypt)
INSERT INTO admin_users (name, username, email, password, role) VALUES
('KASWA Administrator', 'admin', 'info@kaswatech.net', '$2y$10$GEi5mUDLnW4UESraohuRletNgFMnmQ6eomVg46FNnwlqvCApeRKl2', 'super_admin');

-- -----------------------------------------------------
-- Password reset OTP codes (used by forgot-password.php
-- and reset-password.php for the "forgot your password" flow)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Categories
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'flask',
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, description, icon, image, sort_order) VALUES
('Chemical Manufacturing Plants', 'chemical-manufacturing-plants', 'Turnkey reactors, vessels and process lines for bulk and specialty chemical production.', 'reactor', 'category-1-chemical-plants.jpg', 1),
('Lab & Analytical Instruments', 'lab-analytical-instruments', 'Precision instruments for quality control, R&D and analytical testing laboratories.', 'microscope', 'category-2-lab-instruments.jpg', 2),
('Filtration & Separation Systems', 'filtration-separation-systems', 'Industrial filter presses, centrifuges and separation units for process purification.', 'filter', 'category-3-filtration.jpg', 3),
('Mixing, Blending & Milling', 'mixing-blending-milling', 'Ribbon blenders, colloid mills and homogenizers for consistent formulation.', 'blend', 'category-4-mixing.jpg', 4),
('Drying & Thermal Equipment', 'drying-thermal-equipment', 'Ovens, dryers and furnaces engineered for controlled thermal processing.', 'thermometer', 'category-5-drying.jpg', 5),
('Packaging & Filling Machinery', 'packaging-filling-machinery', 'Automated filling, capping and packaging lines for finished chemical and pharma products.', 'package', 'category-6-packaging.jpg', 6);

-- -----------------------------------------------------
-- Products
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    model_no VARCHAR(100),
    short_description VARCHAR(300),
    description TEXT,
    specifications TEXT,
    image VARCHAR(255) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Product gallery images (a product can have many photos;
-- products.image continues to hold the main/cover photo used
-- on cards and listings for backward compatibility)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO products (category_id, name, slug, model_no, short_description, description, specifications, is_featured) VALUES
(1, 'Stainless Steel Reactor Vessel', 'stainless-steel-reactor-vessel-500l', 'KSW-RV500', 'Jacketed SS316 reactor for controlled chemical synthesis.', 'A fully jacketed stainless steel reactor vessel built for consistent temperature-controlled synthesis, suitable for chemical, pharmaceutical and specialty formulation batches.', 'Capacity: 500 L | Material: SS316L | Jacket: Steam/Hot Oil | Agitator: Anchor type | Max Pressure: 3 bar', 1),
(1, 'Glass Lined Reaction Vessel', 'glass-lined-reaction-vessel-1000l', 'KSW-GLR1000', 'Corrosion-resistant glass-lined vessel for aggressive media.', 'Engineered for reactions involving corrosive acids and solvents, with a fused glass lining that resists chemical attack and simplifies cleaning between batches.', 'Capacity: 1000 L | Lining: Glass (ASTM C-635) | Agitator Speed: 20-100 RPM | Jacket Pressure: 6 bar', 0),
(1, 'Continuous Process Skid', 'continuous-process-skid-cps200', 'KSW-CPS200', 'Modular skid-mounted continuous processing line.', 'A compact, skid-mounted continuous manufacturing unit that reduces batch cycle time and footprint for mid-scale chemical production.', 'Throughput: 200 L/hr | Control: PLC/HMI | Construction: SS304/SS316 | Footprint: 3m x 2m', 1),
(2, 'Digital pH Meter', 'digital-ph-meter-ph200', 'KSW-PH200', 'Bench-top precision pH and conductivity meter.', 'A laboratory-grade digital meter for accurate pH, conductivity and TDS measurement, with automatic temperature compensation.', 'Range: 0-14 pH | Accuracy: ±0.01 pH | Display: LCD | Calibration: Auto 3-point', 1),
(2, 'Rotary Evaporator', 'rotary-evaporator-re501', 'KSW-RE501', 'Vacuum distillation unit for solvent recovery.', 'Used for efficient, gentle solvent removal in R&D and pilot-scale labs, with digital speed and temperature control.', 'Flask Capacity: 1-5 L | Rotation Speed: 20-180 RPM | Bath Temp: up to 180°C', 0),
(2, 'Muffle Furnace', 'muffle-furnace-mf1200', 'KSW-MF1200', 'High-temperature furnace for ashing and material testing.', 'A programmable muffle furnace built for consistent high-temperature testing, ashing and heat treatment applications.', 'Max Temp: 1200°C | Chamber: 5 L | Control: PID Programmable | Insulation: Ceramic fiber', 0),
(2, 'Analytical Balance', 'analytical-balance-ab220', 'KSW-AB220', 'High-precision balance for laboratory weighing.', 'Delivers repeatable, high-precision readings for formulation and quality-control weighing tasks.', 'Capacity: 220 g | Readability: 0.1 mg | Calibration: Internal automatic', 0),
(3, 'Plate & Frame Filter Press', 'plate-frame-filter-press-fp50', 'KSW-FP50', 'Manual/hydraulic filter press for solid-liquid separation.', 'Designed for efficient dewatering and clarification of process slurries across chemical and pharma production.', 'Plate Size: 470 x 470 mm | Plates: 20-50 | Cake Capacity: up to 150 L | Closing: Hydraulic', 1),
(3, 'Basket Centrifuge', 'basket-centrifuge-bc300', 'KSW-BC300', 'Top-discharge basket centrifuge for crystal separation.', 'Suited for separating crystals and solids from mother liquor with minimal product loss.', 'Basket Diameter: 800 mm | Capacity: 300 L | Speed: up to 1200 RPM | Drive: VFD controlled', 0),
(4, 'Ribbon Blender', 'ribbon-blender-rb500', 'KSW-RB500', 'Horizontal ribbon blender for dry powder mixing.', 'Achieves fast, uniform blending of dry powders and granules for chemical and pharmaceutical formulations.', 'Capacity: 500 L | Mixing Time: 8-15 min | Discharge: Bottom valve | Construction: SS304', 1),
(4, 'Colloid Mill', 'colloid-mill-cm150', 'KSW-CM150', 'Wet grinding mill for fine particle reduction.', 'Produces fine, stable emulsions and suspensions through precision rotor-stator wet milling.', 'Throughput: 150-500 L/hr | Gap Adjustment: Micrometric | Motor: 5.5 kW', 0),
(4, 'High Shear Homogenizer', 'high-shear-homogenizer-hs100', 'KSW-HS100', 'In-line homogenizer for emulsions and dispersions.', 'Delivers consistent particle size reduction for creams, emulsions and chemical dispersions at production scale.', 'Flow Rate: up to 100 L/min | Speed: 3000-10000 RPM | Seal: Mechanical', 0),
(5, 'Fluid Bed Dryer', 'fluid-bed-dryer-fbd150', 'KSW-FBD150', 'Rapid, uniform granule drying system.', 'Uses fluidization technology for fast, even drying of granules with minimal thermal degradation.', 'Batch Capacity: 150 kg | Air Volume: 3000 CFM | Temp Range: 40-120°C', 1),
(5, 'Hot Air Oven', 'hot-air-oven-hao500', 'KSW-HAO500', 'Tray-type industrial drying oven.', 'A reliable tray dryer for consistent moisture removal across pharmaceutical and chemical intermediates.', 'Trays: 24 | Temp Range: 50-250°C | Air Circulation: Forced convection', 0),
(6, 'Automatic Liquid Filling Machine', 'automatic-liquid-filling-machine-lf24', 'KSW-LF24', 'Multi-head volumetric filling line.', 'A high-speed filling line for liquids and viscous chemical products, with adjustable fill volume and head count.', 'Heads: 24 | Speed: up to 60 bottles/min | Fill Range: 50ml-5L | Accuracy: ±1%', 1),
(6, 'Automatic Capping Machine', 'automatic-capping-machine-cm12', 'KSW-CM12', 'In-line cap tightening and sealing unit.', 'Ensures consistent torque and seal quality across bottle and container formats.', 'Speed: up to 80 caps/min | Cap Size Range: 20-100mm | Control: PLC touchscreen', 0);

-- -----------------------------------------------------
-- Inquiries (contact form submissions)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(150),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    product_id INT DEFAULT NULL,
    status ENUM('new','in_progress','resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Site settings (key/value store for editable content)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('company_name', 'KASWA Tech'),
('tagline', 'Engineering Chemical & Laboratory Solutions'),
('corporate_address', 'Delwar Complex [3rd Floor] Room no: 05, 26, Shahid Nazrul Islam Sarak, Hatkhola, Dhaka-1203, Bangladesh'),
('registered_address', '251/1, Uttar Datta Para (Chanki), Arshad Nagar, Tongi East, Gazipur City Corporation, Bangladesh'),
('phone_1', '+88 01670974843'),
('phone_2', '+88 01741641318'),
('email', 'info@kaswatech.net'),
('website', 'www.kaswatech.net'),
('about_text', 'KASWA Tech designs, manufactures and supplies chemical processing equipment and laboratory instruments engineered for reliability, precision and compliance. From reactor vessels to analytical instruments, every system we build is backed by in-house engineering and local after-sales support across Bangladesh.');

-- -----------------------------------------------------
-- Services (shown on the Services page)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO services (title, description, image, sort_order) VALUES
('Custom Equipment Design', 'We engineer reactors, skids and instruments around your exact batch size, media and layout constraints.', 'service-1-design.jpg', 1),
('Fabrication & Manufacturing', 'In-house SS304/SS316 fabrication, welding and pressure testing to industrial and GMP standards.', 'service-2-fabrication.jpg', 2),
('Installation & Commissioning', 'On-site mechanical and electrical commissioning with full functional testing before handover.', 'service-3-installation.jpg', 3),
('Calibration & Validation', 'Instrument calibration and IQ/OQ documentation support for regulated laboratories.', 'service-4-calibration.jpg', 4),
('Spare Parts & Maintenance', 'Genuine wear parts, seals and preventive maintenance contracts to minimise downtime.', 'service-5-spares.jpg', 5),
('Operator Training', 'Hands-on training for operations and maintenance teams on newly commissioned equipment.', 'service-6-training.jpg', 6);

-- -----------------------------------------------------
-- Clients (logos shown in the homepage client slider)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    website_url VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO clients (name, sort_order) VALUES
('Nessotech', 1),
('Chemtech BD', 2),
('Sciencetech BD', 3),
('Zakia Enterprise', 4),
('Sakaint', 5),
('Medisol Point', 6),
('Acnova Ltd', 7);

-- -----------------------------------------------------
-- Additional site settings (floating contact buttons)
-- -----------------------------------------------------
INSERT INTO settings (setting_key, setting_value) VALUES
('whatsapp_number', '8801670974843'),
('linkedin_url', 'https://www.linkedin.com/company/kaswatech')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- -----------------------------------------------------
-- Hero slides (homepage full-screen slider, admin managed)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    eyebrow VARCHAR(150) DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    image VARCHAR(255) DEFAULT NULL,
    cta_text VARCHAR(80) DEFAULT 'Explore Products',
    cta_link VARCHAR(255) DEFAULT 'products.php',
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO hero_slides (eyebrow, title, subtitle, image, cta_text, cta_link, sort_order) VALUES
('Chemical & Lab Equipment Manufacturer', 'Engineering precision equipment for chemical &amp; laboratory production', 'KASWA Tech designs, builds and supports reactors, process lines and analytical instruments for chemical manufacturers, pharmaceutical formulators and QC laboratories across Bangladesh.', 'hero-1-manufacturing.jpg', 'Explore Products', 'products.php', 1),
('Reactors &amp; Process Vessels', 'Stainless steel and glass-lined reactors built for consistent batches', 'Jacketed vessels, continuous process skids and agitator systems engineered in-house for temperature-controlled synthesis at any scale.', 'hero-2-reactor-vessel.jpg', 'View Chemical Plants', 'products.php?category=chemical-manufacturing-plants', 2),
('Lab &amp; Analytical Instruments', 'Precision instruments trusted by QC and R&amp;D laboratories', 'pH meters, rotary evaporators, muffle furnaces and analytical balances calibrated and supported locally across Bangladesh.', 'hero-3-lab-instruments.jpg', 'View Lab Instruments', 'products.php?category=lab-analytical-instruments', 3),
('Local Installation &amp; Support', 'On-site commissioning, training and 24/7 breakdown response', 'Every system we build is backed by our own engineering team, from first commissioning through spare parts and calibration.', 'hero-4-support.jpg', 'Talk to Our Team', 'contact.php', 4);
