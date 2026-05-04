# Gestion-Inventarios Copilot Instructions

## Project Overview
This is a PHP-based inventory management system for CapsuleCorp, handling products, providers, employees, and inventory movements. It uses MySQL with PDO for database interactions and session-based authentication.

## Architecture
- **Database**: MySQL with tables `producto`, `proveedor`, `empleado`, `movimiento_inventario`, `proveedor_producto`
- **Connection**: `db.php` establishes PDO connection using config from `.env.php`
- **Authentication**: Session-based, stored in `$_SESSION['documento_empleado']`
- **Pages**: Procedural PHP files with embedded HTML, no framework

## Key Patterns
- **Database Access**: Always `require __DIR__ . '/db.php';` then use global `$pdo` for prepared statements
- **Session Checks**: `if (!isset($_SESSION['documento_empleado'])) { header('Location: ' . base_url('login.php')); exit; }`
- **Form Handling**: POST requests, trim inputs, validate required fields, display messages in `<div>` with inline styles
- **Output Sanitization**: Use `htmlspecialchars()` for all user data in HTML
- **Navigation**: Consistent sidebar in `aside` with `.brand` "CapsuleCorp" and nav links
- **URLs**: Use `base_url($path)` for internal links
- **Passwords**: `password_hash($pass, PASSWORD_BCRYPT)` on register, `password_verify()` on login
- **Errors**: Catch `PDOException`, display user-friendly messages

## File Structure
- `db.php`: PDO setup and `base_url()` function
- `login.php`, `register.php`: Auth pages
- `dashboard.php`: Main page with sidebar
- `add-product.php`, `list-products.php`: CRUD for products
- `add-provider.php`, `list-providers.php`: CRUD for providers
- `assets/css/style.css`: Basic styling with classes like `.btn-primary`, `.login-container`

## Development Workflow
- Run on XAMPP: Place in `htdocs`, import `gestion_inventarios.sql`, create `.env.php` with DB config
- No build tools; edit PHP files directly
- Test forms by submitting data, check database via phpMyAdmin

## Conventions
- Language: Spanish (UI text, comments, DB collation utf8mb4_spanish_ci)
- Naming: Spanish field names (e.g., `nombre_producto`, `correo_empleado`)
- Validation: Check for empty strings, duplicates (e.g., barcode uniqueness)
- Success: Clear form fields, show green message, redirect if needed
- Errors: Red background div with message</content>
<parameter name="filePath">c:\xampp\htdocs\Gestion-Inventarios\.github\copilot-instructions.md