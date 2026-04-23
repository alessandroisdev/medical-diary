<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Admin
        User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@medical.diary',
            'password' => $password,
        ]);

        // Helpers faker
        $faker = \Faker\Factory::create('pt_BR');

        // Doctors
        $doctors = [];
        // Fixed Test Doctor
        $doctors[] = Doctor::create([
            'name' => 'Médico Mestre',
            'email' => 'doctor@medical.diary',
            'crm' => 'CRM-SP 99999',
            'password' => $password,
        ]);
        // Random Doctors
        for ($i=1; $i<5; $i++) {
            $doctors[] = Doctor::create([
                'name' => $faker->name,
                'email' => 'doctor'.$i.'@medical.diary',
                'crm' => 'CRM-SP ' . $faker->numberBetween(10000, 99998),
                'password' => $password,
            ]);
        }

        // Clients
        $clients = [];
        // Fixed Test Client
        $clients[] = Client::create([
            'name' => 'Paciente Fixo de Teste',
            'email' => 'client@medical.diary',
            'cpf' => '000.000.000-00',
            'phone' => '(11) 99999-9999',
            'password' => $password,
        ]);
        // Random Clients
        for ($i=1; $i<15; $i++) {
            $clients[] = Client::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'cpf' => $faker->cpf,
                'phone' => $faker->cellphoneNumber,
                'password' => $password,
            ]);
        }

        // Collaborators
        $collab = Collaborator::create([
            'name' => 'Atendente Recepção',
            'email' => 'collaborator@medical.diary',
            'password' => $password,
        ]);

        // Appointments & Transactions
        $statuses = ['scheduled', 'confirmed', 'arrived', 'in_consultation', 'finished', 'canceled', 'no_show'];
        $types = ['routine', 'first_time', 'return', 'surgery'];

        for ($i=0; $i<30; $i++) {
            $client = $faker->randomElement($clients);
            $doctor = $faker->randomElement($doctors);
            
            $app = Appointment::create([
                'client_id' => $client->id,
                'doctor_id' => $doctor->id,
                'collaborator_id' => $faker->boolean(70) ? $collab->id : null,
                'scheduled_at' => Carbon::now()->addDays($faker->numberBetween(-10, 10))->addHours($faker->numberBetween(8, 17)),
                'status' => $faker->randomElement($statuses),
                'consultation_type' => $faker->randomElement($types),
                'notes' => $faker->boolean(30) ? $faker->sentence : null,
            ]);

            // Fake Transaction for finished or confirmed ones
            if (in_array($app->status, ['confirmed', 'finished', 'in_consultation', 'arrived'])) {
                $isPaid = $faker->boolean(80);
                Transaction::create([
                    'client_id' => $client->id,
                    'appointment_id' => $app->id,
                    'amount' => $faker->randomFloat(2, 100, 500),
                    'type' => 'income',
                    'status' => $isPaid ? 'paid' : 'pending',
                    'payment_method' => $faker->randomElement(['pix', 'credit_card', 'cash']),
                    'gateway' => $faker->randomElement(['asaas', 'stripe', 'local']),
                    'gateway_id' => 'TX-MOCK-' . Str::random(8),
                    'due_date' => Carbon::now()->addDays($faker->numberBetween(0, 5)),
                    'paid_at' => $isPaid ? Carbon::now()->subDays($faker->numberBetween(0, 2)) : null,
                ]);
            }
        }
        
        // Add some random expenses
        for($i=0; $i<5; $i++) {
            Transaction::create([
                'amount' => $faker->randomFloat(2, 50, 1500),
                'type' => 'expense',
                'status' => 'paid',
                'payment_method' => 'pix',
                'gateway' => 'local',
                'paid_at' => Carbon::now()->subDays($faker->numberBetween(1, 10)),
            ]);
        }
    }
}
