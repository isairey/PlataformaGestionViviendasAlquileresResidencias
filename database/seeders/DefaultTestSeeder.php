<?php

namespace Database\Seeders;

use App\Enums\AnnouncementType;
use App\Enums\BillStatus;
use App\Enums\MaintenanceRequestStatus;
use App\Enums\PaymentMethod;
use App\Enums\PropertyType;
use App\Enums\TransactionStatus;
use App\Models\Announcement;
use App\Models\Bill;
use App\Models\Landlord;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Seeder;

class DefaultTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Landlord $landlord): void
    {
        $properties = $this->createTestProperties($landlord);
        $rooms = $this->createTestRooms($properties);
        $tenants = $this->createTestTenants($rooms);
        $this->createTestBillsAndTransactions($tenants);
        $this->createTestMaintenanceRequest($tenants);
        $this->createTestAnnouncements($properties, $rooms);
    }

    /**
     * @return Property[]
     */
    private function createTestProperties(Landlord $landlord)
    {
        $propertiesData = [
            [
                'type' => PropertyType::DORM->value,
                'name' => 'Dormitory Haven',
                'description' => 'Dormitory Haven offers a safe and comfortable living space designed specifically for students attending universities in Manila. The dorm features fully furnished rooms, high-speed internet, 24/7 security, and communal study areas to help residents focus on their academic goals. With a friendly atmosphere and proximity to shopping centers, food hubs, and public transportation, Dormitory Haven is your home away from home.',
                'address' => '1232 P. Noval Street, Sampaloc, Manila, Metro Manila, Philippines',
            ],
            [
                'type' => PropertyType::APARTMENT->value,
                'name' => 'Cityview Apartment',
                'description' => 'Cityview Apartment provides modern urban living in the heart of Cebu City. Each unit is designed for young professionals and families seeking convenience and comfort, with amenities including a swimming pool, fitness center, and 24-hour concierge. Residents enjoy easy access to business districts, schools, hospitals, and major shopping malls, making daily life stress-free and enjoyable.',
                'address' => '15 General Maxilom Avenue, Cebu City, Cebu, Philippines',
            ],
            [
                'type' => PropertyType::HOUSE->value,
                'name' => 'Sunny Family House',
                'description' => 'Sunny Family House is a spacious two-story home nestled in a peaceful subdivision, ideal for growing families who value both privacy and community. The house boasts four bedrooms, a large kitchen, a landscaped garden, and a secure two-car garage. Located near schools, parks, and local markets, it offers a perfect balance of suburban tranquility and urban accessibility.',
                'address' => '45 Mango Avenue, Green Meadows Subdivision, Quezon City, Metro Manila, Philippines',
            ],
            [
                'type' => PropertyType::CONDOMINIUM->value,
                'name' => 'Luxury Condo',
                'description' => 'Luxury Condo redefines upscale living with its breathtaking views of Manila Bay and world-class amenities. Enjoy exclusive access to infinity pools, a sky lounge, wellness spa, and a fully equipped gym. Each unit features elegant interiors with top-of-the-line appliances and smart home technology, perfect for executives and expatriates who desire both comfort and prestige.',
                'address' => '888 Roxas Boulevard, Pasay, Metro Manila, Philippines',
            ],
        ];

        $properties = [];

        foreach ($propertiesData as $data) {
            $property = Property::create([
                'landlord_id' => $landlord->id,
                'type' => $data['type'],
                'name' => $data['name'],
                'description' => $data['description'],
                'address' => $data['address'],
            ]);
            $properties[] = $property;
        }

        return $properties;
    }

    /**
     * @param  Property[]  $properties
     * @return Room[]
     */
    private function createTestRooms(array $properties)
    {
        $rooms = [];

        foreach ($properties as $property) {
            if ($property->type === PropertyType::HOUSE->value) {
                $rooms[] = Room::create([
                    'property_id' => $property->id,
                    'code' => generate_code(),
                    'name' => 'Main Room',
                    'rent_amount' => 15000.00,
                    'max_occupancy' => 10,
                ]);
            } else {
                $roomCount = rand(5, 10);
                for ($i = 1; $i <= $roomCount; $i++) {
                    $rooms[] = Room::create([
                        'property_id' => $property->id,
                        'code' => generate_code(),
                        'name' => 'Room ' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'rent_amount' => rand(8, 16) * 500, // 4000-8000
                        'max_occupancy' => rand(1, 6),
                    ]);
                }
            }
        }

        return $rooms;
    }

    /**
     * @param  Room[]  $rooms
     * @return Tenant[]
     */
    private function createTestTenants(array $rooms): array
    {
        $tenants = [];

        foreach ($rooms as $room) {
            $tenantCount = rand(0, $room->max_occupancy);

            for ($j = 1; $j <= $tenantCount; $j++) {
                $user = User::create([
                    'name' => fake()->name,
                    'email' => fake()->unique()->safeEmail,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => 'tenant',
                    'profile_completed' => true,
                ]);

                $tenant = Tenant::create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'move_in_date' => now()->subMonths(rand(3, 12))->day(rand(1, 28)),
                ]);

                $tenants[] = $tenant;
            }
        }

        return $tenants;
    }

    /**
     * @param  Tenant[]  $tenants
     */
    private function createTestBillsAndTransactions(array $tenants): void
    {
        $paymentMethods = [
            PaymentMethod::CASH->value,
            PaymentMethod::GCASH->value,
            PaymentMethod::MAYA->value,
        ];

        foreach ($tenants as $tenant) {
            $moveInDate = Carbon::parse($tenant->move_in_date);
            $originalDay = $moveInDate->day;
            $today = now();

            // Calculate exact months stayed (minimum 1 month)
            $monthsStayed = $moveInDate->diffInMonths($today);
            $monthsStayed = max(1, $monthsStayed); // At least 1 month

            // Generate bills for each month stayed
            for ($i = 0; $i < $monthsStayed; $i++) {
                // Calculate due date (same day as move_in_date each month)
                $dueDate = $moveInDate->copy()->addMonths($i + 1)->endOfDay();

                // Adjust for months with fewer days
                $daysInMonth = $dueDate->daysInMonth;
                $dueDay = min($originalDay, $daysInMonth);
                $dueDate = $dueDate->day($dueDay);

                // Create bill on move_in_date (for first) or previous due date
                $createdAt = ($i === 0)
                    ? $moveInDate->copy()
                    : $moveInDate->copy()->addMonths($i)->startOfDay();

                $bill = Bill::create([
                    'tenant_id' => $tenant->id,
                    'amount_due' => $tenant->room->rent_amount,
                    'due_date' => $dueDate,
                    'status' => BillStatus::UNPAID->value,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // 90% chance of payment (except for future bills)
                if ($dueDate->isPast() && fake()->boolean(90)) {
                    $isOnTime = fake()->boolean(90); // 90% on-time

                    if ($isOnTime) {
                        // On-time payment between creation and due date
                        $paymentDate = fake()->dateTimeBetween(
                            $createdAt->format('Y-m-d'),
                            $dueDate->format('Y-m-d')
                        );
                    } else {
                        // Late payment (after due date but before now)
                        $paymentDate = fake()->dateTimeBetween(
                            $dueDate->addDay()->format('Y-m-d'),
                            $today->format('Y-m-d')
                        );
                    }

                    Transaction::create([
                        'tenant_id' => $tenant->id,
                        'bill_id' => $bill->id,
                        // 'amount' => $bill->amount_due,
                        'payment_method' => fake()->randomElement($paymentMethods),
                        'proof_photo' => fake()->boolean(30) ? 'path/to/photo.jpg' : null,
                        'payment_date' => $paymentDate,
                        'status' => TransactionStatus::COMPLETED->value,
                        'confirmed_at' => $paymentDate,
                    ]);

                    $bill->timestamps = false;
                    $bill->status = BillStatus::PAID->value;
                    $bill->save();
                } elseif ($dueDate->isPast()) {
                    $bill->timestamps = false;
                    $bill->status = BillStatus::OVERDUE->value;
                    $bill->save();
                }
            }
        }
    }

    /**
     * Creates 0–2 maintenance requests per tenant room, choosing from a pool of issues.
     *
     * @param  Tenant[]  $tenants
     */
    private function createTestMaintenanceRequest(array $tenants): void
    {
        $issues = [
            [
                'title' => 'Leaky Faucet',
                'description' => "Hello,\n\nI wanted to report that the faucet in the bathroom is leaking continuously. Water keeps dripping even when turned off completely.\nThis is causing water to pool in the sink and makes a constant dripping noise throughout the night.\nCould someone please take a look at it soon?\n\nThank you!",
            ],
            [
                'title' => 'Air Conditioner Not Cooling',
                'description' => "Hi,\n\nThe air conditioner in my room is running but it's not blowing any cold air. I tried adjusting the thermostat and cleaning the filter, but it didn't help.\nIt's been very uncomfortable during the afternoons.\nCan you kindly send someone to check or repair it?\n\nBest regards.",
            ],
            [
                'title' => 'Broken Door Lock',
                'description' => "Good day,\n\nThe lock on my door has been difficult to turn and now it's completely jammed. I am unable to lock my room when I go out, which is a safety concern for me.\nPlease send a maintenance staff to fix or replace the lock as soon as possible.\n\nThank you for your assistance.",
            ],
            [
                'title' => 'Internet Connectivity Issues',
                'description' => "Hello Admin,\n\nI've been experiencing frequent internet disconnections in my room. The WiFi signal is weak and sometimes the network disappears entirely.\nThis has affected my ability to attend online classes and meetings.\nWould appreciate if you could check the router or the network connection in our area.\n\nSincerely.",
            ],
            [
                'title' => 'Clogged Drain',
                'description' => "Hi,\n\nThe bathroom drain is clogged and water takes a long time to go down. It sometimes overflows onto the floor, making it slippery and causing a bad smell.\nCan you please send someone to clean or fix the drain soon?\n\nThanks!",
            ],
            [
                'title' => 'No Hot Water',
                'description' => "Hello,\n\nThere has been no hot water in the shower for the past couple of days. I checked with my roommates and they're experiencing the same issue.\nCould you please arrange for the water heater to be inspected and repaired?\n\nThank you very much.",
            ],
            [
                'title' => 'Pest Infestation',
                'description' => "Hi,\n\nI have noticed several cockroaches and ants in the kitchen and bathroom areas. They seem to be coming from under the sink.\nIt's getting worse, and I'm worried about hygiene and food safety.\nWould you please arrange for pest control soon?\n\nBest regards.",
            ],
            [
                'title' => 'Light Bulb Replacement',
                'description' => "Greetings,\n\nThe light bulb in the hallway right outside my room has burnt out and it's very dark at night. It's a bit hazardous to walk through that area.\nCould you please have someone replace the bulb?\n\nThank you!",
            ],
            [
                'title' => 'Cracked Window',
                'description' => "Hello,\n\nThere's a visible crack in the window glass of my room. I'm concerned it might break further, especially during strong winds or rain.\nCould maintenance please check and repair or replace the window soon?\n\nThank you.",
            ],
            [
                'title' => 'Washing Machine Not Working',
                'description' => "Hi,\n\nThe shared washing machine is not spinning during the wash cycle. Clothes remain soaking wet after the cycle ends.\nThis has been an ongoing issue for the past week.\nPlease send someone to inspect and repair the washing machine.\n\nThank you for your help.",
            ],
            [
                'title' => 'Water Heater Making Loud Noise',
                'description' => "Hello,\n\nThe water heater is making loud banging and rumbling noises when it's on. I'm worried it might be a sign of a malfunction.\nPlease have it inspected soon to avoid further damage or safety risks.\n\nThanks!",
            ],
            [
                'title' => 'Ceiling Leak During Rain',
                'description' => "Hi,\n\nThere's a leak in the ceiling that becomes active whenever it rains. Water drips down slowly near the corner of the room.\nIt's starting to stain the ceiling and might damage furniture.\nPlease assist at your earliest convenience.\n\nRegards.",
            ],
            [
                'title' => "Toilet Won't Flush Properly",
                'description' => "Good day,\n\nThe toilet in our bathroom isn't flushing completely. Sometimes it takes two or more tries, and even then, it doesn't clear properly.\nCould maintenance please take a look?\n\nAppreciate your help.",
            ],
            [
                'title' => 'Power Outlet Sparks',
                'description' => "Hi,\n\nOne of the power outlets in my room sparks when I plug anything in. I'm afraid it could be a fire hazard.\nCan you send an electrician to inspect it?\n\nThank you.",
            ],
            [
                'title' => 'Mold on Wall',
                'description' => "Hello,\n\nI've noticed mold growing on one of the walls in my room, possibly due to humidity or a hidden leak.\nThis could be a health issue. Can maintenance take a look?\n\nThank you very much.",
            ],
            [
                'title' => 'Broken Window Screen',
                'description' => "Hi,\n\nThe screen on the window is torn and bugs are coming in at night.\nPlease send someone to replace or repair it.\n\nThanks!",
            ],
            [
                'title' => 'Fridge Not Cooling',
                'description' => "Hi Admin,\n\nThe shared refrigerator in the common area is not cooling. Food is spoiling quickly.\nCan someone please check it urgently?\n\nThanks!",
            ],
            [
                'title' => 'Loose Towel Rack',
                'description' => "Hello,\n\nThe towel rack in the bathroom is coming loose from the wall. It feels like it could fall off any moment.\nCan it be tightened or reinstalled properly?\n\nThanks for your help!",
            ],
            [
                'title' => 'Elevator Stuck Often',
                'description' => "Hi,\n\nThe elevator in the building gets stuck frequently, especially on the 3rd floor. It's becoming unreliable.\nCould this be looked into for everyone's safety?\n\nThanks!",
            ],
            [
                'title' => 'Smoke Detector Beeping',
                'description' => "Good day,\n\nThe smoke detector in my unit is constantly beeping. I've replaced the battery but the noise continues.\nPlease have someone check or replace it.\n\nRegards.",
            ],
        ];

        $weights = [0, 0, 0, 0, 0, 0, 0, 0, 1, 2]; // 80% for 0, (10% for 1 or 2)
        foreach ($tenants as $tenant) {
            $requestsToCreate = fake()->randomElement($weights);
            if ($requestsToCreate === 0) {
                continue;
            }

            $chosenIssues = collect($issues)->shuffle()->take($requestsToCreate);
            foreach ($chosenIssues as $issue) {
                MaintenanceRequest::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $tenant->room_id,
                    'title' => $issue['title'],
                    'description' => $issue['description'],
                    'status' => fake()->randomElement(MaintenanceRequestStatus::cases())->value,
                ]);
            }
        }
    }

    private function createTestAnnouncements(array $properties, array $rooms): void
    {
        // --- System-wide Announcements ---
        $systemAnnouncements = [
            [
                'title' => 'Scheduled System Maintenance',
                'description' => "Please be advised that our system will undergo scheduled maintenance this coming Sunday,\nfrom 1:00 AM to 3:00 AM. During this window, some features may become temporarily unavailable,\nand users might experience brief service interruptions.\n\nWe recommend saving your work and avoiding any critical transactions during this time to prevent data loss.\n\nOur team will be working diligently to ensure that everything is back up and running as quickly as possible.\nWe appreciate your patience and understanding as we continue to improve the performance and reliability of our platform.",
            ],
            [
                'title' => 'Welcome to Our Platform!',
                'description' => "We're excited to welcome you to our rental management platform! Whether you're a landlord, property manager,\nor tenant, we've built this tool to make your day-to-day operations simpler and more organized.\n\nYou can manage maintenance requests, view announcements, track payments, and communicate with other members of your community,\nall in one place.\n\nPlease take a few minutes to explore the dashboard and familiarize yourself with the features.\nIf you need any help, our support team is available via chat or email.\nWe're here to support you every step of the way.",
            ],
            [
                'title' => 'New Feature: Payment Reminders',
                'description' => "We're happy to introduce a new feature on the platform: automatic payment reminders.\nThis feature was developed in response to community feedback and aims to help tenants avoid late fees,\nand keep landlords informed about payment activity.\n\nReminders will be sent via email and will also appear in your dashboard notifications prior to your rent due date.\nYou can customize the timing of these reminders in your account settings.\n\nWe encourage everyone to check their contact details to ensure they're accurate.\nAs always, thank you for using our platform, and expect more helpful tools soon!",
            ],
        ];

        foreach ($systemAnnouncements as $data) {
            Announcement::create([
                'type' => AnnouncementType::SYSTEM->value,
                'title' => $data['title'],
                'description' => $data['description'],
                'created_at' => $this->randomCreatedAt(),
                'updated_at' => now(),
            ]);
        }

        // --- Property-wide Announcements ---
        $propertyWideMessages = [
            'Water Supply Interruption' => "Please be informed that there will be a scheduled water supply interruption tomorrow,\nJune 13, from 9:00 AM to 12:00 NN. This is due to maintenance work being conducted by the local water utility provider.\n\nWe highly encourage all residents to store enough water in advance for drinking, cooking, and personal hygiene.\nAdditionally, please avoid using water heaters or washing machines during the downtime to prevent any damage.\n\nWe understand this may cause inconvenience, and we appreciate your patience as we work to maintain essential services\nwithin the property. Regular updates will be posted if there are any changes to the schedule.",
            'Pest Control Notice' => "This is to inform all residents that our routine pest control service will be conducted this Saturday,\nstarting at 8:00 AM and continuing throughout the day in all common areas including hallways, lobbies, and stairwells.\n\nPlease ensure that food items are securely stored and that your doors and windows remain closed during the scheduled time.\nIf you are sensitive to chemicals or have pets, we recommend staying clear of treated areas until the fumes dissipate.\n\nWe appreciate your cooperation in helping us keep the property clean, safe, and pest-free for everyone.",
            'Rent Due Reminder' => "Just a friendly reminder to all tenants that rent is due on or before the 5th of every month,\naccording to the terms of your lease agreement. Timely payment ensures uninterrupted access to services\nand helps us maintain operations smoothly.\n\nIf you anticipate any delays or need assistance with your billing statement, please contact the management office\nat least 3 days prior to the due date. Payments can be made via bank transfer, GCash, or over-the-counter at our office.\n\nThank you for your cooperation and continued tenancy. We're here to help with any concerns you may have.",
            'Garbage Collection Schedule' => "Please be advised that effective this week, garbage will now be collected every Monday and Thursday at 6:00 AM.\nResidents are requested to place their trash outside their units before the scheduled time to ensure proper collection.\n\nMake sure your waste is properly bagged and sealed to avoid attracting pests or creating foul odors in the hallways.\nDo not leave garbage outside beyond collection hours, as this creates hygiene and safety concerns.\n\nLet's work together to keep our shared spaces clean and pleasant for everyone. Thank you for your cooperation and support.",
            'New WiFi Password' => "We've recently updated the WiFi password for the shared connections available in the building's common areas,\nincluding the lobby, function hall, and rooftop lounge. This update is part of our regular security maintenance\nand aims to prevent unauthorized usage and ensure stable connectivity for residents.\n\nTo request the new password, please visit the property management office during business hours or send a message\nthrough the tenant portal. For security reasons, we will only release passwords to verified tenants.\n\nWe appreciate your understanding and hope this helps improve the overall WiFi experience for everyone.",
        ];

        foreach ($propertyWideMessages as $title => $description) {
            $property = fake()->randomElement($properties);

            Announcement::create([
                'type' => AnnouncementType::PROPERTY->value,
                'title' => $title,
                'description' => $description,
                'property_id' => $property->id,
                'created_at' => $this->randomCreatedAt(),
                'updated_at' => now(),
            ]);
        }

        // --- Room-specific Announcements ---
        $roomWideMessages = [
            'Air Conditioner Checkup' => "We will be conducting a routine air conditioner inspection and maintenance service in your room\nthis Friday between 10:00 AM and 3:00 PM. This check is part of our quarterly effort to ensure\nall cooling units are functioning efficiently and to prevent unexpected breakdowns during hotter months.\n\nPlease ensure that someone is available to provide access during this time, or you may leave your key\nat the admin office beforehand. Kindly clear the area around your air conditioner to make space\nfor our technicians to work safely. We appreciate your cooperation and aim to complete all checks smoothly.",

            'Room Inspection Reminder' => "This is a friendly reminder that routine room inspections will take place next week, from Monday\nto Wednesday, between 9:00 AM and 4:00 PM. The purpose of these inspections is to ensure compliance\nwith health and safety standards and to identify any necessary maintenance needs.\n\nPlease make sure your room is clean and accessible. Personal belongings should be organized,\nand any potential hazards removed from walkways. These inspections are quick and non-intrusive,\nwith a focus on maintaining a safe and comfortable living environment for everyone.\nShould you have any questions or require rescheduling, contact the admin office in advance.",

            'Cleaning Schedule' => "Weekly room cleaning will take place every Wednesday at 10:00 AM. This scheduled cleaning\nis part of our housekeeping service to maintain hygiene and prevent pest buildup across all rooms.\n\nTo help our staff clean effectively, please tidy up your space beforehand and store away\nany personal or fragile items. The service includes sweeping, light dusting, and trash removal.\n\nIf you are not available during the cleaning time, we suggest arranging access with the admin office.\nRegular cleaning ensures your living space remains safe, comfortable, and pleasant for all residents.\nThank you for your cooperation and understanding.",

            'Noise Complaint' => "We kindly remind all residents to be mindful of noise levels, especially during designated quiet hours\nfrom 10:00 PM to 7:00 AM. Excessive volume from music, televisions, or gatherings can negatively\nimpact the sleep and study routines of your fellow tenants.\n\nWe understand that occasional noise is unavoidable, but let's all make an effort to keep\nour shared environment respectful and peaceful. If you need to host a guest or anticipate noise,\nplease inform the management in advance.\n\nLet's continue to foster a considerate community by being aware of how our actions affect others nearby.\nThank you for your cooperation.",

            'Maintenance Follow-Up' => "Our maintenance team will return to your room on Thursday to complete repairs related\nto the plumbing issue reported last week. The follow-up work includes rechecking pipe fittings,\nsealing leaks, and ensuring water flow is back to normal.\n\nPlease ensure the area is accessible by clearing the space under the sink and near\nany plumbing fixtures. If you're unavailable during the repair window, you may leave your room key\nat the admin office with a note authorizing access.\n\nWe appreciate your patience as we work to resolve the issue fully.\nIf you notice any related concerns afterward, please report them immediately.",

            'Shared Appliance Usage' => "This is a reminder to please use shared appliances in a responsible and courteous manner.\nThe kitchen is a shared space, and all residents are expected to clean up after using\nappliances like the microwave, rice cooker, and refrigerator.\n\nPlease do not leave food spills or dirty utensils unattended, as this creates inconvenience for others\nand increases the risk of pests. Be sure to label your food items and discard expired goods regularly.\n\nLet's work together to keep the common kitchen clean, safe, and usable for everyone.\nIf issues persist, stricter usage guidelines may be implemented by management.",

            'Fridge Cleaning' => "To maintain cleanliness and prevent foul odors, we will be cleaning out the shared refrigerator\nthis Sunday at 4:00 PM. All unlabelled, expired, or spoiled items will be removed\nduring this time without exception.\n\nPlease check your stored items before Sunday and ensure everything is clearly labeled with\nyour name and the date it was stored. We kindly ask all residents to take responsibility\nfor keeping the fridge organized and sanitary.\n\nA clean and well-maintained fridge benefits everyone and reduces the risk of cross-contamination.\nThank you for your cooperation in keeping our shared spaces tidy and hygienic.",

            'New Roommate' => "We'd like to inform you that a new roommate will be joining your room starting next week.\nThis is part of our standard occupancy management, and we ask that you help them feel welcome\nduring their transition.\n\nPlease make space available for their belongings and be open to sharing important information\nabout the room and community guidelines. A short adjustment period is normal,\nand we hope this change will be a positive experience for everyone.\n\nIf you have concerns or questions regarding room sharing, feel free to reach out\nto the property manager. Thank you for your support in fostering a friendly environment.",

            'Emergency Drill' => "There will be an emergency evacuation drill this Friday at 3:00 PM. Participation is mandatory\nfor all residents, as this drill ensures everyone is familiar with safety protocols in case\nof fire, earthquake, or other emergencies.\n\nPlease take the drill seriously. Familiarize yourself with the nearest exits and emergency assembly points.\nAn announcement will be made before the drill begins. Avoid using elevators during the drill,\nand follow instructions from the staff or security team.\n\nSafety is a top priority, and your cooperation is essential to make this exercise successful.\nThank you for doing your part to keep the community prepared.",

            'Room Renovation Notice' => "Minor renovations will take place in your room next weekend between 9:00 AM and 5:00 PM.\nThis may include paint touch-ups, fixture replacements, or repairs to worn-out furnishings.\n\nWe understand that renovations can be disruptive, and we will do our best to minimize noise\nand disturbance during this period. Please secure any fragile or valuable items\nand inform the staff if you have specific concerns.\n\nIf you won't be home, you can authorize access by leaving your key with the admin office.\nWe appreciate your understanding and patience as we work to improve your living space.",
        ];

        foreach ($roomWideMessages as $title => $description) {
            $room = fake()->randomElement($rooms);

            Announcement::create([
                'type' => AnnouncementType::ROOM->value,
                'title' => $title,
                'description' => $description,
                'property_id' => $room->property_id,
                'room_id' => $room->id,
                'created_at' => $this->randomCreatedAt(),
                'updated_at' => now(),
            ]);
        }
    }

    private function randomCreatedAt(): Carbon
    {
        return now()
            ->subDays(fake()->numberBetween(1, 30))
            ->addHours(fake()->numberBetween(0, 23))
            ->addMinutes(fake()->numberBetween(0, 59));
    }
}
