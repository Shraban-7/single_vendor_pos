<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Black', 'code' => 'black', 'hex_code' => '#000000'],
            ['name' => 'White', 'code' => 'white', 'hex_code' => '#FFFFFF'],
            ['name' => 'Navy Blue', 'code' => 'navy', 'hex_code' => '#000080'],
            ['name' => 'Royal Blue', 'code' => 'royal-blue', 'hex_code' => '#4169E1'],
            ['name' => 'Sky Blue', 'code' => 'sky-blue', 'hex_code' => '#87CEEB'],
            ['name' => 'Red', 'code' => 'red', 'hex_code' => '#FF0000'],
            ['name' => 'Maroon', 'code' => 'maroon', 'hex_code' => '#800000'],
            ['name' => 'Green', 'code' => 'green', 'hex_code' => '#008000'],
            ['name' => 'Olive', 'code' => 'olive', 'hex_code' => '#808000'],
            ['name' => 'Yellow', 'code' => 'yellow', 'hex_code' => '#FFFF00'],
            ['name' => 'Orange', 'code' => 'orange', 'hex_code' => '#FFA500'],
            ['name' => 'Pink', 'code' => 'pink', 'hex_code' => '#FFC0CB'],
            ['name' => 'Purple', 'code' => 'purple', 'hex_code' => '#800080'],
            ['name' => 'Brown', 'code' => 'brown', 'hex_code' => '#8B4513'],
            ['name' => 'Beige', 'code' => 'beige', 'hex_code' => '#F5F5DC'],
            ['name' => 'Gray', 'code' => 'gray', 'hex_code' => '#808080'],
            ['name' => 'Charcoal', 'code' => 'charcoal', 'hex_code' => '#36454F'],
            ['name' => 'Cream', 'code' => 'cream', 'hex_code' => '#FFFDD0'],
            ['name' => 'Off White', 'code' => 'off-white', 'hex_code' => '#FAF9F6'],
            ['name' => 'Teal', 'code' => 'teal', 'hex_code' => '#008080'],
            ['name' => 'Coral', 'code' => 'coral', 'hex_code' => '#FF7F50'],
            ['name' => 'Burgundy', 'code' => 'burgundy', 'hex_code' => '#722F37'],
            ['name' => 'Mustard', 'code' => 'mustard', 'hex_code' => '#FFDB58'],
            ['name' => 'Peach', 'code' => 'peach', 'hex_code' => '#FFCBA4'],
            ['name' => 'Lavender', 'code' => 'lavender', 'hex_code' => '#E6E6FA'],
            ['name' => 'Mint', 'code' => 'mint', 'hex_code' => '#98FF98'],
            ['name' => 'Rust', 'code' => 'rust', 'hex_code' => '#B7410E'],
            ['name' => 'Gold', 'code' => 'gold', 'hex_code' => '#FFD700'],
            ['name' => 'Silver', 'code' => 'silver', 'hex_code' => '#C0C0C0'],
            ['name' => 'Multi', 'code' => 'multi', 'hex_code' => null],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['code' => $color['code']],
                $color
            );
        }
    }
}
