# Arup Enterprise — Industrial Machinery & Magnetic Separation Systems

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%20%2F%20MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Apache](https://img.shields.io/badge/Server-Apache%202.4-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![PHPMailer](https://img.shields.io/badge/Mailer-PHPMailer%206.x-brightgreen?style=for-the-badge)](https://github.com/PHPMailer/PHPMailer)
[![Security](https://img.shields.io/badge/Security-Hardened%20.htaccess-blue?style=for-the-badge)](https://owasp.org/)

A full-featured, enterprise-grade web application and content management system (CMS) engineered for **Arup Enterprise** — an industrial machinery manufacturing pioneer established in 1986 in Kolkata, India. Specializing in high-intensity magnetic separation systems, industrial pulleys, drum fabrication, and custom steel engineering solutions for heavy manufacturing plants across India.

---

## 🏭 About the Business

Founded in 1986 and based in Tangra, Kolkata, **Arup Enterprise** is a manufacturer of magnetic equipment and heavy industrial separation machinery. The company supplies custom-engineered separation solutions for:

* **Iron and Steel Plants** — High-capacity ferrous slag extraction and recovery
* **Cement & Mineral Processing** — Crusher protection and raw material purification
* **Casting & Foundry Facilities** — High-efficiency sand and scrap reclamation
* **Sponge Iron & Pig Iron Plants** — Continuous-duty magnetic drum separation
* **Bulk Material Handling** — Heavy-duty magnetic pulleys, suspension magnets, and stainless steel fabrication

---

## ✨ Application Features

### 🌐 Client-Facing Frontend
* **Dynamic Machinery Catalog**: Categorized showcase of industrial products with rich specifications, dimensions, features, and high-resolution machinery photography.
* **Smart Search & Filter**: Real-time filtering across industrial product categories and sub-categories.
* **Interactive RFQ & Quotation System**: Modal-based and page-level Request for Quote (RFQ) forms that capture lead details, target equipment, and technical inquiries.
* **Automated Email Dispatch**: Native integration with PHPMailer to send immediate HTML and plain-text enquiry notifications to sales administrators with spam/bot protection.
* **Dynamic Announcement Strip**: Centrally-controlled marquee alert banner for trade expos, product launches, or factory updates.
* **Machinery & Facility Gallery**: High-definition image showcase displaying on-site fabrication, heavy machine assemblies, and workshop operations.
* **Modern Responsive Interface**: Mobile-first responsive UI built with modern typography, smooth animations, and optimized asset loading.

### 🛡️ Admin Management Portal (CMS)
* **Secure Authentication**: Hash-protected login (`password_verify` with bcrypt), session hardening, and brute-force mitigations.
* **Product Catalog Management**: Complete CRUD operations for machinery listings including name, category, technical specs, features, and image uploads.
* **Category Hierarchy Manager**: Organize and reorder equipment categories, manage visibility toggles, and pin featured categories.
* **Lead & Enquiry Tracking Inbox**: Centralized dashboard to view, review, search, and manage incoming quotation requests and customer inquiries.
* **Live Announcement Management**: Control global announcement text, link destinations, and visibility directly from the dashboard.
* **Company & Profile Settings**: Manage company contact details, address, notification emails, and change admin credentials securely.

---

## 🔒 Security & Architecture Highlights

The application follows security best practices designed for shared hosting and cloud environments:

* **Strict `public_html` Web Root Isolation**:
  Only user-facing files are placed in `public_html/`. Critical assets — such as `.env` credentials, `.git/`, and `database/schema.sql` — sit one level above the document root where they cannot be accessed or downloaded by HTTP/HTTPS clients.
* **Zero-Dependency `.env` Architecture**:
  Custom lightweight parser loads environment variables without requiring heavy third-party Composer runtimes, making deployments lean and fast.
* **Multi-Tiered Apache Protection (`.htaccess`)**:
  * Prevents directory browsing (`Options -Indexes`).
  * Denies web access to hidden files (`.*`) and sensitive extensions (`.env`, `.sql`, `.log`, `.md`, `.json`, `.yml`).
  * Enforces modern HTTP security headers: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`, and `Referrer-Policy`.
  * Clean URL rewrites (eliminates `.php` extensions from browser URLs).
* **Speed & Bandwidth Optimization**:
  Includes automated Gzip/Deflate compression and browser caching headers for static assets (images, CSS, JS, fonts).

---

## 🛠️ Technology Stack

| Layer | Technologies |
|---|---|
| **Backend** | PHP 8.0+ (Native Procedural & Object-Oriented, MVC-inspired clean separation) |
| **Database** | MySQL 8.0+ / MariaDB (MySQLi with Prepared Statements) |
| **Mail Engine** | PHPMailer 6.x (SMTP, SSL/TLS, Authenticated Relay) |
| **Frontend** | Semantic HTML5, Vanilla CSS3 (Flexbox & CSS Grid), JavaScript (ES6+) |
| **Libraries & Icons** | FontAwesome 6, Google Fonts (*Inter*, *Bebas Neue*), Bootstrap 5 (Admin components) |
| **Server** | Apache 2.4+ (`mod_rewrite`, `mod_deflate`, `mod_headers`, `mod_expires`) |

---

## 📂 Project Directory Structure

```text
arup-enterprise/
├── .github/
│   └── workflows/
│       └── deploy.yml              # CI/CD deployment workflow (Hostinger SSH)
├── database/
│   ├── .htaccess                   # Strict Apache block for database folder
│   └── schema.sql                  # Complete database schema and initial seed data
├── public_html/                    # Public web root (Served by Apache)
│   ├── .htaccess                   # Core routing, security headers & caching
│   ├── index.php                   # Homepage with hero slider, catalog & quote form
│   ├── about.php                   # Company history, capabilities & industries
│   ├── products.php                # Industrial product listings with category filters
│   ├── product-detail.php          # Deep technical specifications & RFQ modal
│   ├── gallery.php                 # Workshop & machinery installation gallery
│   ├── contact.php                 # Contact directory & inquiry submission form
│   ├── admin/                      # Secured administrative portal
│   │   ├── .htaccess               # Admin route guards & indexing block
│   │   ├── login.php               # Admin login screen
│   │   ├── dashboard.php           # CMS overview & activity statistics
│   │   ├── products.php            # Product management CRUD
│   │   ├── categories.php          # Category management CRUD
│   │   ├── enquiries.php           # Customer enquiry inbox & lead status
│   │   ├── announcements.php       # Live notification strip editor
│   │   ├── settings.php            # Company info & admin password manager
│   │   ├── includes/               # Admin navbar, sidebar, and db handler
│   │   └── uploads/                # Admin uploaded media files
│   ├── assets/                     # CSS stylesheets, JS scripts & media
│   ├── images/                     # Logos, brand badges & fallback images
│   ├── includes/                   # Shared templates & logic
│   │   ├── .htaccess               # Blocks direct browser execution of PHP includes
│   │   ├── env.php                 # Lightweight environment file loader
│   │   ├── db.php                  # Database connection with auto-reconnect
│   │   ├── header.php              # Global navigation header & announcement strip
│   │   └── footer.php              # Global footer, company links & copyright
│   ├── PHPMailer/                  # Bundled PHPMailer library (.htaccess protected)
│   └── uploads/                    # Product images & user submissions
├── .env                            # Active environment configuration (gitignored)
├── .env.example                    # Sample environment template
├── .gitignore                      # Git exclusion rules
├── .htaccess                       # Root security shield & local dev URL bridge
└── README.md                       # Comprehensive project documentation
```

---

## 🚀 Getting Started (Local Development)

### 1. Prerequisites
* **Web Server**: Apache 2.4+ with `mod_rewrite` enabled (e.g., XAMPP, WAMP, or standalone Apache)
* **PHP**: Version 8.0 or higher with `mysqli`, `openssl`, and `mbstring` extensions
* **Database**: MySQL 5.7+ or MariaDB 10.4+

### 2. Installation
1. Clone the repository into your local server directory (e.g., `d:/xampp/htdocs/`):
   ```bash
   git clone https://github.com/Abhiraj0406/arup-enterprise.git
   cd arup-enterprise
   ```

2. Configure Environment Variables:
   Copy `.env.example` to `.env` in the project root:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and set your local database credentials:
   ```env
   DB_HOST=localhost
   DB_NAME=arup_enterprise_db
   DB_USER=root
   DB_PASS=
   APP_ENV=development
   APP_URL=http://localhost/arup-enterprise/public_html
   ```

3. Import Database Schema:
   * Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
   * Create a new database named `arup_enterprise_db` with `utf8mb4_unicode_ci` collation.
   * Import the file located at `database/schema.sql`.

4. Run the Application:
   * Open your browser and navigate to:
     ```text
     http://localhost/arup-enterprise/public_html/
     ```
   *(Or `http://localhost/arup-enterprise/` if accessing through the root bridge).*

---

## 🔐 Administrative Access

The admin portal provides complete management over products, categories, enquiries, and site settings.

* **URL**: `http://localhost/arup-enterprise/public_html/admin/`
* **Default Super Admin**:
  * **Email**: `admin@gmail.com`
  * **Password**: *Defined in your initial `database/schema.sql` seed*

> **Security Note**: Immediately change the administrator email and password from the **Settings** page upon initial deployment.

---

## 📄 License & Attribution

Copyright © 1986–2026 **Arup Enterprise**. All rights reserved.  
Proprietary software developed for Arup Enterprise manufacturing operations and digital customer acquisition.
