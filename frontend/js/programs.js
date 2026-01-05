/* for sorting and filtering, formatting date, updating program status */
document.addEventListener("DOMContentLoaded", () => {
    function formatToAMPM(time24) {
        const [hour, minute] = time24.split(':');
        const h = (hour % 12) || 12;
        return `${h}:${minute} ${hour >= 12 ? 'PM' : 'AM'}`;
    }

    function isProgramOnline(daysArray, startT, endT) {
        const now = new Date();
        const [startHour, startMinute] = startT.split(':').map(Number);
        const [endHour, endMinute] = endT.split(':').map(Number);

        const start = new Date(now); start.setHours(startHour, startMinute, 0);
        const end = new Date(now); end.setHours(endHour, endMinute, 0);

        if (end < start) end.setDate(end.getDate() + 1); // handle programs past midnight

        const dayMap = {0:'SUN',1:'WEEKDAYS',2:'WEEKDAYS',3:'WEEKDAYS',4:'WEEKDAYS',5:'WEEKDAYS',6:'SAT'};
        const todayType = dayMap[now.getDay()];

        return daysArray.includes(todayType) && now >= start && now <= end;
    }

    const list = document.querySelector('.program-list');
    const cards = Array.from(list.children);

    // updates ON AIR status
    cards.forEach(card => {
        const days = card.dataset.days.split(',').map(d => d.trim());
        const start = card.dataset.start;
        const end = card.dataset.end;
        const statusEl = card.querySelector('.status');

        if (isProgramOnline(days, start, end)) {
            statusEl.textContent = 'ON AIR';
            statusEl.classList.remove('offline');
            statusEl.classList.add('online');
        } else {
            statusEl.textContent = 'OFFLINE';
            statusEl.classList.remove('online');
            statusEl.classList.add('offline');
        }
    });

    // sorts / filters cards
    function render(filter = 'title') {
        let sortedCards = [...cards];

        if (['weekdays', 'sat', 'sun'].includes(filter)) {
            const map = { weekdays: 'WEEKDAYS', sat: 'SAT', sun: 'SUN' };
            sortedCards = sortedCards.filter(card => card.dataset.days.split(',').map(d => d.trim()).includes(map[filter]));
            sortedCards.sort((a, b) => a.dataset.start.localeCompare(b.dataset.start));
        } else if (filter === 'time') {
            sortedCards.sort((a, b) => a.dataset.start.localeCompare(b.dataset.start));
        } else { // title
            sortedCards.sort((a, b) => a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent));
        }

        list.innerHTML = '';
        sortedCards.forEach(card => list.appendChild(card));
    }

    // initial render (default): Title A–Z
    render('title');

    document.getElementById('filter-select').addEventListener('change', e => render(e.target.value));
});