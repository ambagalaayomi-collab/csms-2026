<?php



use App\Models\User;
use App\Models\UnitRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['Client Demo', 'client@csms.test', 'client'],
            ['Manager Demo', 'manager@csms.test', 'project_manager'],
            ['Engineer Demo', 'engineer@csms.test', 'engineer'],
        ] as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name, 'role' => $role,
                'password' => bcrypt('Password123!'),
            ]);
        }

        foreach ([
            ['cement', 'Cement', 'bags', 2300], ['sand', 'Sand', 'cubes', 18000],
            ['steel', 'Steel', 'kg', 450], ['brick', 'Bricks', 'units', 75],
        ] as [$key, $name, $unit, $rate]) {
            UnitRate::updateOrCreate(['item_key' => $key], [
                'item_name' => $name, 'unit' => $unit, 'rate' => $rate,
            ]);
        }
    }
}
