class NewspaperViewer {
    constructor(options) {
        this.container = options.container;
        this.carouselContainer = options.carouselContainer;
        this.prevButton = options.prevButton;
        this.nextButton = options.nextButton;
        this.currentPage = 0;
        this.totalPages = 0;
        this.pages = [];
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.prevButton.addEventListener('click', () => this.previousPage());
        this.nextButton.addEventListener('click', () => this.nextPage());
        
        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.previousPage();
            if (e.key === 'ArrowRight') this.nextPage();
        });

        // Add touch swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.carouselContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
        });
        
        this.carouselContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].clientX;
            if (touchStartX - touchEndX > 50) { // Swipe left
                this.nextPage();
            }
            if (touchEndX - touchStartX > 50) { // Swipe right
                this.previousPage();
            }
        });
    }

    loadNewspaper(date) {
        const formData = new FormData();
        formData.append('date', date);

        return fetch('fetch_newspaper.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'warning') {
                this.pages = data.pages;
                this.totalPages = data.pages.length;
                this.currentPage = 0;
                this.render();
                if (data.status === 'warning') {
                    this.showMessage(data.message, 'warning');
                }
                this.updateDropdown();
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            this.showMessage(`Error loading newspaper: ${error.message}`, 'error');
        });
    }

    render() {
        if (this.pages.length === 0) {
            this.showMessage('کوئی صفحہ دستیاب نہیں ہے', 'info');
            return;
        }

        const currentPage = this.pages[this.currentPage];
        
        // Preload adjacent pages
        this.preloadPages();

        this.carouselContainer.innerHTML = `
            <div class="newspaper-page">
                <img src="${currentPage.image_path}" 
                     alt="Page ${currentPage.page_number}"
                     class="newspaper-image"
                     loading="lazy">
                <div class="page-number">صفحہ ${currentPage.page_number}</div>
            </div>
        `;

        this.updateNavigationState();
    }

    preloadPages() {
        // Preload next and previous pages
        [-1, 1].forEach(offset => {
            const pageIndex = this.currentPage + offset;
            if (pageIndex >= 0 && pageIndex < this.totalPages) {
                const img = new Image();
                img.src = this.pages[pageIndex].image_path;
            }
        });
    }

    previousPage() {
        if (this.currentPage > 0) {
            this.currentPage--;
            this.render();
        }
    }

    nextPage() {
        if (this.currentPage < this.totalPages - 1) {
            this.currentPage++;
            this.render();
        }
    }

    updateNavigationState() {
        this.prevButton.disabled = this.currentPage === 0;
        this.nextButton.disabled = this.currentPage === this.totalPages - 1;
    }

    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        this.container.innerHTML = '';
        this.container.appendChild(alertDiv);
    }

    updateDropdown() {
        const dropdown = document.getElementById('pagesDropdown');
        if (dropdown) {
            dropdown.innerHTML = this.pages.map((page, index) => `
                <li><a class="dropdown-item page-selector" href="#" data-page="${index}">صفحہ ${page.page_number}</a></li>
            `).join('');

            // Add click handlers to dropdown items
            dropdown.querySelectorAll('.page-selector').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.currentPage = parseInt(e.target.dataset.page);
                    this.render();
                });
            });
        }
    }
}