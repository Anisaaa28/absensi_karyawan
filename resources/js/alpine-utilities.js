// Alpine.js Global Utilities
window.AlpineUtilities = {
  // Notification system
  notifications: [],
  notificationId: 0,

  notify(message, type = 'success', duration = 3000) {
    if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
      new Notification('Absensi Karyawan', { body: message });
    }

    this.notificationId++;
    const id = this.notificationId;
    const notification = { id, message, type, visible: true };
    
    this.notifications.push(notification);
    
    setTimeout(() => {
      const index = this.notifications.findIndex(n => n.id === id);
      if (index > -1) {
        this.notifications[index].visible = false;
        setTimeout(() => {
          this.notifications.splice(index, 1);
        }, 300);
      }
    }, duration);
    
    return id;
  },

  success(message, duration = 3000) {
    return this.notify(message, 'success', duration);
  },

  error(message, duration = 4000) {
    return this.notify(message, 'error', duration);
  },

  warning(message, duration = 3500) {
    return this.notify(message, 'warning', duration);
  },

  info(message, duration = 3000) {
    return this.notify(message, 'info', duration);
  },

  // Confirmation dialog
  confirmDialog(title, message) {
    return new Promise(resolve => {
      const dialog = document.getElementById('nativeConfirmDialog');
      const titleElement = document.getElementById('nativeConfirmTitle');
      const messageElement = document.getElementById('nativeConfirmMessage');

      if (!dialog || typeof dialog.showModal !== 'function') {
        resolve(window.confirm(message));
        return;
      }

      titleElement.textContent = title;
      messageElement.textContent = message;
      dialog.showModal();

      const onClose = () => {
        resolve(dialog.returnValue === 'confirm');
        dialog.removeEventListener('close', onClose);
      };

      dialog.addEventListener('close', onClose);
    });
  },

  // API fetch with error handling
  async fetchJSON(url, options = {}) {
    try {
      const response = await fetch(url, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          ...options.headers,
        },
        ...options,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error('Fetch error:', error);
      this.error('Terjadi kesalahan jaringan');
      throw error;
    }
  },

  // Format currency
  formatCurrency(value, currency = 'IDR') {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 0,
    }).format(value);
  },

  // Format date
  formatDate(date, locale = 'id-ID') {
    return new Intl.DateTimeFormat(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }).format(new Date(date));
  },

  // Format time
  formatTime(date, locale = 'id-ID') {
    return new Intl.DateTimeFormat(locale, {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    }).format(new Date(date));
  },

  // Get initials from name
  getInitials(name) {
    return name
      .split(' ')
      .map(n => n[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  },

  // Check if element is in viewport
  isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  },

  // Debounce function
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  // Throttle function
  throttle(func, wait) {
    let timeout;
    let previous = 0;
    return function(...args) {
      const now = Date.now();
      if (now - previous > wait) {
        func(...args);
        previous = now;
      }
    };
  },

  // Copy to clipboard
  async copyToClipboard(text) {
    try {
      await navigator.clipboard.writeText(text);
      this.success('Berhasil disalin ke clipboard');
    } catch (error) {
      console.error('Copy error:', error);
      this.error('Gagal menyalin ke clipboard');
    }
  },

  // Download file
  downloadFile(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename || 'download';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  },

  // Parse query parameters
  getQueryParam(param) {
    const params = new URLSearchParams(window.location.search);
    return params.get(param);
  },

  // Redirect with delay
  redirect(url, delay = 0) {
    setTimeout(() => {
      window.location.href = url;
    }, delay);
  },

  // Animate counter values for KPI cards
  animateValue(element, start, end, duration = 900, formatter = value => value) {
    if (!element) {
      return;
    }

    const startTime = performance.now();

    const tick = currentTime => {
      const progress = Math.min((currentTime - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const currentValue = Math.round(start + ((end - start) * eased));
      element.textContent = formatter(currentValue);

      if (progress < 1) {
        requestAnimationFrame(tick);
      }
    };

    requestAnimationFrame(tick);
  },

  // Tilt effect for premium cards
  attachTilt(selector = '[data-tilt-card]') {
    document.querySelectorAll(selector).forEach(card => {
      const onMove = event => {
        const rect = card.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        const rotateX = ((y / rect.height) - 0.5) * -6;
        const rotateY = ((x / rect.width) - 0.5) * 8;
        card.style.transform = `perspective(1100px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
      };

      const reset = () => {
        card.style.transform = '';
      };

      card.addEventListener('mousemove', onMove);
      card.addEventListener('mouseleave', reset);
    });
  },

  // Lightweight reveal animation using intersection observer
  attachReveal(selector = '[data-reveal]') {
    const elements = document.querySelectorAll(selector);
    if (!elements.length || !('IntersectionObserver' in window)) {
      return;
    }

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-fade-up');
          entry.target.classList.remove('opacity-0', 'translate-y-4');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.16 });

    elements.forEach(element => observer.observe(element));
  },

  attachConfirmables(selector = '[data-confirm]') {
    document.querySelectorAll(selector).forEach(element => {
      if (element.dataset.confirmBound === 'true') {
        return;
      }

      element.dataset.confirmBound = 'true';
      element.addEventListener('click', async event => {
        if (event.currentTarget.dataset.confirmSkip === 'true') {
          event.currentTarget.dataset.confirmSkip = 'false';
          return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const target = event.currentTarget;
        const title = target.dataset.confirmTitle || 'Konfirmasi tindakan';
        const message = target.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan?';
        const confirmed = await this.confirmDialog(title, message);

        if (!confirmed) {
          return;
        }

        if (target.tagName === 'A' && target.href) {
          window.location.href = target.href;
          return;
        }

        const form = target.closest('form');
        if (form) {
          if (typeof form.requestSubmit === 'function') {
            target.dataset.confirmSkip = 'true';
            form.requestSubmit(target);
          } else {
            form.submit();
          }
        }
      });
    });
  },
};

// Export for use in components
window.utils = window.AlpineUtilities;
