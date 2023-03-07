<?php

namespace Kiwilan\Steward\Services\Factory\Text;

class MeaningProvider
{
    /**
     * @param  string  $type `category` | `tag`
     */
    public static function find(string $type = 'category'): string
    {
        $words = [];

        if ($type === 'category') {
            $words = self::categories();
        }

        if ($type === 'tag') {
            $words = self::tags();
        }

        $random = $words[array_rand($words)];

        return TextProvider::capitalizeFirst($random);
    }

    /**
     * @return string[]
     */
    public static function categories()
    {
        return [
            'Electronics',
            'Computers',
            'Smart Home',
            'Arts & Crafts',
            'Automotive',
            'Baby',
            'Beauty and personal care',
            'Fashion',
            'Health and Household',
            'Home and Kitchen',
            'Industrial and Scientific',
            'Luggage',
            'Movies & Television',
            'Pet supplies',
            'Software',
            'Sports and Outdoors',
            'Tools & Home Improvement',
            'Toys and Games',
            'Video Games',
            'Sciences',
            'Technology',
            'Society',
            'Pop culture',
            'Vehicles',

            'discovers',
            'environment',
            'space',
            'health',
            'helmet',
            'game console',
            'open source',
            'headset',
            'information technology',
            'smart home',
            'smartphone',
            'virtual reality',
            'TV',
            'web',
            'education',
            'entertainment',
            'finance',
            'government',
            'healthcare',
            'web culture',
            'toys',
            'sports',
            'music',
            'movies',
            'books',
            'food',
            'travel',
            'fashion',
            'art',
            'design',
            'car',
            'bike',
            'plane',
            'train',
            'boat',
            'spaceship',
        ];
    }

    /**
     * @return string[]
     */
    public static function tags(): array
    {
        return [
            'Accessories',
            'Camera & Photo',
            'Car',
            'Cell Phones',
            'Computers',
            'GPS',
            'Headphones',
            'Home Audio',
            'Office Electronics',
            'Portable Audio & Video',
            'Security & Surveillance',
            'Service Plans',
            'Television & Video',
            'Video Game Consoles',
            'Video Projectors',
            'Wearable Technology',
            'eBook Readers',
            //
            'Computer Accessories',
            'Computer Components',
            'Computers & Tablets',
            'Data Storage',
            'External Components',
            'Laptop Accessories',
            'Monitors',
            'Networking Products',
            'Power Strips',
            'Printers',
            'Scanners',
            'Servers',
            'Tablet Accessories',
            'Tablet Replacement Parts',
            'Warranties',
            //
            'Smart Home Lighting',
            'Smart Locks',
            'Security Cameras',
            'Plugs and Outlets',
            'New Smart Devices',
            'Heating and Cooling',
            'Detectors and Sensors',
            'Home Entertainment',
            'Pet',
            'Voice Assistants',
            'Kitchen',
            'Vacuums',
            'Garden',
            'Networking',
            //
            'Painting & Drawing',
            'Jewelry',
            'Crafting',
            'Fabric',
            'Knitting',
            'Needlework',
            'Printmaking',
            'Scrapbooking',
            'Sewing',
            'Gift Wrapping',
            'Storage',
            'Party Decorations',
            //
            'Car Care',
            'Car Electronics',
            'Exterior',
            'Interior',
            'Lights',
            'Motorcycle',
            'Oils',
            'Paint',
            'Performance',
            'Replacement',
            'Tires',
            'Wheels',
            'Tools',
            'Heavy Duty',
            'Commercial Vehicle',
            //
            'Activity',
            'Entertainment',
            'Apparel',
            'Toddler Toys',
            'Baby Care',
            'Baby Stationery',
            'Car Seats',
            'Gifts',
            'Nursery',
            'Maternity',
            'Safety',
            'Strollers',
            'Travel Gear',
            //
            'Makeup',
            'Skin Care',
            'Hair Care',
            'Fragrance',
            'Hand Care',
            'Nail Care',
            'Tools',
            'Shave',
            'Personal Care',
            //
            'Clothing',
            'Shoes',
            'Jewelry',
            'Watches',
            'Handbags',
            //
            'Kitchen',
            'Bedding',
            'Bath',
            'Furniture',
            'Wall Art',
            'Ceiling Fans',
            'Party Supplies',
            'Heating',
            'Cooling',
            'Irons & Steamers',
            'Vacuums & Floor Care',
            'Storage & Organization',
            'Cleaning Supplies',
            //
            'Abrasive & Finishing',
            'Additive',
            'Commercial Door',
            'Cutting Tools',
            'Fasteners',
            'Filtration',
            'Food Service',
            'Hydraulics',
            'Pneumatics',
            'Plumbing',
            'Electrical',
            'Hardware',
            'Power',
            'Hand Tools',
            'Lab',
            'Material Handling',
            'Occupational Health',
            'Packaging',
            'Shipping',
            'Power Transmission',
            'Professional Medical',
            'Raw Materials',
            'Retail Store Fixtures',
            'Robotics',
            'Science Education',
            'Adhesives',
            'Measure',
            //
            'Carry-ons',
            'Backpacks',
            'Garment bags',
            'Travel Totes',
            'Luggage Sets',
            'Laptop Bags',
            'Suitcases',
            'Kids Luggage',
            'Umbrellas',
            'Duffles',
            'Travel',
            //
            'Movies',
            'TV Shows',
            'Blu-ray',
            'Best Sellers',
            'Pre-orders',
            //
            'Dogs',
            'Cats',
            'Fish',
            'Birds',
            'Horses',
            'Reptiles',
            'Amphibians',
            //
            'Accounting',
            'Finance',
            'Security',
            'Business',
            'Design',
            'Education',
            'Games',
            'Lifestyle',
            'Music',
            'Networking',
            'Operating Systems',
            'Photography',
            'Programming',
            'Tax Preparation',
            'Utilities',
            'Video',
            //
            'Sports',
            'Outdoor Recreation',
            'Fitness',
            //
            'Tools',
            'Appliances',
            'Building Supplies',
            'Electrical',
            'Hardware',
            'Kitchen',
            'Light Bulbs',
            'Lighting',
            'Ceiling Fans',
            'Measuring',
            'Painting Supplies',
            'Wall Treatments',
            'Power',
            'Hand Tools',
            'Rough Plumbing',
            'Security',
            'Home Organization',
            'Welding',
            //
            'Action Figures',
            'Crafts',
            'Building Toys',
            'Games',
            'Hobbies',
            'Puzzles',
            'Stuffed Animals',
            'Tricycles',
            'Scooters',
            'Video Games',
            //
            'PlayStation 4',
            'PlayStation 3',
            'Xbox One',
            'Xbox 360',
            'Nintendo Switch',
            'Wii U',
            'Wii',
            'PC',
            'Mac',
            'Nintendo 3DS & 2DS',
            'Nintendo DS',
            'PlayStation Vita',
            'Sony PSP',
            'Retro Gaming',
        ];
    }
}
