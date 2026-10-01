---
version: 1
slug: "frontend-index-html"
primary_target: "frontend/index.html"
related_targets: ["backend/resources/views/dashboard.blade.php"]
---

# Frontend Design — LKS Santri Karya

## Direction contract

**THESIS:** LKS is a calm daily register, not a productivity game or an admin spreadsheet. The interface refuses dashboard-card overload and makes today's honest checklist the center of every Santri visit.

**OWN-WORLD:** “Lembar Amalan Harian” uses a restrained ink, paper, and evergreen system: fine ledger rules, direct labels, large touchable checks, and a single progress trail. Completion states use language and mark shape in addition to color.

**STORY:** A Santri sees what can be recorded today, records it confidently, and understands present progress. A Leader sees who needs attention before entering detail. An Admin sees programme health and only then configuration.

**FIRST VIEWPORT:** On the Santri dashboard, the active period and today's date anchor a quiet header; a large circular completion mark and short progress sentence sit above the first actionable checklist items. Desktop adds a narrow progress rail at the side without moving the primary task below the fold.

**FORM:** Lembar Amalan Harian, grounded in a personal daily register and operational check sheet. Direction seed: `8c80f8a9`.

**FINISH:** unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance.

## App shell and navigation

- Fixed desktop sidebar; compact mobile header with a bottom task navigation for Dashboard, LKS Saya, and Rekap.
- Role switch in the prototype demonstrates Santri, Leader, and Admin views without simulating authentication.
- Period selection remains visible in every reporting context.
- Secondary admin configuration remains grouped under Pengaturan, never mixed with daily checklist tasks.

## Screen inventory

| Screen | Primary task | Essential content |
|---|---|---|
| Dashboard Santri | Understand today and continue LKS | Active period, progress, today's action, activity summaries |
| LKS Saya | Record daily activities | Date selector, checklist, saving feedback, score summary |
| Riwayat | Review a closed period | Period list, score, completion status |
| Dashboard Leader | Identify members needing attention | Member count, tuntas/belum tuntas, priority rows |
| Rekap | Compare filtered results | Search, role-aware filters, status-first table, detail drawer |
| Departemen | Read organization trend | Department comparison table and chart |
| Pengaturan | Configure master data | Plain administrative lists and actions |

## Responsive and state rules

- At 390 px, checklists use one column with full-width touch targets. Tables become grouped result cards; key status remains above the fold.
- At tablet and desktop widths, summary/ringkasan remains adjacent to the work area while the action order is unchanged.
- Saved, saving, offline/error, empty, closed-period, denied-access, and loading states must have explicit copy and a recovery action where appropriate.
- Charts supplement tabular values and never become the only presentation of a department result.
