/**
 * PaytoConnect - Unified Data Store & LocalStorage Manager
 * Handles seed data initialization, state persistence, and CRUD operations.
 */

const Store = (function() {
  const KEYS = {
    USERS: 'paytoconnect_users',
    RESTAURANTS: 'paytoconnect_restaurants',
    CUSTOMERS: 'paytoconnect_customers',
    SESSION: 'paytoconnect_session',
    SETTINGS: 'paytoconnect_settings'
  };

  const DEFAULT_RESTAURANTS = [
    {
      id: 'rest_1',
      name: 'Chaudhary Cafe & Family Restaurant',
      slug: 'chaudhary',
      address: 'Gadakul, Shohratgarh, Siddharthnagar',
      phone: '+91 91374 70661',
      upi_id: '9137470661@superyes',
      logo: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=150&auto=format&fit=crop&q=80',
      description: 'Authentic Indian dining, gourmet snacks, fresh brews, and family ambiance.',
      status: 'active',
      customers_count: 1248,
      created_at: '2026-01-15'
    },
    {
      id: 'rest_2',
      name: 'Royal Cafe & Bakers',
      slug: 'royal',
      address: 'Civil Lines, Station Road, Siddharthnagar',
      phone: '+91 98765 43210',
      upi_id: 'royalbakers@icici',
      logo: 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=150&auto=format&fit=crop&q=80',
      description: 'Artisanal sourdoughs, specialty coffee, and handcrafted Italian pastries.',
      status: 'active',
      customers_count: 840,
      created_at: '2026-02-10'
    },
    {
      id: 'rest_3',
      name: 'Spice Route Express',
      slug: 'spiceroute',
      address: 'NH-28 Bypass Highway Food Court',
      phone: '+91 94150 99881',
      upi_id: 'spiceroute@okhdfcbank',
      logo: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=150&auto=format&fit=crop&q=80',
      description: 'Quick-service highway dhaba serving hot tandoori, curries, and traveler refreshments.',
      status: 'active',
      customers_count: 512,
      created_at: '2026-03-01'
    }
  ];

  const DEFAULT_CUSTOMERS = [
    { id: 'c_1', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Rahul Verma', mobile: '9876543210', customer_type: 'Local', bill_amount: 480, marketing_consent: true, date: 'Today', time: '7:42 PM', timestamp: Date.now() - 1000 * 60 * 45 },
    { id: 'c_2', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Priya Sharma', mobile: '9123456789', customer_type: 'Outsider', bill_amount: 1250, marketing_consent: true, date: 'Today', time: '6:15 PM', timestamp: Date.now() - 1000 * 60 * 130 },
    { id: 'c_3', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Amit Chaudhary', mobile: '9839012345', customer_type: 'Local', bill_amount: 320, marketing_consent: false, date: 'Today', time: '5:30 PM', timestamp: Date.now() - 1000 * 60 * 180 },
    { id: 'c_4', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Sunil Kumar', mobile: '9450123456', customer_type: 'Local', bill_amount: 850, marketing_consent: true, date: 'Today', time: '4:10 PM', timestamp: Date.now() - 1000 * 60 * 250 },
    { id: 'c_5', restaurant_slug: 'royal', restaurant_name: 'Royal Cafe', name: 'Neha Gupta', mobile: '9988776655', customer_type: 'Outsider', bill_amount: 640, marketing_consent: true, date: 'Today', time: '3:45 PM', timestamp: Date.now() - 1000 * 60 * 300 },
    { id: 'c_6', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Vikas Tiwari', mobile: '9792011223', customer_type: 'Local', bill_amount: 210, marketing_consent: true, date: 'Today', time: '2:15 PM', timestamp: Date.now() - 1000 * 60 * 390 },
    { id: 'c_7', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Rajesh Mishra', mobile: '9415887744', customer_type: 'Outsider', bill_amount: 1450, marketing_consent: false, date: 'Yesterday', time: '9:10 PM', timestamp: Date.now() - 1000 * 60 * 60 * 24 },
    { id: 'c_8', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Anjali Srivastava', mobile: '9838112299', customer_type: 'Local', bill_amount: 520, marketing_consent: true, date: 'Yesterday', time: '8:05 PM', timestamp: Date.now() - 1000 * 60 * 60 * 25 },
    { id: 'c_9', restaurant_slug: 'royal', restaurant_name: 'Royal Cafe', name: 'Mohit Pandey', mobile: '9161223344', customer_type: 'Local', bill_amount: 920, marketing_consent: true, date: 'Yesterday', time: '7:30 PM', timestamp: Date.now() - 1000 * 60 * 60 * 26 },
    { id: 'c_10', restaurant_slug: 'spiceroute', restaurant_name: 'Spice Route Express', name: 'Harish Chandra', mobile: '9889556677', customer_type: 'Outsider', bill_amount: 1800, marketing_consent: true, date: 'Yesterday', time: '6:50 PM', timestamp: Date.now() - 1000 * 60 * 60 * 27 },
    { id: 'c_11', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Pooja Singh', mobile: '9628334455', customer_type: 'Local', bill_amount: 390, marketing_consent: true, date: '2 days ago', time: '8:40 PM', timestamp: Date.now() - 1000 * 60 * 60 * 48 },
    { id: 'c_12', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Deepak Yadav', mobile: '9795443322', customer_type: 'Local', bill_amount: 760, marketing_consent: false, date: '2 days ago', time: '7:15 PM', timestamp: Date.now() - 1000 * 60 * 60 * 49 },
    { id: 'c_13', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Kavita Patel', mobile: '9554112233', customer_type: 'Outsider', bill_amount: 1100, marketing_consent: true, date: '3 days ago', time: '9:00 PM', timestamp: Date.now() - 1000 * 60 * 60 * 72 },
    { id: 'c_14', restaurant_slug: 'royal', restaurant_name: 'Royal Cafe', name: 'Sanjay Jaiswal', mobile: '9919887766', customer_type: 'Local', bill_amount: 450, marketing_consent: true, date: '3 days ago', time: '6:30 PM', timestamp: Date.now() - 1000 * 60 * 60 * 75 },
    { id: 'c_15', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Manoj Shukla', mobile: '9451224466', customer_type: 'Local', bill_amount: 620, marketing_consent: true, date: '4 days ago', time: '8:10 PM', timestamp: Date.now() - 1000 * 60 * 60 * 96 },
    { id: 'c_16', restaurant_slug: 'spiceroute', restaurant_name: 'Spice Route Express', name: 'Arun Kumar', mobile: '9839445566', customer_type: 'Outsider', bill_amount: 2200, marketing_consent: true, date: '4 days ago', time: '5:20 PM', timestamp: Date.now() - 1000 * 60 * 60 * 99 },
    { id: 'c_17', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Shweta Dixit', mobile: '9125667788', customer_type: 'Local', bill_amount: 340, marketing_consent: true, date: '5 days ago', time: '7:40 PM', timestamp: Date.now() - 1000 * 60 * 60 * 120 },
    { id: 'c_18', restaurant_slug: 'chaudhary', restaurant_name: 'Chaudhary Cafe', name: 'Gaurav Pandey', mobile: '9721443311', customer_type: 'Outsider', bill_amount: 980, marketing_consent: false, date: '5 days ago', time: '6:10 PM', timestamp: Date.now() - 1000 * 60 * 60 * 122 }
  ];

  const DEFAULT_USERS = [
    { id: 'u_admin', username: 'admin', email: 'admin@paytoconnect.com', name: 'Admin', role: 'admin', title: 'Administrator' },
    { id: 'u_owner', username: 'chaudhary', email: 'chaudhary@paytoconnect.com', name: 'Chaudhary Cafe', role: 'restaurant', restaurant_slug: 'chaudhary', title: 'Owner' }
  ];

  const DEFAULT_SETTINGS = {
    platform_name: 'PaytoConnect',
    tagline: 'Scan. Pay. Connect.',
    org: 'ALAG INNOVATIVE SOLUTIONS',
    lead: 'Abhishek Chaudhary',
    notifications: true,
    upi_intent_enabled: true
  };

  function init() {
    if (!localStorage.getItem(KEYS.RESTAURANTS)) {
      localStorage.setItem(KEYS.RESTAURANTS, JSON.stringify(DEFAULT_RESTAURANTS));
    }
    if (!localStorage.getItem(KEYS.CUSTOMERS)) {
      localStorage.setItem(KEYS.CUSTOMERS, JSON.stringify(DEFAULT_CUSTOMERS));
    }
    if (!localStorage.getItem(KEYS.USERS)) {
      localStorage.setItem(KEYS.USERS, JSON.stringify(DEFAULT_USERS));
    }
    if (!localStorage.getItem(KEYS.SETTINGS)) {
      localStorage.setItem(KEYS.SETTINGS, JSON.stringify(DEFAULT_SETTINGS));
    }
  }

  // Auto-run init
  init();

  return {
    // Restaurants
    getRestaurants: function() {
      try {
        return JSON.parse(localStorage.getItem(KEYS.RESTAURANTS)) || DEFAULT_RESTAURANTS;
      } catch (e) {
        return DEFAULT_RESTAURANTS;
      }
    },

    getRestaurantBySlug: function(slug) {
      const all = this.getRestaurants();
      return all.find(r => r.slug.toLowerCase() === (slug || '').toLowerCase()) || all[0];
    },

    saveRestaurant: function(restaurant) {
      const all = this.getRestaurants();
      const existingIdx = all.findIndex(r => r.id === restaurant.id || r.slug === restaurant.slug);
      if (existingIdx >= 0) {
        all[existingIdx] = { ...all[existingIdx], ...restaurant };
      } else {
        restaurant.id = 'rest_' + Date.now();
        restaurant.customers_count = restaurant.customers_count || 0;
        restaurant.created_at = restaurant.created_at || new Date().toISOString().split('T')[0];
        restaurant.status = restaurant.status || 'active';
        all.push(restaurant);
      }
      localStorage.setItem(KEYS.RESTAURANTS, JSON.stringify(all));
      return restaurant;
    },

    deleteRestaurant: function(id) {
      let all = this.getRestaurants();
      all = all.filter(r => r.id !== id);
      localStorage.setItem(KEYS.RESTAURANTS, JSON.stringify(all));
      return true;
    },

    // Customers
    getCustomers: function(filterRestaurantSlug) {
      let list = [];
      try {
        list = JSON.parse(localStorage.getItem(KEYS.CUSTOMERS)) || DEFAULT_CUSTOMERS;
      } catch (e) {
        list = DEFAULT_CUSTOMERS;
      }
      if (filterRestaurantSlug) {
        return list.filter(c => c.restaurant_slug === filterRestaurantSlug);
      }
      return list;
    },

    saveCustomer: function(customerData) {
      const list = this.getCustomers();
      const now = new Date();
      const newCustomer = {
        id: 'c_' + Date.now(),
        restaurant_slug: customerData.restaurant_slug || 'chaudhary',
        restaurant_name: customerData.restaurant_name || 'Chaudhary Cafe',
        name: customerData.name.trim(),
        mobile: customerData.mobile.replace(/\D/g, '').slice(-10),
        customer_type: customerData.customer_type || 'Local',
        bill_amount: parseFloat(customerData.bill_amount) || 0,
        marketing_consent: !!customerData.marketing_consent,
        date: 'Today',
        time: now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        timestamp: now.getTime()
      };
      list.unshift(newCustomer);
      localStorage.setItem(KEYS.CUSTOMERS, JSON.stringify(list));

      // Increment restaurant customer count
      const rests = this.getRestaurants();
      const r = rests.find(x => x.slug === newCustomer.restaurant_slug);
      if (r) {
        r.customers_count = (r.customers_count || 0) + 1;
        localStorage.setItem(KEYS.RESTAURANTS, JSON.stringify(rests));
      }
      return newCustomer;
    },

    // Session Management
    getSession: function() {
      try {
        const sess = JSON.parse(localStorage.getItem(KEYS.SESSION));
        if (sess) return sess;
      } catch (e) {}
      // Default to admin or null
      return null;
    },

    setSession: function(userObj) {
      localStorage.setItem(KEYS.SESSION, JSON.stringify(userObj));
    },

    clearSession: function() {
      localStorage.removeItem(KEYS.SESSION);
    },

    // Platform Settings
    getSettings: function() {
      try {
        return JSON.parse(localStorage.getItem(KEYS.SETTINGS)) || DEFAULT_SETTINGS;
      } catch (e) {
        return DEFAULT_SETTINGS;
      }
    },

    saveSettings: function(settings) {
      const current = this.getSettings();
      const updated = { ...current, ...settings };
      localStorage.setItem(KEYS.SETTINGS, JSON.stringify(updated));
      return updated;
    },

    // Reset to initial demo data
    resetDemoData: function() {
      localStorage.setItem(KEYS.RESTAURANTS, JSON.stringify(DEFAULT_RESTAURANTS));
      localStorage.setItem(KEYS.CUSTOMERS, JSON.stringify(DEFAULT_CUSTOMERS));
      localStorage.setItem(KEYS.USERS, JSON.stringify(DEFAULT_USERS));
      localStorage.setItem(KEYS.SETTINGS, JSON.stringify(DEFAULT_SETTINGS));
      return true;
    }
  };
})();
