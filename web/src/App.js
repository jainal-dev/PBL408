// Main App Component
class App {
  constructor() {
    this.container = document.getElementById('app');
    this.currentPage = 'user/login';
  }

  init() {
    this.render();
    this.setupRouting();
  }

  render() {
    this.container.innerHTML = `
      <div class="app-container">
        <header class="page-top-nav hidden" id="top-nav"></header>
        <main id="main-content">
          <div class="loading">Loading...</div>
        </main>
      </div>
    `;
  }

  setupRouting() {
    const handleHashChange = () => {
      const hash = window.location.hash.substring(1) || '';
      const authRole = localStorage.getItem('authRole');
      const pageName = this.normalizeRoute(hash, authRole);
      if (pageName !== hash) {
        window.location.hash = pageName;
        return;
      }
      this.updateTopNav(pageName, authRole);
      this.loadPage(pageName);
    };

    window.addEventListener('hashchange', handleHashChange);
    handleHashChange();
  }

  normalizeRoute(pageName, authRole) {
    const page = (pageName || '').toLowerCase();

    if (page === '' || page === 'dashboard') {
      if (authRole === 'admin') return 'admin/dashboard';
      if (authRole === 'user') return 'user/dashboard';
      return 'user/login';
    }

    if (page === 'login') return 'user/login';
    if (page === 'register' || page === 'daftar') return 'user/register';

    if (page.startsWith('admin/')) {
      if (authRole === 'admin') return page;
      if (authRole === 'user') return 'user/dashboard';
      return 'admin/login';
    }

    if (page.startsWith('user/')) {
      if (page === 'user/login' || page === 'user/register') {
        if (authRole === 'admin') return 'admin/dashboard';
        return page;
      }
      if (authRole === 'user') return page;
      if (authRole === 'admin') return 'admin/dashboard';
      return 'user/login';
    }

    return authRole === 'admin' ? 'admin/dashboard' : authRole === 'user' ? 'user/dashboard' : 'user/login';
  }

  updateTopNav(pageName, authRole) {
    const topNav = this.container.querySelector('#top-nav');
    if (!authRole || authRole === 'none') {
      topNav.classList.add('hidden');
      topNav.innerHTML = '';
      return;
    }

    const navItems = authRole === 'admin'
      ? [
          { title: 'Dashboard', hash: 'admin/dashboard' }
        ]
      : [
          { title: 'Dashboard', hash: 'user/dashboard' },
          { title: 'Validasi', hash: 'user/validasi' },
          { title: 'Laporan', hash: 'user/laporan' },
          { title: 'Profil', hash: 'user/profil' }
        ];

    const navLinks = navItems.map(item => {
      const activeClass = item.hash === pageName ? ' active' : '';
      return `<a href="#${item.hash}" class="nav-link${activeClass}">${item.title}</a>`;
    }).join('');

    topNav.innerHTML = `
      <div class="nav-bar-inner">
        <div class="nav-links">${navLinks}</div>
        <button class="nav-logout-btn" type="button">Keluar</button>
      </div>
    `;
    topNav.classList.remove('hidden');

    topNav.querySelector('.nav-logout-btn').addEventListener('click', () => {
      localStorage.removeItem('authRole');
      localStorage.removeItem('authUser');
      localStorage.removeItem('authEmail');
      window.location.hash = 'user/login';
    });
  }

  clearPageAssets() {
    document.head.querySelectorAll('[data-page-asset]').forEach(node => node.remove());
    document.body.querySelectorAll('[data-page-script]').forEach(node => node.remove());
  }

  injectPageAssets(doc, pagePath) {
    const baseUrl = new URL(pagePath, window.location.origin);
    const headNodes = Array.from(doc.head.querySelectorAll('link[rel="stylesheet"], style'));

    headNodes.forEach(node => {
      if (node.tagName === 'LINK') {
        const href = node.getAttribute('href');
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = new URL(href, baseUrl).href;
        link.dataset.pageAsset = 'true';
        document.head.appendChild(link);
      } else {
        const style = document.createElement('style');
        style.textContent = node.textContent;
        style.dataset.pageAsset = 'true';
        document.head.appendChild(style);
      }
    });
  }

  executePageScripts(doc, pagePath) {
    const baseUrl = new URL(pagePath, window.location.origin);
    const scripts = Array.from(doc.body.querySelectorAll('script'));

    scripts.forEach(node => {
      const script = document.createElement('script');
      if (node.src) {
        script.src = new URL(node.getAttribute('src'), baseUrl).href;
      } else {
        script.textContent = node.textContent;
      }
      if (node.type) script.type = node.type;
      if (node.async) script.async = true;
      if (node.defer) script.defer = true;
      script.dataset.pageScript = 'true';
      document.body.appendChild(script);
    });
  }

  async loadPage(pageName) {
    const mainContent = this.container.querySelector('#main-content');
    mainContent.innerHTML = '<div class="loading">Loading...</div>';
    const path = `/pages/${pageName}.html`;

    try {
      const response = await fetch(path);
      if (!response.ok) {
        throw new Error(`Page ${pageName} not found`);
      }
      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      this.clearPageAssets();
      this.injectPageAssets(doc, path);
      mainContent.innerHTML = doc.body.innerHTML;
      this.executePageScripts(doc, path);
      this.currentPage = pageName;
      console.log(`Loaded page: ${pageName}`);
    } catch (error) {
      console.error('Error loading page:', error);
      mainContent.innerHTML = `
        <div class="error">
          <h2>Page Not Found</h2>
          <p>The page "${pageName}" could not be loaded.</p>
          <a href="#user/login">Go to Login</a>
        </div>
      `;
    }
  }
}

export default App;
