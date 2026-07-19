-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2026 at 12:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kaswa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'KASWA Administrator', 'admin', 'mdjhk19@gmail.com', '$2y$10$/G3NbrqrH5JsBExKXjGcR.8gBPdCdhETj1.JJ3Gj9UJInBCv7iHAe', 'super_admin', '2026-07-18 15:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'flask',
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Chemical Manufacturing Plants', 'chemical-manufacturing-plants', 'Turnkey reactors, vessels and process lines for bulk and specialty chemical production.', 'reactor', 'category-1-chemical-plants.jpg', 1, 1, '2026-07-18 15:28:11'),
(2, 'Lab & Analytical Instruments', 'lab-analytical-instruments', 'Precision instruments for quality control, R&D and analytical testing laboratories.', 'microscope', 'category-2-lab-instruments.jpg', 2, 1, '2026-07-18 15:28:11'),
(3, 'Filtration & Separation Systems', 'filtration-separation-systems', 'Industrial filter presses, centrifuges and separation units for process purification.', 'filter', 'category-3-filtration.jpg', 3, 1, '2026-07-18 15:28:11'),
(4, 'Mixing, Blending & Milling', 'mixing-blending-milling', 'Ribbon blenders, colloid mills and homogenizers for consistent formulation.', 'blend', 'category-4-mixing.jpg', 4, 1, '2026-07-18 15:28:11'),
(5, 'Drying & Thermal Equipment', 'drying-thermal-equipment', 'Ovens, dryers and furnaces engineered for controlled thermal processing.', 'thermometer', 'category-5-drying.jpg', 5, 1, '2026-07-18 15:28:11'),
(6, 'Packaging & Filling Machinery', 'packaging-filling-machinery', 'Automated filling, capping and packaging lines for finished chemical and pharma products.', 'package', 'category-6-packaging.jpg', 6, 1, '2026-07-18 15:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `logo`, `website_url`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Nessotech', NULL, NULL, 1, 1, '2026-07-18 15:28:12'),
(2, 'Chemtech BD', NULL, NULL, 2, 1, '2026-07-18 15:28:12'),
(3, 'Sciencetech BD', NULL, NULL, 3, 1, '2026-07-18 15:28:12'),
(4, 'Zakia Enterprise', NULL, NULL, 4, 1, '2026-07-18 15:28:12'),
(5, 'Sakaint', NULL, NULL, 5, 1, '2026-07-18 15:28:12'),
(6, 'Medisol Point', NULL, NULL, 6, 1, '2026-07-18 15:28:12'),
(7, 'Acnova Ltd', NULL, NULL, 7, 1, '2026-07-18 15:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `eyebrow` varchar(150) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `cta_text` varchar(80) DEFAULT 'Explore Products',
  `cta_link` varchar(255) DEFAULT 'products.php',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `eyebrow`, `title`, `subtitle`, `image`, `cta_text`, `cta_link`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Chemical & Lab Equipment Manufacturer', 'Engineering precision equipment for chemical &amp; laboratory production', 'KASWA Tech designs, builds and supports reactors, process lines and analytical instruments for chemical manufacturers, pharmaceutical formulators and QC laboratories across Bangladesh.', 'slide-1784450202-31d0ea.png', 'Explore Products', 'products.php', 1, 1, '2026-07-18 15:28:12'),
(2, 'Reactors &amp; Process Vessels', 'Stainless steel and glass-lined reactors built for consistent batches', 'Jacketed vessels, continuous process skids and agitator systems engineered in-house for temperature-controlled synthesis at any scale.', 'slide-1784451053-deb27a.png', 'View Chemical Plants', 'products.php?category=chemical-manufacturing-plants', 2, 1, '2026-07-18 15:28:12'),
(3, 'Lab &amp; Analytical Instruments', 'Precision instruments trusted by QC and R&amp;D laboratories', 'pH meters, rotary evaporators, muffle furnaces and analytical balances calibrated and supported locally across Bangladesh.', 'slide-1784451937-1a466f.png', 'View Lab Instruments', 'products.php?category=lab-analytical-instruments', 3, 1, '2026-07-18 15:28:12'),
(4, 'Local Installation &amp; Support', 'On-site commissioning, training and 24/7 breakdown response', 'Every system we build is backed by our own engineering team, from first commissioning through spare parts and calibration.', 'slide-1784456619-0e1bb3.png', 'Talk to Our Team', 'contact.php', 4, 1, '2026-07-18 15:28:12'),
(5, '', 'thhdh', '', 'slide-1784456632-4f8bee.png', 'Explore Products', 'products.php', 5, 1, '2026-07-19 10:23:52'),
(6, '', 'fjfj', '', 'slide-1784456741-b36019.png', 'Explore Products', 'products.php', 6, 1, '2026-07-19 10:25:41'),
(7, '', 'dfadf', '', 'slide-1784457141-b01cad.png', 'Explore Products', 'products.php', 7, 1, '2026-07-19 10:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `status` enum('new','in_progress','resolved') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `admin_id`, `code`, `expires`, `used`, `created_at`) VALUES
(1, 1, '557112', '2026-07-18 17:43:57', 1, '2026-07-18 15:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `model_no` varchar(100) DEFAULT NULL,
  `short_description` varchar(300) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `model_no`, `short_description`, `description`, `specifications`, `image`, `is_featured`, `status`, `created_at`) VALUES
(1, 1, 'Stainless Steel Reactor Vessel', 'stainless-steel-reactor-vessel-500l', 'KSW-RV500', 'Jacketed SS316 reactor for controlled chemical synthesis.', 'A fully jacketed stainless steel reactor vessel built for consistent temperature-controlled synthesis, suitable for chemical, pharmaceutical and specialty formulation batches.', 'Capacity: 500 L | Material: SS316L | Jacket: Steam/Hot Oil | Agitator: Anchor type | Max Pressure: 3 bar', NULL, 1, 1, '2026-07-18 15:28:12'),
(2, 1, 'Glass Lined Reaction Vessel', 'glass-lined-reaction-vessel-1000l', 'KSW-GLR1000', 'Corrosion-resistant glass-lined vessel for aggressive media.', 'Engineered for reactions involving corrosive acids and solvents, with a fused glass lining that resists chemical attack and simplifies cleaning between batches.', 'Capacity: 1000 L | Lining: Glass (ASTM C-635) | Agitator Speed: 20-100 RPM | Jacket Pressure: 6 bar', NULL, 0, 1, '2026-07-18 15:28:12'),
(3, 1, 'Continuous Process Skid', 'continuous-process-skid-cps200', 'KSW-CPS200', 'Modular skid-mounted continuous processing line.', 'A compact, skid-mounted continuous manufacturing unit that reduces batch cycle time and footprint for mid-scale chemical production.', 'Throughput: 200 L/hr | Control: PLC/HMI | Construction: SS304/SS316 | Footprint: 3m x 2m', NULL, 1, 1, '2026-07-18 15:28:12'),
(4, 2, 'Digital pH Meter', 'digital-ph-meter-ph200', 'KSW-PH200', 'Bench-top precision pH and conductivity meter.', 'A laboratory-grade digital meter for accurate pH, conductivity and TDS measurement, with automatic temperature compensation.', 'Range: 0-14 pH | Accuracy: ±0.01 pH | Display: LCD | Calibration: Auto 3-point', NULL, 1, 1, '2026-07-18 15:28:12'),
(5, 2, 'Rotary Evaporator', 'rotary-evaporator-re501', 'KSW-RE501', 'Vacuum distillation unit for solvent recovery.', 'Used for efficient, gentle solvent removal in R&D and pilot-scale labs, with digital speed and temperature control.', 'Flask Capacity: 1-5 L | Rotation Speed: 20-180 RPM | Bath Temp: up to 180°C', NULL, 0, 1, '2026-07-18 15:28:12'),
(6, 2, 'Muffle Furnace', 'muffle-furnace-mf1200', 'KSW-MF1200', 'High-temperature furnace for ashing and material testing.', 'A programmable muffle furnace built for consistent high-temperature testing, ashing and heat treatment applications.', 'Max Temp: 1200°C | Chamber: 5 L | Control: PID Programmable | Insulation: Ceramic fiber', NULL, 0, 1, '2026-07-18 15:28:12'),
(7, 2, 'Analytical Balance', 'analytical-balance-ab220', 'KSW-AB220', 'High-precision balance for laboratory weighing.', 'Delivers repeatable, high-precision readings for formulation and quality-control weighing tasks.', 'Capacity: 220 g | Readability: 0.1 mg | Calibration: Internal automatic', NULL, 0, 1, '2026-07-18 15:28:12'),
(8, 3, 'Plate & Frame Filter Press', 'plate-frame-filter-press-fp50', 'KSW-FP50', 'Manual/hydraulic filter press for solid-liquid separation.', 'Designed for efficient dewatering and clarification of process slurries across chemical and pharma production.', 'Plate Size: 470 x 470 mm | Plates: 20-50 | Cake Capacity: up to 150 L | Closing: Hydraulic', NULL, 1, 1, '2026-07-18 15:28:12'),
(9, 3, 'Basket Centrifuge', 'basket-centrifuge-bc300', 'KSW-BC300', 'Top-discharge basket centrifuge for crystal separation.', 'Suited for separating crystals and solids from mother liquor with minimal product loss.', 'Basket Diameter: 800 mm | Capacity: 300 L | Speed: up to 1200 RPM | Drive: VFD controlled', NULL, 0, 1, '2026-07-18 15:28:12'),
(10, 4, 'Ribbon Blender', 'ribbon-blender-rb500', 'KSW-RB500', 'Horizontal ribbon blender for dry powder mixing.', 'Achieves fast, uniform blending of dry powders and granules for chemical and pharmaceutical formulations.', 'Capacity: 500 L | Mixing Time: 8-15 min | Discharge: Bottom valve | Construction: SS304', NULL, 1, 1, '2026-07-18 15:28:12'),
(11, 4, 'Colloid Mill', 'colloid-mill-cm150', 'KSW-CM150', 'Wet grinding mill for fine particle reduction.', 'Produces fine, stable emulsions and suspensions through precision rotor-stator wet milling.', 'Throughput: 150-500 L/hr | Gap Adjustment: Micrometric | Motor: 5.5 kW', NULL, 0, 1, '2026-07-18 15:28:12'),
(12, 4, 'High Shear Homogenizer', 'high-shear-homogenizer-hs100', 'KSW-HS100', 'In-line homogenizer for emulsions and dispersions.', 'Delivers consistent particle size reduction for creams, emulsions and chemical dispersions at production scale.', 'Flow Rate: up to 100 L/min | Speed: 3000-10000 RPM | Seal: Mechanical', NULL, 0, 1, '2026-07-18 15:28:12'),
(13, 5, 'Fluid Bed Dryer', 'fluid-bed-dryer-fbd150', 'KSW-FBD150', 'Rapid, uniform granule drying system.', 'Uses fluidization technology for fast, even drying of granules with minimal thermal degradation.', 'Batch Capacity: 150 kg | Air Volume: 3000 CFM | Temp Range: 40-120°C', NULL, 1, 1, '2026-07-18 15:28:12'),
(14, 5, 'Hot Air Oven', 'hot-air-oven-hao500', 'KSW-HAO500', 'Tray-type industrial drying oven.', 'A reliable tray dryer for consistent moisture removal across pharmaceutical and chemical intermediates.', 'Trays: 24 | Temp Range: 50-250°C | Air Circulation: Forced convection', NULL, 0, 1, '2026-07-18 15:28:12'),
(15, 6, 'Automatic Liquid Filling Machine', 'automatic-liquid-filling-machine-lf24', 'KSW-LF24', 'Multi-head volumetric filling line.', 'A high-speed filling line for liquids and viscous chemical products, with adjustable fill volume and head count.', 'Heads: 24 | Speed: up to 60 bottles/min | Fill Range: 50ml-5L | Accuracy: ±1%', NULL, 1, 1, '2026-07-18 15:28:12'),
(16, 6, 'Automatic Capping Machine', 'automatic-capping-machine-cm12', 'KSW-CM12', 'In-line cap tightening and sealing unit.', 'Ensures consistent torque and seal quality across bottle and container formats.', 'Speed: up to 80 caps/min | Cap Size Range: 20-100mm | Control: PLC touchscreen', NULL, 0, 1, '2026-07-18 15:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `image`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Custom Equipment Design', 'We engineer reactors, skids and instruments around your exact batch size, media and layout constraints.', 'service-1-design.jpg', 1, 1, '2026-07-18 15:28:12'),
(2, 'Fabrication & Manufacturing', 'In-house SS304/SS316 fabrication, welding and pressure testing to industrial and GMP standards.', 'service-2-fabrication.jpg', 2, 1, '2026-07-18 15:28:12'),
(3, 'Installation & Commissioning', 'On-site mechanical and electrical commissioning with full functional testing before handover.', 'service-3-installation.jpg', 3, 1, '2026-07-18 15:28:12'),
(4, 'Calibration & Validation', 'Instrument calibration and IQ/OQ documentation support for regulated laboratories.', 'service-4-calibration.jpg', 4, 1, '2026-07-18 15:28:12'),
(5, 'Spare Parts & Maintenance', 'Genuine wear parts, seals and preventive maintenance contracts to minimise downtime.', 'service-5-spares.jpg', 5, 1, '2026-07-18 15:28:12'),
(6, 'Operator Training', 'Hands-on training for operations and maintenance teams on newly commissioned equipment.', 'service-6-training.jpg', 6, 1, '2026-07-18 15:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'company_name', 'KASWA TECHNOLOGIES', '2026-07-19 08:09:20'),
(2, 'tagline', 'SCIENTIFIC SOLUTION MAKER', '2026-07-19 08:09:20'),
(3, 'corporate_address', 'Delwar Complex [3rd Floor] Room no: 05, 26, Shahid Nazrul Islam Sarak, Hatkhola, Dhaka-1203, Bangladesh', '2026-07-18 15:28:12'),
(4, 'registered_address', '251/1, Uttar Datta Para (Chanki), Arshad Nagar, Tongi East, Gazipur City Corporation, Bangladesh', '2026-07-18 15:28:12'),
(5, 'phone_1', '+88 01670974843', '2026-07-18 15:28:12'),
(6, 'phone_2', '+88 01741641318', '2026-07-18 15:28:12'),
(7, 'email', 'info@kaswatech.net', '2026-07-18 15:28:12'),
(8, 'website', 'www.kaswatech.net', '2026-07-18 15:28:12'),
(9, 'about_text', 'KASWA Tech designs, manufactures and supplies chemical processing equipment and laboratory instruments engineered for reliability, precision and compliance. From reactor vessels to analytical instruments, every system we build is backed by in-house engineering and local after-sales support across Bangladesh.', '2026-07-18 15:28:12'),
(10, 'whatsapp_number', '8801670974843', '2026-07-18 15:28:12'),
(11, 'linkedin_url', 'https://www.linkedin.com/company/kaswatech', '2026-07-18 15:28:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
