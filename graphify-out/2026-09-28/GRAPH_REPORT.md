# Graph Report - lks-santri-karya  (2026-09-28)

## Corpus Check
- 102 files · ~27,155 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 16, .css 4, .example 1)

## Summary
- 536 nodes · 617 edges · 78 communities (30 shown, 48 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Product Requirements Document
- Database Design — LKS Santri Karya
- Architecture System — LKS Santri Karya
- Tech Stack — LKS Santri Karya
- Product
- package.json
- Illuminate\Database\Schema\Blueprint
- FortifyServiceProvider.php
- Panduan Kerja Proyek LKS Santri Karya
- User
- task
- FortifyServiceProvider
- spin
- TestCase
- multiselect
- DatabaseSeeder.php
- UserFactory.php
- scripts
- frontend/app.js
- require-dev
- composer.json
- require
- security.blade.php
- Logout.php
- config
- PrototypeDashboardController
- Frontend Design — LKS Santri Karya
- Frontend Design — LKS Santri Karya
- psr-4
- laravel
- logging.php
- two-factor-setup-modal.blade.php
- console.php
- artisan
- autoload-dev
- profile.blade.php
- header.blade.php
- sidebar.blade.php
- card.blade.php
- simple.blade.php
- split.blade.php
- appearance.blade.php
- delete-user-form.blade.php
- recovery-codes.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 53 edges
2. `TestCase` - 24 edges
3. `Product Requirements Document` - 22 edges
4. `scripts` - 13 edges
5. `require-dev` - 12 edges
6. `Product` - 12 edges
7. `Database Design — LKS Santri Karya` - 12 edges
8. `Architecture System — LKS Santri Karya` - 11 edges
9. `SecurityTest` - 10 edges
10. `require` - 9 edges

## Surprising Connections (you probably didn't know these)
- `PrototypeDashboardController` --inherits--> `Controller`  [EXTRACTED]
  backend/app/Http/Controllers/PrototypeDashboardController.php → backend/app/Http/Controllers/Controller.php
- `AuthenticationTest` --inherits--> `TestCase`  [EXTRACTED]
  backend/tests/Feature/Auth/AuthenticationTest.php → backend/tests/TestCase.php
- `EmailVerificationTest` --inherits--> `TestCase`  [EXTRACTED]
  backend/tests/Feature/Auth/EmailVerificationTest.php → backend/tests/TestCase.php
- `PasswordResetTest` --inherits--> `TestCase`  [EXTRACTED]
  backend/tests/Feature/Auth/PasswordResetTest.php → backend/tests/TestCase.php
- `ProfileUpdateTest` --inherits--> `TestCase`  [EXTRACTED]
  backend/tests/Feature/Settings/ProfileUpdateTest.php → backend/tests/TestCase.php

## Import Cycles
- None detected.

## Communities (78 total, 48 thin omitted)

### Community 0 - "Product Requirements Document"
Cohesion: 0.07
Nodes (29): 10. Halaman LKS individu, 11. Rekap, 12. Alur utama, 13. Functional requirements, 14. Business rules, 15. Halaman minimum, 16. Dashboard, 17. Non-functional requirements (+21 more)

### Community 1 - "Database Design — LKS Santri Karya"
Cohesion: 0.08
Nodes (24): 10. Kebijakan integritas dan penghapusan, 11. Open decisions sebelum migrasi final, 1. Tujuan desain, 2. ERD, 3. Aturan relasi dan snapshot, 4.1 `users`, 4.2 `roles`, 4.3 `user_roles` (+16 more)

### Community 2 - "Architecture System — LKS Santri Karya"
Cohesion: 0.09
Nodes (21): 10. Batas evolusi, 1. Ringkasan arsitektur, 2. Prinsip arsitektur, 3. Batas modul, 4.1 Identity & Access, 4.2 Organization, 4.3 LKS Domain, 4.4 Reporting (+13 more)

### Community 3 - "Tech Stack — LKS Santri Karya"
Cohesion: 0.13
Nodes (14): 1. Keputusan utama, 2. Mengapa stack ini, 3. Paket dan kemampuan platform, 4. Standar engineering, 5. Lingkungan lokal, 6. Deployment awal, 7. Batas evolusi, Backend (+6 more)

### Community 4 - "Product"
Cohesion: 0.15
Nodes (12): Accessibility & Inclusion, Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product (+4 more)

### Community 5 - "package.json"
Cohesion: 0.06
Nodes (30): dependencies, concurrently, @laravel/passkeys, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, vite-plus (+22 more)

### Community 6 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.12
Nodes (14): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+6 more)

### Community 7 - "FortifyServiceProvider.php"
Cohesion: 0.07
Nodes (22): CreateNewUser, ResetUserPassword, PasswordValidationRules, ProfileValidationRules, {closure#10}(), {closure#8}(), {closure#9}(), {closure#1}() (+14 more)

### Community 8 - "Panduan Kerja Proyek LKS Santri Karya"
Cohesion: 0.50
Nodes (3): Aturan kerja wajib, Panduan Kerja Proyek LKS Santri Karya, Prosedur minimum

### Community 9 - "User"
Cohesion: 0.06
Nodes (16): User, AuthenticationTest, EmailVerificationTest, PasswordResetTest, ProfileUpdateTest, SecurityTest, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Database\Eloquent\Attributes\Fillable (+8 more)

### Community 11 - "FortifyServiceProvider"
Cohesion: 0.16
Nodes (8): AppServiceProvider, {closure#1}(), FortifyServiceProvider, Carbon\CarbonImmutable, Illuminate\Support\Facades\Date, Illuminate\Support\Facades\DB, Illuminate\Support\ServiceProvider, Illuminate\Validation\Rules\Password

### Community 13 - "TestCase"
Cohesion: 0.07
Nodes (18): PasswordConfirmationTest, RegistrationTest, TwoFactorChallengeTest, DashboardTest, ExampleTest, TestCase, ExampleTest, Illuminate\Auth\Events\Verified (+10 more)

### Community 15 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 16 - "UserFactory.php"
Cohesion: 0.18
Nodes (5): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Str, Pdo\Mysql, static

### Community 17 - "scripts"
Cohesion: 0.15
Nodes (13): scripts, ci:check, dev, lint, lint:check, post-autoload-dump, post-create-project-cmd, post-root-package-install (+5 more)

### Community 21 - "frontend/app.js"
Cohesion: 0.20
Nodes (10): breadcrumb, navButtons, openView(), pageTitle, setRole(), settingRoutes, showToast(), toast (+2 more)

### Community 22 - "require-dev"
Cohesion: 0.17
Nodes (12): require-dev, fakerphp/faker, larastan/larastan, laravel/pail, laravel/pao, laravel/pint, laravel/sail, mockery/mockery (+4 more)

### Community 23 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 24 - "require"
Cohesion: 0.22
Nodes (9): require, laravel/chisel, laravel/fortify, laravel/framework, laravel/tinker, livewire/blaze, livewire/flux, livewire/livewire (+1 more)

### Community 25 - "security.blade.php"
Cohesion: 0.25
Nodes (7): pages, partials.settings-heading, closeDeleteModal, confirmDelete({{ $passkey[, deletePasskey, disable, $dispatch(

### Community 26 - "Logout.php"
Cohesion: 0.38
Nodes (5): Logout, Illuminate\Http\RedirectResponse, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Session, Livewire\Features\SupportRedirects\Redirector

### Community 27 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 28 - "PrototypeDashboardController"
Cohesion: 0.15
Nodes (5): Controller, PrototypeDashboardController, Illuminate\Http\Response, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Route

### Community 30 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 31 - "Frontend Design — LKS Santri Karya"
Cohesion: 0.33
Nodes (5): App shell and navigation, Direction contract, Frontend Design — LKS Santri Karya, Responsive and state rules, Screen inventory

### Community 32 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 33 - "laravel"
Cohesion: 0.40
Nodes (5): extra, laravel, post-create-project, dont-discover, installer

### Community 34 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 36 - "two-factor-setup-modal.blade.php"
Cohesion: 0.50
Nodes (3): confirmTwoFactor, resetVerification, showVerificationIfNecessary

### Community 39 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

## Knowledge Gaps
- **195 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+190 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 330 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `UserFactory.php`, `DatabaseSeeder.php`, `TestCase`, `FortifyServiceProvider.php`?**
  _High betweenness centrality (0.068) - this node is a cross-community bridge._
- **Why does `TestCase` connect `TestCase` to `User`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _195 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Product Requirements Document` be split into smaller, more focused modules?**
  _Cohesion score 0.06666666666666667 - nodes in this community are weakly interconnected._
- **Should `Database Design — LKS Santri Karya` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._
- **Should `Architecture System — LKS Santri Karya` be split into smaller, more focused modules?**
  _Cohesion score 0.09090909090909091 - nodes in this community are weakly interconnected._
- **Should `Tech Stack — LKS Santri Karya` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._