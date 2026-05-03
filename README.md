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

## Features Roadmap
- [x] Excel Exporting
- [x] Product Categories
- [x] Maintenance Stock Integration
- [x] Responsive Data Tables
- [ ] Low Stock Notifications
- [ ] Supplier Management
