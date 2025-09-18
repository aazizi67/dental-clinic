# Appointment System for Dr. Aliazizi's Dental Clinic

This is a separate appointment scheduling system built within the existing Laravel application for Dr. Aliazizi's dental clinic.

## Features

1. **Patient Booking System**
   - Simple form for patients to book initial consultation appointments
   - Persian/Shamsi calendar integration
   - Time slot selection

2. **Admin Panel**
   - Dashboard for managing appointments
   - Time slot management (add/remove available time slots)
   - View all appointments

3. **Persian Calendar Support**
   - Integration with Verta package for Persian date handling
   - Proper display of Persian dates in views

## Implementation Details

### Database Structure
- `appointments` table (existing, extended with new fields)
- `appointment_time_slots` table (new)

### Routes
All appointment system routes are prefixed with `/appointments`:
- `/appointments` - Main booking page
- `/appointments/booking` - Appointment booking form
- `/appointments/admin/dashboard` - Admin dashboard
- `/appointments/admin/appointments` - List all appointments
- `/appointments/admin/time-slots` - Manage time slots

### Controllers
- `SimpleAppointmentController` - Handles all appointment system functionality

### Views
- `resources/views/appointments/welcome.blade.php` - Main welcome page
- `resources/views/appointments/booking.blade.php` - Appointment booking form
- `resources/views/appointments/show.blade.php` - Appointment details
- `resources/views/appointments/admin/dashboard.blade.php` - Admin dashboard
- `resources/views/appointments/admin/index.blade.php` - List of appointments
- `resources/views/appointments/admin/time_slots.blade.php` - Time slot management

## Setup Instructions

1. Run migrations to create the appointment_time_slots table:
   ```
   php artisan migrate
   ```

2. Clear all caches:
   ```
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

3. Start the Laravel development server:
   ```
   php artisan serve
   ```

4. Access the appointment system at:
   - Booking page: http://localhost:8000/appointments
   - Admin panel: http://localhost:8000/appointments/admin/dashboard

## Troubleshooting

If you encounter issues with the development server:

1. Check if PHP is properly installed:
   ```
   php --version
   ```

2. Check if Composer is installed:
   ```
   composer --version
   ```

3. Try running on a different port:
   ```
   php artisan serve --port=8080
   ```

4. Check for port conflicts:
   ```
   lsof -i :8000
   lsof -i :8080
   ```

5. If the built-in server doesn't work, you can use a web server like Apache or Nginx with proper configuration.