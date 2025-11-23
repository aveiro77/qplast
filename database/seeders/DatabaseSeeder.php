<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();  // Initialize Faker
        // User::factory(10)->create();

        // Seed Departments table
        DB::table('departments')->insert([
            ['name' => 'HR', 'description' => 'Human Resources', 'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'IT', 'description' => 'Information Technology', 'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sales', 'description' => 'Sales and Marketing', 'status' => 'active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Seed Roles table
        DB::table('roles')->insert([
            ['title' => 'Manager', 'description' => 'Handles team management', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Developer', 'description' => 'Handles software development', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Salesperson', 'description' => 'Handles sales and client communication', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Cashier', 'description' => 'Handles sales on cashier', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
        
        // Seed Employees table
        DB::table('employees')->insert([
            [
                'fullname' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => $faker->dateTimeBetween('-40 years', '-20 years'),
                'hire_date' => Carbon::now(),
                'department_id' => 2,  // IT
                'role_id' => 2,        // Developer
                // 'supervisor_id' => null,
                'status' => 'active',
                'salary' => $faker->randomFloat(2, 3000, 6000),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            [
                'fullname' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => $faker->dateTimeBetween('-35 years', '-25 years'),
                'hire_date' => Carbon::now(),
                'department_id' => 3,  // Sales
                'role_id' => 4,        // Cashier
                // 'supervisor_id' => 1,  // John Doe (Manager)
                'status' => 'active',
                'salary' => $faker->randomFloat(2, 4000, 7000),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Kasir1',
            'email' => 'kasir@gmail.com',
            'password' => bcrypt('kasir'),
            'employee_id' => 2,
        ]);

        // Seed Customers table
        DB::table('customers')->insert([
            [
                'name' => 'Customer',
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'type' => 'Regular',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'type' => 'Regular',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'type' => 'Premium',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('categories')->insert([
            ['name' => 'Botol air zam-zam', 'description' => 'Botol dengan berbagai ukuran', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::table('products')->insert([
            [
                'name' => 'Botol Zam-Zam 600ml',
                'description' => 'Botol zam-zam ukuran 600ml',
                'stock' => 100,
                'category_id' => 1,
                'unit' => 'Ball',
                'hpp' => 325000,
                'hrg_ecer' => 0,
                'hrg_ball' => 335000,
                'hrg_grosir' => 330000,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Botol Zam-Zam 600ml',
                'description' => 'Botol zam-zam ukuran 600ml',
                'stock' => 100,
                'category_id' => 1,
                'unit' => 'Pcs',
                'hpp' => 2000,
                'hrg_ecer' => 2500,
                'hrg_ball' => 0,
                'hrg_grosir' => 0,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

    }
}
