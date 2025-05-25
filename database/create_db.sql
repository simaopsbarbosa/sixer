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
 

COMMIT;
