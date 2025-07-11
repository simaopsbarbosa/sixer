BEGIN TRANSACTION;

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user_registry;

DROP TABLE IF EXISTS categories;

DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS user_skills;

DROP TABLE IF EXISTS services_list;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS messages;

CREATE TABLE user_registry
(
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    password_hash TEXT NOT NULL,
    access_level TEXT DEFAULT "client" NOT NULL,

    user_picture BLOB DEFAULT NULL,

    full_name TEXT,
    email TEXT UNIQUE NOT NULL,
    join_date DATETIME NOT NULL,

    aboutme TEXT DEFAULT NULL
);

CREATE TABLE skills
(
    skill_name TEXT NOT NULL, /* python */

    PRIMARY KEY(skill_name)
);

CREATE TABLE user_skills
(
    user_id INTEGER NOT NULL,
    skill_name TEXT NOT NULL,
    
    PRIMARY KEY(user_id, skill_name),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id)
    FOREIGN KEY(skill_name) REFERENCES skills(skill_name)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE categories
(
    category_name TEXT PRIMARY KEY NOT NULL
);

CREATE TABLE services_list
(
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER NOT NULL,

    service_title TEXT NOT NULL,
    service_price REAL NOT NULL,
    service_info TEXT NOT NULL,
    service_eta INTEGER NOT NULL,
    service_category TEXT NOT NULL,

    service_delisted BOOLEAN DEFAULT FALSE NOT NULL,

    service_picture BLOB DEFAULT NULL,

    FOREIGN KEY(freelancer_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(service_category) REFERENCES categories(category_name)
);

CREATE TABLE purchases
(
    purchase_id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    review_text TEXT DEFAULT NULL,
    review_rating INTEGER DEFAULT NULL,

    FOREIGN KEY(client_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(service_id) REFERENCES services_list(service_id)
);

CREATE TABLE messages
(
    message_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,

    message_text TEXT NOT NULL,
    date_time DATETIME NOT NULL,
    is_reply BOOLEAN NOT NULL,

    FOREIGN KEY(user_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(service_id) REFERENCES services_list(service_id)
); 
 
-- Main service categories
INSERT INTO categories (category_name) VALUES ('Web Development');
INSERT INTO categories (category_name) VALUES ('Mobile Development');
INSERT INTO categories (category_name) VALUES ('Graphic Design');
INSERT INTO categories (category_name) VALUES ('Digital Marketing');
INSERT INTO categories (category_name) VALUES ('Writing & Translation');
INSERT INTO categories (category_name) VALUES ('Video & Animation');
INSERT INTO categories (category_name) VALUES ('Music & Audio');
INSERT INTO categories (category_name) VALUES ('Data Science');
INSERT INTO categories (category_name) VALUES ('Business Consulting');
INSERT INTO categories (category_name) VALUES ('Education & Training');

-- Digital & Tech Skills
INSERT INTO skills (skill_name) VALUES ('Python');
INSERT INTO skills (skill_name) VALUES ('JavaScript');
INSERT INTO skills (skill_name) VALUES ('Java');
INSERT INTO skills (skill_name) VALUES ('C++');
INSERT INTO skills (skill_name) VALUES ('C#');
INSERT INTO skills (skill_name) VALUES ('HTML');
INSERT INTO skills (skill_name) VALUES ('CSS');
INSERT INTO skills (skill_name) VALUES ('SQL');
INSERT INTO skills (skill_name) VALUES ('TypeScript');
INSERT INTO skills (skill_name) VALUES ('Node.js');
INSERT INTO skills (skill_name) VALUES ('React');
INSERT INTO skills (skill_name) VALUES ('Angular');
INSERT INTO skills (skill_name) VALUES ('Vue.js');
INSERT INTO skills (skill_name) VALUES ('PHP');
INSERT INTO skills (skill_name) VALUES ('Go');
INSERT INTO skills (skill_name) VALUES ('Swift');
INSERT INTO skills (skill_name) VALUES ('Kotlin');
INSERT INTO skills (skill_name) VALUES ('Django');
INSERT INTO skills (skill_name) VALUES ('Flask');
INSERT INTO skills (skill_name) VALUES ('Ruby on Rails');
INSERT INTO skills (skill_name) VALUES ('WordPress');
INSERT INTO skills (skill_name) VALUES ('Shopify');
INSERT INTO skills (skill_name) VALUES ('Wix');
INSERT INTO skills (skill_name) VALUES ('Web Development');
INSERT INTO skills (skill_name) VALUES ('Mobile App Development');
INSERT INTO skills (skill_name) VALUES ('Game Development');
INSERT INTO skills (skill_name) VALUES ('DevOps');
INSERT INTO skills (skill_name) VALUES ('Cloud Computing');
INSERT INTO skills (skill_name) VALUES ('Cybersecurity');
INSERT INTO skills (skill_name) VALUES ('IT Support');

-- Creative & Design
INSERT INTO skills (skill_name) VALUES ('Graphic Design');
INSERT INTO skills (skill_name) VALUES ('UI/UX Design');
INSERT INTO skills (skill_name) VALUES ('Logo Design');
INSERT INTO skills (skill_name) VALUES ('Illustration');
INSERT INTO skills (skill_name) VALUES ('Painting');
INSERT INTO skills (skill_name) VALUES ('Drawing');
INSERT INTO skills (skill_name) VALUES ('3D Modeling');
INSERT INTO skills (skill_name) VALUES ('Animation');
INSERT INTO skills (skill_name) VALUES ('Video Editing');
INSERT INTO skills (skill_name) VALUES ('Photography');
INSERT INTO skills (skill_name) VALUES ('Photo Editing');
INSERT INTO skills (skill_name) VALUES ('Sculpting');
INSERT INTO skills (skill_name) VALUES ('Crafting');
INSERT INTO skills (skill_name) VALUES ('Fashion Design');
INSERT INTO skills (skill_name) VALUES ('Interior Design');

-- Writing & Translation
INSERT INTO skills (skill_name) VALUES ('Copywriting');
INSERT INTO skills (skill_name) VALUES ('Technical Writing');
INSERT INTO skills (skill_name) VALUES ('Creative Writing');
INSERT INTO skills (skill_name) VALUES ('Script Writing');
INSERT INTO skills (skill_name) VALUES ('Translation');
INSERT INTO skills (skill_name) VALUES ('Transcription');
INSERT INTO skills (skill_name) VALUES ('Proofreading');
INSERT INTO skills (skill_name) VALUES ('Editing');
INSERT INTO skills (skill_name) VALUES ('Resume Writing');

-- Music & Audio
INSERT INTO skills (skill_name) VALUES ('Music Production');
INSERT INTO skills (skill_name) VALUES ('Mixing & Mastering');
INSERT INTO skills (skill_name) VALUES ('Singing');
INSERT INTO skills (skill_name) VALUES ('Voice Acting');
INSERT INTO skills (skill_name) VALUES ('Sound Design');
INSERT INTO skills (skill_name) VALUES ('Podcast Editing');

-- Business & Marketing
INSERT INTO skills (skill_name) VALUES ('Digital Marketing');
INSERT INTO skills (skill_name) VALUES ('SEO');
INSERT INTO skills (skill_name) VALUES ('Social Media Marketing');
INSERT INTO skills (skill_name) VALUES ('Email Marketing');
INSERT INTO skills (skill_name) VALUES ('Content Marketing');
INSERT INTO skills (skill_name) VALUES ('Brand Strategy');
INSERT INTO skills (skill_name) VALUES ('Business Consulting');
INSERT INTO skills (skill_name) VALUES ('Accounting');
INSERT INTO skills (skill_name) VALUES ('Project Management');
INSERT INTO skills (skill_name) VALUES ('Data Analysis');

-- Personal & Lifestyle
INSERT INTO skills (skill_name) VALUES ('Fitness Coaching');
INSERT INTO skills (skill_name) VALUES ('Life Coaching');
INSERT INTO skills (skill_name) VALUES ('Meditation Guidance');
INSERT INTO skills (skill_name) VALUES ('Cooking Lessons');
INSERT INTO skills (skill_name) VALUES ('Language Tutoring');
INSERT INTO skills (skill_name) VALUES ('Career Advice');
INSERT INTO skills (skill_name) VALUES ('Nutrition Planning');
INSERT INTO skills (skill_name) VALUES ('Relationship Coaching');

-- Misc & Practical Skills
INSERT INTO skills (skill_name) VALUES ('Virtual Assistance');
INSERT INTO skills (skill_name) VALUES ('Data Entry');
INSERT INTO skills (skill_name) VALUES ('Online Research');
INSERT INTO skills (skill_name) VALUES ('Customer Support');
INSERT INTO skills (skill_name) VALUES ('Ecommerce Management');
INSERT INTO skills (skill_name) VALUES ('Dropshipping');
INSERT INTO skills (skill_name) VALUES ('Presentation Design');
INSERT INTO skills (skill_name) VALUES ('Event Planning');


COMMIT;
