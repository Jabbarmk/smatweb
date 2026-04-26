-- ============================================================
--  SMARTUAE — Main Category Hierarchy SQL Update
--  Run this after database.sql and database_offers_update.sql
--  Date: 2026-04-24
-- ============================================================

USE smartuae;

-- ------------------------------------------------------------
-- 1. Add main_category_id column to business_categories
-- ------------------------------------------------------------
ALTER TABLE `business_categories`
    ADD COLUMN `main_category_id` INT(11) DEFAULT NULL AFTER `id`,
    ADD KEY `main_category_id` (`main_category_id`),
    ADD CONSTRAINT `fk_biz_cat_main_cat`
        FOREIGN KEY (`main_category_id`)
        REFERENCES `main_categories` (`id`)
        ON DELETE SET NULL;

-- ------------------------------------------------------------
-- 2. Map existing business_categories to main_categories
--    Adjust these UPDATE statements to match your data.
-- ------------------------------------------------------------

-- Assuming main_categories were seeded with IDs from database.sql:
--   1 = Shopping, 2 = Food, 3 = Health, 4 = Education,
--   5 = Cafe, 6 = Colleges, 7 = Malls

-- Popular Categories → Shopping
UPDATE `business_categories` SET `main_category_id` = 1
WHERE `group_name` = 'Popular Categories';

-- Health and Fitness → Health
UPDATE `business_categories` SET `main_category_id` = 3
WHERE `group_name` = 'Health and Fitness';

-- Home Services → Shopping (or whichever main category fits)
UPDATE `business_categories` SET `main_category_id` = 1
WHERE `group_name` = 'Home Services';

-- Professional Services → Shopping (adjust as needed)
UPDATE `business_categories` SET `main_category_id` = 1
WHERE `group_name` = 'Professional Services';
