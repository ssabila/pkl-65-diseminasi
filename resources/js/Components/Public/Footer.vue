<template>
  <footer class="footer-section">
    <div class="footer-container">
      <!-- Left Column - Logo & Description -->
      <div class="footer-column footer-left">
        <div class="logo-section">
          <img src="/images/assets/LOGO-PKL_REV8.png" alt="Logo PKL 65" class="footer-logo" />
          <div class="logo-text">
            <h3 class="logo-title">PKL 65</h3>
            <p class="logo-subtitle">STIS 2025/2026</p>
          </div>
        </div>
        <p class="footer-description">
          Praktik Kerja Lapangan Mahasiswa Tingkat III Politeknik Statistika STIS di DI Yogyakarta.
        </p>
      </div>

      <!-- Middle Column - Navigation -->
      <div class="footer-column footer-nav">
        <h4 class="footer-heading">Navigasi</h4>
        <ul class="nav-links">
          <li><a href="#beranda">Beranda</a></li>
          <li><a href="#hasil-riset">Hasil Riset</a></li>
          <li><a href="#login">Login</a></li>
        </ul>
      </div>

      <!-- Right Column - Contact -->
      <div class="footer-column footer-contact">
        <h4 class="footer-heading">Kontak</h4>
        <div class="contact-info">
          <div class="contact-item">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <div class="contact-text">
              <p>Politeknik Statistika STIS, Jakarta Timur</p>
            </div>
          </div>
          <div class="contact-item">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="16" rx="2" ry="2"></rect>
              <polyline points="3 8 12 13 21 8"></polyline>
            </svg>
            <div class="contact-text">
              <a href="mailto:pkl65@stis.ac.id" class="email-link">pkl65@stis.ac.id</a>
            </div>
          </div>
          <div class="contact-item">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="2" y1="12" x2="22" y2="12"></line>
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <div class="contact-text">
              <a href="https://www.stis.ac.id" target="_blank">www.stis.ac.id</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section - Copyright & Social Media -->
    <div class="footer-bottom">
      <p class="copyright">© 2025 Politeknik Statistika STIS. All rights reserved.</p>
      <div class="social-media">
        <a href="#" class="social-icon" aria-label="Facebook">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
        </a>
        <a href="#" class="social-icon" aria-label="Instagram">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
        </a>
        <a href="#" class="social-icon" aria-label="Twitter">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
          </svg>
        </a>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer'
}
</script>

<style scoped>
.footer-section {
  background-color: #1A1F2E;
  color: #E5E7EB;
  padding: 60px 120px 30px;
  font-family: 'Poppins', sans-serif;
}

.footer-container {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr;
  gap: 80px;
  margin-bottom: 50px;
}

/* Left Column */
.footer-left {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}

.footer-logo {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  object-fit: cover;
  background-color: #EF874B;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-title {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
  font-weight: 700;
  color: #FFFFFF;
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 14px;
  color: #9CA3AF;
  margin: 0;
  line-height: 1.2;
}

.footer-description {
  font-size: 15px;
  line-height: 1.7;
  color: #D1D5DB;
  margin: 0;
}

/* Middle Column - Navigation */
.footer-nav {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.footer-heading {
  font-family: 'Playfair Display', serif;
  font-size: 20px;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 10px 0;
}

.nav-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.nav-links a {
  color: #D1D5DB;
  text-decoration: none;
  font-size: 15px;
  transition: color 0.3s ease;
}

.nav-links a:hover {
  color: #EF874B;
}

/* Right Column - Contact */
.footer-contact {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.icon {
  color: #EF874B;
  flex-shrink: 0;
  margin-top: 2px;
}

.contact-text {
  flex: 1;
}

.contact-text p,
.contact-text a {
  margin: 0;
  font-size: 14px;
  line-height: 1.6;
  color: #D1D5DB;
}

.contact-text a {
  text-decoration: none;
  transition: color 0.3s ease;
}

.email-link {
  color: #EF874B !important;
}

.contact-text a:hover {
  color: #EF874B;
}

/* Bottom Section */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 30px;
  border-top: 1px solid #374151;
}

.copyright {
  font-size: 14px;
  color: #9CA3AF;
  margin: 0;
}

.social-media {
  display: flex;
  gap: 20px;
}

.social-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9CA3AF;
  transition: all 0.3s ease;
  border-radius: 8px;
}

.social-icon:hover {
  color: #EF874B;
  background-color: rgba(239, 135, 75, 0.1);
}

/* Responsive */
@media (max-width: 1024px) {
  .footer-section {
    padding: 50px 80px 30px;
  }

  .footer-container {
    grid-template-columns: 1fr 1fr;
    gap: 50px;
  }

  .footer-left {
    grid-column: 1 / -1;
  }
}

@media (max-width: 768px) {
  .footer-section {
    padding: 40px 30px 25px;
  }

  .footer-container {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .footer-bottom {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }

  .logo-title {
    font-size: 24px;
  }

  .footer-logo {
    width: 55px;
    height: 55px;
  }
}
</style>