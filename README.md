# Web Inventory Management System

A modern, comprehensive web-based inventory management system built with Laravel 11 and Tailwind CSS. This system provides a robust solution for tracking products, categories, stock movements, and maintenance schedules with automated inventory logic.

## Key Features

- **Product & Category Management**
  - Hierarchical organization using Categories.
  - Detailed Product tracking with pricing and real-time stock levels.
  - Standardized CRUD interfaces for consistency.
  - Date range filter on Products and Categories index pages.

- **Advanced Stock Tracking**
  - **Stock In**: Record new inventory with automatic unit price fetching and total price calculation.
  - **Stock Out**: Track outgoing items with status categorization (**Consumed** or **Damaged**) and availability validation.
  - **Date Range Filter**: Filter Stock In and Stock Out records by start date and end date on the index and export.
  - **Exporting**: One-click export of all modules to `.xlsx` (Excel) files, supporting all active filters including date range.

- **Maintenance Module**
  - Track products undergoing repair or service.
  - **Smart Stock Logic**:
    - Automatically deducts stock when items enter maintenance (Pending/In Progress).
    - Automatically restores stock when maintenance is marked as **Completed**.
    - Handles stock reconciliation when records are updated or deleted.
  - Date range filter on the Maintenance index page.

- **User & Access Control**
  - Multi-role system (**Admin** and **Manager**).
  - Manager-exclusive approval workflow for new user registrations.
  - Role-based navigation and action permissions.
  - Both Admin and Manager have full access to: Products, Categories, Stock In, Stock Out, and Maintenance (CRUD + export).
  - Manager-only feature: User management (view, create, edit, approve, reject, delete) and Users export.

- **Advanced Filtering**
  - Every module index page supports a **date range filter** (From — To) based on record creation date.
  - Filters stack: search text, dropdowns (status/role/low_stock), and date range can be combined.
  - A **Reset** button appears automatically when any filter is active.
  - All active filters are preserved when exporting to Excel.

- **Data Export**
  - All modules support one-click `.xlsx` export (Excel).
  - Export respects all active filters: search, status, date range, etc.
  - Items, Categories, Stock In, Stock Out, and Maintenance exports are available to both Admin and Manager.
  - Users export is available to Manager only.

- **Modern UI/UX**
  - **Responsive Design**: Built with Tailwind CSS, featuring mobile-friendly navigation and scrollable data tables.
  - **Interactive Elements**: Real-time pricing calculations via JavaScript and smooth dropdowns powered by Alpine.js.
  - **Confirmations**: Integrated SweetAlert2 for safe data deletion and approvals.
  - **Standardized Forms**: Unified create/edit form designs across all modules.

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Excel Support**: phpoffice/phpspreadsheet 2.x (PHP 8.5 compatible)
- **JavaScript Libraries**: SweetAlert2
- **Build Tool**: Vite
- **Database**: MySQL / SQLite

## Requirements

- PHP >= 8.3 (including PHP 8.5)
- Composer
- Node.js >= 18
- NPM

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd web-inventory
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Update `.env` with your database credentials.
   - Run migrations and seed initial data:
     ```bash
     php artisan migrate:fresh --seed
     ```
   - *Default credentials: `manager@example.com` / `password` or `admin@example.com` / `password`*

5. **Build Assets**
   ```bash
   npm run build
   ```

## Development

Start the development server:
```bash
php artisan serve
```

Run Vite for hot-reloading:
```bash
npm run dev
```

## Testing

The project includes a comprehensive PHPUnit test suite covering all major features. Tests use an in-memory SQLite database and run in full isolation via `RefreshDatabase`.

**Run all tests:**
```bash
php artisan test
```

**Run a specific test file:**
```bash
php artisan test tests/Feature/ItemControllerTest.php
```

### Test Coverage

| Module | Tests | Assertions |
|---|---|---|
| Auth / Dashboard | 5 | ~10 |
| Category | 13 | ~22 |
| Item | 18 | ~28 |
| Stock In | 22 | ~40 |
| Stock Out | 23 | ~46 |
| Maintenance | 20 | ~36 |
| User Management | 23 | ~42 |
| **Total** | **124** | **224+** |

### What is tested

- Guest redirect to login for all protected routes
- Role-based access: Admin vs. Manager permissions
- Unapproved user access restriction
- CRUD operations for all modules
- Admin and Manager can perform full CRUD on Products, Categories, Stock In, Stock Out, and Maintenance
- Only Manager can access User management routes
- Stock increment/decrement accuracy on Stock In, Stock Out, and Maintenance
- Insufficient stock validation (cannot over-allocate)
- Stock reconciliation on record update and delete
- Category deletion blocked when items exist
- Manager self-delete prevention
- Excel export (Items, Categories, Stock In, Stock Out, Maintenance) available to both Admin and Manager
- Users export available to Manager only; Admin is blocked from users export route
- Export filter parameters (search, status, date range)
- Date range filter (date_from / date_to) on all module index pages
- Input validation for all forms

## Security

- **Authorization**: All sensitive routes protected by `auth`, `approved`, and `manager` middleware. Controllers perform a secondary role check to prevent privilege escalation via direct URL access.
- **Mass Assignment Protection**: All controllers use `$request->only([...])` to whitelist fillable fields explicitly, preventing mass-assignment attacks (e.g. `stock` cannot be set via item update endpoint).
- **Race Condition Safety**: Stock mutations are wrapped in `DB::transaction()` with `lockForUpdate()` to prevent concurrent over-allocation.
- **Stock Integrity**: Stock availability is validated inside the database transaction to guarantee correctness under concurrent load.
- **Financial Data Precision**: Price fields (`price`, `unit_price`, `total_price`) are cast as `decimal:2` in Eloquent models to preserve precision across operations.
- **Route Order Safety**: Explicit export routes (`/items/export`) are defined before their resource counterparts to prevent Laravel's FIFO route matching from shadowing them with the `/{id}` show route.

## PHP Version Compatibility

This project uses **phpoffice/phpspreadsheet ^2.0** which supports PHP 8.1–8.5+, making it compatible with Laravel Cloud and other modern hosting environments. The older `maatwebsite/excel 3.x` dependency (which was limited to PHP < 8.5) has been replaced.

## Features Roadmap
- [x] Excel Exporting (all modules)
- [x] Product Categories
- [x] Maintenance Stock Integration
- [x] Responsive Data Tables
- [x] Role-based Access Control
- [x] PHP 8.5 Compatibility
- [x] Date Range Filter (all modules)
- [ ] Low Stock Notifications
- [ ] Supplier Management
