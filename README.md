# SSPSEO - Custom Consent Mode v2 Boilerplate for WordPress & GTM

A lightweight, zero-dependency, and 100% GDPR-compliant cookie consent system designed for high-performance websites. Built natively around Google Consent Mode v2 (Advanced Implementation) and WordPress core architecture.

## 🚀 Why This Project Exists?
Standard consent management platforms (CMPs) inject heavy, third-party JavaScript files that severely hurt Core Web Vitals (CLS & LCP) and page speed scores. 

This custom solution solves the performance-vs-compliance dilemma by:
- **0.0ms Performance Impact**: Zero external HTTP requests; all logic runs natively on the client browser.
- **Advanced Consent Mode v2 Ready**: Automatically interfaces with `gtag.js` and Google Tag Manager before container initialization to prevent data leakage.
- **Strict Compliance**: Leverages a custom back-end table inside WordPress to generate an anonymous, immutable **Proof of Consent (Registro dei Consensi)** database for audit trails.
- **Asynchronous Privacy Blocking**: Dynamically intercepts Google Fonts API connections to protect user IP addresses prior to consent expression.

---

## 🛠️ Tech Stack & Architecture
- **Frontend**: Vanilla JavaScript (ES6+), CSS3 (Glassmorphism design, custom animations), HTML5.
- **Backend**: PHP (WordPress AJAX API, WPDB abstraction layer).
- **Data Layer**: Google Tag Manager & Google Analytics 4 (Natively mapped to `analytics_storage`, `ad_storage`, `ad_user_data`, `ad_personalization`).
- **Database**: MySQL / MariaDB (Custom secure schema).
