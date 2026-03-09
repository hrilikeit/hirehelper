const HHWorkspace = {
  noticeKey: 'hirehelper_notice_closed_v3',
};

function setupMenu() {
  const btn = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');
  if (!btn || !menu) return;

  btn.addEventListener('click', () => menu.classList.toggle('open'));

  document.addEventListener('click', (event) => {
    if (!menu.classList.contains('open')) return;
    if (menu.contains(event.target) || btn.contains(event.target)) return;
    menu.classList.remove('open');
  });
}

function setupDismissNotice() {
  const banner = document.querySelector('[data-dismissible-notice]');
  const button = document.querySelector('[data-dismiss-notice]');
  if (!banner || !button) return;

  if (localStorage.getItem(HHWorkspace.noticeKey) === '1') {
    banner.style.display = 'none';
  }

  button.addEventListener('click', () => {
    localStorage.setItem(HHWorkspace.noticeKey, '1');
    banner.style.display = 'none';
  });
}

function updateWeeklyMax() {
  const rate = document.querySelector('#hourly_rate');
  const weekly = document.querySelector('#weekly_limit');
  const output = document.querySelector('[data-weekly-max]');
  if (!rate || !weekly || !output) return;

  const total = (Number(rate.value || 0) * Number(weekly.value || 0)).toFixed(2);
  output.textContent = `$${total} max / week`;
}

function setupInviteForm() {
  const freelancerSelect = document.querySelector('[data-freelancer-select]');
  const roleInput = document.querySelector('#role');
  const rateInput = document.querySelector('#hourly_rate');

  updateWeeklyMax();

  if (rateInput) {
    rateInput.addEventListener('input', updateWeeklyMax);
  }

  const weeklyLimit = document.querySelector('#weekly_limit');
  if (weeklyLimit) {
    weeklyLimit.addEventListener('input', updateWeeklyMax);
  }

  if (!freelancerSelect) return;

  freelancerSelect.addEventListener('change', () => {
    const option = freelancerSelect.options[freelancerSelect.selectedIndex];
    if (!option) return;

    if (roleInput && (!roleInput.value || roleInput.dataset.autofill !== '0')) {
      roleInput.value = option.dataset.role || roleInput.value;
    }

    if (rateInput && (!rateInput.value || rateInput.dataset.autofill !== '0')) {
      rateInput.value = option.dataset.rate || rateInput.value;
      updateWeeklyMax();
    }
  });
}

function setupBillingChoices() {
  const hidden = document.querySelector('[data-billing-input]');
  const choices = document.querySelectorAll('[data-billing-choice]');
  if (!hidden || !choices.length) return;

  choices.forEach((choice) => {
    choice.addEventListener('click', () => {
      choices.forEach((row) => row.classList.remove('is-selected'));
      choice.classList.add('is-selected');
      hidden.value = choice.getAttribute('data-billing-choice') || hidden.value;
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  setupMenu();
  setupDismissNotice();
  setupInviteForm();
  setupBillingChoices();
});
