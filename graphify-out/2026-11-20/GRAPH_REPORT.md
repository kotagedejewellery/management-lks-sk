# Graph Report - lks-santri-karya  (2026-11-20)

## Corpus Check
- 183 files · ~104,213 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1543 nodes · 3360 edges · 144 communities (73 shown, 71 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 28 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `76fc7f68`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- LksCoreTest
- frontend/app.js
- package.json
- PeriodActivity
- LksExportController
- Product Requirements Document
- Illuminate\Http\Response
- Illuminate\Http\JsonResponse
- LksExportController.php
- escapeHtml
- task
- UniqueEmailAddress
- spin
- PeriodMenstruationDispensation
- multiselect
- Database Design — LKS Santri Karya
- openAdminFormModal
- PeriodController
- Architecture System — LKS Santri Karya
- OrganizationController.php
- LksPeriod
- Illuminate\Support\Facades\DB
- AuditLog
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- bootstrap/app.php
- PeriodController.php
- LksChecklist
- PeriodActivationService.php
- PeriodParticipantSnapshot
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Concerns\HasUuids
- RecapController.php
- EmailVerificationTest
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- Controller
- 4. Tabel master identitas dan organisasi
- Tech Stack — LKS Santri Karya
- artisan
- loadRecap
- settings.php
- PasswordResetTest.php
- Closure
- scripts
- showToast
- Product
- AuditLog.php
- FortifyServiceProvider.php
- composer.json
- require-dev
- Illuminate\Http\Request
- TestCase
- require
- AppServiceProvider.php
- Panduan Kerja Proyek LKS Santri Karya
- User
- DocumentSignatoryController.php
- 2026_09_29_000000_create_lks_identity_and_organization_tables.php
- Illuminate\Support\Facades\Schema
- Referensi Rumus LKS
- Illuminate\Database\Eloquent\Relations\HasMany
- LksDocumentVerification
- ⚡security.blade.php
- DocumentSignatory
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- config
- Logout.php
- ProfileUpdateTest
- 0001_01_01_000000_create_users_table.php
- applyDashboard
- LksCoreTest.php
- PasswordValidationRules
- Illuminate\Support\Str
- PasswordResetTest
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- 2026-09-29T06-52-35Z__frontend-index-html.md
- psr-4
- laravel
- logging.php
- RecapController
- 0001_01_01_000001_create_cache_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- PeriodMenstruationDispensation.php
- 2026_10_07_010000_assign_leader_to_teams.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- 2026_11_06_000000_add_menstruation_dispensations_to_lks.php
- 2026_11_06_010000_separate_magang_rules_and_add_employment_start_dates.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
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
- Dashboard reporting

## God Nodes (most connected - your core abstractions)
1. `LksCoreTest` - 99 edges
2. `User` - 95 edges
3. `LksPeriod` - 85 edges
4. `PeriodParticipantSnapshot` - 71 edges
5. `AuditLog` - 52 edges
6. `PeriodActivity` - 50 edges
7. `escapeHtml()` - 43 edges
8. `SantriProfile` - 42 edges
9. `LksScoreCalculator` - 39 edges
10. `TestCase` - 37 edges

## Surprising Connections (you probably didn't know these)
- `2. Prinsip arsitektur` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `4.4 Reporting` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `7. Rumus perhitungan` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Database Design.md → backend/app/Services/LksScoreCalculator.php
- `Supporting Evidence` --references--> `todayIso()`  [INFERRED]
  .impeccable/critique/2026-09-29T06-52-35Z__frontend-index-html.md → frontend/app.js
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php

## Import Cycles
- None detected.

## Communities (144 total, 71 thin omitted)

### Community 0 - "LksCoreTest"
Cohesion: 0.06
Nodes (9): {closure#19}(), {closure#32}(), {closure#33}(), Department, LksActivity, SantriProfile, Team, LksCoreTest (+1 more)

### Community 1 - "frontend/app.js"
Cohesion: 0.05
Nodes (55): activePeriodActivities, adminAnnualDefaultRange, adminAnnualRange, adminAnnualYears, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState (+47 more)

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 3 - "PeriodActivity"
Cohesion: 0.15
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), PeriodActivity

### Community 5 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 6 - "Illuminate\Http\Response"
Cohesion: 0.22
Nodes (4): PrototypeDashboardController, 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 8 - "LksExportController.php"
Cohesion: 0.15
Nodes (6): {closure#3}(), BaconQrCode\Renderer\Image\SvgImageBackEnd, BaconQrCode\Renderer\ImageRenderer, BaconQrCode\Renderer\RendererStyle\RendererStyle, BaconQrCode\Writer, Barryvdh\DomPDF\Facade\Pdf

### Community 9 - "escapeHtml"
Cohesion: 0.17
Nodes (33): dashboardSummaryData(), departmentReportGroups(), escapeHtml(), initials(), lksCategory(), lksCategoryLabel(), openRecapDetail(), participantStatus() (+25 more)

### Community 11 - "UniqueEmailAddress"
Cohesion: 0.31
Nodes (3): ProfileValidationRules, UniqueEmailAddress, Illuminate\Contracts\Validation\ValidationRule

### Community 13 - "PeriodMenstruationDispensation"
Cohesion: 0.22
Nodes (3): {closure#1}(), MenstruationDispensationController, PeriodMenstruationDispensation

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (18): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+10 more)

### Community 16 - "openAdminFormModal"
Cohesion: 0.18
Nodes (18): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), formatDateTime(), isMasterActivityActive(), isPeriodActivityActive(), loadAdminView() (+10 more)

### Community 18 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 19 - "OrganizationController.php"
Cohesion: 0.05
Nodes (5): {closure#30}(), {closure#31}(), {closure#34}(), {closure#35}(), Illuminate\Validation\Rule

### Community 20 - "LksPeriod"
Cohesion: 0.18
Nodes (5): LksPeriod, {closure#11}(), {closure#2}(), LksScoreCalculator, EffectiveActivityTargetTest

### Community 21 - "Illuminate\Support\Facades\DB"
Cohesion: 0.09
Nodes (7): {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Storage

### Community 22 - "AuditLog"
Cohesion: 0.09
Nodes (15): {closure#11}(), {closure#13}(), {closure#14}(), {closure#16}(), {closure#17}(), {closure#18}(), {closure#19}(), {closure#22}() (+7 more)

### Community 23 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 24 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.14
Nodes (14): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#1}(), {closure#2}() (+6 more)

### Community 25 - "bootstrap/app.php"
Cohesion: 0.17
Nodes (10): EnsurePasswordChanged, EnsureUserIsActive, {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware (+2 more)

### Community 26 - "PeriodController.php"
Cohesion: 0.05
Nodes (7): {closure#10}(), {closure#18}(), ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "LksChecklist"
Cohesion: 0.10
Nodes (10): LksChecklist, {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}(), Carbon\CarbonInterface (+2 more)

### Community 28 - "PeriodActivationService.php"
Cohesion: 0.13
Nodes (8): {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#7}(), {closure#8}(), {closure#9}(), PeriodActivationService

### Community 29 - "PeriodParticipantSnapshot"
Cohesion: 0.24
Nodes (4): ChecklistController, PeriodParticipantSnapshot, ChecklistRecordingService, {closure#1}()

### Community 31 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.26
Nodes (4): PeriodHolidaySnapshot, Role, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model

### Community 32 - "RecapController.php"
Cohesion: 0.07
Nodes (11): {closure#13}(), {closure#16}(), {closure#18}(), {closure#20}(), {closure#3}(), {closure#5}(), {closure#8}(), LksParticipantRanking (+3 more)

### Community 33 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 34 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 35 - "Controller"
Cohesion: 0.08
Nodes (6): Controller, AccountController, DashboardController, DocumentVerificationController, {closure#1}(), Illuminate\Validation\ValidationException

### Community 36 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.17
Nodes (8): 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Casts\Attribute, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 37 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 39 - "loadRecap"
Cohesion: 0.13
Nodes (30): accountPasswordField(), apiError(), applyViewerIdentity(), canOpenView(), canUsePersonalLks(), canView(), dashboardNotice(), displayRole() (+22 more)

### Community 41 - "PasswordResetTest.php"
Cohesion: 0.12
Nodes (5): TwoFactorChallengeTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification, Laravel\Fortify\Features

### Community 42 - "Closure"
Cohesion: 0.29
Nodes (3): ThrottlePasswordResetRequests, Closure, Illuminate\Support\Facades\RateLimiter

### Community 43 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 44 - "showToast"
Cohesion: 0.14
Nodes (26): apiFormError(), confirmAction(), csvPercentageNumber(), downloadAdminAnnualStatement(), downloadCsv(), downloadHistoryStatement(), downloadPdfPreview(), loadDashboard() (+18 more)

### Community 45 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 47 - "FortifyServiceProvider.php"
Cohesion: 0.11
Nodes (17): GenericFailedPasswordResetLinkResponse, PeriodParticipantSnapshotPolicy, {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}() (+9 more)

### Community 48 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 49 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 50 - "Illuminate\Http\Request"
Cohesion: 0.23
Nodes (3): {closure#29}(), OrganizationController, Illuminate\Http\Request

### Community 51 - "TestCase"
Cohesion: 0.08
Nodes (12): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, ExampleTest, IndividualPdfTemplateTest, PeriodActivityRuleTest (+4 more)

### Community 52 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 53 - "AppServiceProvider.php"
Cohesion: 0.12
Nodes (10): ActiveUserProvider, AppServiceProvider, {closure#1}(), {closure#2}(), FortifyServiceProvider, Carbon\CarbonImmutable, Illuminate\Auth\EloquentUserProvider, Illuminate\Support\Facades\Date (+2 more)

### Community 54 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.20
Nodes (9): Detail guardrail implementasi, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Skill dan alat tambahan, Urutan kerja wajib (+1 more)

### Community 55 - "User"
Cohesion: 0.07
Nodes (14): User, LksPeriodPolicy, AuthenticationTest, SecurityTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 57 - "2026_09_29_000000_create_lks_identity_and_organization_tables.php"
Cohesion: 0.25
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}()

### Community 58 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.14
Nodes (5): {closure#1}(), {closure#1}(), {closure#2}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 59 - "Referensi Rumus LKS"
Cohesion: 0.20
Nodes (9): Aktivitas dan rumus matriks, Aturan tambahan sistem, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi (+1 more)

### Community 62 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 63 - "DocumentSignatory"
Cohesion: 0.38
Nodes (4): {closure#1}(), {closure#7}(), DocumentSignatory, {closure#6}()

### Community 65 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 66 - "Logout.php"
Cohesion: 0.47
Nodes (4): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 68 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 69 - "applyDashboard"
Cohesion: 0.15
Nodes (20): activityRuleLabel(), activityScheduleLabel(), applyDashboard(), bulkChecklistMonths(), dateInputValue(), employmentCategoryForDate(), formatDate(), loadChecklistCorrectionContext() (+12 more)

### Community 70 - "LksCoreTest.php"
Cohesion: 0.18
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 71 - "PasswordValidationRules"
Cohesion: 0.24
Nodes (6): CreateNewUser, ResetUserPassword, PasswordValidationRules, Illuminate\Support\Facades\Validator, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 72 - "Illuminate\Support\Str"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

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

### Community 79 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 80 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 81 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 82 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

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
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 596 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **71 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LksCoreTest`, `UniqueEmailAddress`, `OrganizationController.php`, `AuditLog`, `LksChecklist`, `PeriodActivationService.php`, `PeriodParticipantSnapshot`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `EmailVerificationTest`, `Controller`, `4. Tabel master identitas dan organisasi`, `PasswordResetTest.php`, `Closure`, `FortifyServiceProvider.php`, `Illuminate\Http\Request`, `TestCase`, `ProfileUpdateTest`, `LksCoreTest.php`, `PasswordValidationRules`, `PasswordResetTest`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `LksCoreTest`, `PeriodActivity`, `LksExportController`, `Illuminate\Http\Response`, `Illuminate\Http\JsonResponse`, `LksExportController.php`, `PeriodMenstruationDispensation`, `PeriodController`, `AuditLog`, `PeriodController.php`, `LksChecklist`, `PeriodActivationService.php`, `PeriodParticipantSnapshot`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `RecapController.php`, `Controller`, `TestCase`, `User`, `Illuminate\Database\Eloquent\Relations\HasMany`, `LksCoreTest.php`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksPeriod` to `RecapController.php`, `Controller`, `LksExportController`, `Tech Stack — LKS Santri Karya`, `LksCoreTest.php`, `LksExportController.php`, `Database Design — LKS Santri Karya`, `PeriodController`, `Architecture System — LKS Santri Karya`, `RecapController`, `PeriodController.php`, `LksChecklist`, `PeriodActivationService.php`?**
  _High betweenness centrality (0.044) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _251 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.06446776611694154 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.04949874686716792 - nodes in this community are weakly interconnected._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06439393939393939 - nodes in this community are weakly interconnected._