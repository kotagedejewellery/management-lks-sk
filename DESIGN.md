# Dashboard reporting

## Typography and product identity

The application uses **Manrope** as its only web UI typeface. Heading, navigation, form, data, and metric hierarchy comes from the existing size and weight scale (400â€“800), not from a second display font. Authentication and document-verification pages follow the same rule. PDF generation uses its built-in **DejaVu Sans** consistently; it stays self-contained and avoids remote-font rendering changes that could break the one-page LKS layout.

The sidebar mark is followed by **LKS** and the supporting label **Lembar Kendali Spiritual** on desktop. The compact mobile lockup remains only the mark and LKS. The personal recording navigation is named **Aktivitas Ibadah Saya**; established terms such as LKS period, LKS history, and report scope remain unchanged.

Dashboard Admin keeps programme health readable before comparison. A single **Jenis LKS** selector scopes the summary, department bars, and follow-up link together: Semua jenis LKS, Reguler, or Magang.

The selector is a normal labelled control, uses the existing paper-and-evergreen system, and expands to the available width on mobile. Rekap makes the active passing rule explicit: Leader 90%, Staff Reguler 85%, or Staff Magang 75% by default. Activity targets remain independently configured per category.

Dashboard Santri names the active LKS category beside the current score. Dashboard Leader keeps one team view: it states the current Reguler/Magang composition and names each member's category in the attention list and achievement rows. It does not add a separate filter or a second dashboard, so follow-up still begins from one complete team view.

Individual rankings appear only in the Ikhwan/Akhwat report and only for Staff. The report presents up to three highest and three lowest Staff separately for Ikhwan and Akhwat; a participant is not repeated between a gender's two lists when that gender has fewer than six Staff. Leader, department, and LKS-category tabs use comparative group bars and trends without ranking cards; each ranked Staff row names its LKS category in text.

The Department trend selector contains every active department, plus historical departments preserved by snapshots within the six periods rendered in the chart. Active departments use their current master name; archived departments retain their historical snapshot name. A department with no participants in a given period remains selectable and renders that period as **—**, rather than a misleading 0%.

Recommendations use a warm, neutral second-person voice: acknowledge progress first when appropriate, state the score or remaining gap, then name no more than two activities for follow-up. When individual minimums are met but the final threshold is still missed, the two lowest activities provide that follow-up. The copy avoids automatic gendered salutations and blame. The same plain-text recommendation is used on the dashboard, recap, history, and PDF; closed periods preserve it as a snapshot.

PDF actions use a labelled preview step before download. The preview opens in a keyboard-operable dialog, keeps the document in the current task, provides a clear close action and a single primary **Unduh PDF** action, and retains a short fallback message for browsers that cannot render PDF inline. A non-PDF response is treated as an error; an expired session tells the user to sign in again.

The individual LKS PDF header keeps the full Yayasan Riyadhul Jannah Indonesia logo on the left and the full Kotagede Jewellery logo on the right. The centered title uses two balanced lines—**Lembar Kendali Spiritual** and **Santri Karya PT. Kotagede Jewellery Group**—followed by **Yayasan Riyadhul Jannah Indonesia** on one smaller line. The period remains in the identity block so the compact header does not push the document past one A4 page.

An active period has an explicit recording state: **Dibuka** or **Dijeda**. Admins control it from the period detail alongside, but distinct from, the irreversible **Tutup periode** action; the period list repeats that substatus. When recording is paused or the date range has ended, Santri retain a read-only view of their records with a direct explanatory message on every unavailable checklist item; checklist and bulk actions are unavailable.

When a participant joins an active period after its start, the date control and bulk checklist begin at their participation date. A concise note states that their target is calculated from that date, without adding a separate configuration step.

On an active period's participant roster, search is paired with compact Departemen, Tim, status checklist, and Jenis LKS filters. Tim remains unavailable until its Departemen is chosen; all filtering and the existing pagination run on the server. The controls wrap into two columns and then one on narrow phones, with a visible Reset filter action only when a filter is active.

Rekap and the Leader dashboard append **Mulai [tanggal]** only for members who joined after the period started, so a new member's partial-period score is not mistaken for a full-period score.

Period detail groups its three completion thresholds under one **Ambang tuntas** block. On phones, the block spans a full grid row and the recording controls may wrap rather than overflow. The reversible recording action is a distinct secondary control, while **Tutup periode** remains visually separated as the irreversible action.
