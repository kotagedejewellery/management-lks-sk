# Graph Report - lks-santri-karya  (2026-10-02)

## Corpus Check
- 132 files · ~40,075 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 17, .css 5, .example 1)

## Summary
- 823 nodes · 1379 edges · 90 communities (38 shown, 52 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 23 edges (avg confidence: 0.9)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `032bb9b4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- frontend/app.js
- Illuminate\Database\Schema\Blueprint
- package.json
- Product Requirements Document
- User
- FortifyServiceProvider.php
- Database Design — LKS Santri Karya
- TestCase
- settings.php
- task
- Tech Stack — LKS Santri Karya
- spin
- Architecture System — LKS Santri Karya
- multiselect
- Illuminate\Database\Eloquent\Concerns\HasUuids
- UserFactory.php
- LksChecklist
- Illuminate\Database\Eloquent\Relations\BelongsTo
- AuditLog
- 4. Tabel master identitas dan organisasi
- scripts
- Product
- OrganizationController.php
- composer.json
- require-dev
- SecurityTest
- PasswordResetTest.php
- PeriodParticipantSnapshot
- require
- bootstrap/app.php
- ⚡security.blade.php
- Logout.php
- config
- EmailVerificationTest
- 2026-09-29T06-52-35Z__frontend-index-html.md
- AuthenticationTest
- artisan
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- Panduan Kerja Proyek LKS Santri Karya
- ⚡two-factor-setup-modal.blade.php
- console.php
- Illuminate\Foundation\Testing\RefreshDatabase
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
1. `User` - 74 edges
2. `LksPeriod` - 34 edges
3. `TestCase` - 24 edges
4. `PeriodParticipantSnapshot` - 23 edges
5. `Product Requirements Document` - 22 edges
6. `escapeHtml()` - 21 edges
7. `SantriProfile` - 18 edges
8. `applyDashboard()` - 17 edges
9. `AuditLog` - 15 edges
10. `PeriodActivity` - 15 edges

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

## Communities (90 total, 52 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (22): Controller, ChecklistController, DashboardController, OrganizationController, {closure#5}(), PeriodConfigurationController, {closure#5}(), PeriodController (+14 more)

### Community 1 - "frontend/app.js"
Cohesion: 0.06
Nodes (81): activePeriodActivities, adminFormDialog, adminFormDialogContent, adminFormSnapshot(), adminListState, adminOptions(), adminPanel(), apiError() (+73 more)

### Community 2 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.06
Nodes (33): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+25 more)

### Community 3 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 4 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 5 - "User"
Cohesion: 0.11
Nodes (12): User, LksPeriodPolicy, ProfileUpdateTest, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\HasOne, Illuminate\Foundation\Auth\User (+4 more)

### Community 6 - "FortifyServiceProvider.php"
Cohesion: 0.06
Nodes (29): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, AppServiceProvider, {closure#1}(), {closure#1}(), {closure#10}() (+21 more)

### Community 7 - "Database Design — LKS Santri Karya"
Cohesion: 0.11
Nodes (17): 10. Kebijakan integritas dan penghapusan, 11. Keputusan kebijakan yang disetujui, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 5.1 `lks_periods`, 5.2 `lks_activities`, 5.3 `period_activities` (+9 more)

### Community 8 - "TestCase"
Cohesion: 0.14
Nodes (6): RegistrationTest, TwoFactorChallengeTest, TestCase, Illuminate\Foundation\Testing\TestCase, Laravel\Fortify\Features, Livewire\Livewire

### Community 11 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.10
Nodes (17): PrototypeDashboardController, 1. Keputusan utama, 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi (+9 more)

### Community 13 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 15 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.18
Nodes (6): Department, LksActivity, Team, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\HasMany

### Community 16 - "UserFactory.php"
Cohesion: 0.16
Nodes (6): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, Illuminate\Support\Str, Pdo\Mysql, static

### Community 17 - "LksChecklist"
Cohesion: 0.19
Nodes (6): LksChecklist, ChecklistRecordingService, {closure#1}(), Carbon\CarbonInterface, Illuminate\Support\Carbon, Illuminate\Validation\ValidationException

### Community 18 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.20
Nodes (3): PeriodActivity, SantriProfile, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 19 - "AuditLog"
Cohesion: 0.23
Nodes (6): AuditLog, {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Auth\Access\AuthorizationException, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 20 - "4. Tabel master identitas dan organisasi"
Cohesion: 0.18
Nodes (9): Role, 4.1 `users`, 4.2 `roles`, 4.3 `user_roles`, 4.4 `departments`, 4.5 `teams`, 4.6 `santri_profiles`, 4. Tabel master identitas dan organisasi (+1 more)

### Community 21 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 22 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 24 - "composer.json"
Cohesion: 0.17
Nodes (11): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+3 more)

### Community 25 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 27 - "PasswordResetTest.php"
Cohesion: 0.18
Nodes (3): PasswordResetTest, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Support\Facades\Notification

### Community 28 - "PeriodParticipantSnapshot"
Cohesion: 0.24
Nodes (3): PeriodParticipantSnapshot, PeriodParticipantSnapshotPolicy, {closure#2}()

### Community 29 - "require"
Cohesion: 0.22
Nodes (9): require, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux, livewire/livewire (+1 more)

### Community 30 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 31 - "⚡security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 32 - "Logout.php"
Cohesion: 0.38
Nodes (5): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 33 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 35 - "EmailVerificationTest"
Cohesion: 0.18
Nodes (4): EmailVerificationTest, Illuminate\Auth\Events\Verified, Illuminate\Support\Facades\Event, Illuminate\Support\Facades\URL

### Community 36 - "2026-09-29T06-52-35Z__frontend-index-html.md"
Cohesion: 0.33
Nodes (5): Design Health Score, Design Specificity Verdict, Persona Red Flags, Priority Issues, Proposed Direction

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

### Community 45 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 46 - "⚡two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 48 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.14
Nodes (6): PasswordConfirmationTest, DashboardTest, ExampleTest, ExampleTest, Illuminate\Foundation\Testing\RefreshDatabase, PHPUnit\Framework\TestCase

## Knowledge Gaps
- **203 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+198 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 375 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **52 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Http\Request`, `EmailVerificationTest`, `AuthenticationTest`, `FortifyServiceProvider.php`, `TestCase`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `UserFactory.php`, `LksChecklist`, `Illuminate\Foundation\Testing\RefreshDatabase`, `AuditLog`, `4. Tabel master identitas dan organisasi`, `OrganizationController.php`, `SecurityTest`, `PasswordResetTest.php`, `PeriodParticipantSnapshot`?**
  _High betweenness centrality (0.150) - this node is a cross-community bridge._
- **Why does `LksScoreCalculator` connect `Illuminate\Http\Request` to `Tech Stack — LKS Santri Karya`, `PeriodParticipantSnapshot`, `Architecture System — LKS Santri Karya`, `Database Design — LKS Santri Karya`?**
  _High betweenness centrality (0.043) - this node is a cross-community bridge._
- **Why does `LksPeriod` connect `Illuminate\Http\Request` to `Illuminate\Database\Eloquent\Relations\BelongsTo`, `AuditLog`, `User`, `Illuminate\Database\Eloquent\Concerns\HasUuids`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _203 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.05311542390194075 - nodes in this community are weakly interconnected._
- **Should `frontend/app.js` be split into smaller, more focused modules?**
  _Cohesion score 0.06229797237731413 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Schema\Blueprint` be split into smaller, more focused modules?**
  _Cohesion score 0.05711263881544157 - nodes in this community are weakly interconnected._