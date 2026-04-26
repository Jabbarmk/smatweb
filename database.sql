-- SMARTUAE Database Schema
CREATE DATABASE IF NOT EXISTS smartuae;
USE smartuae;

-- Admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Site settings
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'SMARTUAE'),
('site_tagline', 'Your Smart Business Directory'),
('contact_phone', '+971 50 000 0000'),
('contact_email', 'info@smartuae.com'),
('whatsapp', '+971500000000');

-- Home page sliders
CREATE TABLE sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    subtitle VARCHAR(200),
    button_text VARCHAR(100),
    button_link VARCHAR(500),
    image VARCHAR(500) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO sliders (title, subtitle, button_text, button_link, image, sort_order) VALUES
('DAMAC', 'LIVE THE LUXURY', 'Read more', '#', 'slide1.jpg', 1),
('Smart Business', 'Find the best businesses near you', 'Explore', '#', 'slide2.jpg', 2);

-- Main categories (Home page icons)
CREATE TABLE main_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    link VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO main_categories (name, icon, sort_order) VALUES
('Shopping', '🛍️', 1), ('Food', '🍔', 2), ('Health', '❤️', 3), ('Education', '🎓', 4),
('Cafe', '☕', 5), ('Colleges', '🏫', 6), ('Malls', '🏬', 7);

-- Popular categories (home page cards)
CREATE TABLE popular_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(500),
    link VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO popular_categories (name, image, sort_order) VALUES
('Car Rental', 'car_rental.jpg', 1),
('Tours & Travels', 'tours.jpg', 2);

-- Business categories
CREATE TABLE business_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    group_name VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO business_categories (name, icon, group_name, sort_order) VALUES
('Giftshop', '🎁', 'Popular Categories', 1),
('Sports', '🏀', 'Popular Categories', 2),
('Furniture', '🛋️', 'Popular Categories', 3),
('Toy Shop', '🧸', 'Popular Categories', 4),
('Clinic', '🩺', 'Health and Fitness', 5),
('Eye Hospital', '👁️', 'Health and Fitness', 6),
('Pest Control', '🐜', 'Health and Fitness', 7),
('Electrician', '💡', 'Health and Fitness', 8),
('Hospital', '🏥', 'Health and Fitness', 9),
('Clinics', '💊', 'Health and Fitness', 10),
('Dental', '🦷', 'Health and Fitness', 11),
('ENT Clinic', '👂', 'Health and Fitness', 12),
('Plumber', '🔧', 'Home Services', 13),
('Electrician', '💡', 'Home Services', 14),
('Painter', '👩‍🎨', 'Home Services', 15),
('AC Service', '❄️', 'Home Services', 16),
('IT Company', '👩‍💻', 'Professional Services', 17),
('Software', '💻', 'Professional Services', 18),
('CCTV', '📹', 'Professional Services', 19),
('Tax Consultant', '📊', 'Professional Services', 20);

-- Businesses (shop listings)
CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category_id INT,
    description TEXT,
    image VARCHAR(500),
    rating DECIMAL(2,1) DEFAULT 0,
    distance VARCHAR(50),
    address VARCHAR(500),
    phone VARCHAR(50),
    whatsapp VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES business_categories(id) ON DELETE SET NULL
);

INSERT INTO businesses (name, category_id, description, image, rating, distance, address, phone, whatsapp) VALUES
('Dubai Tours Company', 1, 'Tours Company', 'dubai_tours.jpg', 4.7, '12 Kms Away', 'Panampilly Nagar, Erna...', '+971500000000', '+971500000000'),
('Safary Tours', 1, 'Tours Company', 'safary_tours.jpg', 4.7, '12 Kms Away', 'Panampilly Nagar, Erna...', '+971500000000', '+971500000000'),
('Dream Car Dubai', 1, 'Rental Car Company', 'dream_car.jpg', 4.7, '12 Kms Away', 'Panampilly Nagar, Erna...', '+971500000000', '+971500000000');

-- Classified categories
CREATE TABLE classified_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO classified_categories (name, icon, sort_order) VALUES
('Used Mobiles', '📱', 1), ('Used Electronics', '💻', 2), ('Used Furniture', '🪑', 3),
('Used Toys', '🧸', 4), ('Used Cars', '🚗', 5), ('Used Bikes', '🏍️', 6),
('Used Spare Parts', '🔧', 7), ('Used TV', '📺', 8);

-- Classified sections (Real Estate, Furniture, Electronics on classifieds home)
CREATE TABLE classified_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO classified_sections (name, sort_order) VALUES
('Real Estate', 1), ('Furniture', 2), ('Electronics', 3);

-- Classified items
CREATE TABLE classifieds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(12,2),
    currency VARCHAR(10) DEFAULT 'AED',
    category_id INT,
    section_id INT,
    image VARCHAR(500),
    location VARCHAR(200),
    age VARCHAR(50),
    model VARCHAR(100),
    warranty VARCHAR(50),
    color VARCHAR(50),
    brand VARCHAR(100),
    condition_status VARCHAR(50),
    version VARCHAR(100),
    storage VARCHAR(100),
    memory VARCHAR(100),
    battery_health VARCHAR(50),
    accompaniments VARCHAR(200),
    carrier_lock VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES classified_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (section_id) REFERENCES classified_sections(id) ON DELETE SET NULL
);

INSERT INTO classifieds (title, description, price, currency, category_id, section_id, image, location, age, model, warranty, color, brand, condition_status, storage, memory, battery_health, accompaniments, carrier_lock) VALUES
('Samsung s25 Ultra', 'Thinner design, greater choice. Galaxy S25 and S25+ both feature our most powerful, custom-made processor, optimized battery life and our most innovative AI.', 2000, 'AED', 1, 3, 'samsung_s25.jpg', 'Al Nahda, Dubai', '1 Year', 's25', 'Yes', 'Grey', 'Samsung', 'New', '128 GB', '8 GB and more', 'Above 85%', 'Box, Charger', 'No'),
('iPhone 15 Pro Max', 'Latest Apple flagship with A17 Pro chip.', 3500, 'AED', 1, 3, 'iphone15.jpg', 'Al Nahda, Dubai', '6 Months', '15 Pro Max', 'Yes', 'Blue', 'Apple', 'New', '256 GB', '8 GB', 'Above 90%', 'Box, Charger, Cable', 'No'),
('Luxury Villa', 'Beautiful 4-bedroom villa in prime location.', 36000, 'AED', NULL, 1, 'villa1.jpg', 'Dubai Marina', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('Modern Apartment', 'Fully furnished 2-bedroom apartment.', 36000, 'AED', NULL, 1, 'apartment1.jpg', 'Downtown Dubai', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('Leather Sofa Set', 'Premium leather sofa set, 3+2 seater.', 36000, 'AED', 3, 2, 'sofa1.jpg', 'Deira, Dubai', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('Office Chair', 'Ergonomic office chair with lumbar support.', 36000, 'AED', 3, 2, 'chair1.jpg', 'Deira, Dubai', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Jobs
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    company VARCHAR(200),
    company_logo VARCHAR(500),
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    currency VARCHAR(10) DEFAULT 'AED',
    location VARCHAR(200),
    job_type ENUM('Fulltime', 'Part Time', 'Contract', 'Freelance') DEFAULT 'Fulltime',
    description TEXT,
    requirements TEXT,
    benefits TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO jobs (title, company, salary_min, salary_max, location, job_type, description, requirements, benefits, is_featured) VALUES
('Arabic Teacher', 'Blems Education', 1000, 3000, 'Dubai, UAE', 'Fulltime', 'We''re looking for a skilled DevOps Engineer to help streamline our deployment processes and maintain our cloud infrastructure.', '3+ years of DevOps or infrastructure experience\nExperience with AWS, Azure, or Google Cloud\nProficiency in Docker and Kubernetes\nKnowledge of CI/CD tools (Jenkins, GitLab CI)\nExperience with infrastructure as Code (Terraform)\nUnderstanding of monitoring and logging tools\nScripting skills in Python or Bash', 'Competitive salary rates\nOpportunity for full-time conversion\nRemote work flexibility\nAccess to cutting-edge technologies\nCollaborative team environment\nProfessional development support\nFlexible schedule', 1),
('Web Developer', 'Smart Technologies', 2000, 5000, 'Dubai, UAE', 'Part Time', 'Looking for an experienced web developer to join our team.', '3+ years of web development experience\nProficiency in PHP, JavaScript\nExperience with MySQL\nKnowledge of modern frameworks', 'Competitive salary\nFlexible hours\nRemote work options', 1),
('Marketing Manager', 'Digital Corp', 3000, 6000, 'Abu Dhabi, UAE', 'Contract', 'We need a marketing manager to lead our digital campaigns.', '5+ years of marketing experience\nDigital marketing expertise\nTeam leadership skills', 'Great benefits package\nProfessional growth\nTeam events', 1);

-- User profiles / CVs
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(200) NOT NULL,
    title VARCHAR(200),
    photo VARCHAR(500),
    email VARCHAR(200),
    phone VARCHAR(50),
    whatsapp VARCHAR(50),
    linkedin VARCHAR(500),
    location VARCHAR(200),
    experience_years INT DEFAULT 0,
    education VARCHAR(500),
    current_company VARCHAR(200),
    work_experience TEXT,
    technical_skills TEXT,
    certifications TEXT,
    education_details TEXT,
    projects TEXT,
    languages VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO user_profiles (full_name, title, experience_years, education, current_company, phone, work_experience, technical_skills, certifications, education_details, projects, languages) VALUES
('Abdul Jabbar', 'Experts in Digital Marketing, Web Design, App Design', 5, 'Bed @ Kerala University', 'Smartflix Technologies', '+91.9516.717777',
'Web Designer | Smart Technologies | Jan 2020 - Jan 2024 | Bangalore, India',
'HTML, CSS, Microsoft Office',
'Google Ads, Adilab Institute, 2022',
'Btech @ Kerala University, Kerala, India | 2001-2004\nPUC @ Kerala University, Kerala, India | 2001-2004',
'Mobile App Development - Developed and involved in a mobile app project.',
'English, Hindi, Malayalam, Arabic');

-- Pages content (for admin-editable pages)
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    meta_description VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO pages (slug, title, content) VALUES
('about', 'About Us', 'SMARTUAE is your go-to business directory in the UAE.'),
('contact', 'Contact Us', 'Get in touch with us for any inquiries.'),
('privacy', 'Privacy Policy', 'Your privacy is important to us.'),
('terms', 'Terms & Conditions', 'Please read our terms and conditions.');
