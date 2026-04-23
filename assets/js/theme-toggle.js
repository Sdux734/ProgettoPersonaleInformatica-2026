function toggleBookStyle() {
    document.body.classList.toggle('book-style');
    const button = document.querySelector('#book-style-toggle');
    if (document.body.classList.contains('book-style')) {
        button.innerHTML = '🔄 <span>Stile Moderno</span>';
        button.title = 'Torna allo stile moderno';
    } else {
        button.innerHTML = '📖 <span>Stile Libro</span>';
        button.title = 'Attiva stile libro retro';
    }
    // Salva la preferenza nel localStorage
    localStorage.setItem('bookStyle', document.body.classList.contains('book-style'));

    // Emetti evento per notificare il cambio di tema
    document.dispatchEvent(new CustomEvent('bookStyleToggled'));
}

// Carica la preferenza al caricamento della pagina
document.addEventListener('DOMContentLoaded', function() {
    const bookStyleEnabled = localStorage.getItem('bookStyle') === 'true';
    if (bookStyleEnabled) {
        document.body.classList.add('book-style');
        const button = document.querySelector('#book-style-toggle');
        if (button) {
            button.innerHTML = '🔄 <span>Stile Moderno</span>';
            button.title = 'Torna allo stile moderno';
        }
    }

    // Carica il script di ricerca in tempo reale
    loadRealTimeSearch();
});

function loadRealTimeSearch() {
    // Controlla se il script è già caricato
    if (document.querySelector('script[data-realtime-search]')) {
        return;
    }

    // Crea e carica il script di ricerca in tempo reale
    const script = document.createElement('script');
    script.setAttribute('data-realtime-search', 'true');

    // Determina il path corretto basato sulla posizione della pagina
    const currentPath = window.location.pathname;
    if (currentPath.includes('/admin/')) {
        script.src = '../../assets/js/real-time-search.js';
    } else if (currentPath.includes('/user/')) {
        script.src = '../assets/js/real-time-search.js';
    } else {
        script.src = 'assets/js/real-time-search.js';
    }

    script.onload = function() {
        console.log('Real-time search script loaded successfully');
        // Inizializza la ricerca subito, il DOM è già pronto
        if (typeof RealTimeSearch !== 'undefined') {
            new RealTimeSearch();
        }
    };

    script.onerror = function() {
        console.error('Failed to load real-time search script from:', script.src);
    };

    document.head.appendChild(script);
}

