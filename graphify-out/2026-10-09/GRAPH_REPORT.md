# Graph Report - lks-santri-karya  (2026-10-09)

## Corpus Check
- 161 files · ~65,968 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1152 nodes · 2298 edges · 121 communities (56 shown, 65 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `13473ffd`
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
- PeriodActivationService.php
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- AppServiceProvider.php
- Referensi Rumus LKS
- LksCoreTest
- LksPeriod
- Illuminate\Database\Schema\Blueprint
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- PeriodActivity
- UserFactory.php
- 4. Tabel master identitas dan organisasi
- escapeHtml
- Ekspor PDF LKS
- RecapController.php
- scripts
- Product
- AuditLog
- composer.json
- require-dev
- settings.php
- OrganizationController.php
- PasswordResetTest
- 2026_09_29_000001_create_lks_configuration_tables.php
- require
- artisan
- Illuminate\Support\Facades\Schema
- ⚡security.blade.php
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- LksScoreCalculator
- 0001_01_01_000000_create_users_table.php
- LksCoreTest.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- applyDashboard
- applyViewerIdentity
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Facades\DB
- 2026_10_07_010000_assign_leader_to_teams.php
- openAdminFormModal
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
- PeriodParticipantSnapshot
- LksScoreCalculator.php
- SecurityTest
- PeriodActivationService
- Illuminate\Database\Migrations\Migration
- ChecklistRecordingService.php
- Team
- PeriodController.php
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- 2026_10_07_030000_add_lks_work_calendar.php
- PasswordResetTest.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- AuthenticationTest

## God Nodes (most connected - your core abstractions)
1. `User` - 84 edges
2. `LksPeriod` - 66 edges
3. `PeriodParticipantSnapshot` - 46 edges
4. `AuditLog` - 37 edges
5. `LksCoreTest` - 35 edges
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

## Communities (121 total, 65 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.08
Nodes (41): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, apiFormError(), breadcrumb (+33 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.07
Nodes (26): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, GenericFailedPasswordResetLinkResponse, {closure#1}(), {closure#10}(), {closure#11}() (+18 more)

### Community 2 - "TestCase"
Cohesion: 0.10
Nodes (10): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, ExampleTest, PeriodActivityRuleTest, Illuminate\Foundation\Testing\RefreshDatabase (+2 more)

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.09
Nodes (14): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, EmailVerificationTest, ProfileUpdateTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.12
Nodes (9): {closure#6}(), Department, LksActivity, PeriodHolidaySnapshot, Role, 4.5 `teams`, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model (+1 more)

### Community 8 - "Illuminate\Http\Request"
Cohesion: 0.06
Nodes (19): Controller, AccountController, DashboardController, {closure#1}(), {closure#2}(), DocumentSignatoryController, OrganizationController, PeriodConfigurationController (+11 more)

### Community 9 - "PeriodActivationService.php"
Cohesion: 0.22
Nodes (6): {closure#1}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#8}(), Illuminate\Auth\Access\AuthorizationException

### Community 11 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (17): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+9 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "AppServiceProvider.php"
Cohesion: 0.06
Nodes (25): ActiveUserProvider, EnsurePasswordChanged, EnsureUserIsActive, Logout, AppServiceProvider, {closure#1}(), {closure#2}(), FortifyServiceProvider (+17 more)

### Community 16 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 18 - "LksPeriod"
Cohesion: 0.33
Nodes (3): {closure#2}(), LksExportController, LksPeriod

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (15): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+7 more)

### Community 20 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 21 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.23
Nodes (3): LksChecklist, SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 22 - "PeriodActivity"
Cohesion: 0.18
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), PeriodActivity, Illuminate\Validation\ValidationException

### Community 23 - "UserFactory.php"
Cohesion: 0.31
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 24 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.20
Nodes (7): 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 25 - "escapeHtml"
Cohesion: 0.25
Nodes (20): escapeHtml(), filterRecapRows(), initials(), openRecapDetail(), participantStatus(), percentage(), renderActivityProgress(), renderHistoryActivities() (+12 more)

### Community 26 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses, Ekspor PDF LKS, Rekomendasi otomatis, Tanda tangan

### Community 27 - "RecapController.php"
Cohesion: 0.15
Nodes (5): {closure#11}(), {closure#4}(), {closure#7}(), {closure#9}(), Illuminate\Support\Collection

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "AuditLog"
Cohesion: 0.09
Nodes (14): {closure#10}(), {closure#10}(), {closure#12}(), {closure#13}(), {closure#14}(), {closure#15}(), {closure#18}(), {closure#19}() (+6 more)

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 34 - "OrganizationController.php"
Cohesion: 0.14
Nodes (4): {closure#11}(), {closure#12}(), {closure#13}(), {closure#14}()

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

### Community 45 - "LksScoreCalculator"
Cohesion: 0.27
Nodes (3): {closure#7}(), LksScoreCalculator, EffectiveActivityTargetTest

### Community 47 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 48 - "LksCoreTest.php"
Cohesion: 0.18
Nodes (5): {closure#5}(), {closure#6}(), Barryvdh\DomPDF\Facade\Pdf, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage

### Community 49 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 50 - "applyDashboard"
Cohesion: 0.10
Nodes (24): activityRuleLabel(), applyDashboard(), bulkChecklistMonths(), canUsePersonalLks(), checkIcon(), dateInputValue(), localDateValue(), openChecklistCorrection() (+16 more)

### Community 51 - "applyViewerIdentity"
Cohesion: 0.16
Nodes (27): apiError(), applyViewerIdentity(), canOpenView(), canView(), dashboardNotice(), displayRole(), exportPdf(), finishInitialBoot() (+19 more)

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

### Community 60 - "openAdminFormModal"
Cohesion: 0.15
Nodes (18): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), formatDate(), loadChecklistCorrectionContext(), markAdminFormPristine(), masterActivityRuleFields() (+10 more)

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 110 - "SecurityTest"
Cohesion: 0.17
Nodes (3): SecurityTest, Illuminate\Support\Facades\Hash, Livewire\Livewire

### Community 113 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.22
Nodes (3): {closure#1}(), {closure#2}(), Illuminate\Database\Migrations\Migration

### Community 114 - "ChecklistRecordingService.php"
Cohesion: 0.25
Nodes (8): ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface, Illuminate\Support\Carbon

### Community 120 - "PasswordResetTest.php"
Cohesion: 0.11
Nodes (8): TwoFactorChallengeTest, Illuminate\Auth\Events\Verified, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\Notification, Illuminate\Support\Facades\URL, Laravel\Fortify\Features

## Knowledge Gaps
- **220 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+215 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 456 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **65 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `OrganizationController.php`, `TestCase`, `PasswordResetTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\Request`, `PeriodActivationService.php`, `SecurityTest`, `PeriodActivationService`, `LksCoreTest.php`, `LksCoreTest`, `ChecklistRecordingService.php`, `UserFactory.php`, `4. Tabel master identitas dan organisasi`, `AuthenticationTest`, `PasswordResetTest.php`?**
  _High betweenness centrality (0.104) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `User`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\Request`, `PeriodActivationService.php`, `PeriodParticipantSnapshot`, `LksScoreCalculator`, `LksScoreCalculator.php`, `PeriodActivationService`, `LksCoreTest.php`, `LksCoreTest`, `PeriodController.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PeriodActivity`, `RecapController.php`, `AuditLog`?**
  _High betweenness centrality (0.053) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `Tech Stack — LKS Santri Karya`, `Illuminate\Http\Request`, `Database Design — LKS Santri Karya`, `LksScoreCalculator.php`, `Architecture System — LKS Santri Karya`, `PeriodActivationService`, `LksCoreTest.php`, `LksPeriod`, `RecapController.php`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _220 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.08013937282229965 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.06852497096399536 - nodes in this community are weakly interconnected._
- **Should `TestCase` be split into smaller, more focused modules?**
  _Cohesion score 0.10344827586206896 - nodes in this community are weakly interconnected._