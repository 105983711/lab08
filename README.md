# User Profile System

A PHP web application for user authentication and profile management.

## Setup

1. Place all files in your XAMPP htdocs folder
2. Start Apache and MySQL in XAMPP
3. Create a MySQL database called `user_profile` with a `user`

CREATE DATABASE user_profile;
 table containing:
   - username (primary key)
   - password
   - email
4. Add a test user with your credentials

## Files

- `index.html` - Landing page
- `login.php` - User login
- `profile.php` - Profile display and editing
- `update_profile.php` - Handles profile updates
- `logout.php` - User logout
- `db_connection.php` - Database connection

## Usage

1. Access `http://localhost/user_profile/`
2. Login with your credentials
3. View and edit your profile information
4. Logout when finished

## Features

- User authentication with sessions
- Profile viewing and editing
- Secure form handling
- Responsive design

The system demonstrates PHP session management, database operations, and form processing in a web application context.