# Graph Report - lks-santri-karya  (2026-11-09)

## Corpus Check
- 180 files · ~98,763 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1471 nodes · 3200 edges · 149 communities (75 shown, 74 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 25 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0f0eda19`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- LksCoreTest
- frontend/app.js
- package.json
- PeriodActivity
- TestCase
- Product Requirements Document
- User
- PeriodConfigurationController
- LksExportController
- escapeHtml
- task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- spin
- PeriodMenstruationDispensation
- multiselect
- Database Design — LKS Santri Karya
- openAdminFormModal
- LksPeriod
- Architecture System — LKS Santri Karya
- RecapController.php
- Illuminate\Support\Facades\DB
- PeriodConfigurationController.php
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- PasswordValidationRules
- PeriodController.php
- DocumentSignatory
- PeriodActivationService
- AuditLog
- SecurityTest
- LksScoreCalculator.php
- PeriodParticipantSnapshot
- ChecklistController.php
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Illuminate\Support\Str
- Tech Stack — LKS Santri Karya
- artisan
- applyDashboard
- Illuminate\Database\Eloquent\Relations\HasMany
- bootstrap/app.php
- AuthenticationTest
- scripts
- loadRecap
- Product
- PasswordResetTest.php
- FortifyServiceProvider.php
- composer.json
- require-dev
- Illuminate\Http\Request
- Illuminate\Foundation\Testing\RefreshDatabase
- require
- LksExportController.php
- Panduan Kerja Proyek LKS Santri Karya
- downloadHistoryStatement
- Controller
- SantriProfile
- Illuminate\Support\Facades\Schema
- Referensi Rumus LKS
- Role
- openBulkChecklist
- ⚡security.blade.php
- PasswordResetTest
- Illuminate\Http\Response
- config
- FortifyServiceProvider
- AppServiceProvider.php
- 2026-09-29T06-52-35Z__frontend-index-html.md
- UserFactory.php
- ProfileValidationRules
- ProfileUpdateTest
- GenericFailedPasswordResetLinkResponse
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- Logout.php
- psr-4
- laravel
- logging.php
- loadChecklistCorrectionContext
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- 0001_01_01_000001_create_cache_table.php
- 0001_01_01_000000_create_users_table.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- 2026_11_06_000000_add_menstruation_dispensations_to_lks.php
- 2026_11_06_010000_separate_magang_rules_and_add_employment_start_dates.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
- LksDocumentVerification
- ⚡two-factor-setup-modal.blade.php
- console.php
- 2026_10_07_010000_assign_leader_to_teams.php
- ⚡profile.blade.php
- header.blade.php
- sidebar.blade.php
- card.blade.php
- simple.blade.php
- split.blade.php
- ⚡appearance.blade.php
- ⚡delete-user-form.blade.php
- ⚡recovery-codes.blade.php
- Dashboard reporting
- PeriodMenstruationDispensation.php
- settings.php
- TwoFactorChallengeTest
- {closure#29}

## God Nodes (most connected - your core abstractions)
1. `LksCoreTest` - 89 edges
2. `User` - 86 edges
3. `LksPeriod` - 83 edges
4. `PeriodParticipantSnapshot` - 71 edges
5. `PeriodActivity` - 50 edges
6. `AuditLog` - 48 edges
7. `escapeHtml()` - 42 edges
8. `SantriProfile` - 41 edges
9. `LksScoreCalculator` - 38 edges
10. `TestCase` - 36 edges

## Surprising Connections (you probably didn't know these)
- `2. Prinsip arsitektur` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `7. Rumus perhitungan` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Database Design.md → backend/app/Services/LksScoreCalculator.php
- `Supporting Evidence` --references--> `todayIso()`  [INFERRED]
  .impeccable/critique/2026-09-29T06-52-35Z__frontend-index-html.md → frontend/app.js
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php
- `3. Paket dan kemampuan platform` --references--> `AuditLog`  [INFERRED]
  docs/Tech Stack.md → backend/app/Models/AuditLog.php

## Import Cycles
- None detected.

## Communities (149 total, 74 thin omitted)

### Community 0 - "LksCoreTest"
Cohesion: 0.08
Nodes (4): Department, LksActivity, Team, LksCoreTest

### Community 1 - "frontend/app.js"
Cohesion: 0.05
Nodes (47): activePeriodActivities, adminAnnualDefaultRange, adminAnnualRange, adminAnnualYears, adminFormDialog, adminFormDialogContent, adminListState, breadcrumb (+39 more)

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 4 - "TestCase"
Cohesion: 0.21
Nodes (4): TestCase, LksParticipantRankingTest, PeriodActivityRuleTest, Illuminate\Foundation\Testing\TestCase

### Community 5 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 6 - "User"
Cohesion: 0.09
Nodes (14): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, EmailVerificationTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 9 - "escapeHtml"
Cohesion: 0.18
Nodes (33): dashboardNotice(), dashboardSummaryData(), departmentReportGroups(), escapeHtml(), formatDate(), initials(), lksCategory(), lksCategoryLabel() (+25 more)

### Community 13 - "PeriodMenstruationDispensation"
Cohesion: 0.26
Nodes (3): {closure#1}(), MenstruationDispensationController, PeriodMenstruationDispensation

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (18): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+10 more)

### Community 16 - "openAdminFormModal"
Cohesion: 0.12
Nodes (26): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), bulkChecklistMonths(), dateInputValue(), employmentCategoryForDate(), formatDateTime() (+18 more)

### Community 17 - "LksPeriod"
Cohesion: 0.20
Nodes (4): ChecklistController, PeriodController, LksPeriod, Illuminate\Http\JsonResponse

### Community 18 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 20 - "RecapController.php"
Cohesion: 0.12
Nodes (5): {closure#3}(), {closure#5}(), {closure#6}(), {closure#9}(), RecapController

### Community 21 - "Illuminate\Support\Facades\DB"
Cohesion: 0.10
Nodes (7): {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 22 - "PeriodConfigurationController.php"
Cohesion: 0.11
Nodes (10): {closure#11}(), {closure#13}(), {closure#14}(), {closure#16}(), {closure#19}(), {closure#23}(), {closure#5}(), {closure#7}() (+2 more)

### Community 23 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 24 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (16): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+8 more)

### Community 25 - "PasswordValidationRules"
Cohesion: 0.24
Nodes (6): CreateNewUser, ResetUserPassword, PasswordValidationRules, Illuminate\Support\Facades\Validator, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 26 - "PeriodController.php"
Cohesion: 0.06
Nodes (7): {closure#10}(), {closure#18}(), ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "DocumentSignatory"
Cohesion: 0.32
Nodes (5): {closure#1}(), {closure#2}(), DocumentSignatoryController, DocumentSignatory, {closure#6}()

### Community 28 - "PeriodActivationService"
Cohesion: 0.27
Nodes (3): {closure#1}(), {closure#7}(), PeriodActivationService

### Community 29 - "AuditLog"
Cohesion: 0.12
Nodes (14): {closure#30}(), {closure#31}(), {closure#34}(), {closure#35}(), {closure#17}(), {closure#18}(), {closure#22}(), {closure#9}() (+6 more)

### Community 31 - "LksScoreCalculator.php"
Cohesion: 0.12
Nodes (6): {closure#11}(), {closure#13}(), LksParticipantRanking, {closure#6}(), {closure#9}(), Illuminate\Support\Collection

### Community 32 - "PeriodParticipantSnapshot"
Cohesion: 0.17
Nodes (5): PeriodParticipantSnapshot, {closure#2}(), {closure#8}(), LksScoreCalculator, EffectiveActivityTargetTest

### Community 33 - "ChecklistController.php"
Cohesion: 0.13
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}() (+4 more)

### Community 34 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 35 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.21
Nodes (4): PeriodHolidaySnapshot, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 36 - "Illuminate\Support\Str"
Cohesion: 0.14
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 37 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 39 - "applyDashboard"
Cohesion: 0.16
Nodes (19): applyDashboard(), applyViewerIdentity(), canOpenView(), canUsePersonalLks(), canView(), displayRole(), hasConfiguredRole(), openLocationView() (+11 more)

### Community 41 - "bootstrap/app.php"
Cohesion: 0.19
Nodes (10): EnsurePasswordChanged, EnsureUserIsActive, {closure#1}(), {closure#2}(), {closure#3}(), Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions (+2 more)

### Community 43 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 44 - "loadRecap"
Cohesion: 0.15
Nodes (30): accountPasswordField(), apiError(), apiFormError(), confirmAction(), downloadPdfPreview(), finishInitialBoot(), hidePageProgress(), loadAccount() (+22 more)

### Community 45 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 46 - "PasswordResetTest.php"
Cohesion: 0.14
Nodes (7): Illuminate\Auth\Events\Verified, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\Notification, Illuminate\Support\Facades\URL, Laravel\Fortify\Features

### Community 47 - "FortifyServiceProvider.php"
Cohesion: 0.17
Nodes (14): {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+6 more)

### Community 48 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 49 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 51 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.11
Nodes (7): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase, PHPUnit\Framework\TestCase

### Community 52 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 53 - "LksExportController.php"
Cohesion: 0.14
Nodes (7): {closure#3}(), {closure#7}(), BaconQrCode\Renderer\Image\SvgImageBackEnd, BaconQrCode\Renderer\ImageRenderer, BaconQrCode\Renderer\RendererStyle\RendererStyle, BaconQrCode\Writer, Barryvdh\DomPDF\Facade\Pdf

### Community 54 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 55 - "downloadHistoryStatement"
Cohesion: 0.50
Nodes (4): downloadAdminAnnualStatement(), downloadCsv(), downloadHistoryStatement(), visibleHistoryPeriods()

### Community 56 - "Controller"
Cohesion: 0.13
Nodes (6): Controller, AccountController, DashboardController, DocumentVerificationController, {closure#1}(), Illuminate\Validation\ValidationException

### Community 57 - "SantriProfile"
Cohesion: 0.23
Nodes (5): {closure#19}(), {closure#32}(), {closure#33}(), SantriProfile, SantriProfileCategoryTest

### Community 58 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.11
Nodes (7): {closure#1}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), Illuminate\Support\Facades\Schema, Illuminate\Support\Facades\Storage

### Community 59 - "Referensi Rumus LKS"
Cohesion: 0.20
Nodes (9): Aktivitas dan rumus matriks, Aturan tambahan sistem, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi (+1 more)

### Community 60 - "Role"
Cohesion: 0.20
Nodes (8): Role, 4.1 `users`, 4.3 `user_roles`, 4.4 `departments`, 4.5 `teams`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 61 - "openBulkChecklist"
Cohesion: 0.24
Nodes (10): activityRuleLabel(), activityScheduleLabel(), markBulkChecklistInvalidDates(), openBulkChecklist(), renderBulkChecklistCalendar(), selectedBulkDates(), setAllBulkChecklistDates(), showBulkChecklistMonth() (+2 more)

### Community 62 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 64 - "Illuminate\Http\Response"
Cohesion: 0.20
Nodes (4): PrototypeDashboardController, 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 65 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 66 - "FortifyServiceProvider"
Cohesion: 0.24
Nodes (3): AppServiceProvider, FortifyServiceProvider, Illuminate\Support\ServiceProvider

### Community 67 - "AppServiceProvider.php"
Cohesion: 0.24
Nodes (8): ActiveUserProvider, {closure#1}(), {closure#2}(), Carbon\CarbonImmutable, Illuminate\Auth\EloquentUserProvider, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Date, Illuminate\Validation\Rules\Password

### Community 69 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 70 - "UserFactory.php"
Cohesion: 0.31
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 71 - "ProfileValidationRules"
Cohesion: 0.32
Nodes (3): ProfileValidationRules, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 73 - "GenericFailedPasswordResetLinkResponse"
Cohesion: 0.40
Nodes (3): GenericFailedPasswordResetLinkResponse, Illuminate\Support\Facades\Password, Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse

### Community 74 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 75 - "2026_09_29_000001_create_lks_configuration_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 76 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 77 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 78 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 79 - "Logout.php"
Cohesion: 0.47
Nodes (4): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 80 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 81 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 82 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 83 - "loadChecklistCorrectionContext"
Cohesion: 0.33
Nodes (6): adminFormSnapshot(), hasUnsavedAdminFormChanges(), loadChecklistCorrectionContext(), markAdminFormPristine(), renderAdminDispensationManager(), requestAdminFormClose()

### Community 87 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 89 - "2026_11_06_000000_add_menstruation_dispensations_to_lks.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 90 - "2026_11_06_010000_separate_magang_rules_and_add_employment_start_dates.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 91 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 92 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses dan preview, Dokumen dan verifikasi QR, Ekspor PDF LKS, Rekomendasi otomatis

### Community 94 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 106 - "Dashboard reporting"
Cohesion: 0.50
Nodes (3): Authentication, Dashboard reporting, Typography and product identity

## Knowledge Gaps
- **251 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+246 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 563 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **74 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LksCoreTest`, `TestCase`, `OrganizationController.php`, `TwoFactorChallengeTest`, `PasswordValidationRules`, `PeriodActivationService`, `AuditLog`, `SecurityTest`, `PeriodParticipantSnapshot`, `ChecklistController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `AuthenticationTest`, `PasswordResetTest.php`, `FortifyServiceProvider.php`, `Illuminate\Http\Request`, `Illuminate\Foundation\Testing\RefreshDatabase`, `PasswordResetTest`, `UserFactory.php`, `ProfileValidationRules`, `ProfileUpdateTest`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `PeriodParticipantSnapshot` to `DashboardController.php`, `TestCase`, `Tech Stack — LKS Santri Karya`, `LksExportController`, `Database Design — LKS Santri Karya`, `LksPeriod`, `Architecture System — LKS Santri Karya`, `RecapController.php`, `LksExportController.php`, `Controller`, `PeriodController.php`, `PeriodActivationService`, `LksScoreCalculator.php`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Why does `PeriodParticipantSnapshot` connect `PeriodParticipantSnapshot` to `LksCoreTest`, `PeriodActivity`, `TestCase`, `User`, `LksExportController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PeriodMenstruationDispensation`, `LksPeriod`, `OrganizationController.php`, `{closure#29}`, `PeriodController.php`, `PeriodActivationService`, `AuditLog`, `LksScoreCalculator.php`, `ChecklistController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Database\Eloquent\Relations\HasMany`, `FortifyServiceProvider.php`, `Illuminate\Http\Request`, `LksExportController.php`, `Illuminate\Http\Response`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _251 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08391771856007481 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.05442176870748299 - nodes in this community are weakly interconnected._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06439393939393939 - nodes in this community are weakly interconnected._