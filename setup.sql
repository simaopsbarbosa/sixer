#pragma FOREING_KEYS

BEGIN TRANSACTION

CREATE TABLE user_registry
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    access_level TEXT,
    password_hash TEXT,
    status TEXT
);

CREATE TABLE user_info
(
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    join_date DATETIME
);

COMMIT