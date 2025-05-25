BEGIN TRANSACTION;

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

COMMIT;
