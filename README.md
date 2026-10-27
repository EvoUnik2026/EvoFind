# EvoFind

Marktplaats-style marketplace for second-hand goods (books, music, devices, cameras, clothes), starting in Deventer and its suburbs.

- Backend: Symfony 7 (PHP 8.3), Doctrine ORM, EasyAdmin
- Infrastructure: Docker Compose — Traefik, nginx + PHP-FPM, MariaDB, Redis, Adminer
- Public UI: Dutch (default), English, Arabic (RTL) — `/nl`, `/en`, `/ar`
- Admin UI (`/admin`): always Dutch. Every new ad is `pending` until an admin approves it.
- All payment and delivery options can be selected per ad (see `Listing::PAYMENT_OPTIONS` / `DELIVERY_OPTIONS`).

## Run

```
cp .env.example .env
docker compose up -d --build
docker compose exec php php bin/console app:seed            # categories + Deventer area
docker compose exec php php bin/console app:create-admin admin@example.com 'secret'
```

- Site: http://evofind.localhost  — Admin: http://evofind.localhost/admin
- Adminer: http://adminer.evofind.localhost — Traefik: http://traefik.evofind.localhost:8080

The schema is created automatically on container start (`doctrine:schema:update`).
Adding a city later: create it in the admin under "Steden".
