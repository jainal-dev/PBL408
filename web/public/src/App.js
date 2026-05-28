// Main App Component
class App {
  constructor() {
    this.container = document.getElementById('app');
    this.currentPage = 'dashboard';
  }

  init() {
    this.render();
    this.setupNavigation();
    this.loadPage(this.currentPage);
    this.setupRouting();
  }

  render() {
    this.container.innerHTML = `
      <div class="app-container">
        <header>
          <h1>PBL409 - Production System</h1>
          <nav class="main-nav">
            <a href="#dashboard" data-page="dashboard">Dashboard</a>
            <a href="#validasi" data-page="validasi">Validasi</a>
            <a href="#detail_validasi" data-page="detail_validasi">Detail Validasi</a>
            <a href="#laporan" data-page="laporan">Laporan</a>
            <a href="#profil" data-page="profil">Profil</a>
            <a href="#login" data-page="login">Login</a>
            <a href="#daftar" data-page="daftar">Daftar</a>
          </nav>
        </header>
        <main id="main-content">
          <div class="loading">Loading...</div>
        </main>
      </div>
    `;
  }

  setupNavigation() {
    const navLinks = this.container.querySelectorAll('.main-nav a');
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const page = link.getAttribute('data-page');
        this.loadPage(page);
        // Update URL hash
        window.location.hash = page;
      });
    });
  }

  setupRouting() {
    // Handle initial load and hash changes
    const handleHashChange = () => {
      const hash = window.location.hash.substring(1) || 'dashboard';
      this.loadPage(hash);
    };

    window.addEventListener('hashchange', handleHashChange);
    handleHashChange(); // Handle initial load
  }

  async loadPage(pageName) {
    const mainContent = this.container.querySelector('#main-content');
    mainContent.innerHTML = '<div class="loading">Loading...</div>';

    try {
      const response = await fetch(`/pages/${pageName}.html`);
      if (!response.ok) {
        throw new Error(`Page ${pageName} not found`);
      }
      const html = await response.text();

      // Extract body content from the HTML
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const bodyContent = doc.body.innerHTML;

      mainContent.innerHTML = bodyContent;

      // Update active navigation
      this.updateActiveNav(pageName);

      this.currentPage = pageName;
      console.log(`Loaded page: ${pageName}`);

    } catch (error) {
      console.error('Error loading page:', error);
      mainContent.innerHTML = `
        <div class="error">
          <h2>Page Not Found</h2>
          <p>The page "${pageName}" could not be loaded.</p>
          <a href="#dashboard">Go to Dashboard</a>
        </div>
      `;
    }
  }

  updateActiveNav(activePage) {
    const navLinks = this.container.querySelectorAll('.main-nav a');
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('data-page') === activePage) {
        link.classList.add('active');
      }
    });
  }
}

export default App;
