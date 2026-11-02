# Mood Journal

Mood Journal is a mood tracking and self-reflection web application designed to help users record their emotions, monitor mood patterns, and reflect on their well-being.

Authenticated users can register, log in, complete daily mood check-ins, add optional notes, and view dashboard summaries and weekly mood visualizations based on their saved entries.

The project has a separate frontend and backend that communicate through a REST API.

## Live Demo

**[Open Mood Journal](https://mood-journal-frontend-l228.onrender.com)**

The application is hosted on Render. Because the services use Render's free tier, they may spin down after a period of inactivity. The first visit after inactivity can therefore take a little while while the services start up.

## Screenshot

![Mood Journal Dashboard](docs/images/mood-journal-dashboard.png)

## Tech Stack

### Frontend

- Nuxt 4
- Vue 3
- TypeScript
- Vue I18n (`@nuxtjs/i18n`)
- Nitro server routes

### Backend

- Laravel 13
- PHP 8.3+
- Laravel Sanctum

### Database

- PostgreSQL
- Neon

### Containerization and Hosting

- Docker (containerized Laravel backend)
- Apache HTTP Server
- Render (frontend and backend hosting)
- Neon (managed PostgreSQL database)

## Architecture

The application consists of three parts:

- **Frontend:** A Nuxt application responsible for the user interface, navigation, localization, and client-side interactions.
- **Backend:** A Laravel REST API responsible for authentication, request validation, application logic, and database operations.
- **Database:** A PostgreSQL database hosted on Neon.

The frontend communicates with the Laravel backend through HTTP requests and JSON responses.

### Production API proxy

The frontend and backend are deployed as separate Render services, which means they have different hostnames. This created an authentication issue with Laravel Sanctum's cookie-based SPA authentication: the browser was interacting with the frontend domain while the CSRF and session cookies belonged to the backend domain.

To address this, a Nuxt server-side proxy was added at `frontend/server/routes/backend/[...].ts`.

Browser requests now use the frontend's `/backend` path. The Nuxt server forwards those requests to the Laravel backend, including the request headers and cookies required by Sanctum. This keeps the browser-facing requests on the frontend domain and avoids relying on frontend JavaScript to read a cookie belonging to a different domain.

The proxy target is configured through Nuxt runtime configuration:

- `runtimeConfig.apiBase` is the server-side backend URL. It defaults to `http://localhost:8000` for local development and is overridden in production by `NUXT_API_BASE`.
- `runtimeConfig.public.apiBase` is `/backend`, so browser-side API calls use the Nuxt proxy rather than calling the backend hostname directly.

This is the production configuration change that resolved the cross-domain CSRF/session-cookie problem.

## Project Structure

```text
mood-journal/
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   ├── Models/
│   │   └── Providers/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── tests/
│
└── frontend/
    ├── app/
    │   ├── components/
    │   │   ├── auth/
    │   │   │   └── Shell.vue
    │   │   └── dashboard/
    │   ├── composables/
    │   │   └── useAuth.ts
    │   ├── pages/
    │   │   ├── login.vue
    │   │   ├── register.vue
    │   │   └── index.vue
    │   └── app.vue
    ├── i18n/
    │   └── locales/
    │       ├── en.json
    │       └── sv.json
    ├── server/
    │   └── routes/
    │       └── backend/
    │           └── [...].ts
    └── nuxt.config.ts

Additional repository documentation and the dashboard screenshot are stored in `docs/`.
```

## Features

### Authentication

- User registration, login, and logout
- Session-based authentication using Laravel Sanctum
- CSRF protection for state-changing requests
- Protected endpoints using the `auth:sanctum` middleware
- Cookie-based session handling
- Shared `AuthShell` component for consistent login and registration layouts

### Dashboard

- Responsive, component-based dashboard
- Compact layout with sticky sidebar navigation
- Daily mood check-in with an optional note
- Mood entries persisted in PostgreSQL
- Mood summary based on the user's saved entries
- Weekly mood overview calculated from saved check-ins
- Dashboard data refreshed after a successful check-in
- Inspirational content
- `My Journal` and `Insights` navigation items marked as coming soon

### Internationalization

- English and Swedish translations
- Language switcher on the dashboard, login page, and registration page
- Localized dashboard content, mood labels, and weekdays
- Localized authentication pages, including labels, buttons, links, and error messages
- Language-aware navigation between login, registration, and the dashboard

## Getting Started

### Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- PostgreSQL database

### 1. Clone the repository

```bash
git clone https://github.com/laura-baraldi/mood-journal.git
cd mood-journal
```

### 2. Backend setup

Navigate to the backend directory:

```bash
cd backend
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database connection in `backend/.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password
DB_SSLMODE=require
```

For local development, configure the Sanctum stateful domain and session settings for the local frontend:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
```

Run the database migrations:

```bash
php artisan migrate
```

Start the Laravel development server:

```bash
php artisan serve
```

The backend will be available at `http://localhost:8000`.

### 3. Frontend setup

Open a second terminal and navigate to the frontend directory:

```bash
cd frontend
```

Install dependencies:

```bash
npm install
```

Start the Nuxt development server:

```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`.

## Authentication and CSRF flow

The application uses Laravel Sanctum's stateful SPA authentication. It relies on session cookies rather than personal access tokens.

The authentication flow is:

1. The frontend requests the CSRF cookie from Laravel Sanctum.
2. The browser stores the CSRF cookie.
3. For state-changing requests, the XSRF token is sent in the request headers.
4. Laravel validates the credentials and creates an authenticated session.
5. The browser sends the session cookie with subsequent requests.
6. Protected API endpoints use `auth:sanctum` to verify the authenticated session.

The frontend sends requests with credentials enabled so that the relevant cookies are included.

In production, browser requests go to the Nuxt `/backend` proxy. The proxy forwards them to Laravel, so the browser does not need to make cross-origin API requests directly to the backend service.

## API Endpoints

The following are the Laravel backend endpoints. In the deployed frontend, browser requests are made through the Nuxt proxy by prefixing these paths with `/backend`.

| Method | Backend endpoint       | Description                                    | Authentication |
| ------ | ---------------------- | ---------------------------------------------- | -------------- |
| GET    | `/sanctum/csrf-cookie` | Retrieve the CSRF cookie                       | No             |
| POST   | `/api/register`        | Register a new user                            | No             |
| POST   | `/api/login`           | Authenticate a user                            | No             |
| POST   | `/api/logout`          | End the authenticated session                  | Yes            |
| GET    | `/api/user`            | Retrieve the authenticated user                | Yes            |
| GET    | `/api/mood-entries`    | Retrieve the authenticated user's mood entries | Yes            |
| POST   | `/api/mood-entries`    | Create a mood entry for the authenticated user | Yes            |

For example, the browser-facing production path for the login endpoint is `/backend/api/login`, while the Nuxt server forwards it to the backend's `/api/login` endpoint.

## Deployment

The application is deployed as separate frontend and backend services on Render, with PostgreSQL hosted on Neon. The Laravel backend is packaged as a Docker image using `backend/Dockerfile`; the Nuxt frontend is deployed as a separate Render service.

### Frontend environment variable

Configure the following environment variable in the Render frontend service:

```env
NUXT_API_BASE=https://your-backend-service.onrender.com
```

Replace the example value with the URL of the deployed Laravel backend.

Nuxt uses this value as the server-side proxy target. The public API base remains `/backend`, so browser requests are routed through the frontend service.

### Backend Docker image

The backend Dockerfile is based on `php:8.4-apache`. It installs the PHP extensions required by the application and PostgreSQL (`pdo_pgsql`, `mbstring`, `bcmath`, and `opcache`), installs Composer dependencies without development packages, and configures Apache to serve Laravel's `public/` directory on port `10000`.

The image exposes port `10000` and starts Apache in the foreground. Render runs the backend from this Dockerfile; database credentials and application secrets are supplied through the Render service environment rather than baked into the image. The repository does not currently define a `docker-compose.yml`, so the frontend and database are not launched as part of a local multi-container Compose stack.

### Backend configuration

Configure the Laravel environment variables in the Render backend service, including:

- `APP_KEY`
- PostgreSQL connection settings for the Neon database
- The appropriate Sanctum and session settings for the deployed frontend

Do not commit `.env` files, database credentials, application keys, or other secrets to the repository. Configure production secrets through Render's environment settings.

## Docker

The Dockerfile is located at `backend/Dockerfile`. To build the backend image locally from the repository root:

```bash
docker build -t mood-journal-backend ./backend
```

To run the container locally, provide the Laravel environment variables and map the container's Apache port:

```bash
docker run --rm -p 10000:10000 --env-file backend/.env mood-journal-backend
```

The `.dockerignore` file excludes local environment files, dependencies, logs, caches, tests, and documentation from the Docker build context. Create `backend/.env` with valid local settings before running the container. The container still needs access to a reachable PostgreSQL database; the database is not included in this image.

## Development Commands

### Backend

```bash
php artisan serve
php artisan migrate
php artisan test
php artisan config:clear
```

### Frontend

```bash
npm run dev
npm run build
npm run preview
```

## Development Roadmap

Planned improvements include:

- Mood history with chronological entries and optional notes
- Calendar view for historical check-ins
- Editing and deleting saved mood entries
- Mood intensity tracking
- Expanded mood trends and insights
- Additional automated tests

## Project Status

The application currently includes user authentication, a responsive dashboard, English and Swedish localization, persistent mood check-ins, and separate production deployments for the frontend and backend.

A production issue involving Laravel Sanctum authentication across the separately hosted frontend and backend was addressed by introducing a Nuxt server-side API proxy. Browser requests now go through the frontend domain, while the Nuxt server forwards them to Laravel with the cookies and headers needed for session-based authentication and CSRF protection.

The `My Journal` and `Insights` sections are planned and currently marked as coming soon.
