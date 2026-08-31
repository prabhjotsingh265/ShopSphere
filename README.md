<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/MySQL-8.4-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 8.4">
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?style=flat-square&logo=docker&logoColor=white" alt="Docker Compose">
  <img src="https://img.shields.io/badge/Stripe-Checkout-635BFF?style=flat-square&logo=stripe&logoColor=white" alt="Stripe">
</p>

# ShopSphere

A full-stack e-commerce platform — Laravel API + admin panel on the backend, a decoupled Vue 3 storefront on the frontend, containerized from scratch with hand-written Docker (no Sail), and built end-to-end as a deep-dive into production-style infrastructure, not just app code.

**Live demo:** _coming soon_

---

## Highlights

- **Decoupled architecture** — a Laravel 13 API (Sanctum token auth) serving a separate Vue 3 SPA, plus a server-rendered Blade admin panel with its own session-guard auth, sharing one database
- **Full commerce flow** — browse/filter by category, brand, color, size, or search → product detail with reviews → cart → coupon codes → Stripe Checkout → order history
- **Full admin panel** — CRUD for categories, brands, colors, sizes, products (multi-image upload), coupons; order fulfillment tracking; review moderation; user management — all behind its own auth guard, independent of the storefront's
- **Docker, built by hand** — separate Dockerfiles for a PHP-FPM backend and an Nginx reverse proxy, a Vite dev container with live-reload and a separate multi-stage production build for the frontend, all wired together with `docker-compose.yml` (network, volumes, MySQL healthcheck). Every line is explained in [`DOCKER_GUIDE.md`](./DOCKER_GUIDE.md) — a from-scratch build was chosen deliberately over Laravel Sail so nothing about the setup is a black box
- **A real design system, not a template** — a bold pine-and-brass palette and a custom logo mark (a garment button, not a generic gradient sphere), applied consistently across both the storefront and the admin panel

## Tech Stack

| Layer | Stack |
|---|---|
| Backend | Laravel 13, PHP 8.4, Sanctum, Stripe PHP SDK |
| Frontend | Vue 3 (Composition API), Vite, Pinia, Vue Router, Axios, Bootstrap 5 |
| Database | MySQL 8.4 |
| Infra | Docker, Docker Compose, Nginx, PHP-FPM |
| Payments | Stripe Checkout |

## Getting Started

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/). No local PHP, Node, or MySQL installation needed — everything runs in containers.

```bash
git clone git@github.com:prabhjotsingh265/ShopSphere.git
cd ShopSphere
cp backend/.env.example backend/.env   # then fill in APP_KEY, DB_*, STRIPE_* — see DOCKER_GUIDE.md
docker compose up --build -d
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate --seed
```

- Storefront: [http://localhost:5173](http://localhost:5173)
- Admin panel: [http://localhost:8080](http://localhost:8080) (seeded admin: `admin@shopsphere.com` / `password`)

Full explanation of every container, every Dockerfile line, and the daily dev workflow (running `artisan`/`npm` commands through the containers) lives in [`DOCKER_GUIDE.md`](./DOCKER_GUIDE.md).

## Project Structure

```
ShopSphere/
├── backend/     Laravel API + admin panel
├── frontend/    Vue 3 storefront (Vite)
├── docker-compose.yml
└── DOCKER_GUIDE.md   full breakdown of the Docker setup
```

## Roadmap

- [x] Containerized backend + frontend, wired with Compose
- [x] Full storefront + admin panel
- [x] Stripe Checkout integration
- [x] Design system applied across both apps
- [ ] CI/CD via GitHub Actions (build → test → push images)
- [ ] Deployed live demo

## License

MIT
