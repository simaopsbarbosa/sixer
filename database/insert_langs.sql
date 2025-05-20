/*
 * ISO 639-1 for common locale codes.
**/

BEGIN TRANSACTION;

INSERT INTO languages (lang_code, lang_name) VALUES ('en', 'English');
INSERT INTO languages (lang_code, lang_name) VALUES ('es', 'Spanish');
INSERT INTO languages (lang_code, lang_name) VALUES ('fr', 'French');
INSERT INTO languages (lang_code, lang_name) VALUES ('de', 'German');
INSERT INTO languages (lang_code, lang_name) VALUES ('pt-br', 'Portuguese (Brazil)');
INSERT INTO languages (lang_code, lang_name) VALUES ('pt-pt', 'Portuguese (Portugal)');
INSERT INTO languages (lang_code, lang_name) VALUES ('it', 'Italian');
INSERT INTO languages (lang_code, lang_name) VALUES ('ru', 'Russian');
INSERT INTO languages (lang_code, lang_name) VALUES ('zh', 'Chinese');
INSERT INTO languages (lang_code, lang_name) VALUES ('zh-cn', 'Chinese (Simplified)');
INSERT INTO languages (lang_code, lang_name) VALUES ('zh-tw', 'Chinese (Traditional)');
INSERT INTO languages (lang_code, lang_name) VALUES ('ja', 'Japanese');
INSERT INTO languages (lang_code, lang_name) VALUES ('ko', 'Korean');
INSERT INTO languages (lang_code, lang_name) VALUES ('ar', 'Arabic');
INSERT INTO languages (lang_code, lang_name) VALUES ('hi', 'Hindi');
INSERT INTO languages (lang_code, lang_name) VALUES ('bn', 'Bengali');
INSERT INTO languages (lang_code, lang_name) VALUES ('ur', 'Urdu');
INSERT INTO languages (lang_code, lang_name) VALUES ('fa', 'Persian');
INSERT INTO languages (lang_code, lang_name) VALUES ('tr', 'Turkish');
INSERT INTO languages (lang_code, lang_name) VALUES ('pl', 'Polish');
INSERT INTO languages (lang_code, lang_name) VALUES ('nl', 'Dutch');
INSERT INTO languages (lang_code, lang_name) VALUES ('sv', 'Swedish');
INSERT INTO languages (lang_code, lang_name) VALUES ('fi', 'Finnish');
INSERT INTO languages (lang_code, lang_name) VALUES ('no', 'Norwegian');
INSERT INTO languages (lang_code, lang_name) VALUES ('da', 'Danish');
INSERT INTO languages (lang_code, lang_name) VALUES ('el', 'Greek');
INSERT INTO languages (lang_code, lang_name) VALUES ('he', 'Hebrew');
INSERT INTO languages (lang_code, lang_name) VALUES ('vi', 'Vietnamese');
INSERT INTO languages (lang_code, lang_name) VALUES ('th', 'Thai');
INSERT INTO languages (lang_code, lang_name) VALUES ('id', 'Indonesian');
INSERT INTO languages (lang_code, lang_name) VALUES ('ms', 'Malay');
INSERT INTO languages (lang_code, lang_name) VALUES ('ro', 'Romanian');
INSERT INTO languages (lang_code, lang_name) VALUES ('hu', 'Hungarian');
INSERT INTO languages (lang_code, lang_name) VALUES ('cs', 'Czech');
INSERT INTO languages (lang_code, lang_name) VALUES ('uk', 'Ukrainian');

COMMIT;
