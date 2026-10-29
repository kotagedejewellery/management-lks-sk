# Graph Report - lks-santri-karya  (2026-10-29)

## Corpus Check
- 172 files · ~85,547 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1301 nodes · 2687 edges · 139 communities (67 shown, 72 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 23 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `477c8309`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- LksCoreTest
- frontend/app.js
- package.json
- applyViewerIdentity
- LksScoreCalculator.php
- Product Requirements Document
- User
- LksPeriod
- PeriodActivationService.php
- escapeHtml
- task
- Illuminate\Http\Response
- spin
- TestCase
- multiselect
- Database Design — LKS Santri Karya
- Controller
- RecapController.php
- Architecture System — LKS Santri Karya
- AuditLog
- Illuminate\Support\Facades\DB
- PeriodConfigurationController.php
- Panduan Admin Menyiapkan LKS
- Illuminate\Database\Schema\Blueprint
- openAdminFormModal
- Illuminate\Foundation\Http\FormRequest
- ChecklistRecordingService.php
- Illuminate\Http\Request
- FortifyServiceProvider.php
- Illuminate\Support\Str
- AppServiceProvider.php
- PeriodParticipantSnapshot
- PasswordResetTest.php
- applyDashboard
- LksExportController
- 4. Tabel master identitas dan organisasi
- Tech Stack — LKS Santri Karya
- artisan
- Illuminate\Http\JsonResponse
- LksExportController.php
- ProfileValidationRules.php
- SecurityTest
- scripts
- showToast
- Product
- PasswordValidationRules
- PeriodActivity
- composer.json
- require-dev
- EmailVerificationTest
- PeriodController.php
- require
- ProfileUpdateTest
- Panduan Kerja Proyek LKS Santri Karya
- PeriodActivationService
- EnsureUserIsActive.php
- UserFactory.php
- Illuminate\Support\Facades\Schema
- Referensi Rumus LKS
- bootstrap/app.php
- ⚡security.blade.php
- PasswordResetTest
- FortifyServiceProvider
- config
- 2026_10_15_000000_add_magang_targets_to_lks_rules.php
- 2026-09-29T06-52-35Z__frontend-index-html.md
- ChecklistController
- GenericFailedPasswordResetLinkResponse
- Logout.php
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- 0001_01_01_000001_create_cache_table.php
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- 2026_10_07_030000_add_lks_work_calendar.php
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- 2026_10_09_000000_add_must_change_password_to_users_table.php
- settings.php
- Panduan Brevo untuk Email Reset Password
- Ekspor PDF LKS
- renderAdminView
- ⚡two-factor-setup-modal.blade.php
- console.php
- {closure#19}
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
2. `LksPeriod` - 75 edges
3. `LksCoreTest` - 59 edges
4. `PeriodParticipantSnapshot` - 54 edges
5. `AuditLog` - 39 edges
6. `PeriodActivity` - 37 edges
7. `LksScoreCalculator` - 35 edges
8. `escapeHtml()` - 35 edges
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

## Communities (139 total, 72 thin omitted)

### Community 0 - "LksCoreTest"
Cohesion: 0.05
Nodes (16): {closure#22}(), {closure#23}(), Department, LksActivity, LksChecklist, LksDocumentVerification, PeriodHolidaySnapshot, Role (+8 more)

### Community 1 - "frontend/app.js"
Cohesion: 0.06
Nodes (41): activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, breadcrumb, checklistSaveErrors, clearPdfPreview() (+33 more)

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 3 - "applyViewerIdentity"
Cohesion: 0.16
Nodes (29): apiError(), applyViewerIdentity(), canOpenView(), canUsePersonalLks(), canView(), dashboardNotice(), displayRole(), finishInitialBoot() (+21 more)

### Community 4 - "LksScoreCalculator.php"
Cohesion: 0.15
Nodes (5): {closure#11}(), {closure#9}(), {closure#2}(), {closure#8}(), Illuminate\Support\Collection

### Community 5 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 6 - "User"
Cohesion: 0.10
Nodes (13): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, AuthenticationTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne (+5 more)

### Community 8 - "PeriodActivationService.php"
Cohesion: 0.20
Nodes (7): {closure#1}(), {closure#10}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#8}(), {closure#9}()

### Community 9 - "escapeHtml"
Cohesion: 0.23
Nodes (25): dashboardSummaryData(), escapeHtml(), initials(), lksCategory(), lksCategoryLabel(), openRecapDetail(), participantStatus(), participationStartNote() (+17 more)

### Community 11 - "Illuminate\Http\Response"
Cohesion: 0.20
Nodes (4): PrototypeDashboardController, 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 13 - "TestCase"
Cohesion: 0.12
Nodes (9): PasswordConfirmationTest, RegistrationTest, DashboardTest, ExampleTest, TestCase, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase (+1 more)

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (17): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+9 more)

### Community 16 - "Controller"
Cohesion: 0.12
Nodes (4): Controller, DashboardController, DocumentVerificationController, {closure#1}()

### Community 17 - "RecapController.php"
Cohesion: 0.14
Nodes (3): {closure#4}(), {closure#7}(), RecapController

### Community 18 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 20 - "AuditLog"
Cohesion: 0.17
Nodes (10): {closure#20}(), {closure#21}(), {closure#24}(), {closure#15}(), {closure#16}(), {closure#17}(), {closure#20}(), {closure#8}() (+2 more)

### Community 21 - "Illuminate\Support\Facades\DB"
Cohesion: 0.09
Nodes (8): {closure#1}(), {closure#2}(), {closure#1}(), {closure#1}(), {closure#1}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Storage

### Community 22 - "PeriodConfigurationController.php"
Cohesion: 0.13
Nodes (9): {closure#11}(), {closure#12}(), {closure#14}(), {closure#21}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#9}() (+1 more)

### Community 23 - "Panduan Admin Menyiapkan LKS"
Cohesion: 0.11
Nodes (18): 10. Uji siap pakai dari database bersih, 1. Buka Pengaturan, 2. Isi Struktur Organisasi, 3. Buat akun Santri Karya, 4. Atur Aktivitas LKS dan aturan default, 5. Isi Kalender Kerja, 6. Kelola Dokumen dan Tanda Tangan, 7. Buat dan lengkapi periode Draft (+10 more)

### Community 24 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.13
Nodes (16): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+8 more)

### Community 25 - "openAdminFormModal"
Cohesion: 0.18
Nodes (18): activityRuleLabel(), activityScheduleLabel(), adminOptions(), audiencePicker(), bulkChecklistMonths(), dateInputValue(), loadChecklistCorrectionContext(), localDateValue() (+10 more)

### Community 26 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "ChecklistRecordingService.php"
Cohesion: 0.18
Nodes (10): ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), Carbon\CarbonInterface, Illuminate\Auth\Access\AuthorizationException (+2 more)

### Community 29 - "FortifyServiceProvider.php"
Cohesion: 0.17
Nodes (14): {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+6 more)

### Community 30 - "Illuminate\Support\Str"
Cohesion: 0.14
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 31 - "AppServiceProvider.php"
Cohesion: 0.14
Nodes (10): ActiveUserProvider, AppServiceProvider, {closure#1}(), {closure#2}(), Carbon\CarbonImmutable, Illuminate\Auth\EloquentUserProvider, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Support\Facades\Date (+2 more)

### Community 32 - "PeriodParticipantSnapshot"
Cohesion: 0.22
Nodes (4): PeriodParticipantSnapshot, {closure#7}(), LksScoreCalculator, EffectiveActivityTargetTest

### Community 33 - "PasswordResetTest.php"
Cohesion: 0.14
Nodes (5): TwoFactorChallengeTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Foundation\Http\Middleware\PreventRequestForgery, Illuminate\Support\Facades\Notification, Laravel\Fortify\Features

### Community 34 - "applyDashboard"
Cohesion: 0.16
Nodes (17): applyDashboard(), checkIcon(), checklistSaveErrorKey(), formatDate(), persistChecklist(), recordingUnavailableLabel(), renderFullCheck(), renderPersonalSummaryLoading() (+9 more)

### Community 36 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.20
Nodes (7): 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 37 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 39 - "Illuminate\Http\JsonResponse"
Cohesion: 0.26
Nodes (3): AccountController, PeriodController, Illuminate\Http\JsonResponse

### Community 40 - "LksExportController.php"
Cohesion: 0.12
Nodes (12): {closure#1}(), {closure#2}(), DocumentSignatoryController, {closure#2}(), {closure#6}(), DocumentSignatory, {closure#4}(), BaconQrCode\Renderer\Image\SvgImageBackEnd (+4 more)

### Community 43 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 44 - "showToast"
Cohesion: 0.23
Nodes (15): accountPasswordField(), apiFormError(), confirmAction(), downloadPdfPreview(), logout(), parseHolidayImportEntries(), renderAccount(), requestLogout() (+7 more)

### Community 45 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 46 - "PasswordValidationRules"
Cohesion: 0.19
Nodes (7): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, Illuminate\Support\Facades\Validator, Laravel\Fortify\Contracts\CreatesNewUsers, Laravel\Fortify\Contracts\ResetsUserPasswords

### Community 47 - "PeriodActivity"
Cohesion: 0.18
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), PeriodActivity, PeriodActivityRuleTest

### Community 48 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 49 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 50 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 52 - "require"
Cohesion: 0.20
Nodes (10): require, barryvdh/laravel-dompdf, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux (+2 more)

### Community 54 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.22
Nodes (8): Aturan kerja wajib, Keselamatan data, Panduan Kerja Proyek LKS Santri Karya, Penggunaan skill wajib, Performa, Prioritas ketika aturan bertentangan, Prosedur minimum, Validasi dan dokumentasi

### Community 56 - "EnsureUserIsActive.php"
Cohesion: 0.43
Nodes (4): EnsurePasswordChanged, EnsureUserIsActive, Closure, Symfony\Component\HttpFoundation\Response

### Community 57 - "UserFactory.php"
Cohesion: 0.31
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 58 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.15
Nodes (4): {closure#1}(), {closure#1}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 59 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 60 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 62 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 65 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 67 - "2026_10_15_000000_add_magang_targets_to_lks_rules.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 69 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 71 - "GenericFailedPasswordResetLinkResponse"
Cohesion: 0.40
Nodes (3): GenericFailedPasswordResetLinkResponse, Illuminate\Support\Facades\Password, Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse

### Community 72 - "Logout.php"
Cohesion: 0.38
Nodes (5): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 73 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.16
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}()

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

### Community 91 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 92 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses dan preview, Dokumen dan verifikasi QR, Ekspor PDF LKS, Rekomendasi otomatis

### Community 93 - "renderAdminView"
Cohesion: 0.40
Nodes (5): activityAudience(), adminPanel(), paginationControls(), renderAdminView(), ruleList()

### Community 94 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

## Knowledge Gaps
- **242 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+237 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 515 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **72 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `LksCoreTest`, `PasswordResetTest.php`, `4. Tabel master identitas dan organisasi`, `PeriodActivationService.php`, `ProfileValidationRules.php`, `SecurityTest`, `TestCase`, `PasswordValidationRules`, `EmailVerificationTest`, `OrganizationController.php`, `ProfileUpdateTest`, `PeriodActivationService`, `UserFactory.php`, `ChecklistRecordingService.php`, `Illuminate\Http\Request`, `FortifyServiceProvider.php`, `PasswordResetTest`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `PeriodParticipantSnapshot` to `LksCoreTest`, `LksExportController`, `LksScoreCalculator.php`, `Tech Stack — LKS Santri Karya`, `LksExportController.php`, `Database Design — LKS Santri Karya`, `Controller`, `RecapController.php`, `Architecture System — LKS Santri Karya`, `PeriodActivationService`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `LksCoreTest`, `PeriodParticipantSnapshot`, `LksExportController`, `LksScoreCalculator.php`, `ChecklistController`, `Illuminate\Http\JsonResponse`, `LksExportController.php`, `User`, `PeriodActivationService.php`, `Illuminate\Http\Response`, `PeriodActivity`, `Controller`, `RecapController.php`, `PeriodController.php`, `PeriodConfigurationController.php`, `PeriodActivationService`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _242 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `LksCoreTest` be split into smaller, more focused modules?**
  _Cohesion score 0.05070028011204482 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.06201550387596899 - nodes in this community are weakly interconnected._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06439393939393939 - nodes in this community are weakly interconnected._