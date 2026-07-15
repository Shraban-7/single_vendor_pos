<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = ExpenseCategory::all();

        if ($categories->isEmpty()) {
            return;
        }


        $expenseTemplates = [
            [
                'title' => 'Shop Rent',
                'category_index' => 0, // Rent & Utilities
                'amount' => 15000.00,
                'payment_method' => 'bank',
                'is_recurring' => true,
                'recurring_frequency' => \App\Enums\RecurringFrequency::MONTHLY,
                'notes' => 'Monthly shop rent payment.'
            ],
            [
                'title' => 'Electricity Bill',
                'category_index' => 0, // Rent & Utilities
                'amount' => 2800.00,
                'payment_method' => 'mobile',
                'is_recurring' => true,
                'recurring_frequency' => \App\Enums\RecurringFrequency::MONTHLY,
                'notes' => 'Electricity bill paid via bKash.'
            ],
            [
                'title' => 'Staff Salary - Kamal',
                'category_index' => 1, // Salaries & Wages
                'amount' => 8000.00,
                'payment_method' => 'cash',
                'is_recurring' => true,
                'recurring_frequency' => \App\Enums\RecurringFrequency::MONTHLY,
                'notes' => 'Monthly salary payment for assistant Kamal.'
            ],
            [
                'title' => 'Product Transportation Cost',
                'category_index' => 4, // Transportation & Conveyance
                'amount' => 1200.00,
                'payment_method' => 'cash',
                'is_recurring' => false,
                'notes' => 'Transportation cost for bringing products from Chawk Bazar.'
            ],
            [
                'title' => 'Customer Refreshments',
                'category_index' => 7, // Entertainment & Refreshment
                'amount' => 350.00,
                'payment_method' => 'cash',
                'is_recurring' => false,
                'notes' => 'Tea and snacks for customers visiting the shop.'
            ],
            [
                'title' => 'Notebooks & Pens Purchase',
                'category_index' => 6, // Office Supplies & Stationery
                'amount' => 250.00,
                'payment_method' => 'cash',
                'is_recurring' => false,
                'notes' => 'Purchase of new ledger notebooks and pens for accounting.'
            ]
        ];



        foreach ($users as $user) {
            foreach ($expenseTemplates as $index => $tmpl) {
                $category = $categories->get($tmpl['category_index']) ?? $categories->first();
                $date = now()->subDays($index * 2);

                Expense::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $tmpl['title'],
                    'amount' => $tmpl['amount'],
                    'expense_date' => $date,
                    'payment_method' => $tmpl['payment_method'],
                    'is_recurring' => $tmpl['is_recurring'],
                    'recurring_frequency' => $tmpl['recurring_frequency'] ?? null,
                    'notes' => $tmpl['notes'],
                ]);
            }
        }
    }
}
