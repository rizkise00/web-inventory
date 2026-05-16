# Web Inventory Management System

A modern, comprehensive web-based inventory management system built with Laravel 11 and Tailwind CSS. This system provides a robust solution for tracking products, categories, stock movements, and maintenance schedules with automated inventory logic.

## Key Features

- **Product & Category Management**
  - Hierarchical organization using Categories.
  - Detailed Product tracking with pricing and real-time stock levels.
  - Standardized CRUD interfaces for consistency.

- **Advanced Stock Tracking**
  - **Stock In**: Record new inventory with automatic unit price fetching and total price calculation.
  - **Stock Out**: Track outgoing items with status categorization (**Consumed** or **Damaged**) and availability validation.
  - **Exporting**: One-click export of Stock In and Stock Out data to `.xlsx` (Excel) files, supporting filtered views.

- **Maintenance Module**
  - Track products undergoing repair or service.
  - **Smart Stock Logic**:
    - Automatically deducts stock when items enter maintenance (Pending/In Progress).
    - Automatically restores stock when maintenance is marked as **Completed**.
    - Handles stock reconciliation when records are updated or deleted.

- **User & Access Control**
  - Multi-role system (**Admin** and **Manager**).
  - Manager-exclusive approval workflow for new user registrations.
  - Role-based navigation and action permissions.
  - Manager-only export for Users, Items, Categories, Stock Out, and Maintenance data.

- **Data Export**
  - All modules support one-click `.xlsx` export (Excel).
  - Export supports search/filter parameters (e.g., export only filtered items or stock records).
  - Access restricted: only Managers can export sensitive reports.

- **Modern UI/UX**
  - **Responsive Design**: Built with Tailwind CSS, featuring mobile-friendly navigation and scrollable data tables.
  - **Interactive Elements**: Real-time pricing calculations via JavaScript and smooth dropdowns powered by Alpine.js.
  - **Confirmations**: Integrated SweetAlert2 for safe data deletion and approvals.
  - **Standardized Forms**: Unified create/edit form designs across all modules.

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Excel Support**: Maatwebsite/Laravel-Excel
- **JavaScript Libraries**: SweetAlert2
- **Build Tool**: Vite
- **Database**: MySQL / SQLite

## Requirements

- PHP >= 8.2
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
| Category | 8 | ~14 |
| Item | 11 | ~17 |
| Stock In | 13 | ~22 |
| Stock Out | 14 | ~28 |
| Maintenance | 14 | ~26 |
| User Management | 17 | ~30 |
| **Total** | **83** | **177** |

### What is tested

- Guest redirect to login for all protected routes
- Role-based access: Admin vs. Manager permissions
- Unapproved user access restriction
- CRUD operations for all modules
- Stock increment/decrement accuracy on Stock In, Stock Out, and Maintenance
- Insufficient stock validation (cannot over-allocate)
- Stock reconciliation on record update and delete
- Category deletion blocked when items exist
- Manager self-delete prevention
- Excel export availability (Managers only) and access denial (non-Managers)
- Export filter parameters (search, status)
- Input validation for all forms

## Security

- **Authorization**: All sensitive routes protected by `auth`, `approved`, and `manager` middleware. Controllers perform secondary role checks where needed.
- **Mass Assignment Protection**: All controllers use `$request->only([...])` to whitelist fillable fields explicitly, preventing mass-assignment attacks.
- **Race Condition Safety**: Stock mutations are wrapped in `DB::transaction()` with `lockForUpdate()` to prevent concurrent over-allocation.
- **Stock Integrity**: Validation against current stock happens inside the database transaction to ensure accuracy under concurrent load.

## Features Roadmap
- [x] Excel Exporting
- [x] Product Categories
- [x] Maintenance Stock Integration
- [x] Responsive Data Tables
- [ ] Low Stock Notifications
- [ ] Supplier Management
