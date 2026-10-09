const viewLabels = { dashboard: ['Ruang pribadi', 'Dashboard'], lks: ['Catatan pribadi', 'LKS Saya'], recap: ['Pemantauan', 'Rekap'], department: ['Laporan organisasi', 'Departemen'], history: ['Catatan pribadi', 'Riwayat'], account: ['Akun', 'Akun Saya'], settings: ['Administrasi', 'Pengaturan'], organization: ['Pengaturan', 'Struktur Organisasi'], people: ['Pengaturan', 'Data Santri Karya'], periods: ['Pengaturan', 'Periode LKS'], 'period-detail': ['Pengaturan', 'Kelola Periode'], activities: ['Pengaturan', 'Aktivitas & Aturan'], calendar: ['Pengaturan', 'Kalender Kerja'], documents: ['Pengaturan', 'Dokumen & Tanda Tangan'] };
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
pageProgress.hidden = false;
let participantId = null;
let viewer = null;
let organizationData = null;
let configurationData = null;
let documentSignatoriesData = null;
let recapData = null;
let historyData = null;
let historyRecapData = null;
let historyScope = 'personal';
let historyYear = '';
let historyParticipantId = '';
let historyDetailLoading = false;
let historyDetailError = '';
let historyDetailRequest = 0;
let historyDetailPeriodId = '';
let recapPeriods = [];
let departmentTrendData = [];
let roleTrendData = [];
let roleTrendError = '';
let selectedReportDimension = 'department';
let selectedTrendGroupId = '';
let recapDepartmentFilter = '';
let recapTeamFilter = '';
let recapMemberFocus = '';
let recapScope = 'team';
let selectedChecklistDate = todayIso();
let activePeriodActivities = [];
let activePeriod = null;
let activeParticipant = null;
let bulkChecklistState = null;
const checklistSaveErrors = new Map();
let correctionParticipantId = null;
let accountData = null;
let selectedPeriodId = null;
let dashboardRequest = 0;
let dashboardAbortController = null;
let recapRequest = 0;
let recapAbortController = null;

function canOpenView(name, roles) {
  if (!hasConfiguredRole(roles)) return ['dashboard', 'account'].includes(name);
  return canView(name, roles);
}

function storedViewKey() {
  return viewer?.id ? `lks.active-view.${viewer.id}` : null;
}

function storedPeriodKey() {
  return viewer?.id ? `lks.selected-period.${viewer.id}` : null;
}

function savedView() {
  const key = storedViewKey();
  if (!key) return null;
  try { return sessionStorage.getItem(key); } catch { return null; }
}

function saveView(name) {
  const key = storedViewKey();
  if (!key) return;
  try {
    sessionStorage.setItem(key, name);
    if (name === 'period-detail' && selectedPeriodId) sessionStorage.setItem(storedPeriodKey(), selectedPeriodId);
  } catch { /* Browser storage is optional. */ }
}

function openView(name, { persist = true, scroll = true, load = true } = {}) {
  if (!viewLabels[name] || (viewer && !canOpenView(name, viewer.roles ?? []))) name = 'dashboard';
  viewPanels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.viewPanel === name));
  navButtons.forEach((button) => button.classList.toggle('is-active', button.dataset.view === name));
  const [trail, title] = viewLabels[name];
  pageTitle.textContent = title;
  breadcrumb.textContent = trail;
  if (persist) saveView(name);
  if (scroll) window.scrollTo({ top: 0, behavior: 'smooth' });
  if (!load) return;
  if (['settings', 'organization', 'people', 'periods', 'period-detail', 'activities', 'calendar', 'documents'].includes(name)) loadAdminView(name);
  if (['recap', 'department'].includes(name)) loadRecap();
  if (['dashboard', 'lks'].includes(name)) loadDashboard({ showProgress: true, reloadRecap: false });
  if (name === 'history') loadHistory({ showProgress: true });
  if (name === 'account') loadAccount({ showProgress: true });
}

function finishInitialBoot({ hideProgress = true } = {}) {
  if (!document.body.classList.contains('is-booting')) return;
  document.body.classList.remove('is-booting');
  if (hideProgress) hidePageProgress();
}

navButtons.forEach((button) => button.addEventListener('click', () => openView(button.dataset.view)));
async function exportPdf(link) {
  if (link.dataset.exporting === 'true') return;
  link.dataset.exporting = 'true';
  link.dataset.exportLabel = link.textContent.trim();
  link.textContent = 'Menyiapkan PDF…';
  link.setAttribute('aria-busy', 'true');
  link.setAttribute('aria-disabled', 'true');
  try {
    const response = await fetch(link.href, { headers: { Accept: 'application/pdf' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    const blob = await response.blob();
    const filename = response.headers.get('content-disposition')?.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)?.[1]?.replace(/['"]/g, '') || 'LKS.pdf';
    const objectUrl = URL.createObjectURL(blob);
    const download = document.createElement('a');
    download.href = objectUrl;
    download.download = filename;
    download.click();
    URL.revokeObjectURL(objectUrl);
    showToast('PDF berhasil diunduh');
  } catch (error) {
    showToast(error.message || 'PDF belum dapat dibuat. Coba lagi beberapa saat lagi.', 'error');
  } finally {
    link.dataset.exporting = 'false';
    link.textContent = link.dataset.exportLabel;
    link.removeAttribute('aria-busy');
    link.removeAttribute('aria-disabled');
  }
}

document.addEventListener('click', (event) => {
  const action = event.target.closest('[data-go]');
  if (action) {
    recapMemberFocus = action.dataset.go === 'recap' ? (action.dataset.recapMember ?? recapMemberFocus) : '';
    openView(action.dataset.go);
  }
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

function setFormError(form, message = '', fieldErrors = {}) {
  form.querySelectorAll('.field-feedback').forEach((feedback) => feedback.remove());
  form.querySelectorAll('[aria-invalid="true"]').forEach((field) => {
    field.removeAttribute('aria-invalid');
    field.removeAttribute('aria-describedby');
  });
  let feedback = form.querySelector('.form-feedback');
  if (!message && !Object.keys(fieldErrors).length) {
    feedback?.remove();
    return;
  }
  if (!feedback) {
    feedback = document.createElement('p');
    feedback.className = 'form-feedback';
    feedback.setAttribute('role', 'alert');
    const fields = form.querySelector('.admin-modal-fields');
    if (fields) fields.before(feedback);
    else form.prepend(feedback);
  }
  feedback.textContent = message;

  const firstInvalid = Object.entries(fieldErrors).reduce((first, [name, errors]) => {
    const field = form.elements.namedItem(name);
    if (!(field instanceof HTMLElement)) return first;
    const messageId = `${field.id || name}-error`;
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', messageId);
    const fieldFeedback = document.createElement('p');
    fieldFeedback.className = 'field-feedback';
    fieldFeedback.id = messageId;
    fieldFeedback.setAttribute('role', 'alert');
    fieldFeedback.textContent = Array.isArray(errors) ? errors[0] : errors;
    (field.closest('label') ?? field.parentElement)?.append(fieldFeedback);
    return first ?? field;
  }, null);
  firstInvalid?.focus();
}

function confirmAction({ title, message, confirmLabel, tone = 'default' }) {
  confirmDialog.querySelector('[data-confirm-title]').textContent = title;
  confirmDialog.querySelector('[data-confirm-message]').textContent = message;
  const confirmButton = confirmDialog.querySelector('[value="confirm"]');
  confirmButton.textContent = confirmLabel;
  confirmDialog.dataset.tone = tone;
  confirmDialog.returnValue = '';
  return new Promise((resolve) => {
    confirmDialog.addEventListener('close', () => {
      resolve(confirmDialog.returnValue === 'confirm');
    }, { once: true });
    confirmDialog.showModal();
  });
}

function setSaving(isSaving, message = isSaving ? 'Menyimpan…' : 'Tersimpan', type = '') {
  document.querySelectorAll('.save-state').forEach((state) => {
    state.classList.toggle('is-saving', isSaving);
    state.classList.toggle('is-error', type === 'error');
    const label = state.querySelector('.save-state-message');
    if (label) label.textContent = message;
  });
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

function localDateValue(date) {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}

function escapeHtml(value) {
  return String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character]));
}

function checkIcon() {
  return '<svg><use href="#icon-check"/></svg>';
}

function renderQuickCheck(activity) {
  const complete = activity.is_completed ?? activity.is_completed_today;
  const saveError = checklistSaveErrors.get(checklistSaveErrorKey(activity.id));
  return `<button class="quick-check${complete ? ' is-done' : ''}" data-activity-id="${escapeHtml(activity.id)}" aria-pressed="${complete}">
    <span class="check-mark">${checkIcon()}</span><span><strong>${escapeHtml(activity.name)}</strong><small${saveError ? ' class="checklist-save-error"' : ''}>${escapeHtml(saveError ? 'Gagal disimpan — coba lagi' : complete ? 'Sudah dicatat' : 'Belum dicatat')}</small></span>
  </button>`;
}

function renderFullCheck(activity) {
  const complete = activity.is_completed ?? activity.is_completed_today;
  const optionalNote = activity.is_optional_today ? ` · Opsional${activity.holiday_name ? ` (${escapeHtml(activity.holiday_name)})` : ''}` : '';
  const saveError = checklistSaveErrors.get(checklistSaveErrorKey(activity.id));
  return `<div class="checklist-item${complete ? ' is-done' : ''}${saveError ? ' has-save-error' : ''}">
    <button class="square-check" data-activity-id="${escapeHtml(activity.id)}" aria-label="${complete ? 'Batalkan catatan' : 'Catat'} ${escapeHtml(activity.name)}" aria-pressed="${complete}">${checkIcon()}</button>
    <div><strong>${escapeHtml(activity.name)}</strong><small>${escapeHtml(activityRuleLabel(activity))}${optionalNote}</small><button class="text-button bulk-checklist-trigger" type="button" data-open-bulk-checklist="${escapeHtml(activity.id)}"><svg aria-hidden="true"><use href="#icon-calendar"/></svg>Catat beberapa tanggal</button></div>
    <span class="item-note${saveError ? ' checklist-save-error' : ''}" role="status">${escapeHtml(saveError ? 'Gagal disimpan — coba lagi' : complete ? 'Dicatat' : 'Belum dicatat')}</span>
  </div>`;
}

function bulkChecklistMonths(activity) {
  if (!activePeriod || !activeParticipant) return [];
  const start = new Date(`${dateInputValue(activeParticipant.participation_start_date)}T00:00:00`);
  const end = new Date(`${[todayIso(), dateInputValue(activePeriod.end_date)].sort()[0]}T00:00:00`);
  if (start > end) return [];
  const allowedWeekdays = new Set(activity.allowed_weekdays ?? []);
  const holidays = new Map((activePeriod.holidays ?? []).map((holiday) => [dateInputValue(holiday.date), holiday.name]));
  const recordedDates = new Set(activity.recorded_dates ?? []);
  const recordedByWeek = new Map();
  const months = new Map();

  for (const date = new Date(start); date <= end; date.setDate(date.getDate() + 1)) {
    const value = localDateValue(date);
    const weekStart = new Date(date);
    weekStart.setDate(date.getDate() - ((date.getDay() + 6) % 7));
    const weekKey = localDateValue(weekStart);
    if (recordedDates.has(value) && !holidays.has(value)) recordedByWeek.set(weekKey, (recordedByWeek.get(weekKey) ?? 0) + 1);
  }

  for (const date = new Date(start); date <= end; date.setDate(date.getDate() + 1)) {
    const weekday = ((date.getDay() + 6) % 7) + 1;
    const value = localDateValue(date);
    const monthKey = value.slice(0, 7);
    if (!months.has(monthKey)) {
      months.set(monthKey, {
        key: monthKey,
        label: new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(date),
        days: [],
      });
    }
    const weekStart = new Date(date);
    weekStart.setDate(date.getDate() - ((date.getDay() + 6) % 7));
    const weekKey = localDateValue(weekStart);
    months.get(monthKey).days.push({
      value,
      day: date.getDate(),
      selectable: allowedWeekdays.size === 0 || allowedWeekdays.has(weekday),
      recorded: recordedDates.has(value),
      holidayName: holidays.get(value) ?? null,
      weekKey,
      recordedInWeek: recordedByWeek.get(weekKey) ?? 0,
    });
  }

  return [...months.values()];
}

function selectedBulkDates() {
  return bulkChecklistState ? [...bulkChecklistState.selectedDates] : [];
}

function syncBulkChecklistSelections(form) {
  if (!bulkChecklistState) return;
  form.querySelectorAll('[data-bulk-checklist-date]').forEach((input) => {
    if (input.checked) bulkChecklistState.selectedDates.add(input.value);
    else bulkChecklistState.selectedDates.delete(input.value);
  });
}

function updateBulkChecklistSelection(form) {
  syncBulkChecklistSelections(form);
  const inputs = [...form.querySelectorAll('[data-bulk-checklist-date]')];
  const selectedByWeek = new Map();
  selectedBulkDates().forEach((date) => {
    const day = bulkChecklistState?.daysByValue.get(date);
    const weekKey = day?.weekKey;
    if (!weekKey || day.holidayName) return;
    selectedByWeek.set(weekKey, (selectedByWeek.get(weekKey) ?? 0) + 1);
  });
  const weeklyLimit = Number(form.dataset.maxPerWeek) || 0;
  const selectionLimitReached = selectedBulkDates().length >= 366;
  inputs.forEach((input) => {
    const reachedLimit = weeklyLimit > 0 && Number(input.dataset.recordedInWeek) + (selectedByWeek.get(input.dataset.weekKey) ?? 0) >= weeklyLimit;
    input.disabled = !input.checked && (reachedLimit || selectionLimitReached);
    input.closest('.bulk-calendar-day')?.classList.toggle('is-unavailable', input.disabled);
  });
  const selected = selectedBulkDates().length;
  const summary = form.querySelector('[data-bulk-selection-summary]');
  const submit = form.querySelector('[data-bulk-submit]');
  if (summary) summary.textContent = selected
    ? selected === 366 ? '366 tanggal dipilih. Ini batas maksimal dalam satu kali pencatatan.' : `${selected} tanggal dipilih.`
    : 'Pilih tanggal yang masih tersedia pada kalender.';
  if (submit) {
    submit.disabled = selected === 0;
    submit.textContent = selected ? `Catat ${selected} tanggal` : 'Catat tanggal terpilih';
  }
}

function renderBulkChecklistCalendar(months) {
  const weekdays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
  return months.map((month) => {
    const firstDay = new Date(`${month.key}-01T00:00:00`);
    const leadingDays = (firstDay.getDay() + 6) % 7;
    const days = new Map(month.days.map((day) => [day.value, day]));
    const lastDay = new Date(firstDay.getFullYear(), firstDay.getMonth() + 1, 0).getDate();
    const cells = Array.from({ length: leadingDays + lastDay }, (_, index) => {
      const day = days.get(`${month.key}-${String(index - leadingDays + 1).padStart(2, '0')}`);
      if (!day) return '<span class="bulk-calendar-blank" aria-hidden="true"></span>';
      if (day.recorded) return `<span class="bulk-calendar-day is-recorded" title="${escapeHtml(formatDate(day.value, true))} sudah dicatat"><span>${day.day}</span><svg aria-hidden="true"><use href="#icon-check"/></svg></span>`;
      if (!day.selectable) return `<span class="bulk-calendar-day is-unavailable" title="${escapeHtml(formatDate(day.value, true))} tidak dijadwalkan"><span>${day.day}</span></span>`;
      const invalidMessage = bulkChecklistState?.invalidDates.get(day.value);
      const title = invalidMessage ?? (day.holidayName ? `${formatDate(day.value, true)} · ${day.holidayName} (opsional)` : formatDate(day.value, true));
      return `<label class="bulk-calendar-day${day.holidayName ? ' is-holiday' : ''}${invalidMessage ? ' is-invalid' : ''}" title="${escapeHtml(title)}"><input type="checkbox" value="${escapeHtml(day.value)}" data-bulk-checklist-date data-week-key="${escapeHtml(day.weekKey)}" data-recorded-in-week="${day.recordedInWeek}"${bulkChecklistState?.selectedDates.has(day.value) ? ' checked' : ''} aria-label="${escapeHtml(`Catat ${formatDate(day.value, true)}${invalidMessage ? `, tidak dapat dicatat: ${invalidMessage}` : day.holidayName ? `, hari libur ${day.holidayName}` : ''}`)}"><span>${day.day}</span>${invalidMessage ? '<b aria-hidden="true">!</b>' : ''}</label>`;
    }).join('');
    return `<section class="bulk-calendar-month" aria-label="${escapeHtml(month.label)}"><h3>${escapeHtml(month.label)}</h3><div class="bulk-calendar-weekdays" aria-hidden="true">${weekdays.map((weekday) => `<span>${weekday}</span>`).join('')}</div><div class="bulk-calendar-grid">${cells}</div></section>`;
  }).join('');
}

function setAllBulkChecklistDates(form, checked) {
  updateBulkChecklistSelection(form);
  form.querySelectorAll('[data-bulk-checklist-date]').forEach((input) => {
    if (!input.disabled) input.checked = checked;
    updateBulkChecklistSelection(form);
  });
}

function showBulkChecklistMonth(form, monthKey) {
  updateBulkChecklistSelection(form);
  const month = bulkChecklistState?.months.find((item) => item.key === monthKey);
  if (!month) return;
  bulkChecklistState.monthKey = monthKey;
  form.querySelector('[data-bulk-checklist-calendar]').innerHTML = renderBulkChecklistCalendar([month]);
  updateBulkChecklistSelection(form);
}

function markBulkChecklistInvalidDates(form, fieldErrors = {}) {
  if (!bulkChecklistState) return;
  const invalidDates = Object.entries(fieldErrors)
    .filter(([field]) => field.startsWith('checklist_dates.'))
    .map(([field, messages]) => [field.replace('checklist_dates.', ''), Array.isArray(messages) ? messages[0] : messages]);
  if (!invalidDates.length) return;
  bulkChecklistState.invalidDates = new Map(invalidDates);
  const month = bulkChecklistState.months.find((item) => item.key === bulkChecklistState.monthKey);
  if (!month) return;
  form.querySelector('[data-bulk-checklist-calendar]').innerHTML = renderBulkChecklistCalendar([month]);
  updateBulkChecklistSelection(form);
}

function openBulkChecklist(activityId) {
  const activity = activePeriodActivities.find((item) => item.id === activityId);
  const months = activity ? bulkChecklistMonths(activity) : [];
  if (!activity || participantId === null || !months.length) {
    showToast('Belum ada tanggal yang dapat dipilih untuk aktivitas ini.', 'error');
    return;
  }

  const monthKey = months.some((month) => month.key === todayIso().slice(0, 7))
    ? todayIso().slice(0, 7)
    : months.at(-1).key;
  bulkChecklistState = {
    months,
    monthKey,
    selectedDates: new Set(),
    invalidDates: new Map(),
    daysByValue: new Map(months.flatMap((month) => month.days.map((day) => [day.value, day]))),
  };
  const currentMonth = months.find((month) => month.key === monthKey);

  adminFormDialog.classList.remove('is-period-config');
  adminFormDialog.dataset.returnView = '';
  adminFormDialogContent.innerHTML = `<form class="admin-modal-form bulk-checklist-form" data-admin-form="bulk-checklist" data-activity-id="${escapeHtml(activity.id)}" data-max-per-week="${Number(activity.max_per_week) || ''}"><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">Catat beberapa tanggal</h2><p>${escapeHtml(activity.name)} · ${escapeHtml(activityScheduleLabel(activity))}. Tanggal yang telah tercatat dikunci.</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields"><div class="bulk-calendar-toolbar"><p class="admin-form-note" data-bulk-selection-summary>Pilih tanggal yang masih tersedia pada kalender.</p><div><button class="text-button" type="button" data-bulk-checklist-clear>Kosongkan semua</button><button class="text-button" type="button" data-bulk-checklist-select>Pilih semua bulan ini</button></div></div><label class="bulk-calendar-month-select">Bulan<select data-bulk-checklist-month>${months.map((month) => `<option value="${escapeHtml(month.key)}"${month.key === monthKey ? ' selected' : ''}>${escapeHtml(month.label)}</option>`).join('')}</select></label><p class="bulk-calendar-legend"><span><i class="is-selectable"></i>Dapat dicatat</span><span><i class="is-recorded"></i>Sudah dicatat</span><span><i class="is-holiday"></i>Hari libur · opsional</span><span><i class="is-invalid"></i>Perlu diperiksa</span></p><div class="bulk-checklist-calendar" data-bulk-checklist-calendar aria-label="Kalender pencatatan ${escapeHtml(activity.name)}">${renderBulkChecklistCalendar([currentMonth])}</div></div><div class="admin-modal-actions"><button class="text-button" type="button" data-close-admin-modal>Batal</button><button class="primary-button" type="submit" data-bulk-submit disabled>Catat tanggal terpilih</button></div></form>`;
  adminFormDialog.showModal();
  updateBulkChecklistSelection(adminFormDialog.querySelector('[data-admin-form="bulk-checklist"]'));
  markAdminFormPristine();
  adminFormDialog.querySelector('[data-bulk-checklist-date]:not(:disabled)')?.focus();
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

function checklistSaveErrorKey(activityId) {
  return `${selectedChecklistDate}:${activityId}`;
}

function setChecklistSaveError(activityId, message = '') {
  const key = checklistSaveErrorKey(activityId);
  if (message) checklistSaveErrors.set(key, message);
  else checklistSaveErrors.delete(key);
  document.querySelectorAll(`[data-activity-id="${CSS.escape(activityId)}"]`).forEach((button) => {
    const error = checklistSaveErrors.get(key);
    if (button.classList.contains('quick-check')) {
      const label = button.querySelector('small');
      label.textContent = error ? 'Gagal disimpan — coba lagi' : button.classList.contains('is-done') ? 'Sudah dicatat' : 'Belum dicatat';
      label.classList.toggle('checklist-save-error', Boolean(error));
      return;
    }
    const item = button.closest('.checklist-item');
    const note = item.querySelector('.item-note');
    item.classList.toggle('has-save-error', Boolean(error));
    note.textContent = error ? 'Gagal disimpan — coba lagi' : item.classList.contains('is-done') ? 'Dicatat' : 'Belum dicatat';
    note.classList.toggle('checklist-save-error', Boolean(error));
  });
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
  document.querySelector('.full-checklist .section-head h2').textContent = selectedChecklistDate === todayIso()
    ? 'Catatan hari ini'
    : `Catatan ${formatDate(selectedChecklistDate, true)}`;
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
  activePeriod = data.period;
  activeParticipant = data.participant;
  if (viewerChanged) applyViewerIdentity(data.viewer);
  if (data.viewer.must_change_password) {
    openView('account', { persist: false, scroll: false });
    finishInitialBoot();
    return false;
  }
  if (!hasConfiguredRole(data.viewer.roles ?? [])) return false;
  if (data.period) {
    document.querySelector('#sidebar-period-name').textContent = data.period.name;
    document.querySelector('#dashboard-period-name').textContent = data.period.name;
    document.querySelector('#sidebar-period-end').textContent = `Berakhir ${formatDate(data.period.end_date)}`;
  } else {
    document.querySelector('#sidebar-period-name').textContent = 'Belum ada periode aktif';
    document.querySelector('#sidebar-period-end').textContent = 'Aktifkan periode untuk mulai mencatat';
  }

  if (!canUsePersonalLks(data.viewer.roles ?? [])) {
    participantId = null;
    activePeriodActivities = [];
    document.querySelector('.date-rail').innerHTML = '<p class="date-note">Akun Admin tidak memiliki catatan LKS pribadi.</p>';
    setChecklistEmpty('Admin menggunakan LKS untuk pemantauan dan koreksi, bukan pencatatan amalan pribadi.');
    renderPersonalSummaryUnavailable('Rekap pribadi hanya tersedia untuk Santri Karya dan Leader.');
    return true;
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
    return true;
  }

  if (data.period.is_open === false) {
    participantId = null;
    document.querySelector('.date-rail').innerHTML = `<p class="date-note">Periode ini telah disiapkan dan pencatatan akan dibuka pada ${formatDate(data.period.start_date)}.</p>`;
    setChecklistEmpty(`Periode ${data.period.name} belum dimulai. Checklist dapat diisi mulai ${formatDate(data.period.start_date)}.`);
    renderPersonalSummaryUnavailable(`Ringkasan tersedia setelah periode ${data.period.name} dimulai.`);
    renderUnavailableDashboard(`Periode ${data.period.name} dijadwalkan dimulai pada ${formatDate(data.period.start_date)}.`);
    return true;
  }

  if (data.participant === null) {
    participantId = null;
    const message = `Anda belum didaftarkan sebagai peserta ${data.period.name}. Hubungi Admin LKS untuk ditambahkan; catatan dapat dibuat mulai tanggal pendaftaran.`;
    document.querySelector('.date-rail').innerHTML = `<p class="date-note">${escapeHtml(message)}</p>`;
    setChecklistEmpty(message);
    renderPersonalSummaryUnavailable(message);
    return true;
  }

  participantId = data.participant.id;
  const maxDate = [todayIso(), dateInputValue(data.period.end_date)].sort()[0];
  const isHistoricalDate = selectedChecklistDate !== todayIso();
  const dateMessage = isHistoricalDate
    ? `Anda sedang melihat catatan lampau: ${formatDate(selectedChecklistDate, true)}.`
    : `Catat aktivitas untuk ${formatDate(selectedChecklistDate, true)}.`;
  document.querySelector('.date-rail').innerHTML = `<label class="checklist-date-control">Tanggal pencatatan<input type="date" data-checklist-date value="${escapeHtml(selectedChecklistDate)}" min="${escapeHtml(dateInputValue(data.participant.participation_start_date))}" max="${escapeHtml(maxDate)}"></label><p class="date-note">${escapeHtml(dateMessage)}</p>${isHistoricalDate ? '<button class="text-button" type="button" data-checklist-today>Kembali ke hari ini</button>' : ''}`;
  if (!data.activities.length) {
    setChecklistEmpty(`Tidak ada aktivitas yang dijadwalkan pada ${formatDate(selectedChecklistDate, true)}.`, 'Tidak ada aktivitas terjadwal');
  } else {
    quickChecks.innerHTML = data.activities.slice(0, 4).map(renderQuickCheck).join('');
    fullChecklist.innerHTML = data.activities.map(renderFullCheck).join('');
    setChecklistReady();
  }
  if (data.personal_summary) renderPersonalSummary(data.personal_summary);
  updateTodayProgress();
  return true;
}

async function apiError(response) {
  const body = await response.json().catch(() => ({}));
  const validationMessage = Object.values(body.errors ?? {}).flat()[0];
  if (validationMessage) return validationMessage;
  if (response.status === 404) return 'Data yang dipilih sudah tidak tersedia. Muat ulang data dan coba kembali.';
  if (response.status >= 500) return 'Server belum dapat memproses perubahan. Coba lagi beberapa saat lagi.';
  return body.message ?? 'Perubahan belum dapat disimpan. Coba lagi.';
}

async function apiFormError(response) {
  const body = await response.json().catch(() => ({}));
  const error = new Error(Object.values(body.errors ?? {}).flat()[0] ?? body.message ?? 'Data belum dapat disimpan. Coba lagi.');
  error.fieldErrors = body.errors ?? {};
  return error;
}

function percentage(value) {
  return `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(Number(value) || 0)}%`;
}

function initials(name) {
  return String(name).split(' ').filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
}

function statusBadge(status) {
  if (status === 'belum_mulai') return '<span class="status status-open">Belum mulai</span>';
  const complete = status === 'tuntas';
  return `<span class="status ${complete ? 'status-done' : 'status-open'}">${complete ? 'Tuntas' : 'Belum tuntas'}</span>`;
}

function participantStatus(participant) {
  return Number(participant.final_percentage) === 0 ? 'belum_mulai' : participant.final_status;
}

function renderActivityProgress(activity) {
  const progress = Math.min(Math.max(Number(activity.percentage) || 0, 0), 100);
  const complete = activity.status === 'tuntas';
  const remaining = Math.max(Number(activity.minimum_target_count) - Number(activity.completed_count), 0);
  const progressText = `${percentage(progress)} · ${activity.completed_count} dari ${activity.target_count} kali${complete ? ' · minimum terpenuhi' : ` · kurang ${remaining} kali`}`;
  return `<article class="activity-row"><div class="activity-name"><span class="activity-dot${complete ? ' completed' : ''}"></span><strong>${escapeHtml(activity.name)}</strong><small>${escapeHtml(activity.completed_count)} dari ${escapeHtml(activity.target_count)} kali · ${complete ? 'minimum terpenuhi' : `kurang ${remaining} kali menuju minimum`}</small></div><div class="activity-track" role="progressbar" aria-label="Capaian ${escapeHtml(activity.name)}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${progress}" aria-valuetext="${escapeHtml(progressText)}"><span style="width:${progress}%"></span></div><strong class="activity-value">${percentage(progress)}</strong>${statusBadge(activity.status)}</article>`;
}

function renderPersonalSummary(personal) {
  if (!personal || !canUsePersonalLks(viewer?.roles ?? [])) return;

  const activityList = document.querySelector('.dashboard-santri .activity-list');
  if (activityList) activityList.innerHTML = personal.activities.map(renderActivityProgress).join('') || '<p class="muted">Belum ada aktivitas pada periode ini.</p>';

  const score = percentage(personal.final_percentage);
  const status = personal.final_status === 'tuntas'
    ? 'Target periode tercapai'
    : Number(personal.final_percentage) === 0
      ? 'Belum mulai mencatat'
      : 'Belum mencapai ambang';
  const scoreBox = document.querySelector('.score-box');
  scoreBox.hidden = false;
  scoreBox.innerHTML = `<span>Nilai sementara</span><strong>${score}</strong><small>${status} · ambang ${percentage(personal.passing_threshold)}</small>`;

  const jobLevel = viewer.identity?.level === 'leader' ? 'Leader' : viewer.identity?.level === 'staff' ? 'Staff' : null;
  const details = [
    ['Tim', viewer.identity?.team], ['Leader', viewer.identity?.leader], ['Departemen', viewer.identity?.department], ['Level jabatan', jobLevel],
  ].filter(([, value]) => value).map(([label, value]) => `<li><span>${escapeHtml(label.slice(0, 1))}</span><div><small>${escapeHtml(label)}</small><strong>${escapeHtml(value)}</strong></div></li>`).join('');
  const exportLink = recapData?.period?.id ? `<a class="text-action export-pdf-link" href="${apiBase}/exports/personal/${encodeURIComponent(recapData.period.id)}" data-export-pdf>Unduh PDF LKS <svg><use href="#icon-arrow"/></svg></a>` : '';
  const recommendation = personal.recommendation ? `<section class="recommendation-summary"><h4>Arahan periode ini</h4><p>${escapeHtml(personal.recommendation)}</p></section>` : '';
  document.querySelector('.period-summary').innerHTML = `<h3>${personal.final_status === 'tuntas' ? 'Target periode tercapai.' : 'Masih ada ruang untuk bertumbuh.'}</h3>${details ? `<ul>${details}</ul>` : ''}${recommendation}${exportLink}<button class="text-action" type="button" data-go="history">Lihat riwayat <svg><use href="#icon-arrow"/></svg></button>`;
}

function renderPersonalRecap() {
  if (!recapData || !canUsePersonalLks(viewer?.roles ?? [])) return;
  const personal = recapData.participants.find((participant) => participant.user_id === viewer.id);
  if (personal) renderPersonalSummary(personal);
}

function renderRoleTrend(role) {
  const isLeader = role === 'leader';
  const title = isLeader ? 'Tren rata-rata tim' : 'Tren nilai pribadi';
  const description = isLeader
    ? 'Rata-rata anggota dalam bimbingan Anda pada enam periode terakhir.'
    : 'Perubahan nilai akhir Anda pada enam periode terakhir.';
  if (roleTrendError) return `<section class="report-trend role-trend"><div class="section-head"><div><h2>${title}</h2><p>${description}</p></div></div><p class="report-trend-empty">${escapeHtml(roleTrendError)} <button class="text-button" type="button" data-retry-role-trend>Coba lagi</button></p></section>`;
  if (roleTrendData.length < 2) return `<section class="report-trend role-trend"><div class="section-head"><div><h2>${title}</h2><p>${description}</p></div></div><p class="report-trend-empty">Tren akan tersedia setelah terdapat minimal dua periode LKS dalam cakupan Anda.</p></section>`;
  return `<section class="report-trend role-trend" aria-labelledby="role-trend-title"><div class="section-head"><div><h2 id="role-trend-title">${title}</h2><p>${description} Tanda — berarti belum ada data pada periode tersebut.</p></div></div><div class="report-trend-bars" role="list" aria-label="${title}">${roleTrendData.map((item) => {
    const value = item.average_percentage === null ? null : Math.min(Math.max(Number(item.average_percentage) || 0, 0), 100);
    return `<div class="report-trend-point" role="listitem"><strong>${value === null ? '—' : percentage(value)}</strong><span class="report-trend-meter"${value === null ? '' : ` role="progressbar" aria-label="${escapeHtml(item.period.name)}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${value}"`} >${value === null ? '' : `<i style="height:${Math.max(value, 2)}%"></i>`}</span><small>${escapeHtml(item.period.name)}</small></div>`;
  }).join('')}</div></section>`;
}

function dashboardNotice(title, message, action = '') {
  return `<div class="page-intro"><div><h2>${escapeHtml(title)}</h2><p>${escapeHtml(message)}</p></div>${action}</div>`;
}

function renderRoleDashboard() {
  if (!viewer || !recapData) return;
  const role = displayRole(viewer.roles ?? []);
  const dashboard = document.querySelector(`[data-role-panel="${role}"]`);
  const { period, participants, summary } = recapData;

  if (role === 'santri') {
    const trendPanel = dashboard.querySelector('[data-personal-trend]');
    if (trendPanel) trendPanel.innerHTML = renderRoleTrend('santri');
    return;
  }

  if (role === 'leader') {
    const teamNames = [...new Set(participants.map((participant) => participant.team).filter(Boolean))];
    const teamLabel = teamNames.length ? `Tim ${teamNames.join(' · ')}` : 'Anggota dalam bimbingan Anda';
    const attentionGap = (participant) => Math.max(Number(participant.passing_threshold) - Number(participant.final_percentage), 0);
    const needingAttention = participants.filter((participant) => participantStatus(participant) !== 'tuntas')
      .sort((left, right) => attentionGap(right) - attentionGap(left));
    const memberRows = [...participants].sort((left, right) => attentionGap(right) - attentionGap(left)).map((participant) => {
      const score = Math.min(Math.max(Number(participant.final_percentage) || 0, 0), 100);
      const threshold = Math.min(Math.max(Number(participant.passing_threshold) || 0, 0), 100);
      const gap = attentionGap(participant);
      return `<div class="leader-team-row" role="listitem"><div><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(participant.team ?? participant.department ?? 'Tanpa tim')}</small></div><div class="leader-team-bar" role="progressbar" aria-label="Capaian ${escapeHtml(participant.name)}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${score}" aria-valuetext="${percentage(score)}; ambang ${percentage(threshold)}"><i style="width:${score}%"></i><b style="left:${threshold}%" aria-hidden="true"></b></div><div class="leader-team-score"><strong>${percentage(score)}</strong><small>${gap ? `Kurang ${percentage(gap)}` : 'Ambang tercapai'}</small></div></div>`;
    }).join('');
    const priorityRows = needingAttention.slice(0, 5).map((participant, index) => {
      const priorityNames = (participant.activities ?? []).filter((activity) => activity.status !== 'tuntas').sort((left, right) => Number(left.percentage) - Number(right.percentage)).slice(0, 2).map((activity) => `${activity.name}: kurang ${Math.max(Number(activity.minimum_target_count) - Number(activity.completed_count), 0)} kali`).join(' · ');
      return `<button class="leader-priority-row" type="button" data-go="recap" data-recap-member="${escapeHtml(participant.participant_id)}"><span class="person-initials tone-${['one', 'two', 'three', 'four'][index % 4]}">${escapeHtml(initials(participant.name))}</span><span><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(priorityNames || participant.team || 'Buka rekap untuk rincian')}</small></span><span class="leader-priority-score"><strong>${percentage(participant.final_percentage)}</strong><small>Kurang ${percentage(attentionGap(participant))}</small></span>${statusBadge(participantStatus(participant))}</button>`;
    }).join('');
    const priorityEmpty = !participants.length
      ? 'Belum ada anggota dalam bimbingan Anda pada periode ini.'
      : 'Semua anggota sudah mencapai ambang pada periode ini.';
    const priorityPanel = `<section class="priority-panel"><div class="section-head"><div><h2>Perlu perhatian</h2><p class="supporting-copy">Urut dari selisih menuju ambang terbesar agar tindak lanjut lebih terarah.</p></div></div><div class="leader-priority-list">${priorityRows || `<p class="muted">${priorityEmpty}</p>`}</div></section>`;
    const teamPanel = `<section class="leader-team-panel" aria-labelledby="leader-team-title"><div class="section-head"><div><h2 id="leader-team-title">Capaian anggota</h2><p class="supporting-copy">Garis penanda menunjukkan ambang pribadi setiap anggota.</p></div><button class="text-button" type="button" data-go="recap">Lihat rincian</button></div><div class="leader-team-list" role="list">${memberRows || '<p class="muted">Belum ada anggota dalam bimbingan Anda pada periode ini.</p>'}</div></section>`;
    dashboard.innerHTML = `${dashboardNotice('Ringkasan tim', `${period.name} · ${teamLabel}.`, '<button class="primary-button" type="button" data-go="recap">Buka rekap</button>')}<section class="leader-overview leader-overview-metrics"><article><p>Anggota aktif</p><strong>${summary.participant_count}</strong><small>Dalam bimbingan Anda</small></article><article><p>Rata-rata tim</p><strong>${percentage(summary.average_percentage)}</strong><small>Capaian seluruh anggota</small></article><article><p>Sudah tuntas</p><strong>${summary.tuntas_count}</strong><small>${summary.participant_count ? percentage((summary.tuntas_count / summary.participant_count) * 100) : '0%'} anggota</small></article><article><p>Perlu perhatian</p><strong>${needingAttention.length}</strong><small>Belum mencapai ambang</small></article></section>${priorityPanel}${teamPanel}${renderRoleTrend('leader')}`;
    return;
  }

  if (role === 'admin') {
    const departments = [...(summary.departments ?? [])].sort((left, right) => Number(right.average_percentage) - Number(left.average_percentage));
    const departmentRows = departments.map((department) => `<button class="dashboard-department-bar dashboard-department-action" type="button" data-recap-department="${escapeHtml(department.department)}"><span><strong>${escapeHtml(department.department)}</strong><small>${escapeHtml(department.tuntas_count)} dari ${escapeHtml(department.participant_count)} tuntas · Buka rekap</small></span><span class="bar-rail" role="progressbar" aria-label="Capaian ${escapeHtml(department.department)}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${Math.min(Math.max(Number(department.average_percentage) || 0, 0), 100)}"><i style="width:${Math.min(Math.max(Number(department.average_percentage) || 0, 0), 100)}%"></i></span><strong>${percentage(department.average_percentage)}</strong></button>`).join('');
    const startedCount = Number(summary.recording_started_count) || 0;
    const lowestActivity = summary.activity_averages?.[0];
    const isCurrentPeriod = period.status === 'active';
    const periodMessage = isCurrentPeriod
      ? `${period.name} · tinjau kondisi periode sebelum mengubah konfigurasi.`
      : `${period.name} sudah ditutup · menampilkan laporan periode terakhir yang tersedia.`;
    const healthNote = summary.participant_count
      ? `${startedCount}/${summary.participant_count} peserta sudah mulai mencatat${lowestActivity ? ` · Aktivitas dengan rata-rata terendah: ${lowestActivity.name} (${percentage(lowestActivity.average_percentage)})` : ''}.`
      : 'Belum ada peserta dalam periode ini.';
    dashboard.innerHTML = `${dashboardNotice(isCurrentPeriod ? 'Capaian LKS organisasi' : 'Laporan organisasi terakhir', periodMessage, '<button class="primary-button" type="button" data-go="department">Buka laporan</button>')}<section class="admin-metrics"><article><p>Peserta aktif</p><strong>${summary.participant_count}</strong><span>Peserta periode ini</span></article><article><p>Rata-rata capaian</p><strong>${percentage(summary.average_percentage)}</strong><span>Perhitungan periode</span></article><article><p>Sudah tuntas</p><strong>${summary.tuntas_count}</strong><span>Peserta mencapai ambang</span></article><article><p>Belum tuntas</p><strong>${Math.max(summary.participant_count - summary.tuntas_count, 0)}</strong><span>Perlu tindak lanjut</span></article></section><p class="admin-dashboard-health">${escapeHtml(healthNote)}</p><section class="department-snapshot"><div class="section-head"><div><h2>Capaian seluruh departemen</h2><p class="supporting-copy">Pilih departemen untuk melihat anggota dan melakukan tindak lanjut.</p></div><button class="text-button" type="button" data-go="department">Lihat laporan</button></div><div class="department-bars" role="list">${departmentRows || '<p class="muted">Belum ada data departemen pada periode ini.</p>'}</div></section>`;
  }
}

function renderUnavailableDashboard(message) {
  if (!viewer) return;
  const role = displayRole(viewer.roles ?? []);
  if (role === 'santri') return;
  const dashboard = document.querySelector(`[data-role-panel="${role}"]`);
  dashboard.innerHTML = dashboardNotice('Ringkasan belum tersedia', message, '<button class="primary-button" type="button" data-go="recap">Coba buka rekap</button>');
}

function resetHistoryDetail() {
  historyDetailRequest += 1;
  historyRecapData = null;
  historyParticipantId = '';
  historyDetailLoading = false;
  historyDetailError = '';
  historyDetailPeriodId = '';
}

function visibleHistoryPeriods() {
  if (!historyData) return [];
  const years = [...new Set(historyData.map((period) => String(period.start_date).slice(0, 4)))];
  if (!years.includes(historyYear)) historyYear = years[0] ?? '';
  return historyYear ? historyData.filter((period) => String(period.start_date).startsWith(historyYear)) : historyData;
}

function renderHistory() {
  const panel = document.querySelector('[data-view-panel="history"]');
  const isLeader = viewer?.roles?.includes('leader');
  const viewingTeam = isLeader && historyScope === 'team';
  const title = viewingTeam ? 'Riwayat tim' : 'Riwayat LKS saya';
  const description = viewingTeam
    ? 'Rekap periode tertutup untuk anggota dalam bimbingan Anda. Data dan aturan periode tetap tersimpan.'
    : 'Rekap periode LKS Anda yang sudah ditutup tetap menggunakan data dan aturan saat periode tersebut berjalan.';
  const scopeToggle = isLeader ? `<nav class="history-scope-toggle" aria-label="Cakupan riwayat"><button class="filter-pill${viewingTeam ? ' is-on' : ''}" type="button" data-history-scope="team" aria-pressed="${viewingTeam}">Riwayat tim</button><button class="filter-pill${viewingTeam ? '' : ' is-on'}" type="button" data-history-scope="personal" aria-pressed="${!viewingTeam}">LKS saya</button></nav>` : '';
  if (!historyData) {
    panel.innerHTML = `${dashboardNotice('Memuat riwayat', 'Riwayat periode tertutup sedang disiapkan.')} `;
    return;
  }

  if (!historyData.length) {
    const emptyMessage = viewingTeam
      ? 'Belum ada periode tertutup yang memiliki anggota dalam bimbingan Anda.'
      : 'Periode LKS Anda yang sudah ditutup akan tersimpan di sini.';
    const emptyAction = viewingTeam
      ? '<button class="primary-button" type="button" data-go="recap">Buka rekap tim</button>'
      : '<button class="primary-button" type="button" data-go="lks">Buka LKS Saya</button>';
    panel.innerHTML = `<div class="page-intro"><div><h2>Belum ada riwayat periode</h2><p>${emptyMessage}</p></div><div class="history-page-actions">${scopeToggle}${emptyAction}</div></div>`;
    return;
  }

  const periods = visibleHistoryPeriods();
  const selected = historyRecapData?.period?.id ?? historyDetailPeriodId;
  const years = [...new Set(historyData.map((period) => String(period.start_date).slice(0, 4)))];
  const yearControl = years.length > 1 ? `<label class="history-year-select">Tahun<select data-history-year>${years.map((year) => `<option value="${year}"${year === historyYear ? ' selected' : ''}>${year}</option>`).join('')}</select></label>` : '';
  const periodRows = periods.map((period) => `<button class="history-period-row${period.id === selected ? ' is-selected' : ''}" type="button" data-history-period="${escapeHtml(period.id)}"${period.id === selected ? ' aria-current="page"' : ''}><span><strong>${escapeHtml(period.name)}</strong><small>${formatDate(period.start_date, true)} — ${formatDate(period.end_date, true)}</small></span><span>${period.id === selected ? 'Ditinjau' : 'Lihat rekap'}</span></button>`).join('');
  const periodSelect = `<label class="history-period-select">Periode<select data-history-period-select>${periods.map((period) => `<option value="${escapeHtml(period.id)}"${period.id === selected ? ' selected' : ''}>${escapeHtml(period.name)} · ${formatDate(period.end_date)}</option>`).join('')}</select></label>`;
  const detail = historyDetailLoading
    ? '<p class="history-empty-detail">Menyiapkan rekap periode…</p>'
    : historyDetailError
      ? `<div class="history-error"><p>${escapeHtml(historyDetailError)}</p><button class="text-button" type="button" data-history-retry>Coba lagi</button></div>`
      : historyRecapData
        ? renderHistoryDetail(historyRecapData)
        : '<p class="history-empty-detail">Pilih periode untuk melihat rekap akhirnya.</p>';

  panel.innerHTML = `<div class="page-intro"><div><h2>${title}</h2><p>${description}</p></div><div class="history-page-actions">${scopeToggle}${yearControl}<button class="text-button admin-refresh" type="button" data-history-refresh>Muat ulang data</button></div></div><section class="history-layout">${periodSelect}<nav class="history-period-list" aria-label="Daftar periode tertutup">${periodRows}</nav><section class="history-detail" aria-live="polite">${detail}</section></section>`;
}

function renderHistoryActivities(participant) {
  return renderRecapActivities(participant);
}

function renderHistoryParticipantDetail(participant, { label = 'Nilai akhir', includeExport = false } = {}) {
  const activities = renderHistoryActivities(participant);
  const recommendation = participant.recommendation ? `<section class="recommendation-summary"><h4>Rekomendasi akhir</h4><p>Disimpan saat periode ditutup.</p><p>${escapeHtml(participant.recommendation)}</p></section>` : '';
  const exportLink = includeExport && historyRecapData?.period?.id
    ? `<a class="text-action export-pdf-link" href="${apiBase}/exports/personal/${encodeURIComponent(historyRecapData.period.id)}" data-export-pdf>Unduh PDF LKS <svg><use href="#icon-arrow"/></svg></a>`
    : '';
  return `<section class="history-participant-detail"><div class="history-score"><span>${escapeHtml(label)}</span><strong>${percentage(participant.final_percentage)}</strong><small>Ambang tuntas ${percentage(participant.passing_threshold)}</small>${statusBadge(participantStatus(participant))}</div>${recommendation}${activities ? `<ul class="history-activity-list">${activities}</ul>` : '<p class="history-empty-detail">Tidak ada aktivitas yang berlaku untuk peserta ini.</p>'}${exportLink}</section>`;
}

function renderHistoryDetail(data) {
  const { period, participants, summary } = data;
  const personal = historyScope === 'personal' ? participants.find((participant) => participant.user_id === viewer?.id) : null;
  const teamMembers = !personal ? [...participants].sort((left, right) => Number(left.final_percentage) - Number(right.final_percentage) || left.name.localeCompare(right.name, 'id')) : [];
  const selectedMember = teamMembers.find((participant) => participant.participant_id === historyParticipantId) ?? teamMembers[0] ?? null;
  const teamScore = `<div class="history-score"><span>Ringkasan tim</span><strong>${summary.participant_count}</strong><small>${summary.tuntas_count} tuntas · ${summary.belum_tuntas_count} belum tuntas · Rata-rata ${percentage(summary.average_percentage)}</small></div>`;
  const memberPicker = teamMembers.length ? `<section class="history-team-members"><div class="section-head"><div><h3>Capaian anggota</h3><p class="supporting-copy">Pilih anggota untuk meninjau capaian dan arahan akhirnya.</p></div></div><label class="history-member-select">Anggota<select data-history-participant-select>${teamMembers.map((participant) => `<option value="${escapeHtml(participant.participant_id)}"${participant.participant_id === selectedMember?.participant_id ? ' selected' : ''}>${escapeHtml(participant.name)} · ${percentage(participant.final_percentage)}</option>`).join('')}</select></label><div class="history-member-list" role="list">${teamMembers.map((participant) => `<button class="history-member-row${participant.participant_id === selectedMember?.participant_id ? ' is-selected' : ''}" type="button" role="listitem" data-history-participant="${escapeHtml(participant.participant_id)}" aria-pressed="${participant.participant_id === selectedMember?.participant_id}"><span><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(participant.team ?? 'Tanpa tim')} · Ambang ${percentage(participant.passing_threshold)}</small></span><span><strong>${percentage(participant.final_percentage)}</strong>${statusBadge(participantStatus(participant))}</span></button>`).join('')}</div></section>` : '<p class="history-empty-detail">Tidak ada anggota dalam cakupan tim pada periode ini.</p>';
  const participantDetail = personal
    ? renderHistoryParticipantDetail(personal, { includeExport: true })
    : selectedMember
      ? renderHistoryParticipantDetail(selectedMember, { label: `Capaian ${selectedMember.name}` })
      : '';
  const closedAt = period.closed_at ? `<small>Ditutup ${formatDate(period.closed_at, true)}</small>` : '';

  return `<div class="history-detail-head"><span class="status status-closed">Ditutup</span><h3>${escapeHtml(period.name)}</h3><p>${formatDate(period.start_date, true)} — ${formatDate(period.end_date, true)}</p>${closedAt}</div>${personal ? '' : teamScore}${memberPicker}${participantDetail}`;
}

async function loadHistory({ showProgress = false } = {}) {
  if (showProgress) showPageProgress('Memuat riwayat periode…');
  try {
    const scope = viewer?.roles?.includes('leader') && historyScope === 'team' ? '?scope=team' : '?scope=personal';
    const response = await fetch(`${apiBase}/periods/history${scope}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    historyData = (await response.json()).data;
    const periods = visibleHistoryPeriods();
    if (!periods.some((period) => period.id === historyRecapData?.period?.id)) {
      resetHistoryDetail();
      renderHistory();
      if (periods[0]) await loadHistoryRecap(periods[0].id);
      return;
    }
    renderHistory();
  } catch (error) {
    document.querySelector('[data-view-panel="history"]').innerHTML = `${dashboardNotice('Riwayat belum dapat dimuat', escapeHtml(error.message || 'Coba lagi beberapa saat lagi.'), '<button class="primary-button" type="button" data-history-refresh>Coba lagi</button>')}`;
  } finally {
    if (showProgress) hidePageProgress();
  }
}

async function loadHistoryRecap(periodId, trigger) {
  const requestId = ++historyDetailRequest;
  historyDetailPeriodId = periodId;
  historyDetailLoading = true;
  historyDetailError = '';
  renderHistory();
  setButtonBusy(trigger, true, 'Memuat…');
  try {
    const scope = historyScope === 'personal' ? '&scope=personal' : '';
    const response = await fetch(`${apiBase}/recap?period_id=${encodeURIComponent(periodId)}${scope}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    if (requestId !== historyDetailRequest) return;
    historyRecapData = (await response.json()).data;
    historyParticipantId = historyScope === 'team'
      ? [...historyRecapData.participants].sort((left, right) => Number(left.final_percentage) - Number(right.final_percentage) || left.name.localeCompare(right.name, 'id'))[0]?.participant_id ?? ''
      : '';
  } catch (error) {
    if (requestId !== historyDetailRequest) return;
    historyRecapData = null;
    historyDetailError = error.message || 'Rekap periode belum dapat dimuat.';
  } finally {
    if (requestId === historyDetailRequest) {
      historyDetailLoading = false;
      renderHistory();
    }
    setButtonBusy(trigger, false);
  }
}

function renderRecapUnavailable(message) {
  const safeMessage = escapeHtml(message);
  const isAdmin = viewer?.roles?.includes('admin');
  const isLeader = viewer?.roles?.includes('leader');
  const personalScope = !isAdmin && (!isLeader || recapScope === 'personal');
  const title = isAdmin ? 'Rekap belum tersedia' : personalScope ? 'Rekap Saya belum tersedia' : 'Rekap tim belum tersedia';
  const detail = isAdmin
    ? 'Belum ada periode aktif atau tertutup yang dapat direkap.'
    : personalScope
    ? 'Belum ada periode aktif atau tertutup yang terkait dengan LKS Anda.'
    : 'Belum ada anggota dalam bimbingan Anda pada periode aktif atau tertutup.';
  document.querySelector('[data-view-panel="recap"]').innerHTML = `<div class="page-intro"><div><h2>${title}</h2><p>${safeMessage}</p></div><button class="text-button" type="button" data-retry-recap>Coba lagi</button></div><p class="empty-search">${detail}</p>`;
  document.querySelector('[data-view-panel="department"]').innerHTML = `<div class="page-intro"><div><h2>Capaian departemen</h2><p>${safeMessage}</p></div></div><p class="empty-search">Coba lagi setelah periode dan peserta tersedia.</p>`;
}

function reportDimensionLabel(dimension) {
  return { department: 'Departemen', leader: 'Leader', gender: 'Ikhwan dan Akhwat' }[dimension] ?? 'Laporan';
}

function reportGroups(participants, dimension) {
  const labels = { ikhwan: 'Ikhwan', akhwat: 'Akhwat' };
  const groups = new Map();
  participants.forEach((participant) => {
    const id = dimension === 'department'
      ? participant.department_id ?? 'unassigned'
      : dimension === 'leader'
        ? participant.leader_user_id ?? 'unassigned'
        : participant.gender ?? 'unknown';
    const label = dimension === 'department'
      ? participant.department ?? 'Tanpa departemen'
      : dimension === 'leader'
        ? participant.leader ?? 'Belum ditetapkan'
        : labels[participant.gender] ?? 'Tidak dicatat';
    const group = groups.get(id) ?? { id, name: label, participantCount: 0, totalPercentage: 0, tuntasCount: 0 };
    group.participantCount += 1;
    group.totalPercentage += Number(participant.final_percentage) || 0;
    group.tuntasCount += participant.final_status === 'tuntas' ? 1 : 0;
    groups.set(id, group);
  });

  return [...groups.values()]
    .map((group) => ({ ...group, averagePercentage: group.participantCount ? group.totalPercentage / group.participantCount : 0 }))
    .sort((left, right) => right.averagePercentage - left.averagePercentage || left.name.localeCompare(right.name, 'id'));
}

function renderReportBars(groups, label, dimension) {
  if (!groups.length) return '<p class="empty-search">Belum ada peserta pada periode ini.</p>';
  return `<div class="report-bar-list" aria-label="Capaian berdasarkan ${escapeHtml(label)}">${groups.map((group) => {
    const value = Math.min(Math.max(group.averagePercentage, 0), 100);
    const row = `<span><strong>${escapeHtml(group.name)}</strong><small>${group.tuntasCount} dari ${group.participantCount} tuntas${dimension === 'department' ? ' · Buka rekap' : ''}</small></span><span class="report-bar-meter" role="progressbar" aria-label="Rata-rata capaian ${escapeHtml(group.name)}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${value}"><i style="width:${value}%"></i></span><strong>${percentage(value)}</strong>`;
    return dimension === 'department'
      ? `<button class="report-bar-row report-bar-action" type="button" data-recap-department="${escapeHtml(group.name)}" aria-label="Buka rekap ${escapeHtml(group.name)}">${row}</button>`
      : `<article class="report-bar-row" role="listitem">${row}</article>`;
  }).join('')}</div>`;
}

function renderReportRankings(groups) {
  if (!groups.length) return '';
  const list = (items) => `<ol>${items.map((group) => `<li><span>${escapeHtml(group.name)}</span><strong>${percentage(group.averagePercentage)}</strong><small>${group.tuntasCount}/${group.participantCount} tuntas</small></li>`).join('')}</ol>`;
  if (groups.length <= 5) return `<section class="report-rankings report-rankings-single" aria-label="Urutan capaian"><div><h3>Urutan capaian</h3>${list(groups)}</div></section>`;
  const rankCount = Math.min(3, groups.length);
  return `<section class="report-rankings" aria-label="Peringkat capaian"><div><h3>${rankCount} capaian tertinggi</h3>${list(groups.slice(0, rankCount))}</div><div><h3>${rankCount} capaian terendah</h3>${list(groups.slice(-rankCount).reverse())}</div></section>`;
}

function renderReportTrend(dimension) {
  const periods = departmentTrendData.slice(-6);
  if (periods.length < 2) return '<p class="report-trend-empty">Tren tersedia setelah minimal dua periode memiliki data.</p>';
  const groups = new Map();
  periods.forEach((item) => (item.groups?.[dimension] ?? []).forEach((group) => {
    if (!groups.has(group.id)) groups.set(group.id, { id: group.id, name: group.name });
  }));
  const options = [...groups.values()];
  if (!options.length) return '<p class="report-trend-empty">Belum ada kelompok untuk ditampilkan pada tren ini.</p>';
  if (!options.some((group) => group.id === selectedTrendGroupId)) selectedTrendGroupId = options[0].id;
  const selected = options.find((group) => group.id === selectedTrendGroupId);
  const points = periods.map((item) => {
    const group = (item.groups?.[dimension] ?? []).find((candidate) => candidate.id === selected.id);
    return { label: item.period.name, value: group ? Number(group.average_percentage) || 0 : null };
  });
  return `<section class="report-trend" aria-labelledby="report-trend-title"><div class="section-head"><div><h2 id="report-trend-title">Tren bulanan</h2><p>Perubahan capaian enam periode terakhir. Tanda — berarti kelompok belum memiliki peserta pada periode tersebut.</p></div><label class="report-period-control">Kelompok<select data-trend-group>${options.map((group) => `<option value="${escapeHtml(group.id)}"${group.id === selected.id ? ' selected' : ''}>${escapeHtml(group.name)}</option>`).join('')}</select></label></div><div class="report-trend-bars" role="list" aria-label="Tren ${escapeHtml(selected.name)}">${points.map((point) => `<div class="report-trend-point" role="listitem"><strong>${point.value === null ? '—' : percentage(point.value)}</strong><span class="report-trend-meter">${point.value === null ? '' : `<i style="height:${Math.max(point.value, 2)}%"></i>`}</span><small>${escapeHtml(point.label)}</small></div>`).join('')}</div></section>`;
}

function renderRecapActivities(participant) {
  return participant.activities?.map((activity) => `<li><span><strong>${escapeHtml(activity.name)}</strong><small>${activity.completed_count}/${activity.target_count} kali · minimum ${activity.minimum_target_count} kali</small></span><span>${statusBadge(activity.status)}<strong>${percentage(activity.percentage)}</strong></span></li>`).join('') ?? '';
}

function openRecapDetail(participantId) {
  const participant = recapData?.participants.find((item) => item.participant_id === participantId);
  if (!participant || !recapData) return;

  const activities = renderRecapActivities(participant);
  const closed = recapData.period.status === 'closed';
  const recommendation = participant.recommendation
    ? `<section class="recommendation-summary"><h4>${closed ? 'Rekomendasi akhir' : 'Arahan periode ini'}</h4>${closed ? '<p>Disimpan saat periode ditutup.</p>' : ''}<p>${escapeHtml(participant.recommendation)}</p></section>`
    : '';
  const personalExport = participant.user_id === viewer?.id && canUsePersonalLks(viewer?.roles ?? [])
    ? `<a class="text-action export-pdf-link" href="${apiBase}/exports/personal/${encodeURIComponent(recapData.period.id)}" data-export-pdf>Unduh PDF LKS <svg><use href="#icon-arrow"/></svg></a>`
    : '';

  adminFormDialog.classList.remove('is-period-config');
  adminFormDialog.dataset.returnView = 'recap';
  adminFormDialogContent.innerHTML = `<section class="admin-modal-form recap-detail-modal"><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">Rincian capaian</h2><p>${escapeHtml(participant.name)} · ${escapeHtml(participant.team ?? 'Tanpa tim')} · ${escapeHtml(recapData.period.name)}</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields"><section class="history-participant-detail"><div class="history-score"><span>Nilai akhir</span><strong>${percentage(participant.final_percentage)}</strong><small>Ambang tuntas ${percentage(participant.passing_threshold)}</small>${statusBadge(participantStatus(participant))}</div>${recommendation}${activities ? `<ul class="history-activity-list">${activities}</ul>` : '<p class="history-empty-detail">Tidak ada aktivitas yang berlaku untuk peserta ini.</p>'}${personalExport}</section></div><div class="admin-modal-actions"><button class="primary-button" type="button" data-close-admin-modal>Tutup</button></div></section>`;
  adminFormDialog.showModal();
  adminFormDialog.querySelector('[data-close-admin-modal]')?.focus();
}

function renderRecapViews() {
  const recapPanel = document.querySelector('[data-view-panel="recap"]');
  const departmentPanel = document.querySelector('[data-view-panel="department"]');
  if (!recapData) return;

  const { period, participants, summary } = recapData;
  const isAdmin = viewer?.roles?.includes('admin');
  const isLeader = viewer?.roles?.includes('leader');
  const personalScope = !isAdmin && (!isLeader || recapScope === 'personal');
  const canExportIndividual = viewer?.roles?.includes('admin');
  const canCorrect = viewer?.roles?.includes('admin') && period.status === 'active';
  const recapRows = participants.map((participant, index) => {
    const actions = canExportIndividual
      ? `<span class="organization-row-actions recap-row-actions"><a class="text-button organization-row-action" href="${apiBase}/admin/exports/periods/${encodeURIComponent(period.id)}/participants/${encodeURIComponent(participant.participant_id)}" data-export-pdf>PDF</a>${canCorrect ? `<button class="text-button organization-row-action" type="button" data-open-checklist-correction="${escapeHtml(participant.participant_id)}">Lihat &amp; koreksi</button>` : ''}</span>`
      : `<span class="organization-row-actions recap-row-actions"><button class="text-button organization-row-action" type="button" data-open-recap-detail="${escapeHtml(participant.participant_id)}">Lihat rincian</button></span>`;
    const recommendation = participant.recommendation ? `<details class="recap-recommendation"><summary>Lihat arahan</summary><p>${escapeHtml(participant.recommendation)}</p></details>` : '';
    const department = participant.department ?? 'Tanpa departemen';
    const finalStatus = participantStatus(participant);
    return `<div class="table-row recap-row" data-participant-id="${escapeHtml(participant.participant_id)}" data-member="${escapeHtml(`${participant.name} ${department}`)}" data-status="${escapeHtml(finalStatus)}" data-gender="${escapeHtml(participant.gender)}" data-department="${escapeHtml(department)}" data-team="${escapeHtml(participant.team ?? '')}"><div class="member-cell"><span class="person-initials tone-${['one', 'two', 'three', 'four'][index % 4]}">${escapeHtml(initials(participant.name))}</span><div><strong>${escapeHtml(participant.name)}</strong><small>${escapeHtml(participant.team ?? 'Tanpa tim')} · Leader: ${escapeHtml(participant.leader ?? 'Belum ditetapkan')}</small></div></div><span class="recap-department"><small>Departemen</small>${escapeHtml(department)}</span><strong class="recap-score"><small>Nilai akhir</small>${percentage(participant.final_percentage)}</strong><span class="recap-status">${statusBadge(finalStatus)}<small>Ambang ${percentage(participant.passing_threshold)}</small>${recommendation}</span>${actions}</div>`;
  }).join('');
  const periodOptions = recapPeriods.map((item) => `<option value="${escapeHtml(item.id)}"${item.id === period.id ? ' selected' : ''}>${escapeHtml(item.name)}${item.status === 'closed' ? ' · Ditutup' : ' · Aktif'}</option>`).join('');
  const departments = [...new Set(participants.map((participant) => participant.department ?? 'Tanpa departemen'))].sort((left, right) => left.localeCompare(right, 'id'));
  const teams = [...new Set(participants.map((participant) => participant.team).filter(Boolean))].sort((left, right) => left.localeCompare(right, 'id'));
  if (!departments.includes(recapDepartmentFilter)) recapDepartmentFilter = '';
  if (!teams.includes(recapTeamFilter)) recapTeamFilter = '';
  const departmentFilter = canExportIndividual ? `<label class="report-period-control recap-department-filter">Departemen<select data-recap-department-select><option value="">Semua departemen</option>${departments.map((department) => `<option value="${escapeHtml(department)}"${department === recapDepartmentFilter ? ' selected' : ''}>${escapeHtml(department)}</option>`).join('')}</select></label>` : '';
  const teamFilter = isLeader && !personalScope && teams.length > 1 ? `<label class="report-period-control recap-team-filter">Tim<select data-recap-team-select><option value="">Semua tim</option>${teams.map((team) => `<option value="${escapeHtml(team)}"${team === recapTeamFilter ? ' selected' : ''}>${escapeHtml(team)}</option>`).join('')}</select></label>` : '';
  const focusedParticipant = participants.find((participant) => participant.participant_id === recapMemberFocus);
  if (recapMemberFocus && !focusedParticipant) recapMemberFocus = '';
  const focusNotice = focusedParticipant ? `<p class="recap-focus-note">Menampilkan ${escapeHtml(focusedParticipant.name)} dari daftar prioritas Leader. <button class="text-button" type="button" data-clear-recap-member>Tampilkan semua</button></p>` : '';
  const belumMulaiCount = participants.filter((participant) => Number(participant.final_percentage) === 0).length;
  const sedangBerjalanCount = Math.max(0, summary.belum_tuntas_count - belumMulaiCount);
  const personalParticipant = personalScope ? participants[0] : null;
  const threshold = personalParticipant ? percentage(personalParticipant.passing_threshold) : `Leader ${percentage(period.leader_passing_threshold)} · Staff ${percentage(period.staff_passing_threshold ?? 85)}`;
  const recapSummary = `<dl class="recap-summary" aria-label="Ringkasan rekap"><div><dt>${personalScope ? 'Status' : 'Peserta'}</dt><dd>${personalScope && personalParticipant ? participantStatus(personalParticipant) === 'tuntas' ? 'Tuntas' : 'Belum tuntas' : summary.participant_count}</dd></div><div><dt>Tuntas</dt><dd>${summary.tuntas_count}</dd></div><div><dt>Berjalan</dt><dd>${sedangBerjalanCount}</dd></div><div><dt>Belum mulai</dt><dd>${belumMulaiCount}</dd></div><div><dt>${personalScope ? 'Nilai akhir' : 'Rata-rata'}</dt><dd>${percentage(summary.average_percentage)}</dd></div><div><dt>Ambang</dt><dd>${threshold}</dd></div></dl>`;
  const title = isAdmin ? 'Perkembangan anggota' : personalScope ? 'Rekap Saya' : 'Perkembangan tim';
  const description = personalScope
    ? `${period.name} · Nilai, aktivitas, dan arahan LKS Anda.`
    : `${period.name} · Rekap anggota dalam bimbingan Anda.`;
  const scopeToggle = isLeader ? `<nav class="history-scope-toggle recap-scope-toggle" aria-label="Cakupan rekap"><button class="filter-pill${personalScope ? '' : ' is-on'}" type="button" data-recap-scope="team" aria-pressed="${!personalScope}">Tim Saya</button><button class="filter-pill${personalScope ? ' is-on' : ''}" type="button" data-recap-scope="personal" aria-pressed="${personalScope}">LKS Saya</button></nav>` : '';
  const periodState = period.status === 'closed' ? '<p class="recap-period-state"><span class="status status-closed">Periode ditutup</span> Data final dan capaian tidak dapat diubah.</p>' : '';
  const filters = personalScope ? '' : `<div class="filter-bar"><label class="search-field"><svg aria-hidden="true"><use href="#icon-search"/></svg><span class="sr-only">Cari anggota</span><input id="member-search" type="search" aria-label="Cari anggota" placeholder="Cari nama anggota" /></label><button class="filter-pill is-on" type="button" data-recap-filter="all" aria-pressed="true">Semua status</button><button class="filter-pill" type="button" data-recap-filter="perlu_perhatian" aria-pressed="false">Perlu perhatian</button><button class="filter-pill" type="button" data-recap-filter="belum_mulai" aria-pressed="false">Belum mulai</button><button class="filter-pill" type="button" data-recap-filter="tuntas" aria-pressed="false">Tuntas</button><button class="filter-pill is-on" type="button" data-recap-gender="all" aria-pressed="true">Semua gender</button><button class="filter-pill" type="button" data-recap-gender="ikhwan" aria-pressed="false">Ikhwan</button><button class="filter-pill" type="button" data-recap-gender="akhwat" aria-pressed="false">Akhwat</button></div>`;

  recapPanel.innerHTML = `<div class="page-intro recap-intro"><div><h2>${title}</h2><p>${escapeHtml(description)}</p>${periodState}</div><div class="recap-page-actions">${scopeToggle}<label class="report-period-control">Periode<select data-recap-period>${periodOptions}</select></label>${departmentFilter}${teamFilter}</div></div>${recapSummary}${filters}${personalScope ? '' : '<p class="filter-result-count" id="recap-filter-results" role="status" aria-live="polite"></p>'}${focusNotice}<section class="recap-table" aria-label="${personalScope ? 'Rekap saya' : 'Rekap anggota'}"><div class="table-head"><span>${personalScope ? 'LKS Saya' : 'Santri Karya'}</span><span>Departemen</span><span>Nilai</span><span>Status</span><span>Aksi</span></div><div id="recap-rows">${recapRows || `<p class="empty-search">${personalScope ? 'Belum ada data LKS pada periode ini.' : 'Belum ada anggota dalam cakupan tim pada periode ini.'}</p>`}</div></section><p class="empty-search" id="empty-search" hidden>Tidak ada anggota yang sesuai dengan pencarian atau filter tersebut.</p>`;
  if (!personalScope) filterRecapRows();

  if (!viewer?.roles?.includes('admin')) {
    departmentPanel.innerHTML = `<div class="page-intro"><div><h2>Capaian departemen</h2><p>Ringkasan lintas departemen tersedia untuk Admin.</p></div></div><p class="empty-search">Gunakan akun Admin untuk melihat perbandingan capaian tiap departemen.</p>`;
    return;
  }

  const reportGroupsForDimension = reportGroups(participants, selectedReportDimension);
  const reportLabel = reportDimensionLabel(selectedReportDimension);
  const tabs = ['department', 'leader', 'gender'].map((dimension) => `<button class="report-tab${selectedReportDimension === dimension ? ' is-active' : ''}" type="button" role="tab" aria-selected="${selectedReportDimension === dimension}" data-report-dimension="${dimension}">${reportDimensionLabel(dimension)}</button>`).join('');
  departmentPanel.innerHTML = `<div class="page-intro"><div><h2>Laporan periode</h2><p>${escapeHtml(period.name)} · Bandingkan capaian dari data snapshot periode yang dipilih.</p></div><div class="report-page-actions"><label class="report-period-control">Periode<select data-recap-period>${periodOptions}</select></label><a class="primary-button export-pdf-link" href="${apiBase}/admin/exports/periods/${encodeURIComponent(period.id)}" data-export-pdf>Ekspor PDF</a></div></div><nav class="report-tabs" role="tablist" aria-label="Dimensi laporan">${tabs}</nav><section class="report-overview" aria-live="polite"><div class="section-head"><div><h2>Capaian ${escapeHtml(reportLabel)}</h2><p>Rata-rata nilai akhir per kelompok.</p></div></div>${renderReportBars(reportGroupsForDimension, reportLabel, selectedReportDimension)}</section>${renderReportRankings(reportGroupsForDimension)}${renderReportTrend(selectedReportDimension)}`;
}

async function loadRecap(periodId = '', { showProgress = true, scope = viewer?.roles?.includes('leader') ? recapScope : 'personal' } = {}) {
  const requestId = ++recapRequest;
  recapAbortController?.abort();
  recapAbortController = new AbortController();
  const { signal } = recapAbortController;
  if (showProgress) showPageProgress('Memuat rekap…');
  try {
    if (viewer?.roles?.includes('leader')) recapScope = scope === 'personal' ? 'personal' : 'team';
    const recapQuery = new URLSearchParams();
    if (periodId) recapQuery.set('period_id', periodId);
    if (!viewer?.roles?.includes('admin')) recapQuery.set('scope', scope);
    const recapUrl = `${apiBase}/recap${recapQuery.size ? `?${recapQuery}` : ''}`;
    const historyScope = viewer?.roles?.includes('leader') ? scope : 'personal';
    const [response, historyResponse, trendResponse, roleTrendResponse] = await Promise.all([
      fetch(recapUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal }),
      fetch(`${apiBase}/periods/history?scope=${encodeURIComponent(historyScope)}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal }),
      viewer?.roles?.includes('admin') ? fetch(`${apiBase}/department-trends`, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal }) : Promise.resolve(null),
      canUsePersonalLks(viewer?.roles ?? []) ? fetch(`${apiBase}/role-trends`, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal }) : Promise.resolve(null),
    ]);
    if (!response.ok) throw new Error(await apiError(response));
    if (requestId !== recapRequest) return;
    recapData = (await response.json()).data;
    recapPeriods = [recapData.period];
    if (historyResponse.ok) {
      const periods = (await historyResponse.json()).data;
      recapPeriods.push(...periods.filter((item) => item.id !== recapData.period.id));
    }
    departmentTrendData = trendResponse?.ok ? (await trendResponse.json()).data : [];
    roleTrendData = roleTrendResponse?.ok ? (await roleTrendResponse.json()).data : [];
    roleTrendError = roleTrendResponse && !roleTrendResponse.ok
      ? 'Tren belum dapat dimuat. Coba lagi beberapa saat lagi.'
      : '';
    renderPersonalRecap();
    renderRecapViews();
    renderRoleDashboard();
  } catch (error) {
    if (requestId !== recapRequest || error.name === 'AbortError') return;
    recapData = null;
    renderRecapUnavailable(error.message || 'Data rekap belum dapat dimuat.');
    renderUnavailableDashboard(error.message || 'Data rekap belum dapat dimuat.');
  } finally {
    if (requestId === recapRequest && showProgress) hidePageProgress();
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
  setChecklistSaveError(activityId);
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
    await loadDashboard({ date: selectedChecklistDate, reloadRecap: false });
    setSaving(false, complete ? 'Dicatat barusan' : 'Perubahan tersimpan');
  } catch (error) {
    setSaving(false, 'Gagal disimpan', 'error');
    const message = error.message || 'Perubahan belum tersimpan. Periksa koneksi lalu coba lagi.';
    setChecklistSaveError(activityId, message);
    showToast(message, 'error');
  } finally {
    button.disabled = false;
    button.classList.remove('is-busy');
    button.removeAttribute('aria-busy');
  }
}

quickChecks.addEventListener('click', (event) => {
  const button = event.target.closest('.quick-check[data-activity-id]');
  if (button) persistChecklist(button);
});

fullChecklist.addEventListener('click', (event) => {
  const bulkChecklist = event.target.closest('[data-open-bulk-checklist]');
  if (bulkChecklist) {
    openBulkChecklist(bulkChecklist.dataset.openBulkChecklist);
    return;
  }
  const button = event.target.closest('.square-check[data-activity-id]');
  if (button) persistChecklist(button);
});

document.addEventListener('change', (event) => {
  if (event.target.matches('[data-bulk-checklist-date]')) {
    bulkChecklistState?.invalidDates.delete(event.target.value);
    updateBulkChecklistSelection(event.target.form);
    return;
  }
  if (event.target.matches('[data-bulk-checklist-month]')) showBulkChecklistMonth(event.target.form, event.target.value);
});

document.addEventListener('click', (event) => {
  const button = event.target.closest('[data-bulk-checklist-select], [data-bulk-checklist-clear]');
  if (!button) return;
  const form = button.closest('[data-admin-form="bulk-checklist"]');
  if (!form) return;
  bulkChecklistState?.invalidDates.clear();
  if (button.hasAttribute('data-bulk-checklist-select')) setAllBulkChecklistDates(form, true);
  else {
    bulkChecklistState?.selectedDates.clear();
    form.querySelectorAll('[data-bulk-checklist-date]').forEach((input) => { input.checked = false; });
    updateBulkChecklistSelection(form);
  }
});

document.addEventListener('click', (event) => {
  if (event.target.closest('[data-checklist-today]')) loadDashboard({ date: todayIso(), reloadRecap: false });
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
    recap: isAdmin || hasPersonalLks,
    department: isAdmin,
    history: hasPersonalLks,
    settings: isAdmin,
    organization: isAdmin,
    people: isAdmin,
    periods: isAdmin,
    'period-detail': isAdmin,
    activities: isAdmin,
    calendar: isAdmin,
    documents: isAdmin,
  }[view] ?? false;
}

function applyViewerIdentity(currentViewer) {
  const roles = currentViewer.roles ?? [];
  recapScope = roles.includes('leader') ? 'team' : 'personal';
  if (!hasConfiguredRole(roles)) {
    document.querySelector('#account-name').textContent = currentViewer.name;
    document.querySelector('#account-role').textContent = 'Akses belum dikonfigurasi';
    navButtons.forEach((button) => { button.hidden = !['dashboard', 'account'].includes(button.dataset.view); });
    document.querySelectorAll('.nav-admin, .admin-only, [data-role-panel]').forEach((item) => { item.hidden = true; });
    document.querySelector('[data-view-panel="dashboard"]').innerHTML = dashboardNotice(
      'Akses LKS belum siap',
      'Akun Anda belum memiliki peran LKS. Hubungi Admin LKS agar akses Santri Karya dapat diaktifkan, lalu muat ulang halaman ini.',
    );
    openView('dashboard', { persist: false, scroll: false, load: false });
    finishInitialBoot();
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
  document.querySelectorAll('[data-view="recap"] span').forEach((label) => {
    label.textContent = role === 'santri' && !label.closest('.mobile-nav') ? 'Rekap Saya' : 'Rekap';
  });
  document.querySelectorAll('.nav-admin, .admin-only').forEach((item) => { item.hidden = !roles.includes('admin'); });
  historyData = null;
  resetHistoryDetail();
  historyScope = roles.includes('leader') ? 'team' : 'personal';
  historyYear = '';
  renderHistory();

  const restoredView = savedView();
  if (restoredView === 'period-detail') {
    try { selectedPeriodId = sessionStorage.getItem(storedPeriodKey()); } catch { selectedPeriodId = null; }
  }
  if (restoredView && canOpenView(restoredView, roles)) {
    openView(restoredView, { persist: false, scroll: false, load: restoredView !== 'dashboard' });
    finishInitialBoot({ hideProgress: restoredView === 'dashboard' });
    return;
  }
  if (restoredView) saveView('dashboard');
  openView('dashboard', { persist: false, scroll: false, load: false });
  finishInitialBoot();
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
  const isAdmin = viewer?.roles?.includes('admin');
  const mustChangePassword = Boolean(data.must_change_password);
  const intro = mustChangePassword
    ? 'Password ini dibuat sementara oleh Admin. Ganti password Anda terlebih dahulu sebelum menggunakan LKS.'
    : isAdmin
    ? 'Kelola identitas akun dan keamanan password Anda. Pengaturan data serta akses akun lain tersedia di menu Pengaturan.'
    : 'Kelola identitas akun dan keamanan password Anda. Penempatan, level jabatan, dan status akun diatur oleh Admin LKS.';
  const requirement = mustChangePassword ? '<p class="account-required-notice" role="alert">Password sementara wajib diganti sebelum Anda dapat membuka atau mencatat LKS.</p>' : '';
  panel.innerHTML = `<div class="page-intro account-intro"><div><h2>Akun Saya</h2><p>${intro}</p>${requirement}</div></div><div class="account-layout">${mustChangePassword ? '' : `<section class="account-section" aria-labelledby="account-profile-title"><div class="account-section-intro"><h2 id="account-profile-title">Informasi akun</h2><p>Nama dan email dipakai untuk identitas serta proses masuk ke LKS.</p></div><form class="account-form" data-account-form="profile"><div class="account-fields"><label class="account-field">Nama<input name="name" maxlength="150" autocomplete="name" value="${escapeHtml(data.name)}" required></label><label class="account-field">Email<input name="email" type="email" maxlength="255" autocomplete="email" value="${escapeHtml(data.email)}" required></label></div><div class="account-actions"><button class="primary-button" type="submit">Simpan informasi</button></div></form></section>`}<section class="account-section" aria-labelledby="account-password-title"><div class="account-section-intro"><h2 id="account-password-title">Ganti password</h2><p>Masukkan password saat ini sebelum memilih password baru.</p></div><form class="account-form" data-account-form="password"><div class="account-fields">${accountPasswordField('account-current-password', 'current_password', 'Password saat ini', 'current-password')}${accountPasswordField('account-password', 'password', 'Password baru', 'new-password')}${accountPasswordField('account-password-confirmation', 'password_confirmation', 'Konfirmasi password baru', 'new-password')}</div><p class="account-help">Gunakan minimal 8 karakter dan simpan password baru Anda di tempat yang aman.</p><div class="account-actions"><button class="primary-button" type="submit">Perbarui password</button></div></form></section></div>`;
  if (mustChangePassword) requestAnimationFrame(() => panel.querySelector('#account-current-password')?.focus());
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
    if (!response.ok) throw await apiFormError(response);
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
    accountData = { ...accountData, must_change_password: false };
    renderAccount(accountData);
    showToast('Password berhasil diperbarui');
    void loadDashboard({ reloadRecap: false });
  } catch (error) {
    const message = error.message || 'Perubahan akun belum dapat disimpan. Coba lagi.';
    setFormError(form, message, error.fieldErrors);
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
  const department = document.querySelector('[data-recap-department-select]')?.value ?? '';
  const team = document.querySelector('[data-recap-team-select]')?.value ?? '';
  let visible = 0;
  document.querySelectorAll('#recap-rows .table-row').forEach((row) => {
    const statusMatches = status === 'all'
      || (status === 'perlu_perhatian' && row.dataset.status !== 'tuntas')
      || row.dataset.status === status;
    const matches = row.dataset.member.toLowerCase().includes(term)
      && statusMatches
      && (selectedGender.dataset.recapGender === 'all' || row.dataset.gender === selectedGender.dataset.recapGender)
      && (!department || row.dataset.department === department)
      && (!team || row.dataset.team === team)
      && (!recapMemberFocus || row.dataset.participantId === recapMemberFocus);
    row.hidden = !matches;
    if (matches) visible += 1;
  });
  const empty = document.querySelector('#empty-search');
  if (empty) empty.hidden = visible !== 0;
  const resultCount = document.querySelector('#recap-filter-results');
  if (resultCount) resultCount.textContent = `Menampilkan ${visible} dari ${document.querySelectorAll('#recap-rows .table-row').length} anggota.`;
}

document.addEventListener('click', (event) => {
  if (event.target.closest('[data-retry-recap]')) {
    loadRecap('', { showProgress: true });
    return;
  }
  const recapDetail = event.target.closest('[data-open-recap-detail]');
  if (recapDetail) {
    openRecapDetail(recapDetail.dataset.openRecapDetail);
    return;
  }
  if (event.target.closest('[data-retry-role-trend]')) {
    loadRecap('', { showProgress: false });
    return;
  }
  if (event.target.closest('[data-clear-recap-member]')) {
    recapMemberFocus = '';
    filterRecapRows();
    event.target.closest('.recap-focus-note')?.remove();
    return;
  }
  const exportLink = event.target.closest('[data-export-pdf]');
  if (exportLink) {
    event.preventDefault();
    void exportPdf(exportLink);
    return;
  }
  const reportTab = event.target.closest('[data-report-dimension]');
  if (reportTab) {
    selectedReportDimension = reportTab.dataset.reportDimension;
    selectedTrendGroupId = '';
    renderRecapViews();
    return;
  }
  const departmentAction = event.target.closest('[data-recap-department]');
  if (departmentAction) {
    recapDepartmentFilter = departmentAction.dataset.recapDepartment;
    openView('recap');
    return;
  }
  const pill = event.target.closest('[data-recap-filter]');
  if (pill) {
    document.querySelectorAll('[data-recap-filter]').forEach((item) => {
      const selected = item === pill;
      item.classList.toggle('is-on', selected);
      item.setAttribute('aria-pressed', String(selected));
    });
    filterRecapRows();
    return;
  }
  const gender = event.target.closest('[data-recap-gender]');
  if (gender) {
    document.querySelectorAll('[data-recap-gender]').forEach((item) => {
      const selected = item === gender;
      item.classList.toggle('is-on', selected);
      item.setAttribute('aria-pressed', String(selected));
    });
    filterRecapRows();
    return;
  }
  const recapScopeButton = event.target.closest('[data-recap-scope]');
  if (recapScopeButton) {
    recapScope = recapScopeButton.dataset.recapScope;
    recapMemberFocus = '';
    recapTeamFilter = '';
    return loadRecap('', { showProgress: true, scope: recapScope });
  }
});

document.addEventListener('change', (event) => {
  if (event.target.matches('[data-recap-period]')) loadRecap(event.target.value, { showProgress: false });
  if (event.target.matches('[data-recap-department-select]')) filterRecapRows();
  if (event.target.matches('[data-recap-team-select]')) filterRecapRows();
  if (event.target.matches('[data-history-year]')) {
    historyYear = event.target.value;
    resetHistoryDetail();
    renderHistory();
    const period = visibleHistoryPeriods()[0];
    if (period) loadHistoryRecap(period.id);
  }
  if (event.target.matches('[data-history-period-select]')) loadHistoryRecap(event.target.value);
  if (event.target.matches('[data-history-participant-select]')) {
    historyParticipantId = event.target.value;
    renderHistory();
  }
  if (event.target.matches('[data-trend-group]')) {
    selectedTrendGroupId = event.target.value;
    renderRecapViews();
  }
});

const settingRoutes = { 'Kelola periode': 'periods', 'Kelola aktivitas': 'activities', 'Kelola Santri': 'people' };
document.querySelectorAll('.settings-grid .text-action').forEach((button) => button.addEventListener('click', () => openView(settingRoutes[button.textContent.trim()])));
document.querySelectorAll('[data-demo]').forEach((button) => button.addEventListener('click', () => showToast(`${button.dataset.demo} tersedia setelah integrasi backend`)));
const adminListState = { organization: { page: 1, search: '' }, people: { page: 1, search: '' }, periods: { page: 1, search: '' }, activities: { page: 1, search: '' }, calendar: { year: new Date().getFullYear() }, participants: { page: 1, search: '' } };
let latestAdminRequest = 0;
let activePeriodParticipants = null;

function adminOptions(items, selected = '') {
  return `<option value="">Pilih</option>${items.map((item) => `<option value="${escapeHtml(item.id)}"${item.id === selected ? ' selected' : ''}>${escapeHtml(item.name)}</option>`).join('')}`;
}

function weekdayPicker(selected = [], fieldName = 'allowed_weekdays') {
  const activeDays = Array.isArray(selected) ? selected.map(Number) : String(selected ?? '').replace(/[{}]/g, '').split(',').filter(Boolean).map(Number);
  const days = [[1, 'Sen'], [2, 'Sel'], [3, 'Rab'], [4, 'Kam'], [5, 'Jum'], [6, 'Sab'], [7, 'Min']];
  return `<fieldset class="weekday-picker"><legend>Jadwal pencatatan</legend><small>Kosongkan semua bila aktivitas dapat dicatat setiap hari.</small><span>${days.map(([value, label]) => `<label><input type="checkbox" name="${fieldName}" value="${value}"${activeDays.includes(value) ? ' checked' : ''}> ${label}</label>`).join('')}</span></fieldset>`;
}

function ruleList(value, fallback) {
  if (Array.isArray(value)) return value.map(String);
  if (typeof value !== 'string' || value === '') return fallback;
  return value.replace(/[{}]/g, '').split(',').filter(Boolean);
}

function activityRuleLabel(activity) {
  const minimum = activity.minimum_target_count ?? activity.target_count;
  const weekly = activity.max_per_week ? ` · maksimal ${activity.max_per_week} kali/pekan` : '';
  return `Target ${minimum}–${activity.target_count} kali${weekly} · Jadwal ${activityScheduleLabel(activity)}`;
}

function activityScheduleLabel(activity) {
  const days = [...new Set((activity.allowed_weekdays ?? []).map(Number))].sort((left, right) => left - right);
  if (!days.length) return 'setiap hari';
  if (days.join(',') === '1,2,3,4,5') return 'Senin–Jumat';
  const labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
  return days.map((day) => labels[day - 1]).filter(Boolean).join(', ');
}

function activityAudience(activity) {
  const genders = ruleList(activity.applicable_genders_list ?? activity.applicable_genders, ['ikhwan', 'akhwat']).map((value) => value === 'ikhwan' ? 'Ikhwan' : 'Akhwat').join(', ');
  const levels = ruleList(activity.applicable_levels_list ?? activity.applicable_levels, ['leader', 'staff']).map((value) => value === 'leader' ? 'Leader' : 'Staff').join(', ');
  return `${genders} · ${levels}`;
}

function audiencePicker(genders = ['ikhwan', 'akhwat'], levels = ['leader', 'staff'], genderField = 'applicable_genders', levelField = 'applicable_levels') {
  const selectedGenders = ruleList(genders, ['ikhwan', 'akhwat']);
  const selectedLevels = ruleList(levels, ['leader', 'staff']);
  return `<fieldset class="activity-audience-picker"><legend>Berlaku untuk</legend><span><label><input type="checkbox" name="${genderField}" value="ikhwan"${selectedGenders.includes('ikhwan') ? ' checked' : ''}> Ikhwan</label><label><input type="checkbox" name="${genderField}" value="akhwat"${selectedGenders.includes('akhwat') ? ' checked' : ''}> Akhwat</label></span><span><label><input type="checkbox" name="${levelField}" value="leader"${selectedLevels.includes('leader') ? ' checked' : ''}> Leader</label><label><input type="checkbox" name="${levelField}" value="staff"${selectedLevels.includes('staff') ? ' checked' : ''}> Staff</label></span></fieldset>`;
}

function parseHolidayImportEntries(value) {
  const holidays = [];
  const errors = [];
  const dates = new Set();
  String(value ?? '').split(/\r?\n/).map((line) => line.trim()).filter(Boolean).forEach((line, index) => {
    const rowNumber = index + 1;
    const values = line.split(',').map((item) => item.trim());
    while (values.at(-1) === '') values.pop();
    const [holiday_date, name, type, ...extra] = values;
    const normalizedType = String(type ?? '').toLowerCase().replace(/[\s-]+/g, '_');
    const parsedDate = new Date(`${holiday_date}T00:00:00Z`);
    const mappedType = ['tanggal_merah', 'public_holiday', 'libur_nasional'].includes(normalizedType)
      ? 'public_holiday'
      : ['cuti_bersama', 'collective_leave'].includes(normalizedType)
        ? 'collective_leave'
        : null;
    const isDate = /^\d{4}-\d{2}-\d{2}$/.test(holiday_date ?? '') && !Number.isNaN(parsedDate.valueOf()) && parsedDate.toISOString().slice(0, 10) === holiday_date;
    if (values.length !== 3 || extra.length || !isDate || !name || !mappedType || dates.has(holiday_date)) {
      errors.push(rowNumber);
      return;
    }
    dates.add(holiday_date);
    holidays.push({ holiday_date, name, type: mappedType });
  });
  return { holidays, errors };
}

function periodActivityConfigurationFields(period) {
  const activities = period.period_activities ?? [];
  if (!activities.length) return '<p class="admin-form-note">Belum ada aktivitas pada periode ini. Tambahkan aktivitas terlebih dahulu.</p>';

  const activityButtons = activities.map((activity, index) => `<button class="period-config-nav-item${index === 0 ? ' is-selected' : ''}" type="button" data-period-config-select="${escapeHtml(activity.id)}" aria-pressed="${index === 0 ? 'true' : 'false'}"><strong>${escapeHtml(activity.activity_name_snapshot)}</strong><small>${activity.is_active ? 'Aktif' : 'Nonaktif'} · target ${escapeHtml(activity.target_count)} kali</small></button>`).join('');
  const activityForms = activities.map((activity, index) => `<fieldset class="period-config-row" data-period-activity-id="${escapeHtml(activity.id)}"${index === 0 ? '' : ' disabled hidden'}><legend class="sr-only">Atur ${escapeHtml(activity.activity_name_snapshot)}</legend><div class="period-config-row-title"><strong>${escapeHtml(activity.activity_name_snapshot)}</strong><small>Nilai berdasarkan checklist ÷ target maksimum, dibatasi 100%. Tuntas saat target minimal tercapai.</small></div><div class="admin-modal-inline-fields"><label>Target maksimum<input type="number" min="1" value="${activity.target_count}" data-target-count required></label><label>Target minimal<input type="number" min="1" max="${activity.target_count}" value="${activity.minimum_target_count ?? activity.target_count}" data-minimum-target-count required></label></div><label>Maksimal per pekan<input type="number" min="1" value="${activity.max_per_week ?? ''}" data-max-per-week><small>Kosongkan bila tidak dibatasi.</small></label><label class="admin-modal-checkbox"><input type="checkbox" data-is-active${activity.is_active ? ' checked' : ''}> Aktivitas aktif</label><div class="period-weekday-control">${weekdayPicker(activity.allowed_weekdays)}</div>${audiencePicker(activity.applicable_genders_list ?? activity.applicable_genders, activity.applicable_levels_list ?? activity.applicable_levels)}</fieldset>`).join('');

  return `<div class="period-config-manager"><aside class="period-config-navigation" aria-label="Daftar aktivitas periode"><p>${activities.length} aktivitas pada periode ini</p><div>${activityButtons}</div></aside><section class="period-config-editor" aria-label="Pengaturan aktivitas"><button class="text-button period-config-back" type="button" data-period-config-back>Kembali ke daftar aktivitas</button><p class="admin-form-note">Perubahan hanya berlaku untuk periode ini dan akan terkunci setelah periode diaktifkan.</p><div class="period-config-list">${activityForms}</div></section></div>`;
}

function masterActivityRuleFields(activity = {}) {
  const target = activity.default_target_count ?? '';
  const minimum = activity.default_minimum_target_count ?? '';
  const weekly = activity.default_max_per_week ?? '';
  return `<div class="admin-modal-inline-fields"><label>Target maksimum<input type="number" name="default_target_count" min="1" value="${escapeHtml(target)}" required></label><label>Target minimal tuntas<input type="number" name="default_minimum_target_count" min="1" value="${escapeHtml(minimum)}" required></label></div><label>Maksimal per pekan<input type="number" name="default_max_per_week" min="1" value="${escapeHtml(weekly)}"><small>Kosongkan bila tidak ada batas mingguan.</small></label>${weekdayPicker(activity.default_allowed_weekdays, 'default_allowed_weekdays')}${audiencePicker(activity.default_applicable_genders, activity.default_applicable_levels, 'default_applicable_genders', 'default_applicable_levels')}<p class="admin-form-note">Nilai dihitung dari jumlah checklist dibanding target maksimum dan dibatasi 100%. Aktivitas berstatus tuntas saat mencapai target minimal.</p>`;
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
  const needsConfiguration = ['settings', 'periods', 'period-detail', 'activities', 'calendar'].includes(name);
  const needsSignatories = ['settings', 'periods', 'period-detail', 'documents'].includes(name);
  if ((needsOrganization && organizationData === null) || (needsConfiguration && configurationData === null) || (needsSignatories && documentSignatoriesData === null)) {
    panel.innerHTML = adminPanel('Memuat konfigurasi', 'Data administrasi sedang disiapkan.', '<p class="admin-empty" role="status">Memuat data…</p>');
    return;
  }

  const organization = organizationData ?? { departments: [], department_options: [], santri: [], santri_options: [], team_leader_options: [] };
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
    const calendarHolidayCount = configuration.calendar_holidays?.length ?? 0;
    panel.querySelector('.admin-hub-list').insertAdjacentHTML('beforeend', `<button class="admin-hub-item" type="button" data-go="calendar"><span><strong>Kalender Kerja</strong><small>${calendarHolidayCount} hari libur pada ${escapeHtml(configuration.calendar_year)}</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button>`);
    const configuredSignatories = documentSignatoriesData.filter((signatory) => signatory.signature_configured && signatory.name).length;
    panel.querySelector('.admin-hub-list').insertAdjacentHTML('beforeend', `<button class="admin-hub-item" type="button" data-go="documents"><span><strong>Dokumen &amp; Tanda Tangan</strong><small>${configuredSignatories} dari 2 TTD digital siap digunakan</small></span><span class="admin-hub-action">Kelola <svg aria-hidden="true"><use href="#icon-chevron"/></svg></span></button>`);
    return;
  }
  if (name === 'documents') {
    const signatoryRows = documentSignatoriesData.map((signatory) => `<article class="signature-settings-row"><div class="signature-settings-preview">${signatory.signature_configured ? `<img src="${escapeHtml(signatory.signature_url)}" alt="TTD digital ${escapeHtml(signatory.title)}">` : '<span>TTD belum diatur</span>'}</div><div><strong>${escapeHtml(signatory.title)}</strong><small>${escapeHtml(signatory.name || 'Nama pejabat belum diatur')}</small><small>${signatory.signature_configured ? 'TTD digital tersedia' : 'Unggah PNG tanda tangan digital'}</small></div><button class="text-button organization-row-action" type="button" data-open-admin-modal="signatory-edit" data-signatory-id="${escapeHtml(signatory.id)}">Kelola</button></article>`).join('');
    panel.innerHTML = adminPanel('Dokumen & Tanda Tangan', 'TTD digital dipakai oleh General Manager dan Kabid. Pengembangan Spiritual. Saat periode diaktifkan, konfigurasi aktif disimpan sebagai snapshot periode.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav><section class="signature-settings-list" aria-label="Tanda tangan digital">${signatoryRows}</section><p class="admin-context-note">Gunakan PNG transparan. TTD disimpan privat dan hanya dapat dipakai untuk membuat PDF oleh sistem.</p>`);
    return;
  }
  if (name === 'organization') {
    const departments = organization.departments;
    const hasOrganizationSearch = Boolean(adminListState.organization.search);
    const showOrganizationEmpty = organizationMeta.total === 0 && !hasOrganizationSearch;
    const hierarchyRows = departments.map((department) => {
      const departmentActions = `<button class="text-button organization-row-action" type="button" data-open-admin-modal="department-edit" data-organization-id="${escapeHtml(department.id)}">Edit</button><button class="text-button organization-row-action" type="button" data-open-admin-modal="team" data-department-id="${escapeHtml(department.id)}"${department.is_active ? '' : ' disabled'}>Tambah tim</button>${department.is_active ? `<button class="text-button organization-row-action organization-archive-action" type="button" data-archive-organization="department" data-organization-id="${escapeHtml(department.id)}" data-organization-name="${escapeHtml(department.name)}">Arsipkan</button>` : ''}${department.teams.length === 0 && department.santri_profiles_count === 0 ? `<button class="text-button organization-row-action" type="button" data-delete-resource="department" data-resource-id="${escapeHtml(department.id)}" data-resource-name="${escapeHtml(department.name)}">Hapus</button>` : ''}`;
      const departmentRow = `<div class="organization-hierarchy-row organization-department-row" role="row"><span role="cell"><strong>${escapeHtml(department.name)}</strong><small>${department.teams.length} tim</small></span><span class="organization-code" role="cell">${escapeHtml(department.code)}</span><span role="cell">${department.santri_profiles_count} Santri Karya</span><span role="cell">—</span><span role="cell">${department.is_active ? 'Aktif' : 'Diarsipkan'}</span><span class="organization-row-actions" role="cell">${departmentActions}</span></div>`;
      const teamRows = department.teams.map((team) => `<div class="organization-hierarchy-row organization-team-child-row" role="row"><span role="cell"><strong>${escapeHtml(team.name)}</strong><small>Tim · ${escapeHtml(department.name)}</small></span><span class="organization-code" role="cell">${escapeHtml(team.code)}</span><span role="cell">${team.santri_profiles_count} Santri Karya</span><span role="cell">${escapeHtml(team.leader?.name ?? 'Belum ditetapkan')}</span><span role="cell">${team.is_active ? 'Aktif' : 'Diarsipkan'}</span><span class="organization-row-actions" role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="team-edit" data-organization-id="${escapeHtml(team.id)}">Edit</button>${team.is_active ? `<button class="text-button organization-row-action organization-archive-action" type="button" data-archive-organization="team" data-organization-id="${escapeHtml(team.id)}" data-organization-name="${escapeHtml(team.name)}">Arsipkan</button>` : ''}${team.santri_profiles_count === 0 ? `<button class="text-button organization-row-action" type="button" data-delete-resource="team" data-resource-id="${escapeHtml(team.id)}" data-resource-name="${escapeHtml(team.name)}">Hapus</button>` : ''}</span></div>`).join('');
      return `${departmentRow}${teamRows || '<div class="organization-no-team">Belum ada tim. Tambahkan tim dari baris departemen di atas.</div>'}`;
    }).join('');
    const hierarchyContent = showOrganizationEmpty
      ? `<section class="organization-empty-state" aria-labelledby="department-empty-title"><h3 id="department-empty-title">Belum ada departemen</h3><p>Mulai dengan membuat departemen sebagai dasar struktur organisasi. Setelah itu, Anda dapat menambahkan tim di dalamnya.</p><button class="primary-button" type="button" data-open-admin-modal="department">Tambah departemen</button></section>`
      : `<section class="admin-record-section organization-hierarchy-section" aria-labelledby="organization-list-title"><div class="organization-section-head"><div><h3 id="organization-list-title">Struktur Departemen dan Tim</h3><p>${organizationMeta.total} departemen · ${teams.length} tim.</p></div></div><form class="admin-list-search" data-admin-list-search="organization"><label class="sr-only" for="organization-search">Cari departemen atau tim</label><input id="organization-search" name="search" value="${escapeHtml(adminListState.organization.search)}" placeholder="Cari nama atau kode departemen/tim" autocomplete="off"></form>${departments.length ? `<div class="organization-hierarchy-table" role="table" aria-label="Struktur organisasi"><div class="organization-hierarchy-head" role="row"><span role="columnheader">Unit</span><span role="columnheader">Kode</span><span role="columnheader">Santri Karya</span><span role="columnheader">Leader Tim</span><span role="columnheader">Status</span><span role="columnheader">Aksi</span></div>${hierarchyRows}</div>${paginationControls(organizationMeta, 'organization')}` : '<p class="admin-empty">Tidak ada unit yang sesuai dengan pencarian.</p>'}</section>`;
    const organizationActions = !showOrganizationEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="department">Tambah departemen</button>' : '';
    panel.innerHTML = adminPanel('Struktur Organisasi', 'Atur departemen dan tim sebagai dasar penempatan Santri Karya.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${hierarchyContent}`, organizationActions, !showOrganizationEmpty);
    return;
  }
  if (name === 'people') {
    const hasTeams = teams.length > 0;
    const people = organization.santri;
    const hasPeopleSearch = Boolean(adminListState.people.search);
    const showPeopleEmpty = hasTeams && peopleMeta.total === 0 && !hasPeopleSearch;
    const peopleRows = people.map((profile) => `<div class="admin-record-row people-record-row" role="row"><span role="cell"><strong>${escapeHtml(profile.user.name)}</strong><small>${escapeHtml(profile.user.email)}</small></span><span role="cell">${escapeHtml(profile.team?.name ?? 'Tanpa tim')} · ${escapeHtml(profile.department?.name ?? 'Tanpa departemen')}</span><span role="cell">${profile.status === 'active' ? 'Aktif' : 'Nonaktif'} · ${profile.level === 'leader' ? 'Leader' : 'Staff'}</span><span role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="santri-edit" data-santri-id="${escapeHtml(profile.user_id)}">Edit</button><button class="text-button organization-row-action" type="button" data-delete-resource="santri" data-resource-id="${escapeHtml(profile.user_id)}" data-resource-name="${escapeHtml(profile.user.name)}">Hapus</button></span></div>`).join('');
    const peopleContent = !hasTeams
      ? `<section class="organization-empty-state" aria-labelledby="people-prerequisite-title"><h3 id="people-prerequisite-title">Siapkan tim terlebih dahulu</h3><p>Santri Karya perlu ditempatkan di dalam tim. Buat departemen dan tim sebelum membuat akun Santri Karya.</p><button class="primary-button" type="button" data-go="organization">Kelola struktur organisasi</button></section>`
      : !showPeopleEmpty
        ? `<section class="admin-record-section" aria-labelledby="people-list-title"><div class="organization-section-head"><div><h3 id="people-list-title">Daftar Santri Karya</h3><p>${peopleMeta.total} Santri Karya terdaftar.</p></div></div><form class="admin-list-search" data-admin-list-search="people"><label class="sr-only" for="people-search">Cari Santri Karya</label><input id="people-search" name="search" value="${escapeHtml(adminListState.people.search)}" placeholder="Cari nama atau email" autocomplete="off"></form>${people.length ? `<div class="admin-record-table people-record-table" role="table" aria-label="Daftar Santri Karya"><div class="admin-record-head" role="row"><span role="columnheader">Santri Karya</span><span role="columnheader">Penempatan</span><span role="columnheader">Status akses</span><span role="columnheader">Aksi</span></div>${peopleRows}</div>${paginationControls(peopleMeta, 'people')}` : '<p class="admin-empty">Tidak ada Santri Karya yang sesuai dengan pencarian.</p>'}</section>`
        : `<section class="organization-empty-state" aria-labelledby="people-empty-title"><h3 id="people-empty-title">Belum ada Santri Karya</h3><p>Buat akun Santri Karya dan tetapkan timnya. Peserta periode dapat dikelola setelah akun tersedia.</p><button class="primary-button" type="button" data-open-admin-modal="santri">Tambah Santri Karya</button></section>`;
    const peopleActions = hasTeams && !showPeopleEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="santri">Tambah Santri Karya</button>' : '';
    panel.innerHTML = adminPanel('Data Santri Karya', 'Buat akun dan tetapkan penempatan organisasinya. Peserta periode dikelola dari Periode LKS.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${peopleContent}`, peopleActions, !showPeopleEmpty && hasTeams);
    return;
  }
  if (name === 'period-detail') {
    const period = periodOptions.find((item) => item.id === selectedPeriodId);
    if (!period) {
      panel.innerHTML = adminPanel('Periode tidak ditemukan', 'Pilih periode dari daftar untuk melanjutkan.', '<button class="primary-button" type="button" data-go="periods">Kembali ke daftar periode</button>', '', false);
      return;
    }
    const participantData = activePeriodParticipants ?? { participants: [], pagination: { total: 0, current_page: 1, last_page: 1, per_page: 25 } };
    const participantMeta = participantData.pagination;
    const activities = period.period_activities ?? [];
    const missingMasterActivities = activityOptions.filter((activity) => activity.is_active && !activities.some((periodActivity) => periodActivity.activity_code_snapshot === activity.code));
    const availableActivityCount = missingMasterActivities.length;
    const holidays = period.holiday_snapshots ?? [];
    const missingSignatories = ['general_manager', 'kabid_pengembangan_spiritual'].filter((role) => !(documentSignatoriesData ?? []).some((signatory) => signatory.role === role && signatory.name && signatory.signature_configured));
    const leaderlessTeams = teams.filter((team) => team.is_active && Number(team.active_santri_profiles_count) > 0 && !team.leader_user_id);
    const participantRows = participantData.participants.map((participant) => `<div class="admin-record-row participant-record-row" role="row"><span role="cell"><strong>${escapeHtml(participant.name)}</strong><small>Mulai ${escapeHtml(formatDate(participant.participation_start_date, true))}</small></span><span role="cell">${escapeHtml(participant.team ?? 'Tanpa tim')}<small>${escapeHtml(participant.department ?? 'Tanpa departemen')}</small></span><span role="cell">${participant.checklists_count ? `${participant.checklists_count} checklist tercatat` : 'Belum ada checklist'}</span><span role="cell">${participant.checklists_count ? '<small>Riwayat tercatat</small>' : `<button class="text-button organization-row-action organization-archive-action" type="button" data-remove-period-participant="${escapeHtml(participant.id)}" data-period-id="${escapeHtml(period.id)}" data-participant-name="${escapeHtml(participant.name)}">Keluarkan</button>`}</span></div>`).join('');
    const activityRows = activities.map((activity) => `<li><strong>${escapeHtml(activity.activity_name_snapshot)}</strong><span>Target ${escapeHtml(activity.target_count)} kali · ${activity.is_active ? 'Aktif' : 'Nonaktif'}</span></li>`).join('');
    const holidayRows = holidays.map((holiday) => `<div class="admin-record-row period-holiday-record-row" role="row"><span role="cell"><strong>${escapeHtml(formatDate(holiday.holiday_date, true))}</strong></span><span role="cell">${escapeHtml(holiday.name)}</span><span role="cell">${holiday.type === 'collective_leave' ? 'Cuti bersama' : 'Tanggal merah'}<small>Checklist opsional · tidak dinilai</small></span><span role="cell">${period.status === 'draft' ? `<button class="text-button organization-row-action" type="button" data-delete-period-holiday="${escapeHtml(holiday.id)}" data-period-id="${escapeHtml(period.id)}" data-holiday-name="${escapeHtml(holiday.name)}">Hapus</button>` : '<small>Terkunci</small>'}</span></div>`).join('');
    const lifecycleAction = period.status === 'active'
      ? `<button class="text-button organization-archive-action" type="button" data-close-period="${escapeHtml(period.id)}" data-period-name="${escapeHtml(period.name)}">Tutup periode</button>`
      : period.status === 'draft'
        ? `<div class="organization-row-actions"><button class="text-button organization-row-action" type="button" data-open-admin-modal="period-edit" data-period-id="${escapeHtml(period.id)}">Edit periode</button><button class="text-button organization-row-action" type="button" data-open-admin-modal="period-config" data-period-id="${escapeHtml(period.id)}">Kelola aktivitas (${activities.length})</button>${availableActivityCount ? `<button class="text-button organization-row-action" type="button" data-open-admin-modal="period-activity" data-period-id="${escapeHtml(period.id)}">Tambah aktivitas</button>` : ''}<button class="text-button organization-row-action" type="button" data-activate-period="${escapeHtml(period.id)}">Aktifkan</button></div>`
        : '<span class="status status-closed">Ditutup</span>';
    const missingActivityNotice = period.status === 'draft' && missingMasterActivities.length
      ? `<p class="admin-context-note"><strong>${missingMasterActivities.length} aktivitas master aktif belum dimasukkan:</strong> ${missingMasterActivities.map((activity) => escapeHtml(activity.name)).join(', ')}. <button class="text-button" type="button" data-open-admin-modal="period-activity" data-period-id="${escapeHtml(period.id)}">Tambahkan aktivitas</button></p>`
      : '';
    const participantSection = period.status === 'active'
      ? `<section class="admin-record-section period-participant-section" aria-labelledby="participant-list-title"><div class="organization-section-head"><div><h3 id="participant-list-title">Peserta periode</h3><p>${participantMeta.total} peserta terdaftar pada ${escapeHtml(period.name)}.</p></div><button class="primary-button" type="button" data-open-admin-modal="participant" data-period-id="${escapeHtml(period.id)}">Tambah peserta</button></div><p class="admin-context-note">Peserta baru dapat mencatat sejak dimasukkan. Peserta tanpa checklist masih dapat dikeluarkan.</p><form class="admin-list-search" data-admin-list-search="participants"><label class="sr-only" for="participant-search">Cari peserta periode</label><input id="participant-search" name="search" value="${escapeHtml(adminListState.participants.search)}" placeholder="Cari nama, tim, atau departemen" autocomplete="off"></form>${participantData.participants.length ? `<div class="admin-record-table participant-record-table" role="table" aria-label="Peserta ${escapeHtml(period.name)}"><div class="admin-record-head" role="row"><span role="columnheader">Santri Karya</span><span role="columnheader">Penempatan</span><span role="columnheader">Checklist</span><span role="columnheader">Aksi</span></div>${participantRows}</div>${paginationControls(participantMeta, 'participants')}` : `<p class="admin-empty">${adminListState.participants.search ? 'Tidak ada peserta yang sesuai dengan pencarian.' : 'Belum ada peserta pada periode ini.'}</p>`}</section>`
      : '';
    const holidayActions = period.status === 'draft' ? `<button class="primary-button" type="button" data-open-admin-modal="period-holiday" data-period-id="${escapeHtml(period.id)}">Tambah hari libur</button><button class="text-button admin-add-team" type="button" data-open-admin-modal="period-holiday-import" data-period-id="${escapeHtml(period.id)}">Impor daftar</button><button class="text-button admin-add-team" type="button" data-import-period-calendar="${escapeHtml(period.id)}">Salin ulang kalender kerja</button>` : '';
    const holidaySection = `<section class="admin-record-section period-holiday-section" aria-labelledby="period-holiday-title"><div class="organization-section-head"><div><h3 id="period-holiday-title">Hari efektif dan libur</h3><p>${period.status === 'draft' ? 'Hari libur Kalender Kerja dalam rentang periode disalin otomatis saat draft dibuat. Gunakan Salin ulang kalender kerja bila kalender diperbarui.' : 'Snapshot hari libur periode ini sudah terkunci untuk menjaga perhitungan.'}</p></div>${holidayActions}</div>${holidays.length ? `<div class="admin-record-table period-holiday-record-table" role="table" aria-label="Hari efektif dan libur ${escapeHtml(period.name)}"><div class="admin-record-head" role="row"><span role="columnheader">Tanggal</span><span role="columnheader">Keterangan</span><span role="columnheader">Dampak</span><span role="columnheader">Aksi</span></div>${holidayRows}</div>` : `<p class="admin-empty">${period.status === 'draft' ? 'Belum ada hari libur dalam rentang periode ini. Tambahkan secara manual atau salin ulang Kalender Kerja setelah tanggal referensinya tersedia.' : 'Tidak ada hari libur yang dicatat pada periode ini.'}</p>`}</section>`;
    panel.innerHTML = adminPanel(`Kelola periode · ${period.name}`, 'Tinjau konfigurasi dan kelola peserta pada periode ini.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="periods">Kembali ke daftar periode</button></nav><section class="period-overview"><div><span class="status status-${escapeHtml(period.status === 'active' ? 'active' : period.status === 'closed' ? 'closed' : 'waiting')}">${escapeHtml(period.status === 'active' ? 'Aktif' : period.status === 'closed' ? 'Ditutup' : 'Draft')}</span><h3>${escapeHtml(period.name)}</h3><p>${escapeHtml(formatDate(period.start_date, true))} — ${escapeHtml(formatDate(period.end_date, true))}</p></div><dl><div><dt>Ambang Leader</dt><dd>${escapeHtml(period.final_passing_threshold)}%</dd></div><div><dt>Ambang Staff</dt><dd>${escapeHtml(period.staff_passing_threshold ?? 85)}%</dd></div><div><dt>Aktivitas aktif</dt><dd>${activities.filter((activity) => activity.is_active).length}</dd></div><div><dt>Hari libur</dt><dd>${holidays.length}</dd></div></dl><div class="period-overview-action">${lifecycleAction}</div></section><section class="period-activity-summary" aria-labelledby="period-activity-title"><div class="organization-section-head"><div><h3 id="period-activity-title">Aktivitas periode</h3><p>Konfigurasi aktivitas terkunci setelah periode diaktifkan.</p></div></div>${missingActivityNotice}${activityRows ? `<ul>${activityRows}</ul>` : '<p class="admin-empty">Belum ada aktivitas pada periode ini.</p>'}</section>${holidaySection}${participantSection}`, '', true);
    if (period.status === 'draft') {
      panel.querySelector('.period-overview')?.insertAdjacentHTML('afterend', `<section class="period-readiness" aria-labelledby="period-readiness-title"><div><h3 id="period-readiness-title">Kesiapan aktivasi</h3><p>Selesaikan yang wajib sebelum periode dikunci aktif.</p></div><ul><li class="${missingSignatories.length ? 'is-blocked' : 'is-ready'}"><strong>TTD digital</strong><span>${missingSignatories.length ? `${missingSignatories.length} pejabat belum lengkap` : 'General Manager dan Kabid siap'}</span>${missingSignatories.length ? '<button class="text-button" type="button" data-go="documents">Kelola TTD</button>' : ''}</li><li class="${activities.filter((activity) => activity.is_active).length ? 'is-ready' : 'is-blocked'}"><strong>Aktivitas periode</strong><span>${activities.filter((activity) => activity.is_active).length ? `${activities.filter((activity) => activity.is_active).length} aktivitas aktif` : 'Belum ada aktivitas aktif'}</span></li><li class="${leaderlessTeams.length ? 'is-warning' : 'is-ready'}"><strong>Leader tim</strong><span>${leaderlessTeams.length ? `${leaderlessTeams.length} tim aktif belum memiliki Leader` : 'Seluruh tim aktif siap'}</span>${leaderlessTeams.length ? '<button class="text-button" type="button" data-go="organization">Kelola tim</button>' : ''}</li></ul></section>`);
    }
    document.querySelectorAll('.period-activity-summary li span').forEach((summary, index) => {
      const activity = activities[index];
      summary.textContent = `${activityRuleLabel(activity)} · ${activityAudience(activity)} · ${activity.is_active ? 'Aktif' : 'Nonaktif'}`;
    });
    return;
  }
  if (name === 'periods') {
    const hasPeriodSearch = Boolean(adminListState.periods.search);
    const showPeriodEmpty = periodMeta.total === 0 && !hasPeriodSearch;
    const periodRows = configuration.periods.map((period) => {
      const activeActivityCount = period.period_activities.filter((activity) => activity.is_active).length;
      const periodAction = period.status === 'draft'
        ? `<button class="text-button organization-row-action" type="button" data-open-period-detail="${escapeHtml(period.id)}">Tinjau kesiapan</button><button class="text-button organization-row-action" type="button" data-delete-resource="period" data-resource-id="${escapeHtml(period.id)}" data-resource-name="${escapeHtml(period.name)}">Hapus draft</button>`
        : `<button class="text-button organization-row-action" type="button" data-open-period-detail="${escapeHtml(period.id)}">${period.status === 'active' ? 'Kelola periode' : 'Lihat periode'}</button>`;
      const periodStatus = period.status === 'active' ? '<span class="status status-active">Aktif</span>' : period.status === 'closed' ? '<span class="status status-closed">Ditutup</span>' : '<span class="status status-waiting">Draft</span>';
      return `<div class="admin-record-row period-record-row" role="row"><span role="cell"><strong>${escapeHtml(period.name)}</strong><small>Leader ${period.final_passing_threshold}% · Staff ${period.staff_passing_threshold ?? 85}%</small></span><span role="cell">${escapeHtml(formatDate(period.start_date, true))} — ${escapeHtml(formatDate(period.end_date, true))}<small>${period.period_activities.length} aktivitas · ${activeActivityCount} aktif</small></span><span role="cell">${periodStatus}</span><span role="cell">${periodAction}</span></div>`;
    }).join('');
    const periodContent = !showPeriodEmpty
      ? `<section class="admin-record-section" aria-labelledby="period-list-title"><div class="organization-section-head"><div><h3 id="period-list-title">Daftar Periode</h3><p>${hasPeriodSearch ? `${periodMeta.total} periode ditemukan.` : `${periodMeta.total} periode tersimpan.`}</p></div></div><form class="admin-list-search" data-admin-list-search="periods"><label class="sr-only" for="period-search">Cari periode LKS</label><input id="period-search" name="search" value="${escapeHtml(adminListState.periods.search)}" placeholder="Cari nama atau status periode" autocomplete="off"></form>${configurationData.periods.length ? `<div class="admin-record-table period-record-table" role="table" aria-label="Daftar periode LKS"><div class="admin-record-head" role="row"><span role="columnheader">Periode</span><span role="columnheader">Rentang dan aktivitas</span><span role="columnheader">Status</span><span role="columnheader">Aksi</span></div>${periodRows}</div>${paginationControls(periodMeta, 'periods')}` : '<p class="admin-empty">Tidak ada periode yang sesuai dengan pencarian.</p>'}</section>`
      : `<section class="organization-empty-state" aria-labelledby="period-empty-title"><h3 id="period-empty-title">Belum ada periode LKS</h3><p>Buat periode draft terlebih dahulu, lalu tambahkan aktivitas dan aktifkan ketika pengaturan sudah siap.</p><button class="primary-button" type="button" data-open-admin-modal="period">Tambah periode</button></section>`;
    const periodActions = !showPeriodEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="period">Tambah periode</button>' : '';
    panel.innerHTML = adminPanel('Periode LKS', 'Buat, siapkan, dan pantau setiap periode LKS dari satu daftar.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${periodContent}`, periodActions, !showPeriodEmpty);
    return;
  }
  if (name === 'calendar') {
    const holidays = configuration.calendar_holidays ?? [];
    const calendarYear = configuration.calendar_year ?? adminListState.calendar.year;
    const rows = holidays.map((holiday) => `<div class="admin-record-row calendar-record-row" role="row"><span role="cell"><strong>${escapeHtml(formatDate(holiday.holiday_date, true))}</strong></span><span role="cell">${escapeHtml(holiday.name)}</span><span role="cell">${holiday.type === 'collective_leave' ? 'Cuti bersama' : 'Tanggal merah'}</span><span role="cell"><button class="text-button organization-row-action" type="button" data-delete-holiday="${escapeHtml(holiday.id)}" data-holiday-name="${escapeHtml(holiday.name)}">Hapus</button></span></div>`).join('');
    const content = `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav><section class="admin-record-section" aria-labelledby="calendar-list-title"><div class="organization-section-head"><div><h3 id="calendar-list-title">Hari libur ${escapeHtml(calendarYear)}</h3><p>Tanggal merah dan cuti bersama membuat checklist menjadi opsional dan tidak memengaruhi nilai.</p></div><label class="calendar-year-control">Tahun<input type="number" min="2000" max="2100" value="${escapeHtml(calendarYear)}" data-calendar-year></label></div>${holidays.length ? `<div class="admin-record-table calendar-record-table" role="table" aria-label="Kalender kerja ${escapeHtml(calendarYear)}"><div class="admin-record-head" role="row"><span role="columnheader">Tanggal</span><span role="columnheader">Keterangan</span><span role="columnheader">Jenis</span><span role="columnheader">Aksi</span></div>${rows}</div>` : '<p class="admin-empty">Belum ada hari libur untuk tahun ini. Tambahkan atau impor kalender resmi perusahaan.</p>'}</section>`;
    panel.innerHTML = adminPanel('Kalender Kerja', 'Kelola kalender tahunan sebelum periode diaktifkan. Periode aktif menyimpan snapshot kalendernya sendiri.', content, '<button class="primary-button" type="button" data-open-admin-modal="holiday">Tambah hari libur</button><button class="text-button admin-add-team" type="button" data-open-admin-modal="holiday-import">Impor daftar</button>', true);
    return;
  }
  const drafts = periodOptions.filter((period) => period.status === 'draft');
  const hasActivitySearch = Boolean(adminListState.activities.search);
  const showActivityEmpty = activityMeta.total === 0 && !hasActivitySearch;
  const activityRows = configuration.activities.map((activity) => {
    const assignments = activity.period_activities ?? [];
    const defaultRule = activity.default_target_count
      ? `<strong>${escapeHtml(activityRuleLabel({ target_count: activity.default_target_count, minimum_target_count: activity.default_minimum_target_count, max_per_week: activity.default_max_per_week, allowed_weekdays: activity.default_allowed_weekdays }))}</strong>`
      : '<span>Belum diatur</span>';
    const usage = assignments.length
      ? `<span class="activity-usage">${assignments.length} periode</span>`
      : '<span class="activity-usage is-empty">Belum digunakan</span>';
    const hasHistoricalUsage = assignments.some((assignment) => assignment.period?.status !== 'draft');
    const draftPeriodCount = assignments.filter((assignment) => assignment.period?.status === 'draft').length;
    const periodAction = assignments.length === 0
      ? (drafts.length ? '<button class="text-button organization-row-action" type="button" data-go="periods">Kelola periode</button>' : '')
      : (assignments.length === 1 && assignments[0].period_id
        ? `<button class="text-button organization-row-action" type="button" data-open-period-detail="${escapeHtml(assignments[0].period_id)}">Lihat periode</button>`
        : '<button class="text-button organization-row-action" type="button" data-go="periods">Lihat periode</button>');
    const usageNote = hasHistoricalUsage ? '<small>Terkunci oleh periode aktif atau riwayat</small>' : draftPeriodCount ? '<small>Hanya dipakai di draft</small>' : '';
    const deleteAction = !hasHistoricalUsage ? `<button class="text-button organization-row-action" type="button" data-delete-resource="activity" data-resource-id="${escapeHtml(activity.id)}" data-resource-name="${escapeHtml(activity.name)}" data-draft-period-count="${draftPeriodCount}">Hapus</button>` : '';
    return `<div class="admin-record-row activity-record-row" role="row"><span role="cell"><strong>${escapeHtml(activity.name)}</strong><small>${escapeHtml(activity.code)}</small></span><span class="activity-default-rule" role="cell">${defaultRule}</span><span role="cell"><span class="status ${activity.is_active ? 'status-active' : 'status-closed'}">${activity.is_active ? 'Aktif' : 'Nonaktif'}</span></span><span role="cell">${usage}${usageNote}</span><span role="cell"><button class="text-button organization-row-action" type="button" data-open-admin-modal="activity-edit" data-activity-id="${escapeHtml(activity.id)}">Edit aturan</button>${periodAction}${deleteAction}</span></div>`;
  }).join('');
  const activityContent = !showActivityEmpty
    ? `<section class="admin-record-section" aria-labelledby="activity-list-title"><div class="organization-section-head"><div><h3 id="activity-list-title">Daftar Aktivitas</h3><p>${hasActivitySearch ? `${activityMeta.total} aktivitas ditemukan.` : `${activityMeta.total} aktivitas master tersimpan.`}</p></div></div><form class="admin-list-search" data-admin-list-search="activities"><label class="sr-only" for="activity-search">Cari aktivitas LKS</label><input id="activity-search" name="search" value="${escapeHtml(adminListState.activities.search)}" placeholder="Cari nama atau kode aktivitas" autocomplete="off"></form>${configuration.activities.length ? `<div class="admin-record-table activity-record-table" role="table" aria-label="Daftar aktivitas LKS"><div class="admin-record-head" role="row"><span role="columnheader">Aktivitas</span><span role="columnheader">Aturan default</span><span role="columnheader">Status</span><span role="columnheader">Pemakaian</span><span role="columnheader">Aksi</span></div>${activityRows}</div>${paginationControls(activityMeta, 'activities')}` : '<p class="admin-empty">Tidak ada aktivitas yang sesuai dengan pencarian.</p>'}${drafts.length ? '<p class="admin-context-note">Aktivitas yang hanya dipakai di draft dapat dihapus. Aktivitas pada periode aktif atau riwayat harus dinonaktifkan agar data LKS tetap terjaga.</p>' : '<p class="admin-context-note">Buat periode draft terlebih dahulu sebelum memasukkan aktivitas ke periode.</p>'}</section>`
    : `<section class="organization-empty-state" aria-labelledby="activity-empty-title"><h3 id="activity-empty-title">Belum ada aktivitas LKS</h3><p>Tambahkan aktivitas master yang akan digunakan dalam periode LKS.</p><button class="primary-button" type="button" data-open-admin-modal="activity">Tambah aktivitas</button></section>`;
  const activityActions = !showActivityEmpty ? '<button class="primary-button" type="button" data-open-admin-modal="activity">Tambah aktivitas</button>' : '';
  panel.innerHTML = adminPanel('Aktivitas & Aturan', 'Aturan default dipakai saat aktivitas dimasukkan ke periode draft. Target periode dapat disesuaikan di sana.', `<nav class="admin-back-link" aria-label="Navigasi pengaturan"><button class="text-button" type="button" data-go="settings">Kembali ke Pengaturan</button></nav>${activityContent}`, activityActions, !showActivityEmpty);
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
    calendar_year: String(adminListState.calendar.year),
  });
  panel.setAttribute('aria-busy', 'true');
  if (!dataOnly) {
    panel.innerHTML = adminPanel('Memuat konfigurasi', 'Data administrasi sedang disiapkan.', '<div class="loading-state" role="status"><span class="loading-spinner" aria-hidden="true"></span><span>Menyiapkan data administrasi…</span></div><div class="loading-lines" aria-hidden="true"><i></i><i></i><i></i></div>');
    showPageProgress('Memuat data administrasi…');
  }
  try {
    const needsSignatories = ['settings', 'periods', 'period-detail', 'documents'].includes(name);
    const [organization, configuration, signatories] = await Promise.all([
      fetch(`${apiBase}/admin/organization?${query}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
      fetch(`${apiBase}/admin/configuration?${query}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }),
      needsSignatories ? fetch(`${apiBase}/admin/document-signatories`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' }) : Promise.resolve(null),
    ]);
    const [organizationPayload, configurationPayload, signatoriesPayload] = await Promise.all([
      organization.ok ? organization.json() : null,
      configuration.ok ? configuration.json() : null,
      signatories?.ok ? signatories.json() : null,
    ]);
    if (requestId !== latestAdminRequest) return;
    if (organizationPayload) organizationData = organizationPayload.data;
    if (configurationPayload) configurationData = configurationPayload.data;
    if (signatoriesPayload) documentSignatoriesData = signatoriesPayload.data;
    if (name === 'period-detail') {
      const period = (configurationPayload?.data?.period_options ?? []).find((item) => item.id === selectedPeriodId);
      if (period?.status === 'active') {
        const participantQuery = new URLSearchParams({ search: adminListState.participants.search, page: String(adminListState.participants.page) });
        const response = await fetch(`${apiBase}/periods/${period.id}/participants?${participantQuery}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        if (!response.ok) throw new Error(await apiError(response));
        activePeriodParticipants = (await response.json()).data;
      } else {
        activePeriodParticipants = null;
      }
    }
    if (requestId !== latestAdminRequest) return;
    const requiresOrganization = ['settings', 'organization', 'people', 'periods', 'period-detail'].includes(name);
    const requiresConfiguration = ['settings', 'periods', 'period-detail', 'activities', 'calendar'].includes(name);
    if ((requiresOrganization && !organizationPayload) || (requiresConfiguration && !configurationPayload) || (needsSignatories && !signatoriesPayload)) {
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
  if (type === 'bulk-checklist') return submitBulkChecklistForm(form);
  let payload = Object.fromEntries(formData.entries());
  let method = 'POST';
  const endpoints = { department: '/admin/departments', team: '/admin/teams', santri: '/admin/santri', period: '/admin/periods', activity: '/admin/activities', holiday: '/admin/calendar-holidays', 'holiday-import': '/admin/calendar-holidays/import' };
  let endpoint = type === 'period-activity' ? `/admin/periods/${payload.period_id}/activities` : type === 'participant' ? `/periods/${payload.period_id}/participants/${payload.profile_id}` : endpoints[type];
  if (type === 'signatory-edit') endpoint = `/admin/document-signatories/${payload.signatory_id}`;
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
  if (type === 'period-holiday') endpoint = `/admin/periods/${form.dataset.periodId}/holidays`;
  if (type === 'period-holiday-import') endpoint = `/admin/periods/${form.dataset.periodId}/holidays/import`;
  if (type === 'activity-edit') {
    endpoint = `/admin/activities/${form.dataset.activityId}`;
    method = 'PATCH';
    payload.is_active = form.elements.is_active.checked;
  }
  if (type === 'activity' || type === 'activity-edit') {
    payload.default_allowed_weekdays = [...form.querySelectorAll('[name="default_allowed_weekdays"]:checked')].map((input) => Number(input.value));
    payload.default_applicable_genders = [...form.querySelectorAll('[name="default_applicable_genders"]:checked')].map((input) => input.value);
    payload.default_applicable_levels = [...form.querySelectorAll('[name="default_applicable_levels"]:checked')].map((input) => input.value);
  }
  if (type === 'holiday-import') {
    const { holidays, errors } = parseHolidayImportEntries(payload.entries);
    if (errors.length || !holidays.length) return setFormError(form, `Periksa baris ${errors.length ? errors.join(', ') : 'yang diisi'}. Gunakan format: YYYY-MM-DD, Nama hari libur, tanggal merah atau cuti bersama.`);
    payload = { holidays };
  }
  if (type === 'period-holiday-import') {
    const { holidays, errors } = parseHolidayImportEntries(payload.entries);
    if (errors.length || !holidays.length) return setFormError(form, `Periksa baris ${errors.length ? errors.join(', ') : 'yang diisi'}. Gunakan format: YYYY-MM-DD, Nama hari libur, tanggal merah atau cuti bersama.`);
    payload = { holidays };
  }
  if (type === 'checklist-correction') {
    const activities = [...form.querySelectorAll('[data-correction-activity]')]
      .filter((input) => input.checked !== (input.dataset.originalCompleted === 'true'))
      .map((input) => ({ period_activity_id: input.value, is_completed: input.checked }));
    if (!activities.length) return setFormError(form, 'Belum ada perubahan checklist untuk disimpan.');
    endpoint = `/admin/periods/${form.dataset.periodId}/participants/${form.dataset.participantId}/checklists`;
    method = 'PUT';
    payload = {
      checklist_date: payload.checklist_date,
      reason: payload.reason,
      activities,
    };
  }
  if (type === 'period-config') {
    endpoint = `/admin/periods/${form.dataset.periodId}/activities`;
    method = 'PATCH';
    payload = { activities: [...form.querySelectorAll('[data-period-activity-id]')].map((row) => ({
      id: row.dataset.periodActivityId,
      target_count: Number(row.querySelector('[data-target-count]').value),
      minimum_target_count: Number(row.querySelector('[data-minimum-target-count]').value),
      max_per_week: row.querySelector('[data-max-per-week]').value === '' ? null : Number(row.querySelector('[data-max-per-week]').value),
      is_active: row.querySelector('[data-is-active]').checked,
      allowed_weekdays: [...row.querySelectorAll('[name="allowed_weekdays"]:checked')].map((input) => Number(input.value)),
      applicable_genders: [...row.querySelectorAll('[name="applicable_genders"]:checked')].map((input) => input.value),
      applicable_levels: [...row.querySelectorAll('[name="applicable_levels"]:checked')].map((input) => input.value),
    })) };
  }
  if (type === 'period-activity') {
    payload.allowed_weekdays = [...form.querySelectorAll('[name="allowed_weekdays"]:checked')].map((input) => Number(input.value));
    payload.applicable_genders = [...form.querySelectorAll('[name="applicable_genders"]:checked')].map((input) => input.value);
    payload.applicable_levels = [...form.querySelectorAll('[name="applicable_levels"]:checked')].map((input) => input.value);
  }
  if (type === 'period-activity' || type === 'participant') { delete payload.period_id; delete payload.profile_id; }
  if (['final_passing_threshold', 'staff_passing_threshold', 'sort_order', 'target_count'].some((key) => key in payload)) Object.keys(payload).forEach((key) => { if (['final_passing_threshold', 'staff_passing_threshold', 'sort_order', 'target_count'].includes(key)) payload[key] = Number(payload[key]); });
  if (type === 'participant') {
    const periodName = form.dataset.periodName || 'periode aktif';
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
    const multipart = type === 'signatory-edit';
    const response = await fetch(`${apiBase}${endpoint}`, { method, credentials: 'same-origin', headers: { Accept: 'application/json', ...(!multipart ? { 'Content-Type': 'application/json' } : {}), ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) }, body: multipart ? formData : JSON.stringify(payload) });
    if (!response.ok) throw await apiFormError(response);
    const result = await response.json().catch(() => ({}));
    showToast(type === 'checklist-correction'
      ? 'Koreksi tersimpan dan nilai telah diperbarui'
      : result.meta?.calendar_review_required
        ? 'Rentang periode berubah. Hari libur referensi telah disesuaikan; tinjau kembali sebelum aktivasi.'
        : 'Data berhasil disimpan');
    form.closest('dialog')?.close();
    if (type === 'checklist-correction') {
      correctionParticipantId = null;
      loadRecap(recapData?.period?.id ?? '', { showProgress: false });
      return;
    }
    organizationData = null;
    configurationData = null;
    documentSignatoriesData = null;
    activePeriodParticipants = null;
    const returnView = form.closest('dialog')?.dataset.returnView;
    if (returnView) loadAdminView(returnView, { dataOnly: true });
  } catch (error) {
    const message = error.message || 'Data belum dapat disimpan. Coba lagi.';
    setFormError(form, message, error.fieldErrors);
  } finally { setFormBusy(form, false); }
}

async function submitBulkChecklistForm(form) {
  updateBulkChecklistSelection(form);
  const checklistDates = selectedBulkDates();
  if (!checklistDates.length) return setFormError(form, 'Pilih minimal satu tanggal untuk dicatat.');
  if (checklistDates.length > 7) {
    const confirmed = await confirmAction({
      title: 'Catat banyak tanggal?',
      message: `${checklistDates.length} tanggal akan dicatat sebagai selesai. Pastikan pilihan ini sudah tepat.`,
      confirmLabel: `Catat ${checklistDates.length} tanggal`,
    });
    if (!confirmed) return;
  }

  setFormError(form);
  setFormBusy(form, true, 'Mencatat…');
  try {
    const response = await fetch(`${apiBase}/checklists/bulk`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) },
      body: JSON.stringify({ period_activity_id: form.dataset.activityId, checklist_dates: checklistDates }),
    });
    if (!response.ok) throw await apiFormError(response);
    const result = (await response.json()).data;
    form.closest('dialog')?.close();
    bulkChecklistState = null;
    await loadDashboard({ date: selectedChecklistDate, reloadRecap: false });
    showToast(`${result.recorded} tanggal dicatat${result.skipped ? ` · ${result.skipped} sudah tercatat` : ''}.`);
  } catch (error) {
    setFormError(form, error.message || 'Tanggal belum dapat dicatat. Coba lagi.', error.fieldErrors);
    markBulkChecklistInvalidDates(form, error.fieldErrors);
  } finally {
    setFormBusy(form, false);
  }
}

async function loadChecklistCorrectionContext(date) {
  if (!recapData || !correctionParticipantId) return;
  adminFormDialogContent.setAttribute('aria-busy', 'true');
  try {
    const response = await fetch(`${apiBase}/admin/periods/${encodeURIComponent(recapData.period.id)}/participants/${encodeURIComponent(correctionParticipantId)}/checklists?date=${encodeURIComponent(date)}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (!response.ok) throw new Error(await apiError(response));
    const context = (await response.json()).data;
    const rows = context.activities.map((activity) => `<label class="correction-checklist-row"><span><strong>${escapeHtml(activity.name)}</strong><small>${activity.is_completed ? 'Tercatat selesai' : 'Belum dicatat'}</small></span><input type="checkbox" value="${escapeHtml(activity.id)}" data-correction-activity data-original-completed="${activity.is_completed ? 'true' : 'false'}"${activity.is_completed ? ' checked' : ''}><span class="sr-only">${escapeHtml(activity.name)}</span></label>`).join('');
    const auditTrail = context.corrections?.length ? `<section class="correction-audit" aria-label="Riwayat koreksi"><h3>Riwayat koreksi tanggal ini</h3>${context.corrections.map((correction) => `<p><strong>${escapeHtml(correction.activity ?? 'Aktivitas')}</strong> · ${escapeHtml(correction.actor)}<small>${correction.before === null ? 'Belum dicatat' : correction.before ? 'Selesai' : 'Tidak selesai'} → ${correction.after ? 'Selesai' : 'Tidak selesai'} · ${escapeHtml(correction.reason ?? '')}</small></p>`).join('')}</section>` : '';
    adminFormDialogContent.innerHTML = `<form class="admin-modal-form" data-admin-form="checklist-correction" data-period-id="${escapeHtml(recapData.period.id)}" data-participant-id="${escapeHtml(context.participant.id)}"><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">Lihat &amp; koreksi catatan</h2><p>${escapeHtml(context.participant.name)} · ${escapeHtml(context.participant.team ?? 'Tanpa tim')} · ${escapeHtml(context.participant.department ?? 'Tanpa departemen')}</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields"><label>Tanggal pencatatan<input type="date" name="checklist_date" data-correction-date min="${escapeHtml(context.minimum_date)}" max="${escapeHtml(context.maximum_date)}" value="${escapeHtml(context.date)}" required></label><section class="correction-checklist" aria-label="Checklist ${escapeHtml(context.participant.name)}"><p>Ubah hanya aktivitas yang perlu dikoreksi. Nilai dan status akan dihitung ulang setelah disimpan.</p>${rows || '<p class="admin-form-note">Tidak ada aktivitas yang berlaku untuk peserta ini.</p>'}</section>${auditTrail}<label>Alasan koreksi<textarea name="reason" maxlength="1000" required></textarea><small>Alasan tersimpan pada riwayat audit.</small></label></div><div class="admin-modal-actions"><button class="text-button" type="button" data-close-admin-modal>Batal</button><button class="primary-button" type="submit">Simpan koreksi</button></div></form>`;
    markAdminFormPristine();
    adminFormDialog.querySelector('[data-correction-date]')?.focus();
  } catch (error) {
    adminFormDialogContent.innerHTML = `<div class="admin-modal-form"><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">Koreksi checklist</h2><p>Data catatan belum dapat dimuat.</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><p class="admin-form-error">${escapeHtml(error.message || 'Coba lagi beberapa saat lagi.')}</p></div>`;
  } finally {
    adminFormDialogContent.removeAttribute('aria-busy');
  }
}

async function openChecklistCorrection(participantId) {
  if (!recapData || recapData.period.status !== 'active') {
    showToast('Koreksi hanya tersedia pada periode aktif.', 'error');
    return;
  }
  correctionParticipantId = participantId;
  const maximumDate = [todayIso(), dateInputValue(recapData.period.end_date)].sort()[0];
  const selectedDate = selectedChecklistDate >= dateInputValue(recapData.period.start_date) && selectedChecklistDate <= maximumDate
    ? selectedChecklistDate
    : maximumDate;
  adminFormDialogContent.innerHTML = '<div class="admin-modal-form"><p class="admin-form-note">Memuat catatan peserta…</p></div>';
  adminFormDialog.dataset.returnView = 'recap';
  adminFormDialog.showModal();
  await loadChecklistCorrectionContext(selectedDate);
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
    loadAdminView(list === 'participants' ? 'period-detail' : list, { dataOnly: true });
  }, 280);
});
document.addEventListener('submit', (event) => {
  const list = event.target.dataset.adminListSearch;
  if (!list) return;
  event.preventDefault();
  clearTimeout(adminSearchTimer);
  adminListState[list].search = event.target.elements.search.value.trim();
  adminListState[list].page = 1;
  loadAdminView(list === 'participants' ? 'period-detail' : list, { dataOnly: true });
});
function openAdminFormModal(type, departmentId = '', returnView = '') {
  const organization = organizationData ?? { departments: [], department_options: [], santri: [], santri_options: [], team_leader_options: [] };
  const configuration = configurationData ?? { periods: [], period_options: [], activities: [], activity_options: [] };
  const departmentOptions = organization.department_options ?? organization.departments;
  const teams = departmentOptions.flatMap((department) => department.teams.map((team) => ({ ...team, name: `${department.name} — ${team.name}` })));
  const selectedDepartment = departmentOptions.find((department) => department.id === departmentId);
  const selectedTeam = teams.find((team) => team.id === departmentId);
  const periodOptions = configuration.period_options ?? configuration.periods;
  const activityOptions = configuration.activity_options ?? configuration.activities;
  const drafts = periodOptions.filter((period) => period.status === 'draft');
  const activePeriods = periodOptions.filter((period) => period.status === 'active');
  const selectedParticipantPeriod = activePeriods.find((period) => period.id === departmentId) ?? activePeriods[0];
  const participants = (organization.santri_options ?? organization.santri).map((profile) => ({ id: profile.user_id ?? profile.id, name: profile.user.name }));
  const enrolledParticipantIds = new Set((activePeriodParticipants?.participants ?? []).map((participant) => participant.user_id));
  const availableParticipants = participants.filter((participant) => !enrolledParticipantIds.has(participant.id));
  const configuredPeriod = periodOptions.find((period) => period.id === departmentId);
  const selectedProfile = (organization.santri ?? []).find((profile) => profile.user_id === departmentId);
  const teamLeaderOptions = (organization.team_leader_options ?? []).filter((profile) => profile.team_id === selectedTeam?.id).map((profile) => ({ id: profile.user_id, name: profile.user.name }));
  const selectedJobLevel = selectedProfile?.level === 'leader' ? 'leader' : 'staff';
  const selectedActivity = activityOptions.find((activity) => activity.id === departmentId);
  const selectedSignatory = (documentSignatoriesData ?? []).find((signatory) => signatory.id === departmentId);
  const activeActivityOptions = activityOptions.filter((activity) => activity.is_active);
  const configuredActivityCodes = new Set((configuredPeriod?.period_activities ?? []).map((activity) => activity.activity_code_snapshot));
  const availablePeriodActivityOptions = activeActivityOptions.filter((activity) => !configuredActivityCodes.has(activity.code));
  const definitions = {
    department: {
      title: 'Tambah departemen', description: 'Tambahkan unit utama untuk penempatan Santri Karya.', submitLabel: 'Simpan departemen',
      fields: '<label>Kode<input name="code" maxlength="30" autocomplete="off" required></label><label>Nama departemen<input name="name" maxlength="100" autocomplete="organization" required></label>',
    },
    team: {
      title: 'Tambah tim', description: 'Pilih departemen induk sebelum menyimpan tim baru. Leader Tim dapat dipilih setelah akun Leader dibuat.', submitLabel: 'Simpan tim',
      fields: `<label>Departemen<select name="department_id" required>${adminOptions(departmentOptions.filter((department) => department.is_active), departmentId)}</select></label><label>Kode<input name="code" maxlength="30" autocomplete="off" required></label><label>Nama tim<input name="name" maxlength="100" autocomplete="organization" required></label>`,
    },
    'department-edit': selectedDepartment ? {
      title: `Kelola departemen · ${escapeHtml(selectedDepartment.name)}`, description: 'Perubahan nama dan kode berlaku untuk penempatan berikutnya. Departemen hanya dapat diarsipkan setelah seluruh tim aktifnya diarsipkan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="30" value="${escapeHtml(selectedDepartment.code)}" required></label><label>Nama departemen<input name="name" maxlength="100" value="${escapeHtml(selectedDepartment.name)}" required></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedDepartment.is_active ? ' checked' : ''}> Departemen aktif</label>`,
    } : null,
    'team-edit': selectedTeam ? {
      title: `Kelola tim · ${escapeHtml(selectedTeam.name)}`, description: 'Pilih satu Leader Tim agar seluruh anggota mengikuti akses dan catatan LKS yang sama. Tim hanya dapat diarsipkan setelah seluruh Santri Karya aktifnya dipindahkan atau dinonaktifkan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="30" value="${escapeHtml(selectedTeam.code)}" required></label><label>Nama tim<input name="name" maxlength="100" value="${escapeHtml(selectedTeam.name)}" required></label><label>Leader Tim<select name="leader_user_id">${adminOptions(teamLeaderOptions, selectedTeam.leader_user_id ?? '')}</select><small>Pilih Santri Karya aktif dengan level jabatan Leader dari tim ini.</small></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedTeam.is_active ? ' checked' : ''}> Tim aktif</label>`,
    } : null,
    santri: {
      title: 'Tambah Santri Karya', description: 'Admin menetapkan password sementara dan penempatan organisasi akun baru. Leader Tim ditentukan dari pengaturan Tim.', submitLabel: 'Buat akun Santri',
      fields: `<label>Nama<input name="name" maxlength="150" autocomplete="name" required></label><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Password sementara<input type="password" name="temporary_password" minlength="8" autocomplete="new-password" required></label><div class="admin-modal-inline-fields"><label>Gender<select name="gender" required><option value="ikhwan">Ikhwan</option><option value="akhwat">Akhwat</option></select></label><label>Tim<select name="team_id" required>${adminOptions(teams)}</select></label></div><label>Level jabatan<select name="level" required><option value="staff" selected>Staff</option><option value="leader">Leader</option></select><small>Setelah akun Leader dibuat, pilih akun tersebut sebagai Leader Tim dari pengaturan Tim.</small></label>`,
    },
    'santri-edit': selectedProfile ? {
      title: `Kelola Santri Karya · ${escapeHtml(selectedProfile.user.name)}`, description: 'Perubahan penempatan berlaku untuk data berikutnya. Riwayat periode yang sudah berjalan tetap menggunakan snapshot sebelumnya. Leader mengikuti Tim yang dipilih.', submitLabel: 'Simpan perubahan',
      fields: `<label>Nama<input name="name" maxlength="150" autocomplete="name" value="${escapeHtml(selectedProfile.user.name)}" required></label><label>Email<input type="email" name="email" autocomplete="email" value="${escapeHtml(selectedProfile.user.email)}" required></label><div class="admin-modal-inline-fields"><label>Gender<select name="gender" required><option value="ikhwan"${selectedProfile.gender === 'ikhwan' ? ' selected' : ''}>Ikhwan</option><option value="akhwat"${selectedProfile.gender === 'akhwat' ? ' selected' : ''}>Akhwat</option></select></label><label>Tim<select name="team_id" required>${adminOptions(teams.filter((team) => team.is_active), selectedProfile.team_id)}</select><small>Leader Tim diatur dari pengaturan Tim.</small></label></div><label>Level jabatan<select name="level" required><option value="staff"${selectedJobLevel === 'staff' ? ' selected' : ''}>Staff</option><option value="leader"${selectedJobLevel === 'leader' ? ' selected' : ''}>Leader</option></select></label><label>Status akun<select name="status" required><option value="active"${selectedProfile.status === 'active' ? ' selected' : ''}>Aktif</option><option value="inactive"${selectedProfile.status === 'inactive' ? ' selected' : ''}>Nonaktif</option></select></label>`,
    } : null,
    'signatory-edit': selectedSignatory ? {
      title: `Kelola TTD digital · ${escapeHtml(selectedSignatory.title)}`, description: 'Nama dan PNG TTD ini dipakai pada PDF baru. Snapshot periode yang sudah aktif atau ditutup tidak berubah.', submitLabel: 'Simpan TTD digital',
      fields: `<input type="hidden" name="signatory_id" value="${escapeHtml(selectedSignatory.id)}"><label>Nama pejabat<input name="name" maxlength="150" value="${escapeHtml(selectedSignatory.name ?? '')}" required></label><label>Gambar TTD digital<input type="file" name="signature" accept="image/png"><small>${selectedSignatory.signature_configured ? 'PNG saat ini tetap digunakan bila Anda tidak memilih berkas baru.' : 'Unggah PNG transparan, maksimal 2 MB.'}</small></label>`,
    } : null,
    period: {
      title: 'Tambah periode LKS', description: 'Periode dibuat sebagai draft agar aktivitas dapat disiapkan sebelum diaktifkan.', submitLabel: 'Simpan periode draft',
      fields: '<label>Nama periode<input name="name" placeholder="Oktober 2026" maxlength="100" required></label><div class="admin-modal-inline-fields"><label>Tanggal mulai<input type="date" name="start_date" required></label><label>Tanggal selesai<input type="date" name="end_date" required></label></div><div class="admin-modal-inline-fields"><label>Ambang Leader (%)<input type="number" name="final_passing_threshold" min="0" max="100" value="90" required></label><label>Ambang Staff (%)<input type="number" name="staff_passing_threshold" min="0" max="100" value="85" required></label></div><p class="admin-form-note">Nilai akhir dibandingkan dengan ambang sesuai level jabatan peserta.</p>',
    },
    'period-edit': configuredPeriod ? {
      title: `Kelola periode · ${escapeHtml(configuredPeriod.name)}`, description: 'Periode draft dapat disesuaikan sebelum diaktifkan. Setelah aktif, rentang dan ambang nilai dikunci untuk menjaga konsistensi perhitungan.', submitLabel: 'Simpan perubahan',
      fields: `<label>Nama periode<input name="name" maxlength="100" value="${escapeHtml(configuredPeriod.name)}" required></label><div class="admin-modal-inline-fields"><label>Tanggal mulai<input type="date" name="start_date" value="${escapeHtml(dateInputValue(configuredPeriod.start_date))}" required></label><label>Tanggal selesai<input type="date" name="end_date" value="${escapeHtml(dateInputValue(configuredPeriod.end_date))}" required></label></div><div class="admin-modal-inline-fields"><label>Ambang Leader (%)<input type="number" name="final_passing_threshold" min="0" max="100" value="${escapeHtml(configuredPeriod.final_passing_threshold)}" required></label><label>Ambang Staff (%)<input type="number" name="staff_passing_threshold" min="0" max="100" value="${escapeHtml(configuredPeriod.staff_passing_threshold ?? 85)}" required></label></div>`,
    } : null,
    activity: {
      title: 'Tambah aktivitas LKS', description: 'Aktivitas master dapat dipakai kembali pada periode draft berikutnya.', submitLabel: 'Simpan aktivitas',
      fields: '<label>Kode<input name="code" maxlength="50" autocomplete="off" required></label><label>Nama aktivitas<input name="name" maxlength="150" required></label><p class="admin-form-note">Urutan aktivitas diatur otomatis oleh sistem. Target dan status aktif diatur saat aktivitas dimasukkan ke periode draft.</p>',
    },
    'activity-edit': selectedActivity ? {
      title: `Kelola aktivitas · ${escapeHtml(selectedActivity.name)}`, description: 'Perubahan berlaku saat aktivitas dipakai pada periode berikutnya. Snapshot aktivitas pada periode aktif atau tertutup tetap terjaga.', submitLabel: 'Simpan perubahan',
      fields: `<label>Kode<input name="code" maxlength="50" autocomplete="off" value="${escapeHtml(selectedActivity.code)}" required></label><label>Nama aktivitas<input name="name" maxlength="150" value="${escapeHtml(selectedActivity.name)}" required></label><label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedActivity.is_active ? ' checked' : ''}> Aktivitas aktif</label>`,
    } : null,
    'period-activity': configuredPeriod ? {
      title: `Tambah aktivitas · ${escapeHtml(configuredPeriod.name)}`, description: 'Hanya aktivitas master yang belum ada di periode ini yang dapat ditambahkan.', submitLabel: 'Tambahkan aktivitas',
      fields: availablePeriodActivityOptions.length ? `<input type="hidden" name="period_id" value="${escapeHtml(configuredPeriod.id)}"><label>Aktivitas<select name="activity_id" required>${adminOptions(availablePeriodActivityOptions)}</select></label><div class="admin-modal-inline-fields"><label>Target maksimum<input type="number" name="target_count" min="1" required></label><label>Target minimal tuntas<input type="number" name="minimum_target_count" min="1" required></label></div><label>Maksimal per pekan<input type="number" name="max_per_week" min="1"><small>Kosongkan bila tidak ada batas mingguan.</small></label>${weekdayPicker()}${audiencePicker()}` : '<p class="admin-form-note">Semua aktivitas master aktif sudah ada di periode ini. Ubah aturannya melalui Kelola aktivitas.</p>',
    } : null,
    'period-config': configuredPeriod ? {
      title: `Atur aktivitas · ${escapeHtml(configuredPeriod.name)}`, description: `Capaian tiap aktivitas dibatasi 100%. Nilai akhir adalah rata-rata aktivitas aktif dan dinyatakan Tuntas pada ${configuredPeriod.final_passing_threshold}%.`, submitLabel: 'Simpan pengaturan aktivitas',
      fields: `<div class="period-config-list">${configuredPeriod.period_activities.map((activity) => `<fieldset class="period-config-row" data-period-activity-id="${escapeHtml(activity.id)}"><div><strong>${escapeHtml(activity.activity_name_snapshot)}</strong><small>Rumus: min(checklist selesai ÷ target, 100%)</small></div><label>Target<input type="number" min="1" value="${activity.target_count}" data-target-count required></label><label class="admin-modal-checkbox"><input type="checkbox" data-is-active${activity.is_active ? ' checked' : ''}> Aktif</label><div class="period-weekday-control">${weekdayPicker(activity.allowed_weekdays)}</div></fieldset>`).join('') || '<p class="admin-form-note">Tambahkan aktivitas ke periode ini terlebih dahulu dari halaman Aktivitas LKS.</p>'}</div>`,
    } : null,
    participant: {
      title: `Tambah peserta · ${escapeHtml(selectedParticipantPeriod?.name ?? 'Periode aktif')}`, description: 'Peserta mulai dapat mengisi checklist sejak dimasukkan. Catatan sebelumnya tidak dibuat mundur.', submitLabel: 'Masukkan peserta',
      fields: `<input type="hidden" name="period_id" value="${escapeHtml(selectedParticipantPeriod?.id ?? '')}"><label>Santri Karya<select name="profile_id" required>${adminOptions(availableParticipants)}</select></label><p class="admin-form-note">Pilih akun yang dibuat setelah periode aktif dimulai atau yang belum tercatat sebagai peserta.</p>`,
    },
  };
  if (type === 'period-activity') {
    definitions['period-activity'].description = 'Tentukan target maksimum, batas tuntas, jadwal, dan sasaran aktivitas untuk periode draft ini.';
  }
  if (type === 'period-config' && configuredPeriod) {
    definitions['period-config'].fields = periodActivityConfigurationFields(configuredPeriod);
    definitions['period-config'].description = `Nilai aktivitas dibatasi 100%. Nilai akhir adalah rata-rata aktivitas yang berlaku untuk peserta; ambang Leader ${configuredPeriod.final_passing_threshold}% dan Staff ${configuredPeriod.staff_passing_threshold ?? 85}%.`;
  }
  if (type === 'activity') {
    definitions.activity.fields = `<label>Kode<input name="code" maxlength="50" autocomplete="off" required></label><label>Nama aktivitas<input name="name" maxlength="150" required></label>${masterActivityRuleFields()}`;
    definitions.activity.description = 'Atur rumus default untuk periode baru. Periode yang sudah aktif tetap memakai snapshot aturannya.';
  }
  if (type === 'activity-edit' && selectedActivity) {
    definitions['activity-edit'].fields = `<label>Kode<input name="code" maxlength="50" autocomplete="off" value="${escapeHtml(selectedActivity.code)}" required></label><label>Nama aktivitas<input name="name" maxlength="150" value="${escapeHtml(selectedActivity.name)}" required></label>${masterActivityRuleFields(selectedActivity)}<label class="admin-modal-checkbox"><input type="checkbox" name="is_active"${selectedActivity.is_active ? ' checked' : ''}> Aktivitas aktif</label>`;
    definitions['activity-edit'].description = 'Aturan ini menjadi default periode baru. Snapshot aktivitas pada periode aktif atau tertutup tetap terjaga.';
  }
  definitions.holiday = {
    title: 'Tambah hari libur', description: 'Hari libur hanya berlaku untuk periode yang diaktifkan setelah kalender ini disiapkan.', submitLabel: 'Simpan hari libur',
    fields: '<label>Tanggal<input type="date" name="holiday_date" required></label><label>Nama hari libur<input name="name" maxlength="150" placeholder="Contoh: Hari Kemerdekaan" required></label><label>Jenis<select name="type" required><option value="public_holiday">Tanggal merah</option><option value="collective_leave">Cuti bersama</option></select></label>',
  };
  definitions['holiday-import'] = {
    title: 'Impor kalender kerja', description: 'Masukkan satu hari libur per baris. Data dengan tanggal sama akan diperbarui, bukan digandakan.', submitLabel: 'Impor kalender',
    fields: '<label>Daftar hari libur<textarea name="entries" rows="8" required placeholder="2026-01-01, Tahun Baru, tanggal merah&#10;2026-03-20, Cuti bersama Idulfitri, cuti bersama"></textarea><small>Format: YYYY-MM-DD, Nama hari libur, tanggal merah atau cuti bersama. Koma di akhir baris boleh diabaikan.</small></label>',
  };
  definitions['period-holiday'] = configuredPeriod ? {
    title: `Tambah hari libur · ${escapeHtml(configuredPeriod.name)}`, description: 'Tanggal hanya dapat berada dalam rentang periode ini. Checklist pada tanggal tersebut opsional dan tidak memengaruhi nilai.', submitLabel: 'Simpan hari libur',
    fields: `<label>Tanggal<input type="date" name="holiday_date" min="${escapeHtml(dateInputValue(configuredPeriod.start_date))}" max="${escapeHtml(dateInputValue(configuredPeriod.end_date))}" required></label><label>Nama hari libur<input name="name" maxlength="150" placeholder="Contoh: Hari Kemerdekaan" required></label><label>Jenis<select name="type" required><option value="public_holiday">Tanggal merah</option><option value="collective_leave">Cuti bersama</option></select></label>`,
  } : null;
  definitions['period-holiday-import'] = configuredPeriod ? {
    title: `Impor hari libur · ${escapeHtml(configuredPeriod.name)}`, description: `Masukkan tanggal antara ${escapeHtml(formatDate(configuredPeriod.start_date, true))} dan ${escapeHtml(formatDate(configuredPeriod.end_date, true))}. Tanggal yang sama diperbarui, bukan digandakan.`, submitLabel: 'Impor daftar',
    fields: '<label>Daftar hari libur<textarea name="entries" rows="8" required placeholder="2026-10-01, Hari libur nasional, tanggal merah&#10;2026-10-02, Cuti bersama, cuti bersama"></textarea><small>Format: YYYY-MM-DD, Nama hari libur, tanggal merah atau cuti bersama. Koma di akhir baris boleh diabaikan.</small></label>',
  } : null;
  const definition = definitions[type];
  if (!definition) return;
  const disableSubmit = (type === 'period-config' && configuredPeriod.period_activities.length === 0) || (type === 'period-activity' && availablePeriodActivityOptions.length === 0) || (type === 'participant' && (!selectedParticipantPeriod || availableParticipants.length === 0));
  adminFormDialogContent.innerHTML = `<form class="admin-modal-form" data-admin-form="${type}"${['period-config', 'period-edit', 'period-holiday', 'period-holiday-import'].includes(type) ? ` data-period-id="${escapeHtml(departmentId)}"` : ''}${type === 'participant' ? ` data-period-name="${escapeHtml(selectedParticipantPeriod?.name ?? '')}"` : ''}${type === 'department-edit' || type === 'team-edit' ? ` data-organization-id="${escapeHtml(departmentId)}"` : ''}${type === 'santri-edit' ? ` data-santri-id="${escapeHtml(departmentId)}"` : ''}${type === 'activity-edit' ? ` data-activity-id="${escapeHtml(departmentId)}"` : ''}><div class="admin-modal-heading"><div><h2 id="admin-form-dialog-title">${definition.title}</h2><p>${definition.description}</p></div><button class="text-button" type="button" data-close-admin-modal>Tutup</button></div><div class="admin-modal-fields">${definition.fields}</div><div class="admin-modal-actions"><button class="text-button" type="button" data-close-admin-modal>Batal</button><button class="primary-button" type="submit"${disableSubmit ? ' disabled' : ''}>${definition.submitLabel}</button></div></form>`;
  adminFormDialog.classList.toggle('is-period-config', type === 'period-config');
  adminFormDialog.dataset.returnView = returnView;
  adminFormDialog.showModal();
  markAdminFormPristine();
  adminFormDialog.querySelector('input, select')?.focus();
}

document.addEventListener('click', async (event) => {
  const activitySelection = event.target.closest('[data-period-config-select]');
  if (activitySelection) {
    const manager = activitySelection.closest('.period-config-manager');
    const selectedId = activitySelection.dataset.periodConfigSelect;
    manager?.querySelectorAll('[data-period-config-select]').forEach((button) => {
      const selected = button.dataset.periodConfigSelect === selectedId;
      button.classList.toggle('is-selected', selected);
      button.setAttribute('aria-pressed', String(selected));
    });
    manager?.querySelectorAll('[data-period-activity-id]').forEach((row) => {
      const selected = row.dataset.periodActivityId === selectedId;
      row.hidden = !selected;
      row.disabled = !selected;
    });
    manager?.classList.add('is-editing');
    manager?.querySelector(`[data-period-activity-id="${CSS.escape(selectedId)}"] input, [data-period-activity-id="${CSS.escape(selectedId)}"] select`)?.focus();
    return;
  }
  const activityBack = event.target.closest('[data-period-config-back]');
  if (activityBack) {
    activityBack.closest('.period-config-manager')?.classList.remove('is-editing');
    return;
  }
  const trigger = event.target.closest('[data-open-admin-modal]');
  if (trigger) {
    openAdminFormModal(
      trigger.dataset.openAdminModal,
      trigger.dataset.departmentId ?? trigger.dataset.periodId ?? trigger.dataset.organizationId ?? trigger.dataset.santriId ?? trigger.dataset.activityId ?? trigger.dataset.signatoryId,
      trigger.closest('[data-view-panel]')?.dataset.viewPanel,
    );
    return;
  }
  if (event.target.closest('[data-close-admin-modal]')) await requestAdminFormClose();
});
document.addEventListener('input', (event) => {
  if (!event.target.matches('[data-target-count]')) return;
  const minimum = event.target.closest('[data-period-activity-id]')?.querySelector('[data-minimum-target-count]');
  if (minimum) minimum.max = event.target.value;
});
document.addEventListener('click', (event) => {
  const correction = event.target.closest('[data-open-checklist-correction]');
  if (correction) openChecklistCorrection(correction.dataset.openChecklistCorrection);
});
document.addEventListener('change', (event) => {
  if (event.target.matches('[data-correction-date]')) loadChecklistCorrectionContext(event.target.value);
});
document.addEventListener('change', (event) => {
  if (!event.target.matches('[data-calendar-year]')) return;
  const year = Number(event.target.value);
  if (!Number.isInteger(year) || year < 2000 || year > 2100) return;
  adminListState.calendar.year = year;
  configurationData = null;
  loadAdminView('calendar', { dataOnly: true });
});
document.addEventListener('click', async (event) => {
  const page = event.target.closest('[data-admin-page]');
  if (page) {
    const list = page.dataset.adminPage;
    const nextPage = Number(page.dataset.page);
    if (!page.disabled && adminListState[list] && nextPage > 0) {
      adminListState[list].page = nextPage;
      return loadAdminView(list === 'participants' ? 'period-detail' : list, { dataOnly: true });
    }
  }
  const periodDetail = event.target.closest('[data-open-period-detail]');
  if (periodDetail) {
    selectedPeriodId = periodDetail.dataset.openPeriodDetail;
    adminListState.participants = { page: 1, search: '' };
    activePeriodParticipants = null;
    openView('period-detail');
    return;
  }
  const participantRemoval = event.target.closest('[data-remove-period-participant]');
  if (participantRemoval) {
    const confirmed = await confirmAction({ title: 'Keluarkan peserta dari periode?', message: `${participantRemoval.dataset.participantName} tidak lagi dapat mengisi checklist periode ini. Riwayat checklist yang sudah tercatat tidak dapat dikeluarkan.`, confirmLabel: 'Keluarkan peserta', tone: 'danger' });
    if (!confirmed) return;
    setButtonBusy(participantRemoval, true, 'Mengeluarkan…');
    try {
      const response = await fetch(`${apiBase}/periods/${participantRemoval.dataset.periodId}/participants/${participantRemoval.dataset.removePeriodParticipant}`, { method: 'DELETE', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      showToast('Peserta dikeluarkan dari periode');
      activePeriodParticipants = null;
      return loadAdminView('period-detail', { dataOnly: true });
    } catch (error) { showToast(error.message || 'Peserta belum dapat dikeluarkan.', 'error'); } finally { setButtonBusy(participantRemoval, false); }
  }
  const refresh = event.target.closest('[data-admin-refresh]');
  if (refresh) {
    setButtonBusy(refresh, true, 'Memuat…');
    return loadAdminView(refresh.closest('[data-view-panel]').dataset.viewPanel, { dataOnly: true });
  }
  const importReferenceCalendar = event.target.closest('[data-import-period-calendar]');
  if (importReferenceCalendar) {
    const confirmed = await confirmAction({ title: 'Salin ulang kalender kerja?', message: 'Hari libur Kalender Kerja dalam rentang periode akan disalin. Data dengan tanggal yang sama akan diperbarui dari referensi.', confirmLabel: 'Salin kalender' });
    if (!confirmed) return;
    setButtonBusy(importReferenceCalendar, true, 'Menyalin…');
    try {
      const response = await fetch(`${apiBase}/admin/periods/${importReferenceCalendar.dataset.importPeriodCalendar}/holidays/import-reference`, { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      const { data } = await response.json();
      showToast(data.count ? `${data.count} hari libur disalin ke periode` : 'Tidak ada hari libur referensi dalam rentang periode ini');
      configurationData = null;
      return loadAdminView('period-detail', { dataOnly: true });
    } catch (error) { showToast(error.message || 'Kalender referensi belum dapat disalin.', 'error'); } finally { setButtonBusy(importReferenceCalendar, false); }
  }
  const periodHolidayDeletion = event.target.closest('[data-delete-period-holiday]');
  if (periodHolidayDeletion) {
    const confirmed = await confirmAction({ title: 'Hapus hari libur periode?', message: `${periodHolidayDeletion.dataset.holidayName} tidak lagi menjadi tanggal opsional pada periode ini.`, confirmLabel: 'Hapus hari libur', tone: 'danger' });
    if (!confirmed) return;
    setButtonBusy(periodHolidayDeletion, true, 'Menghapus…');
    try {
      const response = await fetch(`${apiBase}/admin/periods/${periodHolidayDeletion.dataset.periodId}/holidays/${periodHolidayDeletion.dataset.deletePeriodHoliday}`, { method: 'DELETE', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      showToast('Hari libur periode berhasil dihapus');
      configurationData = null;
      return loadAdminView('period-detail', { dataOnly: true });
    } catch (error) { showToast(error.message || 'Hari libur periode belum dapat dihapus.', 'error'); } finally { setButtonBusy(periodHolidayDeletion, false); }
  }
  const holidayDeletion = event.target.closest('[data-delete-holiday]');
  if (holidayDeletion) {
    const confirmed = await confirmAction({ title: 'Hapus hari libur?', message: `${holidayDeletion.dataset.holidayName} akan dihapus dari kalender. Periode yang sudah aktif tetap menggunakan snapshot kalendernya.`, confirmLabel: 'Hapus hari libur', tone: 'danger' });
    if (!confirmed) return;
    setButtonBusy(holidayDeletion, true, 'Menghapus…');
    try {
      const response = await fetch(`${apiBase}/admin/calendar-holidays/${holidayDeletion.dataset.deleteHoliday}`, { method: 'DELETE', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
      if (!response.ok) throw new Error(await apiError(response));
      showToast('Hari libur berhasil dihapus');
      configurationData = null;
      return loadAdminView('calendar', { dataOnly: true });
    } catch (error) { showToast(error.message || 'Hari libur belum dapat dihapus.', 'error'); } finally { setButtonBusy(holidayDeletion, false); }
  }
  const deletion = event.target.closest('[data-delete-resource]');
  if (deletion) {
    const resource = deletion.dataset.deleteResource;
    const labels = { department: 'departemen', team: 'tim', santri: 'akun Santri Karya', activity: 'aktivitas', period: 'periode draft' };
    const viewByResource = { department: 'organization', team: 'organization', santri: 'people', activity: 'activities', period: 'periods' };
    const endpoints = { department: 'departments', team: 'teams', santri: 'santri', activity: 'activities', period: 'periods' };
    const label = labels[resource];
    const draftPeriodCount = Number(deletion.dataset.draftPeriodCount || 0);
    const message = resource === 'activity' && draftPeriodCount
      ? `${deletion.dataset.resourceName} akan dihapus dari master dan ${draftPeriodCount} periode draft. Periode aktif dan riwayat tidak dapat diubah.`
      : `${deletion.dataset.resourceName} akan dihapus permanen. Tindakan ini hanya berhasil bila data belum memiliki riwayat pemakaian.`;
    const confirmed = await confirmAction({ title: `Hapus ${label}?`, message, confirmLabel: `Hapus ${label}`, tone: 'danger' });
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
  const period = (configurationData?.period_options ?? configurationData?.periods ?? []).find((item) => item.id === activate.dataset.activatePeriod);
  const missingSignatories = ['general_manager', 'kabid_pengembangan_spiritual'].filter((role) => !(documentSignatoriesData ?? []).some((signatory) => signatory.role === role && signatory.name && signatory.signature_configured));
  if (missingSignatories.length) {
    showToast('Lengkapi nama dan TTD digital General Manager serta Kabid. Pengembangan Spiritual sebelum aktivasi.', 'error');
    return openView('documents');
  }
  const configuredCodes = new Set((period?.period_activities ?? []).map((activity) => activity.activity_code_snapshot));
  const missingActivities = (configurationData?.activity_options ?? configurationData?.activities ?? []).filter((activity) => activity.is_active && !configuredCodes.has(activity.code));
  const missingActivityMessage = missingActivities.length
    ? `Aktivitas master aktif yang belum dimasukkan: ${missingActivities.map((activity) => activity.name).join(', ')}. Kelola aktivitas terlebih dahulu bila aktivitas tersebut harus berlaku pada periode ini.`
    : 'Periode ini akan menjadi periode LKS aktif dan digunakan untuk pencatatan checklist peserta.';
  const confirmed = await confirmAction({ title: missingActivities.length ? 'Aktivitas periode belum lengkap' : 'Aktifkan periode ini?', message: missingActivityMessage, confirmLabel: missingActivities.length ? `Aktifkan tanpa ${missingActivities.length} aktivitas` : 'Aktifkan periode', tone: missingActivities.length ? 'danger' : 'default' });
  if (!confirmed) return;
  setButtonBusy(activate, true, 'Mengaktifkan…');
  try {
    const response = await fetch(`${apiBase}/periods/${activate.dataset.activatePeriod}/activate`, { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) } });
    if (!response.ok) throw new Error(await apiError(response));
    configurationData = null;
    activePeriodParticipants = null;
    const refreshView = activate.closest('[data-view-panel]')?.dataset.viewPanel === 'period-detail' ? 'period-detail' : 'periods';
    await loadAdminView(refreshView, { dataOnly: true });
    showToast('Periode berhasil diaktifkan. Konfigurasi terkunci dan peserta dapat mulai mencatat LKS.');
    void loadDashboard();
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
      resetHistoryDetail();
      openView('periods');
      loadHistory();
    } catch (error) { showToast(error.message, 'error'); } finally { setButtonBusy(closePeriod, false); }
    return;
  }

  const historyPeriod = event.target.closest('[data-history-period]');
  if (historyPeriod) return loadHistoryRecap(historyPeriod.dataset.historyPeriod, historyPeriod);
  const historyParticipant = event.target.closest('[data-history-participant]');
  if (historyParticipant) {
    historyParticipantId = historyParticipant.dataset.historyParticipant;
    renderHistory();
    return;
  }
  const historyScopeButton = event.target.closest('[data-history-scope]');
  if (historyScopeButton) {
    historyScope = historyScopeButton.dataset.historyScope;
    historyData = null;
    resetHistoryDetail();
    historyYear = '';
    return loadHistory({ showProgress: true });
  }
  if (event.target.closest('[data-history-retry]') && historyDetailPeriodId) {
    return loadHistoryRecap(historyDetailPeriodId);
  }
  if (event.target.closest('[data-history-refresh]')) {
    resetHistoryDetail();
    return loadHistory();
  }
});

async function loadDashboard({ date = selectedChecklistDate, reloadRecap = true, showProgress = false } = {}) {
  const requestId = ++dashboardRequest;
  dashboardAbortController?.abort();
  dashboardAbortController = new AbortController();
  if (showProgress) showPageProgress();
  try {
    const response = await fetch(`${apiBase}/dashboard?date=${encodeURIComponent(date)}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin', signal: dashboardAbortController.signal });
    if (!response.ok) throw new Error('Data LKS belum dapat dimuat. Muat ulang halaman untuk mencoba lagi.');
    if (requestId !== dashboardRequest) return;
    if (applyDashboard((await response.json()).data) && reloadRecap) loadRecap('', { scope: viewer?.roles?.includes('leader') ? 'team' : 'personal' });
  } catch (error) {
    if (requestId !== dashboardRequest || error.name === 'AbortError') return;
    setChecklistEmpty('Data LKS belum dapat dimuat. Muat ulang halaman untuk mencoba lagi.');
    if (document.body.classList.contains('is-booting')) {
      openView('dashboard', { persist: false, scroll: false, load: false });
      finishInitialBoot();
    }
    showToast(error.message, 'error');
  } finally {
    if (requestId === dashboardRequest && showProgress) hidePageProgress();
  }
}

updateTodayProgress();
loadDashboard();
