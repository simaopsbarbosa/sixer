# ltw17g06

## Features

**User:**
- [x] Register a new account.
- [x] Log in and out.
- [x] Edit their profile, including their name, username, password, and email.

**Freelancers:**
- [x] List new services, providing details such as category, pricing, delivery time, and service description, along with images or videos.
- [x] Track and manage their offered services.
- [x] Respond to inquiries from clients regarding their services and provide custom offers if needed.
- [x] Mark services as completed once delivered.

**Clients:**
- [x] Browse services using filters like category, price, and rating.
- [x] Engage with freelancers to ask questions or request custom orders.
- [x] Hire freelancers and proceed to checkout (simulate payment process).
- [x] Leave ratings and reviews for completed services.

**Admins:**
- [x] Elevate a user to admin status.
- [x] Introduce new service categories and other pertinent entities.
- [x] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [x] Users can add and edit a profile description.
- [x] Users can select their skills to show on the user's profile.
- [x] Admins can add new skill options.
- [x] Admins can monitor system statistics.
- [x] Clients can see detailed service purchase history with review submission system.

## Running

    sqlite3 database/sixer.db < database/database.sql
    php -S localhost:9000

If desired, you can populate the database with mock data to explore the project's features more easily using this command:

    sqlite3 database/sixer.db < database/insert_mock_data.sql

## Credentials

These credentials are for the mock data provided above:

- simao.barbosa.05@gmail.com/simao123   (admin)
- croc@gmail.com/simao123   (not admin)

