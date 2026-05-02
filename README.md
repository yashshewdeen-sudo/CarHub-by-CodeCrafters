# 🚗 CarHub – Online Car Marketplace

CarHub is a full-stack web application designed to simplify the process of buying and selling cars. 
The system provides a clean user interface, secure authentication, listing management, messaging 
between buyers and sellers, and an admin panel for system moderation.

This project was created as part of the Web Technologies & Security module.


# Features

# User Features
- Register and login securely (buyers/sellers)
- Browse all car listings
- Search and filter cars by make, model, year, price, etc.
- View detailed listing pages with images and documents
- Send messages to car sellers
- Leave reviews after successful transactions

# Seller Features
- Create car listings with images and documents
- Edit or delete listings
- Track listing approval status (Pending / Approved / Rejected)

# Admin Features
- Approve or reject car listings
- Manage users (block/unblock)
- Monitor system activity
- Restrict access using role-based permissions


# Technologies Used

# Frontend
- HTML5
- CSS3 / Bootstrap
- JavaScript (Client-side Validation)

# Backend
- PHP (Server-side Validation, CRUD operations)
- MySQL / phpMyAdmin (Database)

# Tools
- GitHub (Version Control)
- Figma (UI/UX Prototype)
- VS Code


# Project Structure
/assets
/css → Stylesheets
/js → Client-side validation scripts
/images → UI images

/backend
/auth → Login / register handling
/database → DB connection + queries (CRUD)
/validation → Server-side validation logic

/admin
dashboard.php
manage_users.php
approve_listings.php

/frontend
home.php
listings.php
view_car.php
sell_car.php
login.php
register.php

/db
carhub_db.sql


# Database
The project uses a fully relational database with:
- Users
- Car Listings
- Car Images
- Documents
- Messages
- Reviews
- Transactions

All tables include primary keys, foreign keys, and referential integrity.


# Security Features
- HTML5 + JS client-side validation
- PHP server-side validation
- Password hashing (bcrypt)
- Prepared statements (PDO) to prevent SQL injection
- Session-based authentication
- Role-based access control (User / Seller / Admin)
- `robots.txt` blocking sensitive pages
- Restricted access to admin routes


# Project Video
A demonstration video is included in the submission showcasing:
- Functional requirements
- Frontend development
- Backend logic
- Database implementation
- Design decisions (Figma → Code)
- Security considerations
- Admin panel demonstration


# Contributors

- **Aditya Ramdeworsing** – HTML & CSS (Frontend Layout and Styling)
- **Yash Shewdeen** – Client-side Validation / HTML5 Validation (JavaScript + Form Constraints)
- **Haarikrishna Murdhen** – PHP Server-side Validations (Backend input sanitisation & security)
- **Priyanka Teeluckdharee** – PHP + Database Operations (CRUD functionality and DB integration)
- **Mohita Unmole** – Admin Panel & Access Control (Role-based permissions and dashboard)
---

# Contact
For any questions regarding this project, feel free to reach out to us.


# License
This project is for academic purposes only.


