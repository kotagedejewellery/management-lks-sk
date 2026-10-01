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
- The approved calculation baseline is capped activity achievement, equal default weights, and a configurable final completion threshold initially set to 90%.
- Exact policy for checklist correction after a period ends remains a business-rule decision; the proposed default is that closed periods are read-only.

## Brand Commitments

The product name is LKS Santri Karya. No visual identity, logo, palette, or typography has been provided. Future UI work must establish those choices through the Impeccable workflow rather than inventing a permanent brand direction here.

## Evidence on Hand

- Approved source requirements: `docs/PRD.md`.
- No existing implementation, visual assets, logo, production data, testimonial, benchmark, or brand manual is present in the workspace.

## Product Principles

1. Daily recording should be quicker than maintaining a spreadsheet row.
2. Calculations and completion status must be explainable from the underlying checklist and target.
3. Access follows responsibility: personal data for Santri, assigned members for leaders, full configuration and reporting for administrators.
4. A completed period remains historically truthful even when master data changes later.
5. The first release solves the established LKS workflow without adding motivational or social mechanics.

## Accessibility & Inclusion

The web interface must be responsive for smartphone and desktop use, keyboard-operable, readable at browser zoom, and communicate completion state with text in addition to color.

