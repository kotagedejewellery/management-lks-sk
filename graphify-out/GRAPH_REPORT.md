# Graph Report - lks-santri-karya  (2026-10-16)

## Corpus Check
- 171 files · ~75,671 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1286 nodes · 2630 edges · 138 communities (68 shown, 70 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 23 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `18462280`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- LksCoreTest
- frontend/app.js
- ChecklistRecordingService.php
- Illuminate\Database\Eloquent\Concerns\HasUuids
- TestCase
- package.json
- User
- Product Requirements Document
- applyViewerIdentity
- task
- LksPeriod
- spin
- escapeHtml
- multiselect
- Architecture System — LKS Santri Karya
- SantriProfile
- PeriodConfigurationController.php
- Illuminate\Support\Facades\DB
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- Database Design — LKS Santri Karya
- RecapController.php
- FortifyServiceProvider.php
- Illuminate\Support\Str
- loadDashboard
- AppServiceProvider.php
- AuditLog
- PeriodActivity
- Tech Stack — LKS Santri Karya
- LksExportController.php
- LksExportController
- 4. Tabel master identitas dan organisasi
- scripts
- openBulkChecklist
- Product
- PasswordValidationRules
- artisan
- composer.json
- require-dev
- openAdminFormModal
- applyDashboard
- EmailVerificationTest
- require
- UserFactory.php
- Panduan Kerja Proyek LKS Santri Karya
- PeriodController.php
- EnsureUserIsActive.php
- LksScoreCalculator.php
- LksScoreCalculator
- Illuminate\Support\Facades\Schema
- AuthenticationTest
- Referensi Rumus LKS
- ProfileValidationRules
- PeriodActivationService
- bootstrap/app.php
- ⚡security.blade.php
- PasswordResetTest.php
- PasswordResetTest
- FortifyServiceProvider
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- 2026_10_15_000000_add_magang_targets_to_lks_rules.php
- ExampleTest
- GenericFailedPasswordResetLinkResponse
- Logout.php
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- ProfileUpdateTest
- Frontend Design — LKS Santri Karya
- 2026-09-29T06-52-35Z__frontend-index-html.md
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- 0001_01_01_000001_create_cache_table.php
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- 2026_10_15_000002_remove_legacy_signature_uploads.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
- renderAdminView
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
- DESIGN.md

## God Nodes (most connected - your core abstractions)
1. `User` - 86 edges
2. `LksPeriod` - 72 edges
3. `LksCoreTest` - 55 edges
4. `PeriodParticipantSnapshot` - 51 edges
5. `AuditLog` - 39 edges
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
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php
- `3. Paket dan kemampuan platform` --references--> `AuditLog`  [INFERRED]
  docs/Tech Stack.md → backend/app/Models/AuditLog.php
- `3. Paket dan kemampuan platform` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Tech Stack.md → backend/app/Services/LksScoreCalculator.php

## Import Cycles
- None detected.

## Communities (138 total, 70 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (19): Controller, AccountController, DashboardController, {closure#1}(), {closure#2}(), DocumentSignatoryController, DocumentVerificationController, OrganizationController (+11 more)

### Community 1 - "LksCoreTest"
Cohesion: 0.11
Nodes (6): {closure#6}(), {closure#9}(), Department, LksActivity, Team, LksCoreTest

### Community 2 - "frontend/app.js"
Cohesion: 0.06
Nodes (41): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminListState, breadcrumb, checkIcon(), checklistSaveErrorKey() (+33 more)

### Community 3 - "ChecklistRecordingService.php"
Cohesion: 0.08
Nodes (15): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}() (+7 more)

### Community 4 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.11
Nodes (8): DocumentSignatory, LksDocumentVerification, PeriodHolidaySnapshot, Role, {closure#4}(), Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\HasMany

### Community 5 - "TestCase"
Cohesion: 0.10
Nodes (10): PasswordConfirmationTest, RegistrationTest, TwoFactorChallengeTest, DashboardTest, ExampleTest, TestCase, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase (+2 more)

### Community 6 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 7 - "User"
Cohesion: 0.10
Nodes (13): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, SecurityTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne (+5 more)

### Community 8 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 9 - "applyViewerIdentity"
Cohesion: 0.16
Nodes (27): apiError(), applyViewerIdentity(), canOpenView(), canUsePersonalLks(), canView(), dashboardNotice(), displayRole(), hasConfiguredRole() (+19 more)

### Community 11 - "LksPeriod"
Cohesion: 0.16
Nodes (10): ChecklistController, LksPeriod, PeriodParticipantSnapshot, {closure#1}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#8}() (+2 more)

### Community 13 - "escapeHtml"
Cohesion: 0.24
Nodes (24): dashboardSummaryData(), escapeHtml(), initials(), lksCategory(), lksCategoryLabel(), openRecapDetail(), participantStatus(), percentage() (+16 more)

### Community 15 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 17 - "SantriProfile"
Cohesion: 0.13
Nodes (5): {closure#22}(), {closure#23}(), LksChecklist, SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 18 - "PeriodConfigurationController.php"
Cohesion: 0.11
Nodes (10): {closure#11}(), {closure#12}(), {closure#14}(), {closure#15}(), {closure#16}(), {closure#17}(), {closure#5}(), {closure#7}() (+2 more)

### Community 19 - "Illuminate\Support\Facades\DB"
Cohesion: 0.11
Nodes (6): {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 20 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 21 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.18
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 22 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (17): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+9 more)

### Community 23 - "RecapController.php"
Cohesion: 0.14
Nodes (6): {closure#11}(), {closure#4}(), {closure#7}(), {closure#9}(), {closure#8}(), Illuminate\Support\Collection

### Community 24 - "FortifyServiceProvider.php"
Cohesion: 0.17
Nodes (14): {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+6 more)

### Community 25 - "Illuminate\Support\Str"
Cohesion: 0.14
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 26 - "loadDashboard"
Cohesion: 0.23
Nodes (16): apiFormError(), confirmAction(), downloadPdfPreview(), loadDashboard(), logout(), parseHolidayImportEntries(), persistChecklist(), requestLogout() (+8 more)

### Community 27 - "AppServiceProvider.php"
Cohesion: 0.18
Nodes (9): ActiveUserProvider, AppServiceProvider, {closure#1}(), {closure#2}(), Carbon\CarbonImmutable, Illuminate\Auth\EloquentUserProvider, Illuminate\Support\Facades\Date, Illuminate\Support\ServiceProvider (+1 more)

### Community 28 - "AuditLog"
Cohesion: 0.15
Nodes (8): {closure#2}(), {closure#19}(), {closure#20}(), {closure#21}(), {closure#24}(), {closure#20}(), AuditLog, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 29 - "PeriodActivity"
Cohesion: 0.19
Nodes (4): {closure#1}(), {closure#3}(), {closure#21}(), PeriodActivity

### Community 30 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 31 - "LksExportController.php"
Cohesion: 0.15
Nodes (7): {closure#2}(), {closure#6}(), BaconQrCode\Renderer\Image\SvgImageBackEnd, BaconQrCode\Renderer\ImageRenderer, BaconQrCode\Renderer\RendererStyle\RendererStyle, BaconQrCode\Writer, Barryvdh\DomPDF\Facade\Pdf

### Community 33 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.17
Nodes (8): 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.5 `teams`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 34 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 35 - "openBulkChecklist"
Cohesion: 0.19
Nodes (13): activityRuleLabel(), activityScheduleLabel(), bulkChecklistMonths(), localDateValue(), markBulkChecklistInvalidDates(), openBulkChecklist(), renderBulkChecklistCalendar(), selectedBulkDates() (+5 more)

### Community 36 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 37 - "PasswordValidationRules"
Cohesion: 0.24
Nodes (6): CreateNewUser, ResetUserPassword, PasswordValidationRules, Illuminate\Support\Facades\Validator, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 39 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 40 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 41 - "openAdminFormModal"
Cohesion: 0.23
Nodes (12): adminFormSnapshot(), adminOptions(), audiencePicker(), hasUnsavedAdminFormChanges(), loadChecklistCorrectionContext(), markAdminFormPristine(), masterActivityRuleFields(), openAdminFormModal() (+4 more)

### Community 42 - "applyDashboard"
Cohesion: 0.23
Nodes (12): applyDashboard(), dateInputValue(), finishInitialBoot(), formatDate(), openChecklistCorrection(), renderPersonalSummaryLoading(), renderPersonalSummaryUnavailable(), setChecklistEmpty() (+4 more)

### Community 43 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 44 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 45 - "UserFactory.php"
Cohesion: 0.27
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 46 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 48 - "EnsureUserIsActive.php"
Cohesion: 0.36
Nodes (5): EnsurePasswordChanged, EnsureUserIsActive, Closure, Illuminate\Support\Facades\Auth, Symfony\Component\HttpFoundation\Response

### Community 51 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.22
Nodes (3): {closure#1}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 53 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 54 - "ProfileValidationRules"
Cohesion: 0.32
Nodes (3): ProfileValidationRules, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Validation\Rule

### Community 56 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 57 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 58 - "PasswordResetTest.php"
Cohesion: 0.25
Nodes (3): Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification

### Community 61 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 62 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 63 - "2026_10_15_000000_add_magang_targets_to_lks_rules.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 64 - "ExampleTest"
Cohesion: 0.38
Nodes (3): ExampleTest, PeriodActivityRuleTest, PHPUnit\Framework\TestCase

### Community 65 - "GenericFailedPasswordResetLinkResponse"
Cohesion: 0.40
Nodes (3): GenericFailedPasswordResetLinkResponse, Illuminate\Support\Facades\Password, Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse

### Community 66 - "Logout.php"
Cohesion: 0.47
Nodes (4): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 67 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 68 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 69 - "2026_09_29_000001_create_lks_configuration_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 70 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 72 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 73 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.33
Nodes (5): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction

### Community 74 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 75 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 76 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 77 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 85 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 86 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses dan preview, Dokumen dan verifikasi QR, Ekspor PDF LKS, Rekomendasi otomatis

### Community 87 - "renderAdminView"
Cohesion: 0.40
Nodes (5): activityAudience(), adminPanel(), paginationControls(), renderAdminView(), ruleList()

### Community 88 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

## Knowledge Gaps
- **242 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+237 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 513 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **70 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\Request`, `4. Tabel master identitas dan organisasi`, `LksCoreTest`, `ChecklistRecordingService.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PasswordValidationRules`, `TestCase`, `ProfileUpdateTest`, `LksPeriod`, `EmailVerificationTest`, `UserFactory.php`, `OrganizationController.php`, `AuthenticationTest`, `ProfileValidationRules`, `PeriodActivationService`, `FortifyServiceProvider.php`, `PasswordResetTest.php`, `PasswordResetTest`?**
  _High betweenness centrality (0.095) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `Illuminate\Http\Request`, `LksExportController`, `LksCoreTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `User`, `PeriodController.php`, `SantriProfile`, `PeriodConfigurationController.php`, `LksScoreCalculator.php`, `LksScoreCalculator`, `PeriodActivationService`, `RecapController.php`, `AuditLog`, `LksExportController.php`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `Illuminate\Http\Request`, `LksExportController`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `LksPeriod`, `Architecture System — LKS Santri Karya`, `LksScoreCalculator.php`, `PeriodActivationService`, `Database Design — LKS Santri Karya`, `RecapController.php`, `Tech Stack — LKS Santri Karya`, `LksExportController.php`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _242 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.0516404581634634 - nodes in this community are weakly interconnected._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.1110523532522475 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.06201550387596899 - nodes in this community are weakly interconnected._