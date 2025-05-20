/*
    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
*/

BEGIN TRANSACTION;

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user_registry;
DROP TABLE IF EXISTS user_language;
DROP TABLE IF EXISTS language;
DROP TABLE IF EXISTS service;
DROP TABLE IF EXISTS user_service;


CREATE TABLE user_registry
(
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    password_hash TEXT,
    access_level TEXT,


    user_picture BLOB DEFAULT NULL,

    name TEXT,
    email TEXT UNIQUE,
    join_date DATETIME,

    aboutme TEXT DEFAULT NULL,


    PRIMARY KEY(user_id),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id)
);

CREATE TABLE skill
(
    skill_name TEXT, /* python */

    PRIMARY KEY(skill_name)
);

CREATE TABLE user_skills
(
    user_id INTEGER,
    skill_name TEXT,
    
    PRIMARY KEY(user_id, skill_name),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(skill_name) REFERENCES skill(skill_name)
);

CREATE TABLE language
(
    lang_code TEXT, /* pt-br */
    lang_name TEXT, /* Portuguese */

    PRIMARY KEY(lang_code)
);

CREATE TABLE user_language
(
    user_id INTEGER,
    lang_code TEXT

    PRIMARY KEY(user_id, lang_code),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(lang_code) REFERENCES language(lang_code)
);

CREATE TABLE service
(
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER,

    service_title TEXT,
    service_price REAL,
    service_info TEXT,

    service_picture BLOB,

    FOREIGN KEY(freelancer_id) REFERENCES user_registry(user_id)
);

CREATE TABLE purchase
(
    purchase_id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER,
    service_id INTEGER,
    completed BOOLEAN,

    review_text TEXT DEFAULT NULL,
    review_rating INTEGER DEFAULT NULL,

    FOREIGN KEY(client_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(service_id) REFERENCES service(service_id)
);

CREATE TABLE message
(
    client_id INTEGER,
    service_id INTEGER,

    message_text TEXT,
    date_time DATETIME,
    is_reply BOOLEAN,

    PRIMARY KEY(client_id, service_id, message_text),
    FOREIGN KEY(client_id) REFERENCES purchase(client_id),
    FOREIGN KEY(service_id) REFERENCES service(service_id)
); 
 

COMMIT;