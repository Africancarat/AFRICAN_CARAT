<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (EmailTemplate::where('type', 'New Order Admin')->count() == 0) {
            $emailTemplete = new EmailTemplate();
            $emailTemplete->type = 'New Order Admin';
            $emailTemplete->subject = 'New Order';
            $emailTemplete->body = '<p>You Got a order, Transaction number {transaction_number}</p>';
            $emailTemplete->save();
        }
    }
}
