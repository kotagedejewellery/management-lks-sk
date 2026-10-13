# Graph Report - lks-santri-karya  (2026-10-13)

## Corpus Check
- 162 files · ~70,244 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1207 nodes · 2426 edges · 126 communities (59 shown, 67 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3abc76f0`
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
- Illuminate\Http\JsonResponse
- LksPeriod
- PeriodParticipantSnapshot
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- AppServiceProvider.php
- Referensi Rumus LKS
- LksCoreTest
- AuthenticationTest
- Illuminate\Database\Schema\Blueprint
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Concerns\HasUuids
- web.php
- PeriodActivity
- loadRecap
- Ekspor PDF LKS
- LksScoreCalculator
- scripts
- Product
- AuditLog
- composer.json
- require-dev
- settings.php
- ChecklistRecordingService.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- require
- artisan
- Illuminate\Support\Facades\Schema
- ⚡security.blade.php
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- escapeHtml
- 0001_01_01_000000_create_users_table.php
- PasswordResetTest
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- PeriodActivationService
- applyViewerIdentity
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Facades\DB
- 2026_10_07_010000_assign_leader_to_teams.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
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
- Panduan Admin Menyiapkan LKS
- applyDashboard
- DocumentSignatoryController.php
- openBulkChecklist
- 4. Tabel master identitas dan organisasi
- Illuminate\Database\Migrations\Migration
- PeriodController.php
- EmailVerificationTest
- Team
- SecurityTest
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- 2026_10_07_030000_add_lks_work_calendar.php
- PasswordResetTest.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- LksCoreTest.php
- ChecklistController.php
- loadDashboard
- 2026-09-29T06-52-35Z__frontend-index-html.md

## God Nodes (most connected - your core abstractions)
1. `User` - 84 edges
2. `LksPeriod` - 68 edges
3. `PeriodParticipantSnapshot` - 46 edges
4. `LksCoreTest` - 45 edges
5. `AuditLog` - 38 edges
6. `escapeHtml()` - 33 edges
7. `PeriodActivity` - 31 edges
8. `LksScoreCalculator` - 29 edges
9. `TestCase` - 28 edges
10. `PeriodConfigurationController` - 27 edges

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

## Communities (126 total, 67 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.07
Nodes (35): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, breadcrumb, checklistSaveErrors (+27 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.05
Nodes (30): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, GenericFailedPasswordResetLinkResponse, {closure#1}(), {closure#10}(), {closure#11}() (+22 more)

### Community 2 - "TestCase"
Cohesion: 0.10
Nodes (10): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, EffectiveActivityTargetTest, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase (+2 more)

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.09
Nodes (14): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, ProfileUpdateTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "Illuminate\Http\JsonResponse"
Cohesion: 0.14
Nodes (5): Controller, AccountController, DashboardController, RecapController, Illuminate\Http\JsonResponse

### Community 9 - "PeriodParticipantSnapshot"
Cohesion: 0.17
Nodes (8): ChecklistController, PeriodParticipantSnapshot, {closure#1}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#8}(), Illuminate\Auth\Access\AuthorizationException

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

### Community 17 - "LksCoreTest"
Cohesion: 0.14
Nodes (4): {closure#6}(), {closure#9}(), LksActivity, LksCoreTest

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (15): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+7 more)

### Community 20 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 22 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.19
Nodes (5): Department, Role, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\HasMany

### Community 23 - "web.php"
Cohesion: 0.15
Nodes (5): PrototypeDashboardController, {closure#1}(), 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 25 - "loadRecap"
Cohesion: 0.18
Nodes (23): canUsePersonalLks(), initials(), loadRecap(), openRecapDetail(), participantStatus(), percentage(), renderActivityProgress(), renderHistoryActivities() (+15 more)

### Community 26 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses, Ekspor PDF LKS, Rekomendasi otomatis, Tanda tangan

### Community 27 - "LksScoreCalculator"
Cohesion: 0.06
Nodes (14): {closure#2}(), {closure#5}(), {closure#6}(), LksExportController, {closure#11}(), {closure#4}(), {closure#7}(), {closure#9}() (+6 more)

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "AuditLog"
Cohesion: 0.09
Nodes (17): {closure#18}(), {closure#19}(), {closure#22}(), {closure#11}(), {closure#12}(), {closure#14}(), {closure#15}(), {closure#16}() (+9 more)

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 35 - "ChecklistRecordingService.php"
Cohesion: 0.21
Nodes (8): ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface, Illuminate\Support\Carbon

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

### Community 45 - "escapeHtml"
Cohesion: 0.30
Nodes (12): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), escapeHtml(), masterActivityRuleFields(), openAdminFormModal(), paginationControls() (+4 more)

### Community 47 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 49 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 51 - "applyViewerIdentity"
Cohesion: 0.17
Nodes (23): apiError(), applyViewerIdentity(), canOpenView(), canView(), dashboardNotice(), displayRole(), exportPdf(), hasConfiguredRole() (+15 more)

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

### Community 60 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.14
Nodes (5): {closure#20}(), {closure#21}(), LksChecklist, SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 108 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 109 - "applyDashboard"
Cohesion: 0.17
Nodes (15): applyDashboard(), checkIcon(), checklistSaveErrorKey(), finishInitialBoot(), formatDate(), renderFullCheck(), renderPersonalSummaryLoading(), renderPersonalSummaryUnavailable() (+7 more)

### Community 110 - "DocumentSignatoryController.php"
Cohesion: 0.29
Nodes (5): {closure#1}(), {closure#2}(), DocumentSignatoryController, Illuminate\Support\Facades\Storage, Symfony\Component\HttpFoundation\BinaryFileResponse

### Community 111 - "openBulkChecklist"
Cohesion: 0.23
Nodes (12): activityRuleLabel(), activityScheduleLabel(), bulkChecklistMonths(), dateInputValue(), loadChecklistCorrectionContext(), localDateValue(), markAdminFormPristine(), openBulkChecklist() (+4 more)

### Community 112 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.22
Nodes (7): 4.1 `users`, 4.3 `user_roles`, 4.4 `departments`, 4.5 `teams`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 113 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.22
Nodes (3): {closure#1}(), {closure#2}(), Illuminate\Database\Migrations\Migration

### Community 115 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 120 - "PasswordResetTest.php"
Cohesion: 0.14
Nodes (5): TwoFactorChallengeTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification, Laravel\Fortify\Features

### Community 122 - "LksCoreTest.php"
Cohesion: 0.29
Nodes (4): DocumentSignatory, PeriodHolidaySnapshot, {closure#4}(), Illuminate\Http\UploadedFile

### Community 123 - "ChecklistController.php"
Cohesion: 0.33
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Validation\ValidationException

### Community 128 - "loadDashboard"
Cohesion: 0.26
Nodes (15): apiFormError(), confirmAction(), loadDashboard(), logout(), parseHolidayImportEntries(), persistChecklist(), requestLogout(), setButtonBusy() (+7 more)

### Community 130 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.33
Nodes (5): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction

## Knowledge Gaps
- **235 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+230 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 482 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **67 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `OrganizationController.php`, `ChecklistRecordingService.php`, `TestCase`, `PeriodParticipantSnapshot`, `PasswordResetTest`, `LksCoreTest`, `PeriodActivationService`, `AuthenticationTest`, `EmailVerificationTest`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `SecurityTest`, `PasswordResetTest.php`, `LksCoreTest.php`?**
  _High betweenness centrality (0.105) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `TestCase`, `User`, `Illuminate\Http\JsonResponse`, `PeriodParticipantSnapshot`, `ChecklistController.php`, `LksCoreTest`, `PeriodController.php`, `PeriodActivationService`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `web.php`, `LksCoreTest.php`, `LksScoreCalculator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `TestCase`, `Tech Stack — LKS Santri Karya`, `Illuminate\Http\JsonResponse`, `Database Design — LKS Santri Karya`, `Architecture System — LKS Santri Karya`, `PeriodActivationService`, `LksCoreTest.php`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _235 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.07301587301587302 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05429864253393665 - nodes in this community are weakly interconnected._
- **Should `TestCase` be split into smaller, more focused modules?**
  _Cohesion score 0.10344827586206896 - nodes in this community are weakly interconnected._