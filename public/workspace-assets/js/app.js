const HH = {
  key: 'hirehelper_brief_v2',
  inviteKey: 'hirehelper_invite_v2',
  noticeKey: 'hirehelper_notice_closed_v2'
};

const defaultBrief = {
  title: 'HireHelper.ai client dashboard rebuild',
  description: 'Design and implement the post-login client experience for HireHelper.ai, including dashboard, project brief setup, billing setup, and contract management.',
  experience: 'Intermediate',
  timeframe: 'Less than 1 month',
  specialty: 'Full stack development',
  discovery: 'Google research'
};

function getBrief(){
  try{
    return JSON.parse(localStorage.getItem(HH.key)) || {...defaultBrief};
  }catch(e){
    return {...defaultBrief};
  }
}
function saveBrief(data){
  localStorage.setItem(HH.key, JSON.stringify({...getBrief(), ...data}));
}

function collectBriefFromForm(form){
  const data = {};
  form.querySelectorAll('[data-brief-field]').forEach(field => {
    const key = field.getAttribute('data-brief-field');
    if(!key) return;
    if(field.type === 'checkbox'){
      data[key] = field.checked;
    }else{
      data[key] = String(field.value || '').trim();
    }
  });
  return data;
}
function saveBriefFromForm(form){
  if(!form) return true;
  if(!form.reportValidity()) return false;
  saveBrief(collectBriefFromForm(form));
  return true;
}
function getInvite(){
  try{
    return JSON.parse(localStorage.getItem(HH.inviteKey)) || {
      freelancer: 'Ava Petrosyan',
      role: 'Full stack developer',
      rate: '35',
      weekly: '20',
      manual: true,
      multiOffer: false
    };
  }catch(e){
    return {
      freelancer: 'Ava Petrosyan',
      role: 'Full stack developer',
      rate: '35',
      weekly: '20',
      manual: true,
      multiOffer: false
    };
  }
}
function saveInvite(data){
  localStorage.setItem(HH.inviteKey, JSON.stringify({...getInvite(), ...data}));
}
function formatCurrentDate(){
  const d = new Date();
  return new Intl.DateTimeFormat(undefined,{month:'short', day:'numeric', year:'numeric'}).format(d);
}
function updateText(selector, value){
  document.querySelectorAll(selector).forEach(el => el.textContent = value);
}
function populateFromBrief(){
  const brief = getBrief();
  updateText('[data-brief="title"]', brief.title || defaultBrief.title);
  updateText('[data-brief="description"]', brief.description || defaultBrief.description);
  updateText('[data-brief="experience"]', brief.experience || defaultBrief.experience);
  updateText('[data-brief="timeframe"]', brief.timeframe || defaultBrief.timeframe);
  updateText('[data-brief="specialty"]', brief.specialty || defaultBrief.specialty);
  updateText('[data-brief="discovery"]', brief.discovery || defaultBrief.discovery);
  updateText('[data-now]', formatCurrentDate());
}
function populateInvite(){
  const invite = getInvite();
  updateText('[data-invite="freelancer"]', invite.freelancer);
  updateText('[data-invite="role"]', invite.role);
  updateText('[data-invite="rate"]', `$${invite.rate} / hr`);
  updateText('[data-invite="weekly"]', `${invite.weekly} hrs / week`);
  const weeklyAmount = (Number(invite.rate || 0) * Number(invite.weekly || 0)).toFixed(2);
  updateText('[data-invite="weekly-amount"]', `$${weeklyAmount} max / week`);
}

function setupMenu(){
  const btn = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');
  if(!btn || !menu) return;
  btn.addEventListener('click', () => menu.classList.toggle('open'));
  document.addEventListener('click', (e) => {
    if(menu.classList.contains('open') && !menu.contains(e.target) && !btn.contains(e.target)){
      menu.classList.remove('open');
    }
  });
}

function setupWizardForms(){
  document.querySelectorAll('[data-brief-form]').forEach(form => {
    const fields = form.querySelectorAll('[data-brief-field]');
    const brief = getBrief();
    fields.forEach(field => {
      const key = field.getAttribute('data-brief-field');
      if(field.type === 'radio' || field.type === 'checkbox') return;
      if(brief[key]) field.value = brief[key];
    });
    form.addEventListener('submit', e => {
      e.preventDefault();
      if(!form.reportValidity()) return;
      saveBrief(collectBriefFromForm(form));
      const next = form.getAttribute('data-next');
      if(next) location.href = next;
    });
  });
}

function selectCard(group, value){
  document.querySelectorAll(`[data-select-group="${group}"]`).forEach(card => {
    const active = card.getAttribute('data-value') === value;
    card.classList.toggle('is-selected', active);
    card.setAttribute('aria-checked', active ? 'true' : 'false');
  });
  const hidden = document.querySelector(`[data-select-input="${group}"]`);
  if(hidden) hidden.value = value;
}

function setupSelectableCards(){
  document.querySelectorAll('[data-select-group]').forEach(card => {
    card.addEventListener('click', () => {
      const group = card.getAttribute('data-select-group');
      const value = card.getAttribute('data-value');
      selectCard(group, value);
    });
  });
  document.querySelectorAll('[data-select-form]').forEach(form => {
    const group = form.getAttribute('data-select-form');
    const input = form.querySelector(`[data-select-input="${group}"]`);
    const brief = getBrief();
    const startValue = brief[group] || input?.value;
    if(startValue) selectCard(group, startValue);
    form.addEventListener('submit', e => {
      e.preventDefault();
      const next = form.getAttribute('data-next');
      const hidden = form.querySelector(`[data-select-input="${group}"]`);
      if(!hidden || !hidden.value){
        const first = form.querySelector(`[data-select-group="${group}"]`);
        if(first){
          hidden.value = first.getAttribute('data-value');
          selectCard(group, hidden.value);
        }
      }
      if(group && hidden?.value){
        saveBrief({[group]: hidden.value});
      }
      if(next) location.href = next;
    });
  });
}

function setupInviteForm(){
  const form = document.querySelector('[data-invite-form]');
  if(!form) return;
  const invite = getInvite();
  const map = {
    freelancer: '[name="freelancer"]',
    role: '[name="role"]',
    rate: '[name="rate"]',
    weekly: '[name="weekly"]',
    manual: '[name="manual"]',
    multiOffer: '[name="multiOffer"]'
  };
  Object.entries(map).forEach(([key, selector]) => {
    const field = form.querySelector(selector);
    if(!field) return;
    if(field.type === 'checkbox') field.checked = !!invite[key];
    else field.value = invite[key];
  });
  const maxBox = document.querySelector('[data-weekly-max]');
  const refresh = () => {
    const rate = Number(form.querySelector('[name="rate"]').value || 0);
    const weekly = Number(form.querySelector('[name="weekly"]').value || 0);
    if(maxBox) maxBox.textContent = `$${(rate * weekly).toFixed(2)} max / week`;
  };
  refresh();
  form.querySelectorAll('input').forEach(el => el.addEventListener('input', refresh));
  form.addEventListener('submit', e => {
    e.preventDefault();
    if(!form.reportValidity()) return;
    saveInvite({
      freelancer: form.querySelector('[name="freelancer"]').value.trim(),
      role: form.querySelector('[name="role"]').value.trim(),
      rate: form.querySelector('[name="rate"]').value.trim(),
      weekly: form.querySelector('[name="weekly"]').value.trim(),
      manual: form.querySelector('[name="manual"]').checked,
      multiOffer: form.querySelector('[name="multiOffer"]').checked
    });
    location.href = form.getAttribute('data-next');
  });
}

function setupModal(){
  const overlay = document.querySelector('[data-modal]');
  if(!overlay) return;
  const closeBtns = overlay.querySelectorAll('[data-close-modal]');
  closeBtns.forEach(btn => btn.addEventListener('click', () => overlay.remove()));
}

function setupBillingChoices(){
  document.querySelectorAll('[data-billing-choice]').forEach(choice => {
    choice.addEventListener('click', () => {
      document.querySelectorAll('[data-billing-choice]').forEach(c => c.classList.remove('is-selected'));
      choice.classList.add('is-selected');
      const input = document.querySelector('[name="billingMethod"]');
      if(input) input.value = choice.getAttribute('data-billing-choice');
    });
  });
}

function setupDismissNotice(){
  const banner = document.querySelector('[data-dismissible-notice]');
  const btn = document.querySelector('[data-dismiss-notice]');
  if(!banner || !btn) return;
  if(localStorage.getItem(HH.noticeKey) === '1') banner.style.display = 'none';
  btn.addEventListener('click', () => {
    localStorage.setItem(HH.noticeKey, '1');
    banner.style.display = 'none';
  });
}

function setupProjectActions(){
  document.querySelectorAll('[data-reset-workspace]').forEach(btn => {
    btn.addEventListener('click', () => {
      localStorage.removeItem(HH.key);
      localStorage.removeItem(HH.inviteKey);
      localStorage.removeItem(HH.noticeKey);
      location.href = btn.getAttribute('href') || '../app/dashboard.html';
    });
  });
}


function setupHireFlow(){
  const form = document.querySelector('[data-hire-flow-form]');
  const saveNote = document.querySelector('[data-save-note]');
  if(form){
    form.addEventListener('submit', e => {
      e.preventDefault();
      if(!saveBriefFromForm(form)) return;
      if(saveNote){
        saveNote.classList.add('show');
        saveNote.textContent = 'Brief saved';
        setTimeout(() => saveNote.classList.remove('show'), 1800);
      }
      populateFromBrief();
    });
  }
  document.querySelectorAll('[data-pick-freelancer]').forEach(link => {
    link.addEventListener('click', e => {
      if(form && !saveBriefFromForm(form)){
        e.preventDefault();
        return;
      }
      saveInvite({
        freelancer: link.getAttribute('data-freelancer') || getInvite().freelancer,
        role: link.getAttribute('data-role') || getInvite().role,
        rate: link.getAttribute('data-rate') || getInvite().rate,
        weekly: link.getAttribute('data-weekly') || getInvite().weekly,
        manual: getInvite().manual,
        multiOffer: false
      });
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  setupMenu();
  setupWizardForms();
  setupSelectableCards();
  setupInviteForm();
  setupModal();
  setupBillingChoices();
  setupDismissNotice();
  setupProjectActions();
  setupHireFlow();
  populateFromBrief();
  populateInvite();
});
