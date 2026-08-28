# Docker Learning Notes — ShopSphere

A living reference of every Docker concept and command used in this project, explained
in plain language. This file gets a new section added every time we cover new Docker
ground — treat it as the textbook version of everything taught in chat.

---

## 1. Core concepts

**The problem Docker solves:** code that "works on my machine" can fail elsewhere because
the *environment* around it (language version, installed extras, settings) differs.
Docker lets you write down the exact environment your app needs, once, and reproduce it
identically anywhere.

**Container** — a small, sealed, isolated mini-computer: its own filesystem and installed
software, but much lighter than a full virtual machine (it shares your Mac's kernel
instead of simulating whole fake hardware).

**Image vs. container** — the analogy that matters most:
- An **image** is a *recipe* — a frozen, written blueprint. It doesn't do anything by
  itself.
- A **container** is what you get when you *run* that recipe — the live, running result.
  One image can be "cooked" into many separate running containers.

**Dockerfile** — a plain text file containing that recipe: a list of instructions,
top to bottom, that Docker reads to build an image.

**Build context** — when you run `docker build`, you point it at a folder (usually `.`).
Docker packages that whole folder up and hands it to the build process — every `COPY`
instruction can only reach files inside that folder. This is a real boundary, not a
convention.

---

## 2. Command reference

### Images
| Command | What it does |
|---|---|
| `docker build -t <name>:<tag> .` | Build an image from the Dockerfile in the current folder, and name it |
| `docker images` | List images you have locally |
| `docker rmi <name>:<tag>` | Delete an image |

### Containers
| Command | What it does |
|---|---|
| `docker run -d --name <name> ...` | Create **and start** a new container from an image. `-d` = run in background |
| `docker ps` | List **running** containers |
| `docker ps -a` | List running **and stopped** containers |
| `docker logs <name>` | See a container's output/log messages |
| `docker logs -f <name>` | Same, but stream live (like `tail -f`) |
| `docker exec <name> <command>` | Run a one-off command inside an **already-running** container |
| `docker exec -it <name> bash` | Open an interactive shell inside a running container (`exit` to leave, container keeps running) |
| `docker stop <name>` | Stop a running container (doesn't delete it) |
| `docker restart <name>` | Stop then start again — useful after changing a mounted config/`.env` file |
| `docker rm <name>` | Delete a stopped container (needed before reusing the same `--name`) |
| `docker rm -f <name>` | Force stop + delete in one step |

### Networking & storage
| Command | What it does |
|---|---|
| `docker network create <name>` | Create a private virtual network containers can join |
| `docker network ls` | List networks |
| `docker volume create <name>` | Create a **named volume** — Docker-managed persistent storage, not tied to a specific host folder |
| `docker volume ls` | List volumes |

### Key flags used constantly
| Flag | Meaning |
|---|---|
| `-p HOST_PORT:CONTAINER_PORT` | Publish a container's port to your Mac. Only needed for containers your **host** (or browser) must reach directly. |
| `-v HOST_PATH:CONTAINER_PATH` | **Bind mount** — link a specific folder/file on your Mac directly into the container. Edits on either side show up on both, live. |
| `-v VOLUME_NAME:CONTAINER_PATH` | Mount a **named volume** instead of a host path — Docker manages where the data actually lives. Used for things like database files that should persist but don't need direct host-folder editing. |
| `-v ...:ro` | Mount read-only — the container can't modify it |
| `--network <name>` | Attach the container to a custom network |
| `--name <name>` | Give the container a human name (and, if on a custom network, a working DNS hostname other containers can use) |
| `-e KEY=value` | Set an environment variable inside the container at start time |

---

## 3. Why our backend needs *three* containers, not one

Analogy: a restaurant. A **waiter** takes orders and serves food but doesn't cook. A
**chef** cooks but never talks to customers. A **storage room** holds ingredients and does
no work itself.

| Restaurant role | Our container | Job |
|---|---|---|
| Waiter | **nginx** | Talks to the browser. Serves static files directly; forwards anything needing logic to the chef. |
| Chef | **backend** (PHP-FPM) | Actually runs our Laravel code. Never talks to the browser directly. |
| Storage room | **mysql** | Holds all persistent data. Does no computation itself. |

Splitting them means each can be restarted, scaled, or upgraded independently — e.g. add
more "chefs" under heavy load without touching the waiter or storage room at all. This is
the real pattern production Laravel deployments use, and why we didn't just cram
everything into one container or use `artisan serve`/Apache+mod_php.

---

## 4. `backend/Dockerfile`, explained top to bottom

```dockerfile
FROM php:8.4-fpm
```
Start from the official PHP image, Debian-based (chosen over the smaller Alpine variant
because Alpine's different core libraries — `musl` instead of `glibc` — occasionally cause
obscure compiled-extension issues; not worth the risk while still learning). Version 8.4 to
match the PHP version already on this Mac via Herd, avoiding "which PHP is real" confusion.
> We originally tried `8.3-fpm` and hit a real error — see §7, "Troubleshooting log" — which
> is *why* it's 8.4 now.

```dockerfile
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev default-mysql-client \
    && rm -rf /var/lib/apt/lists/*
```
Installs Linux system libraries our PHP extensions need to be *built* against — `git`/`unzip`
for Composer, `lib*-dev` packages as raw materials for the extensions below,
`default-mysql-client` gives us a `mysql` CLI tool inside the container for debugging.
The `&& rm -rf ...` cleanup happens **in the same `RUN`** deliberately: every `RUN` becomes
a permanent image layer, so deleting a cache in a *later* `RUN` doesn't shrink a layer that
already baked its size in. Doing install-then-cleanup in one line means the cache never
counts toward the final image size at all.

```dockerfile
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
```
`docker-php-ext-install` is a helper script built into the official PHP image specifically
for compiling/enabling PHP extensions. What each gives us:
- `pdo_mysql` — the actual database driver; how Eloquent talks to MySQL at all
- `mbstring` — correct handling of non-plain-English text
- `exif` — reads metadata from uploaded image files
- `pcntl` — process-control signals, needed for queue workers
- `bcmath` — precise math with no floating-point rounding errors (important for prices)
- `gd` — image manipulation (resizing product photos)
- `zip` — reading/writing zip files, used by Composer internals

```dockerfile
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
```
A **multi-stage copy**: instead of installing Composer ourselves, we reach into the
official, separately-built `composer:2` image and grab its binary directly. No compiling,
no version-guessing.

```dockerfile
RUN groupadd -g 1000 appuser && useradd -u 1000 -ms /bin/bash -g appuser appuser
```
Creates a dedicated, low-privilege Linux user. **Why this matters:** by default, everything
in a container runs as `root`. If the app ever had an exploitable bug, an attacker's
commands would run with whatever privilege the app process has — as `root`, that's
maximum damage inside the container; as a low-privilege user, the blast radius is much
smaller. UID/GID `1000` is used because it's conventionally the first regular Linux user ID
— relevant later for permission alignment when mounting host folders in.

```dockerfile
WORKDIR /var/www/html
```
Every instruction after this happens inside this folder — like a permanent `cd`.

```dockerfile
COPY . .
```
Copies our actual Laravel code into the image (everything in `.dockerignore` gets skipped
first — see §5).

```dockerfile
RUN composer install --no-dev --optimize-autoloader --no-interaction
```
Installs PHP dependencies **fresh, inside Linux** — this is deliberate, not redundant with
the `vendor/` folder already on your Mac, because your Mac's copy was built for macOS/ARM
and would be flat-out wrong inside a Linux container. Flags: `--no-dev` skips
testing/debug-only tools (this image is for running, not developing), `--optimize-autoloader`
pre-builds a fast class-lookup map instead of figuring it out per-request,
`--no-interaction` never pauses for a prompt (nobody's watching a build happen).

```dockerfile
RUN chown -R appuser:appuser /var/www/html \
    && chmod -R 775 storage bootstrap/cache
```
Hands ownership of the code to our new user. `storage/` and `bootstrap/cache/` specifically
need to stay **writable** — Laravel constantly writes logs, cached views, and cached config
there while running.

```dockerfile
USER appuser
```
From here down, everything (including the actual running app) executes as `appuser`, not
`root`.

```dockerfile
EXPOSE 9000
```
**Documentation only** — does not actually open port 9000 to anything. Actually publishing
a port happens with `-p` at `docker run` time.

No `CMD` line is needed — the base `php:8.4-fpm` image already has one built in
(auto-starts PHP-FPM). Anything you don't override in a Dockerfile, you inherit from
`FROM`.

---

## 5. `.dockerignore`, explained

Works exactly like `.gitignore`, but filters what `COPY` can see during a build.

| Excluded | Why |
|---|---|
| `vendor` | Host copy is built for macOS/ARM — wrong platform for a Linux container. We let the container build its own. |
| `node_modules` | Same idea, wrong-platform files we don't need here anyway |
| `.env`, `.env.*` (except `.env.example`) | **Never bake secrets into an image.** Images can end up pushed to a registry; anyone who pulls it could extract baked-in secrets. Config gets handed to the container at *runtime* instead (see §6). |
| `.git` | Entire commit history — useless to a running container, pure dead weight |
| `storage/logs/*` etc. | Runtime-generated files; should start empty and fill up fresh per-container |

---

## 6. Networking model

```
docker network create shopsphere-net
```
Creates a private virtual network. Containers on the **same** custom network can reach
each other **by container name** — Docker runs a built-in DNS for this. Containers on
different networks (or no network) can't see each other at all by default.

Currently on `shopsphere-net`: `backend`, `nginx`, `mysql`.

- `nginx`'s config references `fastcgi_pass backend:9000;` — `backend` resolves via that
  DNS to the PHP-FPM container.
- `backend`'s `.env` has `DB_HOST=mysql` — same trick, resolves to the MySQL container.

Only `nginx` needs `-p 8080:80` (the browser/host needs to reach *it*). `backend` and
`mysql` don't need host ports published for the app to work — they only talk to each other
over the internal network. (We did also publish MySQL to `3307:3306` on the host, purely
as an optional convenience if we want to connect a GUI database tool later — not required
for the app itself.)

---

## 7. Config & secrets — the runtime-injection pattern

Because `.env` is excluded from the image (§5), the image itself has **zero** configuration
baked in. That's intentional: the same image can then run in different environments (local,
staging, production), each handed its *own* separate config at startup, and nobody who ever
gets a copy of the image gets secrets bundled with it.

Two ways we've used to inject config at `docker run` time:
- **Bind-mounting `.env` directly:** `-v "$(pwd)/.env":/var/www/html/.env:ro` — simple for
  local dev. (Not the final production pattern — real deployments typically inject
  individual environment variables instead of a whole file. We'll revisit this properly in
  Phase 4 / Phase 7.)
- **`-e` flags:** e.g. `-e MYSQL_ROOT_PASSWORD=root` for the MySQL container — sets
  individual environment variables directly, no file involved.

**A container that reads a mounted `.env` doesn't automatically notice if you edit that
file later while it's running** — restart it (`docker restart <name>`) to be sure it picks
up fresh values.

---

## 8. Nginx: `backend/docker/nginx.conf`, explained

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

- `root /var/www/html/public;` — points at Laravel's `public/` folder specifically, **not**
  the project root. `public/` is the only folder meant to be web-reachable; `app/`, `.env`,
  `vendor/` etc. sit outside it, invisible to direct requests. `public/index.php` is
  Laravel's single entry point.
- `location / { try_files ... }` — the front-controller pattern: if a request doesn't match
  a real file/folder, silently hand it to `index.php` so Laravel's own router can decide
  what it means. Nginx has no idea what `/products/5` means — it just knows "not a real
  file, pass it on."
- `location ~ \.php$` — only matches URLs ending `.php`. `fastcgi_pass backend:9000;` sends
  the request over FastCGI to the `backend` container. Critically: **Nginx sends a file
  path, not file contents** — `fastcgi_param SCRIPT_FILENAME` tells PHP-FPM which file to
  execute *on its own filesystem*. This is why both containers need to agree on the same
  folder layout at `/var/www/html`.
- The last block blocks direct web access to dotfiles (`.env`, `.git`) as defense-in-depth,
  even though they shouldn't be reachable anyway.

The `nginx` container itself uses the stock official `nginx:stable` image, unmodified — we
just mount our config over its default, and mount `backend/public` in read-only so it can
serve static files directly:
```
docker run -d --name nginx --network shopsphere-net -p 8080:80 \
  -v "$(pwd)/public":/var/www/html/public:ro \
  -v "$(pwd)/docker/nginx.conf":/etc/nginx/conf.d/default.conf:ro \
  nginx:stable
```

---

## 9. MySQL container

```
docker volume create shopsphere-mysql-data

docker run -d --name mysql --network shopsphere-net \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=shopsphere \
  -e MYSQL_USER=shopsphere \
  -e MYSQL_PASSWORD=secret \
  -p 3307:3306 \
  -v shopsphere-mysql-data:/var/lib/mysql \
  mysql:8.4
```

- Stock official `mysql:8.4` image — no custom Dockerfile needed.
- The `MYSQL_*` environment variables are special, documented behavior of this specific
  image: **on first startup with an empty data directory**, it automatically creates the
  root account with that password, creates the named database, creates the named user with
  that password, and grants them access — all without us writing any SQL by hand.
- **Named volume, not a bind mount:** `-v shopsphere-mysql-data:/var/lib/mysql` — unlike
  `-v "$(pwd)/...":...` (which links to a specific folder on your Mac), a named volume is
  storage Docker manages itself. Used here because database files should persist across the
  container being removed/recreated, but don't need to be directly browsable/editable from
  the host the way `.env` or `public/` do.
- Host port mapped to `3307` (not `3306`) because something else on this Mac (likely
  Herd's own MySQL) already occupies `3306`. This mapping is optional convenience for
  connecting a GUI database client later — `backend` itself reaches MySQL over the internal
  network as `mysql:3306`, never through the host at all.
- After first start, ran `docker exec backend php artisan migrate --force` to create the
  actual Laravel tables inside it.

---

## 10. Troubleshooting log — real problems hit and fixed

Keeping this because the *reasoning* is more valuable than the fix itself.

**Problem: `composer install` failed inside the container** —
`symfony/http-foundation v8.1.5 requires php >=8.4.1 -> your php version (8.3.33) does not
satisfy that requirement`.
*Cause:* `composer.lock` pins **exact** package versions, chosen based on whatever PHP was
running at the time it was generated. Our local scaffold ran under Herd's PHP 8.4.23, so
Composer locked in Symfony packages that need PHP 8.4+ — even though Laravel's own
`composer.json` only requires `^8.3`. Our container was still on `php:8.3-fpm` at that
point, so the exact locked versions weren't installable there.
*Fix:* changed the Dockerfile's base image to `php:8.4-fpm` to match, rather than trying to
regenerate the lock file under 8.3.

**Problem: app returned `500 Internal Server Error`, page said `MissingAppKeyException`** —
*Cause:* `.env` is deliberately excluded from the image (`.dockerignore`), so the container
had zero config, including no `APP_KEY`.
*Fix:* bind-mounted `.env` into the container at `docker run` time instead of baking it into
the image — kept the "no secrets in the image" property while still giving the running
container what it needs.

**Problem: `docker run` for MySQL failed with `address already in use` on port 3306** —
*Cause:* something else already listening on host port 3306 (likely a local MySQL via Herd).
*Fix:* mapped to host port `3307` instead — irrelevant to the app itself, which never
reaches MySQL through the host port anyway, only through the Docker network.

---

## 11. Frontend Dockerization — two genuinely different Dockerfiles

Unlike the backend, "dev" and "production" aren't just variations of the same thing for a
frontend — they're structurally different, because a built Vue app needs **no server-side
execution at all**. Laravel must run PHP on every request forever; a built Vue app is just
static HTML/CSS/JS the *browser* runs — our server's job ends the moment it hands the files
over.

### `frontend/Dockerfile.dev` — for active development

```dockerfile
FROM node:24-alpine

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
```

- **`node:24-alpine`** — Node 24 to match the local machine's Node version (same
  version-matching lesson as the backend's PHP). **Alpine here, unlike Debian for the
  backend** — a deliberate *different* answer to the same tradeoff: Alpine's risk for PHP
  was compiled C-extension quirks; frontend JS dependencies are almost entirely pure
  JavaScript, so that risk mostly doesn't apply, and Alpine's small size is a clean win.
  The right base image depends on what your actual dependencies need, not a fixed rule.
- **Copying only `package.json`/`package-lock.json` before `RUN npm install`, separate from
  the rest of the code** — this is **Docker layer caching**: each instruction is a cached
  layer, reused on rebuild if its inputs haven't changed. Splitting the dependency install
  from the full code copy means a later rebuild where only app code changed can skip
  reinstalling dependencies entirely. *(Note: our backend Dockerfile doesn't do this —
  worth revisiting there too, at some point.)*
- **No `COPY . .` at all in this file** — deliberately. Dev-server source code isn't baked
  into the image; it gets supplied live via a bind mount at `docker run` time (below), so
  edits show up instantly without ever rebuilding the image.
- **`--host 0.0.0.0`** — a common Docker + dev-server gotcha: Vite's dev server by default
  only accepts connections from *inside its own container*. Even with the port published,
  outside requests (like your Mac's browser through the port mapping) would be refused
  without this flag telling it to listen on all network interfaces.

Running it:
```
docker build -f Dockerfile.dev -t shopsphere-frontend:dev .

docker run -d --name frontend --network shopsphere-net -p 5173:5173 \
  -v "$(pwd)":/app \
  -v /app/node_modules \
  shopsphere-frontend:dev
```
- `-f Dockerfile.dev` — tells `docker build` which file to use, since it's not the default
  plain `Dockerfile` name (we have two for this folder).
- `-v "$(pwd)":/app` — bind-mounts the **entire** frontend folder live. Edit a file on your
  Mac, Vite's watcher inside the container sees it immediately (same files, not a copy) and
  hot-reloads the browser.
- `-v /app/node_modules` — an **anonymous volume** (no host folder given, just a container
  path). Docker mount precedence favors the more specific path, so this keeps the
  container's own Linux-correct `node_modules` (with correctly-compiled native binaries
  like `esbuild`) intact underneath the broader bind mount, instead of it getting silently
  overwritten by the Mac's own (wrong-platform) `node_modules`.

### `frontend/Dockerfile` — the production build

```dockerfile
FROM node:24-alpine AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

FROM nginx:stable
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
```

A **multi-stage build** — two `FROM` lines, two separate stages:
- **Stage 1 (`AS build`)** — full Node environment, installs dependencies, copies in all
  source code, runs `npm run build`. Vite compiles everything down into a `dist/` folder of
  plain static files. This stage's only purpose is producing that folder.
- **Stage 2** — starts **completely fresh** from bare `nginx:stable`, knowing nothing about
  Node or Stage 1 except what's explicitly pulled across: `COPY --from=build /app/dist ...`
  grabs *only* the finished `dist/` folder. Node, `node_modules`, and all source code are
  left behind entirely — the final image has zero JavaScript runtime in it.

Confirmed concretely: `shopsphere-frontend:dev` is 121MB (content size); `:prod` is 61.5MB —
roughly half, purely from not carrying Node/npm/source code at all.

`frontend/docker/nginx.conf`:
```nginx
server {
    listen 80;
    server_name localhost;
    root /usr/share/nginx/html;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```
Simpler than the backend's version — no PHP hand-off needed. Vue Router handles all
navigation client-side in the browser once `index.html` and its JS bundle have loaded; the
`try_files` fallback just makes sure that directly loading or refreshing a URL like
`/products/5` still serves `index.html` (and its already-loaded JS) instead of a 404, since
Nginx itself has no idea what that path means.

---

## 12. Docker Compose — running everything with one command

`docker-compose.yml` (project root) replaces every manual `docker build`/`docker run`/
`docker network create`/`docker volume create` command from here on. Everything earlier in
this doc about *why* each container/setting exists still applies — Compose is just a single
file that does all of it declaratively instead of us typing it by hand each time.

```yaml
services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    image: shopsphere-backend:dev
    container_name: backend
    volumes:
      - ./backend:/var/www/html
      - /var/www/html/vendor
    networks:
      - shopsphere-net
    depends_on:
      mysql:
        condition: service_healthy
    restart: unless-stopped
  # ...nginx, mysql, frontend follow the same shape
```

What's new/different versus the manual commands we ran before:

- **`build: context: ./backend`** — Compose builds the image itself. `docker compose up
  --build` rebuilds any service whose Dockerfile/context changed.
- **`backend`'s volumes now mount the *entire* `backend/` folder live**, not just `.env` —
  this is the big upgrade for actually writing backend code: any PHP file you edit shows up
  inside the container instantly, no rebuild, exactly like the frontend already worked.
  `- /var/www/html/vendor` is the same anonymous-volume trick as `node_modules`: it protects
  the container's own correctly-built Linux `vendor/` from being overwritten by the live
  mount of your Mac's folder.
- **`depends_on: mysql: condition: service_healthy`** — waits for MySQL's `healthcheck:`
  (repeated `mysqladmin ping` calls) to actually pass, not just for the container to start.
  Confirmed working: on `docker compose up`, we watched `mysql Waiting` → `mysql Healthy` →
  only then `backend Starting`.
- **`ports: "127.0.0.1:3307:3306"`** on `mysql` — bound to `127.0.0.1` specifically (not
  `0.0.0.0` like before), so it's reachable only from this Mac, never from other devices on
  the same network.
- Resource names get auto-prefixed with a **project name** (defaults to the folder name) —
  e.g. our network became `shopsphere_shopsphere-net`. This exists so multiple unrelated
  Compose projects on the same machine never collide.

**Honest flag:** the MySQL credentials in `docker-compose.yml` are plaintext, and this file
gets committed to git — fine for local-only dummy dev credentials with nothing real at
stake, but genuinely not how you'd handle real secrets. The correct fix (pulling them from
a separate, gitignored root-level `.env` that Compose auto-loads for `${VAR}` substitution,
or a real secrets manager) is a Phase 7/CI-CD topic — deliberately deferred rather than
adding another layer of `.env` file right now.

### Compose command reference

| Command | What it does |
|---|---|
| `docker compose up -d` | Start everything (build images first only if they don't exist yet) |
| `docker compose up --build -d` | Force a rebuild of images, then start — use after changing a Dockerfile, `composer.json`, or `package.json` |
| `docker compose up --build backend` | Rebuild/start just one service |
| `docker compose down` | Stop and remove all containers (volumes/data are kept) |
| `docker compose down -v` | Same, but **also delete volumes** — wipes the database. Only do this when you genuinely don't need the data. |
| `docker compose ps` | List this project's containers and their status |
| `docker compose logs <service>` | View a service's logs (`-f` to follow live) |
| `docker compose exec <service> <command>` | Run a one-off command inside a running service, e.g. `docker compose exec backend php artisan migrate` |
| `docker compose restart <service>` | Restart one service — needed after editing a mounted config file like `.env` for it to be picked up |
| `docker compose stop` / `docker compose start` | Stop/start without removing containers |

---

## 13. Changing configuration — what to actually run afterward

Different kinds of changes need different follow-up commands. This trips people up because
it's not always "just restart" — here's the real rule for each case, based on what we've
hit so far:

| You changed... | What to run | Why |
|---|---|---|
| A normal `.env` value (e.g. `APP_DEBUG`, `LOG_LEVEL`) | `docker compose restart backend` | `.env` is live-mounted, but PHP-FPM's already-running worker processes loaded it into memory at startup — a restart forces a fresh read. |
| `DB_PASSWORD` / MySQL credentials, **no real data yet** | Edit `.env` **and** `docker-compose.yml`'s `mysql.environment` to match, then `docker compose down -v && docker compose up --build -d`, then re-run `docker compose exec backend php artisan migrate --force` | MySQL only applies `MYSQL_PASSWORD`/`MYSQL_ROOT_PASSWORD` when it first initializes an **empty** data directory. `down -v` deletes the volume so it genuinely reinitializes fresh. **Destructive — only fine because there's no real data to lose right now.** |
| `DB_PASSWORD` / MySQL credentials, **once there IS real data** | Connect and change it in place: `docker compose exec mysql mysql -uroot -p<current root password>`, then run `ALTER USER 'shopsphere'@'%' IDENTIFIED BY 'newpassword'; FLUSH PRIVILEGES;` — then update `.env` to match and `docker compose restart backend` | Changes the live credential without touching existing data. This is the version you'll actually use later. |
| `backend/Dockerfile` (e.g. adding a PHP extension) | `docker compose up --build backend` | The change is baked in at image-build time — a restart alone won't re-run `apt-get`/`docker-php-ext-install`. |
| `frontend/Dockerfile.dev` | `docker compose up --build frontend` | Same reasoning. |
| Add a PHP package while developing | `docker compose exec backend composer require <package>` | Run it **inside** the running container, not on your Mac — `vendor/` is an isolated anonymous volume now, separate from any host copy. Because `composer.json`/`composer.lock` *are* part of the live-mounted folder, the resulting changes to those two files write straight back to your Mac and are ready to `git add`. |
| Add an npm package while developing | `docker compose exec frontend npm install <package>` | Same reasoning — `node_modules` is the isolated one, `package.json`/`package-lock.json` are live-mounted and sync back. |
| Any `.env` value, **if you've ever run `php artisan config:cache`** | `docker compose exec backend php artisan config:clear` (or re-run `config:cache`) | Once config is cached, Laravel stops reading `.env` directly at all — it reads the frozen cache instead, so `.env` edits silently do nothing until the cache is cleared. Common real-world gotcha, worth remembering once we get to deployment. |

---

## 14. Current running state

Four containers, managed entirely by Compose, one shared network (`shopsphere_shopsphere-net`):

| Container | Image | Role | Host port |
|---|---|---|---|
| `backend` | `shopsphere-backend:dev` (our Dockerfile, live-mounted code) | Runs Laravel via PHP-FPM | *(none — internal only)* |
| `nginx` | `nginx:stable` (official, unmodified) | Web server / reverse proxy for backend | `8080` → app in browser |
| `mysql` | `mysql:8.4` (official, unmodified) | Database | `127.0.0.1:3307` (optional, for GUI tools) |
| `frontend` | `shopsphere-frontend:dev` (our Dockerfile.dev, live-mounted code) | Vite dev server for the Vue app | `5173` → app in browser |

Start everything: `docker compose up -d` (from the project root). Stop everything:
`docker compose down`. `shopsphere-frontend:prod` also still exists as a built image
(verified working) but nothing runs from it day-to-day — the dev container is what's
actually in use while writing code.

DB credentials (local dev only, not for anything real): database `shopsphere`, user
`shopsphere` / `devpassword123`, root password `rootpass123`.

---

## 15. Daily development workflow — writing actual backend code

**The code itself doesn't change.** Controllers, models, migrations — all written exactly
like normal Laravel. The only difference: **PHP, Composer, and `artisan` don't exist on
this Mac for this project anymore — they only exist inside the `backend` container.** Your
code lives on your Mac (the live-mount from §12 takes care of that), but the tools that
*run* it live inside Docker. The habit: prefix artisan/composer commands with
`docker compose exec backend`.

Concrete example — adding a `products` table:

```bash
docker compose exec backend php artisan make:migration create_products_table
docker compose exec backend php artisan make:model Product
docker compose exec backend php artisan make:controller ProductController --resource
docker compose exec backend php artisan make:seeder ProductSeeder
```

Every one of these **writes real files into `backend/app/...` / `backend/database/...` on
the actual Mac** (thanks to the live-mount) — open them in VS Code immediately, edit
completely normally (columns in the migration, relationships in the model, logic in the
controller). Editing itself is identical to non-Docker Laravel work.

Then to apply it:
```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan db:seed --class=ProductSeeder
```

**Why some commands need the container more than others:**
- `make:migration` / `make:model` / `make:controller` / `make:seeder` — just generate PHP
  files. No environment-specific work happens; these would technically also work run via
  Herd's PHP directly. Still worth running the same way for consistency.
- `migrate` / `db:seed` / `tinker` (when it touches the DB) — these **actually connect to
  MySQL**, and only the `backend` container can resolve `mysql` as a hostname (that only
  exists on our Docker network, `shopsphere_shopsphere-net`). Herd's PHP on the Mac has no
  route to it at all — these commands genuinely only work through the container.

**No rebuild is ever needed for this kind of work.** Rebuilds (`docker compose up --build`)
are only for changes to `Dockerfile` or `composer.json`'s dependency list — writing
app code touches neither.

Optional convenience: shorten the prefix with a shell alias —
`alias art='docker compose exec backend php artisan'` in `~/.zshrc` — so it's just
`art make:model Product`.

---

## 16. Still to come (this section grows as we go)

- **Phase 5 (continued):** More daily dev workflow details as they come up (queue workers,
  scheduled tasks, `tinker`, etc.)
- **Phase 6/7:** CI/CD with GitHub Actions — build and push these images automatically
