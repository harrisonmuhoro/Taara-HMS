# HOTEL MANAGEMENT SYSTEM
# MASTER ROLE, PERMISSION & ACCESS-CONTROL PROMPT

---

# 1. PURPOSE

Implement a complete, secure, enterprise-grade **Role-Based Access Control (RBAC)** system for the Hotel Management System.

The role system must control:

- What a user can see
- What a user can create
- What a user can edit
- What a user can delete
- What a user can approve
- What a user can cancel
- What a user can refund
- What a user can access by branch
- What actions a user can perform on specific records

RBAC must be enforced **server-side** using Laravel authorization mechanisms.

UI restrictions are supplementary only.

---

# 2. NON-NEGOTIABLE SECURITY RULE

Never treat frontend visibility as authorization.

This is NOT sufficient:

```blade
@if($user->hasRole('manager'))
    <button>Delete</button>
@endif
```

The backend must independently verify:

```text
Authenticated user
        ↓
Role
        ↓
Permission
        ↓
Branch scope
        ↓
Entity access
        ↓
Business state
        ↓
Allow / Deny
```

A user who manually enters a URL, modifies an ID, submits an HTTP request, or calls an internal endpoint directly must still be denied if they do not have permission.

---

# 3. ROLE ARCHITECTURE

Implement the following roles.

```text
1. Super Administrator
2. Hotel Manager
3. Front Desk / Receptionist
4. Reservations Officer
5. Housekeeping Staff
6. Housekeeping Supervisor
7. Accountant / Finance Officer
8. Restaurant Cashier
9. Inventory Officer
10. Maintenance Staff
```

The system must support adding future roles without redesigning authorization.

---

# 4. PERMISSION MODEL

Do not hard-code authorization using only role names.

Use granular permissions.

Permissions should follow a consistent naming convention:

```text
module.action
```

Examples:

```text
guests.view
guests.create
guests.edit

reservations.view
reservations.create
reservations.edit
reservations.confirm
reservations.cancel

payments.view
payments.create
payments.refund
```

---

# 5. PERMISSION CATEGORIES

Create permissions for the following modules.

## Dashboard

```text
dashboard.view
dashboard.view_operational_metrics
dashboard.view_financial_metrics
```

## Hotel / Branch

```text
hotel.view
hotel.edit

branches.view
branches.create
branches.edit
branches.delete
```

## Staff

```text
staff.view
staff.create
staff.edit
staff.deactivate
```

## Users

```text
users.view
users.create
users.edit
users.deactivate
users.reset_password
users.assign_role
```

## Roles

```text
roles.view
roles.create
roles.edit
roles.delete
roles.assign_permissions
```

## Permissions

```text
permissions.view
permissions.manage
```

## Floors

```text
floors.view
floors.create
floors.edit
floors.delete
```

## Room Types

```text
room_types.view
room_types.create
room_types.edit
room_types.delete
```

## Rooms

```text
rooms.view
rooms.create
rooms.edit
rooms.delete
rooms.manage_status
rooms.block
rooms.unblock
```

## Amenities

```text
amenities.view
amenities.create
amenities.edit
amenities.delete
```

## Guests

```text
guests.view
guests.create
guests.edit
guests.delete
guests.view_sensitive
guests.view_history
```

## Reservations

```text
reservations.view
reservations.create
reservations.edit
reservations.confirm
reservations.cancel
reservations.no_show
reservations.assign_room
reservations.change_room
```

## Availability

```text
availability.view
availability.search
availability.override
```

`availability.override` must be highly restricted.

## Stays

```text
stays.view
stays.create
stays.check_in
stays.check_out
stays.extend
stays.change_room
```

## Folios

```text
folios.view
folios.create
folios.add_charge
folios.edit_charge
folios.remove_charge
folios.close
```

## Invoices

```text
invoices.view
invoices.create
invoices.edit
invoices.issue
invoices.void
invoices.print
```

## Payments

```text
payments.view
payments.create
payments.edit
payments.void
payments.refund
payments.print_receipt
```

## Expenses

```text
expenses.view
expenses.create
expenses.edit
expenses.submit
expenses.approve
expenses.reject
expenses.post
expenses.mark_paid
```

## Housekeeping

```text
housekeeping.view
housekeeping.assign
housekeeping.update
housekeeping.inspect
housekeeping.approve_room
housekeeping.reopen_task
housekeeping.view_reports
```

## Maintenance

```text
maintenance.view
maintenance.create
maintenance.assign
maintenance.update
maintenance.close
maintenance.manage_rooms
maintenance.view_costs
```

## Restaurant

```text
restaurant.view
restaurant.menu_view
restaurant.menu_create
restaurant.menu_edit
restaurant.menu_delete
restaurant.order_create
restaurant.order_edit
restaurant.order_cancel
restaurant.order_void
restaurant.room_charge
restaurant.process_payment
restaurant.print_receipt
restaurant.view_reports
```

## Inventory

```text
inventory.view
inventory.create_product
inventory.edit_product
inventory.delete_product
inventory.manage_categories
inventory.view_stock
inventory.receive_purchase
inventory.create_purchase
inventory.edit_purchase
inventory.adjust_stock
inventory.view_movements
inventory.view_reports
```

## Suppliers

```text
suppliers.view
suppliers.create
suppliers.edit
suppliers.delete
```

## Reports

```text
reports.view
reports.occupancy
reports.reservations
reports.revenue
reports.payments
reports.expenses
reports.financial
reports.housekeeping
reports.inventory
reports.restaurant
```

## Notifications

```text
notifications.view
notifications.manage
```

## Audit Logs

```text
audit_logs.view
audit_logs.export
```

## Settings

```text
settings.view
settings.edit
settings.manage_policies
settings.manage_tax
settings.manage_payment_methods
```

---

# 6. SUPER ADMINISTRATOR

## Purpose

The Super Administrator manages the entire hotel system.

## Access

Full system access.

Can access:

```text
Dashboard
Hotel
Branches
Staff
Users
Roles
Permissions
Floors
Room Types
Rooms
Amenities
Guests
Reservations
Availability
Stays
Folios
Invoices
Payments
Refunds
Housekeeping
Maintenance
Restaurant
Inventory
Suppliers
Purchases
Expenses
Reports
Notifications
Audit Logs
Settings
```

## Responsibilities

Can:

- Create users
- Disable users
- Assign roles
- Manage permissions
- Configure hotel
- Configure branches
- Configure payment methods
- Configure tax
- Configure policies
- Manage rooms
- View financial information
- View audit logs
- Access all operational data
- Configure system settings

## Restrictions

None within the application unless explicitly configured as a separate security administrator model.

Every sensitive action must still be audited.

---

# 7. HOTEL MANAGER

## Purpose

Manages overall hotel operations.

## Can access

```text
Dashboard
Hotel operational settings
Staff
Guests
Reservations
Availability
Rooms
Housekeeping
Maintenance
Restaurant
Inventory
Suppliers
Purchases
Expenses
Invoices
Payments
Reports
```

## Can

### Guests

- View guests
- Create guests
- Edit guests
- View guest history

### Reservations

- View reservations
- Create reservations
- Edit reservations
- Confirm reservations
- Cancel reservations
- Assign rooms
- Change rooms
- Mark no-show

### Rooms

- View rooms
- Edit rooms
- Manage operational status
- Block rooms
- Unblock rooms

### Housekeeping

- View tasks
- Assign tasks
- Review inspections
- View reports

### Maintenance

- View tickets
- Assign tickets
- View costs
- Manage room availability related to maintenance

### Finance

- View invoices
- View payments
- View financial reports
- View expenses
- Approve expenses according to policy

### Restaurant

- View restaurant
- View sales
- View reports
- Review orders

### Inventory

- View stock
- View purchases
- View suppliers
- View inventory reports

## Restrictions

Do not automatically give:

```text
roles.manage
permissions.manage
system security administration
```

unless explicitly assigned.

---

# 8. FRONT DESK / RECEPTIONIST

## Purpose

Handles guest-facing hotel operations.

## Primary modules

```text
Dashboard
Guests
Reservations
Availability
Front Desk
Stays
Folios
Invoices
Permitted Payments
Rooms
```

## Can

### Guests

- Search guests
- Create guests
- Edit guests
- View guest history where permitted

### Reservations

- Create reservations
- View reservations
- Edit reservations
- Confirm reservations where permitted
- Cancel reservations according to policy
- Search availability
- Assign rooms

### Check-In

Can:

- Verify guest
- Verify reservation
- Check in guest
- Assign/confirm room
- Open stay
- Open folio

### Check-Out

Can:

- View stay
- View folio
- Generate invoice
- Collect payment
- Complete checkout when settlement rules are satisfied

### Payments

Can:

- View permitted payment records
- Record permitted payments
- Print receipts

## Restrictions

Cannot by default:

```text
Refund payments
Void invoices
Manage users
Manage roles
Manage permissions
Modify system settings
Approve expenses
Adjust inventory
View unrestricted financial reports
```

Any exception must be explicitly permissioned.

---

# 9. RESERVATIONS OFFICER

## Purpose

Manages hotel bookings and availability.

## Primary modules

```text
Reservations
Availability
Guests
Rooms
Booking Sources
```

## Can

- Create reservations
- Edit reservations
- Confirm reservations
- Cancel reservations
- Mark no-show
- Search availability
- Assign rooms
- Change room assignment
- View guest booking history

## Restrictions

Cannot by default:

```text
Refund payments
Approve expenses
Adjust stock
Manage users
Manage roles
Modify system settings
Process unrestricted financial operations
```

---

# 10. HOUSEKEEPING STAFF

## Purpose

Maintains room cleanliness and readiness.

## Primary modules

```text
Housekeeping
Rooms
Maintenance reporting
```

## Can

- View assigned rooms
- View assigned tasks
- Start cleaning
- Mark room clean
- Add housekeeping notes
- Report room problems
- Request maintenance
- View room operational information needed to perform assigned work

## Cannot

By default:

```text
View guest financial data
View payments
View invoices
View expenses
Refund payments
Create reservations
Check guests in
Check guests out
Manage users
View system settings
View unrestricted guest identification data
```

## Important privacy rule

Housekeeping staff should receive only the guest information necessary for their task.

---

# 11. HOUSEKEEPING SUPERVISOR

## Purpose

Manages housekeeping operations.

## Can

Everything required by housekeeping staff, plus:

- View all housekeeping tasks
- Assign rooms
- Reassign tasks
- Set task priorities
- Inspect rooms
- Approve room readiness
- Reopen tasks
- View housekeeping reports
- Monitor staff performance

## Cannot by default

```text
Financial administration
Refunds
User administration
Permission administration
System configuration
```

---

# 12. ACCOUNTANT / FINANCE OFFICER

## Purpose

Manages hotel financial operations.

## Primary modules

```text
Invoices
Payments
Refunds
Expenses
Financial Reports
Guest Folios
```

## Can

### Invoices

- View invoices
- Create invoices
- Issue invoices
- Print invoices
- Manage permitted invoice adjustments

### Payments

- View payments
- Record payments
- Reconcile payments
- Process refunds according to permission
- Void payments according to permission
- Print receipts

### Expenses

- Create expenses
- Edit expenses
- Submit expenses
- Review expenses
- Approve/reject expenses when assigned approval authority
- Post expenses
- Mark expenses as paid according to policy

### Reports

- Revenue
- Payments
- Expenses
- Financial summary
- Outstanding balances
- Refunds

## Restrictions

Cannot by default:

```text
Manage roles
Manage permissions
Manage security settings
Modify hotel configuration
Modify room configuration
Delete critical financial history
```

---

# 13. RESTAURANT CASHIER

## Purpose

Handles food and beverage transactions.

## Primary modules

```text
Restaurant
POS
Orders
Menu
Permitted Payments
```

## Can

- View menu
- Create restaurant orders
- Edit open orders
- Process permitted payments
- Print receipts
- Cancel/void orders according to permission
- Charge orders to active guest folios when permitted

## Room Charge Rules

Before charging to a room:

```text
Validate guest
↓
Validate active stay
↓
Validate folio
↓
Validate permission
↓
Create charge
```

The restaurant cashier must not be able to charge arbitrary room numbers without validation.

## Restrictions

Cannot by default:

```text
Manage users
Manage roles
Refund arbitrary hotel payments
Modify system settings
Adjust inventory directly
Approve expenses
```

---

# 14. INVENTORY OFFICER

## Purpose

Manages hotel inventory and purchasing.

## Primary modules

```text
Inventory
Products
Suppliers
Purchases
Stock Movements
Inventory Reports
```

## Can

### Products

- Create products
- Edit products
- Manage categories
- Set reorder levels
- View stock

### Suppliers

- Create suppliers
- Edit suppliers
- View supplier history

### Purchases

- Create purchases
- Edit purchases
- Receive purchases
- Manage purchase items

### Stock

- View movements
- Perform authorized stock adjustments
- Record damage
- Record returns
- Review stock levels
- Monitor low-stock alerts

## Restrictions

Cannot by default:

```text
Modify invoices
Refund hotel payments
Manage users
Manage permissions
Approve unrelated expenses
Modify reservations
Check in guests
```

---

# 15. MAINTENANCE STAFF

## Purpose

Keeps hotel facilities operational.

## Primary modules

```text
Maintenance
Rooms
```

## Can

- View assigned tickets
- Create maintenance tickets
- Update tickets
- Mark work in progress
- Mark resolved
- Add repair notes
- Record actual repair cost where authorized
- Report rooms requiring maintenance

## Can affect room availability

When properly authorized:

```text
Room
→ MAINTENANCE
```

But the system must validate that the user has permission.

## Restrictions

Cannot:

```text
View financial dashboards
Refund payments
Manage reservations
Manage users
Manage roles
Modify hotel configuration
```

---

# 16. ROLE × MODULE ACCESS MATRIX

Use this baseline:

| Module | Super Admin | Manager | Reception | Reservations | HK Staff | HK Supervisor | Accountant | Restaurant | Inventory | Maintenance |
|---|---|---|---|---|---|---|---|---|---|---|
| Dashboard | Full | Full | Operational | Operational | Task | HK | Finance | Restaurant | Inventory | Maintenance |
| Hotel Settings | Full | Limited | No | No | No | No | No | No | No | No |
| Users | Full | Limited | No | No | No | No | No | No | No | No |
| Roles | Full | No* | No | No | No | No | No | No | No | No |
| Permissions | Full | No* | No | No | No | No | No | No | No | No |
| Guests | Full | Full | Full | Full | Limited | Limited | Finance-related | Limited | No | Limited |
| Reservations | Full | Full | Full | Full | No | No | Limited | No | No | No |
| Availability | Full | Full | Full | Full | No | No | No | No | No | Limited |
| Rooms | Full | Full | View/Assign | View/Assign | Task-related | Full HK | Limited | Limited | No | Maintenance |
| Check-in | Full | Full | Full | No/limited | No | No | No | No | No | No |
| Check-out | Full | Full | Full | No | No | No | Finance support | No | No | No |
| Folios | Full | Full | Operational | Limited | No | No | Full | Room-charge related | No | No |
| Invoices | Full | Full | Limited | No | No | No | Full | Limited | No | No |
| Payments | Full | Full | Limited | No | No | No | Full | POS-specific | No | No |
| Refunds | Full | Controlled | No* | No | No | No | Controlled | No* | No | No |
| Housekeeping | Full | Full | View | No | Full task | Full | No | No | No | Limited |
| Maintenance | Full | Full | Report | Report | Report | Report | Cost view | Report | No | Full |
| Restaurant | Full | Full | Limited | No | No | No | Financial view | Full | Stock linkage | No |
| Inventory | Full | Full | No | No | No | No | Financial view | Limited | Full | Maintenance-related |
| Expenses | Full | Full | No | No | No | No | Full | Limited | Limited | Limited |
| Reports | Full | Full | Limited | Reservations | HK | HK | Finance | Restaurant | Inventory | Maintenance |
| Audit Logs | Full | Limited | No | No | No | No | Limited | No | Limited | Limited |
| System Settings | Full | Limited | No | No | No | No | No | No | No | No |

`*` means no default access; access should only be granted through explicit permission if the business requires it.

This matrix is a **starting baseline**, not a substitute for granular permission checks.

---

# 17. LEAST PRIVILEGE

Every user must receive the minimum access required to perform their job.

Never implement:

```text
"Hotel Manager gets everything because they are a manager."
```

unless the explicit permission list requires it.

Never implement:

```text
"Receptionist can access all finance because they take payments."
```

Payment collection does not automatically mean:

```text
refund
void
expense approval
financial reporting
```

---

# 18. SEPARATION OF DUTIES

Where practical, separate sensitive operations.

Examples:

```text
Person A creates expense
        ↓
Person B approves expense
```

and:

```text
Person A records payment
        ↓
Person B performs refund
```

Do not automatically allow every user who can create a transaction to approve/reverse it.

---

# 19. SENSITIVE PERMISSIONS

Treat these as high-risk:

```text
roles.manage
permissions.manage
users.assign_role
payments.refund
payments.void
invoices.void
expenses.approve
inventory.adjust
availability.override
settings.manage
audit_logs.view
audit_logs.export
```

These permissions should be explicitly assigned.

Every use must be audited.

---

# 20. BRANCH-LEVEL AUTHORIZATION

All branch-scoped users must be prevented from accessing another branch's records.

Example:

```text
User
Branch A
        ↓
Reservation
Branch B
        ↓
DENY
```

Even when the URL contains a valid ID.

Example:

```text
/reservations/1500
```

must verify:

```text
User has permission
AND
Reservation belongs to authorized branch
```

---

# 21. ENTITY-LEVEL AUTHORIZATION

Do not stop at:

```text
$user->can('reservations.view')
```

Also verify whether the user can access the **specific reservation**.

For branch-scoped resources:

```text
Permission
+
Branch
+
Entity
```

must all pass.

Use Laravel Policies.

---

# 22. POLICY REQUIREMENTS

Create policies for sensitive resources.

Examples:

```text
UserPolicy
GuestPolicy
ReservationPolicy
RoomPolicy
StayPolicy
FolioPolicy
InvoicePolicy
PaymentPolicy
RefundPolicy
HousekeepingTaskPolicy
MaintenanceTicketPolicy
RestaurantOrderPolicy
ProductPolicy
PurchasePolicy
ExpensePolicy
ReportPolicy
SettingPolicy
AuditLogPolicy
```

Use policies for:

```text
view
create
update
delete
approve
cancel
refund
void
```

where appropriate.

---

# 23. MIDDLEWARE REQUIREMENTS

Use Laravel middleware for broad access controls.

Examples:

```text
auth
verified where required
branch.scope
role/permission middleware
```

Do not put all authorization logic into one giant middleware.

Use policies for resource-specific authorization.

---

# 24. BLADE UI RULE

The UI should respect permissions.

Example:

```blade
@can('payments.refund')
    <button>Refund Payment</button>
@endcan
```

But this only improves UX.

It does NOT replace backend authorization.

---

# 25. DIRECT URL TESTING

The system must be tested by manually entering URLs.

Examples:

```text
Housekeeping user:
/finance/payments

Receptionist:
/admin/roles

Restaurant cashier:
/finance/refunds

Inventory officer:
/users

Maintenance:
/reservations
```

Unauthorized requests must return:

```text
403 Forbidden
```

or an appropriate safe authorization response.

---

# 26. DIRECT REQUEST TESTING

Do not only test the UI.

Send direct POST/PUT/PATCH/DELETE requests against protected endpoints.

Example:

```text
POST /payments/100/refund
```

A user without:

```text
payments.refund
```

must be rejected.

---

# 27. ROLE ASSIGNMENT SECURITY

Only authorized administrators may assign roles.

A user must not be able to modify their own role unless explicitly authorized.

Prevent privilege escalation such as:

```text
Receptionist
↓
changes own role
↓
Super Administrator
```

This must be impossible.

---

# 28. PERMISSION ESCALATION

A user cannot grant themselves permissions.

A user cannot grant another user permissions they themselves cannot administer.

Protect:

```text
roles.manage
permissions.manage
users.assign_role
```

with elevated authorization.

---

# 29. DEACTIVATED USERS

If an account is disabled:

```text
User
↓
Account inactive
↓
Protected request
↓
DENY
```

The user should not remain able to operate using an old session.

Invalidate active sessions where practical.

---

# 30. AUDIT REQUIREMENTS FOR AUTHORIZATION

Audit:

```text
Role created
Role modified
Role deleted
Permission assigned
Permission removed
Role assigned to user
Role removed from user
User activated
User deactivated
Sensitive permission used
Refund performed
Payment voided
Invoice voided
Expense approved
Inventory adjusted
Availability override
Settings changed
```

---

# 31. ROLE-AWARE DASHBOARDS

Do not show every dashboard widget to every role.

Examples:

### Reception

Show:

```text
Arrivals
Departures
Available Rooms
Active Guests
Reservations
Check-in
Check-out
```

Do not automatically show:

```text
Profit
Expenses
Refund totals
Payroll
Security administration
```

### Housekeeping

Show:

```text
Dirty Rooms
Cleaning
Inspections
Assigned Tasks
Maintenance Reports
```

### Accountant

Show:

```text
Revenue
Payments
Outstanding Balances
Refunds
Expenses
Financial Reports
```

### Restaurant

Show:

```text
Orders
Sales
Open Tables/Orders
Room Charges
Restaurant Reports
```

---

# 32. NAVIGATION SECURITY

Navigation menus must be generated based on the current user's permissions.

However:

```text
Hidden menu ≠ security
```

Server-side authorization remains mandatory.

---

# 33. ROLE-AWARE PAGE ACCESS

A page must verify permission before rendering.

Example:

```text
GET /finance/refunds
```

requires appropriate permission before loading the page.

Do not render sensitive pages and merely hide their content.

---

# 34. FINANCIAL ROLE RESTRICTIONS

Financial permissions must be separated.

Do not create one permission:

```text
finance.all
```

for normal staff.

Instead separate:

```text
invoices.view
invoices.issue
invoices.void

payments.view
payments.create
payments.void
payments.refund

expenses.view
expenses.create
expenses.approve
expenses.post
expenses.mark_paid
```

---

# 35. ROOM MANAGEMENT RESTRICTIONS

A user who can view rooms does not automatically have permission to:

```text
Block room
Change room operational status
Override availability
Delete room
```

Separate these permissions.

---

# 36. HOUSEKEEPING RESTRICTIONS

A housekeeping staff member may:

```text
DIRTY → CLEANING → CLEAN
```

but should not automatically be allowed to manipulate unrelated operational states.

Room readiness approval should generally belong to:

```text
Housekeeping Supervisor
```

or another explicitly authorized role.

---

# 37. RESERVATION RESTRICTIONS

Creating a reservation does not automatically grant permission to:

```text
Refund payment
Void invoice
Override availability
Change hotel pricing rules
```

These remain separate permissions.

---

# 38. RESTAURANT RESTRICTIONS

Restaurant staff should only access restaurant-related financial operations.

For example:

```text
restaurant.order_create
```

does not imply:

```text
payments.refund
```

and:

```text
restaurant.room_charge
```

does not imply:

```text
invoices.void
```

---

# 39. INVENTORY RESTRICTIONS

An inventory officer may adjust stock only when:

```text
inventory.adjust_stock
```

is explicitly granted.

Inventory access does not automatically grant:

```text
payments.refund
expenses.approve
users.manage
```

---

# 40. REPORT ACCESS

Separate reports by domain.

Examples:

```text
reports.occupancy
reports.reservations
reports.revenue
reports.payments
reports.expenses
reports.financial
reports.housekeeping
reports.inventory
reports.restaurant
```

A housekeeping user should not automatically receive financial reports.

---

# 41. AUDIT LOG ACCESS

Audit logs contain sensitive operational information.

Default access:

```text
Super Administrator = Full
Hotel Manager = Limited
Accountant = Limited where financially relevant
Other roles = No access
```

All access to sensitive audit information should itself be auditable where appropriate.

---

# 42. SETTINGS ACCESS

Separate:

```text
settings.view
settings.edit
settings.manage_policies
settings.manage_tax
settings.manage_payment_methods
```

Do not allow ordinary staff to modify global system settings.

---

# 43. PERMISSION SEEDER

Create a permission seeder containing every permission.

Then create role seeders assigning appropriate permissions.

Do not manually insert permissions ad hoc during application runtime.

---

# 44. ROLE SEEDER

Create the ten default roles.

Then assign permissions according to this prompt.

The seeder should be repeatable/idempotent.

---

# 45. PERMISSION-ROLE MATRIX IMPLEMENTATION

Represent authorization data through database relationships.

Conceptually:

```text
Users
   ↓
Roles
   ↓
Permissions
```

Use Eloquent relationships.

Do not hard-code hundreds of role checks throughout controllers.

---

# 46. EXAMPLE POLICY

Conceptual example:

```php
public function update(User $user, Reservation $reservation): bool
{
    return $user->can('reservations.edit')
        && $user->branch_id === $reservation->branch_id;
}
```

Adapt this to the actual multi-branch architecture.

---

# 47. ADMIN ROLE MANAGEMENT

Create a dedicated role management interface.

Administrators should be able to:

```text
View role
Create role
Edit role
Assign permissions
Remove permissions
View assigned users
```

Do not permit normal staff to modify roles.

---

# 48. USER MANAGEMENT

Administrators should be able to:

```text
View users
Create user
Edit user
Deactivate user
Assign role
Remove role
Reset password
```

Every role assignment should be audited.

---

# 49. CUSTOM ROLES

The system should support creation of custom roles in the future.

Example:

```text
Night Auditor
Spa Manager
General Manager
Security Officer
Purchasing Manager
```

These should be composed from existing permissions.

Do not require code changes to create a normal custom role.

---

# 50. ROLE DISPLAY

Each user's profile should clearly display:

```text
Name
Employee
Department
Branch
Role(s)
Account status
Last login
```

Do not expose password information.

---

# 51. USER PROFILE SECURITY

Users may change their own:

```text
Name where permitted
Phone
Password
Profile information
```

Users must not automatically change:

```text
Role
Permissions
Branch
Account status
```

unless explicitly authorized.

---

# 52. SUPER ADMIN SAFETY

Prevent accidental removal of the final active super administrator unless an explicit emergency/administrative workflow exists.

Do not allow the system to end up with:

```text
0 active super administrators
```

through accidental role deletion.

---

# 53. ROLE DELETION

Before deleting a role:

```text
Check assigned users
Check assigned permissions
```

Do not silently orphan users.

Require reassignment or controlled removal.

---

# 54. PERMISSION DELETION

Avoid deleting permissions that are still assigned.

Prefer:

```text
deprecate
```

or controlled migration.

---

# 55. ACCESS DENIED UI

When authorization fails, display:

```text
403 Forbidden

You do not have permission to perform this action.
```

Do not expose internal authorization details.

---

# 56. SECURITY TESTING

For every role, attempt prohibited actions.

Example:

### Receptionist

Try:

```text
Refund payment
Void invoice
Manage users
Manage roles
Adjust inventory
Approve expense
Change system settings
```

Expected:

```text
DENIED
```

---

# 57. ROLE TEST MATRIX

Create automated authorization tests for all roles.

Example:

```text
Receptionist cannot refund payment
Receptionist cannot manage roles
Housekeeping cannot view financial reports
Restaurant cashier cannot approve expenses
Inventory officer cannot manage users
Maintenance cannot modify reservations
Reservations officer cannot refund payments
```

Also test permitted operations.

---

# 58. BRANCH ISOLATION TEST

Create:

```text
Branch A
Branch B
```

Create users in each.

Verify:

```text
Branch A User
      ↓
Branch A data = ALLOWED
Branch B data = DENIED
```

unless explicit cross-branch permissions exist.

---

# 59. AUTHORIZATION TESTING CHECKLIST

Test:

```text
[ ] Route authorization
[ ] Controller authorization
[ ] Policy authorization
[ ] Branch authorization
[ ] Record authorization
[ ] Role assignment
[ ] Permission assignment
[ ] Deactivated account
[ ] Direct URL access
[ ] Direct HTTP request
[ ] AJAX request
[ ] ID manipulation
[ ] Privilege escalation
[ ] Self-role modification
[ ] Cross-branch access
```

---

# 60. FINAL ACCESS-CONTROL PRINCIPLE

The final authorization decision must be based on:

```text
WHO is the user?
        +
WHAT role do they have?
        +
WHAT permission do they have?
        +
WHICH branch are they allowed to access?
        +
WHICH specific record are they accessing?
        +
IS the requested business action valid in the current state?
```

Only when all required checks pass:

```text
ALLOW
```

Otherwise:

```text
DENY
```

---

# 61. FINAL COMMAND

Implement this role and permission architecture throughout the entire Hotel Management System.

Do not merely create role dropdowns.

Do not merely hide buttons.

Do not merely check role names.

Implement real Laravel authorization with:

```text
Middleware
+
Policies
+
Permissions
+
Branch Scoping
+
Entity Authorization
+
Business-State Validation
+
Audit Logging
+
Automated Tests
```

The system must enforce least privilege, separation of duties, branch isolation, and server-side authorization.

Every role must have a clearly defined operational boundary.

Every sensitive permission must be explicit.

Every privileged action must be auditable.

The resulting system must make it technically difficult—not merely visually unlikely—for one employee to access or modify information outside their responsibilities.