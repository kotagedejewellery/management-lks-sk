# Graph Report - lks-santri-karya  (2026-10-08)

## Corpus Check
- 157 files · ~62,392 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 1100 nodes · 2169 edges · 111 communities (52 shown, 59 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `962f7153`
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
- DatabaseSeeder.php
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- bootstrap/app.php
- Referensi Rumus LKS
- LksCoreTest
- PeriodActivationService.php
- Illuminate\Database\Schema\Blueprint
- PeriodParticipantSnapshot
- Illuminate\Database\Eloquent\Relations\BelongsTo
- PeriodConfigurationController.php
- UserFactory.php
- Illuminate\Foundation\Http\FormRequest
- User.php
- Ekspor PDF LKS
- LksScoreCalculator
- scripts
- Product
- AuditLog
- composer.json
- require-dev
- settings.php
- ProfileUpdateTest
- PasswordResetTest.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- require
- artisan
- Illuminate\Support\Facades\Schema
- ⚡security.blade.php
- 2026_10_07_030000_add_staff_passing_threshold_to_lks_periods.php
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- 2026-09-29T06-52-35Z__frontend-index-html.md
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000002_create_jobs_table.php
- Illuminate\Support\Facades\DB
- PeriodActivity
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Str
- 2026_10_07_010000_assign_leader_to_teams.php
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
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- PeriodActivationService
- 0001_01_01_000001_create_cache_table.php
- 2026_10_08_010000_add_recommendation_snapshot_to_period_participant_snapshots.php
- AuthenticationTest

## God Nodes (most connected - your core abstractions)
1. `User` - 81 edges
2. `LksPeriod` - 65 edges
3. `PeriodParticipantSnapshot` - 45 edges
4. `AuditLog` - 37 edges
5. `PeriodActivity` - 30 edges
6. `LksCoreTest` - 30 edges
7. `escapeHtml()` - 29 edges
8. `TestCase` - 28 edges
9. `LksScoreCalculator` - 27 edges
10. `PeriodConfigurationController` - 25 edges

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

## Communities (111 total, 59 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.05
Nodes (112): accountPasswordField(), activePeriodActivities, activityAudience(), activityRuleLabel(), adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState (+104 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.06
Nodes (30): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, AppServiceProvider, {closure#1}(), {closure#1}(), {closure#10}() (+22 more)

### Community 2 - "TestCase"
Cohesion: 0.10
Nodes (10): PasswordConfirmationTest, RegistrationTest, TwoFactorChallengeTest, DashboardTest, ExampleTest, TestCase, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase (+2 more)

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.11
Nodes (5): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, EmailVerificationTest, SecurityTest

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.13
Nodes (8): {closure#6}(), Department, LksActivity, PeriodHolidaySnapshot, Role, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\HasMany

### Community 8 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (21): Controller, AccountController, DashboardController, {closure#1}(), {closure#2}(), DocumentSignatoryController, OrganizationController, PeriodConfigurationController (+13 more)

### Community 9 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 11 - "Database Design — LKS Santri Karya"
Cohesion: 0.07
Nodes (25): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 4.1 `users`, 4.2 `roles`, 4.3 `user_roles` (+17 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "bootstrap/app.php"
Cohesion: 0.13
Nodes (14): EnsureUserIsActive, Logout, {closure#1}(), {closure#2}(), {closure#3}(), Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions (+6 more)

### Community 16 - "Referensi Rumus LKS"
Cohesion: 0.22
Nodes (8): Aktivitas dan rumus matriks, Catatan validasi spreadsheet, Parameter aktivitas dan cakupan kelompok, Penerapan pada sistem, Referensi Rumus LKS, Rumus lembar kendali individu, Rumus rekap organisasi, Struktur spreadsheet

### Community 18 - "PeriodActivationService.php"
Cohesion: 0.23
Nodes (8): DocumentSignatory, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), Illuminate\Auth\Access\AuthorizationException

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.18
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 20 - "PeriodParticipantSnapshot"
Cohesion: 0.13
Nodes (12): ChecklistController, LksChecklist, PeriodParticipantSnapshot, ChecklistRecordingService, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}() (+4 more)

### Community 21 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (6): {closure#11}(), {closure#12}(), SantriProfile, Team, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Http\UploadedFile

### Community 22 - "PeriodConfigurationController.php"
Cohesion: 0.15
Nodes (7): {closure#10}(), {closure#16}(), {closure#17}(), {closure#5}(), {closure#7}(), {closure#8}(), CalendarHoliday

### Community 23 - "UserFactory.php"
Cohesion: 0.31
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 24 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.13
Nodes (5): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreBulkChecklistRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 25 - "User.php"
Cohesion: 0.18
Nodes (9): Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Fortify\Contracts\PasskeyUser, Laravel\Fortify\PasskeyAuthenticatable (+1 more)

### Community 26 - "Ekspor PDF LKS"
Cohesion: 0.40
Nodes (4): Akses, Ekspor PDF LKS, Rekomendasi otomatis, Tanda tangan

### Community 27 - "LksScoreCalculator"
Cohesion: 0.06
Nodes (15): {closure#2}(), {closure#5}(), {closure#6}(), LksExportController, {closure#4}(), {closure#6}(), {closure#8}(), RecapController (+7 more)

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "AuditLog"
Cohesion: 0.11
Nodes (9): {closure#10}(), {closure#13}(), {closure#8}(), {closure#9}(), {closure#11}(), {closure#12}(), {closure#13}(), AuditLog (+1 more)

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 34 - "ProfileUpdateTest"
Cohesion: 0.20
Nodes (3): ProfileUpdateTest, Illuminate\Support\Facades\Hash, Livewire\Livewire

### Community 35 - "PasswordResetTest.php"
Cohesion: 0.18
Nodes (3): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Support\Facades\Notification

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

### Community 45 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 47 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 48 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 49 - "Illuminate\Support\Facades\DB"
Cohesion: 0.16
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 50 - "PeriodActivity"
Cohesion: 0.19
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), PeriodActivity, Illuminate\Validation\ValidationException

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

### Community 58 - "Illuminate\Support\Str"
Cohesion: 0.18
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Support\Str, Pdo\Mysql

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 120 - "AuthenticationTest"
Cohesion: 0.14
Nodes (5): AuthenticationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL, Laravel\Fortify\Features

## Knowledge Gaps
- **219 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+214 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 440 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **59 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `TestCase`, `PasswordResetTest.php`, `ProfileUpdateTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\Request`, `Database Design — LKS Santri Karya`, `PeriodActivationService`, `LksCoreTest`, `PeriodActivationService.php`, `PeriodParticipantSnapshot`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `UserFactory.php`, `AuthenticationTest`, `User.php`, `AuditLog`?**
  _High betweenness centrality (0.115) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `Tech Stack — LKS Santri Karya`, `Illuminate\Http\Request`, `Database Design — LKS Santri Karya`, `Architecture System — LKS Santri Karya`, `PeriodActivationService`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `Illuminate\Http\Request` to `User`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `PeriodActivationService`, `LksCoreTest`, `PeriodActivity`, `PeriodActivationService.php`, `PeriodParticipantSnapshot`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PeriodConfigurationController.php`, `LksScoreCalculator`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _219 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.05030274802049371 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05587808417997097 - nodes in this community are weakly interconnected._
- **Should `TestCase` be split into smaller, more focused modules?**
  _Cohesion score 0.09879032258064516 - nodes in this community are weakly interconnected._