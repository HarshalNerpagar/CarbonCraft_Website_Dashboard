<?php

namespace Database\Seeders;

use App\Models\UserRole;
use App\Models\UserRolePermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class OrderManagementRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Order Management Role
        $role = UserRole::updateOrCreate(
            ['name' => 'Order Management'],
            [
                'name' => 'Order Management',
                'description' => 'Manages complete order lifecycle: design creation, payment collection, status updates, and logistics coordination'
            ]
        );

        // Define permissions for Order Management role
        $permissions = [
            // Order Management - Full access except creating new orders
            'order_list',           // ✓ View all orders
            'order_view',           // ✓ View order details
            'order_edit',           // ✓ Edit order details
            'order_print',          // ✓ Print invoices
            'order_status_change',  // ✓ Change order status (main responsibility)
            // 'order_create' - ✗ Cannot create new orders
            // 'order_delete' - ✗ Cannot delete orders

            // Customer Management - Read and limited edit
            'customer_list',        // ✓ View customers
            'customer_view',        // ✓ View customer details
            'customer_edit',        // ✓ Update customer info (phone, address for delivery)
            // 'customer_create' - ✗ Cannot create customers manually
            // 'customer_delete' - ✗ Cannot delete customers

            // Payment Management - Collection focused
            'payment_list',         // ✓ View payment history
            'payment_view',         // ✓ View payment details
            'payment_create',       // ✓ Collect remaining payments
            // 'payment_edit' - ✗ Cannot edit payment records
            // 'payment_delete' - ✗ Cannot delete payments

            // Service/Product - Read only (to know what services exist)
            'service_list',         // ✓ View services
            'service_view',         // ✓ View service details
            // 'service_create' - ✗ Cannot create services
            // 'service_edit' - ✗ Cannot edit services
            // 'service_delete' - ✗ Cannot delete services

            // Reports - Limited access (order-related only)
            'report_order',         // ✓ View order reports
            'report_download',      // ✓ Download reports
            'report_print',         // ✓ Print reports
            // 'report_daily' - ✗ No financial reports
            // 'report_expense' - ✗ No expense reports
            // 'report_ledger' - ✗ No ledger reports
            // 'report_sales' - ✗ No sales reports
            // 'report_tax' - ✗ No tax reports

            // NO ACCESS TO:
            // - Expenses (full category denied)
            // - Settings (full category denied)
            // - Users/Roles management (full category denied)
            // - Addons management
            // - Service types management
            // - Translations
        ];

        // Get permissions with their IDs
        $permissionRecords = Permission::whereIn('name', $permissions)->get();

        // Clear existing permissions for this role
        UserRolePermission::where('role_id', $role->id)->delete();

        // Assign permissions to role
        foreach ($permissionRecords as $permission) {
            UserRolePermission::create([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
                'name' => $role->name,
                'permission_name' => $permission->name,
            ]);
        }

        $this->command->info('✓ Order Management role created with ' . count($permissionRecords) . ' permissions');
    }
}
