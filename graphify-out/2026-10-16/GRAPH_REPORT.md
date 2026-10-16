# Graph Report - lks-santri-karya  (2026-10-16)

## Corpus Check
- 169 files · ~74,106 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1269 nodes · 2578 edges · 130 communities (62 shown, 68 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 23 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `586cf8ca`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- frontend/app.js
- FortifyServiceProvider.php
- ExampleTest
- Tech Stack — LKS Santri Karya
- package.json
- User
- Product Requirements Document
- TestCase
- Illuminate\Http\Request
- AuditLog
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- AppServiceProvider.php
- Referensi Rumus LKS
- Illuminate\Database\Eloquent\Concerns\HasUuids
- AuthenticationTest
- Illuminate\Database\Schema\Blueprint
- Illuminate\Foundation\Http\FormRequest
- applyDashboard
- LksExportController.php
- RecapController.php
- PeriodActivity
- renderRecapViews
- Ekspor PDF LKS
- ChecklistRecordingService.php
- scripts
- Product
- PeriodConfigurationController.php
- composer.json
- require-dev
- OrganizationController.php
- ChecklistController.php
- require
- artisan
- Illuminate\Support\Facades\Schema
- ⚡security.blade.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- PeriodActivationService
- 0001_01_01_000000_create_users_table.php
- PasswordResetTest
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- ProfileUpdateTest
- LksCoreTest
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- 2026_10_07_010000_assign_leader_to_teams.php
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
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
- openBulkChecklist
- LksExportController
- PeriodParticipantSnapshot
- DESIGN.md
- 2026_10_15_000000_add_magang_targets_to_lks_rules.php
- 2026_10_07_030000_add_lks_work_calendar.php
- EmailVerificationTest
- 0001_01_01_000002_create_jobs_table.php
- LksScoreCalculator
- Illuminate\Support\Str
- settings.php
- showToast
- 0001_01_01_000001_create_cache_table.php
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- Illuminate\Support\Facades\DB
- applyViewerIdentity
- SantriProfile
- 2026-09-29T06-52-35Z__frontend-index-html.md
- LksActivity
- PasswordResetTest.php
- escapeHtml
- DocumentSignatory
- {closure#19}

## God Nodes (most connected - your core abstractions)
1. `User` - 84 edges
2. `LksPeriod` - 69 edges
3. `PeriodParticipantSnapshot` - 51 edges
4. `LksCoreTest` - 50 edges
5. `AuditLog` - 38 edges
6. `escapeHtml()` - 35 edges
7. `PeriodActivity` - 32 edges
8. `LksScoreCalculator` - 31 edges
9. `PeriodConfigurationController` - 29 edges
10. `TestCase` - 28 edges

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

## Communities (130 total, 68 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.07
Nodes (35): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, breadcrumb, checklistSaveErrors (+27 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.06
Nodes (28): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, GenericFailedPasswordResetLinkResponse, {closure#1}(), {closure#10}(), {closure#11}() (+20 more)

### Community 2 - "ExampleTest"
Cohesion: 0.38
Nodes (3): ExampleTest, PeriodActivityRuleTest, PHPUnit\Framework\TestCase

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.09
Nodes (14): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, SecurityTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "TestCase"
Cohesion: 0.13
Nodes (8): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Livewire\Livewire

### Community 8 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (21): Controller, AccountController, ChecklistController, DashboardController, {closure#2}(), DocumentSignatoryController, DocumentVerificationController, OrganizationController (+13 more)

### Community 9 - "AuditLog"
Cohesion: 0.15
Nodes (12): {closure#20}(), {closure#21}(), {closure#24}(), {closure#17}(), {closure#20}(), {closure#8}(), AuditLog, {closure#1}() (+4 more)

### Community 11 - "Database Design — LKS Santri Karya"
Cohesion: 0.09
Nodes (22): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 4.1 `users`, 4.3 `user_roles`, 4.4 `departments` (+14 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "AppServiceProvider.php"
Cohesion: 0.06
Nodes (26): ActiveUserProvider, EnsurePasswordChanged, EnsureUserIsActive, Logout, AppServiceProvider, {closure#1}(), {closure#2}(), FortifyServiceProvider (+18 more)

### Community 16 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 17 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.10
Nodes (9): LksChecklist, LksDocumentVerification, PeriodHolidaySnapshot, Role, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Database\Eloquent\Relations\BelongsToMany (+1 more)

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.18
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 20 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 21 - "applyDashboard"
Cohesion: 0.19
Nodes (13): activityRuleLabel(), applyDashboard(), checkIcon(), checklistSaveErrorKey(), renderFullCheck(), renderPersonalSummaryLoading(), renderPersonalSummaryUnavailable(), renderQuickCheck() (+5 more)

### Community 22 - "LksExportController.php"
Cohesion: 0.17
Nodes (6): {closure#2}(), BaconQrCode\Renderer\Image\SvgImageBackEnd, BaconQrCode\Renderer\ImageRenderer, BaconQrCode\Renderer\RendererStyle\RendererStyle, BaconQrCode\Writer, Barryvdh\DomPDF\Facade\Pdf

### Community 23 - "RecapController.php"
Cohesion: 0.14
Nodes (6): {closure#11}(), {closure#4}(), {closure#7}(), {closure#9}(), {closure#8}(), Illuminate\Support\Collection

### Community 25 - "renderRecapViews"
Cohesion: 0.18
Nodes (25): canUsePersonalLks(), dashboardSummaryData(), initials(), lksCategory(), lksCategoryLabel(), openRecapDetail(), participantStatus(), percentage() (+17 more)

### Community 26 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses, Ekspor PDF LKS, Rekomendasi otomatis, Tanda tangan

### Community 27 - "ChecklistRecordingService.php"
Cohesion: 0.20
Nodes (9): ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface, Illuminate\Auth\Access\AuthorizationException (+1 more)

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "PeriodConfigurationController.php"
Cohesion: 0.13
Nodes (8): {closure#11}(), {closure#12}(), {closure#14}(), {closure#15}(), {closure#16}(), {closure#5}(), {closure#7}(), CalendarHoliday

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 34 - "OrganizationController.php"
Cohesion: 0.08
Nodes (3): {closure#22}(), {closure#23}(), Illuminate\Validation\Rule

### Community 35 - "ChecklistController.php"
Cohesion: 0.33
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Validation\ValidationException

### Community 37 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 39 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.14
Nodes (5): {closure#1}(), {closure#1}(), {closure#2}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 40 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 42 - "2026_09_29_000001_create_lks_configuration_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

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

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 108 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 111 - "openBulkChecklist"
Cohesion: 0.25
Nodes (11): activityScheduleLabel(), bulkChecklistMonths(), dateInputValue(), loadChecklistCorrectionContext(), localDateValue(), markAdminFormPristine(), openBulkChecklist(), openChecklistCorrection() (+3 more)

### Community 114 - "PeriodParticipantSnapshot"
Cohesion: 0.17
Nodes (3): PeriodParticipantSnapshot, {closure#2}(), {closure#7}()

### Community 118 - "2026_10_15_000000_add_magang_targets_to_lks_rules.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 120 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 122 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 124 - "Illuminate\Support\Str"
Cohesion: 0.14
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 128 - "showToast"
Cohesion: 0.17
Nodes (20): apiFormError(), confirmAction(), downloadPdfPreview(), logout(), markBulkChecklistInvalidDates(), parseHolidayImportEntries(), renderBulkChecklistCalendar(), requestLogout() (+12 more)

### Community 132 - "Illuminate\Support\Facades\DB"
Cohesion: 0.13
Nodes (6): {closure#1}(), {closure#2}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Storage

### Community 133 - "applyViewerIdentity"
Cohesion: 0.17
Nodes (26): apiError(), applyViewerIdentity(), canOpenView(), canView(), dashboardNotice(), displayRole(), finishInitialBoot(), hasConfiguredRole() (+18 more)

### Community 134 - "SantriProfile"
Cohesion: 0.18
Nodes (3): Department, SantriProfile, Team

### Community 135 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 139 - "LksActivity"
Cohesion: 0.12
Nodes (5): {closure#6}(), {closure#9}(), LksActivity, 4.5 `teams`, Illuminate\Database\Eloquent\Relations\HasMany

### Community 140 - "PasswordResetTest.php"
Cohesion: 0.14
Nodes (5): TwoFactorChallengeTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification, Laravel\Fortify\Features

### Community 141 - "escapeHtml"
Cohesion: 0.20
Nodes (18): activityAudience(), adminOptions(), adminPanel(), audiencePicker(), escapeHtml(), formatDate(), masterActivityRuleFields(), openAdminFormModal() (+10 more)

### Community 142 - "DocumentSignatory"
Cohesion: 0.32
Nodes (4): {closure#1}(), {closure#6}(), DocumentSignatory, {closure#4}()

## Knowledge Gaps
- **242 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+237 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 509 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **68 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `OrganizationController.php`, `TestCase`, `Illuminate\Http\Request`, `AuditLog`, `PasswordResetTest.php`, `PeriodActivationService`, `PasswordResetTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `AuthenticationTest`, `LksCoreTest`, `ProfileUpdateTest`, `EmailVerificationTest`, `ChecklistRecordingService.php`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `Illuminate\Http\Request` to `ChecklistController.php`, `User`, `AuditLog`, `LksActivity`, `PeriodActivationService`, `LksExportController`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `LksCoreTest`, `LksExportController.php`, `RecapController.php`, `LksScoreCalculator`, `PeriodConfigurationController.php`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `Tech Stack — LKS Santri Karya`, `Illuminate\Http\Request`, `Database Design — LKS Santri Karya`, `PeriodActivationService`, `Architecture System — LKS Santri Karya`, `LksExportController`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodParticipantSnapshot`, `LksExportController.php`, `RecapController.php`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _242 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.06606606606606606 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05939716312056738 - nodes in this community are weakly interconnected._
- **Should `Tech Stack — LKS Santri Karya` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._