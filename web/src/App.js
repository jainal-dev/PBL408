// Main App Component
class App {
  constructor() {
    this.container = document.getElementById('app');
  }

  init() {
    this.render();
    this.setupNavigation();
  }

  render() {
    this.container.innerHTML = `
      <div class="app-container">
        <header>
          <h1>PBL409 - Production System</h1>
          <nav class="main-nav">
            <a href="#dashboard">Dashboard</a>
            <a href="#validasi">Validasi</a>
            <a href="#laporan">Laporan</a>
            <a href="#profil">Profil</a>
          </nav>
        </header>
        <main id="main-content">
          <p>Welcome to PBL409 Production System</p>
        </main>
      </div>
    `;
  }

  setupNavigation() {
    const navLinks = this.container.querySelectorAll('.main-nav a');
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const page = link.getAttribute('href').substring(1);
        console.log('Navigating to:', page);
      });
    });
  }
}

export default App;
