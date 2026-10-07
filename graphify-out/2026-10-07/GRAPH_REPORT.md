# Graph Report - lks-santri-karya  (2026-10-07)

## Corpus Check
- 145 files · ~48,508 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 960 nodes · 1803 edges · 106 communities (48 shown, 58 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `386bbeea`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- frontend/app.js
- FortifyServiceProvider.php
- TestCase
- PeriodActivity
- package.json
- User
- Product Requirements Document
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Illuminate\Http\Request
- Illuminate\Support\Str
- task
- Database Design — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- bootstrap/app.php
- OrganizationController.php
- PeriodParticipantSnapshot
- Illuminate\Foundation\Testing\RefreshDatabase
- Illuminate\Database\Schema\Blueprint
- Illuminate\Http\JsonResponse
- OrganizationController
- AuditLog
- Tech Stack — LKS Santri Karya
- ActivatePeriodRequest
- LksCoreTest
- AuthenticationTest
- LksScoreCalculator
- scripts
- Product
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- require-dev
- settings.php
- EmailVerificationTest
- PasswordResetTest.php
- web.php
- require
- artisan
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- ⚡security.blade.php
- SecurityTest
- LksPeriod
- config
- 2026_09_29_000003_apply_approved_lks_policy_schema.php
- 2026-09-29T06-52-35Z__frontend-index-html.md
- PeriodActivationService
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000002_create_jobs_table.php
- Illuminate\Database\Migrations\Migration
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Facades\Schema
- 2026_10_07_010000_assign_leader_to_teams.php
- 2026_10_07_030000_add_lks_work_calendar.php
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

## God Nodes (most connected - your core abstractions)
1. `User` - 80 edges
2. `LksPeriod` - 54 edges
3. `PeriodParticipantSnapshot` - 37 edges
4. `AuditLog` - 33 edges
5. `PeriodActivity` - 28 edges
6. `TestCase` - 28 edges
7. `PeriodConfigurationController` - 25 edges
8. `escapeHtml()` - 23 edges
9. `Product Requirements Document` - 22 edges
10. `SantriProfile` - 20 edges

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

## Communities (106 total, 58 thin omitted)

### Community 0 - "frontend/app.js"
Cohesion: 0.06
Nodes (98): accountPasswordField(), activePeriodActivities, activityAudience(), activityRuleLabel(), adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState (+90 more)

### Community 1 - "FortifyServiceProvider.php"
Cohesion: 0.06
Nodes (30): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, AppServiceProvider, {closure#1}(), {closure#1}(), {closure#10}() (+22 more)

### Community 2 - "TestCase"
Cohesion: 0.14
Nodes (6): RegistrationTest, TwoFactorChallengeTest, TestCase, Illuminate\Foundation\Testing\TestCase, Laravel\Fortify\Features, Livewire\Livewire

### Community 3 - "PeriodActivity"
Cohesion: 0.18
Nodes (3): {closure#12}(), PeriodActivity, PeriodActivityRuleTest

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 5 - "User"
Cohesion: 0.11
Nodes (13): User, LksPeriodPolicy, ProfileUpdateTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne (+5 more)

### Community 6 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 7 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.12
Nodes (10): {closure#6}(), CalendarHoliday, Department, LksActivity, PeriodHolidaySnapshot, Role, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model (+2 more)

### Community 9 - "Illuminate\Support\Str"
Cohesion: 0.13
Nodes (9): UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Seeder, Illuminate\Support\Facades\Hash, Illuminate\Support\Str, Pdo\Mysql (+1 more)

### Community 11 - "Database Design — LKS Santri Karya"
Cohesion: 0.08
Nodes (23): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 4.1 `users`, 4.3 `user_roles`, 4.4 `departments` (+15 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "bootstrap/app.php"
Cohesion: 0.13
Nodes (14): EnsureUserIsActive, Logout, {closure#1}(), {closure#2}(), {closure#3}(), Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions (+6 more)

### Community 16 - "OrganizationController.php"
Cohesion: 0.12
Nodes (5): {closure#10}(), {closure#13}(), {closure#8}(), {closure#9}(), Team

### Community 17 - "PeriodParticipantSnapshot"
Cohesion: 0.15
Nodes (7): PeriodParticipantSnapshot, PeriodParticipantSnapshotPolicy, ChecklistRecordingService, {closure#1}(), {closure#7}(), Carbon\CarbonInterface, Illuminate\Validation\ValidationException

### Community 18 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.14
Nodes (6): PasswordConfirmationTest, DashboardTest, ExampleTest, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase, PHPUnit\Framework\TestCase

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.18
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 20 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (6): Controller, AccountController, ChecklistController, {closure#3}(), RecapController, Illuminate\Http\JsonResponse

### Community 22 - "AuditLog"
Cohesion: 0.13
Nodes (13): {closure#10}(), {closure#11}(), {closure#5}(), {closure#7}(), {closure#8}(), {closure#9}(), AuditLog, {closure#1}() (+5 more)

### Community 23 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.14
Nodes (13): 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend, CSS kustom + grafik HTML (+5 more)

### Community 24 - "ActivatePeriodRequest"
Cohesion: 0.19
Nodes (4): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 27 - "LksScoreCalculator"
Cohesion: 0.10
Nodes (7): DashboardController, {closure#2}(), {closure#8}(), LksScoreCalculator, EffectiveActivityTargetTest, Illuminate\Support\Carbon, Illuminate\Support\Collection

### Community 28 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 29 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 30 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.16
Nodes (5): {closure#11}(), {closure#12}(), LksChecklist, SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 31 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 32 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 34 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 35 - "PasswordResetTest.php"
Cohesion: 0.18
Nodes (3): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Support\Facades\Notification

### Community 36 - "web.php"
Cohesion: 0.18
Nodes (5): PrototypeDashboardController, {closure#1}(), 1. Keputusan utama, Illuminate\Http\Response, Illuminate\Support\Facades\File

### Community 37 - "require"
Cohesion: 0.22
Nodes (9): require, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux, livewire/livewire (+1 more)

### Community 40 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 42 - "LksPeriod"
Cohesion: 0.19
Nodes (4): {closure#3}(), {closure#6}(), PeriodController, LksPeriod

### Community 43 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 44 - "2026_09_29_000003_apply_approved_lks_policy_schema.php"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 45 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.33
Nodes (5): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction

### Community 47 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 48 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 49 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.16
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 50 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
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

### Community 57 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.20
Nodes (4): {closure#1}(), {closure#2}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 61 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 62 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 63 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

## Knowledge Gaps
- **208 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+203 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 399 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **58 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `FortifyServiceProvider.php`, `TestCase`, `EmailVerificationTest`, `PasswordResetTest.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Support\Str`, `SecurityTest`, `PeriodActivationService`, `OrganizationController.php`, `PeriodParticipantSnapshot`, `Illuminate\Foundation\Testing\RefreshDatabase`, `OrganizationController`, `AuditLog`, `LksCoreTest`, `AuthenticationTest`?**
  _High betweenness centrality (0.118) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `LksScoreCalculator` to `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Database Design — LKS Santri Karya`, `Architecture System — LKS Santri Karya`, `PeriodParticipantSnapshot`, `Illuminate\Http\JsonResponse`, `Tech Stack — LKS Santri Karya`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `LksPeriod` to `User`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\Request`, `PeriodActivationService`, `PeriodParticipantSnapshot`, `Illuminate\Http\JsonResponse`, `AuditLog`, `LksCoreTest`, `LksScoreCalculator`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _208 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.05595959595959596 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05587808417997097 - nodes in this community are weakly interconnected._
- **Should `TestCase` be split into smaller, more focused modules?**
  _Cohesion score 0.14210526315789473 - nodes in this community are weakly interconnected._