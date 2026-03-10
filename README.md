# 💰 Finance API

API REST para gestión de finanzas personales.

## Stack
- Laravel 12
- MySQL 8
- Laravel Sanctum
- Eloquent ORM

## Instalación
```bash
git clone https://github.com/parisandradelux/finance-api.git
cd finance-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Endpoints
| Método | Ruta | Auth |
|--------|------|------|
| POST | /api/auth/register | ❌ |
| POST | /api/auth/login | ❌ |
| GET  | /api/auth/me | ✅ |
| POST | /api/auth/logout | ✅ |
| GET  | /api/categories | ✅ |
| POST | /api/categories | ✅ |
| PUT  | /api/categories/{id} | ✅ |
| DELETE | /api/categories/{id} | ✅ |
| GET  | /api/transactions | ✅ |
| POST | /api/transactions | ✅ |
| GET  | /api/transactions/summary | ✅ |
| PUT  | /api/transactions/{id} | ✅ |
| DELETE | /api/transactions/{id} | ✅ |