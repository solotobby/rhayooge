# RHÁYỌ̀OGE

A Laravel Livewire storefront for the RHÁYỌ̀OGE female apparel house.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Google and Facebook sign-in

Add credentials to `.env`:

```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

Use these callback URLs in the provider consoles:

- `{APP_URL}/auth/google/callback`
- `{APP_URL}/auth/facebook/callback`
