/* for sorting and highlighting the latest news */
document.addEventListener('DOMContentLoaded', function () {
  // for sorting
  const sortSelect = document.getElementById('sortSelect');
  const container = document.querySelector('.news-section');

  if (!sortSelect || !container) return;

  sortSelect.addEventListener('change', function () {
    const cards = Array.from(container.querySelectorAll('.news-card'));
    const sortType = this.value;

    let sortedCards;

    switch (sortType) {
      case 'oldest':
        sortedCards = cards.sort((a, b) =>
          new Date(a.dataset.date) - new Date(b.dataset.date)
        );
        break;

      case 'newest':
        sortedCards = cards.sort((a, b) =>
          new Date(b.dataset.date) - new Date(a.dataset.date)
        );
        break;

      case 'title-az':
      sortedCards = cards.sort((a, b) => {
        const titleA = a.dataset.title.trim();
        const titleB = b.dataset.title.trim();

        const aStartsWithNumber = /^[0-9]/.test(titleA);
        const bStartsWithNumber = /^[0-9]/.test(titleB);

        // numbers first before letters
        if (aStartsWithNumber && !bStartsWithNumber) return -1;
        if (!aStartsWithNumber && bStartsWithNumber) return 1;

        // Same type → normal A–Z compare
        return titleA.localeCompare(titleB, undefined, {
            numeric: true,
            sensitivity: 'base'
            });
        });
        break;

      case 'org-az':
      sortedCards = cards.sort((a, b) => {
        const orgA = (a.dataset.org || '').trim();
        const orgB = (b.dataset.org || '').trim();

        const aStartsWithNumber = /^[0-9]/.test(orgA);
        const bStartsWithNumber = /^[0-9]/.test(orgB);

        if (aStartsWithNumber && !bStartsWithNumber) return -1;
        if (!aStartsWithNumber && bStartsWithNumber) return 1;

        return orgA.localeCompare(orgB, undefined, {
            numeric: true,
            sensitivity: 'base'
            });
        });
        break;

      case 'author-az':
      sortedCards = cards.sort((a, b) => {
        const authorA = (a.dataset.author || '').trim();
        const authorB = (b.dataset.author || '').trim();

        const aStartsWithNumber = /^[0-9]/.test(authorA);
        const bStartsWithNumber = /^[0-9]/.test(authorB);

        if (aStartsWithNumber && !bStartsWithNumber) return -1;
        if (!aStartsWithNumber && bStartsWithNumber) return 1;

        return authorA.localeCompare(authorB, undefined, {
            numeric: true,
            sensitivity: 'base'
            });
        });
        break;

      default:
        return;
    }

    sortedCards.forEach(card => container.appendChild(card));
  });

    // for searching
    const searchInput = document.getElementById('newsSearch');
    const newsCards = document.querySelectorAll('.news-card');

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();

        newsCards.forEach(card => {
        const headline = card.querySelector('h6')?.textContent.toLowerCase() || '';
        const summary = card.querySelector('p')?.textContent.toLowerCase() || '';
        const organization = card.dataset.org?.toLowerCase() || '';
        const author = card.querySelector('.author-category-section a')?.textContent.toLowerCase() || '';
        const categories = Array.from(card.querySelectorAll('.category-pill'))
            .map(cat => cat.textContent.toLowerCase())
            .join(' ');

        const searchableText = `
            ${headline}
            ${summary}
            ${organization}
            ${author}
            ${categories}
        `;

        if (searchableText.includes(query)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
        });
    });

    // for category filtering
    let activeCategory = null;

    const cards = document.querySelectorAll('.news-card');
    const categoryPills = document.querySelectorAll('.category-pill');

    categoryPills.forEach(pill => {
        pill.addEventListener('click', function () {
        const selectedCategory = this.textContent.toLowerCase().trim();

        if (activeCategory === selectedCategory) {
            activeCategory = null;
            clearActivePills();
            showAllCards();
            return;
        }

        activeCategory = selectedCategory;
        setActivePill(this);

        cards.forEach(card => {
            const cardCategories = card.dataset.categories || '';
            if (cardCategories.includes(activeCategory)) {
            card.style.display = '';
            } else {
            card.style.display = 'none';
            }
        });
        });
    });

    function clearActivePills() {
        categoryPills.forEach(p => p.classList.remove('active'));
    }

    function setActivePill(activePill) {
        clearActivePills();
        activePill.classList.add('active');
    }

    function showAllCards() {
        cards.forEach(card => card.style.display = '');
    }
});
