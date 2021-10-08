<template>
  <div class="landing-pg">
    <div class="columns">
      <div class="column">
        <div class="landing-header">
          <img class="img-logo" src="images/brand.svg" />
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column">
        <div class="carousel-area">
          <div class="columns">
            <div class="column item-center">
              <h1 class="label-ubis">UBIS</h1>
            </div>
          </div>
          <div class="columns">
            <div class="column item-center">
              <h1 class="label-version">Version {{this.systemVersion}}</h1>
            </div>
          </div>
          <div class="columns">
            <div class="column item-center">
              <p class="label-service">Integrated Manufacturing Services</p>
            </div>
          </div>
          <div class="columns">
            <div class="column item-center">
              <div class="up-arch-svg">
                <svg viewBox="0 0 2880 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M720 28H0v28h2880V28h-720S1874 0 1440 0 720 28 720 28z" fill="currentColor"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="columns card-container">
      <div class="container is-mobile">
        <div class="columns">
          <div class="column item-center">
            <div class="card-container-label">
              <p>IT'S YOUR CHOICE</p>
            </div>
          </div>
        </div>
        <div class="columns">
          <div class="column is-12-mobile is-12-tablet is-6-desktop">
            <a :href="registrationPageURL">
              <div class="card-item">
                <div class="columns">
                  <div class="column">
                    <div class="icon-label item-center">
                      <img class="card-icon" src="images/registration-icon.svg" />
                      <p class="card-label">Product registration</p>
                    </div>
                  </div>
                </div>
                <div class="columns">
                  <div class="column">
                    <div class="card-btn">
                      <p class="card-btn-label">Open</p>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="column is-12-mobile is-12-tablet is-6-desktop">
            <a :href="searchPageURL">
              <div class="card-item">
                <div class="columns">
                  <div class="column">
                    <div class="icon-label item-center">
                      <img class="card-icon" src="images/search-icon.svg" />
                      <p class="card-label">Product search</p>
                    </div>
                  </div>
                </div>
                <div class="columns">
                  <div class="column">
                    <div class="card-btn">
                      <p class="card-btn-label">Open</p>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
        <div class="columns">
          <div class="column is-12-mobile is-12-tablet is-6-desktop">
            <a :href="loginPageURL">
              <div class="card-item">
                <div class="columns">
                  <div class="column">
                    <div class="icon-label item-center">
                      <img class="card-icon" src="images/configuration-icon.svg" />
                      <p class="card-label">UBIS Management</p>
                    </div>
                  </div>
                </div>
                <div class="columns">
                  <div class="column">
                    <div class="card-btn">
                      <p class="card-btn-label">Open</p>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="column is-12-mobile is-12-tablet is-6-desktop">
            <a :href="dashboardPageRUL">
              <div class="card-item">
                <div class="columns">
                  <div class="column">
                    <div class="icon-label item-center">
                      <img class="card-icon" src="images/dashboard-icon.svg" />
                      <p class="card-label">Dashboard</p>
                    </div>
                  </div>
                </div>
                <div class="columns">
                  <div class="column">
                    <div class="card-btn">
                      <p class="card-btn-label">Open</p>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="contact-container">
      <div class="columns">
        <div class="column item-center">
          <div class="down-arch-svg">
            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 48h2880V0h-720C1442.5 52 720 0 720 0H0v48z" fill="currentColor"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="columns is-mobile">
          <div class="column is-12-mobile is-12-tablet is-3-desktop">
            <div class="columns">
              <img class="contact-logo" src="images/brand.svg" />
            </div>
            <div class="columns">
              <p class="contact-sub-label strong-color">We Display your Brilliance.</p>
            </div>
            <div class="columns">
              <div class="column is-6-mobile is-8-desktop">
                <div class="columns social-links">
                  <img class="social-icon" src="images/instagram.svg" />
                  <img class="social-icon" src="images/facebook.svg" />
                  <img class="social-icon" src="images/twitter.svg" />
                  <img class="social-icon" src="images/pinterest.svg" />
                </div>
              </div>
              <div class="column is-6-mobile is-4-desktop"></div>
            </div>
          </div>
          <div class="column is-6-mobile is-4-tablet is-3-desktop">
            <div class="columns">
              <p class="contact-main-label">INTERNAL LINKS</p>
            </div>
            <div class="columns">
              <a href="http://sims-int.garching.semsotec.de/index.html" class="contact-sub-label">Prozesse</a>
            </div>
            <div class="columns">
              <a href="https://cloud.garching.semsotec.de/index.php/apps/dashboard/" class="contact-sub-label">Nextcloud</a>
            </div>
            <div class="columns">
              <a href="https://openproject.garching.semsotec.de/" class="contact-sub-label">Projektmanagement</a>
            </div>
          </div>
          <div class="column is-6-mobile is-4-tablet is-3-desktop">
            <div class="columns">
              <p class="contact-main-label">EXTERNAL LINKS</p>
            </div>
            <div class="columns">
              <a href="https://www.semsotec.de/" class="contact-sub-label">Semsotec Website</a>
            </div>
            <div class="columns">
              <a href="#" class="contact-sub-label">UBIS Docs</a>
            </div>
            <div class="columns">
              <a href="#" class="contact-sub-label">etc ...</a>
            </div>
          </div>
          <div class="column is-6-mobile is-4-tablet is-3-desktop">
            <div class="columns">
              <p class="contact-main-label">SUPPORT CONTACT</p>
            </div>
            <div class="columns">
              <a class="contact-sub-label" href="#">Technical support</a>
            </div>
            <div class="columns">
              <p class="contact-owner"><strong>Markus Harrieder</strong></p>
            </div>
            <div class="columns">
              <p class="contact-phone">markus.harrieder@semsotec.de</p>
            </div>
            <div class="columns">
              <p class="contact-phone">+49 941 46392360</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-container">
      <div class="container">
        <div class="columns is-mobile">
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-fast-solutions.svg" />
          </div>
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-esp.svg" />
          </div>
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-iligenta.svg" />
          </div>
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-corporation.svg" />
          </div>
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-czech.svg" />
          </div>
          <div class="column is-6-mobile is-4-tablet is-2-desktop">
            <img class="footer-logo-icon" src="images/logo-holding.svg" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import Parallax from 'vue-parallaxy'
  export default {
    components: {
      Parallax
    },
    data() {
      return {
        searchPageURL: '',
        registrationPageURL: '',
        loginPageURL: '',
        dashboardPageRUL: '',
        systemVersion: ''
      }
    },
    created() {
      this.systemVersion = process.env.MIX_SYSTEM_VERSION
      this.loginPageURL = process.env.MIX_LOGIN_PAGE_URL
      this.searchPageURL = process.env.MIX_PRODUCTS_SEARCH_PAGE_URL
      this.registrationPageURL = process.env.MIX_PRODUCTS_REGISTRATION_PAGE_URL
      this.dashboardPageRUL = process.env.MIX_DASHBOARD_PAGE_URL
    }
  }
</script>
