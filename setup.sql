/*
    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
*/

BEGIN TRANSACTION;

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user_registry;
DROP TABLE IF EXISTS user_info;
DROP TABLE IF EXISTS user_language;
DROP TABLE IF EXISTS language;
DROP TABLE IF EXISTS service;
DROP TABLE IF EXISTS user_service;

/*joint registry table with info*/
CREATE TABLE user_registry
(
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    password_hash TEXT,
    access_level TEXT
);

CREATE TABLE user_info
(
    user_id INTEGER,
    name TEXT,
    email TEXT,
    join_date DATETIME,
    country TEXT,
    user_rating REAL

    PRIMARY KEY(user_id),
    FOREIGN KEY(user_id) REFERENCES user_registry(user_id)
);

CREATE TABLE language
(
    lang_code TEXT, /* pt-br */
    lang_name TEXT, /* Portuguese */

    PRIMARY KEY(lang_code)
)

CREATE TABLE user_language
(
    user_id INTEGER,
    lang_code TEXT

    PRIMARY KEY(user_id, lang_code),
    FOREIGN KEY(user_id) REFERENCES user_info(user_id),
    FOREIGN KEY(lang_code) REFERENCES language(lang_code)
);


CREATE TABLE service
(
    service_id INTEGER,
    freelancer_id INTEGER,
    serviced_price REAL,
    service_info TEXT,
    etc BLOB,

    PRIMARY KEY(service_id),
    FOREIGN KEY(freelancer_id) REFERENCES user_registry(user_id)
);

CREATE TABLE user_service
(
    user_id INTEGER,
    service_id TEXT,
    completed BOOLEAN,

    PRIMARY KEY(user_id, service_id),
    FOREIGN KEY(user_id) REFERENCES user_info(user_id),
    FOREIGN KEY(service_id) REFERENCES service(service_id)
);

COMMIT;