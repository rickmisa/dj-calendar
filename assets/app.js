const state = { date: new Date(), events: [], query: '', category: 'all' };
const $ = (selector) => document.querySelector(selector);
const api = 'api/events.php';
const monthName = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' });
const shortMonth = new Intl.DateTimeFormat('en-US', { month: 'short' });

function dateKey(date) { return date.toISOString().slice(0, 10); }
function pad(value) { return String(value).padStart(2, '0'); }
function formatTime(value) { if (!value) return 'All day'; const [hour, minute] = value.split(':'); const date = new Date(2000, 0, 1, hour, minute); return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }); }
function visibleEvents() { return state.events.filter((event) => (state.category === 'all' || event.category === state.category) && event.title.toLowerCase().includes(state.query.toLowerCase())); }
function setMonthLabel() { $('#monthLabel').textContent = monthName.format(state.date); }

async function loadEvents() {
    setMonthLabel();
    const month = `${state.date.getFullYear()}-${pad(state.date.getMonth() + 1)}`;
    try { const response = await fetch(`${api}?month=${month}`); const data = await response.json(); if (!response.ok) throw new Error(data.error); state.events = data; render(); }
    catch (error) { state.events = []; render(); showToast(error.message || 'Could not load events.'); }
}

function render() { renderCalendar(); renderAgenda(); }
function renderCalendar() {
    const grid = $('#calendarGrid'); grid.innerHTML = '';
    const firstDay = new Date(state.date.getFullYear(), state.date.getMonth(), 1);
    const startOffset = (firstDay.getDay() + 6) % 7;
    const daysInMonth = new Date(state.date.getFullYear(), state.date.getMonth() + 1, 0).getDate();
    const previousDays = new Date(state.date.getFullYear(), state.date.getMonth(), 0).getDate();
    const today = dateKey(new Date());
    for (let index = 0; index < 42; index += 1) {
        const dayNumber = index - startOffset + 1; const current = new Date(state.date.getFullYear(), state.date.getMonth(), dayNumber); const key = dateKey(current);
        const cell = document.createElement('div'); cell.className = `day-cell${current.getMonth() !== state.date.getMonth() ? ' muted' : ''}${key === today ? ' today' : ''}`;
        const label = current.getMonth() === state.date.getMonth() ? dayNumber : (dayNumber < 1 ? previousDays + dayNumber : dayNumber - daysInMonth); const number = document.createElement('div'); number.className = 'day-number'; number.dataset.day = label; number.textContent = label; cell.append(number);
        visibleEvents().filter((event) => event.event_date === key).forEach((event) => { const chip = document.createElement('button'); chip.className = 'event-chip'; chip.style.setProperty('--chip-color', event.color); chip.innerHTML = `<time>${formatTime(event.start_time)}</time>${escapeHtml(event.title)}`; chip.addEventListener('click', () => openModal(event)); cell.append(chip); });
        cell.addEventListener('dblclick', () => openModal(null, key)); grid.append(cell);
    }
}
function renderAgenda() {
    const events = visibleEvents().sort((a, b) => `${a.event_date}${a.start_time || ''}`.localeCompare(`${b.event_date}${b.start_time || ''}`)); $('#eventCount').textContent = `${events.length} event${events.length === 1 ? '' : 's'}`;
    $('#agendaList').innerHTML = events.length ? events.slice(0, 8).map((event) => { const date = new Date(`${event.event_date}T00:00:00`); return `<article class="agenda-item" data-id="${event.id}"><div class="agenda-date">${shortMonth.format(date)}<strong>${date.getDate()}</strong></div><div><h3>${escapeHtml(event.title)}</h3><div class="agenda-meta"><i class="agenda-accent" style="--item-color:${event.color}"></i>${formatTime(event.start_time)}${event.location ? ` · ${escapeHtml(event.location)}` : ''}</div></div></article>`; }).join('') : '<p class="empty-state">Nothing scheduled here yet.<br>Make space for what matters.</p>';
    document.querySelectorAll('.agenda-item').forEach((item) => item.addEventListener('click', () => openModal(state.events.find((event) => String(event.id) === item.dataset.id))));
}
function escapeHtml(value) { const div = document.createElement('div'); div.textContent = value || ''; return div.innerHTML; }
function openModal(event = null, selectedDate = dateKey(state.date)) { $('#eventForm').reset(); $('#eventId').value = event?.id || ''; $('#modalTitle').textContent = event ? 'Edit event' : 'New event'; $('#deleteButton').hidden = !event; $('#eventDate').value = event?.event_date || selectedDate; $('#title').value = event?.title || ''; $('#category').value = event?.category || 'work'; $('#location').value = event?.location || ''; $('#startTime').value = event?.start_time?.slice(0, 5) || ''; $('#endTime').value = event?.end_time?.slice(0, 5) || ''; $('#description').value = event?.description || ''; $('#formMessage').textContent = ''; $('#modalBackdrop').hidden = false; $('#title').focus(); }
function closeModal() { $('#modalBackdrop').hidden = true; }
async function saveEvent(event) { event.preventDefault(); const id = $('#eventId').value; const payload = Object.fromEntries(new FormData($('#eventForm'))); payload.color = { work:'#e85d3f', meeting:'#345467', personal:'#2d7f83', birthday:'#d79b32', holiday:'#8a6a9c' }[payload.category]; try { const response = await fetch(`${api}${id ? `?id=${id}` : ''}`, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }); const data = await response.json(); if (!response.ok) throw new Error(data.error); closeModal(); showToast(id ? 'Event updated.' : 'Event created.'); await loadEvents(); } catch (error) { $('#formMessage').textContent = error.message; } }
async function deleteEvent() { const id = $('#eventId').value; if (!id || !window.confirm('Delete this event?')) return; try { const response = await fetch(`${api}?id=${id}`, { method:'DELETE' }); if (!response.ok) throw new Error('Could not delete event.'); closeModal(); showToast('Event deleted.'); await loadEvents(); } catch (error) { $('#formMessage').textContent = error.message; } }
let toastTimer; function showToast(message) { clearTimeout(toastTimer); $('#toast').textContent = message; $('#toast').classList.add('show'); toastTimer = setTimeout(() => $('#toast').classList.remove('show'), 2600); }
$('#previousMonth').addEventListener('click', () => { state.date.setMonth(state.date.getMonth() - 1); loadEvents(); }); $('#nextMonth').addEventListener('click', () => { state.date.setMonth(state.date.getMonth() + 1); loadEvents(); }); $('#todayButton').addEventListener('click', () => { state.date = new Date(); loadEvents(); }); $('#newEventButton').addEventListener('click', () => openModal()); $('#closeModal').addEventListener('click', closeModal); $('#cancelButton').addEventListener('click', closeModal); $('#deleteButton').addEventListener('click', deleteEvent); $('#eventForm').addEventListener('submit', saveEvent); $('#searchInput').addEventListener('input', (event) => { state.query = event.target.value; render(); }); $('#categoryFilter').addEventListener('change', (event) => { state.category = event.target.value; render(); }); $('#modalBackdrop').addEventListener('click', (event) => { if (event.target.id === 'modalBackdrop') closeModal(); });
loadEvents();
