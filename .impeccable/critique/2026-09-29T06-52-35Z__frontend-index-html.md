---
target: frontend/index.html
total_score: 19
max_score: 40
na_heuristics: 
p0_count: 2
p1_count: 3
target_identity: "file:E:\\AI Engineer\\PT KotaGede Jewellery\\New System\\lks-santri-karya\\frontend\\index.html"
target_fingerprint: "sha256:3f67f085fb81fd4e0ebace5d00b84d9ac5c3bda19c3db800758a79360aefed8c"
target_path: "E:\\AI Engineer\\PT KotaGede Jewellery\\New System\\lks-santri-karya\\frontend\\index.html"
timestamp: 2026-09-29T06-52-35Z
slug: frontend-index-html
---
## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|---|---:|---|
| 1 | Visibility of System Status | 2/4 | Loading and recovery states are incomplete. |
| 2 | Match System / Real World | 3/4 | Language fits LKS activity recording. |
| 3 | User Control and Freedom | 2/4 | Date and period controls imply unavailable actions. |
| 4 | Consistency and Standards | 2/4 | Live data and static prototype screens conflict. |
| 5 | Error Prevention | 2/4 | Server validation exists, but UI affordances are misleading. |
| 6 | Recognition Rather Than Recall | 3/4 | Labeled navigation and checklist states help. |
| 7 | Flexibility and Efficiency | 1/4 | No keyboard/bulk acceleration or effective monitoring path. |
| 8 | Aesthetic and Minimalist Design | 2/4 | Santri screen is focused; the app as a whole contains prototype residue. |
| 9 | Error Recovery | 2/4 | Generic toasts and screen replacement lack targeted recovery. |
| 10 | Help and Documentation | 0/4 | No contextual explanation or onboarding. |
| **Total** | | **19/40** | **Poor: redesign of the operational surfaces is needed.** |

## Design Specificity Verdict

The paper, ink, and evergreen "Lembar Amalan Harian" direction is a credible and product-specific foundation for the Santri daily register. The implementation fails to sustain that promise across roles: dynamic data is mixed with static examples, making the product feel like a prototype rather than a dependable operational tool.

## Priority Issues

1. **P0 — Fabricated operational data.** Leader and Admin dashboards retain hard-coded metrics, names, and department values. Do not render those dashboards as live until their API data and explicit empty/loading states exist.
2. **P0 — Mobile Admin dead end.** The mobile navigation does not expose Pengaturan or Departemen for an Admin while LKS and Riwayat are hidden. Make mobile navigation role-aware.
3. **P1 — Unsupported flows look available.** Date chips, period controls, History, and several row actions look functional but do not have real handlers/data. Implement them, or remove/honestly disable them with explanatory copy.
4. **P1 — Accessibility basics are incomplete.** Full-checklist toggle buttons lack accessible names; recap search lacks a persistent label; compact targets are below practical mobile sizes; table semantics are missing.
5. **P1 — Text polish is broken.** Literal UTF-8 artifacts such as `Â·`, `â€”`, and `â€¦` appear in Indonesian UI copy.

## Supporting Evidence

The deterministic scan reports 23 warnings on `frontend/index.html`: six tiny-text, four undersized functional-text, ten kicker-above-heading, two all-caps body, and one cramped-padding finding. Small table/status/chart text is a real legibility risk; the short all-caps labels and static cramped-padding finding need rendered validation. Browser automation was unavailable, so no viewport or overlay inspection was performed.

`app.js` only wires today’s checklist (`todayIso()`), while date chips have no handler. It updates Santri fields but has no references for leader/admin dashboard metrics. History is static and has no data fetch. Responsive CSS does transform recap records into cards, and the code includes landmarks, role concealment, and text-plus-colour status as positive foundations.

## Persona Red Flags

- **Alex, Leader/Admin power user:** cannot sort or act on risk despite the recap copy; static metrics make monitoring untrustworthy.
- **Sam, keyboard/screen-reader user:** the checklist toggle does not expose its activity name; recap search lacks a label; grid divs do not communicate table columns.
- **Casey, mobile user:** Admin cannot reach configuration through the mobile bar; date chips invite wasted taps; several controls are too small.

## Proposed Direction

Rebuild the interface around three genuine role journeys instead of keeping one static prototype with hidden panels: Santri records today, Leader triages their members, and Admin configures and monitors the programme. First remove deceptive prototype controls and data; then make each role’s dashboard, navigation, empty state, and mobile path complete before adding polish.
