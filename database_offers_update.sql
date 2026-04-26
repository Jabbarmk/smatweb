-- ============================================================
--  SMARTUAE — Offers Feature SQL Update
--  Run this on any fresh copy of the smartuae database
--  Date: 2026-04-22
-- ============================================================

-- ------------------------------------------------------------
-- 1. Create offers table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `offers` (
  `id`               int(11)        NOT NULL AUTO_INCREMENT,
  `business_id`      int(11)        NOT NULL,
  `category_id`      int(11)        DEFAULT NULL,
  `title`            varchar(255)   NOT NULL,
  `description`      text           DEFAULT NULL,
  `details`          text           DEFAULT NULL,
  `image`            varchar(500)   DEFAULT NULL,
  `price`            decimal(10,2)  DEFAULT 0.00,
  `original_price`   decimal(10,2)  DEFAULT NULL,
  `currency`         varchar(10)    DEFAULT 'AED',
  `discount_percent` int(11)        DEFAULT NULL,
  `rating`           decimal(2,1)   DEFAULT 0.0,
  `emirate`          varchar(100)   DEFAULT NULL,
  `ranking`          int(11)        DEFAULT 0,
  `valid_from`       date           DEFAULT NULL,
  `valid_to`         date           DEFAULT NULL,
  `is_active`        tinyint(1)     DEFAULT 1,
  `created_at`       timestamp      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `business_id`  (`business_id`),
  KEY `category_id`  (`category_id`),
  KEY `emirate`      (`emirate`),
  KEY `is_active`    (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. Create offer_reviews table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `offer_reviews` (
  `id`         int(11)       NOT NULL AUTO_INCREMENT,
  `offer_id`   int(11)       NOT NULL,
  `user_id`    int(11)       DEFAULT NULL,
  `user_name`  varchar(150)  DEFAULT NULL,
  `rating`     decimal(2,1)  DEFAULT 5.0,
  `comment`    text          DEFAULT NULL,
  `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `offer_id` (`offer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. Sample offers (skip if already exist)
-- ------------------------------------------------------------
INSERT IGNORE INTO `offers`
  (id, business_id, category_id, title, description, details, image, price, original_price, currency, discount_percent, rating, emirate, ranking, is_active)
VALUES
(1, 1, 1, 'City Tour Package',
 'Full day Dubai city tour with guide',
 'Explore iconic Dubai landmarks including Burj Khalifa, Dubai Mall, Jumeirah Mosque, Dubai Marina. Pickup & drop off included. English speaking guide. Lunch included.',
 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&h=1000&fit=crop',
 199.00, 299.00, 'AED', 33, 4.7, 'Dubai', 100, 1),

(2, 1, 1, 'Desert Safari Deluxe',
 'Evening desert safari with BBQ dinner',
 'Dune bashing, camel ride, sand boarding, traditional entertainment, BBQ dinner. 6 hours. Shared / private options.',
 'https://images.unsplash.com/photo-1529543544282-ea669407fca3?w=800&h=1000&fit=crop',
 149.00, 249.00, 'AED', 40, 4.8, 'Dubai', 95, 1),

(3, 2, 1, 'Abu Dhabi Day Trip',
 'Grand Mosque + Louvre from Dubai',
 'Visit Sheikh Zayed Grand Mosque, Louvre Abu Dhabi, Ferrari World photo stop. Full day. Lunch included.',
 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=1000&fit=crop',
 249.00, 349.00, 'AED', 29, 4.6, 'Dubai', 80, 1),

(4, 3, 1, 'Luxury Sports Car Rental',
 'Drive your dream ride for a day',
 'Huge fleet of Lamborghini, Ferrari, Rolls Royce. Daily / weekly rates. Free delivery across Dubai.',
 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&h=1000&fit=crop',
 899.00, 1299.00, 'AED', 31, 4.9, 'Dubai', 90, 1),

(5, 1, 1, 'Arabic Mezze Platter',
 'Traditional mezze for 2 with hummus, fattoush & more',
 'Full spread of hummus, mutabbal, fattoush, tabbouleh, falafel, warm bread. Serves 2 people. Ideal for a light lunch or starter.',
 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&h=1000&fit=crop',
 79.00, 120.00, 'AED', 34, 4.8, 'Dubai', 88, 1),

(6, 2, 1, 'Grilled Seafood Combo',
 'Fresh catch of the day grilled to perfection',
 'Choice of hammour, shrimp or mixed grill. Served with rice, salad and garlic sauce. Catch changes daily.',
 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=1000&fit=crop',
 149.00, 199.00, 'AED', 25, 4.7, 'Dubai', 85, 1),

(7, 3, 1, 'Shawarma Family Box',
 '10 wraps + fries + 4 drinks bundle',
 'Chicken or mixed shawarma wraps made fresh to order. Includes large fries, garlic sauce, pickles and 4 soft drinks.',
 'https://images.unsplash.com/photo-1561651188-d207bbec4ec3?w=800&h=1000&fit=crop',
 99.00, 140.00, 'AED', 29, 4.6, 'Dubai', 82, 1),

(8, 1, 1, 'Breakfast Feast',
 'Full English + Arabic breakfast combined',
 'Eggs your way, beans, sausage, labneh, olives, cheese, croissant, orange juice. Best way to start your day.',
 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=800&h=1000&fit=crop',
 55.00, 75.00, 'AED', 27, 4.9, 'Dubai', 78, 1),

(9, 2, 1, 'Manakish & Coffee Deal',
 'Freshly baked manakish with specialty coffee',
 'Choose any manakish — zaatar, cheese or mixed — paired with a specialty latte or cappuccino. Perfect morning combo.',
 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&h=1000&fit=crop',
 35.00, 50.00, 'AED', 30, 4.5, 'Dubai', 75, 1);

-- ------------------------------------------------------------
-- 4. Sample reviews
-- ------------------------------------------------------------
INSERT IGNORE INTO `offer_reviews` (id, offer_id, user_name, rating, comment) VALUES
(1, 1, 'Sarah A.',      5.0, 'Amazing experience! Our guide was super friendly and knowledgeable.'),
(2, 1, 'Mohammed K.',   4.5, 'Great tour, well organised. Would definitely book again.'),
(3, 2, 'James P.',      5.0, 'Desert safari was the highlight of our trip!'),
(4, 4, 'Lena H.',       5.0, 'Drove the Lambo for a day - unforgettable!');
