<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Company; 
use App\Models\CompanyOwner;
use App\Models\Category;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear the cache of permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // User permissions
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        Permission::firstOrCreate(['name' => 'approve requests']);
        Permission::firstOrCreate(['name' => 'users.import']);
        Permission::firstOrCreate(['name' => 'users.export']);

        // Supplier permissions
        Permission::firstOrCreate(['name' => 'view suppliers']);
        Permission::firstOrCreate(['name' => 'create suppliers']);
        Permission::firstOrCreate(['name' => 'edit suppliers']);
        Permission::firstOrCreate(['name' => 'delete suppliers']);
        Permission::firstOrCreate(['name' => 'suppliers.import']);
        Permission::firstOrCreate(['name' => 'suppliers.export']);
        
        // Customer permissions
        Permission::firstOrCreate(['name' => 'view customers']);
        Permission::firstOrCreate(['name' => 'create customers']);
        Permission::firstOrCreate(['name' => 'edit customers']);
        Permission::firstOrCreate(['name' => 'delete customers']);
        Permission::firstOrCreate(['name' => 'customers.import']);
        Permission::firstOrCreate(['name' => 'customers.export']);

        // Inventory item permissions
        Permission::firstOrCreate(['name' => 'view inventory items']);
        Permission::firstOrCreate(['name' => 'create inventory items']);
        Permission::firstOrCreate(['name' => 'edit inventory items']);
        Permission::firstOrCreate(['name' => 'delete inventory items']);
        Permission::firstOrCreate(['name' => 'add new category']);
        Permission::firstOrCreate(['name' => 'inventory.import']);
        Permission::firstOrCreate(['name' => 'inventory.export']);

        // Purchase order permissions
        Permission::firstOrCreate(['name' => 'view purchase orders']);
        Permission::firstOrCreate(['name' => 'create purchase orders']);
        Permission::firstOrCreate(['name' => 'edit purchase orders']);
        Permission::firstOrCreate(['name' => 'delete purchase orders']);
        Permission::firstOrCreate(['name' => 'purchase_orders.export']);

        // Warehouse permissions
        Permission::firstOrCreate(['name' => 'view warehouses']);
        Permission::firstOrCreate(['name' => 'create warehouses']);
        Permission::firstOrCreate(['name' => 'edit warehouses']);
        Permission::firstOrCreate(['name' => 'delete warehouses']);
        Permission::firstOrCreate(['name' => 'warehouses.export']);

        // Register Arrival permissions
        Permission::firstOrCreate(['name' => 'view register arrivals']);
        Permission::firstOrCreate(['name' => 'create register arrivals']);
        Permission::firstOrCreate(['name' => 're-correct register arrivals']);

        // Activity log permissions
        Permission::firstOrCreate(['name' => 'view self activity logs']);
        Permission::firstOrCreate(['name' => 'view other users activity logs']);

        // Change daily for production data 
        Permission::firstOrCreate(['name' => 'select_previous_performance_dates']);
        Permission::firstOrCreate(['name' => 'select_next_operation_dates']);

        // view audit columns
        Permission::firstOrCreate(['name' => 'view audit columns']);

        // Customer advance invoices
        Permission::firstOrCreate(['name' => 'cus_adv_invoice.export']);
        Permission::firstOrCreate(['name' => 'create cus_adv_invoices']);

        // End of day reports
        Permission::firstOrCreate(['name' => 'create end of day reports']);
        Permission::firstOrCreate(['name' => 'end_of_day_report.export']);

        // Material QC reports
        Permission::firstOrCreate(['name' => 'create material qc records']);
        Permission::firstOrCreate(['name' => 'material qc.export']);

        // Non-Inventory items
        Permission::firstOrCreate(['name' => 'create non inventory items']);
        Permission::firstOrCreate(['name' => 'non inventory item.export']);

        // Purchase order invoices
        Permission::firstOrCreate(['name' => 'create purchase order invoices']);
        Permission::firstOrCreate(['name' => 'purchase_order_invoices.export']);
        Permission::firstOrCreate(['name' => 'pay purchase order invoice']);

        // Purchase order Advance invoices
        Permission::firstOrCreate(['name' => 'supplier advance invoices.export']);
        Permission::firstOrCreate(['name' => 'create supplier advance invoices']);
        Permission::firstOrCreate(['name' => 'pay supp adv invoice']);

        // Stocks
        Permission::firstOrCreate(['name' => 'create emergency stocks']);
        Permission::firstOrCreate(['name' => 'stocks.import']);
        Permission::firstOrCreate(['name' => 'stock.export']);


        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $superuser = Role::firstOrCreate(['name' => 'superuser']);  
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $qc = Role::firstOrCreate(['name' => 'Quality Control']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['view users', 'create users', 'edit users', 'approve requests']);
        $employee->givePermissionTo(['view users']);

        // Create specific position roles
        $positions = [
            'GM' => 'General Manager',
            'Finance Manager',
            'QC',
            'Technician',
            'Cutting Supervisor',
            'Sewing Line Supervisor'
        ];

        

        // Create a Superuser and assign role
        $superuser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'admin',
            'password' => bcrypt('12345678'), // Hash the password
        ]);

        // Create the main company
        $company = Company::firstOrCreate([
            'name' => 'Textile Manufacturing Co.',
            'address_line_1' => '123 Industrial Zone',
            'address_line_2' => 'Garment Street',
            'address_line_3' => '',
            'city' => 'Colombo',
            'postal_code' => '01000',
            'country' => 'Sri Lanka',
            'primary_phone' => '+94112345678',
            'secondary_phone' => '+94112345679',
            'email' => 'owner@textileco.com',
            'started_date' => '2010-01-15',
            'special_notes' => 'Leading textile manufacturer since 2010',
        ]);

        // Create the company owner
        CompanyOwner::firstOrCreate([
            'company_id' => $company->id,
            'name' => 'Mr. Rajapakse',
            'address_line_1' => '456 Owners Avenue',
            'address_line_2' => 'Highland Gardens',
            'address_line_3' => '',
            'city' => 'Colombo',
            'postal_code' => '01002',
            'country' => 'Sri Lanka',
            'phone_1' => '+94119876543',
            'phone_2' => '+94119876544',
            'email' => 'owner@textileco.com',
            'joined_date' => '2010-01-15',
        ]);
        
        // // Assign all permissions to the superuser role
        $superuser->assignRole('admin');  // Assigning the superuser role
    }
}
