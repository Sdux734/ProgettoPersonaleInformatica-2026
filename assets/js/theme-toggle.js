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
});

