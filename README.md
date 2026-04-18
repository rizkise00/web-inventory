# Web Inventory Management System

A modern web-based inventory management system built with Laravel and Blade templates, featuring stock-in and stock-out tracking, user management, and approval workflows.

## Features

- **User Management**
  - User registration and authentication
  - Role-based access control (Admin/User)
  - User approval workflow (Approve/Reject)
  - User profile management

- **Stock In Management**
  - Record incoming stock/items
  - Track supplier information
  - View stock-in history
  - CRUD operations with confirmation dialogs

- **Stock Out Management**
  - Record outgoing stock/items
  - Track recipient information
  - View stock-out history
  - CRUD operations with confirmation dialogs

- **Dashboard**
  - Overview of inventory statistics
  - Quick access to all features

- **Modern UI/UX**
  - Responsive design with Tailwind CSS
  - SweetAlert2 confirmation dialogs
  - Clean and intuitive interface
  - Color-coded action buttons
  - Gradient-free solid color styling

## Tech Stack

- **Backend**: Laravel 11+
- **Frontend**: Blade Templates, Tailwind CSS 4.2
- **JavaScript**: SweetAlert2 for confirmations
- **Build Tool**: Vite
- **Database**: SQLite (configurable)

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM or Yarn

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd web-inventory
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials
   - Default configuration uses SQLite

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

   Or for development with hot-reload:
   ```bash
   npm run dev
   ```

## Running the Application

### Development Mode

Start the Laravel development server:
```bash
php artisan serve
```

In a separate terminal, start Vite for asset compilation:
```bash
npm run dev
```

The application will be available at: `http://127.0.0.1:8000`

### Production Mode

Build the assets:
```bash
npm run build
```

Then start the server:
```bash
php artisan serve
```

## Default User Roles

- **Admin**: Full access to all features including user approval
- **User**: Can manage stock-in and stock-out records

## Usage

### First Time Setup

1. Register a new account or use admin credentials
2. Admin users need to approve new user registrations
3. Once approved, users can access the inventory system

### Managing Stock

1. **Stock In**: Navigate to "Stock Masuk" to record incoming items
2. **Stock Out**: Navigate to "Stock Keluar" to record outgoing items
3. All CRUD operations include SweetAlert confirmation dialogs

### User Management (Admin Only)

1. Navigate to "Users" to view all registered users
2. Approve or reject pending user registrations
3. Edit or delete user accounts

## Project Structure

```
web-inventory/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── StockInController.php
│   │   │   ├── StockOutController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       ├── CheckAdmin.php
│   │       └── CheckApproved.php
│   └── Models/
│       ├── StockIn.php
│       ├── StockOut.php
│       └── User.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── auth/
│   │   ├── users/
│   │   ├── stock-in/
│   │   ├── stock-out/
│   │   └── dashboard.blade.php
│   ├── js/
│   │   └── app.js
│   └── css/
│       └── app.css
├── routes/
│   └── web.php
└── database/
    └── migrations/
```

## Key Features Implementation

### SweetAlert2 Confirmations

All destructive actions (delete, reject, logout) and important actions (approve) use SweetAlert2 confirmation dialogs:

- **Approve User**: Green confirmation button
- **Reject User**: Red warning dialog
- **Delete Records**: Red warning dialog with "cannot be undone" message
- **Logout**: Question dialog for confirmation

### UI Consistency

All index pages follow a consistent design pattern:
- Simple header with title and action button
- Clean table layout with gray gradient headers
- Solid color action buttons (Blue: View, Yellow: Edit, Red: Delete)
- Rounded badges for status and quantities
- No gradient backgrounds on page containers

## Development

### Adding New Features

1. Create controller: `php artisan make:controller YourController`
2. Create model: `php artisan make:model YourModel -m`
3. Add routes in `routes/web.php`
4. Create views in `resources/views/`
5. Build assets: `npm run build`

### Styling Guidelines

- Use Tailwind CSS utility classes
- Action buttons use solid colors (no gradients):
  - View: `bg-blue-600`
  - Edit: `bg-yellow-500`
  - Delete: `bg-red-600`
  - Approve: `bg-green-600`
  - Reject: `bg-red-600`
- Use `@push('scripts')` for page-specific JavaScript
- Include SweetAlert2 confirmations for important actions

## License

This project is open-source and available under the MIT License.

## Support

For issues, questions, or contributions, please create an issue in the repository.
