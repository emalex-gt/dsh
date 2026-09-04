# Brief Estimate Confirmation Design

## Goal

Replace the immediate public brief submission with a protected review and confirmation flow. A prospect reviews the calculated estimate, adjusts services when needed, then confirms the estimate to create the client account, the approved budget PDF, and a password-setup email.

## Scope

- The public brief retains the existing company and services steps, validations, service preselection, and live estimate.
- Submitting the brief creates a confirmation-ready brief record rather than immediately completing the workflow.
- The review screen presents the selected services, their selected options, the calculated estimate before VAT, and explanatory copy.
- The prospect can return to the services step with their selections intact.
- Confirmation creates a client account, associates it with the brief, renders and stores a budget PDF, creates an approved budget record, and sends the Laravel password-reset notification.
- Filament continues to manage the generated client, brief, and budget.

## Non-Goals

- No payment collection, invoice generation, or e-signature provider integration.
- No changes to the existing authenticated client budget decision workflow.
- No public user registration form or plaintext password delivery.

## Public Confirmation Model

The first POST to `/brief` validates the form data and creates a `Brief` with status `pendiente_confirmacion`. The record stores the complete validated brief and estimate, but does not yet have a user or budget.

Each pending confirmation receives a cryptographically random opaque token. Only a SHA-256 hash is persisted, with an expiry timestamp. Public review, adjustment, and confirmation routes receive the raw token, locate the matching unexpired record by hash, and never expose a numeric brief ID. Confirmed or expired records cannot be reviewed or confirmed again.

This durable state is preferred to a session-only flow because confirmation creates operational records and must be safe against duplicate requests, new browser sessions, and concurrent tabs.

## Status Lifecycle

```text
pendiente_confirmacion
  -> confirmado
  -> expirado (when accessed after expiry)
```

`confirmado` is terminal for the public flow. The confirmation action locks the brief row inside a database transaction and proceeds only if it remains unconfirmed and unexpired.

## Review Experience

After the user selects `Enviar brief`, the application redirects to a tokenized review URL. The page title is **Revisar estimación** and includes:

- Selected services and item choices.
- Technical base, selected one-time or first-year charges, and prepaid recurring components when available.
- A clear total labeled **Estimación inicial** followed by **Importes sin IVA**.
- A note that the estimate reflects the selected scope and is the basis for the initial budget.

The primary action is **Confirmar estimación y crear mi acceso**. Supporting copy states that confirmation creates the client access and registers the budget for follow-up.

The secondary action is **Ajustar servicios**. Supporting copy states that it returns the user to the services step and recalculates the estimate after changes. It is not labeled “Cancelar”, because it does not discard the brief.

Before confirmation, the prospect enters or confirms their full name and checks an explicit acceptance checkbox. These values, along with timestamp, IP address, and user agent, are stored as the acceptance audit trail.

## Adjustment Flow

The adjustment route opens the existing brief wizard with data from the pending brief. It starts at the services step and retains company data, selected services, and item selections. Re-submitting replaces the pending brief data and estimate, rotates the opaque token, and redirects to the new review URL. The brief remains in `pendiente_confirmacion` while it is being adjusted. This prevents stale review links from confirming an outdated estimate.

## Confirmation Transaction

Within one database transaction, the confirmation action:

1. Locks and validates the pending brief state, token hash, and expiry.
2. Creates a non-administrator user with brief contact and company data, a random password hash, and no plaintext credential.
3. Associates the user with the brief and records its confirmation state and audit trail.
4. Renders a Blade-based PDF containing the client identity, selected service configuration, estimate summary, and pre-VAT note.
5. Stores the PDF in the private local budget disk path and creates a `Budget` in `aprobado` status with the same acceptance audit values.
6. Sends Laravel's password-reset notification after commit so the prospect establishes their own password.

The transaction rejects repeat confirmations. If the contact email already belongs to an account, confirmation does not create a duplicate account; it fails safely with a clear message instructing the prospect to contact the team.

## PDF Generation

Install `barryvdh/laravel-dompdf` and render a dedicated Blade view. The PDF uses only stored brief data and the stored calculated estimate, never recalculating from mutable catalog data. It does not display internal DSH calculations or VAT as included in the estimate.

The generated PDF is private and follows the existing budget storage and access conventions. Administrators can open it from Filament, while the authenticated client can access it through the existing budget area.

## Data Changes

Add public-confirmation metadata to `briefs`:

- `confirmation_token_hash`: nullable, unique SHA-256 token hash.
- `confirmation_expires_at`: nullable timestamp.
- `confirmed_at`: nullable timestamp.
- `confirmed_name`: nullable string.
- `confirmed_ip`: nullable string.
- `confirmed_user_agent`: nullable text.

Brief status options include `pendiente_confirmacion`, `confirmado`, and `expirado` alongside existing administrative states. No new budget columns are required.

## Error Handling

- Missing, invalid, used, or expired public tokens return a neutral unavailable page without exposing brief data.
- A missing calculated estimate prevents review and returns the prospect to service selection with a clear validation message.
- Existing client email, PDF generation failure, or database failure rolls back client, brief association, and budget creation. The pending brief remains available for retry where appropriate.
- Password-reset delivery failures are logged and reported to administrators; no plaintext password is surfaced to the public visitor.

## Testing

Feature coverage will verify:

- Valid brief submission redirects to a protected estimate review and stores no client or budget before confirmation.
- Review renders selected service information and pre-VAT estimate.
- Adjustment returns to the services step with selections retained and rotates the old token after resubmission.
- Confirmation creates exactly one client, confirmed brief, private PDF budget, and password-reset notification.
- A repeated confirmation cannot create duplicate records.
- Invalid, expired, and reused tokens do not expose data or create records.
- Existing client email is rejected without partial writes.
