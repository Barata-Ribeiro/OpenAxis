<div align="center">
    <img alt="The main logo of OpenAxis" width="400" src="public/logo.svg" title="OpenAxis ERP Logo"/>
</div>

<p align="center">
    <strong>OpenAxis ERP</strong> is a modern, full-stack Enterprise Resource Planning system built with Laravel and React. It provides comprehensive business management tools including inventory control, financial management, sales and purchasing workflows, and client relationship management.
</p>

**⚠️ Development Notice:** This project is under active development. The current version may contain bugs and incomplete features. Use at your own risk.

## 📚 Features

### Core Modules

- **Inventory Management** - Track products, stock levels, and warehouse operations
- **Sales Management** - Create and manage sales orders, commercial proposals, and quotes
- **Purchase Management** - Handle purchase orders, supplier relationships, and procurement
- **Financial Management** - Manage accounts receivable, payable, and payment conditions
- **Client & Supplier Management** - Comprehensive CRM for business partners
- **Product Catalog** - Detailed product information with media support

### Administrative Features

- **User Management** - Role-based access control with granular permissions
- **Authentication** - Secure login with two-factor authentication (2FA)
- **Audit Logging** - Complete activity tracking and audit trails
- **Real-time Updates** - WebSocket integration for live notifications
- **Dashboard Analytics** - Business insights and reporting
- **Settings Management** - Customizable system configuration

### Technical Features

- **Modern UI/UX** - Responsive design with dark mode support
- **Type-safe Routing** - Laravel Wayfinder for frontend route generation
- **Real-time Broadcasting** - Laravel Reverb for WebSocket connections
- **Media Library** - File uploads and management with Spatie Media Library
- **Developer Tools** - Telescope for debugging, Debugbar for development insights
- **Comprehensive Testing** - Pest 4 with browser testing capabilities

## 🚀 Built With

### Backend

- **[PHP 8.4](https://www.php.net/)** - Latest PHP version with performance improvements
- **[Laravel 12](https://laravel.com/)** - Modern PHP framework
- **[Laravel Fortify](https://laravel.com/docs/fortify)** - Authentication backend
- **[Laravel Reverb](https://reverb.laravel.com/)** - WebSocket server
- **[Laravel Telescope](https://laravel.com/docs/telescope)** - Application debugging
- **[Laravel Wayfinder](https://github.com/laravel/wayfinder)** - Type-safe routing
- **[Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)** - Role and permission management
- **[Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)** - Media management
- **[Laravel Auditing](https://github.com/owen-it/laravel-auditing)** - Activity logging

### Frontend

- **[React 19](https://react.dev/)** - Modern React with compiler
- **[Inertia.js 2](https://inertiajs.com/)** - SPA framework for Laravel
- **[TypeScript 5](https://www.typescriptlang.org/)** - Type-safe JavaScript
- **[Tailwind CSS 4](https://tailwindcss.com/)** - Utility-first CSS framework
- **[Vite 7](https://vitejs.dev/)** - Fast build tool
- **[Radix UI](https://www.radix-ui.com/)** - Accessible component primitives
- **[TanStack Table](https://tanstack.com/table)** - Powerful table library
- **[Framer Motion](https://motion.dev/)** - Animation library
- **[React Day Picker](https://react-day-picker.js.org/)** - Date picker component

### Development Tools

- **[Pest 4](https://pestphp.com/)** - Testing framework with browser support
- **[Laravel Pint](https://laravel.com/docs/pint)** - Code style fixer
- **[ESLint](https://eslint.org/)** - JavaScript linter
- **[Prettier](https://prettier.io/)** - Code formatter
- **[Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)** - Debug toolbar
- **[Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)** - IDE autocomplete

## 🛠️ Project Setup

### Prerequisites

- PHP 8.4 or higher
- The following PHP extensions:
    - bz2
    - curl
    - fileinfo
    - gd
    - gettext
    - intl
    - mbstring
    - exif
    - mysqli
    - pdo_mysql
    - pdo_sqlite
    - zip
    - php_openssl.dll
    - php_ftp.dll
- Composer 2.x
- Node.js 22.x or higher
- npm or yarn
- SQLite (default) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/Barata-Ribeiro/OpenAxis.git
    cd OpenAxis
    ```

2. **Install dependencies and setup**

    ```bash
    composer run setup
    ```

    This command will:
    - Install PHP dependencies
    - Copy `.env.example` to `.env`
    - Generate application key
    - Run database migrations
    - Install Node.js dependencies
    - Build frontend assets

3. **Configure environment**

    ```bash
    # Edit .env file with your settings
    cp .env.example .env
    php artisan key:generate
    ```

4. **Run database migrations and seeders (optional)**
    ```bash
    php artisan migrate:fresh --seed
    ```

### Development

**Start the development server:**

```bash
composer run dev
```

This starts:

- Laravel development server (http://localhost:8000)
- Queue worker
- Vite dev server (hot reload)
- Reverb WebSocket server

**With SSR support:**

```bash
composer run dev:ssr
```

This additionally starts:

- Inertia SSR server
- Laravel Pail (log viewer)

### Testing

**Run all tests:**

```bash
composer run test
```

**Run specific test file:**

```bash
php artisan test tests/Feature/ExampleTest.php
```

**Run with filter:**

```bash
php artisan test --filter=testName
```

### Code Quality

**Format PHP code:**

```bash
vendor/bin/pint
```

**Format JavaScript/TypeScript:**

```bash
npm run format
```

**Lint JavaScript/TypeScript:**

```bash
npm run lint
```

**Type check:**

```bash
npm run types
```

### Building for Production

```bash
npm run build
```

**With SSR:**

```bash
npm run build:ssr
```

## 🗂️ Folder Structure

```
openaxis/
├── app/                          # Application core
│   ├── Actions/                  # Business logic actions
│   │   └── Fortify/              # Fortify authentication actions
│   ├── Common/                   # Shared utilities
│   ├── Console/                  # Artisan commands
│   ├── Enums/                    # PHP enumerations
│   ├── Http/                     # HTTP layer
│   │   ├── Controllers/          # Route controllers
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form request validation
│   ├── Interfaces/               # Business logic interfaces
│   │   ├── Admin/                # Administrative interfaces
│   │   ├── Management/           # Management interfaces
│   │   ├── Product/              # Product interfaces
│   │   └── Settings/             # Settings interfaces
│   ├── Mail/                     # Email templates
│   ├── Models/                   # Eloquent models
│   ├── Notifications/            # User notifications
│   ├── Providers/                # Service providers
│   ├── Rules/                    # Custom validation rules
│   └── Services/                 # Application services
│
├── bootstrap/                    # Application bootstrap
│   ├── app.php                   # Application configuration
│   ├── providers.php             # Service provider registration
│   └── cache/                    # Framework cache
│
├── config/                       # Configuration files
│   ├── app.php                   # Application config
│   ├── database.php              # Database config
│   ├── fortify.php               # Authentication config
│   └── ...                       # Other configs
│
├── database/                     # Database files
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
│
├── public/                       # Public assets
│   └── build/                    # Compiled frontend assets
│
├── resources/                    # Frontend resources
│   ├── css/                      # Stylesheets
│   ├── js/                       # JavaScript/TypeScript
│   │   ├── actions/              # Wayfinder generated routes
│   │   ├── components/           # React components
│   │   │   ├── application/      # App shell components
│   │   │   ├── forms/            # Form components
│   │   │   ├── navigation/       # Navigation components
│   │   │   ├── table/            # Table components
│   │   │   └── ui/               # UI primitives
│   │   ├── hooks/                # React hooks
│   │   ├── layouts/              # Page layouts
│   │   ├── pages/                # Page components
│   │   │   ├── administrative/   # Admin pages
│   │   │   ├── auth/             # Auth pages
│   │   │   ├── dashboards/       # Dashboard pages
│   │   │   ├── erp/              # ERP module pages
│   │   │   └── settings/         # Settings pages
│   │   ├── routes/               # Named routes
│   │   ├── types/                # TypeScript definitions
│   │   └── lib/                  # Utilities
│   └── views/                    # Blade templates
│
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes
│   ├── administrative.php        # Admin routes
│   ├── erp.php                   # ERP routes
│   ├── settings.php              # Settings routes
│   ├── console.php               # Console routes
│   └── channels.php              # Broadcast channels
│
├── storage/                      # Storage directory
│   ├── app/                      # Application storage
│   ├── framework/                # Framework files
│   ├── logs/                     # Application logs
│   └── media-library/            # Media files
│
├── tests/                        # Tests
│   ├── Feature/                  # Feature tests
│   ├── Unit/                     # Unit tests
│   └── Browser/                  # Browser tests (Pest 4)
│
├── .env.example                  # Environment template
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── phpunit.xml                   # PHPUnit configuration
├── vite.config.ts                # Vite configuration
└── tsconfig.json                 # TypeScript configuration
```

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/Barata-Ribeiro/MediManage/issues) if you want to contribute.

## 📜 License

This project is free software available under the [GPLv3](LICENSE) license.
