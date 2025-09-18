@extends('layouts.app')

@section('title', 'تقویم')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 mb-1">تقویم</h1>
        <p class="text-muted mb-0">تقویم شمسی با مناسبت‌ها و تعطیلات رسمی ایران</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary" id="prevMonth">
                <i class="bi bi-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-outline-primary" id="currentMonth">
                امروز
            </button>
            <button type="button" class="btn btn-outline-secondary" id="nextMonth">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" id="monthYearDisplay">
            <i class="bi bi-calendar3 me-1"></i>
            <span id="currentMonthYear">فروردین ۱۴۰۴</span>
        </button>
    </div>
</div>

<!-- تقویم -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row text-center mb-3" id="calendar-header">
            <div class="col fw-bold">شنبه</div>
            <div class="col fw-bold">یک‌شنبه</div>
            <div class="col fw-bold">دوشنبه</div>
            <div class="col fw-bold">سه‌شنبه</div>
            <div class="col fw-bold">چهارشنبه</div>
            <div class="col fw-bold">پنج‌شنبه</div>
            <div class="col fw-bold">جمعه</div>
        </div>
        <hr>
        <div class="calendar-grid" id="calendar-grid">
            <!-- تقویم به صورت داینامیک توسط JavaScript پر می‌شود -->
        </div>
    </div>
</div>

<!-- اطلاعات روز انتخاب شده -->
<div class="card border-0 shadow-sm mt-4" id="dayDetails" style="display: none;">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0" id="selectedDateTitle">۱ فروردین ۱۴۰۴</h5>
        <button type="button" class="btn btn-light btn-sm" id="addNoteBtn">
            <i class="bi bi-plus-circle me-1"></i>
            افزودن یادداشت
        </button>
    </div>
    <div class="card-body">
        <div id="dayEvents">
            <!-- رویدادها و مناسبت‌ها به صورت داینامیک اضافه می‌شوند -->
        </div>
    </div>
</div>

<!-- Modal for adding notes/reminders -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">افزودن یادداشت/یادآور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noteForm">
                    <input type="hidden" id="noteDate" value="">
                    <div class="mb-3">
                        <label for="noteTitle" class="form-label">عنوان</label>
                        <input type="text" class="form-control" id="noteTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="noteDescription" class="form-label">توضیحات</label>
                        <textarea class="form-control" id="noteDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="noteTime" class="form-label">ساعت (اختیاری)</label>
                        <input type="text" class="form-control" id="noteTime" placeholder="مثال: ۱۴:۳۰">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="isReminder">
                        <label class="form-check-label" for="isReminder">یادآور باشد</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="button" class="btn btn-primary" id="saveNoteBtn">ذخیره</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day {
    min-height: 120px;
    border-radius: 8px;
    padding: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
}

.calendar-day:hover {
    background: #e9f7fe;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.calendar-day.other-month {
    background: #f0f0f0;
    color: #adb5bd;
}

.calendar-day.today {
    background: #d1ecf1;
    border-color: #0dcaf0;
}

.calendar-day.holiday {
    background: #fff3f3;
    border-color: #dc3545;
}

.calendar-day.holiday.today {
    background: #f8d7da;
}

.calendar-day-number {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 4px;
    align-self: flex-start;
}

.calendar-day-events {
    font-size: 0.75rem;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    flex-grow: 1;
}

.calendar-day.holiday .calendar-day-number {
    color: #dc3545;
}

.event-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 0.7rem;
    margin-bottom: 2px;
    background: #0d6efd;
    color: white;
}

.holiday-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 0.7rem;
    margin-bottom: 2px;
    background: #dc3545;
    color: white;
}

.note-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 0.7rem;
    margin-bottom: 2px;
    background: #ffc107;
    color: #000;
}

.reminder-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 0.7rem;
    margin-bottom: 2px;
    background: #28a745;
    color: white;
}

@media (max-width: 768px) {
    .calendar-grid {
        gap: 4px;
    }
    
    .calendar-day {
        min-height: 80px;
        padding: 4px;
    }
    
    .calendar-day-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// متغیرهای سراسری
let currentMonth = parseInt("{{ verta()->month }}");
let currentYear = parseInt("{{ verta()->year }}");
let selectedDate = null;
let monthEvents = {};
let monthHolidays = {};

// اطلاعات ماه‌ها
const persianMonths = [
    'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
    'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
];

// اطلاعات روزهای هفته
const persianWeekDays = [
    'شنبه', 'یک‌شنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'
];

// تابع برای تبدیل اعداد انگلیسی به فارسی
function toPersianDigits(input) {
    const persianDigitMap = {
        '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴',
        '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹'
    };
    return String(input).replace(/[0-9]/g, d => persianDigitMap[d]);
}

// تابع برای تبدیل اعداد فارسی به انگلیسی
function toEnglishDigits(input) {
    const englishDigitMap = {
        '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
        '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
    };
    return String(input).replace(/[۰-۹]/g, d => englishDigitMap[d]);
}

// تابع برای دریافت اولین روز ماه (شنبه = 0, جمعه = 6)
function getFirstDayOfMonth(year, month) {
    // استفاده از کتابخانه Verta برای محاسبه دقیق
    try {
        // اولین روز ماه شمسی
        const firstDay = verta(year, month, 1);
        // دریافت روز هفته (شنبه = 0, جمعه = 6)
        // format('w') returns 0 for Saturday, 1 for Sunday, ..., 6 for Friday
        let dayIndex = parseInt(firstDay.format('w'));
        
        // No shift needed - use the actual day of week from Verta
        // dayIndex = (dayIndex + 3) % 7;
        
        return dayIndex;
    } catch (e) {
        console.error('Error calculating first day of month:', e);
        // در صورت بروز خطا، مقدار تقریبی برگردانده می‌شود
        return (month + year) % 7;
    }
}

// تابع برای دریافت تعداد روزهای ماه
function getDaysInMonth(year, month) {
    // تعداد روزهای هر ماه در تقویم شمسی
    const daysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    
    // برای سال کبیسه ماه اسفند 30 روز دارد
    if (isLeapYear(year) && month === 12) {
        return 30;
    }
    
    return daysInMonth[month - 1];
}

// تابع برای بررسی سال کبیسه
function isLeapYear(year) {
    // الگوریتم ساده برای تشخیص سال کبیسه شمسی
    return (year % 33 % 4 === 1);
}

// تابع برای دریافت رویدادهای ماه از API
async function fetchMonthEvents(year, month) {
    try {
        const response = await fetch(`/calendar/events/${year}/${month}`);
        if (response.ok) {
            const data = await response.json();
            monthEvents = data.events || {};
            monthHolidays = data.holidays || {};
            return { events: monthEvents, holidays: monthHolidays };
        }
    } catch (error) {
        console.error('Error fetching calendar events:', error);
    }
    return { events: {}, holidays: {} };
}

// تابع برای دریافت اطلاعات روز از API
async function fetchDayEvents(year, month, day) {
    try {
        const response = await fetch(`/calendar/day/${year}/${month}/${day}`);
        if (response.ok) {
            const data = await response.json();
            return data;
        }
    } catch (error) {
        console.error('Error fetching day events:', error);
    }
    return { events: [], holidays: [] };
}

// تابع برای ذخیره یادداشت
async function saveNote(date, title, description, time, isReminder) {
    try {
        const response = await fetch('/calendar/note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                date: date,
                title: title,
                description: description,
                time: time,
                is_reminder: isReminder
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // نمایش پیام موفقیت
            showSuccessMessage(data.message);
            // بستن مودال
            bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
            // به‌روزرسانی نمایش روز
            if (selectedDate) {
                selectDay(selectedDate.day, selectedDate.month, selectedDate.year, selectedDate.isOtherMonth);
            }
            // به‌روزرسانی تقویم
            renderCalendar();
        } else {
            alert(data.message || 'خطا در ذخیره یادداشت');
        }
    } catch (error) {
        console.error('Error saving note:', error);
        alert('خطا در ذخیره یادداشت');
    }
}

// تابع برای حذف یادداشت
async function deleteNote(noteId) {
    if (!confirm('آیا از حذف این یادداشت اطمینان دارید؟')) {
        return;
    }
    
    try {
        const response = await fetch('/calendar/note/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: noteId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // نمایش پیام موفقیت
            showSuccessMessage(data.message);
            // به‌روزرسانی نمایش روز
            if (selectedDate) {
                selectDay(selectedDate.day, selectedDate.month, selectedDate.year, selectedDate.isOtherMonth);
            }
            // به‌روزرسانی تقویم
            renderCalendar();
        } else {
            alert(data.message || 'خطا در حذف یادداشت');
        }
    } catch (error) {
        console.error('Error deleting note:', error);
        alert('خطا در حذف یادداشت');
    }
}

// تابع نمایش پیام موفقیت
function showSuccessMessage(message) {
    // Generate a unique ID for this alert
    const alertId = 'success-alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-success alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.appendChild(document.createElement('div')).innerHTML = alertHtml;
    
    // حذف خودکار بعد از 3 ثانیه
    setTimeout(function() {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.remove();
        }
    }, 3000);
}

// تابع برای نمایش تقویم
async function renderCalendar() {
    const calendarGrid = document.getElementById('calendar-grid');
    const currentMonthYear = document.getElementById('currentMonthYear');
    
    // به‌روزرسانی نمایش ماه و سال
    currentMonthYear.textContent = `${persianMonths[currentMonth - 1]} ${toPersianDigits(currentYear)}`;
    
    // دریافت رویدادهای ماه
    const { events, holidays } = await fetchMonthEvents(currentYear, currentMonth);
    monthEvents = events;
    monthHolidays = holidays;
    
    // پاک کردن تقویم قبلی
    calendarGrid.innerHTML = '';
    
    // دریافت اولین روز ماه و تعداد روزها
    const firstDay = getFirstDayOfMonth(currentYear, currentMonth);
    const daysInMonth = getDaysInMonth(currentYear, currentMonth);
    
    // دریافت ماه قبلی برای نمایش روزهای اول ماه
    let prevMonth = currentMonth - 1;
    let prevYear = currentYear;
    if (prevMonth < 1) {
        prevMonth = 12;
        prevYear--;
    }
    const daysInPrevMonth = getDaysInMonth(prevYear, prevMonth);
    
    // ایجاد آرایه برای نگهداری تمام روزهای تقویم (42 روز برای 6 هفته)
    const calendarDays = Array(42).fill(null);
    
    // پر کردن روزهای ماه قبلی
    for (let i = 0; i < firstDay; i++) {
        const day = daysInPrevMonth - firstDay + i + 1;
        calendarDays[i] = { day, month: prevMonth, year: prevYear, isOtherMonth: true };
    }
    
    // پر کردن روزهای ماه جاری
    const today = new Date();
    const todayPersian = {
        year: parseInt('{{ verta()->year }}'),
        month: parseInt('{{ verta()->month }}'),
        day: parseInt('{{ verta()->day }}')
    };
    
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = (day === todayPersian.day && currentMonth === todayPersian.month && currentYear === todayPersian.year);
        const index = firstDay + day - 1;
        calendarDays[index] = { day, month: currentMonth, year: currentYear, isOtherMonth: false, isToday };
    }
    
    // پر کردن روزهای ماه بعدی
    let nextMonth = currentMonth + 1;
    let nextYear = currentYear;
    if (nextMonth > 12) {
        nextMonth = 1;
        nextYear++;
    }
    
    const totalCells = 42;
    const remainingCells = Math.max(0, totalCells - firstDay - daysInMonth);
    for (let day = 1; day <= remainingCells; day++) {
        const index = firstDay + daysInMonth + day - 1;
        calendarDays[index] = { day, month: nextMonth, year: nextYear, isOtherMonth: true };
    }
    
    // ایجاد عناصر برای هر روز و اضافه کردن به تقویم
    for (let i = 0; i < calendarDays.length; i++) {
        const dayInfo = calendarDays[i];
        if (dayInfo) {
            const dayElement = createDayElement(dayInfo.day, dayInfo.month, dayInfo.year, dayInfo.isOtherMonth, dayInfo.isToday);
            calendarGrid.appendChild(dayElement);
        } else {
            // ایجاد یک عنصر خالی برای روزهای بدون داده
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day';
            calendarGrid.appendChild(emptyDay);
        }
    }
}

// تابع برای ایجاد عنصر روز
function createDayElement(day, month, year, isOtherMonth, isToday = false) {
    const dayElement = document.createElement('div');
    dayElement.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayElement.classList.add('other-month');
    }
    
    if (isToday) {
        dayElement.classList.add('today');
    }
    
    // بررسی تعطیل بودن روز
    const dateKey = `${year}-${month}-${day}`;
    const isHoliday = monthHolidays[dateKey] || false;
    
    // اضافه کردن کلاس تعطیل برای روزهای جمعه یا تعطیلات
    try {
        const vertaDay = verta(year, month, day);
        const dayOfWeek = parseInt(vertaDay.format('w')); // 0 = Saturday, 6 = Friday
        // No adjustment needed - use actual day of week
        // const adjustedDayOfWeek = (dayOfWeek + 3) % 7;
        if (dayOfWeek === 6 || isHoliday) { // جمعه یا تعطیل رسمی
            dayElement.classList.add('holiday');
        }
    } catch (e) {
        // Fallback for any errors
        if (isHoliday) {
            dayElement.classList.add('holiday');
        }
    }
    
    // اضافه کردن شماره روز
    const dayNumber = document.createElement('div');
    dayNumber.className = 'calendar-day-number';
    dayNumber.textContent = toPersianDigits(day);
    dayElement.appendChild(dayNumber);
    
    // اضافه کردن رویدادها
    const eventsContainer = document.createElement('div');
    eventsContainer.className = 'calendar-day-events';
    
    // اضافه کردن مناسبت‌های روز
    const dateKeyFormatted = `${year}-${month}-${day}`;
    if (monthEvents[dateKeyFormatted]) {
        monthEvents[dateKeyFormatted].slice(0, 2).forEach(event => {
            const eventBadge = document.createElement('span');
            eventBadge.className = 'event-badge';
            
            // Check if it's a holiday
            if (event.is_holiday) {
                eventBadge.className = 'holiday-badge';
            }
            
            const title = event.title || event.description || 'مناسبت';
            eventBadge.textContent = title.length > 10 ? title.substring(0, 10) + '...' : title;
            eventBadge.title = title;
            eventsContainer.appendChild(eventBadge);
        });
    }
    
    // اضافه کردن تعطیلات
    if (isHoliday) {
        const holidayBadge = document.createElement('span');
        holidayBadge.className = 'holiday-badge';
        holidayBadge.textContent = 'تعطیل';
        eventsContainer.appendChild(holidayBadge);
    } else {
        // Add Friday badge if it's Friday (actual position)
        try {
            const vertaDay = verta(year, month, day);
            const dayOfWeek = parseInt(vertaDay.format('w')); // 0 = Saturday, 6 = Friday
            // No adjustment needed - use actual day of week
            // const adjustedDayOfWeek = (dayOfWeek + 3) % 7;
            if (dayOfWeek === 6) { // جمعه (actual)
                const holidayBadge = document.createElement('span');
                holidayBadge.className = 'holiday-badge';
                holidayBadge.textContent = 'جمعه';
                eventsContainer.appendChild(holidayBadge);
            }
        } catch (e) {
            // Ignore errors in this case
        }
    }
    
    dayElement.appendChild(eventsContainer);
    
    // اضافه کردن event listener
    dayElement.addEventListener('click', () => selectDay(day, month, year, isOtherMonth));
    
    return dayElement;
}

// تابع برای انتخاب روز
async function selectDay(day, month, year, isOtherMonth) {
    selectedDate = { day, month, year, isOtherMonth };
    
    // نمایش اطلاعات روز
    const dayDetails = document.getElementById('dayDetails');
    const selectedDateTitle = document.getElementById('selectedDateTitle');
    const dayEvents = document.getElementById('dayEvents');
    
    selectedDateTitle.textContent = `${toPersianDigits(day)} ${persianMonths[month - 1]} ${toPersianDigits(year)}`;
    
    // نمایش پیام بارگذاری
    dayEvents.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 mb-0">در حال بارگذاری اطلاعات...</p></div>';
    dayDetails.style.display = 'block';
    dayDetails.scrollIntoView({ behavior: 'smooth' });
    
    // دریافت اطلاعات روز از API
    const dayData = await fetchDayEvents(year, month, day);
    
    // پاک کردن محتوای قبلی
    dayEvents.innerHTML = '';
    
    // اضافه کردن مناسبت‌ها و رویدادها
    const eventsList = document.createElement('ul');
    eventsList.className = 'list-group';
    
    // اضافه کردن مناسبت‌ها و تعطیلات
    if (dayData.holidays && dayData.holidays.length > 0) {
        dayData.holidays.forEach(holiday => {
            const eventItem = document.createElement('li');
            eventItem.className = 'list-group-item';
            eventItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-calendar-x text-danger me-2"></i>
                        <strong>${holiday}</strong>
                    </div>
                    <span class="badge bg-danger">تعطیل</span>
                </div>
            `;
            eventsList.appendChild(eventItem);
        });
    }
    
    // اضافه کردن مناسبت‌های روز
    if (dayData.events && dayData.events.length > 0) {
        dayData.events.forEach(event => {
            const eventItem = document.createElement('li');
            eventItem.className = 'list-group-item';
            
            const title = event.title || event.description || 'مناسبت';
            const badgeClass = event.is_holiday ? 'bg-danger' : 'bg-primary';
            const type = event.is_holiday ? 'تعطیل' : 'مناسبت';
            
            eventItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-calendar-check text-primary me-2"></i>
                        <strong>${title}</strong>
                    </div>
                    <span class="badge ${badgeClass}">${type}</span>
                </div>
                ${event.description ? `<p class="mb-0 mt-2 text-muted">${event.description}</p>` : ''}
            `;
            eventsList.appendChild(eventItem);
        });
    }
    
    // اگر هیچ رویدادی وجود نداشت
    if (eventsList.children.length === 0) {
        const noEvents = document.createElement('div');
        noEvents.className = 'text-center py-4 text-muted';
        noEvents.innerHTML = `
            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">هیچ مناسبت یا تعطیلی برای این روز ثبت نشده است</p>
        `;
        dayEvents.appendChild(noEvents);
    } else {
        dayEvents.appendChild(eventsList);
    }
}

// تابع برای تغییر ماه
function changeMonth(direction) {
    currentMonth += direction;
    
    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    } else if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }
    
    renderCalendar();
}

// تابع برای رفتن به ماه جاری
function goToCurrentMonth() {
    currentYear = parseInt('{{ verta()->year }}');
    currentMonth = parseInt('{{ verta()->month }}');
    renderCalendar();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // تنظیم event listenerها
    document.getElementById('prevMonth').addEventListener('click', () => changeMonth(-1));
    document.getElementById('nextMonth').addEventListener('click', () => changeMonth(1));
    document.getElementById('currentMonth').addEventListener('click', goToCurrentMonth);
    
    // Event listener برای دکمه افزودن یادداشت
    document.getElementById('addNoteBtn').addEventListener('click', function() {
        if (!selectedDate) return;
        
        const dateStr = `${selectedDate.year}-${selectedDate.month}-${selectedDate.day}`;
        document.getElementById('noteDate').value = dateStr;
        document.getElementById('noteTitle').value = '';
        document.getElementById('noteDescription').value = '';
        document.getElementById('noteTime').value = '';
        document.getElementById('isReminder').checked = false;
        
        // نمایش مودال
        const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
        noteModal.show();
    });
    
    // Event listener برای دکمه ذخیره یادداشت
    document.getElementById('saveNoteBtn').addEventListener('click', function() {
        const date = document.getElementById('noteDate').value;
        const title = document.getElementById('noteTitle').value;
        const description = document.getElementById('noteDescription').value;
        const time = document.getElementById('noteTime').value;
        const isReminder = document.getElementById('isReminder').checked;
        
        if (!title) {
            alert('لطفا عنوان یادداشت را وارد کنید');
            return;
        }
        
        saveNote(date, title, description, time, isReminder);
    });
    
    // Event delegation for delete note buttons
    document.getElementById('dayEvents').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-note-btn') || e.target.closest('.remove-note-btn')) {
            const button = e.target.classList.contains('remove-note-btn') ? e.target : e.target.closest('.remove-note-btn');
            const noteId = button.getAttribute('data-id');
            deleteNote(noteId);
        }
    });
    
    // نمایش تقویم اولیه
    renderCalendar();
});
</script>
@endpush