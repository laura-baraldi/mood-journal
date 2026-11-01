# Mood Journal

Mood Journal is a mood tracking and self-reflection web application designed to help users record their daily emotions, monitor mood patterns, and reflect on their well-being.

The project is built with a separate frontend and backend, communicating through a REST API.

## Tech Stack

### Frontend

- Nuxt 4
- Vue 3
- TypeScript
- Vue I18n (`@nuxtjs/i18n`)

### Backend

- Laravel 13
- PHP 8.3+
- Laravel Sanctum

### Database

- PostgreSQL
- Neon

## Project Architecture

The application is divided into two independent applications:

- **Frontend:** Nuxt application responsible for the user interface, navigation, and client-side interactions.
- **Backend:** Laravel REST API responsible for authentication, validation, business logic, and database operations.

The frontend communicates with the backend through HTTP requests and JSON responses.

## Project Structure

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
        └── nuxt.config.ts

## Current Features

### Authentication

- User registration
- User login and logout
- Session-based authentication with Laravel Sanctum
- CSRF protection
- Protected authenticated dashboard
- Cookie-based authentication between frontend and backend

### Dashboard

- Responsive dashboard layout
- Component-based Vue architecture
- Daily mood selection interface
- Mood summary
- Weekly mood overview
- Inspirational content

### Internationalization

- English and Swedish translations
- Language switcher
- Localized dashboard content
- Localized mood labels and weekdays

**Note:** Mood check-ins, weekly chart values, and dashboard statistics currently use frontend demonstration data. Persistent mood tracking and database-backed statistics are planned for a subsequent development stage.

## Getting Started

### Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- PostgreSQL database

### 1. Clone the repository

    git clone https://github.com/laura-baraldi/mood-journal.git
    cd mood-journal

### 2. Backend setup

Navigate to the backend directory:

    cd backend

Install PHP dependencies:

    composer install

Create the environment file:

    copy .env.example .env

Generate the application key:

    php artisan key:generate

Configure the database connection in `backend/.env`:

    DB_CONNECTION=pgsql
    DB_HOST=your-database-host
    DB_PORT=5432
    DB_DATABASE=your-database-name
    DB_USERNAME=your-database-username
    DB_PASSWORD=your-database-password
    DB_SSLMODE=require

Configure the Sanctum stateful domain and session settings:

    SANCTUM_STATEFUL_DOMAINS=localhost:3000
    SESSION_DOMAIN=null
    SESSION_SECURE_COOKIE=false

Run database migrations:

    php artisan migrate

Start the Laravel development server:

    php artisan serve

The backend API will be available at `http://localhost:8000`.

### 3. Frontend setup

Open a second terminal and navigate to the frontend directory:

    cd frontend

Install dependencies:

    npm install

Start the Nuxt development server:

    npm run dev

The frontend will be available at `http://localhost:3000`.

## Authentication

Authentication is implemented using Laravel Sanctum's stateful SPA authentication.

The application uses session-based authentication with cookies rather than personal access tokens.

The authentication flow works as follows:

1. The frontend requests the CSRF cookie from Laravel Sanctum.
2. The browser stores the CSRF cookie.
3. The frontend sends the XSRF token in the request headers for state-changing requests.
4. Laravel validates the credentials and creates an authenticated session.
5. The browser sends the session cookie with subsequent API requests.
6. Protected endpoints use the `auth:sanctum` middleware to verify authentication.

The frontend includes credentials in API requests to allow session cookies to be sent.

## API Endpoints

| Method | Endpoint               | Description                     | Authentication |
| ------ | ---------------------- | ------------------------------- | -------------- |
| GET    | `/sanctum/csrf-cookie` | Retrieve the CSRF cookie        | No             |
| POST   | `/api/register`        | Register a new user             | No             |
| POST   | `/api/login`           | Authenticate a user             | No             |
| POST   | `/api/logout`          | End the authenticated session   | Yes            |
| GET    | `/api/user`            | Retrieve the authenticated user | Yes            |

## Environment Variables

Environment files contain local configuration and credentials and must not be committed to the repository.

Use `.env.example` as a template and configure your own local environment.

Never commit database passwords, application secrets, or other credentials.

## Development Commands

### Backend

Start the development server:

    php artisan serve

Run database migrations:

    php artisan migrate

Run the automated test suite:

    php artisan test

Clear cached configuration:

    php artisan config:clear

### Frontend

Start the development server:

    npm run dev

Build the production application:

    npm run build

Preview the production build:

    npm run preview

## Development Roadmap

Planned functionality includes:

- Backend integration for daily mood check-ins
- Persistent mood entries and optional journal notes
- Mood intensity tracking
- Mood history and calendar view
- Database-backed mood trends and statistics
- Historical mood data visualization
- Automated application-specific tests

## Project Status

The initial project setup, authentication foundation, and dashboard user interface have been implemented.

The application currently supports user registration, login, logout, authenticated session retrieval, a responsive dashboard interface, and English and Swedish localization.

Mood selection and dashboard visualizations currently use demonstration data. Backend integration for persistent mood tracking and statistics is planned for the next development stage.
