# Project Structure

## Overview

Mood Journal is organized as a monorepo containing two independent applications:

- `frontend/` – Nuxt application responsible for the user interface.
- `backend/` – Laravel application responsible for business logic, authentication and data management.
- `docs/` – Project documentation.

The application will support two interface languages:

- English (`en`) – default language.
- Swedish (`sv`) – additional language.

Internationalization will be handled through Nuxt i18n, allowing users to switch between languages without duplicating pages or components.

## Repository Structure

    mood-journal/
    ├── frontend/
    ├── backend/
    ├── docs/
    │   └── PROJECT_STRUCTURE.md
    └── README.md

## Frontend – Nuxt

The frontend will use Nuxt 4, Vue 3 and TypeScript.

Planned organization:

    frontend/
    ├── app/
    │   ├── assets/
    │   ├── components/
    │   ├── composables/
    │   ├── layouts/
    │   ├── middleware/
    │   ├── pages/
    │   └── utils/
    ├── i18n/
    │   └── locales/
    │       ├── en.json
    │       └── sv.json
    ├── public/
    ├── tests/
    ├── nuxt.config.ts
    └── package.json

Responsibilities:

- `components/`: reusable interface components.
- `composables/`: reusable Vue logic and API interactions.
- `layouts/`: shared application layouts.
- `middleware/`: route protection and navigation logic.
- `pages/`: application pages and routing.
- `utils/`: shared utility functions.
- `i18n/locales/`: English and Swedish translation files.
- `tests/`: frontend tests.

### Internationalization

The application will use `@nuxtjs/i18n` to manage translations.

- English will be the default interface language.
- Swedish will be available as an alternative.
- UI text will be stored in translation files rather than hardcoded in components.
- Users will be able to switch languages through the interface.
- User-generated journal content will remain in the language in which it was written.

## Backend – Laravel

The backend will use PHP and Laravel to expose a REST API.

Planned organization:

    backend/
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   ├── Middleware/
    │   │   └── Requests/
    │   ├── Models/
    │   ├── Policies/
    │   └── Services/
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    ├── routes/
    ├── tests/
    └── composer.json

Responsibilities:

- `Controllers/`: handle HTTP requests and responses.
- `Middleware/`: process requests before they reach controllers.
- `Requests/`: validate incoming data.
- `Models/`: represent database entities.
- `Policies/`: define authorization rules.
- `Services/`: contain business logic when separate services are justified.
- `migrations/`: manage database schema changes.
- `factories/` and `seeders/`: generate test and development data.
- `tests/`: backend automated tests.

## Database

PostgreSQL will be used as the relational database.

Initial entities are expected to include:

- Users
- Mood entries
- Journal entries

User preferences may include the selected interface language.

Database relationships, constraints and indexes will be managed through Laravel migrations.

## Communication

The frontend will communicate with the Laravel backend through HTTP requests to a REST API.

The backend will validate requests, enforce authorization rules and manage database operations.

## Security and Privacy

Mood and journal records are personal and potentially sensitive.

The application will follow these principles:

- Users can only access their own records.
- Authorization is enforced server-side.
- All incoming data is validated.
- Sensitive information is not unnecessarily exposed in API responses or logs.
- Authentication and session management follow secure practices.

## Development Principles

- Keep frontend and backend responsibilities clearly separated.
- Prefer simple solutions over unnecessary abstractions.
- Use automated tests for important application behavior.
- Manage database changes through migrations.
- Keep documentation aligned with the actual implementation.

This structure is a starting point and will evolve as the application grows.
