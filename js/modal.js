/**
 * PaytoConnect - Modal & Dialog Management System
 * Handles Add/Edit Restaurant Modal, Customer Detail Modal, and Delete Confirmation Modal.
 */

const Modal = (function() {
  let activeModal = null;

  function closeModal() {
    if (activeModal) {
      activeModal.classList.add('opacity-0');
      const dialog = activeModal.querySelector('.modal-box');
      if (dialog) dialog.classList.add('scale-95');
      setTimeout(() => {
        if (activeModal && activeModal.parentElement) {
          activeModal.parentElement.removeChild(activeModal);
        }
        activeModal = null;
        document.body.classList.remove('overflow-hidden');
      }, 200);
    }
  }

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
  });

  function createBaseModal(title, contentHtml) {
    closeModal();
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200 opacity-0';
    overlay.id = 'dynamic-modal';

    overlay.innerHTML = `
      <div class="modal-box bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-200 border border-slate-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <h3 class="text-lg font-bold text-slate-900">${title}</h3>
          <button type="button" class="close-modal-btn text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Close modal">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div class="p-6">
          ${contentHtml}
        </div>
      </div>
    `;

    overlay.querySelector('.close-modal-btn').addEventListener('click', closeModal);
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal();
    });

    document.body.appendChild(overlay);
    document.body.classList.add('overflow-hidden');
    activeModal = overlay;

    requestAnimationFrame(() => {
      overlay.classList.remove('opacity-0');
      overlay.querySelector('.modal-box').classList.remove('scale-95');
    });

    return overlay;
  }

  return {
    close: closeModal,

    // Requirement 29: Add / Edit Restaurant Modal
    openRestaurantModal: function(existingData = null, onSaved) {
      const isEdit = !!existingData;
      const title = isEdit ? 'Edit Restaurant' : 'Add New Restaurant';
      const r = existingData || {
        id: '',
        name: '',
        slug: '',
        address: '',
        phone: '',
        upi_id: '',
        logo: '',
        description: ''
      };

      const html = `
        <form id="restaurant-form" class="space-y-4">
          <input type="hidden" id="modal-rest-id" value="${r.id || ''}">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Restaurant Name *</label>
            <input type="text" id="modal-rest-name" required value="${r.name}" placeholder="e.g. Chaudhary Cafe" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Slug (URL Identifier) *</label>
            <div class="flex items-center">
              <span class="px-3 py-2.5 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-xs font-semibold text-slate-500">scan.html?restaurant=</span>
              <input type="text" id="modal-rest-slug" required value="${r.slug}" placeholder="chaudhary" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-r-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Unique alphanumeric string used for customer QR links.</p>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">UPI ID *</label>
              <input type="text" id="modal-rest-upi" required value="${r.upi_id}" placeholder="merchant@upi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone Number *</label>
              <input type="text" id="modal-rest-phone" required value="${r.phone}" placeholder="+91 91374 70661" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Physical Address *</label>
            <input type="text" id="modal-rest-address" required value="${r.address}" placeholder="Street, City, State" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Logo Image URL</label>
            <input type="url" id="modal-rest-logo" value="${r.logo}" placeholder="https://..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Description / Tagline</label>
            <textarea id="modal-rest-desc" rows="2" placeholder="Brief description of cuisine or outlet ambiance" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">${r.description || ''}</textarea>
          </div>
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" class="btn-cancel px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold text-white shadow-md shadow-indigo-200 transition-colors">${isEdit ? 'Update Restaurant' : 'Create Restaurant'}</button>
          </div>
        </form>
      `;

      const modal = createBaseModal(title, html);

      // Auto slugify name if creating
      if (!isEdit) {
        const nameInput = modal.querySelector('#modal-rest-name');
        const slugInput = modal.querySelector('#modal-rest-slug');
        nameInput.addEventListener('input', () => {
          slugInput.value = nameInput.value.toLowerCase().trim().replace(/[^a-z0-9]/g, '');
        });
      }

      modal.querySelector('.btn-cancel').addEventListener('click', closeModal);

      modal.querySelector('#restaurant-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const name = modal.querySelector('#modal-rest-name').value.trim();
        const slug = modal.querySelector('#modal-rest-slug').value.trim().toLowerCase();
        const upi = modal.querySelector('#modal-rest-upi').value.trim();
        const phone = modal.querySelector('#modal-rest-phone').value.trim();
        const address = modal.querySelector('#modal-rest-address').value.trim();
        const logo = modal.querySelector('#modal-rest-logo').value.trim() || 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=150&auto=format&fit=crop&q=80';
        const desc = modal.querySelector('#modal-rest-desc').value.trim();
        const id = modal.querySelector('#modal-rest-id').value;

        if (!name || !slug || !upi || !phone || !address) {
          Toast.error('Please fill in all required fields.');
          return;
        }

        if (!upi.includes('@')) {
          Toast.error('Please enter a valid UPI ID (e.g. name@bank).');
          return;
        }

        const saved = Store.saveRestaurant({
          id: id || undefined,
          name: name,
          slug: slug,
          upi_id: upi,
          phone: phone,
          address: address,
          logo: logo,
          description: desc,
          status: 'active'
        });

        Toast.success(isEdit ? 'Restaurant updated successfully.' : 'Restaurant created successfully.');
        closeModal();
        if (typeof onSaved === 'function') onSaved(saved);
      });
    },

    // Requirement 36: Customer Detail Modal
    openCustomerModal: function(customer) {
      if (!customer) return;
      const html = `
        <div class="space-y-4">
          <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">
              ${customer.name.charAt(0).toUpperCase()}
            </div>
            <div>
              <h4 class="text-base font-bold text-slate-900">${customer.name}</h4>
              <p class="text-xs text-slate-500 font-medium">Customer ID: #${customer.id}</p>
            </div>
            <div class="ml-auto">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${customer.customer_type === 'Local' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200'}">
                ${customer.customer_type}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="p-3 bg-white border border-slate-200 rounded-xl">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Phone Number</span>
              <span class="text-sm font-bold text-slate-800 mt-0.5 block">+91 ${customer.mobile}</span>
            </div>
            <div class="p-3 bg-white border border-slate-200 rounded-xl">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Bill Amount</span>
              <span class="text-sm font-bold text-emerald-600 mt-0.5 block">₹${Number(customer.bill_amount).toLocaleString('en-IN')}</span>
            </div>
            <div class="p-3 bg-white border border-slate-200 rounded-xl">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Restaurant Outlet</span>
              <span class="text-sm font-semibold text-slate-800 mt-0.5 block">${customer.restaurant_name || customer.restaurant_slug}</span>
            </div>
            <div class="p-3 bg-white border border-slate-200 rounded-xl">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Check-in Time</span>
              <span class="text-sm font-semibold text-slate-800 mt-0.5 block">${customer.date}, ${customer.time}</span>
            </div>
          </div>

          <div class="p-4 rounded-xl border ${customer.marketing_consent ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200'}">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 ${customer.marketing_consent ? 'text-emerald-600' : 'text-slate-400'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${customer.marketing_consent ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}" />
              </svg>
              <span class="text-xs font-bold ${customer.marketing_consent ? 'text-emerald-800' : 'text-slate-600'}">
                ${customer.marketing_consent ? 'Marketing & WhatsApp Consent Granted' : 'No Marketing Consent Provided'}
              </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1 pl-7">
              ${customer.marketing_consent ? 'The diner agreed to receive festive updates and discounts from this restaurant.' : 'Customer opted out of promotional communications.'}
            </p>
          </div>

          <div class="pt-2 flex justify-end">
            <button type="button" class="close-btn px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
              Close Details
            </button>
          </div>
        </div>
      `;

      const modal = createBaseModal('Customer Details', html);
      modal.querySelector('.close-btn').addEventListener('click', closeModal);
    },

    // Requirement 44: Delete Confirmation Dialog
    confirmDelete: function(restaurantName, onConfirm) {
      const html = `
        <div class="space-y-4 text-center">
          <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          </div>
          <div>
            <h4 class="text-lg font-bold text-slate-900">Delete Restaurant?</h4>
            <p class="text-xs text-slate-500 mt-1">This action will remove <strong class="text-slate-700">${restaurantName}</strong> from the demo system.</p>
          </div>
          <div class="flex items-center justify-center gap-3 pt-3">
            <button type="button" class="btn-cancel px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button type="button" class="btn-delete px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-xs font-semibold text-white shadow-md shadow-rose-200 transition-colors">Delete Restaurant</button>
          </div>
        </div>
      `;

      const modal = createBaseModal('', html);
      modal.querySelector('.btn-cancel').addEventListener('click', closeModal);
      modal.querySelector('.btn-delete').addEventListener('click', () => {
        closeModal();
        if (typeof onConfirm === 'function') onConfirm();
      });
    }
  };
})();
