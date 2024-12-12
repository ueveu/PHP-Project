# Marvin Web Application

A feature-rich PHP web application that provides user authentication, content management, gallery functionality, and more.

## 🚀 Features

- User Authentication (Login/Register)
- Blog Post Creation and Management
- Image Gallery
- Contact Form
- Calculator Tool
- Admin Dashboard
- Responsive Design

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Apache Web Server
- XAMPP (recommended) or similar local development environment

## 🔧 Installation

1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/yourusername/marvin.git
   ```

2. Place the project in your web server's root directory:
   - For XAMPP: `C:\xampp\htdocs\marvin`
   - For other servers: Refer to your server's documentation

3. Import the database:
   - Open phpMyAdmin
   - Create a new database named 'marvin'
   - Import the database structure from `data/database.sql`

4. Configure database connection:
   - Navigate to `includes/config.php`
   - Update the database credentials if necessary

## 💻 Usage

### User Features

1. **Registration & Login**
   - Visit `/register.php` to create a new account
   - Use `/login.php` to access your account
   - Logout via `/logout.php`

2. **Blog Posts**
   - Create new posts through `/create-post.php`
   - View posts on the homepage and individual post pages
   - Comment on posts (requires login)

3. **Gallery**
   - Browse images in the gallery section
   - Upload new images (requires login)

4. **Calculator**
   - Access the calculator tool for basic calculations

5. **Contact**
   - Use the contact form to send messages to administrators

### Admin Access

To access the admin dashboard:
1. Login with these credentials:
   - Alias: `admin`
   - Password: `Passw0rd!`
2. Access the admin dashboard at `/admin`
3. Features available:
   - User management
   - Post management
   - Contact message overview
   - System statistics

## 🔒 Security

- All user passwords are securely hashed
- Input validation and sanitization implemented
- CSRF protection enabled
- XSS prevention measures in place

## 📁 Project Structure

```
marvin/
├── admin/           # Admin dashboard files
├── assets/          # Static assets (CSS, JS, images)
├── data/           # Database files
├── includes/       # PHP includes and functions
├── templates/      # Template files
├── index.php       # Homepage
├── login.php       # User login
├── register.php    # User registration
├── create-post.php # Post creation
├── post.php        # Single post view
├── gallery.php     # Image gallery
├── contact.php     # Contact form
├── calculator.php  # Calculator tool
└── logout.php      # Logout handler
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support, please:
- Open an issue in the repository
- Contact the administrator through the contact form
- Check the documentation in the admin dashboard

## 🔄 Updates

Check the repository regularly for updates and new features. Pull the latest changes to stay up to date.