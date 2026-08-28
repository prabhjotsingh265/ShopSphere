# Session Handoff — Continue From Downloads Chat

This file is a full context transfer from a Claude Code conversation that ran in
`/Users/saini/Downloads/vue_laravel_ecommerce_udemy_laravel_12` (a different VS Code
window/session). That conversation cannot be automatically resumed here — my memory
system is scoped per-project-directory, so nothing carries over on its own. This
document is the bridge. Read it fully before doing anything else, then confirm back
to the user that you understand it.

---

## 1. Who the user is and what they actually want

The user bought a Udemy course that ships a complete Laravel + Vue e-commerce
project as reference material. Their goal is **not** to just follow the course —
it's to use it as a blueprint while separately learning, hands-on, how to:

1. Set up a brand-new Laravel + Vue project from scratch
2. Containerize it with **Docker**, understanding *every line* of every Dockerfile
   and compose file — not just running commands that work, but knowing what each
   line does and what the alternatives/possibilities are
3. Wire up **Git + CI/CD** (GitHub Actions) around it
4. Do this specifically to **put it on their resume** — so depth of understanding
   matters more than speed

Explicit working-style preferences (do not deviate from these without being told):

- **Teach, don't just execute.** Every Docker-related step needs a line-by-line
  explanation of what it does and why, not a silent dump of working config.
- **Go one step at a time.** The user wants to build this incrementally — a piece
  at a time — and `git push` after each completed piece, not get an entire
  pre-built project dropped on them at once.
- **Never `git commit` unless explicitly asked.** This was already demonstrated
  once in the prior session: files were staged with `git add` but the assistant
  deliberately did not commit, and asked first.
- The user's typing has frequent typos — read intent, don't get hung up on exact
  spelling (e.g. "ShopSphere-REFERNCE-FINAL" is short one letter for "REFERENCE";
  this was flagged to the user once and they haven't responded either way, so it
  currently stays as-is).

---

## 2. The reference project (fully analyzed, in the OTHER folder/session)

Location: `/Users/saini/Downloads/vue_laravel_ecommerce_udemy_laravel_12`
(a separate git repo, **not** part of this ShopSphere project — for background
understanding only).

It's a T-shirt/clothing e-commerce store, two apps in one repo:

- **`backend/`** — Laravel 12, two auth systems in one app:
  - `User` model + Sanctum bearer tokens for the public storefront API
    (`routes/api.php`)
  - `Admin` model + session guard + custom `AdminMiddleware` for a server-rendered
    Blade admin panel at `/admin/*` (`routes/web.php`)
  - Models: `Product` (belongsTo `Category`/`Brand`, belongsToMany `Color`/`Size`,
    hasMany approved `Review`s), `Order` (belongsToMany `Product`, belongsTo
    `User`/`Coupon`), `Coupon` (uppercased name, expiry check via `checkIfValid()`),
    `Review` (needs admin approval before showing publicly)
  - Admin panel: full CRUD for categories/brands/colors/sizes/products (with image
    uploads)/coupons; read/moderate for orders/reviews/users
  - Public API: product listing + filtering (category/brand/color/size/search) with
    facet data returned alongside; register/login; and behind `auth:sanctum` —
    profile update, coupon apply, order creation, Stripe checkout, review CRUD
  - Payment flow: cart lives entirely client-side (Pinia) → Stripe Checkout Session
    created server-side (currently a **hardcoded placeholder** secret key in
    `OrderController::payOrdersByStripe` — needs a real key eventually) → redirect
    to Stripe → success redirects to `/#/success/payment/:hash` → frontend checks
    the hash matches what it generated (basic replay guard) → then POSTs
    `/store/order` to actually persist the order rows
  - Default seeded admin: `admin@email.com` / `password`

- **`frontend/`** — Vue 3 (`<script setup>`), Vite, hash-based router
  (`createWebHashHistory`), Bootstrap 5. Pinia stores: `useAuthStore` (persisted),
  `useCartStore` (persisted, color/size-aware line items + coupon), `useFavoritesStore`
  (persisted), `useProductsStore` (listing/filters), `useProductDetailsStore`
  (single product + reviews). Standard flow: browse/filter → product detail
  (color/size pickers) → cart → checkout → Stripe → success → order recorded →
  visible in profile/orders. Reviews are gated to users who actually bought the
  product.

- **`starter_admin_files/`** — raw HTML/CSS admin theme the course uses as the
  starting point for the Blade admin views (not code we need to touch).

⚠️ **Known unresolved issue in that repo** (not ours to fix, just worth knowing
if the user brings it up): that repo was mid-merge — `git status` showed "All
conflicts fixed but you are still merging," and the *staged* version of
`backend/resources/views/admin/products/edit.blade.php` still literally contained
`<<<<<<<`/`=======`/`>>>>>>>` conflict-marker text in the index, even though the
working-tree copy was already cleaned up. It just needs `git add` + `git commit`
to finish. This was flagged to the user but not yet acted on as of this handoff.

---

## 3. This project — ShopSphere

### Decisions already made (confirmed via direct question to the user, don't re-ask):

| Decision | Answer |
|---|---|
| Project/repo name | **ShopSphere** |
| Docker build approach | **Build from scratch**, hand-written Dockerfiles/compose — explicitly *not* Laravel Sail, because Sail abstracts away the learning |
| Web server architecture | **Nginx + PHP-FPM**, two separate containers — chosen over Apache/mod_php or `artisan serve` specifically because it's the real production pattern and most resume-relevant |
| Laravel version | Whatever `composer create-project laravel/laravel` pulls as latest (came out as **Laravel 13.29.0**, PHP `^8.3`) — user said "12 or 13," either is fine |
| Database | Defaulted to MySQL (not explicitly asked, matches the reference project) — not yet set up in this new project |

### Folder layout (mirror this same top-level shape when rebuilding — that's why the reference copy exists):

```
/Users/saini/PRINCE-DATA/HERD-LARAVEL/
├── ShopSphereREFERENCEFINAL/   ← DO NOT build in here. Reference only. (renamed 2026-08-28, was ShopSphere-REFERNCE-FINAL)
│   ├── backend/                   Already-scaffolded Laravel 13 app (composer create-project, plain skeleton, no starter kit)
│   └── frontend/                  Already-scaffolded Vue 3 app (npm create vue@latest --router --pinia --eslint)
│   (git repo initialized here, everything `git add`-ed, nothing committed yet)
│
└── ShopSphere/                  ← THIS is the real project. Build here, step by step, from scratch.
    └── CONTINUE_FROM_DOWNLOADS.md   (this file)
```

The reference folder is a fully working, already-`npm install`ed / `composer install`ed
scaffold the user can diff against or peek at, but the actual teaching/building
happens fresh in `ShopSphere/`. As of this handoff, `ShopSphere/` contains *only*
this file — everything else (Laravel scaffold, Vue scaffold, git init, Docker files,
CI/CD) is still to be done, deliberately, one explained step at a time.

### The full roadmap (agreed with the user, nothing beyond Phase 1 done yet in THIS folder):

| Phase | Topic | Status |
|---|---|---|
| 0 | Install Docker Desktop | ✅ Done — see §4 below, was broken, now fixed and verified |
| 1 | Scaffold Laravel + Vue in `ShopSphere/`, git init | ⏳ **Not started in this folder** — this is the next thing to do |
| 2 | Dockerize the Laravel backend (Dockerfile, PHP-FPM, nginx config) — full line-by-line | ⏳ pending |
| 3 | Dockerize the Vue frontend (dev container + prod multi-stage build) — full line-by-line | ⏳ pending |
| 4 | `docker-compose.yml` — wire everything together (networks, volumes, env vars) | ⏳ pending |
| 5 | Daily dev workflow in Docker (artisan commands, migrations, hot-reload) | ⏳ pending |
| 6 | Push to GitHub (user creates the empty repo; we wire up the remote) | ⏳ pending |
| 7 | CI/CD with GitHub Actions (lint/test → build → push images) | ⏳ pending |

**Immediate next step when this session resumes:** re-scaffold Phase 1 inside
`ShopSphere/` — `composer create-project laravel/laravel backend`, then
`npm create vue@latest frontend -- --router --pinia --eslint`, then `npm install`
— but this time walk through what each command and flag actually does as we go
(last time in the reference folder it was done quickly without much narration,
which is part of why it's being redone here). Watch for the same `oxlint` /
`eslint-plugin-oxlint` peer-dependency conflict that hit last time (fixed by
pinning `oxlint` to `~1.73.0` in `frontend/package.json` to match
`eslint-plugin-oxlint`'s peer requirement — do NOT just reach for
`--legacy-peer-deps`).

---

## 4. Docker Desktop — installation issue that was fixed (context in case it regresses)

Docker Desktop is installed and **was verified fully working** (`docker run
hello-world` succeeded) as of this handoff, but the fix has a fragile edge —
read this before assuming Docker "isn't working" if `docker` commands fail.

**What was wrong:** the installer `.dmg` got opened and Docker.app was launched
directly from the *mounted disk image* (`/Volumes/Docker/Docker.app/...`) instead
of from the properly installed `/Applications/Docker.app`. When that temporary
volume was later ejected, the CLI symlinks (`/usr/local/bin/docker`,
`~/.docker/cli-plugins/*`) were left dangling, pointing at a path that no longer
existed — hence `docker: command not found` despite Docker Desktop actually
running. Separately, backend/VM processes launched from that same stale volume
were stuck spinning at 100–350% CPU, which likely also explains why no dashboard
window would render.

**What was done to fix it:**
1. Force-killed (`kill -9`) the stuck processes tied to the ejected volume
2. Relaunched Docker Desktop cleanly via `open -a "/Applications/Docker.app"`
   (all processes then correctly ran from `/Applications/...`)
3. Repointed the user-owned symlinks in `~/.docker/cli-plugins/*` to the correct
   `/Applications/Docker.app/Contents/Resources/cli-plugins/` targets
4. **Could not fix `/usr/local/bin/docker` itself** — it's root-owned, fixing it
   needs an interactive `sudo` password that can't be supplied headlessly.
   Worked around it instead by adding this to `~/.zshrc` (not `/usr/local/bin`):
   ```sh
   # Docker Desktop CLI (the /usr/local/bin/docker symlink is stale, pointing at an
   # ejected install-disk-image volume; this points straight at the real app instead)
   export PATH="/Applications/Docker.app/Contents/Resources/bin:$PATH"
   ```

**Practical implication:** `docker` only works in shells that load `~/.zshrc`
(i.e. new terminal tabs/windows opened *after* this fix, or after running
`source ~/.zshrc`). If a `docker` command mysteriously "isn't found," that's
almost certainly why — not a real reinstall problem.

Also: Docker Desktop opening with **no visible window** is normal/expected — it
launches to a menu-bar tray icon by default (`--reason=open-tray`). That's not a
bug. Only the CLI matters for this project's work.

**Verified environment at time of fix:**
- Mac, Apple Silicon (`arm64`), macOS 26.6 (build 25G72)
- No Homebrew installed
- No GitHub CLI (`gh`) installed — user will create the GitHub repo via the web UI
- PHP 8.4.23 / Composer 2.10.2 via Laravel Herd (not Docker — Herd is the existing
  local PHP setup, separate from this Docker learning track)
- Node v24.18.0, npm 11.16.0
- Docker Desktop 29.7.2, Docker Compose v5.4.0 (both confirmed working)

---

## 5. What to do right now

1. Confirm to the user you've read and understood this file.
2. Ask if anything changed since this was written (e.g. did they rename the
   `REFERNCE` typo, did they already start Phase 1 themselves, is Docker still
   working).
3. Resume at **Phase 1** in `ShopSphere/` as described in §3 — scaffold Laravel +
   Vue from scratch, narrating each command/flag, matching the reference folder's
   structure, staging with git but not committing until asked.
