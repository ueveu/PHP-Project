# Marvin - PHP Weblog System

<details>
<summary>🇩🇪 Deutsche Version</summary>

Ein modernes PHP-Blogsystem mit Benutzer-Authentifizierung, Admin-Dashboard und Kontaktformular. Verwendet Textdateien zur Datenspeicherung.

## 🚀 Features

- **Benutzer-Authentifizierung**
  - Registrierung mit Vorname, Nachname und Alias
  - Login mit Alias und Passwort
  - "Angemeldet bleiben" Funktion
  - Sicheres Passwort-Hashing

- **Blog-System**
  - Beiträge erstellen und verwalten
  - Übersichtliche Beitragsliste
  - Detailansicht für einzelne Beiträge

- **Admin-Dashboard**
  - Benutzerübersicht
  - Statistiken zu Beiträgen und Benutzern
  - Kontaktanfragen-Verwaltung
  - System-Wartungsfunktionen

- **Kontaktformular**
  - Einfache Kontaktaufnahme
  - Validierung der Eingaben
  - Speicherung der Nachrichten

## 📋 Voraussetzungen

- PHP 7.4 oder höher
- Apache Webserver
- Schreibrechte für das `data` Verzeichnis (wichtig für die Datenspeicherung)

## 🛠 Installation

1. **Projekt herunterladen**
   ```bash
   git clone https://github.com/IhrUsername/marvin.git
   cd marvin
   ```

2. **Webserver konfigurieren**
   - Projekt in das Webserver-Verzeichnis kopieren (z.B. `C:\xampp\htdocs\marvin` für XAMPP)
   - Apache mod_rewrite aktivieren (falls noch nicht geschehen)

3. **Datenspeicherung einrichten**
   - Erstellen Sie das Verzeichnis `data` im Projektroot (falls nicht vorhanden)
   - Setzen Sie die korrekten Schreibrechte:
     ```bash
     chmod 755 data/
     chmod 644 data/*.txt
     ```
   - Das System erstellt automatisch die benötigten .txt Dateien

## 👤 Admin-Zugang

Der erste registrierte Benutzer wird automatisch zum Administrator. 
Alternativ können Sie sich mit diesen Zugangsdaten einloggen:

- **Alias:** admin
- **Passwort:** Passw0rd!

## 🔒 Sicherheit

- Passwort-Hashing mit PHP's password_hash
- XSS-Schutz durch htmlspecialchars
- Validierung aller Eingaben
- Schutz vor CSRF-Angriffen

## 📁 Projektstruktur

```
marvin/
├── admin/                 # Admin-Bereich
│   ├── assets/           # Admin-spezifische Assets
│   │   └── css/         # Admin-CSS-Dateien
│   ├── templates/       # Admin-Templates
│   ├── index.php        # Admin-Dashboard
│   └── maintenance.php  # Wartungsfunktionen
├── assets/               # Frontend Assets
│   ├── css/            # Stylesheet-Dateien
│   ├── js/             # JavaScript-Dateien
│   └── images/         # Bilder und Uploads
│       └── gallery/    # Galerie-Bilder
├── data/                 # Datenspeicherung (.txt)
├── includes/             # PHP-Funktionen
│   ├── config.php      # Konfigurationsdatei
│   ├── post_functions.php    # Blog-Funktionen
│   ├── user_functions.php    # Benutzer-Funktionen
│   └── validation.php   # Eingabevalidierung
├── templates/            # Frontend-Templates
│   └── layout.php      # Haupt-Layout
├── calculator.php        # Taschenrechner
├── contact.php          # Kontaktformular
├── create-post.php      # Beitrag erstellen
├── gallery.php          # Bildergalerie
├── index.php            # Startseite/Blog-Übersicht
├── login.php            # Login-Seite
├── logout.php           # Logout-Funktion
├── post.php             # Einzelner Blogbeitrag
└── register.php         # Registrierung
```

## 🛟 Fehlerbehebung

### Häufige Probleme

1. **Schreibrechte-Fehler**
   - Überprüfen Sie die Berechtigungen des `data` Verzeichnisses
   - Stellen Sie sicher, dass der Webserver-Benutzer Schreibrechte hat
   ```bash
   chmod 755 data/
   chmod 644 data/*.txt
   ```

2. **Seite nicht gefunden**
   - Überprüfen Sie die .htaccess-Datei
   - Aktivieren Sie mod_rewrite in Apache

## 📧 Support

Bei Fragen oder Problemen:
- Issue auf GitHub öffnen
- Kontaktformular auf der Website nutzen

## 📝 Lizenz

Dieses Projekt ist unter der MIT-Lizenz lizenziert. Details in der [LICENSE](LICENSE) Datei.

</details>

<details open>
<summary>🇬🇧 English Version</summary>

A modern PHP blog system with user authentication, admin dashboard, and contact form. Uses text files for data storage.

## 🚀 Features

- **User Authentication**
  - Registration with first name, last name, and alias
  - Login with alias and password
  - "Remember me" functionality
  - Secure password hashing

- **Blog System**
  - Create and manage posts
  - Clear post overview
  - Detailed view for individual posts

- **Admin Dashboard**
  - User overview
  - Statistics for posts and users
  - Contact request management
  - System maintenance functions

- **Contact Form**
  - Easy contact submission
  - Input validation
  - Message storage

## 📋 Prerequisites

- PHP 7.4 or higher
- Apache Web Server
- Write permissions for the `data` directory (crucial for data storage)

## 🛠 Installation

1. **Download Project**
   ```bash
   git clone https://github.com/IhrUsername/marvin.git
   cd marvin
   ```

2. **Configure Web Server**
   - Copy project to web server directory (e.g., `C:\xampp\htdocs\marvin` for XAMPP)
   - Enable Apache mod_rewrite (if not already enabled)

3. **Set Up Data Storage**
   - Create the `data` directory in the project root (if it doesn't exist)
   - Set the correct permissions:
     ```bash
     chmod 755 data/
     chmod 644 data/*.txt
     ```
   - The system will automatically create the necessary .txt files

## 👤 Admin Access

The first registered user automatically becomes an administrator.
Alternatively, you can log in with these credentials:

- **Alias:** admin
- **Password:** Passw0rd!

## 🔒 Security

- Password hashing with PHP's password_hash
- XSS protection through htmlspecialchars
- Input validation
- CSRF attack protection

## 📁 Project Structure

```
marvin/
├── admin/                 # Admin area
│   ├── assets/           # Admin-specific assets
│   │   └── css/         # Admin CSS files
│   ├── templates/       # Admin templates
│   ├── index.php        # Admin dashboard
│   └── maintenance.php  # Maintenance functions
├── assets/               # Frontend assets
│   ├── css/            # Stylesheet files
│   ├── js/             # JavaScript files
│   └── images/         # Images and uploads
│       └── gallery/    # Gallery images
├── data/                 # Data storage (.txt)
├── includes/             # PHP functions
│   ├── config.php      # Configuration file
│   ├── post_functions.php    # Blog functions
│   ├── user_functions.php    # User functions
│   └── validation.php   # Input validation
├── templates/            # Frontend templates
│   └── layout.php      # Main layout
├── calculator.php        # Calculator
├── contact.php          # Contact form
├── create-post.php      # Create post
├── gallery.php          # Image gallery
├── index.php            # Homepage/Blog overview
├── login.php            # Login page
├── logout.php           # Logout function
├── post.php             # Single blog post
└── register.php         # Registration
```

## 🛟 Troubleshooting

### Common Issues

1. **Permission Errors**
   - Check the permissions of the `data` directory
   - Ensure the web server user has write permissions
   ```bash
   chmod 755 data/
   chmod 644 data/*.txt
   ```

2. **Page Not Found**
   - Check the .htaccess file
   - Enable mod_rewrite in Apache

## 📧 Support

For questions or issues:
- Open an issue on GitHub
- Use the contact form on the website

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

</details>