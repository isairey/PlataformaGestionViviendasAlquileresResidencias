<?php

namespace App\Console\Commands;

use App\Enums\BillStatus;
use App\Enums\PropertyType;
use App\Enums\TransactionStatus;
use App\Models\Bill;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

/**
 * Scenario 1: New tenant with 1 unpaid bill
 * Scenario 2: Tenant stayed 3 months, all previous bills paid, 1 unpaid for next month
 * Scenario 3: Tenant stayed 1 month, current bill will be overdue
 * Scenario 4: Tenant stayed 5 months, last month is overdue + current month overdue
 */
class DemoJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create demo tenants for showcasing billing jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('🏠 Demo Job Command');
        note('This command creates demo tenants and bills to showcase your billing job functionality.');

        $hasExistingData = $this->checkExistingDemoData();

        if ($hasExistingData) {
            warning('Demo data already exists!');

            $action = select(
                'What would you like to do?',
                [
                    'status' => '📊 Show current demo status',
                    'clean' => '🧹 Clean existing demo data',
                    'add' => '➕ Add more scenarios',
                    'exit' => '❌ Exit',
                ],
                default: 'status'
            );

            match ($action) {
                'status' => $this->showStatus(),
                'clean' => $this->handleCleanData(),
                'add' => $this->handleCreateScenarios(),
                'exit' => outro('👋 Goodbye!')
            };
        } else {
            $this->handleCreateScenarios();
        }
    }

    private function checkExistingDemoData(): bool
    {
        return Tenant::whereHas('user', function ($query) {
            $query->where('email', 'like', 'tenant.s%@demo.com');
        })->exists();
    }

    private function handleCleanData(): void
    {
        $confirmed = confirm('Are you sure you want to clean all demo data?', false);

        if (! $confirmed) {
            info('Operation cancelled.');

            return;
        }

        $this->cleanDemoData();
        info('✅ Demo data cleaned successfully!');

        $createNew = confirm('Would you like to create new demo scenarios?', true);
        if ($createNew) {
            $this->handleCreateScenarios();
        } else {
            outro('Demo data cleaned. Run the command again to create new scenarios.');
        }
    }

    private function handleCreateScenarios(): void
    {
        $scenario = select(
            'Which demo scenario would you like to create?',
            [
                '1' => '🆕 Scenario 1: New tenant with 1 unpaid bill',
                '2' => '💰 Scenario 2: 3-month tenant with all bills paid except current',
                '3' => '⚠️ Scenario 3: 1-month tenant with overdue bill',
                '4' => '🚨 Scenario 4: 5-month tenant with multiple overdue bills',
                'all' => '🎯 Create all scenarios',
            ],
            default: 'all'
        );

        // Get or create landlord and property
        info('Setting up demo environment...');
        $landlord = $this->getOrCreateLandlord();
        $property = $this->getOrCreateProperty($landlord);

        // Create scenarios
        match ((string) $scenario) {
            '1' => $this->createScenario1($property),
            '2' => $this->createScenario2($property),
            '3' => $this->createScenario3($property),
            '4' => $this->createScenario4($property),
            'all' => $this->createAllScenarios($property),
        };

        info('🎉 Demo scenarios created successfully!');
        $this->showStatus();

        outro('Demo setup complete! You can now test your billing jobs.');
    }

    private function createAllScenarios(Property $property): void
    {
        info('Creating all demo scenarios...');

        $this->createScenario1($property);
        $this->createScenario2($property);
        $this->createScenario3($property);
        $this->createScenario4($property);
    }

    private function getOrCreateLandlord(): Landlord
    {
        $user = User::firstOrCreate(
            ['email' => env('DEFAULT_LANDLORD_EMAIL', 'landlord@gmail.com')],
            [
                'name' => 'Default Landlord',
                // 'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'profile_completed' => true,
            ]
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return Landlord::firstOrCreate(['user_id' => $user->id]);
    }

    private function getOrCreateProperty(Landlord $landlord): Property
    {
        return Property::firstOrCreate([
            'landlord_id' => $landlord->id,
            'name' => 'Demo Property',
        ], [
            'type' => PropertyType::DORM->value,
            'description' => 'Property for demo purposes',
            'address' => '123 Demo Street, Demo City',
        ]);
    }

    private function cleanDemoData(): void
    {
        // Delete demo tenants and related data
        $demoUsers = User::where('email', 'like', '%.demo.com')
            ->orWhere('email', 'like', 'tenant.s%@demo.com')
            ->get();

        foreach ($demoUsers as $user) {
            if ($user->tenant) {
                // Delete related transactions
                Transaction::where('tenant_id', $user->tenant->id)->delete();
                // Delete related bills
                Bill::where('tenant_id', $user->tenant->id)->delete();
                // Delete tenant
                $user->tenant->delete();
            }
            $user->delete();
        }

        // Delete demo rooms
        Room::where('name', 'like', 'Demo Room%')->delete();

        // Delete demo property if no other rooms
        $demoProperty = Property::where('name', 'Demo Property')->first();
        if ($demoProperty && $demoProperty->rooms()->count() === 0) {
            $demoProperty->delete();
        }
    }

    /**
     * Scenario 1: New tenant with 1 unpaid bill
     */
    private function createScenario1(Property $property): void
    {
        info('🆕 Creating Scenario 1: New tenant with 1 unpaid bill');

        // Create room
        $room = Room::create([
            'property_id' => $property->id,
            'code' => generate_code(),
            'name' => 'Demo Room S1',
            'rent_amount' => 5000.00,
            'max_occupancy' => 2,
        ]);

        // Create tenant that moved in today (billing day is today)
        $tenant = $this->createTenant('Demo Tenant S1', 'tenant.s1@demo.com', $room, now());

        note("✅ Scenario 1 created - Room: {$room->code}, Move-in: ".now()->format('Y-m-d'));
    }

    /**
     * Scenario 2: Tenant stayed 3 months, all previous bills paid, 1 unpaid for next month
     */
    private function createScenario2(Property $property): void
    {
        info('💰 Creating Scenario 2: 3-month tenant with all bills paid except current');

        // Create room
        $room = Room::create([
            'property_id' => $property->id,
            'code' => generate_code(),
            'name' => 'Demo Room S2',
            'rent_amount' => 6000.00,
            'max_occupancy' => 2,
        ]);

        // Create tenant that moved in 3 months ago
        $moveInDate = now()->subMonths(3);
        $tenant = $this->createTenant('Demo Tenant S2', 'tenant.s2@demo.com', $room, $moveInDate);

        // Create and pay bills for the past 3 months
        for ($i = 0; $i < 3; $i++) {
            $dueDate = $moveInDate->copy()->addMonths($i + 1);
            $createdAt = ($i === 0) ? $moveInDate->copy() : $moveInDate->copy()->addMonths($i);

            $bill = Bill::create([
                'tenant_id' => $tenant->id,
                'amount_due' => $tenant->room->rent_amount,
                'due_date' => $dueDate->endOfDay(),
                'status' => BillStatus::PAID->value,
                'created_at' => $createdAt->startOfDay(),
                'updated_at' => $createdAt->startOfDay(),
            ]);

            // Create payment transaction
            $paymentDate = fake()->dateTimeBetween(
                $createdAt->format('Y-m-d'),
                $dueDate->format('Y-m-d')
            );

            Transaction::create([
                'tenant_id' => $tenant->id,
                'bill_id' => $bill->id,
                'payment_method' => 'gcash',
                'proof_photo' => null,
                'payment_date' => $paymentDate,
                'status' => TransactionStatus::COMPLETED->value,
                'confirmed_at' => $paymentDate,
            ]);
        }

        note("✅ Scenario 2 created - Room: {$room->code}, Move-in: ".$moveInDate->format('Y-m-d'));
    }

    /**
     * Scenario 3: Tenant stayed 1 month, current bill will be overdue
     */
    private function createScenario3(Property $property): void
    {
        info('⚠️  Creating Scenario 3: 1-month tenant with overdue bill');

        // Create room
        $room = Room::create([
            'property_id' => $property->id,
            'code' => generate_code(),
            'name' => 'Demo Room S3',
            'rent_amount' => 4500.00,
            'max_occupancy' => 1,
        ]);

        // Create tenant that moved in 1 month and 1 day ago
        $moveInDate = now()->subMonths(1)->subDay();
        $tenant = $this->createTenant('Demo Tenant S3', 'tenant.s3@demo.com', $room, $moveInDate);

        $bill = Bill::create([
            'tenant_id' => $tenant->id,
            'amount_due' => $tenant->room->rent_amount,
            'due_date' => $moveInDate->copy()->addMonth()->endOfDay(), // Past due date
            'status' => BillStatus::UNPAID->value,
            'created_at' => $moveInDate->startOfDay(),
            'updated_at' => $moveInDate->startOfDay(),
        ]);

        note("✅ Scenario 3 created - Room: {$room->code}, Move-in: ".$moveInDate->format('Y-m-d'));
    }

    /**
     * Scenario 4: Tenant stayed 5 months, last month is overdue + current month overdue
     */
    private function createScenario4(Property $property): void
    {
        info('🚨 Creating Scenario 4: 5-month tenant with multiple overdue bills');

        // Create room
        $room = Room::create([
            'property_id' => $property->id,
            'code' => generate_code(),
            'name' => 'Demo Room S4',
            'rent_amount' => 7000.00,
            'max_occupancy' => 3,
        ]);

        // Create tenant that moved in 5 months and 1 day ago
        $moveInDate = now()->subMonths(5)->subDay();
        $tenant = $this->createTenant('Demo Tenant S4', 'tenant.s4@demo.com', $room, $moveInDate);

        // Let GenerateBillsJob create the 5th month bill
        for ($i = 0; $i < 5; $i++) {
            $dueDate = $moveInDate->copy()->addMonths($i + 1)->endOfDay();
            $createdAt = ($i === 0) ? $moveInDate->copy() : $moveInDate->copy()->addMonths($i);

            // First 3 months: paid bills
            if ($i < 3) {
                $bill = Bill::create([
                    'tenant_id' => $tenant->id,
                    'amount_due' => $tenant->room->rent_amount,
                    'due_date' => $dueDate,
                    'status' => BillStatus::PAID->value,
                    'created_at' => $createdAt->startOfDay(),
                    'updated_at' => $createdAt->startOfDay(),
                ]);

                // Create payment transaction
                $paymentDate = fake()->dateTimeBetween(
                    $createdAt->format('Y-m-d'),
                    $dueDate->format('Y-m-d')
                );

                Transaction::create([
                    'tenant_id' => $tenant->id,
                    'bill_id' => $bill->id,
                    'payment_method' => 'cash',
                    'proof_photo' => null,
                    'payment_date' => $paymentDate,
                    'status' => TransactionStatus::COMPLETED->value,
                    'confirmed_at' => $paymentDate,
                ]);
            } else {

                Bill::create([
                    'tenant_id' => $tenant->id,
                    'amount_due' => $tenant->room->rent_amount,
                    'due_date' => $dueDate, // This will be past due
                    'status' => BillStatus::UNPAID->value,
                    'created_at' => $createdAt->startOfDay(),
                    'updated_at' => $createdAt->startOfDay(),
                ]);
            }
        }

        note("✅ Scenario 4 created - Room: {$room->code}, Move-in: ".$moveInDate->format('Y-m-d'));
    }

    private function createTenant(string $name, string $email, Room $room, Carbon $moveInDate): Tenant
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            // 'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'profile_completed' => true,
        ]);

        $user->markEmailAsVerified();

        return Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'move_in_date' => $moveInDate,
        ]);
    }

    private function showStatus(): void
    {
        $demoTenants = Tenant::whereHas('user', function ($query) {
            $query->where('email', 'like', 'tenant.s%@demo.com');
        })->with(['user', 'room', 'bills'])->get();

        if ($demoTenants->isEmpty()) {
            warning('No demo tenants found.');

            return;
        }

        info('📊 Demo Status Overview');

        // Create summary table
        $summaryData = [];
        $totalBills = 0;
        $unpaidBills = 0;
        $overdueBills = 0;
        $paidBills = 0;

        foreach ($demoTenants as $tenant) {
            $bills = $tenant->bills;
            $unpaid = $bills->where('status', 'unpaid')->count();
            $overdue = $bills->where('status', 'overdue')->count();
            $paid = $bills->where('status', 'paid')->count();

            $totalBills += $bills->count();
            $unpaidBills += $unpaid;
            $overdueBills += $overdue;
            $paidBills += $paid;

            $summaryData[] = [
                'Tenant' => $tenant->user->name,
                'Room' => $tenant->room->code,
                'Rent' => '₱'.number_format($tenant->room->rent_amount, 2),
                'Move-in' => $tenant->move_in_date->format('M j, Y'),
                'Total Bills' => $bills->count(),
                'Paid' => $paid > 0 ? "✅ {$paid}" : '-',
                'Unpaid' => $unpaid > 0 ? "⏳ {$unpaid}" : '-',
                'Overdue' => $overdue > 0 ? "❌ {$overdue}" : '-',
            ];
        }

        table(
            ['Tenant', 'Room', 'Rent', 'Move-in', 'Total Bills', 'Paid', 'Unpaid', 'Overdue'],
            $summaryData
        );

        // Show overall summary
        info('📈 Overall Summary');
        table(
            ['Metric', 'Count'],
            [
                ['Total Demo Tenants', $demoTenants->count()],
                ['Total Bills', $totalBills],
                ['Paid Bills', "✅ {$paidBills}"],
                ['Unpaid Bills', "⏳ {$unpaidBills}"],
                ['Overdue Bills', "❌ {$overdueBills}"],
            ]
        );
    }
}
