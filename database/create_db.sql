BEGIN TRANSACTION;

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user_registry;

DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS user_skills;

DROP TABLE IF EXISTS languages;
DROP TABLE IF EXISTS user_languages;

DROP TABLE IF EXISTS services_list;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS messages;

CREATE TABLE user_registry
(
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    password_hash TEXT NOT NULL,
    access_level TEXT DEFAULT "client" NOT NULL,

    user_picture BLOB DEFAULT NULL,

    username TEXT, /* This was changed bc 'name' is somehow a sql keyword. Is still suposed to be the user actual name */
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

CREATE TABLE languages
(
    lang_code TEXT NOT NULL, /* pt-br */
    lang_name TEXT NOT NULL, /* Portuguese */

    PRIMARY KEY(lang_code)
);

CREATE TABLE user_languages
(
    user_id INTEGER NOT NULL,
    lang_code TEXT NOT NULL,

    PRIMARY KEY(user_id, lang_code),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id),
    FOREIGN KEY(lang_code) REFERENCES languages(lang_code)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE services_list
(
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER NOT NULL,

    service_title TEXT NOT NULL,
    service_price REAL NOT NULL,
    service_info TEXT NOT NULL,
    service_eta TEXT NOT NULL,

    service_delisted BOOLEAN DEFAULT FALSE NOT NULL,

    service_picture BLOB DEFAULT NULL,

    FOREIGN KEY(freelancer_id) REFERENCES user_registry(user_id)
);

CREATE TABLE purchases
(
    purchase_id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    completed BOOLEAN DEFAULT FALSE,

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
    FOREIGN KEY(service_id) REFERENCES services(service_id)
); 
 

COMMIT;
