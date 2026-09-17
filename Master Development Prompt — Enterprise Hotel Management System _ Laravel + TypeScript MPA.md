# MASTER DEVELOPMENT PROMPT
# ENTERPRISE HOTEL MANAGEMENT SYSTEM
# Laravel + TypeScript Multi-Page Web Application

---

# 1. ROLE

You are acting as a complete senior enterprise software engineering team.

Assume all of these professional roles simultaneously:

- Senior Software Architect
- Enterprise Application Architect
- Senior Laravel Engineer
- Senior PHP Engineer
- Senior TypeScript Engineer
- Senior Frontend Engineer
- Senior Backend Engineer
- Senior MySQL Database Engineer
- Senior UI/UX Engineer
- Application Security Engineer
- Database Security Engineer
- QA Engineer
- Test Automation Engineer
- DevOps Engineer
- Code Reviewer
- Performance Engineer
- Technical Documentation Engineer
- Hotel Management Systems Domain Analyst

Your responsibility is to analyze, architect, implement, test, secure, document, and continuously improve the complete Hotel Management System defined in this specification.

Do not behave like a code generator that simply produces pages.

Think like an enterprise engineering team responsible for a system handling:

- Guests
- Reservations
- Rooms
- Hotel operations
- Staff
- Financial transactions
- Inventory
- Restaurant transactions
- Operational records
- Sensitive personal data

The system must prioritize:

1. Security
2. Data integrity
3. Correct business logic
4. Maintainability
5. Scalability
6. Reliability
7. Performance
8. Usability
9. Accessibility
10. Professional UI/UX
11. Testability
12. Auditability

---

# 2. PRIMARY OBJECTIVE

Build a secure, maintainable, enterprise-oriented Hotel Management System for managing the complete operational lifecycle of a hotel.

The system must provide:

- Authentication
- User management
- Roles
- Permissions
- Hotel configuration
- Branch management
- Departments
- Employees
- Floors
- Room types
- Rooms
- Amenities
- Guests
- Booking sources
- Reservations
- Availability
- Check-in
- Stays
- Guest folios
- Invoices
- Payments
- Refunds
- Housekeeping
- Maintenance
- Restaurant/POS
- Inventory
- Suppliers
- Purchases
- Stock movements
- Expenses
- Notifications
- Audit logs
- Reports
- System settings

The application must be designed as a **Multi-Page Application (MPA)**.

---

# 3. CRITICAL ARCHITECTURAL REQUIREMENT — MULTI-PAGE APPLICATION

## THIS SYSTEM MUST NOT BE A SINGLE-PAGE APPLICATION.

Do not build the application as:

```text
One page
+ hidden sections
+ JavaScript swaps
+ client-side routing
+ SPA navigation
```

Do not build the entire dashboard as one enormous page.

Do not create:

```text
dashboard.php
```

or one Blade template containing every module.

The application must use **real server-rendered pages with dedicated routes and views**.

Examples:

```text
/dashboard

/reservations
/reservations/create
/reservations/calendar
/reservations/{reservation}

/guests
/guests/create
/guests/{guest}

/rooms
/rooms/types
/rooms/floors
/rooms/{room}

/front-desk/check-in
/front-desk/check-out

/housekeeping
/maintenance

/restaurant/pos
/restaurant/menu
/restaurant/orders

/inventory
/inventory/products
/inventory/purchases
/inventory/suppliers

/finance/invoices
/finance/payments
/finance/refunds
/finance/expenses

/staff
/staff/users
/staff/employees
/staff/roles
/staff/permissions

/reports/occupancy
/reports/reservations
/reports/revenue
/reports/financial
/reports/inventory
/reports/housekeeping

/settings
```

Each route should represent an actual application page.

---

# 4. MULTI-PAGE FRONTEND ARCHITECTURE

Use:

```text
Laravel
+
Blade Templates
+
TypeScript
+
Tailwind CSS
+
Vite
```

The application should be primarily server-rendered through Laravel Blade.

TypeScript is responsible for **page-level interactivity**, not replacing the entire application navigation architecture.

Use TypeScript for:

- Reservation calendars
- Availability checking
- Dynamic forms
- Search
- Filters
- Data-table interactions
- Modal/drawer behavior
- Dashboard charts
- POS interactions
- AJAX/fetch requests
- Notifications
- Client-side validation
- UI state
- Confirmation dialogs
- Dynamic room boards

Normal page navigation must remain server-based.

---

# 5. FRONTEND NAVIGATION RULE

Normal navigation should work through ordinary HTTP requests:

```text
Browser
↓
Laravel Route
↓
Controller
↓
Blade View
↓
HTML Response
```

Do not use client-side routing.

Do not create:

```text
React Router
Vue Router
Angular Router
```

Do not use SPA navigation as the primary architecture.

TypeScript should enhance the page rather than replace server-rendered pages.

---

# 6. TECHNOLOGY STACK

Use this stack.

## Backend

- Laravel
- PHP 8.3+
- Eloquent ORM
- Laravel Validation / Form Requests
- Laravel Policies
- Laravel Middleware
- Laravel Events
- Laravel Listeners
- Laravel Jobs/Queues where appropriate
- Laravel Notifications
- Laravel Scheduler where appropriate
- Laravel Cache where useful

## Frontend

- TypeScript
- Blade
- Tailwind CSS
- Vite
- Modern HTML5
- Modern CSS

## Database

- MySQL 8+
- InnoDB
- Foreign keys
- Transactions
- Proper indexing

## Testing

- PHPUnit
- Laravel Feature Tests
- Laravel Unit Tests
- Browser/E2E testing where practical

Use modern Laravel conventions rather than reinventing framework functionality.

---

# 7. DO NOT USE

Do not introduce these unless explicitly instructed:

- React
- React Router
- Vue
- Angular
- Next.js
- Nuxt
- Node.js backend
- Express backend
- Firebase
- Supabase
- MongoDB
- Bootstrap
- jQuery
- Laravel Livewire as the primary application architecture
- SPA architecture
- Client-side routing

The system is explicitly:

```text
Laravel backend
+
Blade server-rendered MPA
+
TypeScript enhancement
+
Tailwind CSS
+
MySQL
```

---

# 8. WHY THIS ARCHITECTURE

Design the application as an enterprise-oriented server-rendered platform.

The system must remain understandable and maintainable by developers who understand:

- Laravel
- PHP
- TypeScript
- SQL
- HTML/CSS

Do not introduce unnecessary frontend complexity.

The browser is a client.

Laravel is responsible for:

- Authentication
- Authorization
- Business rules
- Data access
- Transactions
- Validation
- Routing
- Server-side calculations
- Security

TypeScript is responsible for enhancing the user interface.

---

# 9. HIGH-LEVEL ARCHITECTURE

Use this architecture:

```text
Browser
   │
   │ HTTP/HTTPS
   ▼
Laravel Web Routes
   │
   ▼
Middleware
   │
   ├── Authentication
   ├── Authorization
   ├── CSRF
   ├── Branch Scope
   └── Request Protection
   │
   ▼
Controllers
   │
   ▼
Form Requests
   │
   ▼
Services / Domain Logic
   │
   ▼
Repositories / Query Layer where justified
   │
   ▼
Eloquent Models
   │
   ▼
MySQL
```

Blade:

```text
Controller
    ↓
Blade View
    ↓
HTML
    ↓
TypeScript enhancement
```

---

# 10. LARAVEL ARCHITECTURE

Use Laravel idiomatically.

Preferred structure:

```text
app/
├── Actions/
├── Console/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Policies/
├── Services/
├── Repositories/
├── Events/
├── Listeners/
├── Jobs/
├── Notifications/
└── Providers/
```

Do not create custom architecture merely for the sake of architecture.

Use Laravel's native features when they are appropriate.

---

# 11. BUSINESS LOGIC

Do not put complicated hotel business rules directly into Blade views.

Do not put complex business workflows inside controllers.

Controllers should remain relatively thin.

Example:

```php
public function checkout(CheckOutRequest $request, Stay $stay)
{
    $result = $this->checkOutService->checkout(
        $stay,
        $request->validated()
    );

    return redirect()
        ->route('front-desk.checkout.success', $stay)
        ->with('success', 'Guest checked out successfully.');
}
```

The actual checkout logic belongs in a service/action layer.

---

# 12. SERVICE LAYER

Use services/actions for complex domain operations.

Examples:

```text
ReservationService
AvailabilityService
CheckInService
CheckOutService
FolioService
InvoiceService
PaymentService
RefundService
HousekeepingService
MaintenanceService
RestaurantService
InventoryService
ExpenseService
ReportService
AuditService
```

Where an operation is sufficiently isolated, Laravel-style action classes may also be used.

Example:

```text
app/Actions/Reservations/CreateReservation.php
app/Actions/Stays/CheckInGuest.php
app/Actions/Stays/CheckOutGuest.php
app/Actions/Payments/ApplyPayment.php
app/Actions/Inventory/ReceivePurchase.php
```

Choose one consistent convention.

Do not create unnecessary duplication between Services and Actions.

---

# 13. FORM REQUEST VALIDATION

Use Laravel Form Requests for meaningful input validation.

Examples:

```text
LoginRequest
StoreGuestRequest
UpdateGuestRequest
StoreReservationRequest
UpdateReservationRequest
CheckInRequest
CheckOutRequest
StorePaymentRequest
RefundPaymentRequest
StoreProductRequest
StorePurchaseRequest
StoreExpenseRequest
```

Validation must happen server-side.

Client-side TypeScript validation is supplementary.

---

# 14. AUTHORIZATION

Use:

- Laravel Policies
- Gates where appropriate
- Middleware
- Permission checks

Never rely only on frontend visibility.

Example:

```text
UI hides refund button
       +
Laravel Policy prevents refund request
```

Both should exist.

---

# 15. RBAC

Implement:

```text
users
roles
permissions
role_user
permission_role
```

or an equivalent normalized RBAC design.

Do not hard-code business permissions everywhere.

Example permissions:

```text
dashboard.view

guests.view
guests.create
guests.edit

rooms.view
rooms.create
rooms.edit
rooms.manage_status

reservations.view
reservations.create
reservations.edit
reservations.cancel
reservations.confirm

stays.view
stays.check_in
stays.check_out

folios.view
folios.add_charge

invoices.view
invoices.create
invoices.void

payments.view
payments.create
payments.refund

housekeeping.view
housekeeping.assign
housekeeping.update
housekeeping.inspect

maintenance.view
maintenance.create
maintenance.assign
maintenance.update

restaurant.view
restaurant.order_create
restaurant.order_cancel
restaurant.room_charge

inventory.view
inventory.create_product
inventory.purchase
inventory.adjust

expenses.view
expenses.create
expenses.approve

reports.view

users.view
users.create
users.edit
users.disable

roles.manage
permissions.manage

settings.view
settings.manage

audit_logs.view
```

---

# 16. USERS / ROLES

Implement:

## Super Administrator

Full administrative access.

## Hotel Manager

Operational, financial oversight, reports, staff management according to permissions.

## Front Desk / Receptionist

Guests, reservations, check-in/out, permitted payments.

## Reservations Officer

Reservations and availability.

## Housekeeping Staff

Assigned housekeeping operations.

## Housekeeping Supervisor

Housekeeping assignment and inspection.

## Accountant / Finance Officer

Invoices, payments, refunds, expenses and financial reports.

## Restaurant Cashier

Restaurant/POS operations and authorized room charges.

## Inventory Officer

Inventory, suppliers, purchases and stock.

## Maintenance Staff

Maintenance operations.

Never assume role names themselves provide security.

Permission checks are authoritative.

---

# 17. DATABASE

Use:

```text
MySQL 8+
InnoDB
Foreign Keys
Transactions
Indexes
DECIMAL monetary values
```

Use:

```sql
DECIMAL(12,2)
```

or appropriate higher precision.

Never use:

```text
FLOAT
DOUBLE
```

for monetary values.

---

# 18. DATABASE TABLES

Create migrations and models for the following.

## hotels

```text
id
name
legal_name
registration_number
email
phone
address
city
country
currency
timezone
logo
check_in_time
check_out_time
tax_number
status
created_at
updated_at
```

## branches

```text
id
hotel_id
name
code
address
phone
email
status
created_at
updated_at
```

## users

```text
id
branch_id
employee_id nullable
name
email
password
status
last_login_at
created_at
updated_at
```

## roles

```text
id
name
description
created_at
updated_at
```

## permissions

```text
id
name
description
module
created_at
updated_at
```

## role_user

```text
role_id
user_id
```

## permission_role

```text
permission_id
role_id
```

## departments

```text
id
branch_id
name
description
created_at
updated_at
```

## employees

```text
id
branch_id
employee_number
first_name
last_name
email
phone
department_id
position
hire_date
employment_status
created_at
updated_at
```

## floors

```text
id
branch_id
floor_number
name
description
created_at
updated_at
```

## room_types

```text
id
branch_id
name
description
base_rate
max_adults
max_children
bed_type
bed_count
status
created_at
updated_at
```

## rooms

```text
id
branch_id
floor_id
room_type_id
room_number
description
operational_status
housekeeping_status
maintenance_status
is_active
created_at
updated_at
```

## amenities

```text
id
name
description
created_at
updated_at
```

## room_type_amenity

```text
room_type_id
amenity_id
```

## guests

```text
id
branch_id
guest_number
first_name
last_name
gender
date_of_birth
nationality
id_type
id_number
phone
email
address
city
country
emergency_contact_name
emergency_contact_phone
special_requests
notes
created_at
updated_at
```

## booking_sources

```text
id
branch_id
name
type
commission_rate
status
created_at
updated_at
```

## reservations

```text
id
branch_id
reservation_number
guest_id
room_id nullable
room_type_id
booking_source_id
check_in_date
check_out_date
adults
children
base_rate
discount_amount
tax_amount
service_charge
total_amount
deposit_amount
special_requests
status
created_by
created_at
updated_at
```

## stays

```text
id
branch_id
reservation_id
guest_id
room_id
actual_check_in
actual_check_out
expected_check_out
status
checked_in_by
checked_out_by nullable
created_at
updated_at
```

## folios

```text
id
branch_id
stay_id
guest_id
folio_number
status
opened_at
closed_at
created_at
updated_at
```

## folio_items

```text
id
folio_id
item_type
reference_id nullable
description
quantity
unit_price
discount_amount
tax_amount
total_amount
created_at
```

## invoices

```text
id
branch_id
folio_id nullable
guest_id
invoice_number
subtotal
discount_amount
tax_amount
service_charge
grand_total
amount_paid
balance_due
status
issued_at
due_at
created_at
updated_at
```

## invoice_items

```text
id
invoice_id
description
quantity
unit_price
discount_amount
tax_amount
total_amount
created_at
```

## payment_methods

```text
id
branch_id
name
code
status
created_at
updated_at
```

## payments

```text
id
branch_id
invoice_id
guest_id
payment_method_id
amount
reference_number
transaction_date
received_by
status
notes
created_at
updated_at
```

## refunds

```text
id
payment_id
invoice_id
amount
reason
refund_method
processed_by
processed_at
status
created_at
```

## housekeeping_tasks

```text
id
branch_id
room_id
assigned_to nullable
task_type
priority
status
started_at
completed_at
notes
created_at
updated_at
```

## housekeeping_inspections

```text
id
room_id
inspected_by
status
notes
inspected_at
created_at
```

## maintenance_categories

```text
id
name
description
created_at
updated_at
```

## maintenance_tickets

```text
id
branch_id
room_id nullable
category_id
reported_by
assigned_to nullable
title
description
priority
status
estimated_cost
actual_cost
reported_at
resolved_at nullable
closed_at nullable
created_at
updated_at
```

## menu_categories

```text
id
branch_id
name
description
status
created_at
updated_at
```

## menu_items

```text
id
branch_id
category_id
name
description
price
tax_rate
is_available
created_at
updated_at
```

## restaurant_orders

```text
id
branch_id
order_number
guest_id nullable
stay_id nullable
room_id nullable
table_number nullable
order_type
subtotal
discount_amount
tax_amount
total_amount
status
created_by
created_at
updated_at
```

## restaurant_order_items

```text
id
order_id
menu_item_id
quantity
unit_price
discount_amount
tax_amount
total_amount
created_at
```

## inventory_categories

```text
id
branch_id
name
description
created_at
updated_at
```

## products

```text
id
branch_id
category_id
sku
name
description
unit
cost_price
selling_price
reorder_level
current_stock
status
created_at
updated_at
```

## suppliers

```text
id
branch_id
supplier_number
name
contact_person
phone
email
address
tax_number
status
created_at
updated_at
```

## purchases

```text
id
branch_id
supplier_id
purchase_number
purchase_date
subtotal
tax_amount
discount_amount
total_amount
status
created_by
created_at
updated_at
```

## purchase_items

```text
id
purchase_id
product_id
quantity
unit_cost
tax_amount
total_amount
created_at
```

## stock_movements

```text
id
branch_id
product_id
movement_type
quantity
unit_cost
reference_type
reference_id
balance_after
created_by
notes
created_at
```

## expense_categories

```text
id
branch_id
name
description
created_at
updated_at
```

## expenses

```text
id
branch_id
category_id
amount
expense_date
payment_method_id
description
receipt_number
attachment
status
created_by
approved_by nullable
created_at
updated_at
```

## notifications

```text
id
user_id
type
title
message
reference_type nullable
reference_id nullable
is_read
created_at
```

## audit_logs

```text
id
branch_id
user_id nullable
action
entity_type
entity_id
old_values JSON nullable
new_values JSON nullable
ip_address
user_agent
created_at
```

## settings

```text
id
branch_id
setting_key
setting_value
setting_type
is_public
created_at
updated_at
```

---

# 19. ELOQUENT MODELS

Create appropriate Eloquent models and relationships.

Examples:

```text
Hotel
Branch
User
Role
Permission
Department
Employee
Floor
RoomType
Room
Amenity
Guest
BookingSource
Reservation
Stay
Folio
FolioItem
Invoice
InvoiceItem
PaymentMethod
Payment
Refund
HousekeepingTask
HousekeepingInspection
MaintenanceCategory
MaintenanceTicket
MenuCategory
MenuItem
RestaurantOrder
RestaurantOrderItem
InventoryCategory
Product
Supplier
Purchase
PurchaseItem
StockMovement
ExpenseCategory
Expense
Notification
AuditLog
Setting
```

Define relationships such as:

```text
Hotel hasMany Branches

Branch hasMany Rooms

Room belongsTo RoomType

Guest hasMany Reservations

Reservation belongsTo Guest

Reservation belongsTo Room

Reservation belongsTo RoomType

Reservation hasMany Stays

Stay belongsTo Reservation

Stay belongsTo Guest

Stay belongsTo Room

Stay hasMany Folios

Folio hasMany FolioItems

Folio belongsTo Stay

Invoice hasMany InvoiceItems

Invoice hasMany Payments

Purchase hasMany PurchaseItems

Product hasMany StockMovements
```

Use appropriate:

- `belongsTo`
- `hasMany`
- `belongsToMany`
- `hasOne`

relationships.

---

# 20. DATABASE CONSTRAINTS

Use:

- Foreign keys
- Cascading rules where appropriate
- Unique constraints
- Composite indexes
- Query indexes

Examples:

```text
users.email
users.username where applicable
reservation_number
invoice_number
folio_number
employee_number
room_number within branch
SKU within branch
```

Be deliberate with delete behavior.

Do not cascade-delete critical financial history accidentally.

---

# 21. MULTI-BRANCH ARCHITECTURE

The initial installation can operate with one branch.

However, database design must be branch-aware.

Most operational entities must include:

```text
branch_id
```

A user must only access records within their authorized branch scope unless they have explicit cross-branch permission.

Centralize branch-scope checks where possible.

Do not repeat insecure checks inconsistently across controllers.

---

# 22. HOTEL CONFIGURATION

Admin can configure:

- Hotel name
- Legal name
- Address
- Phone
- Email
- Logo
- Currency
- Timezone
- Tax
- Service charge
- Check-in time
- Check-out time
- Cancellation policy
- Booking policy

Recommended demo configuration:

```text
Currency: KES
Timezone: Africa/Nairobi
```

These values must remain configurable.

---

# 23. AUTHENTICATION

Use Laravel's secure authentication mechanisms.

Implement:

- Login
- Logout
- Password reset
- Password change
- Account activation/deactivation
- Session management
- Authentication throttling/rate limiting
- Login auditing

Passwords must be stored using Laravel's secure password hashing.

Never expose password hashes in responses.

Never store plaintext passwords.

---

# 24. SESSION SECURITY

Use secure session configuration.

After authentication:

```text
Regenerate session ID
```

On logout:

```text
Invalidate session
```

Use secure cookie settings:

```text
HttpOnly
Secure in HTTPS
SameSite
```

---

# 25. AUTHORIZATION

Every protected route/action must enforce:

```text
Authenticated
+
Authorized
+
Correct branch scope
```

Never rely only on Blade:

```blade
@if($user->isAdmin())
```

The backend must enforce authorization.

---

# 26. CSRF

Use Laravel's CSRF protection for state-changing web requests.

This applies to:

- Create
- Update
- Delete
- Cancel
- Check-in
- Check-out
- Payment
- Refund
- Stock adjustment
- Expense approval
- Settings updates

TypeScript requests must correctly include CSRF protection.

---

# 27. RESERVATION MODULE

Pages:

```text
/reservations
/reservations/create
/reservations/calendar
/reservations/{reservation}
/reservations/{reservation}/edit
```

Features:

- Search reservations
- Create reservation
- Edit reservation
- Confirm
- Cancel
- No-show
- Reservation details
- Guest association
- Room assignment
- Room-type booking
- Booking source
- Pricing
- Discount
- Tax
- Service charge
- Deposit
- Special requests

---

# 28. RESERVATION STATUS

Use controlled transitions.

```text
PENDING
CONFIRMED
CHECKED_IN
CHECKED_OUT
CANCELLED
NO_SHOW
```

Do not allow arbitrary status changes.

Example:

```text
PENDING → CONFIRMED
PENDING → CANCELLED

CONFIRMED → CHECKED_IN
CONFIRMED → CANCELLED
CONFIRMED → NO_SHOW

CHECKED_IN → CHECKED_OUT
```

Reject invalid transitions.

---

# 29. AVAILABILITY ENGINE

Create:

```text
AvailabilityService
```

or equivalent domain action.

It must evaluate:

```text
Room
+
Reservation dates
+
Existing reservations
+
Room state
+
Maintenance
+
Out-of-order state
+
Readiness policy
```

Use the standard overlap condition:

```text
requested_check_in < existing_check_out
AND
requested_check_out > existing_check_in
```

Prevent overlapping bookings.

---

# 30. CONCURRENCY

The system must safely handle simultaneous operations.

Examples:

```text
Receptionist A books Room 201
Receptionist B books Room 201

User A checks out Room 205
User B attempts same checkout

User A applies payment
User B applies payment

Inventory User A receives stock
Inventory User B modifies same product
```

Use:

- Database transactions
- Appropriate locks
- Unique constraints
- Conflict handling
- Idempotency where appropriate

---

# 31. CHECK-IN

Dedicated page:

```text
/front-desk/check-in
```

Workflow:

```text
Reservation
↓
Verify reservation
↓
Verify guest
↓
Verify room
↓
Validate check-in conditions
↓
Create stay
↓
Open folio
↓
Record arrival
↓
Update reservation
↓
Update room
↓
Audit
```

Perform critical changes inside a database transaction.

---

# 32. STAY MANAGEMENT

Do not treat reservation and stay as the same domain object.

Keep:

```text
Reservation
Stay
```

separate.

A reservation represents a planned booking.

A stay represents the actual guest occupancy.

---

# 33. FOLIO

A folio represents charges associated with an active guest stay.

Support:

```text
ROOM
RESTAURANT
LAUNDRY
MINIBAR
TRANSFER
SERVICE
OTHER
```

Every folio charge must have:

- Description
- Quantity
- Unit price
- Discount
- Tax
- Total
- Source/reference
- Timestamp

---

# 34. CHECK-OUT

Dedicated page:

```text
/front-desk/check-out
```

Workflow:

```text
Load active stay
↓
Load folio
↓
Load charges
↓
Calculate authoritative totals
↓
Apply discounts
↓
Calculate taxes
↓
Apply service charge
↓
Load payments
↓
Calculate balance
↓
Validate settlement rules
↓
Close invoice/folio
↓
Close stay
↓
Set room DIRTY
↓
Create housekeeping task
↓
Audit
↓
Commit
```

Use a transaction.

Rollback if a critical operation fails.

---

# 35. FINANCIAL INTEGRITY

Use server-side calculations.

Never trust totals sent from TypeScript.

Client may submit:

```text
quantity
item
discount request
payment amount
```

Laravel must recalculate authoritative amounts.

Never trust:

```text
grand_total
balance_due
tax_amount
```

provided by the browser.

---

# 36. INVOICE

Support:

```text
DRAFT
ISSUED
PARTIALLY_PAID
PAID
VOID
OVERDUE
```

Invoice total:

```text
subtotal
- discount
+ tax
+ service charge
= grand total
```

Balance:

```text
grand total
-
valid payments
=
balance
```

Ensure calculations are deterministic.

---

# 37. PAYMENTS

Support:

```text
Cash
Card
Bank Transfer
Mobile Money
Other
```

Payment must contain:

- Amount
- Method
- Reference
- Invoice
- Guest
- Receiver
- Status
- Timestamp
- Notes

Do not casually delete payment history.

Prefer:

```text
VOID
REFUND
REVERSE
```

where applicable.

---

# 38. REFUNDS

Refund operations must:

- Validate original payment
- Validate refundable amount
- Prevent over-refunding
- Record reason
- Record refund method
- Record actor
- Record timestamp
- Audit the operation

Use transactions.

---

# 39. ROOM MANAGEMENT

Pages:

```text
/rooms
/rooms/types
/rooms/floors
/rooms/{room}
```

Room state must remain separate from:

```text
reservation status
housekeeping status
maintenance status
```

Operational room status:

```text
AVAILABLE
RESERVED
OCCUPIED
OUT_OF_ORDER
MAINTENANCE
```

Housekeeping state:

```text
DIRTY
CLEANING
CLEAN
INSPECTED
```

---

# 40. ROOM LIFECYCLE

Example:

```text
AVAILABLE
↓
RESERVED
↓
OCCUPIED
↓
DIRTY
↓
CLEANING
↓
CLEAN
↓
INSPECTED
↓
AVAILABLE
```

Maintenance may interrupt the lifecycle.

Example:

```text
AVAILABLE
↓
MAINTENANCE
↓
AVAILABLE
```

Rooms under maintenance must not become bookable accidentally.

---

# 41. ROOM BOARD

Create:

```text
/rooms
```

with an operational room board.

Each room card can display:

```text
Room number
Room type
Operational status
Housekeeping status
Maintenance state
Current reservation/guest when authorized
Quick actions
```

Use accessible labels.

Do not rely solely on color.

---

# 42. RESERVATION CALENDAR

Dedicated page:

```text
/reservations/calendar
```

The calendar should show:

```text
Room
Date range
Reservation
Guest
Status
```

Example:

```text
Room     16   17   18   19   20

101      ███████████████
102           █████████████
103      ██████
104                     █████
```

Calendar interactions may use TypeScript.

Actual reservation creation must still pass through Laravel business rules.

---

# 43. GUEST MODULE

Pages:

```text
/guests
/guests/create
/guests/{guest}
/guests/{guest}/edit
```

Support:

- Registration
- Editing
- Search
- Guest profiles
- Guest history
- Reservations
- Stays
- Folios
- Invoices

Search:

```text
Name
Phone
Email
ID/passport
Guest number
```

Protect sensitive guest information using authorization.

---

# 44. HOUSEKEEPING

Pages:

```text
/housekeeping
/housekeeping/tasks
/housekeeping/inspections
```

Support:

- Dirty rooms
- Cleaning tasks
- Assignments
- Priorities
- Cleaning completion
- Inspection
- Notes

States:

```text
DIRTY
CLEANING
CLEAN
INSPECTED
```

A room should only become bookable once configured readiness rules are satisfied.

---

# 45. MAINTENANCE

Pages:

```text
/maintenance
/maintenance/create
/maintenance/{ticket}
```

States:

```text
REPORTED
ASSIGNED
IN_PROGRESS
RESOLVED
CLOSED
```

Support:

- Room-related problems
- Facility problems
- Categories
- Priority
- Assignment
- Estimated cost
- Actual cost
- Resolution notes

---

# 46. RESTAURANT / POS

Pages:

```text
/restaurant/pos
/restaurant/menu
/restaurant/menu/categories
/restaurant/orders
/restaurant/orders/{order}
```

Features:

- Menu categories
- Menu items
- Prices
- Taxes
- Availability
- Orders
- Order items
- Receipts
- Cancellation
- Room charges

Order lifecycle:

```text
OPEN
↓
CONFIRMED
↓
PREPARING
↓
READY
↓
SERVED
↓
PAID
```

Alternative:

```text
OPEN → CANCELLED
```

---

# 47. RESTAURANT ROOM CHARGE

A restaurant order may be charged to a guest's active folio.

Workflow:

```text
Restaurant order
↓
Validate items
↓
Calculate total
↓
Validate guest
↓
Validate active stay
↓
Validate folio
↓
Create folio charge
↓
Mark room charge linkage
↓
Audit
```

The same restaurant order must never be added to the folio twice.

Implement safeguards against duplicate requests.

---

# 48. INVENTORY

Pages:

```text
/inventory
/inventory/products
/inventory/categories
/inventory/suppliers
/inventory/purchases
/inventory/stock-movements
/inventory/adjustments
```

Support:

- Products
- Categories
- SKU
- Units
- Cost price
- Selling price
- Reorder level
- Suppliers
- Purchases
- Goods receiving
- Consumption
- Adjustments
- Returns
- Damage
- Stock movement history

---

# 49. STOCK MOVEMENT MODEL

Every stock mutation must generate a stock movement.

Movement types:

```text
PURCHASE
SALE
CONSUMPTION
RETURN
ADJUSTMENT
DAMAGE
TRANSFER_IN
TRANSFER_OUT
```

Stock balance must be explainable through the movement history.

Use transactions and appropriate locking for concurrent stock updates.

---

# 50. PURCHASE WORKFLOW

```text
Purchase created
↓
Purchase items
↓
Purchase approved/received according to business workflow
↓
Stock increases
↓
Stock movement created
↓
Audit event
```

Do not increase inventory merely because a draft purchase exists.

---

# 51. EXPENSES

Pages:

```text
/finance/expenses
/finance/expenses/create
/finance/expenses/{expense}
```

Support:

- Expense category
- Amount
- Date
- Payment method
- Description
- Receipt number
- Attachment
- Approval
- Status
- Audit history

Workflow:

```text
DRAFT
↓
SUBMITTED
↓
APPROVED / REJECTED
↓
POSTED
↓
PAID
```

---

# 52. STAFF MODULE

Pages:

```text
/staff
/staff/users
/staff/employees
/staff/roles
/staff/permissions
```

Support:

- User accounts
- Employees
- Departments
- Roles
- Permissions
- Account status
- Role assignment
- Activity history

---

# 53. DASHBOARDS

Do not build one universal dashboard that looks identical for everyone.

Use role-aware dashboards.

## Manager Dashboard

Display:

```text
Today's arrivals
Today's departures
Occupancy
Available rooms
Occupied rooms
Dirty rooms
Maintenance rooms
Revenue
Outstanding balances
Active reservations
Restaurant sales
```

## Front Desk

Display:

```text
Arrivals
Departures
Available rooms
Current guests
Pending reservations
Check-in shortcut
Check-out shortcut
```

## Housekeeping

Display:

```text
Dirty rooms
Cleaning
Clean
Inspection pending
Assigned tasks
Priority tasks
```

## Finance

Display:

```text
Revenue
Payments
Outstanding balances
Refunds
Expenses
Financial summary
```

---

# 54. MULTI-PAGE DASHBOARD PRINCIPLE

Each dashboard is its own page:

```text
/dashboard
/front-desk
/housekeeping
/finance
```

The user navigates between pages normally.

TypeScript may update charts, tables, counters, and notifications without turning the page into an SPA.

---

# 55. REPORTS

Pages:

```text
/reports
/reports/occupancy
/reports/reservations
/reports/revenue
/reports/payments
/reports/expenses
/reports/financial
/reports/housekeeping
/reports/inventory
```

Reports must be generated from transactional source data.

---

# 56. HOTEL KPIs

Implement where definitions are clearly established:

```text
Occupancy Rate
ADR
RevPAR
Average Length of Stay
Cancellation Rate
No-show Rate
```

Document every metric.

Do not implement metrics using arbitrary assumptions.

---

# 57. REPORT FILTERING

Reports should support appropriate:

- Date ranges
- Branch
- Room type
- Booking source
- Payment method
- Employee
- Status

Large results must be paginated.

---

# 58. TYPEScript ORGANIZATION

Use a clear TypeScript structure.

Example:

```text
resources/
└── ts/
    ├── app.ts
    ├── bootstrap.ts
    │
    ├── shared/
    │   ├── http.ts
    │   ├── csrf.ts
    │   ├── modal.ts
    │   ├── toast.ts
    │   ├── confirm.ts
    │   ├── form.ts
    │   └── table.ts
    │
    ├── dashboard/
    │   └── dashboard.ts
    │
    ├── reservations/
    │   ├── index.ts
    │   ├── create.ts
    │   ├── calendar.ts
    │   └── show.ts
    │
    ├── guests/
    │   ├── index.ts
    │   ├── create.ts
    │   └── show.ts
    │
    ├── rooms/
    │   ├── index.ts
    │   ├── board.ts
    │   └── show.ts
    │
    ├── front-desk/
    │   ├── check-in.ts
    │   └── check-out.ts
    │
    ├── housekeeping/
    │   └── housekeeping.ts
    │
    ├── maintenance/
    │   └── maintenance.ts
    │
    ├── restaurant/
    │   ├── pos.ts
    │   ├── menu.ts
    │   └── orders.ts
    │
    ├── inventory/
    │   ├── products.ts
    │   ├── purchases.ts
    │   └── movements.ts
    │
    ├── finance/
    │   ├── invoices.ts
    │   ├── payments.ts
    │   └── expenses.ts
    │
    └── reports/
        ├── occupancy.ts
        ├── revenue.ts
        └── financial.ts
```

---

# 59. TYPEScript RULES

Use:

- Strict TypeScript
- Interfaces/types
- ES modules
- Typed API responses
- Reusable utilities
- Clear error handling

Avoid:

```text
any
```

unless there is a justified reason.

Do not put the whole application into one TypeScript file.

---

# 60. TYPECRIPT RESPONSIBILITY

TypeScript may:

```text
show/hide UI elements
validate basic input
load data asynchronously
refresh page sections
render charts
render calendars
handle modals
handle filters
handle search
handle confirmations
```

TypeScript must NOT be the authority for:

```text
permissions
pricing
taxes
payment validity
reservation availability
financial totals
inventory integrity
branch access
```

Laravel remains authoritative.

---

# 61. BLADE ARCHITECTURE

Use reusable Blade layouts/components.

Example:

```text
resources/views/
├── layouts/
│   ├── app.blade.php
│   ├── guest.blade.php
│   └── print.blade.php
│
├── components/
│   ├── sidebar.blade.php
│   ├── topbar.blade.php
│   ├── breadcrumb.blade.php
│   ├── alert.blade.php
│   ├── modal.blade.php
│   ├── table.blade.php
│   └── status-badge.blade.php
│
├── auth/
│
├── dashboard/
│
├── reservations/
├── guests/
├── rooms/
├── front-desk/
├── housekeeping/
├── maintenance/
├── restaurant/
├── inventory/
├── finance/
├── staff/
├── reports/
└── settings/
```

---

# 62. VIEW PRINCIPLE

Each major page gets its own Blade view.

Examples:

```text
reservations/index.blade.php
reservations/create.blade.php
reservations/calendar.blade.php

guests/index.blade.php
guests/create.blade.php
guests/show.blade.php

rooms/index.blade.php
rooms/types.blade.php
rooms/floors.blade.php
```

Do not combine unrelated screens into one giant Blade file.

---

# 63. VIEWS MUST REMAIN CLEAN

Blade views must not contain:

- Large database queries
- Business calculations
- Permission decisions as the only authorization mechanism
- Complex workflow logic
- Repeated business rules

Use prepared data from controllers/services.

---

# 64. ROUTES

Use named Laravel routes.

Example:

```php
Route::get('/reservations', ...)
    ->name('reservations.index');

Route::get('/reservations/create', ...)
    ->name('reservations.create');

Route::post('/reservations', ...)
    ->name('reservations.store');

Route::get('/reservations/{reservation}', ...)
    ->name('reservations.show');
```

Use route model binding where appropriate.

---

# 65. ROUTE GROUPS

Use route groups for:

```text
Authentication
Administration
Front Desk
Housekeeping
Finance
Inventory
Reports
```

Apply middleware consistently.

---

# 66. FORM HANDLING

Prefer standard Laravel form submissions for normal CRUD operations.

Use TypeScript/fetch for operations where asynchronous interaction clearly improves UX.

Do not convert every form into AJAX simply because TypeScript is available.

The application remains an MPA.

---

# 67. AJAX / FETCH

TypeScript can use Fetch for:

```text
Availability lookup
Search
Filtering
Notifications
Room board updates
Charts
POS item interactions
Dynamic form data
```

But all requests must still pass through Laravel.

---

# 68. API RESPONSE SECURITY

Never expose:

- Password hashes
- Internal secrets
- Database errors
- Stack traces
- Environment variables
- Sensitive internal fields unnecessarily

Use explicit response resources/transformers where helpful.

---

# 69. SECURITY

Implement enterprise-level security practices.

At minimum:

```text
Authentication
Authorization
RBAC
CSRF
XSS prevention
SQL injection prevention
IDOR prevention
Session security
Rate limiting
Secure file uploads
Input validation
Output escaping
Audit logging
Branch isolation
Secure headers
Safe exception handling
```

---

# 70. SQL INJECTION

Use Eloquent or parameterized queries.

Never construct unsafe queries from user input.

Avoid:

```php
DB::raw("... $userInput ...")
```

unless the value is completely controlled and safely handled.

---

# 71. XSS

Escape untrusted output.

Use Blade escaping by default.

Only use raw HTML output where it is explicitly trusted and sanitized.

---

# 72. IDOR PROTECTION

A user must not be able to access:

```text
/guests/1234
```

simply because they guessed an ID.

Verify:

```text
User permissions
+
Branch scope
+
Record authorization
```

before accessing the resource.

---

# 73. FILE UPLOAD SECURITY

For guest documents, receipts and attachments:

- Validate MIME type
- Validate size
- Generate secure filenames
- Store safely
- Prevent script execution
- Do not trust user-provided extensions
- Keep sensitive uploads out of publicly executable locations

---

# 74. AUDIT LOGGING

Audit critical operations.

Log:

```text
Login
Logout
Failed login
User creation
User changes
Role changes
Permission changes
Reservation creation
Reservation cancellation
Check-in
Check-out
Payment
Refund
Invoice void
Inventory adjustment
Expense approval
Settings change
```

Capture:

```text
User
Branch
Action
Entity
Entity ID
Timestamp
IP address
User agent
Before values
After values
```

Do not log passwords or secrets.

---

# 75. EVENTS / LISTENERS

Use Laravel events/listeners when asynchronous or decoupled reactions are appropriate.

Examples:

```text
ReservationCreated
ReservationCancelled
GuestCheckedIn
GuestCheckedOut
PaymentReceived
PaymentRefunded
LowStockDetected
MaintenanceReported
RoomReady
```

Listeners may handle:

- Notifications
- Audit enrichment
- Non-critical secondary processing

Do not move critical transactional updates into asynchronous processes unless the business rules explicitly allow eventual consistency.

---

# 76. NOTIFICATIONS

Use Laravel Notifications where appropriate.

Support in-app notifications first.

Potential future channels:

```text
Email
SMS
WhatsApp
```

Do not make external providers mandatory for the MVP.

---

# 77. QUEUES

Use Laravel queues for tasks that genuinely benefit from background processing.

Examples:

- Email notifications
- Report generation
- Large exports
- Non-critical processing

Do not queue a critical financial mutation that must complete before the transaction is considered successful.

---

# 78. SCHEDULER

Use Laravel Scheduler where useful for tasks such as:

```text
Reservation reminders
Upcoming checkout alerts
Low-stock checks
Expired pending reservations
Daily summaries
```

Scheduled jobs must be idempotent.

---

# 79. CACHING

Use Laravel caching carefully.

Potential cached data:

```text
Hotel configuration
Room types
Amenities
Permissions
Dashboard aggregates
```

Do not cache data in a way that allows users to see stale security permissions or stale financial state incorrectly.

---

# 80. TRANSACTIONS

Use database transactions for critical operations.

Examples:

```text
Check-in
Check-out
Payment application
Refund
Restaurant room charge
Inventory receipt
Stock adjustment
Expense posting
```

Example:

```php
DB::transaction(function () {
    // critical operation
});
```

If any critical operation fails:

```text
ROLLBACK
```

---

# 81. DOUBLE-BOOKING PREVENTION

The reservation system must remain correct under concurrency.

Do not rely exclusively on:

```text
SELECT availability
then
INSERT reservation
```

because another request may occur between those operations.

Use an appropriate combination of:

- Transactions
- Locks
- Constraints
- Conflict detection
- Carefully designed availability queries

Test concurrent booking attempts.

---

# 82. IDEMPOTENCY

Operations that can be accidentally submitted twice should be protected.

Examples:

```text
Payment submission
Refund
Checkout
Restaurant room charge
Purchase receipt
Inventory adjustment
```

Repeated requests must not duplicate financial or stock effects.

---

# 83. FINANCIAL MODEL

Use:

```text
Folio
↓
Invoice
↓
Payments
```

A guest may have several folio charges.

Invoices contain authoritative charges.

Payments settle invoices.

Do not blur these concepts.

---

# 84. HISTORICAL PRICING

Historical financial records must retain the actual charged amount.

If room type pricing changes:

```text
Future booking
```

may use the new rate.

But:

```text
Historical invoice
```

must not change.

Store transaction-level prices where appropriate.

---

# 85. ROOM AVAILABILITY

Never determine availability from:

```text
rooms.operational_status
```

alone.

Availability must consider:

```text
Reservations
Room state
Maintenance
Out-of-order blocks
Housekeeping readiness
Date overlap
```

---

# 86. INVENTORY INTEGRITY

Do not treat:

```text
products.current_stock
```

as the only source of truth.

Maintain:

```text
stock_movements
```

so that every adjustment can be explained.

Use transactions for changes.

---

# 87. UI/UX

The UI should feel like a serious hotel enterprise platform.

Use:

- Clean dashboard
- Responsive sidebar
- Top navigation
- Breadcrumbs
- Tables
- Forms
- Search
- Filters
- Cards
- Charts
- Calendar
- Room board
- Status badges
- Notifications
- Confirmation dialogs

Avoid excessive decorative animation.

Prioritize usability.

---

# 88. DESIGN LANGUAGE

The visual language should communicate:

```text
Hospitality
Professionalism
Trust
Operational efficiency
Financial clarity
Enterprise software
```

Do not make it look like a generic developer admin dashboard.

---

# 89. ICONS

Use professional SVG icons.

Do not use emojis as:

- Navigation icons
- Buttons
- Status indicators
- Actions
- Dashboard icons

---

# 90. RESPONSIVE DESIGN

The MPA must work on:

- Desktop
- Tablet
- Mobile

Desktop is the primary environment for front-desk staff.

Mobile should remain usable for:

- Housekeeping
- Maintenance
- Managers reviewing dashboards

---

# 91. ACCESSIBILITY

Implement:

- Semantic HTML
- Labels
- Keyboard navigation
- Focus states
- Accessible form errors
- Sufficient contrast
- ARIA only where necessary
- Text labels for statuses
- Color-independent status communication

---

# 92. DATA TABLES

Tables should support:

- Search
- Filtering
- Sorting
- Pagination
- Empty state
- Loading state where asynchronous
- Error state
- Row actions

Do not load thousands of records into the browser unnecessarily.

Use server-side pagination.

---

# 93. PRINTING

Create dedicated print-friendly Blade layouts.

Examples:

```text
/invoices/{invoice}/print
/payments/{payment}/receipt
/reports/revenue/print
/reservations/{reservation}/confirmation
```

Do not print the application sidebar/navigation.

---

# 94. DASHBOARD CHARTS

Use TypeScript + Chart.js or an equivalent chart library.

Charts may display:

```text
Occupancy trend
Revenue trend
Reservation trend
Payment method distribution
Room status distribution
Restaurant sales
Inventory movement
```

The underlying data must originate from Laravel-controlled queries.

---

# 95. SEARCH

Implement server-side search for major entities.

Examples:

```text
Guests
Reservations
Rooms
Invoices
Payments
Products
Orders
Suppliers
Expenses
Maintenance tickets
Audit logs
Users
```

Avoid downloading entire datasets to TypeScript simply to search them locally.

---

# 96. PAGINATION

Use Laravel pagination.

Examples:

```php
Guest::paginate(25);
Reservation::paginate(25);
Invoice::paginate(25);
```

Use sensible defaults.

Allow configurable page size where useful.

---

# 97. REPORT EXPORTS

Support export/print where practical.

Possible formats:

```text
CSV
PDF
Print
```

Exports should be permission-protected.

Large exports may be queued.

---

# 98. ERROR HANDLING

Implement professional error handling.

Pages:

```text
403
404
419
422
429
500
503
```

Users should receive understandable messages.

Developers should receive detailed logs.

Do not expose:

```text
SQLSTATE
Stack traces
Database credentials
Server paths
Secrets
Environment values
```

---

# 99. EXCEPTION HANDLING

Use Laravel's exception system.

Handle business exceptions cleanly.

Potential domain exceptions:

```text
ReservationConflictException
InvalidReservationStateException
InvalidPaymentException
InsufficientRefundException
UnauthorizedRoomAccessException
InventoryConflictException
```

Map them to appropriate user responses.

---

# 100. HTTP STATUS CODES

Use appropriate responses.

Examples:

```text
200 OK
201 Created
302 Redirect for normal form workflow
400 Bad Request
401 Unauthorized
403 Forbidden
404 Not Found
409 Conflict
419 CSRF/session expiration
422 Validation Error
429 Too Many Requests
500 Internal Server Error
503 Service Unavailable
```

---

# 101. LOGGING

Use Laravel logging.

Log:

- Exceptions
- Security events
- Integration failures
- Critical application failures
- Unexpected business errors

Never log:

```text
Passwords
Secrets
Session secrets
API credentials
Environment variables
```

---

# 102. TESTING STRATEGY

Use:

```text
Unit Tests
Feature Tests
Integration Tests
Security Tests
Browser/E2E Tests
Regression Tests
```

---

# 103. UNIT TESTS

Test:

```text
Date overlap
Availability calculation
Pricing
Tax
Discounts
Invoice totals
Payment balance
Refund calculations
Stock calculations
Reservation transitions
```

---

# 104. FEATURE TESTS

Test:

```text
Guest creation
Guest update
Reservation creation
Reservation cancellation
Check-in
Check-out
Payment
Refund
Housekeeping
Maintenance
Restaurant orders
Room charges
Inventory purchases
Stock adjustments
Expenses
```

---

# 105. INTEGRATION TESTS

Test full workflows.

## Guest stay

```text
Guest
↓
Reservation
↓
Check-in
↓
Stay
↓
Folio
↓
Invoice
↓
Payment
↓
Checkout
```

## Restaurant

```text
Order
↓
Items
↓
Total
↓
Room charge
↓
Folio
↓
Invoice
```

## Inventory

```text
Purchase
↓
Receipt
↓
Stock Increase
↓
Stock Movement
```

---

# 106. SECURITY TESTS

Test:

```text
Authentication bypass
Authorization bypass
SQL injection
XSS
CSRF
IDOR
Session fixation
Session handling
Rate limiting
Privilege escalation
Branch isolation
File uploads
Direct URL access
Unauthorized finance access
Unauthorized refund
Unauthorized inventory adjustment
```

---

# 107. CONCURRENCY TESTS

Explicitly test:

```text
Two users booking the same room
Two users checking out the same stay
Two payment submissions
Two stock updates
Two restaurant room-charge submissions
```

The database must remain consistent.

---

# 108. DATABASE TESTING

Tests should verify:

```text
Foreign keys
Unique constraints
Transactions
Relationship integrity
Index-supported queries
Cascade rules
Branch isolation
```

---

# 109. SEEDERS

Create realistic development seeders.

Seed:

```text
Hotel
Branch
Departments
Employees
Roles
Permissions
Users
Floors
Room Types
Rooms
Amenities
Guests
Booking Sources
Payment Methods
Menu Categories
Menu Items
Inventory Categories
Products
Suppliers
Expense Categories
```

Create enough demo data for dashboards and reports.

---

# 110. DEMO USERS

Create development-only test accounts.

Example:

```text
admin@example.test
manager@example.test
reception@example.test
housekeeping@example.test
accountant@example.test
restaurant@example.test
inventory@example.test
maintenance@example.test
```

Use development-only credentials.

Never use real passwords or real user information.

---

# 111. LARAVEL FILE STRUCTURE

Use a structure broadly like:

```text
hotel-management-system/
│
├── app/
│   ├── Actions/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   ├── Repositories/
│   ├── Events/
│   ├── Listeners/
│   ├── Jobs/
│   ├── Notifications/
│   └── Providers/
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
│
├── resources/
│   ├── css/
│   ├── ts/
│   └── views/
│
├── routes/
│   ├── web.php
│   ├── console.php
│   └── api.php
│
├── storage/
│
├── tests/
│   ├── Feature/
│   ├── Unit/
│   ├── Browser/
│   └── Security/
│
├── .env
├── .env.example
├── artisan
├── composer.json
├── package.json
├── tsconfig.json
├── vite.config.ts
└── README.md
```

---

# 112. ROUTING ORGANIZATION

Use Laravel route groups.

Example conceptual structure:

```text
routes/web.php

Public
├── login
└── password reset

Authenticated
├── dashboard
├── reservations
├── guests
├── rooms
├── front desk
├── housekeeping
├── maintenance
├── restaurant
├── inventory
├── finance
├── staff
├── reports
└── settings
```

Use middleware and policies rather than duplicating authorization code.

---

# 113. NO SINGLE PAGE SYSTEM

The following is explicitly forbidden:

```text
dashboard.blade.php
    ↓
all modules hidden inside tabs
```

Also forbidden:

```text
app.ts
    ↓
all application logic
```

Also forbidden:

```text
One giant Blade component
One giant Controller
One giant Service
One giant TypeScript file
```

Keep the system modular.

---

# 114. PAGE-LEVEL TYPE SCRIPT

Each page may have its own TypeScript entry point.

Example:

```text
reservations/index.ts
reservations/create.ts
reservations/calendar.ts

guests/index.ts
guests/show.ts

rooms/index.ts
rooms/board.ts

front-desk/check-in.ts
front-desk/check-out.ts

restaurant/pos.ts
inventory/products.ts
reports/revenue.ts
```

Do not load every TypeScript module on every page.

Load only what the page requires.

---

# 115. VITE ENTRY STRATEGY

Configure Vite to support page-specific assets.

Conceptually:

```text
resources/ts/app.ts
resources/ts/reservations/index.ts
resources/ts/reservations/create.ts
resources/ts/reservations/calendar.ts
resources/ts/front-desk/check-in.ts
resources/ts/front-desk/check-out.ts
resources/ts/restaurant/pos.ts
```

Blade should load the required asset for the current page.

---

# 116. TYPE SAFETY

Use interfaces/types for backend JSON responses.

Example:

```ts
interface AvailabilityResult {
    available: boolean;
    roomId?: number;
    message?: string;
}
```

Do not treat every response as:

```ts
any
```

---

# 117. SECURITY OF TYPE SCRIPT

Never place secrets in browser JavaScript.

Never expose:

```text
Database credentials
Private API keys
Server secrets
Application secrets
```

TypeScript is client-side and therefore not trusted.

---

# 118. API / ASYNC BOUNDARY

When TypeScript requires asynchronous functionality:

```text
TypeScript
↓
HTTP request
↓
Laravel route
↓
Middleware
↓
Controller
↓
Validation
↓
Service
↓
Database
```

The browser must never directly access MySQL.

---

# 119. DATA TRANSFER

Use explicit JSON structures.

Example:

```json
{
    "success": true,
    "data": {
        "available": true
    },
    "message": "Room is available."
}
```

For errors:

```json
{
    "success": false,
    "message": "The room is no longer available.",
    "errors": {}
}
```

Do not expose internal exceptions.

---

# 120. API SECURITY

Every asynchronous endpoint must apply:

```text
Authentication
Authorization
CSRF where applicable
Validation
Branch scope
Rate limiting where appropriate
```

Do not make `/api/...` endpoints automatically trusted just because they are internal.

---

# 121. DESIGN SYSTEM

Create a consistent design system for:

```text
Colors
Typography
Spacing
Buttons
Inputs
Tables
Cards
Alerts
Badges
Modals
Dropdowns
Pagination
Tabs
Breadcrumbs
```

Use reusable Blade components.

---

# 122. DARK/LIGHT MODE

If implemented, use a proper system-level theme.

The user preference should persist safely.

Do not make theme switching dependent on an SPA framework.

---

# 123. PERFORMANCE

Optimize:

- Eloquent queries
- Relationships
- Dashboard queries
- Report queries
- Pagination
- Asset loading
- Image loading
- Caching

Watch for:

```text
N+1 queries
Unbounded queries
Large joins
Repeated calculations
Loading unnecessary data
```

---

# 124. DATABASE QUERY QUALITY

Use:

```php
with()
select()
where()
whereDate()
whereBetween()
paginate()
```

appropriately.

Avoid retrieving whole tables when only a subset is needed.

---

# 125. FINANCIAL PERFORMANCE

Financial dashboards may need aggregation queries.

Optimize them carefully.

If caching is used:

```text
Do not return stale values where immediate financial accuracy is required.
```

---

# 126. AUDIT LOG PERFORMANCE

Audit tables may become large.

Plan for:

- Indexes
- Pagination
- Date filtering
- Entity filtering
- User filtering
- Branch filtering

Do not load the entire audit table.

---

# 127. DATABASE BACKUPS

Document a proper backup process.

Backups must not be stored in:

```text
public/
```

or any publicly accessible directory.

---

# 128. ENVIRONMENT CONFIGURATION

Use:

```text
.env
.env.example
```

Configure:

```text
APP_NAME
APP_ENV
APP_KEY
APP_DEBUG
APP_URL

DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

CACHE_STORE
SESSION_DRIVER
QUEUE_CONNECTION
MAIL_*
```

Never commit real secrets.

---

# 129. LOCAL DEVELOPMENT

The project should run with standard Laravel development tooling.

Example workflow:

```text
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

Adjust the exact workflow to the selected deployment environment.

---

# 130. DEPLOYMENT READINESS

Prepare the project for deployment to a normal PHP/Laravel-capable environment.

Document:

- PHP requirements
- MySQL requirements
- Environment variables
- Storage configuration
- Queue setup
- Scheduler setup
- Web server
- SSL/HTTPS requirements
- Cache setup
- Deployment steps

---

# 131. NO DOCKER REQUIREMENT

Do not require Docker.

The application must be capable of running locally with:

```text
Laragon
XAMPP
or another standard PHP/Laravel environment
```

Docker support may be documented optionally but is not mandatory.

---

# 132. HOTEL WORKFLOW PRINCIPLES

When implementing a workflow, always ask:

```text
What happens to the reservation?
What happens to the stay?
What happens to the room?
What happens to housekeeping?
What happens financially?
What happens to inventory?
What happens in the audit log?
What happens if the process fails halfway?
What happens if two users execute it simultaneously?
```

Every relevant state transition must remain consistent.

---

# 133. RESERVATION WORKFLOW

```text
Guest
↓
Availability search
↓
Reservation
↓
Confirmation
↓
Arrival
↓
Check-in
↓
Stay
↓
Services
↓
Checkout
↓
Invoice
↓
Payment settlement
↓
Closed stay
```

---

# 134. ROOM WORKFLOW

```text
AVAILABLE
↓
RESERVED
↓
OCCUPIED
↓
DIRTY
↓
CLEANING
↓
CLEAN
↓
INSPECTED
↓
AVAILABLE
```

---

# 135. FINANCIAL WORKFLOW

```text
Stay
↓
Folio
↓
Charges
↓
Invoice
↓
Payment
↓
Balance
↓
Settlement
```

---

# 136. INVENTORY WORKFLOW

```text
Purchase
↓
Receive
↓
Stock increase
↓
Consumption/Sale
↓
Stock decrease
↓
Low stock
↓
Reorder
```

---

# 137. RESTAURANT WORKFLOW

```text
Order
↓
Items
↓
Preparation
↓
Service
↓
Payment OR Room Charge
↓
Folio
↓
Invoice
```

---

# 138. HOUSEKEEPING WORKFLOW

```text
Checkout
↓
DIRTY
↓
CLEANING
↓
CLEAN
↓
INSPECTED
↓
READY
```

---

# 139. MAINTENANCE WORKFLOW

```text
REPORTED
↓
ASSIGNED
↓
IN_PROGRESS
↓
RESOLVED
↓
CLOSED
```

---

# 140. EXPENSE WORKFLOW

```text
DRAFT
↓
SUBMITTED
↓
APPROVED / REJECTED
↓
POSTED
↓
PAID
```

---

# 141. EDGE CASES

Test all important scenarios.

## Reservations

```text
Same room same dates
Overlapping dates
Back-to-back reservations
Same-day checkout/check-in
Cancelled booking
No-show
Inactive room
Room maintenance
Out-of-order room
Concurrent booking
```

## Payments

```text
Partial payment
Exact payment
Overpayment
Duplicate payment
Failed payment
Refund
Partial refund
Excessive refund
Void payment
Concurrent payment
```

## Checkout

```text
Outstanding balance
No balance
Restaurant charges
Multiple services
Multiple payments
Duplicate checkout request
Concurrent checkout
```

## Inventory

```text
Purchase
Consumption
Damage
Return
Adjustment
Negative stock
Duplicate adjustment
Concurrent stock updates
```

---

# 142. BUSINESS RULES MUST BE CENTRALIZED

Do not duplicate:

```text
availability calculations
tax calculations
invoice calculations
payment rules
refund rules
room transitions
stock rules
```

across multiple controllers.

Centralize the rules.

---

# 143. VALID STATUS TRANSITIONS

Implement explicit transition handling.

Do not simply allow:

```php
$model->status = $request->status;
```

without checking whether that transition is valid.

Use domain methods/services/policies.

---

# 144. AUTHORIZATION BY DOMAIN

Examples:

```text
Can user refund this payment?
Can user check out this stay?
Can user edit this reservation?
Can user access this guest?
Can user modify this room?
Can user perform stock adjustment?
Can user approve expense?
```

Authorization should consider:

```text
Role
Permission
Branch
Entity ownership/scope
Business state
```

---

# 145. DATA PRIVACY

Guest data must be minimized and protected.

Do not display sensitive information to roles that do not need it.

Avoid exposing identification data unnecessarily.

---

# 146. REPORT TRUSTWORTHINESS

Every report must be traceable back to source transactions.

Do not store arbitrary dashboard totals just to make the UI look complete.

---

# 147. DEVELOPMENT PHASES

Implement in phases.

## Phase 1 — Foundation

Build:

```text
Laravel application
Environment configuration
MySQL
Migrations
Seeders
Authentication
RBAC
Base layout
Navigation
Error pages
Audit foundation
```

Exit criteria:

```text
User can authenticate
Users have roles
Permissions work
Unauthorized access is blocked
Database is operational
MPA routing works
```

---

# 148. PHASE 2 — HOTEL CORE

Build:

```text
Hotels
Branches
Departments
Employees
Floors
Room Types
Rooms
Amenities
Guests
Booking Sources
```

Exit criteria:

```text
Hotel can be configured
Rooms can be configured
Guests can be created
Roles can access appropriate records
```

---

# 149. PHASE 3 — RESERVATIONS

Build:

```text
Reservations
Availability
Calendar
Room board
Reservation workflow
Conflict handling
```

Exit criteria:

```text
Reservation works
Calendar works
Double booking is prevented
Concurrent booking test passes
```

---

# 150. PHASE 4 — STAYS & FINANCE

Build:

```text
Check-in
Stay
Folio
Invoice
Payment
Refund
Checkout
```

Exit criteria:

```text
Guest can check in
Charges are recorded
Invoice is correct
Payment works
Checkout works
Room becomes dirty
```

---

# 151. PHASE 5 — OPERATIONS

Build:

```text
Housekeeping
Maintenance
Notifications
Operational reports
```

Exit criteria:

```text
Rooms can be cleaned
Rooms can be inspected
Maintenance blocks rooms
Operational dashboards work
```

---

# 152. PHASE 6 — RESTAURANT & INVENTORY

Build:

```text
Restaurant
POS
Menu
Orders
Room charges
Inventory
Suppliers
Purchases
Stock movements
Expenses
```

Exit criteria:

```text
Restaurant works
Room charges work exactly once
Inventory updates correctly
Purchase receiving works
Expenses work
```

---

# 153. PHASE 7 — REPORTING

Build:

```text
Occupancy
Reservations
Revenue
Payments
Expenses
Financial summary
Housekeeping
Inventory
```

Exit criteria:

```text
Reports match transactional source data
Filters work
Permissions work
Exports/printing work where implemented
```

---

# 154. PHASE 8 — HARDENING

Perform:

```text
Security audit
Authorization audit
Database integrity audit
Concurrency tests
Performance optimization
UI review
Accessibility review
Regression testing
Documentation review
Deployment review
```

---

# 155. DEVELOPMENT LOOP

For every feature:

```text
Requirement
↓
Database design
↓
Migration
↓
Model
↓
Relationship
↓
Policy
↓
Form Request
↓
Service/Action
↓
Controller
↓
Route
↓
Blade view
↓
TypeScript enhancement
↓
Test
↓
Security test
↓
Manual verification
↓
Documentation
```

Do not skip directly from idea to UI.

---

# 156. CODE QUALITY

Use:

- Strong naming
- Type declarations
- Strict TypeScript
- Small classes
- Single responsibility
- Reusable components
- Reusable services
- Clear domain boundaries
- Dependency injection
- Laravel conventions

Avoid:

- Giant classes
- Giant controllers
- Giant TypeScript files
- Copy-paste business logic
- Global mutable state
- Magic numbers
- Hard-coded hotel settings
- Business rules inside views

---

# 157. LARAVEL BEST PRACTICES

Prefer Laravel-native features where suitable:

```text
Eloquent
Form Requests
Policies
Middleware
Events
Listeners
Notifications
Jobs
Queues
Scheduler
Cache
Validation
Route Model Binding
Database Transactions
```

Do not reinvent functionality Laravel already provides well.

---

# 158. TYPESCRIPT BEST PRACTICES

Use:

```text
Strict mode
Modules
Interfaces
Types
Reusable functions
Typed responses
Error handling
DOM-safe operations
```

Avoid:

```text
any
global variables
huge files
duplicated HTTP code
duplicated validation logic
```

---

# 159. COMPONENT REUSE

Blade components should be created for reusable UI.

Examples:

```text
<Button>
<Input>
<Select>
<Modal>
<Alert>
<Badge>
<Table>
<Pagination>
<Breadcrumb>
<EmptyState>
<LoadingState>
<StatusBadge>
```

Do not over-engineer components.

---

# 160. API / FETCH UTILITY

Create a typed TypeScript request helper.

It should handle:

```text
CSRF
JSON parsing
Validation errors
401
403
404
409
422
429
500
Network errors
```

Do not duplicate fetch boilerplate on every page.

---

# 161. LOADING STATES

Every async interaction should provide appropriate feedback.

Examples:

```text
Checking room availability...
Processing payment...
Loading reservations...
Saving guest...
Applying room charge...
```

Do not allow users to submit the same critical action repeatedly while it is processing.

---

# 162. SUCCESS STATES

Use consistent:

- Toasts
- Alerts
- Redirect messages
- Inline messages

Examples:

```text
Reservation created successfully.
Payment recorded successfully.
Room marked as clean.
Inventory received successfully.
```

---

# 163. EMPTY STATES

Do not leave tables blank.

Examples:

```text
No reservations found.
No guests found.
No pending housekeeping tasks.
No payments found for this period.
```

Provide useful next actions where appropriate.

---

# 164. ERROR STATES

Errors must be understandable.

Avoid:

```text
Error 500.
```

Prefer:

```text
We could not complete the checkout.
No changes were saved. Please try again or contact an administrator.
```

Detailed technical information belongs in logs.

---

# 165. DESTRUCTIVE ACTIONS

Protect actions such as:

```text
Delete
Cancel
Refund
Void
Deactivate
Stock Adjustment
```

with:

```text
Permission
Validation
Confirmation
Audit
```

Where historical information matters, prefer:

```text
Archive
Cancel
Void
Refund
Reverse
```

over destructive deletion.

---

# 166. FINANCIAL RECORD IMMUTABILITY

Financial history must be preserved.

Do not allow arbitrary deletion of:

```text
Payments
Refunds
Invoices
Financial transactions
Stock movements
Audit logs
```

Use controlled reversal mechanisms.

---

# 167. DEMO ENVIRONMENT

Seed enough data to demonstrate:

```text
Multiple room states
Multiple guests
Reservations
Active stays
Housekeeping tasks
Maintenance tickets
Invoices
Payments
Restaurant orders
Inventory
Expenses
```

The dashboard should look realistic immediately after seeding.

---

# 168. README

Create detailed documentation.

Include:

```text
Project overview
Architecture
Requirements
Installation
Environment variables
Database setup
Migrations
Seeders
Demo accounts
Development commands
Build commands
Testing
Security
Deployment
Backup
Troubleshooting
```

---

# 169. GIT

Create a professional `.gitignore`.

Never commit:

```text
.env
vendor/
node_modules/
storage/logs/*
sensitive uploads
secrets
```

Use meaningful commit messages when working in a Git repository.

---

# 170. VERSIONING

Document important schema/application changes.

Keep migrations synchronized with application behavior.

Never modify an already-applied production migration carelessly.

Create new migrations for changes.

---

# 171. PRODUCTION SAFETY

Before declaring the system production-ready:

Verify:

```text
APP_DEBUG=false
HTTPS enabled
Secure cookies enabled
Environment secrets protected
Database credentials protected
Logs protected
Uploads protected
Authorization enabled
CSRF active
Rate limiting active
Backups configured
Queues configured if used
Scheduler configured if used
```

---

# 172. SECURITY REVIEW

Perform a final review for:

```text
Authentication bypass
Authorization bypass
IDOR
SQL injection
XSS
CSRF
Session security
File uploads
Privilege escalation
Branch isolation
Sensitive information exposure
Mass assignment
Unsafe model binding
Improper validation
Improper error disclosure
Race conditions
Duplicate financial requests
```

---

# 173. MASS ASSIGNMENT

Be careful with Laravel model assignment.

Use:

```text
fillable
guarded
validated request data
```

Never blindly pass:

```php
$request->all()
```

into sensitive model updates.

---

# 174. ROUTE MODEL BINDING SECURITY

Do not assume route model binding automatically makes a resource accessible.

Example:

```text
/reservations/{reservation}
```

must still verify:

```text
permission
branch
ownership/scope
business state
```

---

# 175. BUSINESS CONFLICT RESPONSES

Example:

Two users attempt to reserve the same room.

One succeeds.

The second should receive:

```text
409 Conflict
```

with a friendly message:

```text
The selected room is no longer available for those dates.
Please select another room.
```

---

# 176. NO FAKE DATA

Do not use hard-coded fake production-looking statistics in dashboards after the real backend is implemented.

Dashboards must derive from actual database data.

Seeded demo data is acceptable for development.

---

# 177. NO FAKE BUTTONS

Every visible action must:

- Work
- Be permission protected
- Have appropriate validation
- Have a meaningful response

If an action is intentionally unavailable, visually communicate that clearly.

---

# 178. NO PLACEHOLDER BUSINESS LOGIC

Never implement security like:

```php
return true;
```

Never implement availability as:

```php
return true;
```

Never implement payment processing as:

```php
$status = 'paid';
```

without real validation and transactional logic.

Never fake inventory calculations.

---

# 179. ACCEPTANCE CRITERIA

The system is acceptable only when:

```text
User can authenticate
User receives appropriate dashboard
Permissions are enforced server-side
Branch scope is enforced
Rooms can be managed
Guests can be managed
Reservations can be created
Double bookings are prevented
Availability is accurate
Check-in works
Stays work
Folios work
Invoices work
Payments work
Refunds work
Checkout works
Housekeeping works
Maintenance works
Restaurant works
Room charges work exactly once
Inventory works
Stock movements work
Expenses work
Reports work
Audit logs work
Printing works
Responsive pages work
Security tests pass
Critical workflows are tested
```

---

# 180. DEFINITION OF DONE

A feature is NOT considered complete because the UI has been created.

A feature is complete only when:

```text
[ ] Route exists
[ ] Authorization exists
[ ] Validation exists
[ ] Database migration exists
[ ] Model exists where required
[ ] Relationships exist
[ ] Business logic exists
[ ] Transaction handling exists where required
[ ] Blade page exists
[ ] TypeScript enhancement exists where required
[ ] Error handling exists
[ ] Loading state exists where required
[ ] Empty state exists
[ ] Success feedback exists
[ ] Audit behavior exists where required
[ ] Tests exist
[ ] Security has been reviewed
[ ] Documentation is updated
```

---

# 181. SELF-REVIEW

After every major module, review it as:

## Software Architect

Is the architecture maintainable?

## Laravel Engineer

Are Laravel conventions being used properly?

## TypeScript Engineer

Is the frontend typed, modular, and maintainable?

## Security Engineer

Can the functionality be bypassed?

## Database Engineer

Can the data become inconsistent?

## QA Engineer

What happens when something goes wrong?

## Hotel Operations Analyst

Does the workflow make sense for actual staff?

---

# 182. BUILD ORDER

Implement in this order:

```text
1. Laravel foundation
2. Environment configuration
3. Database
4. Migrations
5. Models
6. Authentication
7. RBAC
8. Policies
9. Base Blade layout
10. TypeScript/Vite architecture
11. Hotel configuration
12. Branches
13. Staff
14. Rooms
15. Guests
16. Reservations
17. Availability
18. Check-in
19. Stays
20. Folios
21. Invoices
22. Payments
23. Refunds
24. Checkout
25. Housekeeping
26. Maintenance
27. Restaurant/POS
28. Inventory
29. Suppliers
30. Purchases
31. Stock movements
32. Expenses
33. Notifications
34. Audit logs
35. Reports
36. Testing
37. Security hardening
38. Performance optimization
39. Documentation
40. Deployment preparation
```

---

# 183. CRITICAL ARCHITECTURAL SUMMARY

The final system must follow:

```text
                    BROWSER
                       │
                       ▼
                 Laravel Routes
                       │
                       ▼
                  Middleware
                       │
                       ▼
                   Policies
                       │
                       ▼
                Form Requests
                       │
                       ▼
                  Controllers
                       │
                       ▼
              Services / Actions
                       │
                       ▼
                 Eloquent ORM
                       │
                       ▼
                    MySQL
```

Frontend:

```text
Laravel Blade
      │
      ▼
HTML
      │
      ▼
Tailwind CSS
      │
      ▼
TypeScript
      │
      ├── Calendar
      ├── Tables
      ├── Charts
      ├── Forms
      ├── Search
      ├── Filters
      ├── POS interactions
      └── Async requests
```

Navigation:

```text
Page
↓
Laravel Route
↓
Controller
↓
Blade View
↓
New Page
```

This is an **MPA**, not an SPA.

---

# 184. FINAL COMMAND TO THE CODING AGENT

Begin by inspecting the existing repository.

Do not immediately generate hundreds of files.

First determine:

```text
Current Laravel version
Existing application structure
Existing dependencies
Current database
Current migrations
Existing routes
Existing Blade views
Existing TypeScript
Existing authentication
Existing tests
```

Then reconcile the repository against this specification.

After that:

```text
1. Establish the architecture.
2. Establish database migrations.
3. Establish models and relationships.
4. Establish authentication.
5. Establish RBAC and policies.
6. Establish base Blade layout.
7. Establish TypeScript/Vite architecture.
8. Build each module incrementally.
9. Test each module.
10. Perform security review.
11. Perform regression tests.
12. Optimize performance.
13. Update documentation.
14. Verify acceptance criteria.
```

Do not skip foundational work to make the UI look complete.

Do not convert the system into a SPA.

Do not use client-side routing.

Do not put the whole system into one page.

Do not put the entire business logic into controllers.

Do not put the entire frontend into one TypeScript file.

Do not trust the browser.

Do not trust user input.

Do not trust client-side authorization.

Do not trust client-side financial calculations.

Do not create fake functionality.

Do not claim a feature is complete unless it has been tested.

Build this as a **serious, secure, enterprise-oriented, modular, multi-page Hotel Management System using Laravel, PHP, TypeScript, Blade, Tailwind CSS, Vite, and MySQL.**