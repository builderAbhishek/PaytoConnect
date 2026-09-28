# PaytoConnect &bull; Scan. Pay. Connect.
### Commercial Frontend SaaS Platform & CRM
**Created by:** **ALAG INNOVATIVE SOLUTIONS**  
**Lead & Product Architect:** **Abhishek Chaudhary**  
**Technology Stack:** HTML5 &bull; Tailwind CSS &bull; Vanilla JavaScript &bull; LocalStorage  

---

## 1. Product Overview

Traditional restaurant QR counter standees are silent: **Scan &rarr; Pay &rarr; Transaction Over**. The diner departs, and the restaurant loses 100% of customer contact data. The restaurant owner never learns who dined, whether they were a regular local neighbor or a one-time highway traveler, or how to re-engage them with weekend discounts and festive specials.

**PaytoConnect** eliminates this blind spot by inserting a frictionless 10-second check-in step before payment:

$$\text{Scan Counter QR} \longrightarrow \text{Quick Diner Check-in (Name, Phone, Demographic, Consent)} \longrightarrow \text{1-Tap UPI App Redirect} \longrightarrow \text{100\% Scoped Data Saved}$$

The diner pays through their existing UPI app (Google Pay, PhonePe, Paytm, BHIM, Cred) directly to the restaurant’s existing bank account via their UPI VPA with **0% intermediary transaction fees**. Meanwhile, the restaurant captures verified customer profiles, demographic insights (Local vs. Outsider), and marketing consent for compliant WhatsApp campaigns.

---

## 2. Architecture & File Structure

```
PaytoConnect/
├── index.html                 # Public commercial landing page with Hero, Features, Preview, and CTA
├── scan.html                  # Customer QR check-in & UPI intent launch (Name, Mobile, Demographic, Consent)
├── login.html                 # Unified role login (Master Admin & Restaurant Owner with 1-click demo fill)
├── components/
│   ├── sidebar.html           # Reusable modern sidebar (Admin & Restaurant roles, collapsible desktop, mobile drawer)
│   └── navbar.html            # Reusable navbar (Breadcrumb, Title, Quick Search, Notifications, User Profile)
├── admin/
│   ├── dashboard.html         # Admin Overview: 4 KPI stats, 3 Chart.js graphs, Date range selector
│   ├── restaurants.html       # Outlet management, Search, Status filter, Add/Edit modal, Delete confirmation
│   └── customers.html         # Global diner records, Stats, Demographics, WhatsApp consent, CSV export
├── restaurant/
│   ├── dashboard.html         # Restaurant Overview: Greeting, Today's metrics, 7-day activity chart, Recent diners
│   ├── customers.html         # Scoped customer CRM, Phone privacy mask, Filter chips, CSV export
│   ├── qr.html                # Large QR standee, Copy link, Download PNG, Test page, Printable acrylic view
│   ├── analytics.html         # In-depth analytics: 4 stats, 4 charts (Growth, Demographics, Activity, Consent)
│   └── settings.html          # Profile information, UPI ID, Slug, Customer page URL, Branding logo, Save Toast
├── js/
│   ├── store.js               # LocalStorage manager: seed demo data, restaurants, customers, settings, session
│   ├── app.js                 # Reusable layout controller: sidebar/navbar injection, collapse, mobile drawer, logout
│   ├── toast.js               # Reusable notification toasts: Success, Error, Warning, Info
│   └── modal.js               # Reusable modal system: Add/Edit restaurant, Customer detail, Delete confirmation
└── assets/
    └── css/
        └── custom.css         # Typography (Plus Jakarta Sans/Inter), custom scrollbar, skeleton loader, print styles
```

---

## 3. End-to-End User Experience Flows

### A. Public Diner Flow
1. **Diner Scans Counter QR:** Opens `scan.html?restaurant=chaudhary`.
2. **Restaurant Context Loaded:** Restaurant name, address, logo, and verified UPI ID are dynamically loaded.
3. **Data Input:**
   - Full Name
   - 10-digit Indian Mobile Number (validated `^[6-9]\d{9}$`)
   - Demographic Choice: Local Neighbor vs. Outsider/Traveler
   - Bill Amount (supports prefilled `?amt=450`)
   - WhatsApp Marketing Consent Checkbox
4. **Pre-Payment Commitment:** Saved to `paytoconnect_customers` in LocalStorage before payment launches.
5. **UPI Intent Handoff:**
   - Constructs standard UPI deep-link: `upi://pay?pa={UPI}&pn={NAME}&am={AMOUNT}&cu=INR&tn={NOTE}`.
   - Switches to handoff state: *"Details Saved! Opening UPI..."* (strictly adheres to the rule: **NEVER display "Payment Successful"** without a payment verification gateway).
   - Prompts diner to show the green confirmation screen in their UPI app to the counter cashier.

### B. Restaurant Owner Experience (`/restaurant/*`)
- **Dashboard:** Personalized greeting (*"Good Morning, Chaudhary Cafe"*), Today's check-ins (87), Local customers (52), Outsiders (35), Today's bill total (₹14,280), Lifetime customers (1,248), Marketing consent opt-ins (728), 7-day activity chart, and recent diner feed.
- **Customers CRM:** Scoped table & mobile cards with privacy phone mask (`98XXXXXX21`), search, demographic filters, customer detail modal, and CSV export.
- **My QR & Standee:** High-resolution printable acrylic standee card, copy link button, download QR PNG, and print CSS layout.
- **Analytics:** 4 stats and 4 Chart.js charts (Customer growth, Local vs outsider donut, daily hourly check-in distribution, and WhatsApp marketing consent rate).
- **Settings:** Outlet legal name, phone, physical location, settlement UPI ID, branding logo, customer page URL, and reset/save buttons with animated toast notification.

### C. Master Admin Experience (`/admin/*`)
- **Overview:** 4 macro KPIs (24 restaurants, 1,248 customers, 87 today, ₹24,580 today), date range selectors, 3 analytical charts (7-day customer activity, local vs outsider donut, customer growth line chart labeled "Demo Data").
- **Restaurants Management:** Search, status filter, table & mobile cards, Add Restaurant Modal with JavaScript validation, Edit Profile, and Delete Confirmation Modal.
- **Global Customers Directory:** Cross-outlet search, filters by restaurant, customer type, marketing consent, date, and global CSV export.

---

## 4. Reusable UI Components & Design System

- **Desktop Sidebar:** Fixed 260px width with PaytoConnect logo, role-based navigation groups, active state highlights, hover states, bottom user profile pill, and a desktop collapse toggle (collapses to 72px icon mode with hover tooltips).
- **Mobile Drawer:** Slide-out drawer with dark backdrop overlay that automatically closes when navigation links or backdrop are clicked.
- **Top Navbar:** Page title, breadcrumb hierarchy, quick search bar, notification badge & dropdown, user avatar, and account dropdown menu with logout.
- **Toast Notifications (`Toast.show(msg, type)`):** Lightweight notifications with auto-dismiss and color-coded types: `success`, `error`, `warning`, `info`.
- **Modals (`Modal.openRestaurantModal()`, `Modal.openCustomerModal()`, `Modal.confirmDelete()`):** Accessible overlay dialogs with smooth enter/exit animations.
- **Empty States:** Friendly illustration, explanation, and action buttons ensuring screens are never left blank.

---

## 5. LocalStorage Architecture & Seed Data

The platform automatically initializes realistic demo data on first load:

| Key | Purpose |
|---|---|
| `paytoconnect_restaurants` | Array of onboarded restaurants (Default: Chaudhary Cafe & Family Restaurant, Royal Cafe & Bakers, Spice Route Express). |
| `paytoconnect_customers` | Array of realistic diner records with names, phones, demographic tags, amounts, and consent. |
| `paytoconnect_users` | Admin and restaurant owner credentials for portal authentication. |
| `paytoconnect_session` | Current authenticated session state (`admin` or `restaurant`). |
| `paytoconnect_settings` | Global platform metadata and configuration flags. |

---

## 6. How to Run Locally

Because PaytoConnect is built using pure **HTML5 + Tailwind CSS (via CDN) + Vanilla JavaScript**, you can run it instantly using any method:

### Option 1: Any Local Web Server (Recommended)
From the project folder, run any lightweight server:
```bash
# Python:
python -m http.server 8000

# or PHP:
php -S localhost:8000

# or Node.js npx:
npx serve .
```
Then visit `http://localhost:8000` in any web browser.

### Option 2: Direct File Inspection
Open `index.html` directly in Google Chrome, Microsoft Edge, Safari, or Firefox. The built-in resilient component loader ensures all sidebars, navbars, and mock APIs render smoothly without CORS restrictions.

---

## 7. Commercial Ownership

**PaytoConnect** is a commercial proprietary software product developed by:

### **ALAG INNOVATIVE SOLUTIONS**
- **Lead & System Architect:** Abhishek Chaudhary
- **Platform:** PaytoConnect &bull; Scan. Pay. Connect.
- **All Rights Reserved.**
