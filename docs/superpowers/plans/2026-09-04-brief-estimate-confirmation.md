# Brief Estimate Confirmation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow public brief visitors to review and confirm a calculated estimate, creating an approved PDF budget and client access exactly once.

**Architecture:** Persist a pending brief with an opaque, hashed confirmation token before exposing the review screen. A dedicated confirmation controller and service own public token resolution, token rotation, transactional client/budget creation, and password-reset dispatch. The existing brief wizard remains the data-entry interface and is reopened at the services step for adjustments.

**Tech Stack:** PHP 8.2, Laravel 12, Blade, Alpine.js, Filament 3, MySQL, `barryvdh/laravel-dompdf`, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-09-04-brief-estimate-confirmation-design.md`

## Global Constraints

- Keep public registration disabled and never expose a generated plaintext password.
- Persist only SHA-256 hashes of public confirmation tokens; raw tokens appear only in the current public URL or form submission.
- Generate and store budget PDFs on the existing private `local` disk.
- Display estimate totals without VAT included and without internal DSH calculation details.
- Only commercial active catalog services remain selectable and service selections remain editable before confirmation.
- Preserve the existing authenticated client budget decision workflow.

---

## File Structure

- Modify: `composer.json`, `composer.lock` — add the maintained Dompdf Laravel integration.
- Create: `database/migrations/2026_09_04_000000_add_confirmation_metadata_to_briefs_table.php` — persist lifecycle, token, expiry, and acceptance audit fields.
- Modify: `app/Models/Brief.php` — casts, fillable properties, state helpers, and relationships required by confirmation.
- Create: `app/Services/BriefEstimateConfirmationService.php` — token generation, token lookup, token rotation, and transactional confirmation.
- Create: `app/Http/Controllers/BriefEstimateConfirmationController.php` — public review, adjustment, and confirmation endpoints.
- Create: `app/Http/Requests/ConfirmBriefEstimateRequest.php` — signed-name and explicit acceptance validation.
- Modify: `app/Http/Controllers/ClientBriefController.php` — create/update pending records and hydrate adjustment data.
- Modify: `routes/web.php` — public tokenized review, adjustment, and confirmation routes.
- Modify: `resources/views/briefs/edit.blade.php` — submit pending brief token when adjusting and begin at services after an adjustment link.
- Create: `resources/views/briefs/review-estimate.blade.php` — public estimate review and action copy.
- Create: `resources/views/pdf/brief-estimate-budget.blade.php` — server-only budget PDF markup.
- Modify: `app/Filament/Resources/BriefResource.php` and `app/Filament/Resources/BriefResource/Pages/ViewBrief.php` — render pending/confirmed lifecycle state and prevent duplicate manual client provisioning.
- Modify: `tests/Feature/BriefTest.php` — public lifecycle, tokens, adjustment, idempotency, PDF, client creation, and reset-notification tests.

### Task 1: Add PDF dependency and durable confirmation metadata

**Files:**
- Modify: `composer.json`
- Modify: `composer.lock`
- Create: `database/migrations/2026_09_04_000000_add_confirmation_metadata_to_briefs_table.php`
- Modify: `app/Models/Brief.php`
- Test: `tests/Feature/BriefTest.php`

**Interfaces:**
- Produces: `Brief::isPendingConfirmation(): bool`, `Brief::isConfirmationAvailable(): bool`, and persisted confirmation metadata.
- Consumes: existing `briefs` table and `Brief` model.

- [ ] **Step 1: Write failing persistence tests**

```php
public function test_pending_brief_persists_confirmation_metadata(): void
{
    $brief = Brief::create([
        'status' => 'pendiente_confirmacion',
        'data' => ['contact_email' => 'ana@example.test'],
        'confirmation_token_hash' => hash('sha256', 'token'),
        'confirmation_expires_at' => now()->addHour(),
    ]);

    $this->assertTrue($brief->isPendingConfirmation());
    $this->assertTrue($brief->isConfirmationAvailable());
}
```

- [ ] **Step 2: Run the targeted test to verify the schema and helpers are absent**

Run: `php artisan test --filter=BriefTest::test_pending_brief_persists_confirmation_metadata`

Expected: FAIL because confirmation columns and model helpers do not exist.

- [ ] **Step 3: Install the PDF package and add the migration**

Run: `composer require barryvdh/laravel-dompdf`

Create the migration with nullable `confirmation_token_hash`, `confirmation_expires_at`, `confirmed_at`, `confirmed_name`, `confirmed_ip`, and `confirmed_user_agent` columns. Add a unique index for `confirmation_token_hash`. Update `Brief::$fillable` and casts for the three timestamps. Implement helpers that require status `pendiente_confirmacion`, a nonblank token hash, and an expiry later than `now()`.

- [ ] **Step 4: Re-run the targeted test**

Run: `php artisan test --filter=BriefTest::test_pending_brief_persists_confirmation_metadata`

Expected: PASS.

- [ ] **Step 5: Commit the isolated dependency and schema change**

```bash
git add composer.json composer.lock database/migrations/2026_09_04_000000_add_confirmation_metadata_to_briefs_table.php app/Models/Brief.php tests/Feature/BriefTest.php
git commit -m "feat: add brief confirmation metadata"
```

### Task 2: Implement protected pending-brief creation and tokenized review access

**Files:**
- Create: `app/Services/BriefEstimateConfirmationService.php`
- Create: `app/Http/Controllers/BriefEstimateConfirmationController.php`
- Modify: `app/Http/Controllers/ClientBriefController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/BriefTest.php`

**Interfaces:**
- Produces: `BriefEstimateConfirmationService::createPending(array $briefData): array{brief: Brief, token: string}` and `findAvailable(string $token): Brief`.
- Consumes: validated output from `StoreClientBriefRequest::validatedBriefData()`.

- [ ] **Step 1: Write failing lifecycle and privacy tests**

```php
public function test_submitting_a_brief_redirects_to_its_protected_estimate_review(): void
{
    $response = $this->post(route('brief.update'), $this->validBriefPayload());

    $response->assertRedirectToRoute('brief.review', ['token' => $this->anything()]);
    $this->assertDatabaseHas('briefs', ['status' => 'pendiente_confirmacion']);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('budgets', 0);
}

public function test_invalid_or_expired_review_tokens_do_not_expose_brief_data(): void
{
    $this->get(route('brief.review', 'invalid'))->assertNotFound();
}
```

- [ ] **Step 2: Run the targeted tests to verify they fail**

Run: `php artisan test --filter="(submitting_a_brief_redirects_to_its_protected_estimate_review|invalid_or_expired_review_tokens_do_not_expose_brief_data)"`

Expected: FAIL because the review route and token service do not exist.

- [ ] **Step 3: Build the token service and routes**

Implement `createPending()` with `Str::random(64)`, persist only `hash('sha256', $token)`, set a 48-hour expiry, and return raw token only to the controller. Implement `findAvailable()` by hash, pending state, and non-expired timestamp; return a 404 for every unavailable token. Change `ClientBriefController::update()` to call the service and redirect to `brief.review`. Add public routes:

```php
Route::get('/brief/revisar/{token}', [BriefEstimateConfirmationController::class, 'show'])->name('brief.review');
Route::get('/brief/revisar/{token}/ajustar', [BriefEstimateConfirmationController::class, 'adjust'])->name('brief.adjust');
Route::post('/brief/revisar/{token}/confirmar', [BriefEstimateConfirmationController::class, 'confirm'])->name('brief.confirm');
```

- [ ] **Step 4: Re-run the lifecycle and privacy tests**

Run: same command as Step 2.

Expected: PASS. Confirm no user or budget exists before public confirmation.

- [ ] **Step 5: Commit the tokenized pending-brief flow**

```bash
git add app/Services/BriefEstimateConfirmationService.php app/Http/Controllers/BriefEstimateConfirmationController.php app/Http/Controllers/ClientBriefController.php routes/web.php tests/Feature/BriefTest.php
git commit -m "feat: add protected brief estimate review"
```

### Task 3: Add adjustment handling and review experience

**Files:**
- Modify: `app/Http/Controllers/ClientBriefController.php`
- Modify: `app/Http/Controllers/BriefEstimateConfirmationController.php`
- Modify: `resources/views/briefs/edit.blade.php`
- Create: `resources/views/briefs/review-estimate.blade.php`
- Test: `tests/Feature/BriefTest.php`

**Interfaces:**
- Consumes: a pending `Brief` resolved by `findAvailable()`.
- Produces: `brief.adjust` view data that starts `briefWizard` at `servicios`; re-submission rotates the old token and preserves prior company/service data.

- [ ] **Step 1: Write failing adjustment tests**

```php
public function test_adjusting_a_pending_estimate_reopens_the_services_step_with_current_selections(): void
{
    [$brief, $token] = $this->pendingBriefWithToken();

    $this->get(route('brief.adjust', $token))
        ->assertOk()
        ->assertSee('stepIndex: 1', false)
        ->assertSee((string) $brief->data['selected_service_ids'][0]);
}

public function test_resubmitting_an_adjusted_brief_rotates_its_review_token(): void
{
    [$brief, $token] = $this->pendingBriefWithToken();

    $response = $this->post(route('brief.update'), array_merge($this->validBriefPayload(), ['pending_token' => $token]));

    $response->assertRedirect();
    $this->get(route('brief.review', $token))->assertNotFound();
}
```

- [ ] **Step 2: Run the targeted adjustment tests to verify failure**

Run: `php artisan test --filter="(adjusting_a_pending_estimate_reopens_the_services_step_with_current_selections|resubmitting_an_adjusted_brief_rotates_its_review_token)"`

Expected: FAIL because adjustment and token rotation do not exist.

- [ ] **Step 3: Implement adjustment data hydration, token rotation, and the review view**

Pass pending brief data into `briefs.edit`, render a hidden `pending_token`, and add `initialStepKey` to the Alpine config. Initialize `stepIndex` from that key only for `brief.adjust`; initial public entry remains company step. When `pending_token` is valid, update the same pending brief, recompute via the existing request object, rotate its token hash and expiry, then redirect to the new review URL.

Build `review-estimate.blade.php` using the application’s existing premium panel classes. Render selected service configurations and `technical_estimate.budget.estimated_total_before_tax`; label the total `Estimación inicial` and include `Importes sin IVA`. Add explanatory text and the actions:

```text
Confirmar estimación y crear mi acceso
Al confirmar, generaremos tu acceso al área privada y registraremos este presupuesto para su seguimiento.

Ajustar servicios
Vuelve al paso de servicios para cambiar tu selección. La estimación se actualizará automáticamente.
```

- [ ] **Step 4: Re-run the targeted adjustment tests**

Run: same command as Step 2.

Expected: PASS.

- [ ] **Step 5: Commit the review and adjustment flow**

```bash
git add app/Http/Controllers/ClientBriefController.php app/Http/Controllers/BriefEstimateConfirmationController.php resources/views/briefs/edit.blade.php resources/views/briefs/review-estimate.blade.php tests/Feature/BriefTest.php
git commit -m "feat: add editable brief estimate review"
```

### Task 4: Confirm estimate, generate client account and approved PDF budget

**Files:**
- Create: `app/Http/Requests/ConfirmBriefEstimateRequest.php`
- Modify: `app/Services/BriefEstimateConfirmationService.php`
- Modify: `app/Http/Controllers/BriefEstimateConfirmationController.php`
- Create: `resources/views/pdf/brief-estimate-budget.blade.php`
- Test: `tests/Feature/BriefTest.php`

**Interfaces:**
- Consumes: `ConfirmBriefEstimateRequest` containing `confirmed_name` and `accept_terms` plus an available pending brief token.
- Produces: one client user, one confirmed brief, one approved private `Budget`, one generated PDF, and one Laravel password-reset notification.

- [ ] **Step 1: Write failing confirmation and idempotency tests**

```php
public function test_confirming_an_estimate_creates_client_budget_pdf_and_password_reset_link(): void
{
    Notification::fake();
    [$brief, $token] = $this->pendingBriefWithToken();

    $response = $this->post(route('brief.confirm', $token), [
        'confirmed_name' => 'Ana Cliente',
        'accept_terms' => '1',
    ]);

    $response->assertRedirectToRoute('brief.thanks');
    $this->assertDatabaseHas('briefs', ['id' => $brief->id, 'status' => 'confirmado']);
    $this->assertDatabaseHas('budgets', ['status' => 'aprobado']);
    Storage::disk('local')->assertExists(Budget::query()->value('pdf_path'));
    Notification::assertSentTo(User::query()->firstOrFail(), ResetPassword::class);
}

public function test_confirming_a_token_twice_cannot_create_duplicate_records(): void
{
    [$brief, $token] = $this->pendingBriefWithToken();
    $payload = ['confirmed_name' => 'Ana Cliente', 'accept_terms' => '1'];

    $this->post(route('brief.confirm', $token), $payload)->assertRedirect();
    $this->post(route('brief.confirm', $token), $payload)->assertNotFound();
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('budgets', 1);
}
```

- [ ] **Step 2: Run the confirmation tests and verify they fail**

Run: `php artisan test --filter="(confirming_an_estimate_creates_client_budget_pdf_and_password_reset_link|confirming_a_token_twice_cannot_create_duplicate_records)"`

Expected: FAIL because confirmation validation, PDF output, and client creation are absent.

- [ ] **Step 3: Implement validation, transactional confirmation, PDF rendering, and email dispatch**

Validate `confirmed_name` as required string max 255 and `accept_terms` as accepted. In `BriefEstimateConfirmationService::confirm()`, use `DB::transaction()` with `lockForUpdate()` on the available brief. Create the `User` from stored brief contact/company fields, set `is_admin` false, and save `Hash::make(Str::random(64))` as password. Store confirmation audit fields on both `Brief` and `Budget`. Render the PDF through `Pdf::loadView('pdf.brief-estimate-budget', [...])`, write it to `budgets/brief-{$brief->id}.pdf` on `local`, calculate `pdf_hash`, and create `Budget` with `status => 'aprobado'`.

After the transaction succeeds, call `Password::sendResetLink(['email' => $user->email])`. If the email already exists, return a validation error before writes. The PDF Blade view must render legal/brand/contact data, selected services/items, estimate components, total before VAT, the `Importes sin IVA` note, confirmation name, and confirmation date; it must not render internal DSH percentages.

- [ ] **Step 4: Re-run the confirmation tests**

Run: same command as Step 2.

Expected: PASS. Inspect the test disk path to confirm PDF storage is private.

- [ ] **Step 5: Commit confirmation, PDF, and password setup flow**

```bash
git add app/Http/Requests/ConfirmBriefEstimateRequest.php app/Services/BriefEstimateConfirmationService.php app/Http/Controllers/BriefEstimateConfirmationController.php resources/views/pdf/brief-estimate-budget.blade.php tests/Feature/BriefTest.php
git commit -m "feat: confirm brief estimates into client budgets"
```

### Task 5: Integrate the lifecycle with Filament and run end-to-end verification

**Files:**
- Modify: `app/Filament/Resources/BriefResource.php`
- Modify: `app/Filament/Resources/BriefResource/Pages/ViewBrief.php`
- Modify: `tests/Feature/AdminPanelTest.php`
- Test: `tests/Feature/BriefTest.php`, `tests/Feature/AdminPanelTest.php`, `tests/Feature/BudgetTest.php`

**Interfaces:**
- Consumes: new brief statuses and generated `user_id`/budget association.
- Produces: accurate Filament labels/colors and no manual “Crear cliente” action for a confirmed brief.

- [ ] **Step 1: Write failing Filament status/action tests**

```php
public function test_admin_brief_listing_shows_pending_confirmation_status(): void
{
    $brief = Brief::factory()->create(['status' => 'pendiente_confirmacion']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/briefs')
        ->assertSee('Pendiente de confirmación');
}
```

- [ ] **Step 2: Run the targeted Filament test to verify failure**

Run: `php artisan test --filter=AdminPanelTest::test_admin_brief_listing_shows_pending_confirmation_status`

Expected: FAIL because the status has no Filament label.

- [ ] **Step 3: Update Filament status presentation and action visibility**

Add labels/colors for `pendiente_confirmacion`, `confirmado`, and `expirado`. Keep `Crear cliente` visible only when no client is assigned and status is not `confirmado`; generated client accounts must not be provisioned manually a second time.

- [ ] **Step 4: Run focused and full regression suites**

Run:

```bash
php artisan test --filter=BriefTest
php artisan test --filter=AdminPanelTest
php artisan test --filter=BudgetTest
php artisan test
git diff --check
```

Expected: all tests pass and no whitespace errors occur.

- [ ] **Step 5: Build frontend assets and commit final integration**

Run: `npm run build`

Review generated artifacts against repository policy, preserving only intended versioned build outputs. Then commit:

```bash
git add app/Filament/Resources/BriefResource.php app/Filament/Resources/BriefResource/Pages/ViewBrief.php tests/Feature/AdminPanelTest.php
git commit -m "feat: surface brief confirmations in admin"
```
