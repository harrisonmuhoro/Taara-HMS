# Pages (key UI)

## /login
Entry: resources/views/auth/login.blade.php
Dependencies:
- resources/css/app.css
- resources/views/components/auth-session-status.blade.php
- resources/views/components/input-error.blade.php

## /dashboard
Entry: resources/views/dashboard.blade.php
Dependencies:
- resources/views/components/app-layout.blade.php
  - resources/views/layouts/sidebar.blade.php
    - resources/views/components/nav-link.blade.php
  - resources/views/layouts/topbar.blade.php
- resources/views/components/breadcrumb.blade.php
- resources/views/components/alert.blade.php
- resources/views/components/status-badge.blade.php
- resources/css/app.css

## /reservations
Entry: resources/views/reservations/index.blade.php
Dependencies:
- resources/views/components/app-layout.blade.php (same shell as dashboard)
- resources/views/components/breadcrumb.blade.php
- resources/views/components/alert.blade.php
- resources/views/components/status-badge.blade.php

## /front-desk/check-in
Entry: resources/views/front-desk/check-in.blade.php
Dependencies:
- resources/views/components/app-layout.blade.php
- resources/views/components/breadcrumb.blade.php
- resources/views/components/alert.blade.php

## /front-desk/check-out
Entry: resources/views/front-desk/check-out.blade.php
Dependencies: same as check-in

## /rooms
Entry: resources/views/rooms/index.blade.php
Dependencies:
- resources/views/components/app-layout.blade.php
- resources/views/components/breadcrumb.blade.php
