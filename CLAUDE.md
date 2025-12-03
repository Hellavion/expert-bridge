# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# Bridge - Laravel + Vue 3 + Inertia.js Codebase Guide

## Essential Development Commands

### Setup
```bash
# Full project setup (install dependencies + migrate database)
composer setup

# Manual setup steps
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Development
```bash
# Run full development environment (Laravel + Vite + Queue + Logs)
composer dev

# Alternative: Development with Server-Side Rendering (SSR)
composer dev:ssr

# Frontend development only
npm run dev

# Build frontend (development)
npm run build

# Build frontend with SSR support
npm run build:ssr
```

### Database & Migrations
```bash
# Run migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Fresh database (drop all + migrate)
php artisan migrate:fresh
```

### Testing
```bash
# Run Pest tests
composer test

# Run specific test file
php artisan test --filter TestName

# Watch tests during development
php artisan test --watch

# Generate coverage report
php artisan test --coverage
```

### Code Quality
```bash
# Format Vue/TypeScript code with Prettier
npm run format

# Check formatting without applying changes
npm run format:check

# Lint and fix JavaScript/TypeScript issues
npm run lint

# Fix PHP code with Laravel Pint
php artisan pint
```

### Artisan Utilities
```bash
# Interactive shell with database/app access
php artisan tinker

# View logs in real-time
php artisan pail

# Generate new migration
php artisan make:migration create_table_name

# Generate model with migration and controller
php artisan make:model ModelName -mcr
```

---

## Architecture Overview

### Backend: Laravel 12 + Inertia.js

**Full-Stack SPA Architecture**: This project uses Inertia.js to bridge Laravel and Vue 3, eliminating the need for separate API/frontend codebases. Pages are rendered server-side as Vue components.

#### Route Structure
- **Public Routes** (`routes/web.php`):
  - `GET /` - Client registration page
  - `POST /register-client` - Client registration endpoint

- **Admin Routes** (`routes/web.php`, protected with `auth` + `verified` middleware):
  - `/admin/dashboard` - Admin dashboard
  - `/admin/experts/*` - CRUD for experts
  - `/admin/curators/*` - CRUD for curators
  - `/admin/products/*` - CRUD for products
  - `/admin/promocodes/*` - CRUD for promocodes
  - `/admin/clients/*` - Client management with payment toggle

- **Settings Routes** (`routes/settings.php`, protected with `auth` middleware):
  - `/settings/profile` - User profile management
  - `/settings/password` - Password change
  - `/settings/appearance` - Theme customization
  - `/settings/two-factor` - Two-factor authentication

#### Controller Pattern
Uses standard RESTful resource controllers with Inertia:
```php
// Example from ExpertController
Route::resource('experts', ExpertController::class);

// Typical controller flow:
public function index(): Response {
    $experts = Expert::withCount(['curators', 'products'])
        ->latest()
        ->get();
    return Inertia::render('Admin/Experts/Index', ['experts' => $experts]);
}
```

#### Database Models

**Core Models**:
- **User** - Admin/staff authentication with Fortify + 2FA support
- **Client** - End users registering via public form (linked to promocode)
- **Expert** - Content creators with commission tracking
- **Curator** - Content managers (relationship structure with experts)
- **Product** - Content items with pricing (belongs to Expert)
- **Promocode** - Registration codes with usage tracking
- **PromocodeUsageHistory** - Track client usage per promocode

**Key Relationships**:
```
Expert 1--* Curator
Expert 1--* Product
Expert 1--* Promocode
Promocode 1--* Client
Client 1--* PromocodeUsageHistory
Product --* Expert
```

**Important Model Fields**:
- Expert: `commission_percent` (decimal), `expert_bonus`, `is_active` (boolean)
- Promocode: Usage tracking and client linkage
- Client: Payment status toggle (boolean field)

#### Authentication & Authorization
- **Framework**: Laravel Fortify (built-in auth scaffolding)
- **Features Enabled**:
  - User registration
  - Email verification
  - Password reset
  - Two-factor authentication with recovery codes
  - Remember-me functionality
- **Session Driver**: Database-backed sessions (configurable in `.env`)
- **Middleware**:
  - `auth` - Requires authenticated user
  - `verified` - Requires email verification
  - `throttle:6,1` - Rate limiting on password change (6 attempts per minute)

#### Database Configuration
- **Default**: SQLite (`database/database.sqlite`)
- **Alternative**: MySQL/PostgreSQL (configure in `.env` via `DB_*` variables)
- **Session Storage**: Database
- **Cache Store**: Database
- **Queue Connection**: Database

---

### Frontend: Vue 3 + Inertia.js + Tailwind CSS

#### Architecture Pattern
```
resources/js/
├── app.ts              # Entry point - creates Inertia app with Vue 3
├── pages/              # Inertia page components (maps to routes)
│   ├── Welcome.vue     # Public client registration
│   ├── Dashboard.vue   # Admin dashboard
│   ├── admin/
│   │   ├── Experts/
│   │   ├── Curators/
│   │   ├── Products/
│   │   └── Promocodes/
│   ├── auth/           # Authentication pages (login, register, 2FA)
│   ├── settings/       # User settings (profile, password, appearance, 2FA)
├── layouts/            # Layout wrappers
│   ├── AppLayout.vue   # Main app wrapper with sidebar
│   ├── AuthLayout.vue  # Auth page wrapper (split layout)
│   ├── app/
│   │   ├── AppSidebarLayout.vue
│   │   ├── AppHeaderLayout.vue
│   └── settings/Layout.vue
├── components/         # Reusable UI components
│   ├── AppSidebar.vue
│   ├── AppHeader.vue
│   ├── Breadcrumbs.vue
│   ├── ui/             # Reka-UI component library (button, input, etc.)
└── composables/        # Vue 3 composition functions
    ├── useAppearance.ts    # Light/dark theme toggle
    ├── useTwoFactorAuth.ts
    └── useInitials.ts
```

#### Build & Vite Configuration
- **Build Tool**: Vite 7
- **Entry Point**: `resources/js/app.ts`
- **SSR Entry**: `resources/js/ssr.ts`
- **Key Plugins**:
  - `@vitejs/plugin-vue` - Vue 3 support
  - `laravel-vite-plugin` - Laravel integration + auto-refresh
  - `@tailwindcss/vite` - Tailwind CSS v4
  - `@laravel/vite-plugin-wayfinder` - Route helpers + form variants

#### Component Library
- **Reka UI** (`reka-ui`) - Headless component library
- **Tailwind CSS v4** - Utility-first styling
- **Class Variance Authority** - Type-safe component variants
- **Lucide Icons** (`lucide-vue-next`) - Icon library
- **VueUse** - Composition utilities

#### Page Resolution
Pages are auto-discovered from `resources/js/pages/` directory using Vite's glob:
```typescript
resolve: (name) => resolvePageComponent(
    `./pages/${name}.vue`,
    import.meta.glob<DefineComponent>('./pages/**/*.vue'),
)
```

#### Styling Features
- **Dark Mode**: System preference detection + manual toggle persistence (localStorage + cookie)
- **Tailwind v4**: Modern CSS syntax with support for dynamic values
- **Prettier Plugin**: Auto-formats Tailwind class order in Vue templates

---

## Important Patterns & Conventions

### 1. **Inertia.js Data Flow**
```php
// Backend: Pass data from controller
return Inertia::render('PageName', [
    'experts' => $experts,
    'canCreate' => auth()->user()->can('create'),
]);

// Frontend: Access as component props
defineProps<{ experts: Expert[]; canCreate: boolean }>();
```

### 2. **Form Handling with Inertia**
```vue
<Form v-bind="store.form()" v-slot="{ errors, processing }">
    <Input name="email" type="email" />
    <InputError :message="errors.email" />
    <Button :disabled="processing">Submit</Button>
</Form>
```
- Uses `@inertiajs/vue3` Form helper for validation & submission
- Server-side validation errors automatically mapped to frontend
- CSRF protection handled automatically

### 3. **Authentication Flow**
```typescript
// Laravel Fortify handles:
1. Login/Register endpoints
2. Email verification
3. Password reset
4. Two-factor setup & challenge
5. Recovery codes

// Frontend just renders the pages - no API calls needed
```

### 4. **Resource Controllers (CRUD)**
All admin entities follow RESTful conventions:
```php
Route::resource('experts', ExpertController::class);
// Generates: index, create, store, edit, update, destroy
```

### 5. **Type Safety**
- **TypeScript**: Strict mode enabled (`tsconfig.json`)
- **Vue 3**: `<script setup lang="ts">` with typed props
- **Vite**: Auto-discovers `.ts` and `.vue` files
- **ESLint + Prettier**: Automatic code formatting

### 6. **Theme Persistence**
Three-tier approach for dark mode:
1. System preference (default)
2. localStorage (client persistence)
3. Cookie (SSR compatibility)

```typescript
function updateTheme(value: 'light' | 'dark' | 'system') {
    document.documentElement.classList.toggle('dark', isDark);
}
```

### 7. **Middleware Chain**
Admin routes require two middleware checks:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Only authenticated + email-verified users can access
});
```

### 8. **Validation Pattern**
Blade validation automatic conversion via Inertia:
```php
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);
// Errors sent back as component props automatically
```

---

## Project Setup Details

### Environment Variables (`.env.example`)
```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log          # Log emails to console in dev
VITE_APP_NAME="${APP_NAME}"
```

### Dependencies Summary

**Laravel Packages**:
- `laravel/framework:^12` - Core framework
- `laravel/fortify:^1.30` - Authentication scaffolding
- `inertiajs/inertia-laravel:^2.0` - Inertia server adapter
- `laravel/wayfinder:^0.1.9` - Route path helpers

**Frontend Packages**:
- `vue:^3.5` - Progressive framework
- `@inertiajs/vue3:^2.1` - Inertia Vue adapter
- `tailwindcss:^4.1` - Utility CSS
- `reka-ui:^2.4` - Headless components
- `laravel-vite-plugin:^2.0` - Vite integration

**Dev Tools**:
- `vite:^7` - Lightning-fast build tool
- `typescript:^5.2` - Type safety
- `eslint` + `prettier` - Code quality
- `pestphp/pest:^3.8` - Modern PHP testing

---

## Special Features Implemented

1. **Two-Factor Authentication**: Full Fortify integration with recovery codes
2. **Client Registration Form**: Public-facing registration with promocode tracking
3. **Admin CRUD Dashboard**: Full management interface for experts, products, curators, promocodes
4. **Payment Tracking**: Track client payment status and toggle in admin
5. **Commission Tracking**: Expert commission percentages and bonus tracking
6. **Dark Mode**: System-aware theme with manual toggle
7. **Settings Panel**: User profile, password, appearance, 2FA management

---

## Quick Development Workflow

```bash
# 1. Start development environment
composer dev
# Runs: Laravel server + Vite dev server + Queue listener + Log tail

# 2. Open browser
http://localhost:8000

# 3. Create new admin page
# - Create component: resources/js/pages/Admin/MyPage.vue
# - Create controller: app/Http/Controllers/Admin/MyController.php
# - Add route: routes/web.php
# - Data flows: Controller -> Inertia::render() -> Vue component

# 4. Format & test
npm run format
composer test
```

---

## Testing Structure

Tests are located in `tests/` directory using Pest framework:
- **Feature tests**: Full request/response cycle tests
- **Unit tests**: Individual component/class tests

Example Pest test structure:
```php
test('description of what is being tested', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)->get('/admin/dashboard');

    // Assert
    $response->assertStatus(200);
});
```

---

## Development Notes

1. **Codebase Language**: Routes and comments may contain Russian text (Cyrillic). This is intentional for the project's target audience.

2. **Creating New Admin CRUD**: Follow the existing pattern in `app/Http/Controllers/Admin/`:
   - Controller extends base `Controller` class
   - Uses RESTful methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
   - Returns Inertia responses: `return Inertia::render('Admin/Entity/Index', $data);`

3. **Database Models Best Practices**:
   - Always define `$fillable` or `$guarded` properties
   - Use `$casts` for boolean and decimal fields
   - Define relationships using Eloquent methods (`hasMany`, `belongsTo`, etc.)

4. **Frontend Component Creation**:
   - Place reusable components in `resources/js/components/`
   - Use Reka UI components from `resources/js/components/ui/`
   - Follow TypeScript strict mode conventions
   - Use composition API with `<script setup lang="ts">`

