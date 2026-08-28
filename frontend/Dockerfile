# ---- Stage 1: build the static assets using a full Node environment ----
FROM node:24-alpine AS build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build

# ---- Stage 2: serve the built files with a minimal nginx — no Node in this image at all ----
FROM nginx:stable

COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
