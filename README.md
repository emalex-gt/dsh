# DSH Client Area

DSH Client Area is a Laravel application for managing the working relationship between a digital services company and its clients. It brings project intake, service selection, estimates, client access, billing, delivery requests, and support into one application.

The product has two complementary experiences: a private portal where clients can review project information and take action, and an internal administration area for the team.

## Tech Stack

| Area | Technologies |
| --- | --- |
| Backend | PHP 8.2+, Laravel 12 |
| Administration | Filament 3 |
| Frontend | Blade, Alpine.js, Tailwind CSS |
| Database | MySQL; SQLite in-memory configuration for tests |
| Authentication | Laravel Breeze |
| Build | Vite |
| Testing | PHPUnit and Laravel feature tests |

## Core Workflows

- Public, structured project briefs for requirements intake.
- Configurable service selection with client-facing estimate feedback.
- Administrator-driven client provisioning, including client creation from a reviewed brief.
- Private client dashboard with project data, assigned demos, and direct mockups.
- Quote review, acceptance, and change-request handling, followed by invoice access and payment visibility.
- Hosting, domain, and email service information in the client portal.
- Separate development change requests and general support ticket workflows.
- Filament administration for briefs, clients, commercial catalog data, billing records, demos, and workflow requests.

## Engineering Highlights

### Configurable service catalog

The commercial catalog is modeled as a hierarchy that can be managed from Filament:

```text
Category
  -> Subcategory
      -> Service
          -> Service Item
              -> Options
```

This structure drives the public brief: it determines which services can be selected, which configuration choices are required, and which options are available for each service.

### Quotation and pricing logic

`BriefTechnicalQuoteCalculator` combines the selected commercial configuration with technical calculation rules, technical implementation options, recurring components, and additional fees. It produces a stored estimate while keeping configurable pricing rules and internal commercial handling on the server side.

### Client and administration separation

The client portal is a server-rendered Laravel experience built with Blade and Alpine.js. It is focused on information access and client actions. The Filament panel is restricted to administrators and manages operational data, catalogs, client accounts, billing documents, and workflow updates.

### Workflow state modeling

The application keeps distinct lifecycle states for quotes, invoices, development requests, and support tickets. For example, approved quotes leave the pending client list, completed development requests are separated from active editing work, and invoices expose payment status independently from quote approval.

### Authentication and authorization

Client areas require authentication, and public customer registration is not available. Administrators provision client accounts through the internal panel, while Filament access is limited to users marked as administrators.

### Sensitive operational credentials

Recoverable hosting and email credentials are encrypted at rest using Laravel encrypted attribute casting and excluded from model serialization.

## Architecture Overview

```text
Public Project Brief
        |
        v
Laravel Application
        |
        +---- Client Portal
        |       Blade + Alpine.js
        |
        +---- Filament Admin Panel
        |
        +---- Business / Application Logic
        |       Catalog and estimation
        |       Quotes and billing
        |       Development requests
        |       Support workflows
        |
        v
      MySQL
```

## Example Business Flow

### Project Intake to Quote

1. A prospect completes the public project brief.
2. The active catalog determines the available services and configuration options.
3. Server-side validation stores the selected configuration and the resulting estimate.
4. An administrator reviews the submitted brief and can provision a client account associated with it.
5. The administrator makes a quote available to the client in the private portal.
6. The client reviews the PDF, accepts it, or requests changes.
7. Invoice documents and payment status are made available through the billing area as they are assigned.

## Testing

The feature test suite covers authentication and authorization, public brief submission and validation, pricing and estimation behavior, quote and invoice access, development and support workflows, credential encryption, and administrator-only operational actions.

```bash
php artisan test
```

## Local Development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure the appropriate MySQL connection in `.env`, then run:

```bash
php artisan migrate
npm run build
php artisan serve
```

## Deployment Notes

Compiled Vite assets are versioned in `public/build` to support deployment environments where Node.js is not available at runtime.

## AI-Assisted Development

OpenAI Codex and ChatGPT are used as part of the development workflow for implementation support, debugging, refactoring, technical investigation, and documentation. Architecture, code review, validation, testing, and final technical decisions remain developer responsibilities.

## Project Status

DSH Client Area is under active development. This repository represents the current application implementation.
