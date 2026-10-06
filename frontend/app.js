const viewLabels = { dashboard: ['Ruang pribadi', 'Dashboard'], lks: ['Catatan pribadi', 'LKS Saya'], recap: ['Pemantauan', 'Rekap'], department: ['Laporan organisasi', 'Departemen'], history: ['Catatan pribadi', 'Riwayat'], account: ['Akun', 'Akun Saya'], settings: ['Administrasi', 'Pengaturan'], organization: ['Pengaturan', 'Struktur Organisasi'], people: ['Pengaturan', 'Data Santri Karya'], periods: ['Pengaturan', 'Periode LKS'], activities: ['Pengaturan', 'Aktivitas LKS'] };
const navButtons = document.querySelectorAll('[data-view]');
const viewPanels = document.querySelectorAll('[data-view-panel]');
const pageTitle = document.querySelector('#page-title');
const breadcrumb = document.querySelector('#breadcrumb');
const toast = document.querySelector('#toast');
const pageProgress = document.querySelector('#page-progress');
const confirmDialog = document.querySelector('#confirm-dialog');
const adminFormDialog = document.querySelector('#admin-form-dialog');
const adminFormDialogContent = document.querySelector('[data-admin-form-dialog-content]');
const quickChecks = document.querySelector('#quick-checks');
const fullChecklist = document.querySelector('#full-checklist');
const logoutButton = document.querySelector('#logout-button');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const apiBase = '/api/lks';
let participantId = null;
let viewer = null;
let organizationData = null;
let configurationData = null;
let recapData = null;
let historyData = null;
let historyRecapData = null;
let recapPeriods = [];
let departmentTrendData = [];
let selectedChecklistDate = todayIso();
let activePeriodActivities = [];
let accountData = null;

function openView(name) {
  viewPanels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.viewPanel === name));
  navButtons.forEach((button) => button.classList.toggle('is-active', button.dataset.view === name));
  const [trail, title] = viewLabels[name];
  pageTitle.textContent = title;
  breadcrumb.textContent = trail;
  window.scrollTo({ top: 0, behavior: 'smooth' });
  if (['settings', 'organization', 'people', 'periods', 'activities'].includes(name)) loadAdminView(name);
  if (['recap', 'department'].includes(name)) loadRecap();
  if (['dashboard', 'lks'].includes(name)) loadDashboard({ showProgress: true, reloadRecap: false });
  if (name === 'history') loadHistory({ showProgress: true });
  if (name === 'account') loadAccount({ showProgress: true });
}

navButtons.forEach((button) => button.addEventListener('click', () => openView(button.dataset.view)));
document.addEventListener('click', (event) => {
  const action = event.target.closest('[data-go]');
  if (action) openView(action.dataset.go);
});

function showPageProgress(message = 'Memuat halaman…') {
  pageProgress.querySelector('span:last-child').textContent = message;
  pageProgress.hidden = false;
}

function hidePageProgress() {
  pageProgress.hidden = true;
}

let toastTimer;
function showToast(message, type = 'success', duration = type === 'error' ? 6000 : 2600) {
  toast.querySelector('span').textContent = message;
  toast.dataset.type = type;
  toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
  toast.classList.add('is-visible');
  clearTimeout(toastTimer);
  if (duration > 0) toastTimer = setTimeout(() => toast.classList.remove('is-visible'), duration);
}

document.querySelector('[data-dismiss-toast]')?.addEventListener('click', () => {
  clearTimeout(toastTimer);
  toast.classList.remove('is-visible');
});

function setButtonBusy(button, isBusy, label = '') {
  if (!button) return;
  if (isBusy) {
    if (!button.dataset.originalContent) button.dataset.originalContent = button.innerHTML;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.classList.add('is-busy');
    if (label) button.innerHTML = `<span class="button-spinner" aria-hidden="true"></span>${escapeHtml(label)}`;
    return;
  }
  if (button.dataset.originalContent) {
    button.innerHTML = button.dataset.originalContent;
    delete button.dataset.originalContent;
  }
  button.disabled = false;
  button.removeAttribute('aria-busy');
  button.classList.remove('is-busy');
}

function setFormBusy(form, isBusy, label) {
  const submitButton = form.querySelector('button[type="submit"]');
  [...form.elements].filter((control) => control !== submitButton).forEach((control) => {
    if (isBusy) {
      control.dataset.wasDisabled = String(control.disabled);
      control.disabled = true;
      return;
    }
    control.disabled = control.dataset.wasDisabled === 'true';
    delete control.dataset.wasDisabled;
  });
  setButtonBusy(submitButton, isBusy, label);
}

function setFormError(form, message = '') {
  let feedback = form.querySelector('.form-feedback');
  if (!message) {
    feedback?.remove();
    return;
  }
  if (!feedback) {
    feedback = document.createElement('p');
    feedback.className = 'form-feedback';
    feedback.setAttribute('role', 'alert');
    form.append(feedback);
  }
  feedback.textContent = message;
}

function confirmAction({ title, message, confirmLabel, tone = 'default', requireText = '' }) {
  confirmDialog.querySelector('[data-confirm-title]').textContent = title;
  confirmDialog.querySelector('[data-confirm-message]').textContent = message;
  const confirmButton = confirmDialog.querySelector('[value="confirm"]');
  const phraseWrap = confirmDialog.querySelector('[data-confirm-phrase-wrap]');
  const phrase = confirmDialog.querySelector('[data-confirm-phrase]');
  const phraseInput = confirmDialog.querySelector('[data-confirm-phrase-input]');
  confirmButton.textContent = confirmLabel;
  confirmDialog.dataset.tone = tone;
  confirmDialog.returnValue = '';
  phraseWrap.hidden = !requireText;
  phrase.textContent = requireText;
  phraseInput.value = '';
  phraseInput.required = Boolean(requireText);
  const syncConfirmation = () => { confirmButton.disabled = Boolean(requireText) && phraseInput.value.trim() !== requireText; };
  phraseInput.oninput = syncConfirmation;
  syncConfirmation();
  return new Promise((resolve) => {
    confirmDialog.addEventListener('close', () => {
      phraseInput.oninput = null;
      resolve(confirmDialog.returnValue === 'confirm');
    }, { once: true });
    confirmDialog.showModal();
    if (requireText) phraseInput.focus();
  });
}

function setSaving(isSaving) {
  document.querySelectorAll('.save-state').forEach((state) => state.classList.toggle('is-saving', isSaving));
}

function todayIso() {
  return new Date().toISOString().slice(0, 10);
}

function formatDate(date, withYear = false) {
  const dateValue = String(date ?? '').slice(0, 10);
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', ...(withYear ? { year: 'numeric' } : {}),
  }).format(new Date(`${dateValue}T00:00:00`));
}

function dateInputValue(date) {
  return String(date ?? '').slice(0, 10);
}

function escapeHtml(value) {
  return String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character]));
}

function checkIcon() {
  return '<svg><use href="#icon-check"/></svg>';
}

function renderQuickCheck(activity) {
  const complete = activity.is_completed ?? activity.is_completed_today;
  return `<button class="quick-check${complete ? ' is-done' : ''}" data-activity-id="${escapeHtml(activity.id)}" aria-pressed="${complete}">
    <span class="check-mark">${checkIcon()}</span><span><strong>${escapeHtml(activity.name)}</strong><small>${complete ? 'Sudah dicatat' : 'Belum dicatat'}</small></span>
  </button>`;
}

function renderFullCheck(activity) {
  const complete = activity.is_completed ?? activity.is_completed_today;
  return `<div class="checklist-item${complete ? ' is-done' : ''}">
    <button class="square-check" data-activity-id="${escapeHtml(activity.id)}" aria-label="${complete ? 'Batalkan catatan' : 'Catat'} ${escapeHtml(activity.name)}" aria-pressed="${complete}">${checkIcon()}</button>
    <div><strong>${escapeHtml(activity.name)}</strong><small>Target periode: ${escapeHtml(activity.target_count)} kali</small></div>
    <span class="item-note">${complete ? 'Dicatat' : 'Belum dicatat'}</span>
  </div>`;
}

function updateTodayProgress() {
  const checks = [...document.querySelectorAll('.quick-check')];
  const done = checks.filter((check) => check.classList.contains('is-done')).length;
  document.querySelector('#daily-done').textContent = done;
  document.querySelector('#daily-line').style.width = `${checks.length === 0 ? 0 : (done / checks.length) * 100}%`;
  document.querySelector('#daily-copy').textContent = checks.length === 0
    ? 'Belum ada amalan untuk ditampilkan pada tanggal ini.'
    : done === checks.length ? 'Semua amalan pada tanggal ini sudah tercatat.' : `${done} amalan pada tanggal ini sudah tercatat.`;
}

function setChecklistState(button, complete) {
  if (button.classList.contains('quick-check')) {
    button.classList.toggle('is-done', complete);
    button.setAttribute('aria-pressed', String(complete));
    button.querySelector('small').textContent = complete ? 'Sudah dicatat' : 'Belum dicatat';
    return;
  }

  const item = button.closest('.checklist-item');
  item.classList.toggle('is-done', complete);
  button.setAttribute('aria-pressed', String(complete));
  item.querySelector('.item-note').textContent = complete ? 'Dicatat' : 'Belum dicatat';
}

function syncChecklist(activityId, complete) {
  document.querySelectorAll(`[data-activity-id="${CSS.escape(activityId)}"]`).forEach((button) => setChecklistState(button, complete));
  updateTodayProgress();
}

function setChecklistEmpty(message, title = 'Catatan LKS belum tersedia') {
  const empty = `<p class="muted" role="status">${escapeHtml(message)}</p>`;
  quickChecks.innerHTML = empty;
  fullChecklist.innerHTML = empty;
  document.querySelector('.full-checklist .section-head h2').textContent = title;
  document.querySelector('.full-checklist .save-state').hidden = true;
  const activityList = document.querySelector('.dashboard-santri .activity-list');
  if (activityList) activityList.innerHTML = empty;
  updateTodayProgress();
}

function setChecklistReady() {
  document.querySelector('.full-checklist .section-head h2').textContent = 'Catatan hari ini';
  document.querySelector('.full-checklist .save-state').hidden = false;
}

function canUsePersonalLks(roles = []) {
  return !roles.includes('admin') && roles.some((role) => ['santri', 'leader'].includes(role));
}

function renderPersonalSummaryUnavailable(message) {
  document.querySelector('.score-box').hidden = true;
  document.querySelector('.period-summary').innerHTML = `<h3>Ringkasan belum tersedia.</h3><p class="muted">${escapeHtml(message)}</p>`;
}

function renderPersonalSummaryLoading() {
  document.querySelector('.score-box').hidden = true;
  document.querySelector('.period-summary').innerHTML = '<h3>Menyiapkan ringkasan.</h3><p class="muted">Capaian periode Anda sedang dimuat.</p>';
}

function applyDashboard(data) {
  const viewerChanged = viewer?.id !== data.viewer.id;
  viewer = data.viewer;
  if (viewerChanged) applyViewerIdentity(data.viewer);
  if (!hasConfiguredRole(data.viewer.roles ?? [])) return;
  if (!canUsePersonalLks(data.viewer.roles ?? [])) {
    participantId = null;
    activePeriodActivities = [];
    document.querySelector('.date-rail').innerHTML = '<p class="date-note">Akun Admin tidak memiliki catatan LKS pribadi.</p>';
    setChecklistEmpty('Admin menggunakan LKS untuk pemantauan dan koreksi, bukan pencatatan amalan pribadi.');
    renderPersonalSummaryUnavailable('Rekap pribadi hanya tersedia untuk Santri Karya dan Leader.');
    return;
  }
  selectedChecklistDate = data.selected_date ?? todayIso();
  activePeriodActivities = data.activities ?? [];
  document.querySelector('#dashboard-date').textContent = formatDate(selectedChecklistDate, true);
  document.querySelector('#register-date').textContent = formatDate(selectedChecklistDate);
  document.querySelector('#full-checklist-date').textContent = formatDate(selectedChecklistDate);
  document.querySelector('#dashboard-greeting').textContent = `Assalamu'alaikum, ${data.viewer.name.split(' ')[0]}.`;
  document.querySelector('.date-rail').innerHTML = '<p class="date-note">Pilih tanggal dalam periode untuk melihat atau mencatat aktivitas.</p>';
  renderPersonalSummaryLoading();

  if (data.period === null) {
    participantId = null;
    setChecklistEmpty('Belum ada periode LKS aktif. Hubungi Admin untuk mengaktifkan periode.');
    renderPersonalSummaryUnavailable('Belum ada periode LKS aktif untuk diringkas.');
    renderUnavailableDashboard('Belum ada periode aktif untuk diringkas.');
    return;
  }

  document.querySelector('#sidebar-period-name').textContent = data.period.name;
  document.querySelector('#dashboard-period-name').textContent = data.period.name;
  document.querySelector('#sidebar-period-end').textContent = `Berakhir ${formatDate(data.period.end_date)}`;

  if (data.period.is_open === false) {
    participantId = null;
    document.querySelector('.date-rail').innerHTML = `<p class="date-note">Periode ini telah disiapkan dan pencatatan akan dibuka pada ${formatDate(data.period.start_date)}.</p>`;
    setChecklistEmpty(`Periode ${data.period.name} belum dimulai. Checklist dapat diisi mulai ${formatDate(data.period.start_date)}.`);
    renderPersonalSummaryUnavailable(`Ringkasan tersedia setelah periode ${data.period.name} dimulai.`);
    renderUnavailableDashboard(`Periode ${data.period.name} dijadwalkan dimulai pada ${formatDate(data.period.start_date)}.`);
    return;
  }

  if (data.participant === null) {
    participantId = null;
    const message = `Anda belum didaftarkan sebagai peserta ${data.period.name}. Hubungi Admin LKS untuk ditambahkan; catatan dapat dibuat mulai tanggal pendaftaran.`;
    document.querySelector('.date-rail').innerHTML = `<p class="date-note">${escapeHtml(message)}</p>`;
    setChecklistEmpty(message);
    renderPersonalSummaryUnavailable(message);
    return;
  }

  participantId = data.participant.id;
  const maxDate = [todayIso(), dateInputValue(data.period.end_date)].sort()[0];
  document.querySelector('.date-rail').innerHTML = `<label class="checklist-date-control">Tanggal pencatatan<input type="date" data-checklist-date value="${escapeHtml(selectedChecklistDate)}" min="${escapeHtml(dateInputValue(data.participant.participation_start_date))}" max="${escapeHtml(maxDate)}"></label><p class="date-note">Checklist tersimpan berdasarkan tanggal yang dipilih.</p>`;
  quickChecks.innerHTML = data.activities.slice(0, 4).map(renderQuickCheck).join('');
  fullChecklist.innerHTML = data.activities.map(renderFullCheck).join('');
  setChecklistReady();
  if (data.personal_summary) renderPersonalSummary(data.personal_summary);
  updateTodayProgress();
}

async function apiError(response) {
  const body = await response.json().catch(() => ({}));
  const validationMessage = Object.values(body.errors ?? {}).flat()[0];
  if (validationMessage) return validationMessage;
  if (response.status === 404) return 'Data yang dipilih sudah tidak tersedia. Muat ulang data dan coba kembali.';
  if (response.status >= 500) return 'Server belum dapat memproses perubahan. Coba lagi beberapa saat lagi.';
  return body.message ?? 'Perubahan belum dapat disimpan. Coba lagi.';
}

function percentage(value) {
  return `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(Number(value) || 0)}%`;
}

function initials(name) {
  return String(name).split(' ').filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
}

function statusBadge(status) {
  const complete = status === 'tuntas';
  return `<span class="status ${complete ? 'status-done' : 'status-open'}">${complete ? 'Tuntas' : 'Belum tuntas'}</span>`;
}

function renderActivityProgress(activity) {
  const progress = Math.min(Math.max(Number(activity.percentage) || 0, 0), 100);
  const complete = activity.status === 'tuntas';
  return `<article class="activity-row"><div class="activity-name"><span class="activity-dot${complete ? ' completed' : ''}"></span><strong>${escapeHtml(activity.name)}</strong><small>${escapeHtml(activity.completed_count)} dari target ${escapeHtml(activity.target_count)} kali</small></div><div class="activity-track"><span style="width:${progress}%"></span></div><strong class="activity-value">${percentage(progress)}</strong>${statusBadge(activity.status)}</article>`;
}

function renderPersonalSummary(personal) {
  if (!personal || !canUsePersonalLks(viewer?.roles ?? [])) return;

  const activityList = document.querySelector('.dashboard-santri .activity-list');
  if (activityList) activityList.innerHTML = personal.activities.map(renderActivityProgress).join('') || '<p class="muted">Belum ada aktivitas pada periode ini.</p>';

  const score = percentage(personal.final_percentage);
  const status = personal.final_status === 'tuntas' ? 'Tuntas' : 'Belum tuntas';
  const scoreBox = document.querySelector('.score-box');
  scoreBox.hidden = false;
  scoreBox.innerHTML = `<span>Nilai sementara</span><strong>${score}</strong><small>${status}</small>`;

  const details = [
    ['Tim', viewer.identity?.team], ['Leader', viewer.identity?.leader], ['Departemen', viewer.identity?.department], ['Kategori', viewer.identity?.category], ['Level', viewer.identity?.level],
  ].filter(([, value]) => value).map(([label, value]) => `<li><span>${escapeHtml(label.slice(0, 1))}</span><div><small>${escapeHtml(label)}</small><strong>${escapeHtml(value)}</strong></div></li>`).join('');
  document.querySelector('.period-summary').innerHTML = `<h3>${personal.final_status === 'tuntas' ? 'Target periode tercapai.' : 'Masih ada ruang untuk bertumbuh.'}</h3><div class="summary-score"><strong>${score}</strong><span>Nilai sementara</span></div>${details ? `<ul>${details}</ul>` : ''}<button class="text-action" type="button" data-go="history">Lihat riwayat <svg><use href="#icon-arrow"/></svg></button>`;
}

function renderPersonalRecap() {
  if (!recapData || !canUsePersonalLks(viewer?.roles ?? [])) return;
  const personal = recapData.participants.find((participant) => participant.user_id === viewer.id);
  if (personal) renderPersonalSummary(personal);
}

function dashboardNotice(title, message, action = '') {
  return `<div class="page-intro"><div><h2>${escapeHtml(title)}</h2><p>${escapeHtml(message)}</p></div>${action}</div>`;
}

function renderRoleDashboard() {
  if (!viewer || !recapData) return;
  const role = displayRole(viewer.roles ?? []);
  const dashboard = document.querySelector(`[data-role-panel="${role}"]`);
  const { period, participants, summary } = recapData;

  if (role === 'leader') {
    const needingAttention = participants.filter((participant) => participant.final_status !== 'tuntas');
    const rows = needingAttention.slice(0, 5).map((participant, index) => `<div class="member-row"><span class="person-initials tone-${['one', 'two', 'three', 'four'][index % 4]}">${escapeHtml(initials(participant.name))}</span><div><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(participant.department ?? 'Tanpa departemen')} · ${percentage(participant.final_percentage)} capaian</small></div>${statusBadge(participant.final_status)}</div>`).join('');
    dashboard.innerHTML = `${dashboardNotice('Ringkasan anggota', `${period.name} · mulai dari anggota yang masih membutuhkan perhatian.`, '<button class="primary-button" type="button" data-go="recap">Buka rekap</button>')}<section class="leader-overview"><article><p>Anggota aktif</p><strong>${summary.participant_count}</strong><small>Dalam bimbingan Anda</small></article><article><p>Sudah tuntas</p><strong>${summary.tuntas_count}</strong><small>${summary.participant_count ? percentage((summary.tuntas_count / summary.participant_count) * 100) : '0%'} anggota</small></article><article><p>Perlu perhatian</p><strong>${needingAttention.length}</strong><small>Belum mencapai ambang tuntas</small></article></section><section class="priority-panel"><div class="section-head"><div><h2>Perlu perhatian</h2></div></div><div class="member-list">${rows || '<p class="muted">Semua anggota sudah tuntas pada periode ini.</p>'}</div></section>`;
    return;
  }

  if (role === 'admin') {
    const departments = summary.departments ?? [];
    const departmentRows = departments.map((department) => `<div><span>${escapeHtml(department.department)}</span><div class="bar-rail"><i style="width:${Math.min(Math.max(Number(department.average_percentage) || 0, 0), 100)}%"></i></div><strong>${percentage(department.average_percentage)}</strong></div>`).join('');
    dashboard.innerHTML = `${dashboardNotice('Capaian LKS organisasi', `${period.name} · tinjau kondisi periode sebelum mengubah konfigurasi.`, '<button class="primary-button" type="button" data-go="settings">Kelola LKS</button>')}<section class="admin-metrics"><article><p>Peserta aktif</p><strong>${summary.participant_count}</strong><span>Peserta periode ini</span></article><article><p>Rata-rata capaian</p><strong>${percentage(summary.average_percentage)}</strong><span>Perhitungan periode aktif</span></article><article><p>Sudah tuntas</p><strong>${summary.tuntas_count}</strong><span>Peserta mencapai ambang</span></article><article><p>Belum tuntas</p><strong>${Math.max(summary.participant_count - summary.tuntas_count, 0)}</strong><span>Perlu tindak lanjut</span></article></section><section class="department-snapshot"><div class="section-head"><div><h2>Capaian departemen</h2></div><button class="text-button" type="button" data-go="department">Lihat laporan</button></div><div class="department-bars">${departmentRows || '<p class="muted">Belum ada data departemen pada periode ini.</p>'}</div></section>`;
  }
}

function renderUnavailableDashboard(message) {
  if (!viewer) return;
  const role = displayRole(viewer.roles ?? []);
  if (role === 'santri') return;
  const dashboard = document.querySelector(`[data-role-panel="${role}"]`);
  dashboard.innerHTML = dashboardNotice('Ringkasan belum tersedia', message, '<button class="primary-button" type="button" data-go="recap">Coba buka rekap</button>');
}

function renderHistory() {
  const panel = document.querySelector('[data-view-panel="history"]');
  if (!historyData) {
    panel.innerHTML = `${dashboardNotice('Memuat riwayat', 'Riwayat periode tertutup sedang disiapkan.')} `;
    return;
  }

  if (!historyData.length) {
    panel.innerHTML = `${dashboardNotice('Belum ada riwayat periode', 'Periode yang sudah ditutup akan tersimpan di sini dan tetap dapat ditinjau sesuai akses Anda.')}`;
    return;
  }

  const selected = historyRecapData?.period?.id;
  const periodRows = historyData.map((period) => `<button class="history-period-row${period.id === selected ? ' is-selected' : ''}" type="button" data-history-period="${escapeHtml(period.id)}"><span><strong>${escapeHtml(period.name)}</strong><small>${formatDate(period.start_date, true)} — ${formatDate(period.end_date, true)}</small></span><span>${period.id === selected ? 'Ditinjau' : 'Lihat rekap'}</span></button>`).join('');
  const detail = historyRecapData ? renderHistoryDetail(historyRecapData) : '<p class="history-empty-detail">Pilih periode untuk melihat rekap akhirnya.</p>';

  panel.innerHTML = `<div class="page-intro"><div><h2>Riwayat periode</h2><p>Rekap periode yang sudah ditutup tetap menggunakan data dan aturan pada saat periode tersebut berjalan.</p></div><button class="text-button admin-refresh" type="button" data-history-refresh>Muat ulang data</button></div><section class="history-layout"><nav class="history-period-list" aria-label="Daftar periode tertutup">${periodRows}</nav><section class="history-detail" aria-live="polite">${detail}</section></section>`;
}

function renderHistoryDetail(data) {
  const { period, participants, summary } = data;
  const personal = canUsePersonalLks(viewer?.roles ?? []) ? participants.find((participant) => participant.user_id === viewer?.id) : null;
  const score = personal ? `<div class="history-score"><span>Nilai akhir</span><strong>${percentage(personal.final_percentage)}</strong>${statusBadge(personal.final_status)}</div>` : `<div class="history-score"><span>Peserta sesuai akses</span><strong>${summary.participant_count}</strong><small>${summary.tuntas_count} tuntas · Rata-rata ${percentage(summary.average_percentage)}</small></div>`;
  const activities = personal?.activities?.map((activity) => `<li><span>${escapeHtml(activity.name)}</span><strong>${activity.completed_count}/${activity.target_count} · ${percentage(activity.percentage)}</strong></li>`).join('');
  return `<div class="history-detail-head"><span class="status status-closed">Ditutup</span><h3>${escapeHtml(period.name)}</h3><p>${formatDate(period.start_date, true)} — ${formatDate(period.end_date, true)}</p></div>${score}${activities ? `<ul class="history-activity-list">${activities}</ul>` : '<p class="history-empty-detail">Ringkasan ini menampilkan hasil peserta yang berada dalam cakupan akses Anda.</p>'}`;
}

async function loadHistory({ showProgress = false } = {}) {
  if (showProgress) showPageProgress('Memuat riwayat periode…');
  try {
    const response = await fetch(`${apiBase}/periods/history`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    historyData = (await response.json()).data;
    renderHistory();
  } catch (error) {
    document.querySelector('[data-view-panel="history"]').innerHTML = `${dashboardNotice('Riwayat belum dapat dimuat', escapeHtml(error.message || 'Coba lagi beberapa saat lagi.'), '<button class="primary-button" type="button" data-history-refresh>Coba lagi</button>')}`;
  } finally {
    if (showProgress) hidePageProgress();
  }
}

async function loadHistoryRecap(periodId, trigger) {
  setButtonBusy(trigger, true, 'Memuat…');
  try {
    const scope = canUsePersonalLks(viewer?.roles ?? []) ? '&scope=personal' : '';
    const response = await fetch(`${apiBase}/recap?period_id=${encodeURIComponent(periodId)}${scope}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    historyRecapData = (await response.json()).data;
    renderHistory();
  } catch (error) {
    showToast(error.message || 'Rekap periode belum dapat dimuat.', 'error');
  } finally {
    setButtonBusy(trigger, false);
  }
}

function renderRecapUnavailable(message) {
  const safeMessage = escapeHtml(message);
  document.querySelector('[data-view-panel="recap"]').innerHTML = `<div class="page-intro"><div><h2>Rekap belum tersedia</h2><p>${safeMessage}</p></div></div><p class="empty-search">Coba lagi setelah periode dan peserta tersedia.</p>`;
  document.querySelector('[data-view-panel="department"]').innerHTML = `<div class="page-intro"><div><h2>Capaian departemen</h2><p>${safeMessage}</p></div></div><p class="empty-search">Coba lagi setelah periode dan peserta tersedia.</p>`;
}

function renderRecapViews() {
  const recapPanel = document.querySelector('[data-view-panel="recap"]');
  const departmentPanel = document.querySelector('[data-view-panel="department"]');
  if (!recapData) return;

  const { period, participants, summary } = recapData;
  const canCorrect = viewer?.roles?.includes('admin') && period.status === 'active';
  const recapRows = participants.map((participant, index) => `<div class="table-row" data-member="${escapeHtml(`${participant.name} ${participant.department ?? ''}`)}" data-status="${escapeHtml(participant.final_status)}" data-gender="${escapeHtml(participant.gender)}"><div class="member-cell"><span class="person-initials tone-${['one', 'two', 'three', 'four'][index % 4]}">${escapeHtml(initials(participant.name))}</span><div><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(participant.team ?? 'Tanpa tim')} · Leader: ${escapeHtml(participant.leader ?? 'Belum ditetapkan')}</small></div></div><span>${escapeHtml(participant.department ?? 'Tanpa departemen')}</span><strong>${percentage(participant.final_percentage)}</strong>${statusBadge(participant.final_status)}${canCorrect ? `<button class="text-button organization-row-action" type="button" data-open-checklist-correction="${escapeHtml(participant.participant_id)}">Koreksi</button>` : '<span aria-hidden="true"></span>'}</div>`).join('');
  const periodOptions = recapPeriods.map((item) => `<option value="${escapeHtml(item.id)}"${item.id === period.id ? ' selected' : ''}>${escapeHtml(item.name)}${item.status === 'closed' ? ' · Ditutup' : ' · Aktif'}</option>`).join('');

  recapPanel.innerHTML = `<div class="page-intro recap-intro"><div><h2>Perkembangan anggota</h2><p>${escapeHtml(period.name)} · ${summary.participant_count} peserta dalam cakupan akses Anda.</p></div><label class="report-period-control">Periode<select data-recap-period>${periodOptions}</select></label></div><div class="filter-bar"><label class="search-field"><svg aria-hidden="true"><use href="#icon-search"/></svg><span class="sr-only">Cari anggota</span><input id="member-search" type="search" aria-label="Cari anggota" placeholder="Cari nama anggota" /></label><button class="filter-pill is-on" type="button" data-recap-filter="all">Semua status</button><button class="filter-pill" type="button" data-recap-filter="belum_tuntas">Belum tuntas</button><button class="filter-pill" type="button" data-recap-filter="tuntas">Tuntas</button><button class="filter-pill is-on" type="button" data-recap-gender="all">Semua gender</button><button class="filter-pill" type="button" data-recap-gender="ikhwan">Ikhwan</button><button class="filter-pill" type="button" data-recap-gender="akhwat">Akhwat</button></div><section class="recap-table" aria-label="Rekap anggota"><div class="table-head"><span>Santri Karya</span><span>Departemen</span><span>Nilai</span><span>Status</span><span>${canCorrect ? 'Aksi' : ''}</span></div><div id="recap-rows">${recapRows || '<p class="empty-search">Belum ada peserta pada periode ini.</p>'}</div></section><p class="empty-search" id="empty-search" hidden>Tidak ada anggota yang sesuai dengan pencarian atau filter tersebut.</p>`;

  if (!viewer?.roles?.includes('admin')) {
    departmentPanel.innerHTML = `<div class="page-intro"><div><h2>Capaian departemen</h2><p>Ringkasan lintas departemen tersedia untuk Admin.</p></div></div><p class="empty-search">Gunakan akun Admin untuk melihat perbandingan capaian tiap departemen.</p>`;
    return;
  }

  const departments = summary.departments ?? [];
  const departmentBars = departments.map((department) => `<div class="chart-group"><div class="bars"><i class="now" style="height:${Math.min(Math.max(Number(department.average_percentage) || 0, 0), 100)}%"></i></div><strong>${escapeHtml(department.department)}</strong></div>`).join('');
  const departmentRows = departments.map((department) => `<div class="department-row"><strong>${escapeHtml(department.department)}</strong><span>${percentage(department.average_percentage)} capaian</span><span class="trend up">${escapeHtml(department.tuntas_count)} dari ${escapeHtml(department.participant_count)} tuntas</span></div>`).join('');
  const trendPeriods = departmentTrendData.slice(-3);
  const departmentNames = [...new Set(departmentTrendData.flatMap((item) => item.departments.map((department) => department.department)))];
  const trendRows = departmentNames.map((name) => `<div class="department-history-row"><strong>${escapeHtml(name)}</strong>${trendPeriods.map((item) => `<span><small>${escapeHtml(item.period.name)}</small>${percentage(item.departments.find((department) => department.department === name)?.average_percentage ?? 0)}</span>`).join('')}</div>`).join('');
  departmentPanel.innerHTML = `<div class="page-intro"><div><h2>Capaian departemen</h2><p>${escapeHtml(period.name)} · Rekap periode dapat dipilih dan dibandingkan dengan riwayat yang tersedia.</p></div><label class="report-period-control">Periode<select data-recap-period>${periodOptions}</select></label></div><section class="chart-panel"><div class="chart-legend"><span><i class="legend-mark now"></i>Rata-rata ${escapeHtml(period.name)}</span></div><div class="bar-chart" role="img" aria-label="Grafik capaian rata-rata per departemen"><div class="chart-scale"><span>100</span><span>75</span><span>50</span><span>25</span><span>0</span></div><div class="chart-groups">${departmentBars}</div></div></section><section class="department-table"><div class="section-head"><div><h2>Rincian per departemen</h2></div></div>${departmentRows || '<p class="empty-search">Belum ada data departemen pada periode ini.</p>'}</section>${trendRows ? `<section class="department-history"><div class="section-head"><div><h2>Perbandingan riwayat</h2><p>Tiga periode terakhir yang tersedia.</p></div></div>${trendRows}</section>` : ''}`;
}

async function loadRecap(periodId = '', { showProgress = true } = {}) {
  if (showProgress) showPageProgress('Memuat rekap…');
  try {
    const recapUrl = periodId ? `${apiBase}/recap?period_id=${encodeURIComponent(periodId)}` : `${apiBase}/recap`;
    const [response, historyResponse, trendResponse] = await Promise.all([
      fetch(recapUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
      fetch(`${apiBase}/periods/history`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
      viewer?.roles?.includes('admin') ? fetch(`${apiBase}/department-trends`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }) : Promise.resolve(null),
    ]);
    if (!response.ok) throw new Error(await apiError(response));
    recapData = (await response.json()).data;
    recapPeriods = [recapData.period];
    if (historyResponse.ok) {
      const periods = (await historyResponse.json()).data;
      recapPeriods.push(...periods.filter((item) => item.id !== recapData.period.id));
    }
    departmentTrendData = trendResponse?.ok ? (await trendResponse.json()).data : [];
    renderPersonalRecap();
    renderRecapViews();
    renderRoleDashboard();
  } catch (error) {
    recapData = null;
    renderRecapUnavailable(error.message || 'Data rekap belum dapat dimuat.');
    renderUnavailableDashboard(error.message || 'Data rekap belum dapat dimuat.');
  } finally {
    if (showProgress) hidePageProgress();
  }
}

async function persistChecklist(button) {
  if (participantId === null) {
    showToast('Checklist belum dapat diisi pada periode ini.');
    return;
  }

  const complete = !button.classList.contains('quick-check')
    ? !button.closest('.checklist-item').classList.contains('is-done')
    : !button.classList.contains('is-done');
  const activityId = button.dataset.activityId;
  button.disabled = true;
  button.classList.add('is-busy');
  button.setAttribute('aria-busy', 'true');
  setSaving(true);

  try {
    const response = await fetch(`${apiBase}/checklists`, {
      method: 'PUT',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) },
      body: JSON.stringify({ period_activity_id: activityId, participant_id: participantId, checklist_date: selectedChecklistDate, is_completed: complete }),
    });

    if (!response.ok) throw new Error(await apiError(response));

    syncChecklist(activityId, complete);
    showToast(complete ? 'Amalan dicatat' : 'Catatan diperbarui');
  } catch (error) {
    showToast(error.message || 'Perubahan belum tersimpan. Periksa koneksi lalu coba lagi.', 'error');
  } finally {
    button.disabled = false;
    button.classList.remove('is-busy');
    button.removeAttribute('aria-busy');
    setSaving(false);
  }
}

quickChecks.addEventListener('click', (event) => {
  const button = event.target.closest('.quick-check[data-activity-id]');
  if (button) persistChecklist(button);
});

fullChecklist.addEventListener('click', (event) => {
  const button = event.target.closest('.square-check[data-activity-id]');
  if (button) persistChecklist(button);
});

function displayRole(roles) {
  if (roles.includes('admin')) return 'admin';
  if (roles.includes('leader')) return 'leader';
  return 'santri';
}

function hasConfiguredRole(roles) {
  return roles.some((role) => ['admin', 'leader', 'santri'].includes(role));
}

function canView(view, roles) {
  const isAdmin = roles.includes('admin');
  const isLeader = roles.includes('leader');
  const hasPersonalLks = canUsePersonalLks(roles);
  return {
    dashboard: true,
    account: true,
    lks: hasPersonalLks,
    recap: isAdmin || isLeader,
    department: isAdmin,
    history: hasPersonalLks,
    settings: isAdmin,
  }[view] ?? false;
}

function applyViewerIdentity(currentViewer) {
  const roles = currentViewer.roles ?? [];
  if (!hasConfiguredRole(roles)) {
    document.querySelector('#account-name').textContent = currentViewer.name;
    document.querySelector('#account-role').textContent = 'Akses belum dikonfigurasi';
    navButtons.forEach((button) => { button.hidden = !['dashboard', 'account'].includes(button.dataset.view); });
    document.querySelectorAll('.nav-admin, .admin-only, [data-role-panel]').forEach((item) => { item.hidden = true; });
    document.querySelector('[data-view-panel="dashboard"]').innerHTML = dashboardNotice(
      'Akses LKS belum siap',
      'Akun Anda belum memiliki peran LKS. Hubungi Admin LKS agar akses Santri Karya dapat diaktifkan, lalu muat ulang halaman ini.',
    );
    return;
  }
  const role = displayRole(roles);
  const roleLabels = { santri: 'Santri Karya', leader: 'Leader', admin: 'Admin' };
  document.querySelector('#account-name').textContent = currentViewer.name;
  document.querySelector('#account-role').textContent = roleLabels[role];
  document.querySelectorAll('[data-role-panel]').forEach((panel) => { panel.hidden = panel.dataset.rolePanel !== role; });
  navButtons.forEach((button) => {
    button.hidden = !canView(button.dataset.view, roles);
  });
  document.querySelectorAll('.nav-admin, .admin-only').forEach((item) => { item.hidden = !roles.includes('admin'); });
  historyData = null;
  historyRecapData = null;
  renderHistory();

  const activeNavigation = [...navButtons].find((button) => button.classList.contains('is-active'));
  if (activeNavigation?.hidden) openView('dashboard');
}

async function logout() {
  setButtonBusy(logoutButton, true, 'Keluar…');
  try {
    const response = await fetch('/logout', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) },
    });
    if (!response.ok) throw new Error('Sesi belum dapat diakhiri. Coba lagi.');
    window.location.assign('/login');
  } catch (error) {
    showToast(error.message, 'error');
    setButtonBusy(logoutButton, false);
  }
}

async function requestLogout() {
  const confirmed = await confirmAction({
    title: 'Keluar dari LKS?',
    message: 'Anda akan keluar dari sesi pada perangkat ini. Masuk kembali diperlukan untuk melanjutkan pencatatan.',
    confirmLabel: 'Keluar',
  });
  if (confirmed) logout();
}

logoutButton.addEventListener('click', requestLogout);

function accountPasswordField(id, name, label, autocomplete) {
  return `<label class="account-field">${label}<span class="account-password-control"><input id="${id}" name="${name}" type="password" minlength="8" autocomplete="${autocomplete}" required><button type="button" class="account-password-toggle" data-toggle-account-password="${id}" aria-label="Tampilkan ${label.toLowerCase()}" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.8"/></svg></button></span></label>`;
}

function renderAccount(data) {
  accountData = data;
  const panel = document.querySelector('[data-view-panel="account"]');
  panel.innerHTML = `<div class="page-intro"><div><h2>Akun Saya</h2><p>Kelola identitas akun dan keamanan password Anda. Penempatan, role, dan status akun diatur oleh Admin LKS.</p></div></div><div class="account-layout"><section class="account-section" aria-labelledby="account-profile-title"><div><h2 id="account-profile-title">Informasi akun</h2><p>Nama dan email dipakai untuk identitas serta proses masuk ke LKS.</p></div><form class="account-form" data-account-form="profile"><div class="account-fields"><label class="account-field">Nama<input name="name" maxlength="150" autocomplete="name" value="${escapeHtml(data.name)}" required></label><label class="account-field">Email<input name="email" type="email" maxlength="255" autocomplete="email" value="${escapeHtml(data.email)}" required></label></div><div class="account-actions"><button class="primary-button" type="submit">Simpan informasi</button></div></form></section><section class="account-section" aria-labelledby="account-password-title"><div><h2 id="account-password-title">Ganti password</h2><p>Masukkan password saat ini sebelum memilih password baru.</p></div><form class="account-form" data-account-form="password"><div class="account-fields">${accountPasswordField('account-current-password', 'current_password', 'Password saat ini', 'current-password')}${accountPasswordField('account-password', 'password', 'Password baru', 'new-password')}${accountPasswordField('account-password-confirmation', 'password_confirmation', 'Konfirmasi password baru', 'new-password')}</div><p class="account-help">Gunakan minimal 8 karakter dan simpan password baru Anda di tempat yang aman.</p><div class="account-actions"><button class="primary-button" type="submit">Perbarui password</button></div></form></section></div>`;
}

async function loadAccount({ showProgress = false } = {}) {
  const panel = document.querySelector('[data-view-panel="account"]');
  if (showProgress) showPageProgress('Memuat akun…');
  panel.setAttribute('aria-busy', 'true');
  try {
    const response = await fetch(`${apiBase}/account`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    renderAccount((await response.json()).data);
  } catch (error) {
    panel.innerHTML = `${dashboardNotice('Akun belum dapat dimuat', error.message || 'Coba lagi beberapa saat lagi.', '<button class="primary-button" type="button" data-account-refresh>Coba lagi</button>')}`;
  } finally {
    panel.removeAttribute('aria-busy');
    if (showProgress) hidePageProgress();
  }
}

async function submitAccountForm(form) {
  const type = form.dataset.accountForm;
  const endpoint = type === 'password' ? '/account/password' : '/account/profile';
  const payload = Object.fromEntries(new FormData(form).entries());
  setFormError(form);
  setFormBusy(form, true, type === 'password' ? 'Memperbarui…' : 'Menyimpan…');
  try {
    const response = await fetch(`${apiBase}${endpoint}`, {
      method: 'PUT', credentials: 'same-origin',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) },
      body: JSON.stringify(payload),
    });
    if (!response.ok) throw new Error(await apiError(response));
    const data = (await response.json()).data;
    if (type === 'profile') {
      accountData = { ...accountData, ...data };
      viewer = { ...viewer, name: data.name };
      document.querySelector('#account-name').textContent = data.name;
      renderAccount(accountData);
      showToast('Informasi akun diperbarui');
      return;
    }
    form.reset();
    showToast('Password berhasil diperbarui');
  } catch (error) {
    const message = error.message || 'Perubahan akun belum dapat disimpan. Coba lagi.';
    setFormError(form, message);
    showToast(message, 'error');
  } finally {
    setFormBusy(form, false);
  }
}

document.addEventListener('submit', (event) => {
  const form = event.target.closest('[data-account-form]');
  if (!form) return;
  event.preventDefault();
  submitAccountForm(form);
});

document.addEventListener('click', (event) => {
  const toggle = event.target.closest('[data-toggle-account-password]');
  if (toggle) {
    const input = document.querySelector(`#${CSS.escape(toggle.dataset.toggleAccountPassword)}`);
    if (!input) return;
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    toggle.setAttribute('aria-pressed', String(!visible));
    toggle.setAttribute('aria-label', `${visible ? 'Tampilkan' : 'Sembunyikan'} ${input.closest('label')?.childNodes[0]?.textContent?.trim() ?? 'password'}`);
    input.focus({ preventScroll: true });
    return;
  }
  if (event.target.closest('[data-account-refresh]')) loadAccount({ showProgress: true });
});

document.addEventListener('input', (event) => {
  if (event.target.matches('#member-search')) filterRecapRows();
  if (event.target.matches('[data-checklist-date]')) loadDashboard({ date: event.target.value, reloadRecap: false });
});

function filterRecapRows() {
  const search = document.querySelector('#member-search');
  const selected = document.querySelector('[data-recap-filter].is-on');
  const selectedGender = document.querySelector('[data-recap-gender].is-on');
  if (!search || !selected || !selectedGender) return;
  const term = search.value.toLowerCase().trim();
  const status = selected.dataset.recapFilter;
  let visible = 0;
  document.querySelectorAll('#recap-rows .table-row').forEach((row) => {
    const matches = row.dataset.member.toLowerCase().includes(term)
      && (status === 'all' || row.dataset.status === status)
      && (selectedGender.dataset.recapGender === 'all' || row.dataset.gender === selectedGender.dataset.recapGender);
    row.hidden = !matches;
    if (matches) visible += 1;
  });
  const empty = document.querySelector('#empty-search');
  if (empty) empty.hidden = visible !== 0;
}

document.addEventListener('click', (event) => {
  const pill = event.target.closest('[data-recap-filter]');
  if (pill) {
    document.querySelectorAll('[data-recap-filter]').forEach((item) => item.classList.toggle('is-on', item === pill));
    filterRecapRows();
    return;
  }
  const gender = event.target.closest('[data-recap-gender]');
  if (gender) {
    document.querySelectorAll('[data-recap-gender]').forEach((item) => item.classList.toggle('is-on', item === gender));
    filterRecapRows();
  }
});

document.addEventListener('change', (event) => {
  if (event.target.matches('[data-recap-period]')) loadRecap(event.target.value, { showProgress: false });
});

const settingRoutes = { 'Kelola periode': 'periods', 'Kelola aktivitas': 'activities', 'Kelola Santri': 'people' };
document.querySelectorAll('.settings-grid .text-action').forEach((button) => button.addEventListener('click', () => openView(settingRoutes[button.textContent.trim()])));
document.querySelectorAll('[data-demo]').forEach((button) => button.addEventListener('click', () => showToast(`${button.dataset.demo} tersedia setelah integrasi backend`)));
const adminListState = { organization: { page: 1, search: '' }, people: { page: 1, search: '' }, periods: { page: 1, search: '' }, activities: { page: 1, search: '' } };
let latestAdminRequest = 0;

function adminOptions(items, selected = '') {
  return `<option value="">Pilih</option>${items.map((item) => `<option value="${escapeHtml(item.id)}"${item.id === selected ? ' selected' : ''}>${escapeHtml(item.name)}</option>`).join('')}`;
}

function weekdayPicker(selected = []) {
  const activeDays = Array.isArray(selected) ? selected.map(Number) : String(selected ?? '').replace(/[{}]/g, '').split(',').filter(Boolean).map(Number);
  const days = [[1, 'Sen'], [2, 'Sel'], [3, 'Rab'], [4, 'Kam'], [5, 'Jum'], [6, 'Sab'], [7, 'Min']];
  return `<fieldset class="weekday-picker"><legend>Jadwal pencatatan</legend><small>Kosongkan semua bila aktivitas dapat dicatat setiap hari.</small><span>${days.map(([value, label]) => `<label><input type="checkbox" name="allowed_weekdays" value="${value}"${activeDays.includes(value) ? ' checked' : ''}> ${label}</label>`).join('')}</span></fieldset>`;
}

function paginationControls(meta, list) {
  if (!meta || meta.last_page <= 1) return '';
  const from = ((meta.current_page - 1) * meta.per_page) + 1;
  const to = Math.min(meta.current_page * meta.per_page, meta.total);
  return `<nav class="admin-pagination" aria-label="Navigasi halaman data"><span>Menampilkan ${from}–${to} dari ${meta.total} data</span><div><button class="text-button" type="button" data-admin-page="${list}" data-page="${meta.current_page - 1}"${meta.current_page === 1 ? ' disabled' : ''}>Sebelumnya</button><strong>Halaman ${meta.current_page} / ${meta.last_page}</strong><button class="text-button" type="button" data-admin-page="${list}" data-page="${meta.current_page + 1}"${meta.current_page === meta.last_page ? ' disabled' : ''}>Berikutnya</button></div></nav>`;
}

function adminPanel(title, description, content, actions = '', showRefresh = true) {
  return `<div class="page-intro"><div><h2>${escapeHtml(title)}</h2><p>${escapeHtml(description)}</p></div><div class="admin-page-actions">${actions}${showRefresh ? '<button class="text-button admin-refresh" type="button" data-admin-refresh>Muat ulang data</button>' : ''}</div></div><section class="admin-workbench">${content}</section>`;
}

function renderAdminView(name) {
  const panel = document.querySelector(`[data-view-panel="${name}"]`);
  if (!viewer?.roles?.includes('admin')) {
    panel.innerHTML = adminPanel('Akses terbatas', 'Halaman ini hanya tersedia untuk Admin.', '<p class="admin-empty">Gunakan akun Admin untuk mengelola konfigurasi LKS.</p>');
    return;
  }
  const needsOrganization = ['settings', 'organization', 'people'].includes(name);
  const needsConfiguration = ['settings', 'periods', 'activities'].includes(name);
  if ((needsOrganization && organizationData === null) || (needsConfiguration && configurationData === null)) {
    panel.innerHTML = adminPanel('Memuat konfigurasi', 'Data administrasi sedang disiapkan.', '<p class="admin-empty" role="status">Memuat data…</p>');
    return;
  }

  const organization = organizationData ?? { departments: [], department_options: [], santri: [], santri_options: [], leaders: [] };
  const configuration = configurationData ?? { periods: [], period_options: [], activities: [], activity_options: [] };
  const departmentOptions = organization.department_options ?? organization.departments;
  const teams = departmentOptions.flatMap((department) => department.teams.map((team) => ({ ...team, name: `${department.name} — ${team.name}`, teamName: team.name, departmentName: department.name })));
  const organizationMeta = organization.department_pagination ?? { total: organization.departments.length, current_page: 1, last_page: 1, per_page: 25 };
  const peopleMeta = organization.santri_pagination ?? { total: organization.santri.length, current_page: 1, last_page: 1, per_page: 25 };
  const periodMeta = configuration.period_pagination ?? { total: configuration.periods.length, current_page: 1, last_page: 1, per_page: 25 };
  const activityMeta = configuration.activity_pagination ?? { total: configuration.activities.length, current_page: 1, last_page: 1, per_page: 25 };
  const periodOptions = configuration.period_options ?? configuration.periods;
  const activityOptions = configuration.activity_options ?? configuration.activities;
  if (name === 'settings') {
    panel.innerHTML = adminPanel('Pengaturan LKS', 'Pilih area yang ingin dikelola. Setiap area memisahkan keputusan administrasi agar lebih mudah ditinjau.', `<nav class="admin-hub-list" aria-label="Area pengaturan LKS"><button class="admin-hub-item" type="button" data-go="organization"><span><strong>Struktur Organisasi</strong><small>${organizationMeta.total} departemen · ${teams.length} tim</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button><button class="admin-hub-item" type="button" data-go="people"><span><strong>Data Santri Karya</strong><small>${peopleMeta.total} Santri Karya terdaftar</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button><button class="admin-hub-item" type="button" data-go="activities"><span><strong>Aktivitas LKS</strong><small>${activityMeta.total} aktivitas master</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button><button class="admin-hub-item" type="button" data-go="periods"><span><strong>Periode LKS</strong><small>${periodMeta.total} periode tersimpan</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button></nav>`);
    return;
  }
  if (name === 'organization') {
    const departments = organization.departments;
    const hasOrganizationSearch = Boolean(adminListState.organization.search);
    const showOrganizationEmpty = organizationMeta.total === 0 && !hasOrganizationSearch;
    const hierarchyRows = departments.map((department) => {
      const departmentActions = `<button class="text-button organization-row-action" type="button" data-open-admin-modal="department-edit" data-organization-id="${escapeHtml(department.id)}">Edit</button><button class="text-button organization-row-action" type="button" data-open-admin-modal="team" data-department-id="${escapeHtml(department.id)}"${department.is_active ? '' : ' disabled'}>Tambah tim</button>${department.is_active ? `<button class="text-button organization-row-action organization-archive-action" type="button" data-archive-organization="department" data-organization-id="${escapeHtml(department.id)}" data-organization-name="${escapeHtml(department.name)}">Arsipkan</button>` : ''}${department.teams.length === 0 && department.santri_profiles_count === 0 ? `<button class="text-button organization-row-action" type="button" data-delete-resource="department" data-resource-id="${escapeHtml(department.id)}" data-resource-name="${escapeHtml(department.name)}" data-confirm-phrase="${escapeHtml(department.code)}">Hapus</button>` : ''}`;
      const departmentRow = `<div class="organization-hierarchy-row organization-department-row" role="row"><span role="cell"><strong>${escapeHtml(department.name)}</strong><small>${department.teams.length} tim</small></span><span class="organization-code" role="cell">${escapeHtml(department.code)}</span><span role="cell">${department.santri_profiles_count} Santri Karya</span><span role="cell">${department.is_active ? 'Aktif' : 'Diarsipkan'}</span><span class="organization-row-actions" role="cell">${departmentActions}</span></div>`;
      const teamRows = department.teams.map((team) => `<div class="organization-hierarchy-row organization-team-child-row" role="row"><span role="cell"><strong>${escapeHtml(team.name)}</strong><small>Tim · ${escapeHtml(department.name)}</small></span><span class="organization-code" role="cell">${escapeHtml(team.code)}</span><span role="cell">${team.santri_profiles_count} Santri Karya</span><span role="cell">${team.is_active ? 'Aktif' : 'Diarsipkan'}</span><span class="organization-row-actions" role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="team-edit" data-organization-id="${escapeHtml(team.id)}">Edit</button>${team.is_active ? `<button class="text-button organization-row-action organization-archive-action" type="button" data-archive-organization="team" data-organization-id="${escapeHtml(team.id)}" data-organization-name="${escapeHtml(team.name)}">Arsipkan</button>` : ''}${team.santri_profiles_count === 0 ? `<button class="text-button organization-row-action" type="button" data-delete-resource="team" data-resource-id="${escapeHtml(team.id)}" data-resource-name="${escapeHtml(team.name)}" data-confirm-phrase="${escapeHtml(team.code)}">Hapus</button>` : ''}</span></div>`).join('');
      return `${departmentRow}${teamRows || '<div class="organization-no-team">Belum ada tim. Tambahkan tim dari baris departemen di atas.</div>'}`;
    }).join('');
    const hierarchyContent = showOrganizationEmpty
      ? `<section class="organization-empty-state" aria-labelledby="department-empty-title"><h3 id="department-empty-title">Belum ada departemen</h3><p>Mulai dengan membuat departemen sebagai dasar struktur organisasi. Setelah itu, Anda dapat menambahkan tim di dalamnya.</p><button class="primary-button" type="button" data-open-admin-modal="department">Tambah departemen</button></section>`
      : `<section class="admin-record-section organization-hierarchy-section" aria-labelledby="organization-list-title"><div class="organization-section-head"><div><h3 id="organization-list-title">Struktur Departemen dan Tim</h3><p>${organizationMeta.total} departemen · ${teams.length} tim.</p></div></div><form class="admin-list-search" data-admin-list-search="organization"><label class="sr-only" for="organization-search">Cari departemen atau tim</label><input id="organization-search" name="search" value="${escapeHtml(adminListState.organization.search)}" placeholder="Cari nama atau kode departemen/tim" autocomplete="off"></form>${departments.length ? `<div class="organization-hierarchy-table" role="table" aria-label="Struktur organisasi"><div class="organization-hierarchy-head" role="row"><span role="columnheader">Unit</span><span role="columnheader">Kode</span><span role="columnheader">Santri Karya</span><span role="columnheader">Status</span><span role="columnheader">Aksi</span></div>${hierarchyRows}</div>${paginationControls(organizationMeta, 'organization')}` : '<p class="admin-empty">Tidak ada unit yang sesuai dengan pencarian.</p>'}</section>`;
    const organizationActions = !showOrganizationEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="department">Tambah departemen</button>' : '';
    panel.innerHTML = adminPanel('Struktur Organisasi', 'Atur departemen dan tim sebagai dasar penempatan Santri Karya.', hierarchyContent, organizationActions, !showOrganizationEmpty);
    return;
  }
  if (name === 'people') {
    const hasTeams = teams.length > 0;
    const people = organization.santri;
    const hasPeopleSearch = Boolean(adminListState.people.search);
    const showPeopleEmpty = hasTeams && peopleMeta.total === 0 && !hasPeopleSearch;
    const peopleRows = people.map((profile) => `<div class="admin-record-row people-record-row" role="row"><span role="cell"><strong>${escapeHtml(profile.user.name)}</strong><small>${escapeHtml(profile.user.email)}</small></span><span role="cell">${escapeHtml(profile.team?.name ?? 'Tanpa tim')} · ${escapeHtml(profile.department?.name ?? 'Tanpa departemen')}</span><span role="cell">${profile.status === 'active' ? 'Aktif' : 'Nonaktif'}${profile.user.roles?.some((role) => role.code === 'leader') ? ' · Leader' : ''}</span><span role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="santri-edit" data-santri-id="${escapeHtml(profile.user_id)}">Edit</button><button class="text-button organization-row-action" type="button" data-delete-resource="santri" data-resource-id="${escapeHtml(profile.user_id)}" data-resource-name="${escapeHtml(profile.user.name)}" data-confirm-phrase="${escapeHtml(profile.user.email)}">Hapus</button></span></div>`).join('');
    const peopleContent = !hasTeams
      ? `<section class="organization-empty-state" aria-labelledby="people-prerequisite-title"><h3 id="people-prerequisite-title">Siapkan tim terlebih dahulu</h3><p>Santri Karya perlu ditempatkan di dalam tim. Buat departemen dan tim sebelum membuat akun Santri Karya.</p><button class="primary-button" type="button" data-go="organization">Kelola struktur organisasi</button></section>`
      : !showPeopleEmpty
        ? `<section class="admin-record-section" aria-labelledby="people-list-title"><div class="organization-section-head"><div><h3 id="people-list-title">Daftar Santri Karya</h3><p>${peopleMeta.total} Santri Karya terdaftar.</p></div></div><form class="admin-list-search" data-admin-list-search="people"><label class="sr-only" for="people-search">Cari Santri Karya</label><input id="people-search" name="search" value="${escapeHtml(adminListState.people.search)}" placeholder="Cari nama atau email" autocomplete="off"></form>${people.length ? `<div class="admin-record-table people-record-table" role="table" aria-label="Daftar Santri Karya"><div class="admin-record-head" role="row"><span role="columnheader">Santri Karya</span><span role="columnheader">Penempatan</span><span role="columnheader">Status akses</span><span role="columnheader">Aksi</span></div>${peopleRows}</div>${paginationControls(peopleMeta, 'people')}` : '<p class="admin-empty">Tidak ada Santri Karya yang sesuai dengan pencarian.</p>'}</section>`
        : `<section class="organization-empty-state" aria-labelledby="people-empty-title"><h3 id="people-empty-title">Belum ada Santri Karya</h3><p>Buat akun Santri Karya dan tetapkan timnya. Peserta periode dapat dikelola setelah akun tersedia.</p><button class="primary-button" type="button" data-open-admin-modal="santri">Tambah Santri Karya</button></section>`;
    const peopleActions = hasTeams && !showPeopleEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="santri">Tambah Santri Karya</button>' : '';
    panel.innerHTML = adminPanel('Data Santri Karya', 'Buat akun dan tetapkan penempatan organisasinya. Peserta periode dikelola dari Periode LKS.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${peopleContent}`, peopleActions, !showPeopleEmpty && hasTeams);
    return;
  }
  if (name === 'periods') {
    const activePeriods = periodOptions.filter((period) => period.status === 'active');
    const hasPeriodSearch = Boolean(adminListState.periods.search);
    const showPeriodEmpty = periodMeta.total === 0 && !hasPeriodSearch;
    const periodRows = configuration.periods.map((period) => {
      const activeActivityCount = period.period_activities.filter((activity) => activity.is_active).length;
      const periodAction = period.status === 'draft'
        ? `<button class="text-button organization-row-action" type="button" data-open-admin-modal="period-edit" data-period-id="${escapeHtml(period.id)}">Edit</button><button class="text-button organization-row-action" type="button" data-open-admin-modal="period-config" data-period-id="${escapeHtml(period.id)}">Atur aktivitas</button><button class="text-button organization-row-action" type="button" data-activate-period="${escapeHtml(period.id)}">Aktifkan</button><button class="text-button organization-row-action" type="button" data-delete-resource="period" data-resource-id="${escapeHtml(period.id)}" data-resource-name="${escapeHtml(period.name)}" data-confirm-phrase="${escapeHtml(period.name)}">Hapus draft</button>`
        : period.status === 'active'
          ? `<span class="status status-active">Aktif</span><button class="text-button organization-row-action organization-archive-action" type="button" data-close-period="${escapeHtml(period.id)}" data-period-name="${escapeHtml(period.name)}">Tutup periode</button>`
          : '<span class="status status-closed">Ditutup</span>';
      return `<div class="admin-record-row period-record-row" role="row"><span role="cell"><strong>${escapeHtml(period.name)}</strong><small>Batas tuntas ${period.final_passing_threshold}%</small></span><span role="cell">${escapeHtml(formatDate(period.start_date, true))} — ${escapeHtml(formatDate(period.end_date, true))}<small>${period.period_activities.length} aktivitas · ${activeActivityCount} aktif</small></span><span role="cell">${periodAction}</span></div>`;
    }).join('');
    const periodContent = !showPeriodEmpty
      ? `<section class="admin-record-section" aria-labelledby="period-list-title"><div class="organization-section-head"><div><h3 id="period-list-title">Daftar Periode</h3><p>${hasPeriodSearch ? `${periodMeta.total} periode ditemukan.` : `${periodMeta.total} periode tersimpan.`}</p></div></div><form class="admin-list-search" data-admin-list-search="periods"><label class="sr-only" for="period-search">Cari periode LKS</label><input id="period-search" name="search" value="${escapeHtml(adminListState.periods.search)}" placeholder="Cari nama atau status periode" autocomplete="off"></form>${configurationData.periods.length ? `<div class="admin-record-table period-record-table" role="table" aria-label="Daftar periode LKS"><div class="admin-record-head" role="row"><span role="columnheader">Periode</span><span role="columnheader">Rentang dan aktivitas</span><span role="columnheader">Status dan aksi</span></div>${periodRows}</div>${paginationControls(periodMeta, 'periods')}` : '<p class="admin-empty">Tidak ada periode yang sesuai dengan pencarian.</p>'}</section>`
      : `<section class="organization-empty-state" aria-labelledby="period-empty-title"><h3 id="period-empty-title">Belum ada periode LKS</h3><p>Buat periode draft terlebih dahulu, lalu tambahkan aktivitas dan aktifkan ketika pengaturan sudah siap.</p><button class="primary-button" type="button" data-open-admin-modal="period">Tambah periode</button></section>`;
    const periodActions = !showPeriodEmpty ? `<button class="primary-button" type="button" data-open-admin-modal="period">Tambah periode</button>${activePeriods.length && (organization.santri_options ?? organization.santri).length ? '<button class="text-button admin-add-team" type="button" data-open-admin-modal="participant">Tambah peserta</button>' : ''}` : '';
    panel.innerHTML = adminPanel('Periode LKS', 'Buat periode draft, atur aktivitasnya, lalu aktifkan saat siap.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${periodContent}`, periodActions, !showPeriodEmpty);
    return;
  }
  const drafts = periodOptions.filter((period) => period.status === 'draft');
  const hasActivitySearch = Boolean(adminListState.activities.search);
  const showActivityEmpty = activityMeta.total === 0 && !hasActivitySearch;
  const activityRows = configuration.activities.map((activity) => {
    const assignments = activity.period_activities ?? [];
    const periodSummary = assignments.length
      ? `<span><strong>Dipakai di ${assignments.length} periode</strong><small>Target dan status diatur dari Periode LKS.</small></span>`
      : '<span><small>Belum digunakan di periode mana pun.</small></span>';
    return `<div class="admin-record-row activity-record-row" role="row"><span role="cell"><strong>${escapeHtml(activity.name)}</strong><small>${escapeHtml(activity.code)} · ${activity.is_active ? 'Aktif' : 'Nonaktif'}</small></span><span class="activity-period-summary" role="cell">${periodSummary}</span><span role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="activity-edit" data-activity-id="${escapeHtml(activity.id)}">Edit</button><button class="text-button organization-row-action" type="button" data-go="periods">Atur periode</button>${assignments.length === 0 ? `<button class="text-button organization-row-action" type="button" data-delete-resource="activity" data-resource-id="${escapeHtml(activity.id)}" data-resource-name="${escapeHtml(activity.name)}" data-confirm-phrase="${escapeHtml(activity.code)}">Hapus</button>` : ''}</span></div>`;
  }).join('');
  const activityContent = !showActivityEmpty
    ? `<section class="admin-record-section" aria-labelledby="activity-list-title"><div class="organization-section-head"><div><h3 id="activity-list-title">Daftar Aktivitas</h3><p>${hasActivitySearch ? `${activityMeta.total} aktivitas ditemukan.` : `${activityMeta.total} aktivitas master tersimpan.`}</p></div></div><form class="admin-list-search" data-admin-list-search="activities"><label class="sr-only" for="activity-search">Cari aktivitas LKS</label><input id="activity-search" name="search" value="${escapeHtml(adminListState.activities.search)}" placeholder="Cari nama atau kode aktivitas" autocomplete="off"></form>${configuration.activities.length ? `<div class="admin-record-table activity-record-table" role="table" aria-label="Daftar aktivitas LKS"><div class="admin-record-head" role="row"><span role="columnheader">Aktivitas</span><span role="columnheader">Pemakaian</span><span role="columnheader">Aksi</span></div>${activityRows}</div>${paginationControls(activityMeta, 'activities')}` : '<p class="admin-empty">Tidak ada aktivitas yang sesuai dengan pencarian.</p>'}${drafts.length ? '<p class="admin-context-note">Gunakan “Tambahkan ke periode” untuk menetapkan target pertama kali. Untuk mengubah target atau status aktif, buka periode draft lalu pilih “Atur aktivitas”.</p>' : '<p class="admin-context-note">Buat periode draft terlebih dahulu sebelum memasukkan aktivitas ke periode.</p>'}</section>`
    : `<section class="organization-empty-state" aria-labelledby="activity-empty-title"><h3 id="activity-empty-title">Belum ada aktivitas LKS</h3><p>Tambahkan aktivitas master yang akan digunakan dalam periode LKS.</p><button class="primary-button" type="button" data-open-admin-modal="activity">Tambah aktivitas</button></section>`;
  const activityActions = !showActivityEmpty ? `<button class="primary-button" type="button" data-open-admin-modal="activity">Tambah aktivitas</button>${activityOptions.length && drafts.length ? '<button class="text-button admin-add-team" type="button" data-open-admin-modal="period-activity">Tambahkan ke periode</button>' : ''}` : '';
  panel.innerHTML = adminPanel('Aktivitas LKS', 'Aktivitas master dapat dimasukkan ke periode draft dengan targetnya.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${activityContent}`, activityActions, !showActivityEmpty);
}

async function loadAdminView(name, { dataOnly = false } = {}) {
  if (!viewer?.roles?.includes('admin')) return renderAdminView(name);
  const panel = document.querySelector(`[data-view-panel="${name}"]`);
  const requestId = ++latestAdminRequest;
  const query = new URLSearchParams({
    organization_page: String(adminListState.organization.page),
    organization_search: adminListState.organization.search,
    people_page: String(adminListState.people.page),
    people_search: adminListState.people.search,
    period_page: String(adminListState.periods.page),
    period_search: adminListState.periods.search,
    activity_page: String(adminListState.activities.page),
    activity_search: adminListState.activities.search,
  });
  panel.setAttribute('aria-busy', 'true');
  if (!dataOnly) {
    panel.innerHTML = adminPanel('Memuat konfigurasi', 'Data administrasi sedang disiapkan.', '<div class="loading-state" role="status"><span class="loading-spinner" aria-hidden="true"></span><span>Menyiapkan data administrasi…</span></div><div class="loading-lines" aria-hidden="true"><i></i><i></i><i></i></div>');
    showPageProgress('Memuat data administrasi…');
  }
  try {
    const [organization, configuration] = await Promise.all([
      fetch(`${apiBase}/admin/organization?${query}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
      fetch(`${apiBase}/admin/configuration?${query}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
    ]);
    const [organizationPayload, configurationPayload] = await Promise.all([
      organization.ok ? organization.json() : null,
      configuration.ok ? configuration.json() : null,
    ]);
    if (requestId !== latestAdminRequest) return;
    if (organizationPayload) organizationData = organizationPayload.data;
    if (configurationPayload) configurationData = configurationPayload.data;
    const requiresOrganization = ['settings', 'organization', 'people'].includes(name);
    const requiresConfiguration = ['settings', 'periods', 'activities'].includes(name);
    if ((requiresOrganization && !organizationPayload) || (requiresConfiguration && !configurationPayload)) {
      throw new Error('Data untuk halaman ini belum dapat dimuat. Coba lagi.');
    }
    renderAdminView(name);
  } catch (error) {
    if (requestId !== latestAdminRequest) return;
    if (dataOnly) showToast(error.message || 'Data belum dapat dimuat.', 'error');
    else panel.innerHTML = adminPanel('Data belum tersedia', error.message, '<button class="primary-button" type="button" data-admin-refresh>Coba lagi</button>');
  } finally {
    if (requestId !== latestAdminRequest) return;
    panel.removeAttribute('aria-busy');
    if (!dataOnly) hidePageProgress();
  }
}

async function submitAdminForm(form) {
  const formData = new FormData(form);
  const type = form.dataset.adminForm;
  let payload = Object.fromEntries(formData.entries());
  let method = 'POST';
  if (type === 'santri' || type === 'santri-edit') payload.is_leader = form.elements.is_leader.checked;
  const endpoints = { department: '/admin/departments', team: '/admin/teams', santri: '/admin/santri', period: '/admin/periods', activity: '/admin/activities' };
  let endpoint = type === 'period-activity' ? `/admin/periods/${payload.period_id}/activities` : type === 'participant' ? `/periods/${payload.period_id}/participants/${payload.profile_id}` : endpoints[type];
  if (type === 'department-edit' || type === 'team-edit') {
    const resource = type === 'department-edit' ? 'departments' : 'teams';
    endpoint = `/admin/${resource}/${form.dataset.organizationId}`;
    method = 'PATCH';
    payload.is_active = form.elements.is_active.checked;
  }
  if (type === 'santri-edit') {
    endpoint = `/admin/santri/${form.dataset.santriId}`;
    method = 'PATCH';
  }
  if (type === 'period-edit') {
    endpoint = `/admin/periods/${form.dataset.periodId}`;
    method = 'PATCH';
  }
  if (type === 'activity-edit') {
    endpoint = `/admin/activities/${form.dataset.activityId}`;
    method = 'PATCH';
    payload.is_active = form.elements.is_active.checked;
  }
  if (type === 'checklist-correction') {
    endpoint = '/checklists';
    method = 'PUT';
    payload = {
      participant_id: payload.participant_id,
      period_activity_id: payload.period_activity_id,
      checklist_date: payload.checklist_date,
      is_completed: payload.is_completed === 'true',
      reason: payload.reason,
    };
  }
  if (type === 'period-config') {
    endpoint = `/admin/periods/${form.dataset.periodId}/activities`;
    method = 'PATCH';
    payload = { activities: [...form.querySelectorAll('[data-period-activity-id]')].map((row) => ({ id: row.dataset.periodActivityId, target_count: Number(row.querySelector('[data-target-count]').value), is_active: row.querySelector('[data-is-active]').checked, allowed_weekdays: [...row.querySelectorAll('[name="allowed_weekdays"]:checked')].map((input) => Number(input.value)) })) };
  }
  if (type === 'period-activity') {
    payload.allowed_weekdays = [...form.querySelectorAll('[name="allowed_weekdays"]:checked')].map((input) => Number(input.value));
  }
  if (type === 'period-activity' || type === 'participant') { delete payload.period_id; delete payload.profile_id; }
  if (['final_passing_threshold', 'sort_order', 'target_count'].some((key) => key in payload)) Object.keys(payload).forEach((key) => { if (['final_passing_threshold', 'sort_order', 'target_count'].includes(key)) payload[key] = Number(payload[key]); });
  if (type === 'participant') {
    const periodName = form.elements.period_id.selectedOptions[0]?.textContent ?? 'periode aktif';
    const participantName = form.elements.profile_id.selectedOptions[0]?.textContent ?? 'Santri Karya ini';
    const confirmed = await confirmAction({ title: 'Masukkan peserta ke periode?', message: `${participantName} akan mulai dapat mengisi checklist pada ${periodName} sejak hari ini. Catatan sebelumnya tidak dibuat mundur.`, confirmLabel: 'Masukkan peserta' });
    if (!confirmed) return;
  }
  if (type === 'santri-edit' && payload.status === 'inactive') {
    const confirmed = await confirmAction({ title: 'Nonaktifkan akun ini?', message: 'Santri Karya tidak dapat masuk atau dipilih untuk penempatan baru. Data periode yang sudah tersimpan tetap utuh.', confirmLabel: 'Nonaktifkan akun', tone: 'danger' });
    if (!confirmed) return;
  }
  if (type === 'activity-edit' && !payload.is_active) {
    const confirmed = await confirmAction({ title: 'Nonaktifkan aktivitas ini?', message: 'Aktivitas tidak tersedia untuk ditambahkan ke periode draft berikutnya. Konfigurasi periode yang sudah ada tidak berubah.', confirmLabel: 'Nonaktifkan aktivitas', tone: 'danger' });
    if (!confirmed) return;
  }
  setFormError(form);
  setFormBusy(form, true, 'Menyimpan…');
  try {
    const response = await fetch(`${apiBase}${endpoint}`, { method, credentials: 'same-origin', headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) }, body: JSON.stringify(payload) });
    if (!response.ok) throw new Error(await apiError(response));
    showToast('Data berhasil disimpan');
    form.closest('dialog')?.close();
    if (type === 'checklist-correction') {
      loadDashboard({ date: selectedChecklistDate, reloadRecap: true });
      return;
    }
    organizationData = null;
    configurationData = null;
    const returnView = form.closest('dialog')?.dataset.returnView;
    if (returnView) loadAdminView(returnView, { dataOnly: true });
  } catch (error) {
    const message = error.message || 'Data belum dapat disimpan. Coba lagi.';
    setFormError(form, message);
    showToast(message, 'error');
  } finally { setFormBusy(form, false); }
}

function openChecklistCorrection(participantId) {
  if (!recapData || recapData.period.status !== 'active' || !activePeriodActivities.length) {
    showToast('Koreksi hanya tersedia pada periode aktif yang memiliki aktivitas.', 'error');
    return;
  }
  const participants = recapData.participants.map((participant) => ({ id: participant.participant_id, name: `${participant.name} — ${participant.team ?? 'Tanpa tim'}` }));
  const activities = activePeriodActivities.map((activity) => ({ id: activity.id, name: activity.name }));
  adminFormDialogContent.innerHTML = `<form class="admin-modal-form" data-admin-form="checklist-correction"><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">Koreksi checklist</h2><p>Perubahan oleh Admin harus disertai alasan dan akan tercatat pada audit log.</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields"><label>Santri Karya<select name="participant_id" required>${adminOptions(participants, participantId)}</select></label><label>Aktivitas<select name="period_activity_id" required>${adminOptions(activities)}</select></label><label>Tanggal checklist<input type="date" name="checklist_date" min="${escapeHtml(dateInputValue(recapData.period.start_date))}" max="${escapeHtml([todayIso(), dateInputValue(recapData.period.end_date)].sort()[0])}" value="${escapeHtml(selectedChecklistDate)}" required></label><label>Status<select name="is_completed" required><option value="true">Dicatat selesai</option><option value="false">Tidak selesai</option></select></label><label>Alasan koreksi<textarea name="reason" maxlength="1000" required></textarea></label></div><div class="admin-modal-actions"><button class="text-button" type="button" data-close-admin-modal>Batal</button><button class="primary-button" type="submit">Simpan koreksi</button></div></form>`;
  adminFormDialog.dataset.returnView = 'recap';
  adminFormDialog.showModal();
  markAdminFormPristine();
  adminFormDialog.querySelector('select, input, textarea')?.focus();
}

function adminFormSnapshot(form) {
  return [...form.querySelectorAll('input, select, textarea')]
    .filter((field) => field.type !== 'submit' && field.type !== 'button')
    .map((field) => `${field.name}:${field.type === 'checkbox' ? field.checked : field.value}`)
    .join('|');
}

function markAdminFormPristine() {
  const form = adminFormDialog.querySelector('[data-admin-form]');
  if (form) form.dataset.initialSnapshot = adminFormSnapshot(form);
}

function hasUnsavedAdminFormChanges() {
  const form = adminFormDialog.querySelector('[data-admin-form]');
  return Boolean(form?.dataset.initialSnapshot) && form.dataset.initialSnapshot !== adminFormSnapshot(form);
}

async function requestAdminFormClose() {
  if (!hasUnsavedAdminFormChanges()) {
    adminFormDialog.close();
    return;
  }
  const confirmed = await confirmAction({
    title: 'Batalkan perubahan?',
    message: 'Input yang belum disimpan akan hilang.',
    confirmLabel: 'Buang perubahan',
    tone: 'danger',
  });
  if (confirmed) adminFormDialog.close();
}

adminFormDialog.addEventListener('cancel', async (event) => {
  if (!hasUnsavedAdminFormChanges()) return;
  event.preventDefault();
  await requestAdminFormClose();
});

document.addEventListener('submit', (event) => { if (event.target.matches('[data-admin-form]')) { event.preventDefault(); submitAdminForm(event.target); } });
let adminSearchTimer;
document.addEventListener('input', (event) => {
  const form = event.target.closest('[data-admin-list-search]');
  if (!form || event.target.name !== 'search') return;
  const list = form.dataset.adminListSearch;
  clearTimeout(adminSearchTimer);
  adminSearchTimer = setTimeout(() => {
    adminListState[list].search = event.target.value.trim();
    adminListState[list].page = 1;
    loadAdminView(list, { dataOnly: true });
  }, 280);
});
document.addEventListener('submit', (event) => {
  const list = event.target.dataset.adminListSearch;
  if (!list) return;
  event.preventDefault();
  clearTimeout(adminSearchTimer);
  adminListState[list].search = event.target.elements.search.value.trim();
  adminListState[list].page = 1;
  loadAdminView(list, { dataOnly: true });
});
function openAdminFormModal(type, departmentId = '', returnView = '') {
  const organization = organizationData ?? { departments: [], department_options: [], santri: [], santri_options: [], leaders: [] };
  const configuration = configurationData ?? { periods: [], period_options: [], activities: [], activity_options: [] };
  const departmentOptions = organization.department_options ?? organization.departments;
  const teams = departmentOptions.flatMap((department) => department.teams.map((team) => ({ ...team, name: `${department.name} — ${team.name}` })));
  const selectedDepartment = departmentOptions.find((department) => department.id === departmentId);
  const selectedTeam = teams.find((team) => team.id === departmentId);
  const periodOptions = configuration.period_options ?? configuration.periods;
  const activityOptions = configuration.activity_options ?? configuration.activities;
  const drafts = periodOptions.filter((period) => period.status === 'draft');
  const activePeriods = periodOptions.filter((period) => period.status === 'active');
  const participants = (organization.santri_options ?? organization.santri).map((profile) => ({ id: profile.id, name: profile.user.name }));
  const configuredPeriod = periodOptions.find((period) => period.id === departmentId);
  const selectedProfile = (organizationData.santri ?? []).find((profile) => profile.user_id === departmentId);
  const selectedActivity = activityOptions.find((activity) => activity.id === departmentId);
  const activeActivityOptions = activityOptions.filter((activity) => activity.is_active);
  const definitions = {
    department: {
      title: 'Tambah departemen', description: 'Tambahkan unit utama untuk penempatan Santri Karya.', submitLabel: 'Simpan departemen',
      fields: '<label>Kode<input name="code" maxlength="30" autocomplete="off" required></label><label>Nama departemen<input name="name" maxlength="100" autocomplete="organization" required></label>',
    },
    team: {
      title: 'Tambah tim', description: 'Pilih departemen induk sebelum menyimpan tim baru.', submitLabel: 'Simpan tim',
      fields: `<label>Departemen<select name="department_id" required>${adminOptions(departmentOptions.filter((department) => department.is_active), departmentId)}</select></label><label>Kode<input name="code" maxlength="30" autocomplete="off" required></label><label>Nama tim<input name="name" maxlength="100" autocomplete="organization" required></label>`,
    },
    'department-edit': selectedDepartment ? {
      title: `Kelola departemen · ${escapeHtml(selectedDepartment.name)}`, description: 'Perubahan nama dan kode berlaku untuk penempatan berikutnya. Departemen hanya dapat diarsipkan setelah seluruh tim aktifnya diarsipkan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="30" value="${escapeHtml(selectedDepartment.code)}" required></label><label>Nama departemen<input name="name" maxlength="100" value="${escapeHtml(selectedDepartment.name)}" required></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedDepartment.is_active ? ' checked' : ''}> Departemen aktif</label>`,
    } : null,
    'team-edit': selectedTeam ? {
      title: `Kelola tim · ${escapeHtml(selectedTeam.name)}`, description: 'Tim hanya dapat diarsipkan setelah seluruh Santri Karya aktifnya dipindahkan atau dinonaktifkan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="30" value="${escapeHtml(selectedTeam.code)}" required></label><label>Nama tim<input name="name" maxlength="100" value="${escapeHtml(selectedTeam.name)}" required></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedTeam.is_active ? ' checked' : ''}> Tim aktif</label>`,
    } : null,
    santri: {
      title: 'Tambah Santri Karya', description: 'Admin menetapkan password sementara dan penempatan organisasi akun baru.', submitLabel: 'Buat akun Santri',
      fields: `<label>Nama<input name="name" maxlength="150" autocomplete="name" required></label><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Password sementara<input type="password" name="temporary_password" minlength="8" autocomplete="new-password" required></label><div class="admin-modal-inline-fields"><label>Gender<select name="gender" required><option value="ikhwan">Ikhwan</option><option value="akhwat">Akhwat</option></select></label><label>Tim<select name="team_id" required>${adminOptions(teams)}</select></label></div><label>Leader<select name="leader_user_id">${adminOptions(organization.leaders)}</select></label><div class="admin-modal-inline-fields"><label>Kategori (opsional)<input name="category" maxlength="100"></label><label>Level (opsional)<input name="level" maxlength="100"></label></div><label class="admin-modal-checkbox"><input type="checkbox" name="is_leader"> Jadikan Leader</label>`,
    },
    'santri-edit': selectedProfile ? {
      title: `Kelola Santri Karya · ${escapeHtml(selectedProfile.user.name)}`, description: 'Perubahan penempatan berlaku untuk data berikutnya. Riwayat periode yang sudah berjalan tetap menggunakan snapshot sebelumnya.', submitLabel: 'Simpan perubahan',
      fields: `<label>Nama<input name="name" maxlength="150" autocomplete="name" value="${escapeHtml(selectedProfile.user.name)}" required></label><label>Email<input type="email" name="email" autocomplete="email" value="${escapeHtml(selectedProfile.user.email)}" required></label><div class="admin-modal-inline-fields"><label>Gender<select name="gender" required><option value="ikhwan"${selectedProfile.gender === 'ikhwan' ? ' selected' : ''}>Ikhwan</option><option value="akhwat"${selectedProfile.gender === 'akhwat' ? ' selected' : ''}>Akhwat</option></select></label><label>Tim<select name="team_id" required>${adminOptions(teams.filter((team) => team.is_active), selectedProfile.team_id)}</select></label></div><label>Leader<select name="leader_user_id">${adminOptions(organization.leaders, selectedProfile.leader_user_id ?? '')}</select></label><div class="admin-modal-inline-fields"><label>Kategori<input name="category" maxlength="100" value="${escapeHtml(selectedProfile.category ?? '')}"></label><label>Level<input name="level" maxlength="100" value="${escapeHtml(selectedProfile.level ?? '')}"></label></div><label>Status akun<select name="status" required><option value="active"${selectedProfile.status === 'active' ? ' selected' : ''}>Aktif</option><option value="inactive"${selectedProfile.status === 'inactive' ? ' selected' : ''}>Nonaktif</option></select></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_leader"${selectedProfile.user.roles?.some((role) => role.code === 'leader') ? ' checked' : ''}> Jadikan Leader</label>`,
    } : null,
    period: {
      title: 'Tambah periode LKS', description: 'Periode dibuat sebagai draft agar aktivitas dapat disiapkan sebelum diaktifkan.', submitLabel: 'Simpan periode draft',
      fields: '<label>Nama periode<input name="name" placeholder="Oktober 2026" maxlength="100" required></label><div class="admin-modal-inline-fields"><label>Tanggal mulai<input type="date" name="start_date" required></label><label>Tanggal selesai<input type="date" name="end_date" required></label></div><label>Batas tuntas periode (%)<input type="number" name="final_passing_threshold" min="0" max="100" value="90" required><small>Nilai akhir yang sama dengan atau melebihi batas ini berstatus Tuntas.</small></label>',
    },
    'period-edit': configuredPeriod ? {
      title: `Kelola periode · ${escapeHtml(configuredPeriod.name)}`, description: 'Periode draft dapat disesuaikan sebelum diaktifkan. Setelah aktif, rentang dan ambang nilai dikunci untuk menjaga konsistensi perhitungan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Nama periode<input name="name" maxlength="100" value="${escapeHtml(configuredPeriod.name)}" required></label><div class="admin-modal-inline-fields"><label>Tanggal mulai<input type="date" name="start_date" value="${escapeHtml(dateInputValue(configuredPeriod.start_date))}" required></label><label>Tanggal selesai<input type="date" name="end_date" value="${escapeHtml(dateInputValue(configuredPeriod.end_date))}" required></label></div><label>Batas tuntas periode (%)<input type="number" name="final_passing_threshold" min="0" max="100" value="${escapeHtml(configuredPeriod.final_passing_threshold)}" required></label>`,
    } : null,
    activity: {
      title: 'Tambah aktivitas LKS', description: 'Aktivitas master dapat dipakai kembali pada periode draft berikutnya.', submitLabel: 'Simpan aktivitas',
      fields: '<label>Kode<input name="code" maxlength="50" autocomplete="off" required></label><label>Nama aktivitas<input name="name" maxlength="150" required></label><p class="admin-form-note">Urutan aktivitas diatur otomatis oleh sistem. Target dan status aktif diatur saat aktivitas dimasukkan ke periode draft.</p>',
    },
    'activity-edit': selectedActivity ? {
      title: `Kelola aktivitas · ${escapeHtml(selectedActivity.name)}`, description: 'Perubahan berlaku saat aktivitas dipakai pada periode berikutnya. Snapshot aktivitas pada periode aktif atau tertutup tetap terjaga.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="50" autocomplete="off" value="${escapeHtml(selectedActivity.code)}" required></label><label>Nama aktivitas<input name="name" maxlength="150" value="${escapeHtml(selectedActivity.name)}" required></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedActivity.is_active ? ' checked' : ''}> Aktivitas aktif</label>`,
    } : null,
    'period-activity': {
      title: 'Tambahkan ke periode', description: 'Pilih periode draft, aktivitas, dan target pencatatan untuk periode tersebut.', submitLabel: 'Tambahkan aktivitas',
      fields: `<label>Periode draft<select name="period_id" required>${adminOptions(drafts)}</select></label><label>Aktivitas<select name="activity_id" required>${adminOptions(activeActivityOptions)}</select></label><label>Target periode<input type="number" name="target_count" min="1" required></label>${weekdayPicker()}`,
    },
    'period-config': configuredPeriod ? {
      title: `Atur aktivitas · ${escapeHtml(configuredPeriod.name)}`, description: `Capaian tiap aktivitas dibatasi 100%. Nilai akhir adalah rata-rata aktivitas aktif dan dinyatakan Tuntas pada ${configuredPeriod.final_passing_threshold}%.`, submitLabel: 'Simpan pengaturan aktivitas',
      fields: `<div class="period-config-list">${configuredPeriod.period_activities.map((activity) => `<fieldset class="period-config-row" data-period-activity-id="${escapeHtml(activity.id)}"><div><strong>${escapeHtml(activity.activity_name_snapshot)}</strong><small>Rumus: min(checklist selesai ÷ target, 100%)</small></div><label>Target<input type="number" min="1" value="${activity.target_count}" data-target-count required></label><label class="admin-modal-checkbox"><input type="checkbox" data-is-active${activity.is_active ? ' checked' : ''}> Aktif</label><div class="period-weekday-control">${weekdayPicker(activity.allowed_weekdays)}</div></fieldset>`).join('') || '<p class="admin-form-note">Tambahkan aktivitas ke periode ini terlebih dahulu dari halaman Aktivitas LKS.</p>'}</div>`,
    } : null,
    participant: {
      title: 'Tambah peserta periode', description: 'Peserta mulai dapat mengisi checklist sejak dimasukkan ke periode aktif.', submitLabel: 'Masukkan peserta',
      fields: `<label>Periode aktif<select name="period_id" required>${adminOptions(activePeriods)}</select></label><label>Santri Karya<select name="profile_id" required>${adminOptions(participants)}</select></label>`,
    },
  };
  const definition = definitions[type];
  if (!definition) return;
  const disableSubmit = type === 'period-config' && configuredPeriod.period_activities.length === 0;
  adminFormDialogContent.innerHTML = `<form class="admin-modal-form" data-admin-form="${type}"${type === 'period-config' || type === 'period-edit' ? ` data-period-id="${escapeHtml(departmentId)}"` : ''}${type === 'department-edit' || type === 'team-edit' ? ` data-organization-id="${escapeHtml(departmentId)}"` : ''}${type === 'santri-edit' ? ` data-santri-id="${escapeHtml(departmentId)}"` : ''}${type === 'activity-edit' ? ` data-activity-id="${escapeHtml(departmentId)}"` : ''}><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">${definition.title}</h2><p>${definition.description}</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields">${definition.fields}</div><div class="admin-modal-actions"><button class="text-button" type="button" data-close-admin-modal>Batal</button><button class="primary-button" type="submit"${disableSubmit ? ' disabled' : ''}>${definition.submitLabel}</button></div></form>`;
  adminFormDialog.dataset.returnView = returnView;
  adminFormDialog.showModal();
  markAdminFormPristine();
  adminFormDialog.querySelector('input, select')?.focus();
}

document.addEventListener('click', async (event) => {
  const trigger = event.target.closest('[data-open-admin-modal]');
  if (trigger) {
    openAdminFormModal(
      trigger.dataset.openAdminModal,
      trigger.dataset.departmentId ?? trigger.dataset.periodId ?? trigger.dataset.organizationId ?? trigger.dataset.santriId ?? trigger.dataset.activityId,
      trigger.closest('[data-view-panel]')?.dataset.viewPanel,
    );
    return;
  }
  if (event.target.closest('[data-close-admin-modal]')) await requestAdminFormClose();
});
document.addEventListener('click', (event) => {
  const correction = event.target.closest('[data-open-checklist-correction]');
  if (correction) openChecklistCorrection(correction.dataset.openChecklistCorrection);
});
document.addEventListener('click', async (event) => {
  const page = event.target.closest('[data-admin-page]');
  if (page) {
    const list = page.dataset.adminPage;
    const nextPage = Number(page.dataset.page);
    if (!page.disabled && adminListState[list] && nextPage > 0) {
      adminListState[list].page = nextPage;
      return loadAdminView(list, { dataOnly: true });
    }
  }
  const refresh = event.target.closest('[data-admin-refresh]');
  if (refresh) {
    setButtonBusy(refresh, true, 'Memuat…');
    return loadAdminView(refresh.closest('[data-view-panel]').dataset.viewPanel, { dataOnly: true });
  }
  const deletion = event.target.closest('[data-delete-resource]');
  if (deletion) {
    const resource = deletion.dataset.deleteResource;
    const labels = { department: 'departemen', team: 'tim', santri: 'akun Santri Karya', activity: 'aktivitas', period: 'periode draft' };
    const viewByResource = { department: 'organization', team: 'organization', santri: 'people', activity: 'activities', period: 'periods' };
    const endpoints = { department: 'departments', team: 'teams', santri: 'santri', activity: 'activities', period: 'periods' };
    const label = labels[resource];
    const confirmed = await confirmAction({ title: `Hapus ${label}?`, message: `${deletion.dataset.resourceName} akan dihapus permanen. Tindakan ini hanya berhasil bila data belum memiliki riwayat pemakaian.`, confirmLabel: `Hapus ${label}`, tone: 'danger', requireText: deletion.dataset.confirmPhrase });
    if (!confirmed) return;
    setButtonBusy(deletion, true, 'Menghapus…');
    try {
      const response = await fetch(`${apiBase}/admin/${endpoints[resource]}/${deletion.dataset.resourceId}`, { method: 'DELETE', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      showToast(`${label.charAt(0).toUpperCase()}${label.slice(1)} berhasil dihapus`);
      organizationData = null;
      configurationData = null;
      return loadAdminView(viewByResource[resource], { dataOnly: true });
    } catch (error) { showToast(error.message || 'Data belum dapat dihapus.', 'error'); } finally { setButtonBusy(deletion, false); }
  }
  const archive = event.target.closest('[data-archive-organization]');
  if (archive) {
    const type = archive.dataset.archiveOrganization;
    const unitName = archive.dataset.organizationName;
    const confirmed = await confirmAction({ title: `Arsipkan ${type === 'department' ? 'departemen' : 'tim'}?`, message: `${unitName} tidak akan tersedia untuk penempatan baru. Data historis tetap tersimpan.`, confirmLabel: 'Arsipkan', tone: 'danger' });
    if (!confirmed) return;
    setButtonBusy(archive, true, 'Mengarsipkan…');
    try {
      const response = await fetch(`${apiBase}/admin/${type === 'department' ? 'departments' : 'teams'}/${archive.dataset.organizationId}`, { method: 'PATCH', credentials: 'same-origin', headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) }, body: JSON.stringify({ is_active: false }) });
      if (!response.ok) throw new Error(await apiError(response));
      showToast(`${type === 'department' ? 'Departemen' : 'Tim'} berhasil diarsipkan`);
      organizationData = null;
      configurationData = null;
      return loadAdminView('organization', { dataOnly: true });
    } catch (error) { showToast(error.message, 'error'); } finally { setButtonBusy(archive, false); }
  }
  const activate = event.target.closest('[data-activate-period]');
  if (!activate) return;
  const confirmed = await confirmAction({ title: 'Aktifkan periode ini?', message: 'Periode ini akan menjadi periode LKS aktif dan digunakan untuk pencatatan checklist peserta.', confirmLabel: 'Aktifkan periode' });
  if (!confirmed) return;
  setButtonBusy(activate, true, 'Mengaktifkan…');
  try {
    const response = await fetch(`${apiBase}/periods/${activate.dataset.activatePeriod}/activate`, { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
    if (!response.ok) throw new Error(await apiError(response));
    showToast('Periode berhasil diaktifkan');
    configurationData = null;
    loadAdminView('periods', { dataOnly: true });
    loadDashboard();
  } catch (error) { showToast(error.message, 'error'); } finally { setButtonBusy(activate, false); }
});

document.addEventListener('click', async (event) => {
  const closePeriod = event.target.closest('[data-close-period]');
  if (closePeriod) {
    const periodName = closePeriod.dataset.periodName;
    const confirmed = await confirmAction({ title: 'Tutup periode ini?', message: `${periodName} akan dikunci sebagai riwayat. Checklist dan konfigurasi periode tidak dapat diubah lagi.`, confirmLabel: 'Tutup periode', tone: 'danger' });
    if (!confirmed) return;
    setButtonBusy(closePeriod, true, 'Menutup…');
    try {
      const response = await fetch(`${apiBase}/periods/${closePeriod.dataset.closePeriod}/close`, { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      showToast('Periode ditutup dan dipindahkan ke riwayat');
      configurationData = null;
      historyData = null;
      historyRecapData = null;
      loadAdminView('periods', { dataOnly: true });
      loadHistory();
    } catch (error) { showToast(error.message, 'error'); } finally { setButtonBusy(closePeriod, false); }
    return;
  }

  const historyPeriod = event.target.closest('[data-history-period]');
  if (historyPeriod) return loadHistoryRecap(historyPeriod.dataset.historyPeriod, historyPeriod);
  if (event.target.closest('[data-history-refresh]')) {
    historyRecapData = null;
    return loadHistory();
  }
});

async function loadDashboard({ date = selectedChecklistDate, reloadRecap = true, showProgress = false } = {}) {
  if (showProgress) showPageProgress();
  try {
    const response = await fetch(`${apiBase}/dashboard?date=${encodeURIComponent(date)}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error('Data LKS belum dapat dimuat. Muat ulang halaman untuk mencoba lagi.');
    applyDashboard((await response.json()).data);
    if (reloadRecap) loadRecap();
  } catch (error) {
    setChecklistEmpty('Data LKS belum dapat dimuat. Muat ulang halaman untuk mencoba lagi.');
    showToast(error.message, 'error');
  } finally {
    if (showProgress) hidePageProgress();
  }
}

updateTodayProgress();
loadDashboard();
