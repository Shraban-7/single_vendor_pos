<?php

namespace App\Enums;

enum HomeSectionType: string
{
    case BANNER_SLIDER = 'banner_slider';
    case SINGLE_BANNER = 'single_banner';
    case CATEGORY_SLIDER = 'category_slider';
    case CATEGORY_GRID = 'category_grid';
    case PRODUCT_SLIDER = 'product_slider';
    case PRODUCT_GRID = 'product_grid';

    case BRAND_SLIDER = 'brand_slider';
    case BRAND_GRID = 'brand_grid';
    case CUSTOM_HTML = 'custom_html';

    public function label(): string
    {
        return match ($this) {
            self::BANNER_SLIDER => 'Banner Slider',
            self::SINGLE_BANNER => 'Single Banner',
            self::CATEGORY_SLIDER => 'Category Slider',
            self::CATEGORY_GRID => 'Category Grid',
            self::PRODUCT_SLIDER => 'Product Slider',
            self::PRODUCT_GRID => 'Product Grid',

            self::BRAND_SLIDER => 'Brand Slider',
            self::BRAND_GRID => 'Brand Grid',
            self::CUSTOM_HTML => 'Custom HTML',
        };
    }
}
