# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Delegated and approved: Laravel 13 on PHP 8.3+, Livewire 4, Blade, Tailwind CSS 4, PostgreSQL, Chart.js, Pest, Docker Compose, and Nginx/PHP-FPM deployment.

## Users

- Santri Karya records daily spiritual activities and needs a fast way to understand personal progress, score, and completion status.
- Leaders monitor the members assigned to them and need a clear, limited recap of each member's result.
- Administrators configure people, organizational structure, LKS periods, activities, and targets, then review organization-wide results.

## Product Purpose

LKS Santri Karya replaces a manual Google Sheets checklist and recap process with a browser-based system for recording daily spiritual activities, calculating achievement automatically, and reporting results from individual to department level.

## Positioning

The product preserves the existing LKS mechanism while making daily input, automatic calculation, scoped access, historical records, and organizational recap part of one controlled workflow.

## Operating Context

LKS operates in recurring, generally monthly periods. A Santri Karya checks completed activities by date, while leaders and administrators consume current-period and historical recaps. The application must work on desktop and smartphone browsers.

## Capabilities and Constraints

- The first release includes login, personal LKS, history, role-scoped recaps, organization data, period settings, and activity settings.
- The first release excludes gamification, AI, complex notifications, external integrations, multilevel approvals, rewards, and social features.
- The approved calculation baseline is capped activity achievement and equal default weights. Each period stores final thresholds: Leader 90%, Staff Reguler 85%, and Staff Magang 75% by default.
- Individual rankings are limited to Staff; Leader, department, and category reporting remain aggregate comparisons.
- PDF LKS and rekap can be previewed before download using the same authorized export file. The first individual PDF generated for a closed period issues one reusable verification record; it never changes the period calculation or snapshot.
- Period activation locks its snapshots and configuration; a separate recording control lets an administrator pause or resume Santri checklist input without changing that lifecycle. Checklist input remains limited to the configured date range, while an active period can still be reviewed outside it.
- Santri checklist input is read-only when an active period is paused or its date range has ended. Admin may correct a valid historical date while the period remains active; a closed period is read-only for everyone.
- A participant added after a period starts records only from their snapshot participation date. Their target and minimum target are prorated to their remaining eligible dates; the addition is rejected when no eligible activity date remains. Previously closed periods retain their original non-prorated calculation.

## Brand Commitments

The product name is LKS Santri Karya. Individual LKS PDFs use the supplied original Yayasan Riyadhul Jannah Indonesia and Kotagede Jewellery logos as document identity. No broader web visual identity, palette, or typography has been provided; future web UI work must establish those choices through the Impeccable workflow rather than inventing a permanent brand direction here.

## Evidence on Hand

- Approved source requirements: `docs/PRD.md`.
- Supplied document-logo assets: Yayasan Riyadhul Jannah Indonesia and Kotagede Jewellery.

## Product Principles

1. Daily recording should be quicker than maintaining a spreadsheet row.
2. Calculations and completion status must be explainable from the underlying checklist and target.
3. Access follows responsibility: personal data for Santri, assigned members for leaders, full configuration and reporting for administrators.
4. A completed period remains historically truthful even when master data changes later.
5. The first release solves the established LKS workflow without adding motivational or social mechanics.

## Accessibility & Inclusion

The web interface must be responsive for smartphone and desktop use, keyboard-operable, readable at browser zoom, and communicate completion state with text in addition to color.

