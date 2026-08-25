<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DJ FRED | Event Calendar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="index.php"><span class="brand-mark">D</span><span>DJ FRED</span></a>
            <nav class="main-nav" aria-label="Main navigation">
                <a class="nav-link active" href="index.php"><span class="nav-icon">▦</span> Calendar</a>
                <a class="nav-link" href="#agenda"><span class="nav-icon">☷</span> Agenda</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="mini-note"><span class="status-dot"></span><div><strong>Workspace ready</strong><small>Local calendar</small></div></div>
                <p class="sidebar-foot">Dj Fred at your service<br> Get Fun and Happiness.</p>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div><p class="eyebrow">Your time, in focus</p><h1>Calendar</h1></div>
                <div class="top-actions"><button class="icon-button" id="todayButton" title="Jump to today">Today</button><button class="primary-button" id="newEventButton"><span>+</span> New event</button></div>
            </header>

            <section class="content-grid">
                <div class="calendar-panel">
                    <div class="calendar-toolbar"><div class="month-heading"><button class="round-button" id="previousMonth" aria-label="Previous month">‹</button><h2 id="monthLabel"></h2><button class="round-button" id="nextMonth" aria-label="Next month">›</button></div><div class="calendar-tools"><label class="search-box"><span>⌕</span><input id="searchInput" type="search" placeholder="Search events"></label><select id="categoryFilter" aria-label="Filter by category"><option value="all">All categories</option><option value="work">Work</option><option value="meeting">Meetings</option><option value="personal">Personal</option><option value="birthday">Birthdays</option><option value="holiday">Holidays</option></select></div></div>
                    <div class="weekday-row"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
                <aside class="agenda-panel" id="agenda"><div class="agenda-heading"><div><p class="eyebrow">Coming up</p><h2>Agenda</h2></div><span class="event-count" id="eventCount">0 events</span></div><div id="agendaList" class="agenda-list"></div></aside>
            </section>
        </main>
    </div>

    <div class="modal-backdrop" id="modalBackdrop" hidden><section class="event-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle"><div class="modal-header"><div><p class="eyebrow">Calendar entry</p><h2 id="modalTitle">New event</h2></div><button class="close-button" id="closeModal" aria-label="Close">×</button></div><form id="eventForm"><input type="hidden" id="eventId"><div class="form-row"><label>Event name<input id="title" name="title" required maxlength="150" placeholder="Give it a clear name"></label><label>Category<select id="category" name="category"><option value="work">Work</option><option value="meeting">Meeting</option><option value="personal">Personal</option><option value="birthday">Birthday</option><option value="holiday">Holiday</option></select></label></div><div class="form-row"><label>Date<input id="eventDate" name="event_date" type="date" required></label><label>Location<input id="location" name="location" placeholder="Where is it?"></label></div><div class="form-row"><label>Start time<input id="startTime" name="start_time" type="time"></label><label>End time<input id="endTime" name="end_time" type="time"></label></div><label>Notes<textarea id="description" name="description" rows="3" placeholder="Add a little context"></textarea></label><div class="modal-footer"><button type="button" class="text-button danger" id="deleteButton" hidden>Delete event</button><span class="form-message" id="formMessage"></span><button type="button" class="text-button" id="cancelButton">Cancel</button><button class="primary-button" type="submit">Save event</button></div></form></section></div>
    <div class="toast" id="toast" role="status"></div>
    <script src="assets/app.js"></script>
</body>
</html>
