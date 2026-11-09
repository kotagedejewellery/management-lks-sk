# Graph Report - lks-santri-karya  (2026-11-09)

## Corpus Check
- 180 files · ~98,378 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1467 nodes · 3194 edges · 134 communities (66 shown, 68 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 25 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b96f6584`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- LksCoreTest
- frontend/app.js
- package.json
- PeriodActivity
- PeriodParticipantSnapshot
- Product Requirements Document
- User
- Illuminate\Http\Request
- LksExportController
- escapeHtml
- task
- Illuminate\Database\Eloquent\Relations\BelongsTo
- spin
- Illuminate\Http\Response
- multiselect
- Database Design — LKS Santri Karya
- persistChecklist
- Illuminate\Http\JsonResponse
- Architecture System — LKS Santri Karya
- OrganizationController.php
- LksExportController.php
- Illuminate\Support\Facades\DB
- AuditLog
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- CreateNewUser.php
- PeriodController.php
- DocumentSignatoryController.php
- LksPeriod
- 2026_09_29_000000_create_lks_identity_and_organization_tables.php
- SecurityTest
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- LksScoreCalculator
- ChecklistController.php
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- Illuminate\Database\Eloquent\Concerns\HasUuids
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- Tech Stack — LKS Santri Karya
- artisan
- applyViewerIdentity
- Illuminate\Database\Eloquent\Relations\HasMany
- AppServiceProvider.php
- AuthenticationTest
- scripts
- loadRecap
- Product
- 2026_10_15_000002_remove_legacy_signature_uploads.php
- FortifyServiceProvider.php
- composer.json
- require-dev
- OrganizationController
- TestCase
- require
- PeriodHolidaySnapshot
- Panduan Kerja Proyek LKS Santri Karya
- downloadHistoryStatement
- Controller
- Illuminate\Support\Facades\Schema
- Referensi Rumus LKS
- 4. Tabel master identitas dan organisasi
- applyDashboard
- ⚡security.blade.php
- PasswordResetTest.php
- config
- 2026-09-29T06-52-35Z__frontend-index-html.md
- UserFactory.php
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- 0001_01_01_000000_create_users_table.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- 2026_11_06_000000_add_menstruation_dispensations_to_lks.php
- 2026_11_06_010000_separate_magang_rules_and_add_employment_start_dates.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
- LksDocumentVerification
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
- PeriodMenstruationDispensation.php

## God Nodes (most connected - your core abstractions)
1. `LksCoreTest` - 88 edges
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

## Communities (134 total, 68 thin omitted)

### Community 0 - "LksCoreTest"
Cohesion: 0.07
Nodes (9): {closure#19}(), {closure#32}(), {closure#33}(), Department, LksActivity, SantriProfile, Team, LksCoreTest (+1 more)

### Community 1 - "frontend/app.js"
Cohesion: 0.06
Nodes (53): activePeriodActivities, activityAudience(), adminAnnualYears, adminFormDialog, adminFormDialogContent, adminListState, adminOptions(), adminPanel() (+45 more)

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 4 - "PeriodParticipantSnapshot"
Cohesion: 0.21
Nodes (3): ChecklistController, PeriodParticipantSnapshot, PeriodActivityRuleTest

### Community 5 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 6 - "User"
Cohesion: 0.08
Nodes (15): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, EmailVerificationTest, ProfileUpdateTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden (+7 more)

### Community 9 - "escapeHtml"
Cohesion: 0.18
Nodes (32): canUsePersonalLks(), dashboardSummaryData(), departmentReportGroups(), escapeHtml(), initials(), lksCategory(), lksCategoryLabel(), openRecapDetail() (+24 more)

### Community 11 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (3): LksChecklist, {closure#6}(), Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 13 - "Illuminate\Http\Response"
Cohesion: 0.27
Nodes (4): {closure#1}(), MenstruationDispensationController, PeriodMenstruationDispensation, Illuminate\Http\Response

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (18): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+10 more)

### Community 16 - "persistChecklist"
Cohesion: 0.24
Nodes (11): checkIcon(), checklistSaveErrorKey(), persistChecklist(), recordingUnavailableLabel(), renderFullCheck(), renderQuickCheck(), setChecklistSaveError(), setChecklistSaving() (+3 more)

### Community 17 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (4): PasswordValidationRules, AccountController, PeriodController, Illuminate\Http\JsonResponse

### Community 18 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 19 - "OrganizationController.php"
Cohesion: 0.05
Nodes (9): {closure#30}(), {closure#31}(), {closure#34}(), {closure#35}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str (+1 more)

### Community 20 - "LksExportController.php"
Cohesion: 0.06
Nodes (18): {closure#3}(), {closure#7}(), {closure#11}(), {closure#13}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#9}() (+10 more)

### Community 21 - "Illuminate\Support\Facades\DB"
Cohesion: 0.10
Nodes (7): {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 22 - "AuditLog"
Cohesion: 0.09
Nodes (15): {closure#11}(), {closure#13}(), {closure#14}(), {closure#16}(), {closure#17}(), {closure#18}(), {closure#19}(), {closure#22}() (+7 more)

### Community 23 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 24 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.14
Nodes (14): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#1}(), {closure#2}() (+6 more)

### Community 25 - "CreateNewUser.php"
Cohesion: 0.18
Nodes (7): CreateNewUser, ResetUserPassword, ProfileValidationRules, Illuminate\Support\Facades\Validator, Illuminate\Validation\Rule, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 26 - "PeriodController.php"
Cohesion: 0.06
Nodes (7): {closure#10}(), {closure#17}(), ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "DocumentSignatoryController.php"
Cohesion: 0.39
Nodes (3): {closure#1}(), {closure#2}(), DocumentSignatoryController

### Community 28 - "LksPeriod"
Cohesion: 0.11
Nodes (10): LksPeriod, {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#7}(), {closure#8}(), {closure#9}() (+2 more)

### Community 29 - "2026_09_29_000000_create_lks_identity_and_organization_tables.php"
Cohesion: 0.25
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}()

### Community 32 - "LksScoreCalculator"
Cohesion: 0.15
Nodes (4): {closure#2}(), {closure#8}(), LksScoreCalculator, 4.6 `santri_profiles`

### Community 33 - "ChecklistController.php"
Cohesion: 0.12
Nodes (13): {closure#1}(), {closure#2}(), {closure#3}(), ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}() (+5 more)

### Community 34 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 35 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.23
Nodes (6): DocumentSignatory, Role, {closure#6}(), Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 37 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 39 - "applyViewerIdentity"
Cohesion: 0.20
Nodes (17): applyViewerIdentity(), canOpenView(), canView(), dashboardNotice(), displayRole(), hasConfiguredRole(), loadHistory(), loadHistoryRecap() (+9 more)

### Community 41 - "AppServiceProvider.php"
Cohesion: 0.06
Nodes (26): ActiveUserProvider, EnsurePasswordChanged, EnsureUserIsActive, Logout, AppServiceProvider, {closure#1}(), {closure#2}(), FortifyServiceProvider (+18 more)

### Community 43 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 44 - "loadRecap"
Cohesion: 0.15
Nodes (28): accountPasswordField(), apiError(), apiFormError(), confirmAction(), downloadPdfPreview(), finishInitialBoot(), hidePageProgress(), loadAccount() (+20 more)

### Community 45 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 47 - "FortifyServiceProvider.php"
Cohesion: 0.13
Nodes (17): GenericFailedPasswordResetLinkResponse, {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+9 more)

### Community 48 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 49 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 51 - "TestCase"
Cohesion: 0.07
Nodes (16): PasswordConfirmationTest, RegistrationTest, TwoFactorChallengeTest, DashboardTest, ExampleTest, TestCase, ExampleTest, IndividualPdfTemplateTest (+8 more)

### Community 52 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 54 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 55 - "downloadHistoryStatement"
Cohesion: 0.67
Nodes (3): downloadAdminAnnualStatement(), downloadCsv(), downloadHistoryStatement()

### Community 56 - "Controller"
Cohesion: 0.07
Nodes (8): Controller, DashboardController, DocumentVerificationController, PrototypeDashboardController, {closure#1}(), 1. Keputusan utama, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Route

### Community 58 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.14
Nodes (5): {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 59 - "Referensi Rumus LKS"
Cohesion: 0.20
Nodes (9): Aktivitas dan rumus matriks, Aturan tambahan sistem, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi (+1 more)

### Community 60 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.25
Nodes (6): 4.1 `users`, 4.3 `user_roles`, 4.4 `departments`, 4.5 `teams`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 61 - "applyDashboard"
Cohesion: 0.10
Nodes (29): activityRuleLabel(), activityScheduleLabel(), adminFormSnapshot(), applyDashboard(), bulkChecklistMonths(), dateInputValue(), formatDate(), hasUnsavedAdminFormChanges() (+21 more)

### Community 62 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 63 - "PasswordResetTest.php"
Cohesion: 0.14
Nodes (3): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Support\Facades\Notification

### Community 65 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 69 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 70 - "UserFactory.php"
Cohesion: 0.27
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

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

### Community 80 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 81 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 82 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

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
- **249 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+244 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 560 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **68 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LksCoreTest`, `ChecklistController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `PeriodActivity`, `UserFactory.php`, `AuthenticationTest`, `FortifyServiceProvider.php`, `OrganizationController`, `OrganizationController.php`, `TestCase`, `CreateNewUser.php`, `LksPeriod`, `SecurityTest`, `PasswordResetTest.php`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `PeriodActivity`, `Tech Stack — LKS Santri Karya`, `LksExportController`, `Database Design — LKS Santri Karya`, `Illuminate\Http\JsonResponse`, `Architecture System — LKS Santri Karya`, `LksExportController.php`, `Controller`, `PeriodController.php`, `LksPeriod`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `LksScoreCalculator`, `ChecklistController.php`, `LksCoreTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `PeriodActivity`, `User`, `Illuminate\Http\Request`, `LksExportController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Http\Response`, `Illuminate\Http\JsonResponse`, `TestCase`, `LksExportController.php`, `AuditLog`, `Controller`, `PeriodController.php`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _249 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.07132188200149366 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.05656565656565657 - nodes in this community are weakly interconnected._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06439393939393939 - nodes in this community are weakly interconnected._