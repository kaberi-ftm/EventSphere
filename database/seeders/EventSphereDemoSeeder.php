<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class EventSphereDemoSeeder extends Seeder
{
    private array $columnCache = [];

    private array $ids = [];

    public function run(): void
    {
        $this->command?->newLine();

        $this->command?->info(
            'Starting EventSphere demo data seeding...'
        );

        $this->runModule(
            'Roles and users',
            fn () => $this->seedRolesAndUsers()
        );

        $this->runModule(
            'Clubs and memberships',
            fn () => $this->seedClubsAndMemberships()
        );

        $this->runModule(
            'Venues and events',
            fn () => $this->seedVenuesAndEvents()
        );

        $this->runModule(
            'Registrations and attendances',
            fn () => $this->seedRegistrationsAndAttendances()
        );

        $this->runModule(
            'Volunteers',
            fn () => $this->seedVolunteers()
        );

        $this->runModule(
            'Volunteer tasks',
            fn () => $this->seedTasks()
        );

        $this->runModule(
            'Sponsors',
            fn () => $this->seedSponsors()
        );

        $this->runModule(
            'Event sponsorships',
            fn () => $this->seedEventSponsorships()
        );

        $this->runModule(
            'Budgets and payments',
            fn () => $this->seedBudgetsAndPayments()
        );

        $this->runModule(
            'Certificates',
            fn () => $this->seedCertificates()
        );

        $this->runModule(
            'Notifications',
            fn () => $this->seedNotifications()
        );

        $this->command?->newLine();

        $this->command?->info(
            'EventSphere demo seeding completed.'
        );

        $this->command?->warn(
            'All demo account passwords are: password'
        );
    }

    private function runModule(
        string $name,
        Closure $callback
    ): void {
        try {
            DB::transaction($callback);

            $this->command?->info(
                "✓ {$name}"
            );
        } catch (Throwable $exception) {
            $this->command?->warn(
                "Skipped {$name}: "
                . $exception->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Roles and users
    |--------------------------------------------------------------------------
    */

    private function seedRolesAndUsers(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
            ],
            [
                'name' => 'executive',
                'display_name' => 'Club Executive',
            ],
            [
                'name' => 'volunteer',
                'display_name' => 'Volunteer',
            ],
            [
                'name' => 'participant',
                'display_name' => 'Participant',
            ],
        ];

        foreach ($roles as $role) {
            $this->ids[
                'role_' . $role['name']
            ] = $this->upsertAndGetId(
                'roles',
                [
                    'name' => $role['name'],
                ],
                [
                    'display_name' =>
                        $role['display_name'],
                ]
            );
        }

        $users = [
            [
                'key' => 'admin',
                'role' => 'admin',
                'name' => 'EventSphere Admin',
                'email' => 'admin@eventsphere.test',
                'points' => 950,
            ],
            [
                'key' => 'executive',
                'role' => 'executive',
                'name' => 'Nusrat Jahan',
                'email' => 'executive@eventsphere.test',
                'points' => 720,
            ],
            [
                'key' => 'volunteer_one',
                'role' => 'volunteer',
                'name' => 'Rahim Ahmed',
                'email' => 'volunteer@eventsphere.test',
                'points' => 580,
            ],
            [
                'key' => 'volunteer_two',
                'role' => 'volunteer',
                'name' => 'Sadia Islam',
                'email' => 'volunteer2@eventsphere.test',
                'points' => 490,
            ],
            [
                'key' => 'participant_one',
                'role' => 'participant',
                'name' => 'Arafat Hossain',
                'email' => 'participant@eventsphere.test',
                'points' => 260,
            ],
            [
                'key' => 'participant_two',
                'role' => 'participant',
                'name' => 'Tasnim Akter',
                'email' => 'participant2@eventsphere.test',
                'points' => 210,
            ],
            [
                'key' => 'participant_three',
                'role' => 'participant',
                'name' => 'Sabbir Hasan',
                'email' => 'participant3@eventsphere.test',
                'points' => 170,
            ],
            [
                'key' => 'participant_four',
                'role' => 'participant',
                'name' => 'Maliha Noor',
                'email' => 'participant4@eventsphere.test',
                'points' => 150,
            ],
        ];

        foreach ($users as $user) {
            $this->ids[
                'user_' . $user['key']
            ] = $this->upsertAndGetId(
                'users',
                [
                    'email' => $user['email'],
                ],
                [
                    'role_id' =>
                        $this->ids[
                            'role_' . $user['role']
                        ],

                    'name' => $user['name'],

                    'email_verified_at' =>
                        Carbon::now(),

                    'password' =>
                        Hash::make('password'),

                    'engagement_points' =>
                        $user['points'],
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Clubs and memberships
    |--------------------------------------------------------------------------
    */

    private function seedClubsAndMemberships(): void
    {
        $this->requireIds([
            'user_admin',
            'user_executive',
            'user_volunteer_one',
            'user_participant_one',
        ]);

        $clubs = [
            [
                'key' => 'computer',
                'name' => 'Computer Club',
                'description' =>
                    'Technology, programming and innovation club.',
                'founded_date' =>
                    Carbon::create(2018, 2, 15),
                'admin_user_id' =>
                    $this->ids['user_executive'],
            ],
            [
                'key' => 'business',
                'name' => 'Business Club',
                'description' =>
                    'Entrepreneurship, leadership and career development.',
                'founded_date' =>
                    Carbon::create(2019, 7, 8),
                'admin_user_id' =>
                    $this->ids['user_admin'],
            ],
            [
                'key' => 'cultural',
                'name' => 'Cultural Club',
                'description' =>
                    'Arts, culture, music and creative activities.',
                'founded_date' =>
                    Carbon::create(2017, 10, 21),
                'admin_user_id' =>
                    $this->ids['user_admin'],
            ],
        ];

        foreach ($clubs as $club) {
            $this->ids[
                'club_' . $club['key']
            ] = $this->upsertAndGetId(
                'clubs',
                [
                    'name' => $club['name'],
                ],
                [
                    'description' =>
                        $club['description'],

                    'founded_date' =>
                        $club['founded_date'],

                    'admin_user_id' =>
                        $club['admin_user_id'],
                ]
            );
        }

        if (!$this->tableExists(
            'club_memberships'
        )) {
            return;
        }

        $memberships = [
            [
                'user_id' =>
                    $this->ids['user_executive'],

                'club_id' =>
                    $this->ids['club_computer'],

                'member_role' => 'president',
            ],
            [
                'user_id' =>
                    $this->ids['user_volunteer_one'],

                'club_id' =>
                    $this->ids['club_computer'],

                'member_role' => 'member',
            ],
            [
                'user_id' =>
                    $this->ids['user_participant_one'],

                'club_id' =>
                    $this->ids['club_business'],

                'member_role' => 'member',
            ],
        ];

        foreach ($memberships as $membership) {
            $this->upsertAndGetId(
                'club_memberships',
                [
                    'user_id' =>
                        $membership['user_id'],

                    'club_id' =>
                        $membership['club_id'],
                ],
                [
                    'member_role' =>
                        $membership['member_role'],

                    'joined_at' =>
                        Carbon::now()->subMonths(4),
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Venues and events
    |--------------------------------------------------------------------------
    */

    private function seedVenuesAndEvents(): void
    {
        $venues = [
            [
                'key' => 'auditorium',
                'name' => 'Central Auditorium',
                'location' =>
                    'Main Academic Building',
                'capacity' => 500,
                'description' =>
                    'Large auditorium with stage and sound facilities.',
            ],
            [
                'key' => 'lab',
                'name' => 'Innovation Lab',
                'location' =>
                    'Technology Building, Level 3',
                'capacity' => 120,
                'description' =>
                    'Modern laboratory for workshops and hackathons.',
            ],
            [
                'key' => 'conference',
                'name' => 'Conference Hall',
                'location' =>
                    'Administration Building',
                'capacity' => 220,
                'description' =>
                    'Professional venue for seminars and conferences.',
            ],
        ];

        foreach ($venues as $venue) {
            $this->ids[
                'venue_' . $venue['key']
            ] = $this->upsertAndGetId(
                'venues',
                [
                    'name' => $venue['name'],
                ],
                [
                    'location' =>
                        $venue['location'],

                    'capacity' =>
                        $venue['capacity'],

                    'description' =>
                        $venue['description'],
                ]
            );
        }

        $this->requireIds([
            'club_computer',
            'club_business',
            'club_cultural',
            'user_admin',
            'user_executive',
        ]);

        $events = [
            [
                'key' => 'tech_fest',
                'club_id' =>
                    $this->ids['club_computer'],

                'created_by' =>
                    $this->ids['user_executive'],

                'venue_id' =>
                    $this->ids[
                        'venue_auditorium'
                    ] ?? null,

                'title' =>
                    'EventSphere Tech Innovation Fest',

                'description' =>
                    'A technology festival featuring projects, coding challenges and innovation showcases.',

                'start_time' =>
                    Carbon::now()
                        ->addDays(25)
                        ->setTime(10, 0),

                'end_time' =>
                    Carbon::now()
                        ->addDays(25)
                        ->setTime(17, 0),

                'status' => 'upcoming',
                'max_participants' => 350,
            ],
            [
                'key' => 'career_summit',
                'club_id' =>
                    $this->ids['club_business'],

                'created_by' =>
                    $this->ids['user_admin'],

                'venue_id' =>
                    $this->ids[
                        'venue_conference'
                    ] ?? null,

                'title' =>
                    'Career Development Summit 2026',

                'description' =>
                    'Career talks, networking sessions and professional development workshops.',

                'start_time' =>
                    Carbon::now()
                        ->addDays(40)
                        ->setTime(9, 30),

                'end_time' =>
                    Carbon::now()
                        ->addDays(40)
                        ->setTime(16, 30),

                'status' => 'upcoming',
                'max_participants' => 200,
            ],
            [
                'key' => 'cultural_night',
                'club_id' =>
                    $this->ids['club_cultural'],

                'created_by' =>
                    $this->ids['user_admin'],

                'venue_id' =>
                    $this->ids[
                        'venue_auditorium'
                    ] ?? null,

                'title' =>
                    'Annual Cultural Night',

                'description' =>
                    'An evening of music, theatre, dance and cultural performances.',

                'start_time' =>
                    Carbon::now()
                        ->addDays(55)
                        ->setTime(17, 30),

                'end_time' =>
                    Carbon::now()
                        ->addDays(55)
                        ->setTime(21, 30),

                'status' => 'upcoming',
                'max_participants' => 450,
            ],
            [
                'key' => 'workshop',
                'club_id' =>
                    $this->ids['club_computer'],

                'created_by' =>
                    $this->ids['user_executive'],

                'venue_id' =>
                    $this->ids['venue_lab']
                        ?? null,

                'title' =>
                    'Laravel and Oracle Workshop',

                'description' =>
                    'Hands-on workshop on Laravel integration with Oracle Database.',

                'start_time' =>
                    Carbon::now()
                        ->subDays(12)
                        ->setTime(10, 0),

                'end_time' =>
                    Carbon::now()
                        ->subDays(12)
                        ->setTime(15, 0),

                'status' => 'completed',
                'max_participants' => 90,
            ],
        ];

        foreach ($events as $event) {
            $this->ids[
                'event_' . $event['key']
            ] = $this->upsertAndGetId(
                'events',
                [
                    'title' => $event['title'],
                ],
                [
                    'club_id' =>
                        $event['club_id'],

                    'created_by' =>
                        $event['created_by'],

                    'venue_id' =>
                        $event['venue_id'],

                    'description' =>
                        $event['description'],

                    'start_time' =>
                        $event['start_time'],

                    'end_time' =>
                        $event['end_time'],

                    'status' =>
                        $event['status'],

                    'max_participants' =>
                        $event['max_participants'],

                    'poster' => null,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Registrations and attendances
    |--------------------------------------------------------------------------
    */

    private function seedRegistrationsAndAttendances(): void
    {
        $registrations = [
            [
                'event' => 'tech_fest',
                'user' => 'participant_one',
                'status' => 'Pending',
            ],
            [
                'event' => 'tech_fest',
                'user' => 'participant_two',
                'status' => 'Approved',
            ],
            [
                'event' => 'career_summit',
                'user' => 'participant_three',
                'status' => 'Approved',
            ],
            [
                'event' => 'cultural_night',
                'user' => 'participant_four',
                'status' => 'Pending',
            ],
            [
                'event' => 'workshop',
                'user' => 'participant_one',
                'status' => 'Approved',
            ],
            [
                'event' => 'workshop',
                'user' => 'participant_two',
                'status' => 'Approved',
            ],
        ];

        foreach ($registrations as $registration) {
            $eventId = $this->ids[
                'event_' . $registration['event']
            ] ?? null;

            $userId = $this->ids[
                'user_' . $registration['user']
            ] ?? null;

            if (!$eventId || !$userId) {
                continue;
            }

            $this->upsertAndGetId(
                'registrations',
                [
                    'event_id' => $eventId,
                    'user_id' => $userId,
                ],
                [
                    'status' =>
                        $registration['status'],

                    'registered_at' =>
                        Carbon::now()->subDays(5),
                ]
            );
        }

        $completedEvent =
            $this->ids['event_workshop']
            ?? null;

        if (!$completedEvent) {
            return;
        }

        foreach ([
            'participant_one',
            'participant_two',
        ] as $userKey) {
            $userId =
                $this->ids[
                    'user_' . $userKey
                ] ?? null;

            if (!$userId) {
                continue;
            }

            $this->upsertAndGetId(
                'attendances',
                [
                    'event_id' =>
                        $completedEvent,

                    'user_id' =>
                        $userId,
                ],
                [
                    'is_present' => 1,

                    'checked_in_at' =>
                        Carbon::now()
                            ->subDays(12)
                            ->setTime(9, 45),
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Volunteers
    |--------------------------------------------------------------------------
    */

    private function seedVolunteers(): void
    {
        $volunteers = [
            [
                'key' => 'tech_one',
                'user' => 'volunteer_one',
                'event' => 'tech_fest',
                'role' => 'Technical Support',
                'status' => 'approved',
            ],
            [
                'key' => 'tech_two',
                'user' => 'volunteer_two',
                'event' => 'tech_fest',
                'role' => 'Registration Desk',
                'status' => 'approved',
            ],
            [
                'key' => 'career_one',
                'user' => 'volunteer_one',
                'event' => 'career_summit',
                'role' => 'Stage Management',
                'status' => 'pending',
            ],
        ];

        foreach ($volunteers as $volunteer) {
            $userId =
                $this->ids[
                    'user_' . $volunteer['user']
                ] ?? null;

            $eventId =
                $this->ids[
                    'event_' . $volunteer['event']
                ] ?? null;

            if (!$userId || !$eventId) {
                continue;
            }

            $this->ids[
                'volunteer_' . $volunteer['key']
            ] = $this->upsertAndGetId(
                'volunteers',
                [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                ],
                [
                    'status' =>
                        $volunteer['status'],

                    'role' =>
                        $volunteer['role'],

                    'applied_at' =>
                        Carbon::now()->subDays(8),

                    'approved_at' =>
                        $volunteer['status']
                            === 'approved'
                                ? Carbon::now()
                                    ->subDays(6)
                                : null,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tasks
    |--------------------------------------------------------------------------
    */

    private function seedTasks(): void
    {
        if (!$this->tableExists('tasks')) {
            return;
        }

        $taskTitleColumn = match (true) {
            $this->hasColumn(
                'tasks',
                'title'
            ) => 'title',

            $this->hasColumn(
                'tasks',
                'task_name'
            ) => 'task_name',

            $this->hasColumn(
                'tasks',
                'name'
            ) => 'name',

            default => null,
        };

        if (!$taskTitleColumn) {
            throw new RuntimeException(
                'No task title column was found.'
            );
        }

        $tasks = [
            [
                'title' =>
                    'Prepare registration desk',

                'description' =>
                    'Arrange participant lists, badges and registration materials.',

                'event_id' =>
                    $this->ids['event_tech_fest']
                    ?? null,

                'volunteer_id' =>
                    $this->ids[
                        'volunteer_tech_two'
                    ] ?? null,

                'assigned_to' =>
                    $this->ids[
                        'user_volunteer_two'
                    ] ?? null,

                'status' => 'pending',
                'priority' => 'high',

                'due_date' =>
                    Carbon::now()->addDays(22),
            ],
            [
                'title' =>
                    'Test stage equipment',

                'description' =>
                    'Verify sound, projector and technical equipment before the event.',

                'event_id' =>
                    $this->ids['event_tech_fest']
                    ?? null,

                'volunteer_id' =>
                    $this->ids[
                        'volunteer_tech_one'
                    ] ?? null,

                'assigned_to' =>
                    $this->ids[
                        'user_volunteer_one'
                    ] ?? null,

                'status' => 'in_progress',
                'priority' => 'medium',

                'due_date' =>
                    Carbon::now()->addDays(23),
            ],
        ];

        foreach ($tasks as $task) {
            $this->upsertAndGetId(
                'tasks',
                [
                    $taskTitleColumn =>
                        $task['title'],
                ],
                [
                    'description' =>
                        $task['description'],

                    'event_id' =>
                        $task['event_id'],

                    'volunteer_id' =>
                        $task['volunteer_id'],

                    'assigned_to' =>
                        $task['assigned_to'],

                    'user_id' =>
                        $task['assigned_to'],

                    'status' =>
                        $task['status'],

                    'priority' =>
                        $task['priority'],

                    'due_date' =>
                        $task['due_date'],

                    'deadline' =>
                        $task['due_date'],

                    'completed_at' => null,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Sponsors
    |--------------------------------------------------------------------------
    */

    private function seedSponsors(): void
    {
        $sponsors = [
            [
                'key' => 'techcorp',
                'name' => 'TechCorp Bangladesh',
                'contact_person' =>
                    'Mahmud Rahman',
                'email' =>
                    'partnerships@techcorp.test',
                'phone' => '01711000001',
                'address' =>
                    'Gulshan, Dhaka',
                'website' =>
                    'https://example.com/techcorp',
                'sponsor_type' => 'corporate',
                'status' => 'active',
                'description' =>
                    'Technology and innovation partner.',
            ],
            [
                'key' => 'careerhub',
                'name' => 'CareerHub',
                'contact_person' =>
                    'Nabila Khan',
                'email' =>
                    'events@careerhub.test',
                'phone' => '01711000002',
                'address' =>
                    'Banani, Dhaka',
                'website' =>
                    'https://example.com/careerhub',
                'sponsor_type' => 'corporate',
                'status' => 'active',
                'description' =>
                    'Career development and recruitment partner.',
            ],
            [
                'key' => 'mediaplus',
                'name' => 'MediaPlus',
                'contact_person' =>
                    'Fahim Ahmed',
                'email' =>
                    'hello@mediaplus.test',
                'phone' => '01711000003',
                'address' =>
                    'Dhanmondi, Dhaka',
                'website' =>
                    'https://example.com/mediaplus',
                'sponsor_type' => 'media',
                'status' => 'active',
                'description' =>
                    'Media and promotional partner.',
            ],
        ];

        foreach ($sponsors as $sponsor) {
            $this->ids[
                'sponsor_' . $sponsor['key']
            ] = $this->upsertAndGetId(
                'sponsors',
                [
                    'name' => $sponsor['name'],
                ],
                [
                    'contact_person' =>
                        $sponsor['contact_person'],

                    'email' =>
                        $sponsor['email'],

                    'phone' =>
                        $sponsor['phone'],

                    'address' =>
                        $sponsor['address'],

                    'website' =>
                        $sponsor['website'],

                    'sponsor_type' =>
                        $sponsor['sponsor_type'],

                    'status' =>
                        $sponsor['status'],

                    'description' =>
                        $sponsor['description'],
                ]
            );
        }
    }

    private function seedEventSponsorships(): void
    {
        $items = [
            [
                'event' => 'tech_fest',
                'sponsor' => 'techcorp',
                'amount' => 120000,
                'contribution_type' =>
                    'financial',
                'status' => 'confirmed',
            ],
            [
                'event' => 'career_summit',
                'sponsor' => 'careerhub',
                'amount' => 85000,
                'contribution_type' =>
                    'financial',
                'status' => 'confirmed',
            ],
            [
                'event' => 'cultural_night',
                'sponsor' => 'mediaplus',
                'amount' => 45000,
                'contribution_type' =>
                    'media',
                'status' => 'confirmed',
            ],
        ];

        foreach ($items as $item) {
            $eventId =
                $this->ids[
                    'event_' . $item['event']
                ] ?? null;

            $sponsorId =
                $this->ids[
                    'sponsor_' . $item['sponsor']
                ] ?? null;

            if (!$eventId || !$sponsorId) {
                continue;
            }

            $this->upsertAndGetId(
                'event_sponsors',
                [
                    'event_id' => $eventId,
                    'sponsor_id' => $sponsorId,
                ],
                [
                    'amount' =>
                        $item['amount'],

                    'contribution_type' =>
                        $item['contribution_type'],

                    'agreement_date' =>
                        Carbon::now()->subDays(15),

                    'status' =>
                        $item['status'],

                    'notes' =>
                        'Demo sponsorship agreement.',
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Budgets and payments
    |--------------------------------------------------------------------------
    */

    private function seedBudgetsAndPayments(): void
    {
        $budgets = [
            [
                'key' => 'tech_venue',
                'event' => 'tech_fest',
                'category' => 'venue',
                'amount' => 70000,
            ],
            [
                'key' => 'tech_food',
                'event' => 'tech_fest',
                'category' => 'food',
                'amount' => 50000,
            ],
            [
                'key' => 'tech_equipment',
                'event' => 'tech_fest',
                'category' => 'equipment',
                'amount' => 90000,
            ],
            [
                'key' => 'career_venue',
                'event' => 'career_summit',
                'category' => 'venue',
                'amount' => 45000,
            ],
            [
                'key' => 'career_marketing',
                'event' => 'career_summit',
                'category' => 'marketing',
                'amount' => 30000,
            ],
            [
                'key' => 'culture_decoration',
                'event' => 'cultural_night',
                'category' => 'decoration',
                'amount' => 65000,
            ],
        ];

        foreach ($budgets as $budget) {
            $eventId =
                $this->ids[
                    'event_' . $budget['event']
                ] ?? null;

            if (!$eventId) {
                continue;
            }

            $this->ids[
                'budget_' . $budget['key']
            ] = $this->upsertAndGetId(
                'budgets',
                [
                    'event_id' => $eventId,

                    'category' =>
                        $budget['category'],
                ],
                [
                    'description' =>
                        ucfirst(
                            $budget['category']
                        )
                        . ' budget allocation.',

                    'allocated_amount' =>
                        $budget['amount'],

                    'status' => 'approved',
                ]
            );
        }

        $payments = [
            [
                'reference' => 'EVS-DEMO-EXP-001',
                'event' => 'tech_fest',
                'budget' => 'tech_venue',
                'payee' =>
                    'Central Auditorium Authority',
                'type' => 'expense',
                'amount' => 25000,
                'method' => 'bank',
                'status' => 'paid',
            ],
            [
                'reference' => 'EVS-DEMO-EXP-002',
                'event' => 'tech_fest',
                'budget' => 'tech_food',
                'payee' =>
                    'Campus Catering Service',
                'type' => 'expense',
                'amount' => 12000,
                'method' => 'mobile_banking',
                'status' => 'paid',
            ],
            [
                'reference' => 'EVS-DEMO-EXP-003',
                'event' => 'tech_fest',
                'budget' => 'tech_equipment',
                'payee' =>
                    'Digital Equipment House',
                'type' => 'expense',
                'amount' => 28000,
                'method' => 'bank',
                'status' => 'approved',
            ],
            [
                'reference' => 'EVS-DEMO-EXP-004',
                'event' => 'career_summit',
                'budget' => 'career_marketing',
                'payee' =>
                    'Creative Print Studio',
                'type' => 'expense',
                'amount' => 9500,
                'method' => 'cash',
                'status' => 'paid',
            ],
            [
                'reference' => 'EVS-DEMO-INC-001',
                'event' => 'tech_fest',
                'budget' => null,
                'payee' =>
                    'Event Registration Income',
                'type' => 'income',
                'amount' => 18000,
                'method' => 'mobile_banking',
                'status' => 'paid',
            ],
            [
                'reference' => 'EVS-DEMO-INC-002',
                'event' => 'career_summit',
                'budget' => null,
                'payee' =>
                    'Workshop Ticket Income',
                'type' => 'income',
                'amount' => 12500,
                'method' => 'bank',
                'status' => 'paid',
            ],
        ];

        foreach ($payments as $payment) {
            $eventId =
                $this->ids[
                    'event_' . $payment['event']
                ] ?? null;

            $budgetId = $payment['budget']
                ? (
                    $this->ids[
                        'budget_' . $payment['budget']
                    ] ?? null
                )
                : null;

            if (!$eventId) {
                continue;
            }

            $this->upsertAndGetId(
                'payments',
                [
                    'reference_number' =>
                        $payment['reference'],
                ],
                [
                    'event_id' =>
                        $eventId,

                    'budget_id' =>
                        $budgetId,

                    'payee_name' =>
                        $payment['payee'],

                    'payment_type' =>
                        $payment['type'],

                    'amount' =>
                        $payment['amount'],

                    'payment_method' =>
                        $payment['method'],

                    'payment_date' =>
                        Carbon::now()->subDays(3),

                    'status' =>
                        $payment['status'],

                    'notes' =>
                        'EventSphere demonstration record.',
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Certificates
    |--------------------------------------------------------------------------
    */

    private function seedCertificates(): void
    {
        $certificates = [
            [
                'user' => 'participant_one',
                'event' => 'workshop',
                'type' => 'participation',
                'title' =>
                    'Certificate of Participation',
                'description' =>
                    'Awarded for successful participation in the Laravel and Oracle Workshop.',
            ],
            [
                'user' => 'participant_two',
                'event' => 'workshop',
                'type' => 'participation',
                'title' =>
                    'Certificate of Participation',
                'description' =>
                    'Awarded for successful participation in the Laravel and Oracle Workshop.',
            ],
            [
                'user' => 'volunteer_one',
                'event' => 'workshop',
                'type' => 'volunteer',
                'title' =>
                    'Certificate of Volunteer Service',
                'description' =>
                    'Awarded in recognition of valuable volunteer service.',
            ],
        ];

        foreach ($certificates as $certificate) {
            $userId =
                $this->ids[
                    'user_' . $certificate['user']
                ] ?? null;

            $eventId =
                $this->ids[
                    'event_' . $certificate['event']
                ] ?? null;

            if (!$userId || !$eventId) {
                continue;
            }

            $this->upsertAndGetId(
                'certificates',
                [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'certificate_type' =>
                        $certificate['type'],
                ],
                [
                    'issued_by' =>
                        $this->ids['user_admin']
                        ?? null,

                    'certificate_number' =>
                        null,

                    'verification_code' =>
                        null,

                    'title' =>
                        $certificate['title'],

                    'description' =>
                        $certificate['description'],

                    'issued_at' =>
                        Carbon::now()->subDays(10),

                    'status' => 'issued',

                    'revoked_at' => null,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    private function seedNotifications(): void
    {
        if (!$this->tableExists(
            'notifications'
        )) {
            return;
        }

        $notifications = [
            [
                'user' => 'participant_one',
                'title' =>
                    'Registration received',
                'message' =>
                    'Your registration for EventSphere Tech Innovation Fest has been received.',
                'level' => 'success',
                'action_url' =>
                    '/participant/dashboard',
            ],
            [
                'user' => 'volunteer_one',
                'title' =>
                    'Volunteer task assigned',
                'message' =>
                    'A new volunteer responsibility has been assigned to you.',
                'level' => 'info',
                'action_url' =>
                    '/volunteer/dashboard',
            ],
            [
                'user' => 'executive',
                'title' =>
                    'Event preparation update',
                'message' =>
                    'Please review the latest event preparation status.',
                'level' => 'warning',
                'action_url' =>
                    '/executive/dashboard',
            ],
        ];

        foreach ($notifications as $notification) {
            $userId =
                $this->ids[
                    'user_' . $notification['user']
                ] ?? null;

            if (!$userId) {
                continue;
            }

            $type =
                'App\\Notifications\\DemoNotification';

            $existing = DB::table(
                'notifications'
            )
                ->where('type', $type)
                ->where(
                    'notifiable_id',
                    $userId
                )
                ->first();

            if ($existing) {
                continue;
            }

            $payload = $this->filterData(
                'notifications',
                [
                    'id' =>
                        (string) Str::uuid(),

                    'type' => $type,

                    'notifiable_type' =>
                        'App\\Models\\User',

                    'notifiable_id' =>
                        $userId,

                    'data' => json_encode(
                        [
                            'title' =>
                                $notification['title'],

                            'message' =>
                                $notification['message'],

                            'level' =>
                                $notification['level'],

                            'action_url' =>
                                $notification['action_url'],
                        ],
                        JSON_UNESCAPED_UNICODE
                    ),

                    'read_at' => null,

                    'created_at' =>
                        Carbon::now(),

                    'updated_at' =>
                        Carbon::now(),
                ]
            );

            DB::table(
                'notifications'
            )->insert($payload);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Database helpers
    |--------------------------------------------------------------------------
    */

    private function upsertAndGetId(
        string $table,
        array $unique,
        array $values
    ): ?int {
        if (!$this->tableExists($table)) {
            return null;
        }

        $filteredUnique =
            $this->filterData(
                $table,
                $unique
            );

        if (
            count($filteredUnique)
            !== count($unique)
        ) {
            throw new RuntimeException(
                "Unique columns do not match table {$table}."
            );
        }

        $filteredValues =
            $this->filterData(
                $table,
                $values
            );

        $query = DB::table($table);

        foreach (
            $filteredUnique
            as $column => $value
        ) {
            $query->where(
                $column,
                $value
            );
        }

        $existing = $query->first();

        if ($existing) {
            unset(
                $filteredValues['created_at']
            );

            if ($this->hasColumn(
                $table,
                'updated_at'
            )) {
                $filteredValues['updated_at'] =
                    Carbon::now();
            }

            if ($filteredValues !== []) {
                $updateQuery =
                    DB::table($table);

                foreach (
                    $filteredUnique
                    as $column => $value
                ) {
                    $updateQuery->where(
                        $column,
                        $value
                    );
                }

                $updateQuery->update(
                    $filteredValues
                );
            }

            return $this->extractId(
                $existing
            );
        }

        $insert = array_merge(
            $filteredUnique,
            $filteredValues
        );

        if (
            $this->hasColumn(
                $table,
                'created_at'
            )
            && !array_key_exists(
                'created_at',
                $insert
            )
        ) {
            $insert['created_at'] =
                Carbon::now();
        }

        if (
            $this->hasColumn(
                $table,
                'updated_at'
            )
            && !array_key_exists(
                'updated_at',
                $insert
            )
        ) {
            $insert['updated_at'] =
                Carbon::now();
        }

        DB::table($table)->insert(
            $insert
        );

        $lookup = DB::table($table);

        foreach (
            $filteredUnique
            as $column => $value
        ) {
            $lookup->where(
                $column,
                $value
            );
        }

        $created = $lookup->first();

        return $created
            ? $this->extractId($created)
            : null;
    }

    private function tableExists(
        string $table
    ): bool {
        return Schema::hasTable($table);
    }

    private function hasColumn(
        string $table,
        string $column
    ): bool {
        return in_array(
            strtolower($column),
            $this->columns($table),
            true
        );
    }

    private function columns(
        string $table
    ): array {
        if (
            array_key_exists(
                $table,
                $this->columnCache
            )
        ) {
            return $this->columnCache[
                $table
            ];
        }

        if (!$this->tableExists($table)) {
            return [];
        }

        $columns = array_map(
            static fn ($column) =>
                strtolower(
                    (string) $column
                ),

            Schema::getColumnListing(
                $table
            )
        );

        $this->columnCache[$table] =
            $columns;

        return $columns;
    }

    private function filterData(
        string $table,
        array $data
    ): array {
        $columns = $this->columns(
            $table
        );

        return array_filter(
            $data,
            static fn (
                mixed $value,
                string|int $key
            ): bool =>
                in_array(
                    strtolower(
                        (string) $key
                    ),
                    $columns,
                    true
                ),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function extractId(
        object $row
    ): ?int {
        foreach (
            (array) $row
            as $column => $value
        ) {
            if (
                strtolower(
                    (string) $column
                ) === 'id'
            ) {
                return $value !== null
                    ? (int) $value
                    : null;
            }
        }

        return null;
    }

    private function requireIds(
        array $keys
    ): void {
        foreach ($keys as $key) {
            if (
                empty($this->ids[$key])
            ) {
                throw new RuntimeException(
                    "Required ID {$key} is unavailable."
                );
            }
        }
    }
}