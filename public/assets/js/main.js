(() => {
  const doc = document;
  doc.body.classList.add('js-ready');

  const yearTarget = doc.querySelector('[data-current-year]');
  if (yearTarget) yearTarget.textContent = new Date().getFullYear();

  const menuToggle = doc.querySelector('[data-menu-toggle]');
  const mobileMenu = doc.querySelector('[data-mobile-menu]');
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', String(!expanded));
      mobileMenu.classList.toggle('is-open', !expanded);
    });
    mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
      menuToggle.setAttribute('aria-expanded', 'false');
      mobileMenu.classList.remove('is-open');
    }));
  }

  const revealItems = [...doc.querySelectorAll('[data-reveal]')];
  if ('IntersectionObserver' in window && revealItems.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealItems.forEach(item => observer.observe(item));
  } else {
    revealItems.forEach(item => item.classList.add('revealed'));
  }

  const accordions = doc.querySelectorAll('[data-accordion]');
  accordions.forEach(item => {
    const button = item.querySelector('.faq-button');
    if (!button) return;
    button.addEventListener('click', () => {
      const open = item.classList.toggle('is-open');
      button.setAttribute('aria-expanded', String(open));
    });
  });

  const toast = doc.querySelector('[data-toast]');
  let toastTimer;
  const showToast = (message) => {
    if (!toast || !message) return;
    toast.textContent = message;
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2800);
  };

  if (toast?.dataset?.flashToast) {
    showToast(toast.dataset.flashToast);
  }

  const searchInput = doc.querySelector('[data-help-search]');
  const helpItems = [...doc.querySelectorAll('[data-help-item]')];
  const helpEmpty = doc.querySelector('[data-help-empty]');
  if (searchInput && helpItems.length) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.trim().toLowerCase();
      let visible = 0;
      helpItems.forEach(item => {
        const haystack = [item.dataset.title, item.dataset.category, item.dataset.text].join(' ');
        const match = !q || haystack.includes(q);
        item.hidden = !match;
        if (match) visible += 1;
      });
      if (helpEmpty) helpEmpty.hidden = visible !== 0;
    });
  }

  const contactForm = doc.querySelector('[data-contact-form]');
  if (contactForm) {
    contactForm.addEventListener('submit', (event) => {
      const emailInput = contactForm.querySelector('input[type="email"]');
      if (emailInput && emailInput.value && !emailInput.validity.valid) {
        emailInput.setCustomValidity('Please enter a valid work email.');
      } else if (emailInput) {
        emailInput.setCustomValidity('');
      }
      const messageInput = contactForm.querySelector('textarea[name="message"]');
      if (messageInput && messageInput.value.trim().length < 20) {
        messageInput.setCustomValidity('Please include a little more detail in your message.');
      } else if (messageInput) {
        messageInput.setCustomValidity('');
      }
      if (!contactForm.reportValidity()) {
        event.preventDefault();
      }
    });
  }

  const inquiryForm = doc.querySelector('[data-inquiry-form]');
  const inquiryStorageKey = 'hirehelper_public_inquiry';
  if (inquiryForm) {
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category');
    if (category) {
      const categorySelect = inquiryForm.querySelector('#category');
      if (categorySelect) {
        [...categorySelect.options].forEach(option => {
          if (option.value === category || option.textContent === category) {
            categorySelect.value = option.value || option.textContent;
          }
        });
      }
    }

    inquiryForm.addEventListener('submit', (event) => {
      const emailInput = inquiryForm.querySelector('input[type="email"]');
      if (emailInput && emailInput.value && !emailInput.validity.valid) {
        emailInput.setCustomValidity('Please enter a valid work email.');
      } else if (emailInput) {
        emailInput.setCustomValidity('');
      }
      const detailsInput = inquiryForm.querySelector('textarea[name="needs"]');
      if (detailsInput && detailsInput.value.trim().length < 40) {
        detailsInput.setCustomValidity('Please add enough detail for us to understand the project.');
      } else if (detailsInput) {
        detailsInput.setCustomValidity('');
      }

      if (!inquiryForm.reportValidity()) {
        event.preventDefault();
        return;
      }

      const data = Object.fromEntries(new FormData(inquiryForm).entries());
      window.localStorage.setItem(inquiryStorageKey, JSON.stringify(data));
    });
  }

  const requestSummary = doc.querySelector('[data-request-summary]');
  if (requestSummary) {
    try {
      const saved = window.localStorage.getItem(inquiryStorageKey);
      if (saved) {
        const data = JSON.parse(saved);
        const map = {
          category: data.category,
          timeline: data.timeline,
          budget: data.budget,
          email: data.email,
        };
        Object.entries(map).forEach(([key, value]) => {
          const target = doc.querySelector(`[data-summary-field="${key}"]`);
          if (target && value) target.textContent = value;
        });
        requestSummary.hidden = false;
      }
    } catch (error) {
      console.error(error);
    }
  }
})();
