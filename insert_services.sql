-- CarbonCraft Services Seeder
-- Run this SQL in phpMyAdmin or MySQL console to populate the services table
-- Database: harshaln_carboncraft_dashboard

-- Insert services with explicit IDs (used by OrderApiController)
INSERT INTO `services` (`id`, `service_name`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Metal Card - DIY Service', '🎨', 1, NOW(), NOW()),
(2, 'Metal Card - Full Service', '✨', 1, NOW(), NOW()),
(3, 'Plastic Card - DIY Service', '🎨', 1, NOW(), NOW()),
(4, 'Plastic Card - Full Service', '✨', 1, NOW(), NOW()),
(5, 'Tap & Pay Card - DIY Service', '📱', 1, NOW(), NOW()),
(6, 'Tap & Pay Card - Full Service', '📱', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `service_name` = VALUES(`service_name`),
    `icon` = VALUES(`icon`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- Verify insertion
SELECT * FROM `services`;
