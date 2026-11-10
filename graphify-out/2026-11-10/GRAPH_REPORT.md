# Graph Report - lks-santri-karya  (2026-11-10)

## Corpus Check
- 180 files · ~101,339 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1490 nodes · 3256 edges · 133 communities (67 shown, 66 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 26 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c8addadb`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- LksCoreTest
- frontend/app.js
- package.json
- PeriodActivity
- LksExportController
- Product Requirements Document
- User
- PeriodConfigurationController
- LksExportController.php
- percentage
- task
- Illuminate\Database\Eloquent\Concerns\HasUuids
- spin
- PeriodParticipantSnapshot
- multiselect
- Database Design — LKS Santri Karya
- escapeHtml
- Illuminate\Http\JsonResponse
- Architecture System — LKS Santri Karya
- OrganizationController.php
- ProfileUpdateTest
- Illuminate\Support\Facades\DB
- AuditLog
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- PeriodActivationService.php
- PeriodController.php
- LksPeriod
- EmailVerificationTest
- openBulkChecklist
- ChecklistController.php
- LksScoreCalculator.php
- RecapController.php
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- DocumentSignatory
- Illuminate\Support\Str
- Tech Stack — LKS Santri Karya
- artisan
- applyViewerIdentity
- ChecklistController
- AppServiceProvider.php
- PasswordResetTest.php
- scripts
- loadRecap
- Product
- TwoFactorChallengeTest
- FortifyServiceProvider.php
- composer.json
- require-dev
- Illuminate\Http\Request
- TestCase
- require
- Panduan Kerja Proyek LKS Santri Karya
- settings.php
- Illuminate\Support\Facades\Schema
- Referensi Rumus LKS
- 0001_01_01_000001_create_cache_table.php
- 2026_10_07_010000_assign_leader_to_teams.php
- ⚡security.blade.php
- Controller
- config
- ChecklistRecordingService.php
- applyDashboard
- UserFactory.php
- CreateNewUser.php
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- persistChecklist
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- 0001_01_01_000000_create_users_table.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- 2026_11_06_000000_add_menstruation_dispensations_to_lks.php
- 2026_11_06_010000_separate_magang_rules_and_add_employment_start_dates.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
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
1. `LksCoreTest` - 93 edges
2. `User` - 86 edges
3. `LksPeriod` - 84 edges
4. `PeriodParticipantSnapshot` - 71 edges
5. `PeriodActivity` - 50 edges
6. `AuditLog` - 48 edges
7. `escapeHtml()` - 44 edges
8. `SantriProfile` - 41 edges
9. `LksScoreCalculator` - 39 edges
10. `TestCase` - 36 edges

## Surprising Connections (you probably didn't know these)
- `2. Prinsip arsitektur` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `4.4 Reporting` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `7. Rumus perhitungan` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Database Design.md → backend/app/Services/LksScoreCalculator.php
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php
- `3. Paket dan kemampuan platform` --references--> `AuditLog`  [INFERRED]
  docs/Tech Stack.md → backend/app/Models/AuditLog.php

## Import Cycles
- None detected.

## Communities (133 total, 66 thin omitted)

### Community 0 - "LksCoreTest"
Cohesion: 0.07
Nodes (6): Department, LksActivity, SantriProfile, Team, LksCoreTest, SantriProfileCategoryTest

### Community 1 - "frontend/app.js"
Cohesion: 0.05
Nodes (48): accountPasswordField(), activePeriodActivities, adminAnnualDefaultRange, adminAnnualRange, adminAnnualYears, adminFormDialog, adminFormDialogContent, adminFormSnapshot() (+40 more)

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 6 - "User"
Cohesion: 0.08
Nodes (14): User, LksPeriodPolicy, AuthenticationTest, SecurityTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 8 - "LksExportController.php"
Cohesion: 0.11
Nodes (8): {closure#3}(), {closure#7}(), LksParticipantRanking, BaconQrCode\Renderer\Image\SvgImageBackEnd, BaconQrCode\Renderer\ImageRenderer, BaconQrCode\Renderer\RendererStyle\RendererStyle, BaconQrCode\Writer, Barryvdh\DomPDF\Facade\Pdf

### Community 9 - "percentage"
Cohesion: 0.12
Nodes (37): canUsePersonalLks(), dashboardSummaryData(), departmentFollowUpActivityNames(), departmentReportGroups(), initials(), lksCategory(), lksCategoryLabel(), loadAdminAnnualHistory() (+29 more)

### Community 11 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.05
Nodes (20): LksChecklist, LksDocumentVerification, PeriodHolidaySnapshot, {closure#1}(), Role, {closure#6}(), {closure#7}(), 4.1 `users` (+12 more)

### Community 13 - "PeriodParticipantSnapshot"
Cohesion: 0.21
Nodes (4): {closure#1}(), MenstruationDispensationController, PeriodMenstruationDispensation, PeriodParticipantSnapshot

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (18): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+10 more)

### Community 16 - "escapeHtml"
Cohesion: 0.18
Nodes (20): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), escapeHtml(), formatDateTime(), isMasterActivityActive(), isPeriodActivityActive() (+12 more)

### Community 17 - "Illuminate\Http\JsonResponse"
Cohesion: 0.14
Nodes (5): PasswordValidationRules, AccountController, PeriodController, RecapController, Illuminate\Http\JsonResponse

### Community 18 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 19 - "OrganizationController.php"
Cohesion: 0.05
Nodes (7): {closure#19}(), {closure#30}(), {closure#31}(), {closure#32}(), {closure#33}(), {closure#34}(), {closure#35}()

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
Cohesion: 0.13
Nodes (16): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+8 more)

### Community 25 - "PeriodActivationService.php"
Cohesion: 0.12
Nodes (9): {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#6}(), {closure#7}(), {closure#8}(), {closure#9}() (+1 more)

### Community 26 - "PeriodController.php"
Cohesion: 0.05
Nodes (7): {closure#10}(), {closure#18}(), ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "LksPeriod"
Cohesion: 0.19
Nodes (4): LksPeriod, {closure#2}(), LksScoreCalculator, EffectiveActivityTargetTest

### Community 28 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 29 - "openBulkChecklist"
Cohesion: 0.24
Nodes (10): activityRuleLabel(), activityScheduleLabel(), markBulkChecklistInvalidDates(), openBulkChecklist(), renderBulkChecklistCalendar(), selectedBulkDates(), setAllBulkChecklistDates(), showBulkChecklistMonth() (+2 more)

### Community 30 - "ChecklistController.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Validation\ValidationException

### Community 32 - "RecapController.php"
Cohesion: 0.13
Nodes (8): {closure#11}(), {closure#13}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#9}(), {closure#12}(), Illuminate\Support\Collection

### Community 34 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 35 - "DocumentSignatory"
Cohesion: 0.40
Nodes (4): {closure#1}(), {closure#2}(), DocumentSignatoryController, DocumentSignatory

### Community 36 - "Illuminate\Support\Str"
Cohesion: 0.14
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 37 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 39 - "applyViewerIdentity"
Cohesion: 0.18
Nodes (22): apiError(), applyViewerIdentity(), canOpenView(), canView(), dashboardNotice(), displayRole(), hasConfiguredRole(), loadAccount() (+14 more)

### Community 41 - "AppServiceProvider.php"
Cohesion: 0.06
Nodes (26): ActiveUserProvider, EnsurePasswordChanged, EnsureUserIsActive, Logout, AppServiceProvider, {closure#1}(), {closure#2}(), FortifyServiceProvider (+18 more)

### Community 42 - "PasswordResetTest.php"
Cohesion: 0.11
Nodes (5): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification, Laravel\Fortify\Features

### Community 43 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 44 - "loadRecap"
Cohesion: 0.24
Nodes (18): apiFormError(), confirmAction(), downloadPdfPreview(), finishInitialBoot(), hidePageProgress(), loadAdminView(), loadDashboard(), loadRecap() (+10 more)

### Community 45 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 47 - "FortifyServiceProvider.php"
Cohesion: 0.11
Nodes (18): GenericFailedPasswordResetLinkResponse, PeriodParticipantSnapshotPolicy, {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}() (+10 more)

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
Cohesion: 0.09
Nodes (11): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, ExampleTest, IndividualPdfTemplateTest, LksParticipantRankingTest (+3 more)

### Community 52 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 54 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 58 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.11
Nodes (7): {closure#1}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), Illuminate\Support\Facades\Schema, Illuminate\Support\Facades\Storage

### Community 59 - "Referensi Rumus LKS"
Cohesion: 0.20
Nodes (9): Aktivitas dan rumus matriks, Aturan tambahan sistem, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi (+1 more)

### Community 62 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 64 - "Controller"
Cohesion: 0.13
Nodes (8): Controller, DashboardController, DocumentVerificationController, PrototypeDashboardController, {closure#1}(), 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 65 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 67 - "ChecklistRecordingService.php"
Cohesion: 0.16
Nodes (9): ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface, Illuminate\Auth\Access\AuthorizationException (+1 more)

### Community 69 - "applyDashboard"
Cohesion: 0.13
Nodes (20): applyDashboard(), bulkChecklistMonths(), dateInputValue(), formatDate(), loadChecklistCorrectionContext(), localDateValue(), openChecklistCorrection(), renderAdminDispensationManager() (+12 more)

### Community 70 - "UserFactory.php"
Cohesion: 0.27
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 71 - "CreateNewUser.php"
Cohesion: 0.18
Nodes (7): CreateNewUser, ResetUserPassword, ProfileValidationRules, Illuminate\Support\Facades\Validator, Illuminate\Validation\Rule, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

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

### Community 83 - "persistChecklist"
Cohesion: 0.24
Nodes (11): checkIcon(), checklistSaveErrorKey(), persistChecklist(), recordingUnavailableLabel(), renderFullCheck(), renderQuickCheck(), setChecklistSaveError(), setChecklistSaving() (+3 more)

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
- **250 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+245 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 569 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **66 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LksCoreTest`, `ChecklistRecordingService.php`, `UserFactory.php`, `CreateNewUser.php`, `PasswordResetTest.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TwoFactorChallengeTest`, `FortifyServiceProvider.php`, `Illuminate\Http\Request`, `OrganizationController.php`, `TestCase`, `ProfileUpdateTest`, `PeriodActivationService.php`, `EmailVerificationTest`?**
  _High betweenness centrality (0.083) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksPeriod` to `RecapController.php`, `DashboardController.php`, `LksExportController`, `Tech Stack — LKS Santri Karya`, `LksExportController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Database Design — LKS Santri Karya`, `Illuminate\Http\JsonResponse`, `Architecture System — LKS Santri Karya`, `PeriodActivationService.php`, `PeriodController.php`, `LksScoreCalculator.php`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `Controller`, `DashboardController.php`, `RecapController.php`, `LksCoreTest`, `LksExportController`, `User`, `PeriodConfigurationController`, `ChecklistController`, `LksExportController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `Illuminate\Http\JsonResponse`, `TestCase`, `AuditLog`, `PeriodActivationService.php`, `PeriodController.php`, `ChecklistController.php`, `LksScoreCalculator.php`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _250 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.07241689128481581 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.05142857142857143 - nodes in this community are weakly interconnected._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06439393939393939 - nodes in this community are weakly interconnected._