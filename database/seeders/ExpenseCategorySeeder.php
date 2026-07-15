<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rent & Utilities',
                'description' => 'Shop rent, electricity, water, internet bills, etc.',
                'icon' => 'home',
                'color' => '#FF5733',
            ],
            [
                'name' => 'Salaries & Wages',
                'description' => 'Staff salaries, daily labor wages, commissions.',
                'icon' => 'people',
                'color' => '#33FF57',
            ],
            [
                'name' => 'Purchase & Inventory Cost',
                'description' => 'Cost of buying goods/inventory from suppliers.',
                'icon' => 'shopping-cart',
                'color' => '#3357FF',
            ],
            [
                'name' => 'Marketing & Advertising',
                'description' => 'Social media promotions, banners, leaflets, local ads.',
                'icon' => 'campaign',
                'color' => '#F3FF33',
            ],
            [
                'name' => 'Transportation & Conveyance',
                'description' => 'Goods transport, passenger fare, fuel costs.',
                'icon' => 'local-shipping',
                'color' => '#33FFF3',
            ],
            [
                'name' => 'Repairs & Maintenance',
                'description' => 'Shop decoration, computer repairs, machinery maintenance.',
                'icon' => 'build',
                'color' => '#FF33F3',
            ],
            [
                'name' => 'Office Supplies & Stationery',
                'description' => 'Paper, pens, registers, bookkeeping ledgers.',
                'icon' => 'edit',
                'color' => '#FFA833',
            ],
            [
                'name' => 'Entertainment & Refreshment',
                'description' => 'Tea, snacks, meals for customers or business guests.',
                'icon' => 'restaurant',
                'color' => '#A833FF',
            ],
            [
                'name' => 'Bank Fees & Interest',
                'description' => 'Account maintenance fees, POS transaction charges, interest.',
                'icon' => 'account-balance',
                'color' => '#33FFA8',
            ],
            [
                'name' => 'Others',
                'description' => 'Miscellaneous business expenses.',
                'icon' => 'more-horiz',
                'color' => '#808080',
            ],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::create(array_merge($cat, ['is_active' => true]));
        }
    }
}
