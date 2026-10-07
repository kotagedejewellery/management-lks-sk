# Graph Report - lks-santri-karya  (2026-10-07)

## Corpus Check
- 139 files · ~44,710 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 28 file(s) not represented in the graph (top: (none) 18, .css 5, .example 1)

## Summary
- 893 nodes · 1622 edges · 99 communities (45 shown, 54 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 20 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `48f7874e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- frontend/app.js
- Illuminate\Database\Eloquent\Concerns\HasUuids
- FortifyServiceProvider.php
- OrganizationController.php
- package.json
- User
- Product Requirements Document
- Architecture System — LKS Santri Karya
- bootstrap/app.php
- task
- TestCase
- spin
- Illuminate\Foundation\Testing\RefreshDatabase
- multiselect
- Database Design — LKS Santri Karya
- Illuminate\Database\Schema\Blueprint
- Tech Stack — LKS Santri Karya
- ActivatePeriodRequest
- scripts
- Product
- composer.json
- require-dev
- EmailVerificationTest
- PasswordResetTest.php
- Illuminate\Support\Str
- require
- Illuminate\Support\Facades\DB
- ⚡security.blade.php
- SecurityTest
- config
- AuthenticationTest
- 2026-09-29T06-52-35Z__frontend-index-html.md
- LksCoreTest.php
- 0001_01_01_000000_create_users_table.php
- 0001_01_01_000002_create_jobs_table.php
- 2026_09_29_000001_create_lks_configuration_tables.php
- 2026_09_29_000002_create_lks_records_and_audit_tables.php
- artisan
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Illuminate\Support\Facades\Schema
- PeriodConfigurationController.php
- 2025_08_14_170933_add_two_factor_columns_to_users_table.php
- settings.php
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
- LksChecklist
- Illuminate\Database\Eloquent\Relations\BelongsTo
- LksCoreTest
- Team

## God Nodes (most connected - your core abstractions)
1. `User` - 80 edges
2. `LksPeriod` - 42 edges
3. `PeriodParticipantSnapshot` - 34 edges
4. `AuditLog` - 26 edges
5. `TestCase` - 26 edges
6. `escapeHtml()` - 22 edges
7. `Product Requirements Document` - 22 edges
8. `SantriProfile` - 20 edges
9. `OrganizationController` - 18 edges
10. `PeriodActivity` - 18 edges

## Surprising Connections (you probably didn't know these)
- `1. Keputusan utama` --references--> `PrototypeDashboardController`  [INFERRED]
  docs/Tech Stack.md → backend/app/Http/Controllers/PrototypeDashboardController.php
- `2. Prinsip arsitektur` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Architecture System.md → backend/app/Services/LksScoreCalculator.php
- `7. Rumus perhitungan` --references--> `LksScoreCalculator`  [INFERRED]
  docs/Database Design.md → backend/app/Services/LksScoreCalculator.php
- `Supporting Evidence` --references--> `todayIso()`  [INFERRED]
  .impeccable/critique/2026-09-29T06-52-35Z__frontend-index-html.md → frontend/app.js
- `3. Paket dan kemampuan platform` --references--> `AuditLog`  [INFERRED]
  docs/Tech Stack.md → backend/app/Models/AuditLog.php

## Import Cycles
- None detected.

## Communities (99 total, 54 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.06
Nodes (24): Controller, AccountController, ChecklistController, DashboardController, OrganizationController, PeriodConfigurationController, {closure#3}(), {closure#6}() (+16 more)

### Community 1 - "frontend/app.js"
Cohesion: 0.06
Nodes (91): accountPasswordField(), activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, adminOptions(), adminPanel() (+83 more)

### Community 2 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.16
Nodes (8): Department, LksActivity, Role, 4.5 `teams`, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Eloquent\Relations\HasMany

### Community 3 - "FortifyServiceProvider.php"
Cohesion: 0.06
Nodes (29): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, AppServiceProvider, {closure#1}(), {closure#1}(), {closure#10}() (+21 more)

### Community 4 - "OrganizationController.php"
Cohesion: 0.12
Nodes (11): {closure#12}(), {closure#8}(), {closure#9}(), AuditLog, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}() (+3 more)

### Community 5 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 6 - "User"
Cohesion: 0.09
Nodes (14): User, LksPeriodPolicy, PeriodParticipantSnapshotPolicy, ProfileUpdateTest, 4.2 `roles`, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 7 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 8 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 9 - "bootstrap/app.php"
Cohesion: 0.13
Nodes (14): EnsureUserIsActive, Logout, {closure#1}(), {closure#2}(), {closure#3}(), Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions (+6 more)

### Community 11 - "TestCase"
Cohesion: 0.14
Nodes (6): RegistrationTest, TwoFactorChallengeTest, TestCase, Illuminate\Foundation\Testing\TestCase, Laravel\Fortify\Features, Livewire\Livewire

### Community 13 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.14
Nodes (6): PasswordConfirmationTest, DashboardTest, ExampleTest, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase, PHPUnit\Framework\TestCase

### Community 15 - "Database Design — LKS Santri Karya"
Cohesion: 0.09
Nodes (22): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 4.1 `users`, 4.3 `user_roles`, 4.4 `departments` (+14 more)

### Community 16 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.20
Nodes (10): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#1}(), {closure#2}(), {closure#3}() (+2 more)

### Community 17 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.13
Nodes (14): 1. Keputusan utama, 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend (+6 more)

### Community 18 - "ActivatePeriodRequest"
Cohesion: 0.19
Nodes (4): ActivatePeriodRequest, AddPeriodParticipantRequest, StoreChecklistRequest, Illuminate\Foundation\Http\FormRequest

### Community 19 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 20 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 21 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 22 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 23 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 24 - "PasswordResetTest.php"
Cohesion: 0.18
Nodes (3): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Support\Facades\Notification

### Community 25 - "Illuminate\Support\Str"
Cohesion: 0.24
Nodes (5): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Str, Pdo\Mysql

### Community 26 - "require"
Cohesion: 0.22
Nodes (9): require, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux, livewire/livewire (+1 more)

### Community 27 - "Illuminate\Support\Facades\DB"
Cohesion: 0.18
Nodes (4): {closure#1}(), {closure#2}(), Illuminate\Database\Migrations\Migration, Illuminate\Support\Facades\DB

### Community 28 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 30 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 32 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.29
Nodes (6): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction, Supporting Evidence

### Community 33 - "LksCoreTest.php"
Cohesion: 0.24
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 34 - "0001_01_01_000000_create_users_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 35 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 36 - "2026_09_29_000001_create_lks_configuration_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 37 - "2026_09_29_000002_create_lks_records_and_audit_tables.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 39 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 40 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 41 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 42 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 43 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 44 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.20
Nodes (4): {closure#1}(), {closure#2}(), {closure#1}(), Illuminate\Support\Facades\Schema

### Community 45 - "PeriodConfigurationController.php"
Cohesion: 0.12
Nodes (5): {closure#5}(), {closure#6}(), {closure#7}(), PeriodActivity, Illuminate\Validation\Rule

### Community 48 - "Panduan Brevo untuk Email Reset Password"
Cohesion: 0.40
Nodes (4): Konfigurasi production, Panduan Brevo untuk Email Reset Password, Persiapan di Brevo, Terapkan dan verifikasi

### Community 49 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 50 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 95 - "LksChecklist"
Cohesion: 0.21
Nodes (5): LksChecklist, ChecklistRecordingService, {closure#1}(), Carbon\CarbonInterface, Illuminate\Support\Carbon

### Community 96 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.22
Nodes (4): {closure#10}(), {closure#11}(), SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

## Knowledge Gaps
- **208 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+203 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 386 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **54 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\Request`, `LksCoreTest.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `FortifyServiceProvider.php`, `OrganizationController.php`, `LksCoreTest`, `TestCase`, `PeriodConfigurationController.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `EmailVerificationTest`, `PasswordResetTest.php`, `AuthenticationTest`, `SecurityTest`, `LksChecklist`?**
  _High betweenness centrality (0.125) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `Illuminate\Http\Request` to `Architecture System — LKS Santri Karya`, `LksCoreTest.php`, `Tech Stack — LKS Santri Karya`, `Database Design — LKS Santri Karya`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `Illuminate\Http\Request` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `LksCoreTest.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `LksCoreTest`, `OrganizationController.php`, `User`, `PeriodConfigurationController.php`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _208 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.057195149851292613 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.059841047218326324 - nodes in this community are weakly interconnected._
- **Should `FortifyServiceProvider.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05803921568627451 - nodes in this community are weakly interconnected._