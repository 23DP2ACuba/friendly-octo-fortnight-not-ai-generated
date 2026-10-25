# UniPlaner

Laravel and MySQL power the planner. Vue/Inertia pages, components, layouts,
styles, and the Vite/Tailwind build configuration live in `frontend/`.
Laravel controllers, routes, and the Inertia Blade shell live in `backend/`.

## Docker

Run `docker compose up --build -d` from this directory, then open
http://localhost:8080. The backend is also available at http://localhost:8000.
MySQL is available on port 3306. Database, storage, and cache data use named volumes.

Images include dependencies and compiled frontend assets. After editing source,
run `docker compose up --build -d` again. Use `docker compose logs` to inspect
startup and `docker compose down` to stop services while preserving data.

## Frontend development

Use Node.js 22.12 or newer. Run `npm ci` in `frontend/`, then `npm run dev`
alongside a local Laravel server. Vite writes Laravel's hot file to
`backend/public/hot`. `npm run build` writes production assets and the Laravel
manifest to `frontend/dist/`; Docker copies these to `public/build`.
For a local production build served by Laravel, run
`npm run build -- --outDir ../backend/public/build` from `frontend/`.
