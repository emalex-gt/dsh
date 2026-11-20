# Brief Optional Sections and Mobile Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore optional brief sections after the required company and service steps while making the mobile brief use substantially more horizontal space.

**Architecture:** Keep the current Blade and Alpine wizard. The step registry will expose every historic section after Services, and the existing submit endpoint will remain the single transition to estimate review. Browser-side validation will continue to enforce only the visible required step; optional inputs will have no HTML or group-level required constraints.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Tailwind CSS, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-11-20-brief-optional-sections-mobile-design.md`

## Global Constraints

- Empresa and Servicios remain the only required brief steps.
- Existing calculation, catalog and estimate-confirmation behavior must not change.
- Do not add a database migration or a dependency.
- Preserve previously supplied optional values when the form is submitted.

## Review Focus

- Submitting after Services with every optional input empty must create the same pending estimate flow.
- A partially completed optional phase must not block direct estimate review.
- Required service item choices must still block estimate review when missing.
- All optional panels must be visible in the step registry and have a functional Alpine condition.
- Mobile layout changes must not reduce desktop padding or the desktop step sidebar.

---

### Task 1: Cover the restored optional-flow contract

**Files:**
- Modify: `tests/Feature/BriefTest.php`
- Modify: `resources/views/briefs/edit.blade.php`

**Interfaces:**
- Consumes: `POST /brief` handled by `StoreClientBriefRequest`.
- Produces: Public brief markup with all optional step keys and a direct estimate CTA.

- [ ] **Step 1: Write failing feature assertions**

Add tests proving a payload containing only required company and service fields redirects to the estimate review, and that the rendered brief contains the restored optional labels plus `Ver estimacion`.

- [ ] **Step 2: Run the focused test to verify it fails**

Run: `php artisan test --filter=BriefTest`
Expected: FAIL because optional labels and direct-estimate copy are absent.

- [ ] **Step 3: Restore all step records and optional markup**

In `resources/views/briefs/edit.blade.php`, order `Empresa` and `Servicios` first, enable the remaining ten step records, replace each static hidden panel with the current-step expression, remove HTML `required` attributes from optional fields, and change checkbox group metadata so optional groups do not require a selection.

- [ ] **Step 4: Add direct estimate navigation**

Add `Ver estimacion` in the shared wizard footer whenever the active step is Services or optional. It must call the existing `submitBrief()` and must retain `Continuar` for optional step navigation.

- [ ] **Step 5: Run the focused test to verify it passes**

Run: `php artisan test --filter=BriefTest`
Expected: PASS.

### Task 2: Compact the brief on mobile without affecting desktop

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/briefs/edit.blade.php`
- Test: `tests/Feature/BriefTest.php`

**Interfaces:**
- Consumes: the `briefMode` layout flag.
- Produces: compact mobile page, header, form body and footer spacing below the `sm` breakpoint.

- [ ] **Step 1: Write a failing render assertion**

Add a markup assertion for the mobile-specific spacing classes used by the brief layout.

- [ ] **Step 2: Run the focused test to verify it fails**

Run: `php artisan test --filter=BriefTest`
Expected: FAIL because the compact classes are not rendered.

- [ ] **Step 3: Apply mobile-only spacing changes**

Use smaller base padding on the `briefMode` wrapper and compact base padding for brief panel header, body and footer; retain current `sm` and `lg` values for wider screens.

- [ ] **Step 4: Run the focused test to verify it passes**

Run: `php artisan test --filter=BriefTest`
Expected: PASS.

### Task 3: Verify the integrated brief flow

**Files:**
- Modify: `tests/Feature/BriefTest.php`

**Interfaces:**
- Consumes: optional section form fields and existing service validation.
- Produces: regression coverage that confirms optional values persist without becoming mandatory.

- [ ] **Step 1: Write a failing partial-optional-data test**

Post a valid required brief plus one optional field such as `brand_values`; assert the created pending brief retains the value and redirects to review.

- [ ] **Step 2: Run the focused test to verify it fails**

Run: `php artisan test --filter=BriefTest`
Expected: FAIL until the optional panel requirements are removed.

- [ ] **Step 3: Make the minimal validation or markup correction**

Only adjust client-side optional requirements if the test identifies a blocking path; server-side rules remain required-only unless the failure demonstrates otherwise.

- [ ] **Step 4: Run project verification**

Run: `php artisan test` and `npm run build`
Expected: all tests pass and Vite completes successfully.

- [ ] **Step 5: Commit**

```bash
git add resources/views/briefs/edit.blade.php resources/views/layouts/guest.blade.php tests/Feature/BriefTest.php docs/superpowers/specs/2026-11-20-brief-optional-sections-mobile-design.md docs/superpowers/plans/2026-11-20-brief-optional-sections-mobile.md
git commit -m "Improve optional brief sections on mobile"
```
