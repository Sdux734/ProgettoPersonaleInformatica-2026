/**
 * Real-time search functionality for library management system
 * Provides instant search without requiring button clicks
 */

class RealTimeSearch {
    constructor() {
        this.searchTimeout = null;
        this.currentResults = [];
        this.init();
    }

    init() {
        console.log('Initializing RealTimeSearch...');
        // Initialize search for all search forms on the page
        this.initSearchForms();

        // Initialize table filtering for admin pages
        this.initTableFilters();
    }

    initSearchForms() {
        // Find all search forms
        const searchForms = document.querySelectorAll('.search-form');

        searchForms.forEach(form => {
            const searchInput = form.querySelector('#search-input') || form.querySelector('input[name="search"]');
            const categorySelect = form.querySelector('#category-select') || form.querySelector('select[name="categoria"]');
            const resetButton = form.querySelector('#reset-search');

            if (searchInput) {
                // Add real-time search to text input
                searchInput.addEventListener('input', (e) => {
                    this.handleSearchInput(e.target, form);
                });

                // Add search active indicator
                searchInput.addEventListener('focus', (e) => {
                    e.target.classList.add('search-active');
                });

                searchInput.addEventListener('blur', (e) => {
                    setTimeout(() => {
                        e.target.classList.remove('search-active');
                    }, 200);
                });
            }

            if (categorySelect) {
                // Add real-time filtering for category
                categorySelect.addEventListener('change', (e) => {
                    this.handleCategoryChange(e.target, form);
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    this.resetSearch();
                });
            }
        });
    }

    initTableFilters() {
        // For admin tables, implement client-side filtering
        const tables = document.querySelectorAll('.table');

        tables.forEach(table => {
            const searchInput = document.querySelector('#search-input') || document.querySelector('input[name="search"]');
            const categorySelect = document.querySelector('#category-select') || document.querySelector('select[name="categoria"]');

            if (searchInput && table) {
                searchInput.addEventListener('input', () => {
                    this.filterTableRows(table, searchInput.value, categorySelect ? categorySelect.value : '');
                });
            }

            if (categorySelect && table) {
                categorySelect.addEventListener('change', () => {
                    this.filterTableRows(table, searchInput ? searchInput.value : '', categorySelect.value);
                });
            }
        });
    }

    handleSearchInput(input, form) {
        const query = input.value.trim();

        // Remove existing results dropdown
        this.removeSearchResults();

        // Show visual feedback for active search
        if (query.length > 0) {
            input.classList.add('search-active');
        } else {
            input.classList.remove('search-active');
        }

        // Perform search immediately (letter by letter as requested)
        this.performSearch(query, form);
    }

    handleCategoryChange(select, form) {
        const categoryId = select.value;
        console.log('Category changed to:', categoryId);

        // Get current search query
        const searchInput = form.querySelector('#search-input') || form.querySelector('input[name="search"]');
        const query = searchInput ? searchInput.value.trim() : '';

        // For admin tables, filter immediately
        const table = document.querySelector('.table');
        if (table) {
            this.filterTableRows(table, query, categoryId);
        } else {
            // For user catalog, filter book cards immediately
            this.filterBookCards(query, categoryId);
        }
    }

    performSearch(query, form, categoryId = '') {
        console.log('Performing search:', query, 'Category:', categoryId);

        // Check if we're on an admin page with a table
        const table = document.querySelector('.table');
        if (table) {
            console.log('Admin table found, filtering client-side');
            // Client-side filtering for admin tables
            this.filterTableRows(table, query, categoryId);
        } else {
            console.log('No admin table, filtering book cards');
            // For user catalog, filter book cards
            this.filterBookCards(query, categoryId);
        }
    }

    filterTableRows(table, query, categoryId) {
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length < 2) return; // Skip if not a data row

            const title = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
            const author = row.cells[3] ? row.cells[3].textContent.toLowerCase() : '';
            const isbn = row.cells[4] ? row.cells[4].textContent.toLowerCase() : '';
            const category = row.cells[5] ? row.cells[5].textContent.toLowerCase() : '';

            const matchesQuery = !query ||
                title.includes(query.toLowerCase()) ||
                author.includes(query.toLowerCase()) ||
                isbn.includes(query.toLowerCase());

            const matchesCategory = !categoryId || category.includes(categoryId);

            if (matchesQuery && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update no results message
        this.updateNoResultsMessage(table, visibleCount);
    }

    updateNoResultsMessage(table, visibleCount) {
        let noResultsRow = table.querySelector('.no-results-row');
        const tbody = table.querySelector('tbody');

        if (visibleCount === 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = '<td colspan="8" style="text-align:center; padding: 40px;" class="no-results">Nessun libro trovato con i criteri di ricerca specificati.</td>';
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }
    }

    filterBookCards(query, categoryId) {
        const bookCards = document.querySelectorAll('.book-card');
        let visibleCount = 0;

        bookCards.forEach(card => {
            const title = card.dataset.title || '';
            const author = card.dataset.author || '';
            const cardCategoryId = card.dataset.category || '';

            const matchesQuery = !query ||
                title.includes(query.toLowerCase()) ||
                author.includes(query.toLowerCase());

            const matchesCategory = !categoryId || cardCategoryId === categoryId;

            if (matchesQuery && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update no results message
        this.updateNoBooksMessage(visibleCount);
        console.log(`Filtered ${visibleCount} books out of ${bookCards.length}`);
    }

    updateNoBooksMessage(visibleCount) {
        let noBooksMsg = document.querySelector('.no-books-message');
        const booksGrid = document.querySelector('.books-grid');

        if (visibleCount === 0) {
            if (!noBooksMsg) {
                noBooksMsg = document.createElement('div');
                noBooksMsg.className = 'no-books-message';
                noBooksMsg.innerHTML = '<p style="text-align: center; padding: 40px; font-size: 18px; color: #666;">Nessun libro trovato con i criteri di ricerca specificati.</p>';
                booksGrid.appendChild(noBooksMsg);
            }
            noBooksMsg.style.display = 'block';
        } else {
            if (noBooksMsg) {
                noBooksMsg.style.display = 'none';
            }
        }
    }

    showSearchLoading(form) {
        // Create loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'search-loading';
        loadingDiv.textContent = 'Ricerca in corso...';

        // Position it below the search form
        form.parentNode.insertBefore(loadingDiv, form.nextSibling);
    }

    removeSearchResults() {
        const results = document.querySelectorAll('.search-results');
        results.forEach(result => result.remove());
    }

    resetSearch() {
        console.log('Resetting search');

        // Clear search input
        const searchInput = document.querySelector('#search-input') || document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.value = '';
        }

        // Reset category select
        const categorySelect = document.querySelector('#category-select') || document.querySelector('select[name="categoria"]');
        if (categorySelect) {
            categorySelect.value = '';
        }

        // Show all table rows
        const table = document.querySelector('.table');
        if (table) {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (!row.classList.contains('no-results-row')) {
                    row.style.display = '';
                }
            });

            // Hide no results message
            const noResultsRow = table.querySelector('.no-results-row');
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }

        // Show all book cards
        const bookCards = document.querySelectorAll('.book-card');
        bookCards.forEach(card => {
            card.style.display = '';
        });

        // Hide no books message
        const noBooksMsg = document.querySelector('.no-books-message');
        if (noBooksMsg) {
            noBooksMsg.style.display = 'none';
        }
    }
}

// Initialize real-time search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new RealTimeSearch();
});


