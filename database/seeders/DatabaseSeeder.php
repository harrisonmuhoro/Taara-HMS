<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\BookingSource;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Floor;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HousekeepingTask;
use App\Models\InventoryCategory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceTicket;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hotel & Branch
        $hotel = Hotel::create([
            'name' => 'Grand Horizon Hotel',
            'legal_name' => 'Grand Horizon Hospitality Services Ltd',
            'registration_number' => 'GH-890412-KE',
            'email' => 'info@grandhorizon.co.ke',
            'phone' => '+254 700 123 456',
            'address' => '124 Hospitality Way, Westlands',
            'city' => 'Nairobi',
            'country' => 'Kenya',
            'currency' => 'KES',
            'timezone' => 'Africa/Nairobi',
            'check_in_time' => '14:00:00',
            'check_out_time' => '10:00:00',
            'tax_number' => 'P051239841Z',
            'status' => 'active',
        ]);

        $branch = Branch::create([
            'hotel_id' => $hotel->id,
            'name' => 'Nairobi Main Branch',
            'code' => 'NBO-MAIN',
            'address' => '124 Hospitality Way, Westlands, Nairobi',
            'phone' => '+254 700 123 456',
            'email' => 'nairobi@grandhorizon.co.ke',
            'status' => 'active',
        ]);

        // 2. Departments
        $depts = [
            'Administration' => 'Executive management & admin',
            'Front Desk' => 'Reception, check-in, check-out & guest relations',
            'Housekeeping' => 'Room cleaning, laundry & sanitation',
            'Food & Beverage' => 'Restaurant, room service & kitchen',
            'Finance' => 'Accounting, invoicing, expenses & billing',
            'Inventory' => 'Stock management, receiving & procurement',
            'Maintenance' => 'Facility repairs & mechanical upkeep',
        ];

        $departmentModels = [];
        foreach ($depts as $name => $desc) {
            $departmentModels[$name] = Department::create([
                'branch_id' => $branch->id,
                'name' => $name,
                'description' => $desc,
            ]);
        }

        // 3. Permissions
        $permissionsList = [
            ['name' => 'dashboard.view', 'module' => 'dashboard', 'description' => 'View operational dashboard'],
            ['name' => 'guests.view', 'module' => 'guests', 'description' => 'View guest profiles'],
            ['name' => 'guests.create', 'module' => 'guests', 'description' => 'Create new guest profiles'],
            ['name' => 'guests.edit', 'module' => 'guests', 'description' => 'Edit guest profiles'],
            ['name' => 'rooms.view', 'module' => 'rooms', 'description' => 'View room board and status'],
            ['name' => 'rooms.create', 'module' => 'rooms', 'description' => 'Create rooms and room types'],
            ['name' => 'rooms.edit', 'module' => 'rooms', 'description' => 'Edit room details'],
            ['name' => 'rooms.manage_status', 'module' => 'rooms', 'description' => 'Change room operational status'],
            ['name' => 'reservations.view', 'module' => 'reservations', 'description' => 'View reservation list and calendar'],
            ['name' => 'reservations.create', 'module' => 'reservations', 'description' => 'Create new reservations'],
            ['name' => 'reservations.edit', 'module' => 'reservations', 'description' => 'Edit reservation details'],
            ['name' => 'reservations.cancel', 'module' => 'reservations', 'description' => 'Cancel reservations'],
            ['name' => 'reservations.confirm', 'module' => 'reservations', 'description' => 'Confirm reservations'],
            ['name' => 'stays.view', 'module' => 'stays', 'description' => 'View active stays'],
            ['name' => 'stays.check_in', 'module' => 'stays', 'description' => 'Process guest check-in'],
            ['name' => 'stays.check_out', 'module' => 'stays', 'description' => 'Process guest check-out'],
            ['name' => 'folios.view', 'module' => 'folios', 'description' => 'View guest folios'],
            ['name' => 'folios.add_charge', 'module' => 'folios', 'description' => 'Add charges to folios'],
            ['name' => 'invoices.view', 'module' => 'finance', 'description' => 'View invoices'],
            ['name' => 'invoices.create', 'module' => 'finance', 'description' => 'Issue invoices'],
            ['name' => 'invoices.void', 'module' => 'finance', 'description' => 'Void invoices'],
            ['name' => 'payments.view', 'module' => 'finance', 'description' => 'View payments'],
            ['name' => 'payments.create', 'module' => 'finance', 'description' => 'Record payments'],
            ['name' => 'payments.refund', 'module' => 'finance', 'description' => 'Process refunds'],
            ['name' => 'housekeeping.view', 'module' => 'housekeeping', 'description' => 'View housekeeping tasks'],
            ['name' => 'housekeeping.assign', 'module' => 'housekeeping', 'description' => 'Assign cleaning tasks'],
            ['name' => 'housekeeping.update', 'module' => 'housekeeping', 'description' => 'Update task status'],
            ['name' => 'housekeeping.inspect', 'module' => 'housekeeping', 'description' => 'Perform room inspection'],
            ['name' => 'maintenance.view', 'module' => 'maintenance', 'description' => 'View maintenance tickets'],
            ['name' => 'maintenance.create', 'module' => 'maintenance', 'description' => 'Report maintenance issue'],
            ['name' => 'maintenance.assign', 'module' => 'maintenance', 'description' => 'Assign maintenance ticket'],
            ['name' => 'maintenance.update', 'module' => 'maintenance', 'description' => 'Update ticket status'],
            ['name' => 'restaurant.view', 'module' => 'restaurant', 'description' => 'View restaurant POS'],
            ['name' => 'restaurant.order_create', 'module' => 'restaurant', 'description' => 'Create POS orders'],
            ['name' => 'restaurant.order_cancel', 'module' => 'restaurant', 'description' => 'Cancel POS orders'],
            ['name' => 'restaurant.room_charge', 'module' => 'restaurant', 'description' => 'Post order to room folio'],
            ['name' => 'inventory.view', 'module' => 'inventory', 'description' => 'View inventory stock'],
            ['name' => 'inventory.create_product', 'module' => 'inventory', 'description' => 'Create inventory items'],
            ['name' => 'inventory.purchase', 'module' => 'inventory', 'description' => 'Create & receive purchases'],
            ['name' => 'inventory.adjust', 'module' => 'inventory', 'description' => 'Adjust stock levels'],
            ['name' => 'expenses.view', 'module' => 'expenses', 'description' => 'View expenses'],
            ['name' => 'expenses.create', 'module' => 'expenses', 'description' => 'Submit expenses'],
            ['name' => 'expenses.approve', 'module' => 'expenses', 'description' => 'Approve expenses'],
            ['name' => 'reports.view', 'module' => 'reports', 'description' => 'View analytical reports'],
            ['name' => 'users.view', 'module' => 'staff', 'description' => 'View user accounts'],
            ['name' => 'users.create', 'module' => 'staff', 'description' => 'Create user accounts'],
            ['name' => 'users.update', 'module' => 'staff', 'description' => 'Update user accounts'],
            ['name' => 'users.edit', 'module' => 'staff', 'description' => 'Edit user accounts'],
            ['name' => 'users.disable', 'module' => 'staff', 'description' => 'Disable user accounts'],
            ['name' => 'roles.manage', 'module' => 'staff', 'description' => 'Manage RBAC roles'],
            ['name' => 'permissions.manage', 'module' => 'staff', 'description' => 'Manage RBAC permissions'],
            ['name' => 'settings.view', 'module' => 'settings', 'description' => 'View system settings'],
            ['name' => 'settings.manage', 'module' => 'settings', 'description' => 'Modify system settings'],
            ['name' => 'audit_logs.view', 'module' => 'audit', 'description' => 'View audit log history'],
        ];

        $permissionModels = [];
        foreach ($permissionsList as $p) {
            $permissionModels[$p['name']] = Permission::create($p);
        }

        // 4. Roles
        $rolesData = [
            'Super Administrator' => array_keys($permissionModels),
            'Hotel Manager' => array_values(array_diff(array_keys($permissionModels), [
                'roles.manage', 'permissions.manage', 'settings.view', 'settings.manage',
            ])),
            'Front Desk / Receptionist' => [
                'dashboard.view', 'guests.view', 'guests.create', 'guests.edit',
                'rooms.view', 'rooms.manage_status', 'reservations.view', 'reservations.create',
                'reservations.edit', 'reservations.cancel', 'reservations.confirm',
                'stays.view', 'stays.check_in', 'stays.check_out', 'folios.view', 'folios.add_charge',
                'invoices.view', 'payments.view', 'payments.create'
            ],
            'Reservations Officer' => [
                'dashboard.view', 'guests.view', 'guests.create', 'guests.edit',
                'rooms.view', 'reservations.view', 'reservations.create', 'reservations.edit',
                'reservations.cancel', 'reservations.confirm'
            ],
            'Housekeeping Staff' => [
                'dashboard.view', 'rooms.view', 'housekeeping.view', 'housekeeping.update'
            ],
            'Housekeeping Supervisor' => [
                'dashboard.view', 'rooms.view', 'rooms.manage_status', 'housekeeping.view',
                'housekeeping.assign', 'housekeeping.update', 'housekeeping.inspect'
            ],
            'Accountant / Finance Officer' => [
                'dashboard.view', 'folios.view', 'invoices.view', 'invoices.create', 'invoices.void',
                'payments.view', 'payments.create', 'payments.refund', 'expenses.view',
                'expenses.create', 'expenses.approve', 'reports.view'
            ],
            'Restaurant Cashier' => [
                'dashboard.view', 'restaurant.view', 'restaurant.order_create',
                'restaurant.order_cancel', 'restaurant.room_charge', 'guests.view', 'stays.view'
            ],
            'Inventory Officer' => [
                'dashboard.view', 'inventory.view', 'inventory.create_product',
                'inventory.purchase', 'inventory.adjust'
            ],
            'Maintenance Staff' => [
                'dashboard.view', 'rooms.view', 'maintenance.view', 'maintenance.create',
                'maintenance.update'
            ],
        ];

        $roleModels = [];
        foreach ($rolesData as $roleName => $perms) {
            $role = Role::create(['name' => $roleName, 'description' => "$roleName operational role"]);
            $roleModels[$roleName] = $role;
            $permIds = array_map(fn($p) => $permissionModels[$p]->id, $perms);
            $role->permissions()->sync($permIds);
        }

        // 5. Employees & Demo Users
        $demoUsers = [
            [
                'email' => 'admin@example.test',
                'name' => 'Alexander Vance',
                'role' => 'Super Administrator',
                'emp_no' => 'EMP-001',
                'dept' => 'Administration',
                'position' => 'General Manager',
            ],
            [
                'email' => 'manager@example.test',
                'name' => 'Sophia Otieno',
                'role' => 'Hotel Manager',
                'emp_no' => 'EMP-002',
                'dept' => 'Administration',
                'position' => 'Operations Manager',
            ],
            [
                'email' => 'reception@example.test',
                'name' => 'David Wanjiku',
                'role' => 'Front Desk / Receptionist',
                'emp_no' => 'EMP-003',
                'dept' => 'Front Desk',
                'position' => 'Head Receptionist',
            ],
            [
                'email' => 'housekeeping@example.test',
                'name' => 'Grace Mwangi',
                'role' => 'Housekeeping Supervisor',
                'emp_no' => 'EMP-004',
                'dept' => 'Housekeeping',
                'position' => 'Housekeeping Supervisor',
            ],
            [
                'email' => 'accountant@example.test',
                'name' => 'Michael Kamau',
                'role' => 'Accountant / Finance Officer',
                'emp_no' => 'EMP-005',
                'dept' => 'Finance',
                'position' => 'Financial Controller',
            ],
            [
                'email' => 'restaurant@example.test',
                'name' => 'Brian Ochieng',
                'role' => 'Restaurant Cashier',
                'emp_no' => 'EMP-006',
                'dept' => 'Food & Beverage',
                'position' => 'POS Head Cashier',
            ],
            [
                'email' => 'inventory@example.test',
                'name' => 'Esther Njeri',
                'role' => 'Inventory Officer',
                'emp_no' => 'EMP-007',
                'dept' => 'Inventory',
                'position' => 'Store Keeper',
            ],
            [
                'email' => 'maintenance@example.test',
                'name' => 'Joseph Kipchumba',
                'role' => 'Maintenance Staff',
                'emp_no' => 'EMP-008',
                'dept' => 'Maintenance',
                'position' => 'Senior Technician',
            ],
        ];

        $userModels = [];
        foreach ($demoUsers as $du) {
            $nameParts = explode(' ', $du['name']);
            $employee = Employee::create([
                'branch_id' => $branch->id,
                'employee_number' => $du['emp_no'],
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $du['email'],
                'phone' => '+254 711 ' . rand(100000, 999999),
                'department_id' => $departmentModels[$du['dept']]->id,
                'position' => $du['position'],
                'hire_date' => now()->subMonths(12),
                'employment_status' => 'active',
            ]);

            $user = User::create([
                'branch_id' => $branch->id,
                'employee_id' => $employee->id,
                'name' => $du['name'],
                'email' => $du['email'],
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);

            $user->roles()->attach($roleModels[$du['role']]->id);
            $userModels[$du['email']] = $user;
        }

        // 6. Floors
        $floor1 = Floor::create(['branch_id' => $branch->id, 'floor_number' => 1, 'name' => 'Ground Floor', 'description' => 'Lobby level & Deluxe rooms']);
        $floor2 = Floor::create(['branch_id' => $branch->id, 'floor_number' => 2, 'name' => 'Second Floor', 'description' => 'Executive suites & Standard rooms']);
        $floor3 = Floor::create(['branch_id' => $branch->id, 'floor_number' => 3, 'name' => 'Penthouse Floor', 'description' => 'Presidential suites']);

        // 7. Amenities
        $amenities = [
            'High-Speed Wi-Fi' => 'Complimentary 100Mbps Wi-Fi',
            'Mini Bar' => 'Fully stocked refreshment center',
            'Air Conditioning' => 'Climate-controlled thermostat',
            'Smart TV' => '55" Ultra HD Smart TV with Netflix',
            'Ocean/City View' => 'Panoramic balcony view',
            'Jacuzzi Tub' => 'Private whirlpool hydrotherapy tub',
            'Digital Safe' => 'Laptop-compatible in-room safe',
        ];
        $amenityModels = [];
        foreach ($amenities as $aName => $aDesc) {
            $amenityModels[$aName] = Amenity::create(['name' => $aName, 'description' => $aDesc]);
        }

        // 8. Room Types
        $rtStandard = RoomType::create([
            'branch_id' => $branch->id,
            'name' => 'Standard Double',
            'description' => 'Comfortable room with plush Queen bed & modern workspace.',
            'base_rate' => 8500.00,
            'max_adults' => 2,
            'max_children' => 1,
            'bed_type' => 'Queen',
            'bed_count' => 1,
            'status' => 'active',
        ]);
        $rtStandard->amenities()->attach([$amenityModels['High-Speed Wi-Fi']->id, $amenityModels['Air Conditioning']->id, $amenityModels['Digital Safe']->id]);

        $rtDeluxe = RoomType::create([
            'branch_id' => $branch->id,
            'name' => 'Deluxe Ocean View',
            'description' => 'Spacious room with King bed and breathtaking balcony view.',
            'base_rate' => 14000.00,
            'max_adults' => 2,
            'max_children' => 2,
            'bed_type' => 'King',
            'bed_count' => 1,
            'status' => 'active',
        ]);
        $rtDeluxe->amenities()->attach([$amenityModels['High-Speed Wi-Fi']->id, $amenityModels['Mini Bar']->id, $amenityModels['Air Conditioning']->id, $amenityModels['Smart TV']->id, $amenityModels['Ocean/City View']->id, $amenityModels['Digital Safe']->id]);

        $rtExecutive = RoomType::create([
            'branch_id' => $branch->id,
            'name' => 'Executive Suite',
            'description' => 'Luxury suite featuring separate living area and executive privileges.',
            'base_rate' => 25000.00,
            'max_adults' => 3,
            'max_children' => 2,
            'bed_type' => 'King + Sofa Bed',
            'bed_count' => 2,
            'status' => 'active',
        ]);
        $rtExecutive->amenities()->attach(array_map(fn($a) => $a->id, $amenityModels));

        $rtPresidential = RoomType::create([
            'branch_id' => $branch->id,
            'name' => 'Presidential Suite',
            'description' => 'Top floor 200sqm master suite with private jacuzzi and butler service.',
            'base_rate' => 60000.00,
            'max_adults' => 4,
            'max_children' => 2,
            'bed_type' => 'Super King',
            'bed_count' => 2,
            'status' => 'active',
        ]);
        $rtPresidential->amenities()->attach(array_map(fn($a) => $a->id, $amenityModels));

        // 9. Rooms
        $roomsData = [
            ['floor' => $floor1->id, 'type' => $rtStandard->id, 'no' => '101', 'op' => 'AVAILABLE', 'hk' => 'CLEAN'],
            ['floor' => $floor1->id, 'type' => $rtStandard->id, 'no' => '102', 'op' => 'OCCUPIED', 'hk' => 'CLEAN'],
            ['floor' => $floor1->id, 'type' => $rtDeluxe->id, 'no' => '103', 'op' => 'AVAILABLE', 'hk' => 'INSPECTED'],
            ['floor' => $floor1->id, 'type' => $rtDeluxe->id, 'no' => '104', 'op' => 'RESERVED', 'hk' => 'CLEAN'],
            ['floor' => $floor2->id, 'type' => $rtDeluxe->id, 'no' => '201', 'op' => 'OCCUPIED', 'hk' => 'DIRTY'],
            ['floor' => $floor2->id, 'type' => $rtExecutive->id, 'no' => '202', 'op' => 'AVAILABLE', 'hk' => 'INSPECTED'],
            ['floor' => $floor2->id, 'type' => $rtExecutive->id, 'no' => '203', 'op' => 'OUT_OF_ORDER', 'hk' => 'DIRTY', 'maint' => 'IN_MAINTENANCE'],
            ['floor' => $floor2->id, 'type' => $rtStandard->id, 'no' => '204', 'op' => 'AVAILABLE', 'hk' => 'CLEANING'],
            ['floor' => $floor3->id, 'type' => $rtPresidential->id, 'no' => '301', 'op' => 'OCCUPIED', 'hk' => 'CLEAN'],
            ['floor' => $floor3->id, 'type' => $rtPresidential->id, 'no' => '302', 'op' => 'AVAILABLE', 'hk' => 'INSPECTED'],
        ];

        $roomModels = [];
        foreach ($roomsData as $r) {
            $room = Room::create([
                'branch_id' => $branch->id,
                'floor_id' => $r['floor'],
                'room_type_id' => $r['type'],
                'room_number' => $r['no'],
                'description' => "Room {$r['no']}",
                'operational_status' => $r['op'],
                'housekeeping_status' => $r['hk'],
                'maintenance_status' => $r['maint'] ?? 'NONE',
                'is_active' => true,
            ]);
            $roomModels[$r['no']] = $room;
        }

        // 10. Booking Sources
        $bsDirect = BookingSource::create(['branch_id' => $branch->id, 'name' => 'Direct Website', 'type' => 'DIRECT', 'commission_rate' => 0.00]);
        $bsWalkIn = BookingSource::create(['branch_id' => $branch->id, 'name' => 'Walk-In Desk', 'type' => 'DIRECT', 'commission_rate' => 0.00]);
        $bsBooking = BookingSource::create(['branch_id' => $branch->id, 'name' => 'Booking.com', 'type' => 'OTA', 'commission_rate' => 15.00]);
        $bsExpedia = BookingSource::create(['branch_id' => $branch->id, 'name' => 'Expedia', 'type' => 'OTA', 'commission_rate' => 18.00]);

        // 11. Payment Methods
        $pmCash = PaymentMethod::create(['branch_id' => $branch->id, 'name' => 'Cash', 'code' => 'CASH']);
        $pmCard = PaymentMethod::create(['branch_id' => $branch->id, 'name' => 'Credit / Debit Card', 'code' => 'CARD']);
        $pmMpesa = PaymentMethod::create(['branch_id' => $branch->id, 'name' => 'M-Pesa Mobile Money', 'code' => 'MPESA']);
        $pmBank = PaymentMethod::create(['branch_id' => $branch->id, 'name' => 'Bank Wire Transfer', 'code' => 'BANK']);

        // 12. Guests
        $guest1 = Guest::create([
            'branch_id' => $branch->id,
            'guest_number' => 'GST-1001',
            'first_name' => 'James',
            'last_name' => 'Kariuki',
            'gender' => 'Male',
            'date_of_birth' => '1988-05-14',
            'nationality' => 'Kenyan',
            'id_type' => 'National ID',
            'id_number' => '28491029',
            'phone' => '+254 722 998 877',
            'email' => 'james.kariuki@example.com',
            'city' => 'Nairobi',
            'country' => 'Kenya',
        ]);

        $guest2 = Guest::create([
            'branch_id' => $branch->id,
            'guest_number' => 'GST-1002',
            'first_name' => 'Sarah',
            'last_name' => 'Jenkins',
            'gender' => 'Female',
            'date_of_birth' => '1992-11-20',
            'nationality' => 'British',
            'id_type' => 'Passport',
            'id_number' => 'GB90412891',
            'phone' => '+44 7911 123456',
            'email' => 'sarah.j@example.co.uk',
            'city' => 'London',
            'country' => 'United Kingdom',
        ]);

        $guest3 = Guest::create([
            'branch_id' => $guest1->branch_id,
            'guest_number' => 'GST-1003',
            'first_name' => 'Emmanuel',
            'last_name' => 'Nkurunziza',
            'gender' => 'Male',
            'date_of_birth' => '1985-03-10',
            'nationality' => 'Rwandan',
            'id_type' => 'Passport',
            'id_number' => 'RW4910283',
            'phone' => '+250 788 123 456',
            'email' => 'e.nkurunziza@example.com',
            'city' => 'Kigali',
            'country' => 'Rwanda',
        ]);

        // 13. Active Stay & Folio (Guest 1 in Room 102)
        $res1 = Reservation::create([
            'branch_id' => $branch->id,
            'reservation_number' => 'RES-2026-001',
            'guest_id' => $guest1->id,
            'room_type_id' => $rtStandard->id,
            'room_id' => $roomModels['102']->id,
            'booking_source_id' => $bsDirect->id,
            'check_in_date' => now()->subDays(2),
            'check_out_date' => now()->addDays(2),
            'adults' => 2,
            'children' => 0,
            'base_rate' => 8500.00,
            'total_amount' => 34000.00,
            'deposit_amount' => 17000.00,
            'status' => 'CHECKED_IN',
            'created_by' => $userModels['reception@example.test']->id,
        ]);

        $stay1 = Stay::create([
            'branch_id' => $branch->id,
            'reservation_id' => $res1->id,
            'guest_id' => $guest1->id,
            'room_id' => $roomModels['102']->id,
            'actual_check_in' => now()->subDays(2),
            'expected_check_out' => now()->addDays(2),
            'status' => 'ACTIVE',
            'checked_in_by' => $userModels['reception@example.test']->id,
        ]);

        $folio1 = Folio::create([
            'branch_id' => $branch->id,
            'stay_id' => $stay1->id,
            'guest_id' => $guest1->id,
            'folio_number' => 'FOL-102-001',
            'status' => 'OPEN',
            'opened_at' => now()->subDays(2),
        ]);

        FolioItem::create([
            'folio_id' => $folio1->id,
            'item_type' => 'ROOM',
            'description' => 'Room 102 Nightly Rate (2 Nights)',
            'quantity' => 2,
            'unit_price' => 8500.00,
            'total_amount' => 17000.00,
        ]);

        FolioItem::create([
            'folio_id' => $folio1->id,
            'item_type' => 'RESTAURANT',
            'description' => 'Savannah Restaurant Dinner Service',
            'quantity' => 1,
            'unit_price' => 3200.00,
            'total_amount' => 3200.00,
        ]);

        $invoice1 = Invoice::create([
            'branch_id' => $branch->id,
            'folio_id' => $folio1->id,
            'guest_id' => $guest1->id,
            'invoice_number' => 'INV-2026-0001',
            'subtotal' => 20200.00,
            'discount_amount' => 0.00,
            'tax_amount' => 3232.00,
            'service_charge' => 1010.00,
            'grand_total' => 24442.00,
            'amount_paid' => 17000.00,
            'balance_due' => 7442.00,
            'status' => 'PARTIALLY_PAID',
            'issued_at' => now()->subDays(2),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'description' => 'Room 102 Charge (2 nights)',
            'quantity' => 2,
            'unit_price' => 8500.00,
            'total_amount' => 17000.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'description' => 'Dinner Service',
            'quantity' => 1,
            'unit_price' => 3200.00,
            'total_amount' => 3200.00,
        ]);

        Payment::create([
            'branch_id' => $branch->id,
            'invoice_id' => $invoice1->id,
            'guest_id' => $guest1->id,
            'payment_method_id' => $pmMpesa->id,
            'amount' => 17000.00,
            'reference_number' => 'QHK9041289',
            'transaction_date' => now()->subDays(2),
            'received_by' => $userModels['reception@example.test']->id,
            'status' => 'COMPLETED',
        ]);

        // 14. Menu & Restaurant
        $mc1 = MenuCategory::create(['branch_id' => $branch->id, 'name' => 'Main Courses', 'description' => 'Gourmet dinner mains']);
        $mc2 = MenuCategory::create(['branch_id' => $branch->id, 'name' => 'Beverages', 'description' => 'Wines, cocktails & fresh juices']);

        $mi1 = MenuItem::create(['branch_id' => $branch->id, 'category_id' => $mc1->id, 'name' => 'Prime Ribeye Steak 300g', 'description' => 'Grilled aged beef ribeye with truffle fries', 'price' => 2800.00, 'tax_rate' => 16.00]);
        $mi2 = MenuItem::create(['branch_id' => $branch->id, 'category_id' => $mc1->id, 'name' => 'Pan-Seared Swahili Tilapia', 'description' => 'Fresh lake tilapia in coconut tamarind sauce', 'price' => 1950.00, 'tax_rate' => 16.00]);
        $mi3 = MenuItem::create(['branch_id' => $branch->id, 'category_id' => $mc2->id, 'name' => 'Passion Fruit Mint Mocktail', 'description' => 'Fresh tropical fruit blend', 'price' => 650.00, 'tax_rate' => 16.00]);

        $ro1 = RestaurantOrder::create([
            'branch_id' => $branch->id,
            'order_number' => 'ORD-2026-001',
            'guest_id' => $guest1->id,
            'stay_id' => $stay1->id,
            'room_id' => $roomModels['102']->id,
            'table_number' => 'T-04',
            'order_type' => 'ROOM_SERVICE',
            'subtotal' => 3450.00,
            'tax_amount' => 552.00,
            'total_amount' => 4002.00,
            'status' => 'ROOM_CHARGED',
            'created_by' => $userModels['restaurant@example.test']->id,
        ]);

        RestaurantOrderItem::create([
            'order_id' => $ro1->id,
            'menu_item_id' => $mi1->id,
            'quantity' => 1,
            'unit_price' => 2800.00,
            'total_amount' => 2800.00,
        ]);
        RestaurantOrderItem::create([
            'order_id' => $ro1->id,
            'menu_item_id' => $mi3->id,
            'quantity' => 1,
            'unit_price' => 650.00,
            'total_amount' => 650.00,
        ]);

        // 15. Housekeeping Task & Inspection
        HousekeepingTask::create([
            'branch_id' => $branch->id,
            'room_id' => $roomModels['201']->id,
            'assigned_to' => $userModels['housekeeping@example.test']->id,
            'task_type' => 'CLEANING',
            'priority' => 'HIGH',
            'status' => 'PENDING',
            'notes' => 'Checkout cleaning required for Room 201',
        ]);

        // 16. Maintenance Ticket
        $mcAC = MaintenanceCategory::create(['name' => 'HVAC & Air Conditioning', 'description' => 'Cooling and ventilation systems']);
        MaintenanceTicket::create([
            'branch_id' => $branch->id,
            'room_id' => $roomModels['203']->id,
            'category_id' => $mcAC->id,
            'reported_by' => $userModels['reception@example.test']->id,
            'assigned_to' => $userModels['maintenance@example.test']->id,
            'title' => 'AC Compressor Noise in Room 203',
            'description' => 'Guest reported loud rattling noise from split unit compressor.',
            'priority' => 'HIGH',
            'status' => 'IN_PROGRESS',
            'estimated_cost' => 12500.00,
            'reported_at' => now()->subDay(),
        ]);

        // 17. Inventory & Stock
        $icLinens = InventoryCategory::create(['branch_id' => $branch->id, 'name' => 'Bed Linens & Towels', 'description' => 'Hotel textiles']);
        $pBathTowel = Product::create([
            'branch_id' => $branch->id,
            'category_id' => $icLinens->id,
            'sku' => 'TWL-BATH-800G',
            'name' => 'Luxury Cotton Bath Towel 800g',
            'unit' => 'pcs',
            'cost_price' => 1400.00,
            'selling_price' => 0.00,
            'reorder_level' => 25,
            'current_stock' => 120,
            'status' => 'active',
        ]);

        StockMovement::create([
            'branch_id' => $branch->id,
            'product_id' => $pBathTowel->id,
            'movement_type' => 'PURCHASE',
            'quantity' => 120,
            'unit_cost' => 1400.00,
            'balance_after' => 120,
            'created_by' => $userModels['inventory@example.test']->id,
            'notes' => 'Initial inventory delivery',
        ]);

        // 18. Expenses
        $ecUtils = ExpenseCategory::create(['branch_id' => $branch->id, 'name' => 'Utilities & Power', 'description' => 'Electricity, water & internet']);
        Expense::create([
            'branch_id' => $branch->id,
            'category_id' => $ecUtils->id,
            'amount' => 145000.00,
            'expense_date' => now()->subDays(5),
            'payment_method_id' => $pmBank->id,
            'description' => 'Kenya Power Electricity Bill - August 2026',
            'receipt_number' => 'KPLC-890412',
            'status' => 'APPROVED',
            'created_by' => $userModels['accountant@example.test']->id,
            'approved_by' => $userModels['manager@example.test']->id,
        ]);
    }
}
