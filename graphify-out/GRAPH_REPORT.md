# Graph Report - lks-santri-karya  (2026-10-09)

## Corpus Check
- 161 files · ~66,871 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1166 nodes · 2346 edges · 131 communities (64 shown, 67 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `e1dc6228`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- frontend/app.js
- FortifyServiceProvider.php
- TestCase
- Tech Stack — LKS Santri Karya
- package.json
- User
- Product Requirements Document
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Illuminate\Http\Request
- PeriodParticipantSnapshot
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- bootstrap/app.php
- Referensi Rumus LKS
- LksCoreTest
- LksPeriod
- Illuminate\Database\Schema\Blueprint
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- PeriodActivity
- UserFactory.php
- 4. Tabel master identitas dan organisasi
- loadRecap
- Ekspor PDF LKS
- LksScoreCalculator
- scripts
- Product
- AuditLog
- composer.json
- require-dev
- settings.php
- OrganizationController.php
- web.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- require
- artisan
- Illuminate\Support\Facades\Schema
- ⚡security.blade.php
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- AuthenticationTest
- 0001_01_01_000000_create_users_table.php
- PasswordResetTest
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- openBulkChecklist
- applyDashboard
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Facades\DB
- 2026_10_07_010000_assign_leader_to_teams.php
- escapeHtml
- Panduan Brevo untuk Email Reset Password
- Panduan Kerja Proyek LKS Santri Karya
- ⚡two-factor-setup-modal.blade.php
- console.php
- ⚡profile.blade.php
- header.blade.php
- sidebar.blade.php
- card.blade.php
- simple.blade.php
- split.blade.php
- ⚡appearance.blade.php
- ⚡delete-user-form.blade.php
- ⚡recovery-codes.blade.php
- Illuminate\Http\JsonResponse
- renderFullCheck
- SecurityTest
- PeriodActivationService
- PasswordValidationRules
- Illuminate\Database\Migrations\Migration
- LksChecklist
- PeriodController.php
- FortifyServiceProvider
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- 2026_10_07_030000_add_lks_work_calendar.php
- PasswordResetTest.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- AppServiceProvider.php
- OrganizationController
- ProfileValidationRules
- GenericFailedPasswordResetLinkResponse
- ExampleTest
- loadDashboard
- LksExportController.php
- EmailVerificationTest
- loadHistory
- Illuminate\Http\Response

## God Nodes (most connected - your core abstractions)
1. `User` - 84 edges
2. `LksPeriod` - 66 edges
3. `PeriodParticipantSnapshot` - 46 edges
4. `LksCoreTest` - 38 edges
5. `AuditLog` - 37 edges
6. `escapeHtml()` - 33 edges
7. `PeriodActivity` - 30 edges
8. `LksScoreCalculator` - 28 edges
9. `TestCase` - 28 edges
10. `PeriodConfigurationController` - 25 edges

## Surprising Connections (you probably didn't know these)
- `2. Prinsip arsitektur` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `7. Rumus perhitungan` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Database Design.md → backend/app/Services/LksScoreCalculator.php
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php
- `3. Paket dan kemampuan platform` --references--> `AuditLog`  [INFERRED]
  docs/Tech Stack.md → backend/app/Models/AuditLog.php
- `3. Paket dan kemampuan platform` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Tech Stack.md → backend/app/Services/LksScoreCalculator.php

## Import Cycles
- None detected.

## Communities (131 total, 67 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.07
Nodes (37): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, breadcrumb, checklistSaveErrors (+29 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.17
Nodes (14): {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+6 more)

### Community 2 - "TestCase"
Cohesion: 0.10
Nodes (10): PasswordConfirmationTest, RegistrationTest, TwoFactorChallengeTest, DashboardTest, ExampleTest, TestCase, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase (+2 more)

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.11
Nodes (12): User, LksPeriodPolicy, ProfileUpdateTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne, Illuminate\Foundation\Auth\User (+4 more)

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.11
Nodes (10): {closure#6}(), Department, LksActivity, PeriodHolidaySnapshot, Role, 4.5 `teams`, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model (+2 more)

### Community 9 - "PeriodParticipantSnapshot"
Cohesion: 0.10
Nodes (10): PeriodParticipantSnapshot, PeriodParticipantSnapshotPolicy, {closure#2}(), {closure#7}(), {closure#1}(), {closure#5}(), {closure#6}(), {closure#7}() (+2 more)

### Community 11 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (17): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+9 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "bootstrap/app.php"
Cohesion: 0.13
Nodes (15): EnsurePasswordChanged, EnsureUserIsActive, Logout, {closure#1}(), {closure#2}(), {closure#3}(), Closure, Illuminate\Foundation\Application (+7 more)

### Community 16 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 18 - "LksPeriod"
Cohesion: 0.25
Nodes (3): ChecklistController, LksExportController, LksPeriod

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (15): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+7 more)

### Community 20 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 21 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.18
Nodes (4): {closure#12}(), {closure#13}(), SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 22 - "PeriodActivity"
Cohesion: 0.21
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), PeriodActivity, Illuminate\Validation\ValidationException

### Community 23 - "UserFactory.php"
Cohesion: 0.27
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 24 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.25
Nodes (7): 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 25 - "loadRecap"
Cohesion: 0.17
Nodes (24): canUsePersonalLks(), filterRecapRows(), initials(), loadRecap(), openRecapDetail(), participantStatus(), percentage(), renderActivityProgress() (+16 more)

### Community 26 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses, Ekspor PDF LKS, Rekomendasi otomatis, Tanda tangan

### Community 27 - "LksScoreCalculator"
Cohesion: 0.10
Nodes (9): {closure#11}(), {closure#4}(), {closure#7}(), {closure#9}(), RecapController, {closure#8}(), LksScoreCalculator, EffectiveActivityTargetTest (+1 more)

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "AuditLog"
Cohesion: 0.10
Nodes (14): {closure#10}(), {closure#10}(), {closure#12}(), {closure#13}(), {closure#14}(), {closure#15}(), {closure#18}(), {closure#19}() (+6 more)

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 34 - "OrganizationController.php"
Cohesion: 0.10
Nodes (5): {closure#11}(), {closure#14}(), {closure#9}(), Team, Illuminate\Validation\Rule

### Community 36 - "2026_09_29_000001_create_lks_configuration_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 37 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 39 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.20
Nodes (4): {closure#1}(), {closure#1}(), {closure#2}(), Illuminate\Support\Facades\Schema

### Community 40 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 43 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 44 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 47 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 49 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 50 - "openBulkChecklist"
Cohesion: 0.15
Nodes (15): activityScheduleLabel(), bulkChecklistMonths(), dateInputValue(), loadChecklistCorrectionContext(), localDateValue(), markAdminFormPristine(), openBulkChecklist(), openChecklistCorrection() (+7 more)

### Community 51 - "applyDashboard"
Cohesion: 0.21
Nodes (16): applyDashboard(), applyViewerIdentity(), canOpenView(), canView(), displayRole(), finishInitialBoot(), hasConfiguredRole(), openView() (+8 more)

### Community 52 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 53 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 54 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 55 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 56 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 58 - "Illuminate\Support\Facades\DB"
Cohesion: 0.12
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Facades\DB, Illuminate\Support\Str (+1 more)

### Community 60 - "escapeHtml"
Cohesion: 0.25
Nodes (14): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), escapeHtml(), formatDate(), masterActivityRuleFields(), openAdminFormModal() (+6 more)

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 108 - "Illuminate\Http\JsonResponse"
Cohesion: 0.24
Nodes (4): Controller, AccountController, PeriodController, Illuminate\Http\JsonResponse

### Community 109 - "renderFullCheck"
Cohesion: 0.40
Nodes (6): activityRuleLabel(), checkIcon(), checklistSaveErrorKey(), renderFullCheck(), renderQuickCheck(), setChecklistSaveError()

### Community 112 - "PasswordValidationRules"
Cohesion: 0.24
Nodes (6): CreateNewUser, ResetUserPassword, PasswordValidationRules, Illuminate\Support\Facades\Validator, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 113 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.22
Nodes (3): {closure#1}(), {closure#2}(), Illuminate\Database\Migrations\Migration

### Community 114 - "LksChecklist"
Cohesion: 0.16
Nodes (9): LksChecklist, ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface (+1 more)

### Community 117 - "FortifyServiceProvider"
Cohesion: 0.24
Nodes (3): AppServiceProvider, FortifyServiceProvider, Illuminate\Support\ServiceProvider

### Community 120 - "PasswordResetTest.php"
Cohesion: 0.25
Nodes (3): Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification

### Community 122 - "AppServiceProvider.php"
Cohesion: 0.21
Nodes (8): ActiveUserProvider, {closure#1}(), {closure#2}(), Carbon\CarbonImmutable, Illuminate\Auth\EloquentUserProvider, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Support\Facades\Date, Illuminate\Validation\Rules\Password

### Community 125 - "GenericFailedPasswordResetLinkResponse"
Cohesion: 0.40
Nodes (3): GenericFailedPasswordResetLinkResponse, Illuminate\Support\Facades\Password, Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse

### Community 127 - "ExampleTest"
Cohesion: 0.38
Nodes (3): ExampleTest, PeriodActivityRuleTest, PHPUnit\Framework\TestCase

### Community 128 - "loadDashboard"
Cohesion: 0.26
Nodes (15): apiFormError(), confirmAction(), loadDashboard(), logout(), parseHolidayImportEntries(), persistChecklist(), requestLogout(), setButtonBusy() (+7 more)

### Community 129 - "LksExportController.php"
Cohesion: 0.14
Nodes (11): {closure#1}(), {closure#2}(), DocumentSignatoryController, {closure#2}(), {closure#5}(), {closure#6}(), DocumentSignatory, {closure#4}() (+3 more)

### Community 131 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 132 - "loadHistory"
Cohesion: 0.35
Nodes (11): apiError(), dashboardNotice(), exportPdf(), hidePageProgress(), loadAccount(), loadAdminView(), loadHistory(), loadHistoryRecap() (+3 more)

### Community 133 - "Illuminate\Http\Response"
Cohesion: 0.28
Nodes (4): PrototypeDashboardController, 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

## Knowledge Gaps
- **221 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+216 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 459 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **67 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `OrganizationController.php`, `EmailVerificationTest`, `TestCase`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `AuthenticationTest`, `SecurityTest`, `PeriodActivationService`, `PasswordValidationRules`, `PasswordResetTest`, `LksChecklist`, `LksCoreTest`, `UserFactory.php`, `PasswordResetTest.php`, `OrganizationController`?**
  _High betweenness centrality (0.109) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `LksExportController.php`, `web.php`, `Illuminate\Http\Response`, `User`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\Request`, `PeriodParticipantSnapshot`, `Illuminate\Http\JsonResponse`, `PeriodActivationService`, `LksCoreTest`, `PeriodController.php`, `PeriodActivity`, `LksScoreCalculator`, `AuditLog`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `LksExportController.php`, `web.php`, `Tech Stack — LKS Santri Karya`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `Database Design — LKS Santri Karya`, `Architecture System — LKS Santri Karya`, `PeriodActivationService`, `LksPeriod`?**
  _High betweenness centrality (0.031) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _221 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.07112375533428165 - nodes in this community are weakly interconnected._
- **Should `TestCase` be split into smaller, more focused modules?**
  _Cohesion score 0.10227272727272728 - nodes in this community are weakly interconnected._
- **Should `Tech Stack — LKS Santri Karya` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._