# Routes

Laravel outes/web.php. Auth via Breeze. Authenticated group uses x-app-layout (sidebar + topbar).

| Path | Name | View |
| --- | --- | --- |
| / | - | redirects to dashboard if auth else login |
| /login | login | resources/views/auth/login.blade.php (standalone, no guest layout) |
| /dashboard | dashboard | resources/views/dashboard.blade.php |
| /reservations | reservations.index | resources/views/reservations/index.blade.php |
| /front-desk/check-in | front-desk.check-in | resources/views/front-desk/check-in.blade.php |
| /front-desk/check-out | front-desk.check-out | resources/views/front-desk/check-out.blade.php |
| /rooms | rooms.index | resources/views/rooms/index.blade.php |
| /guests | guests.index | resources/views/guests/index.blade.php |
| /housekeeping | housekeeping.index | resources/views/housekeeping/index.blade.php |
| /restaurant/pos | restaurant.pos | resources/views/restaurant/pos.blade.php |
| /finance/invoices | finance.invoices | resources/views/finance/invoices.blade.php |
| /settings | settings.index | resources/views/settings/index.blade.php |

Layout: esources/views/components/app-layout.blade.php includes layouts.sidebar + layouts.topbar.
Login is a full HTML document in uth/login.blade.php.
