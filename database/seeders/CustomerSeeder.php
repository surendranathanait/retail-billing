<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Thomas D. Call', 'email' => 'thomas.d.call@example.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@example.com'],
            ['name' => 'Michael Smith', 'email' => 'michael.smith@example.com'],
            ['name' => 'Emma Davis', 'email' => 'emma.davis@example.com'],
            ['name' => 'Robert Wilson', 'email' => 'robert.wilson@example.com'],
            ['name' => 'Jennifer Brown', 'email' => 'jennifer.brown@example.com'],
            ['name' => 'David Martinez', 'email' => 'david.martinez@example.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.anderson@example.com'],
            ['name' => 'James Taylor', 'email' => 'james.taylor@example.com'],
            ['name' => 'Maria Garcia', 'email' => 'maria.garcia@example.com'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
