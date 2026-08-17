<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        $getProvider = function (string $email) {
            return ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->firstOrFail();
        };

        $getCategory = function (string $name) {
            return Category::whereHas('translations', function ($query) use ($name) {
                $query->where('locale', 'en')
                    ->where('name', $name);
            })->firstOrFail();
        };

        /*
        |--------------------------------------------------------------------------
        | Service Data
        |--------------------------------------------------------------------------
        */

        $services = [

            // Ahmad - Photographer
            [
                'provider' => 'ahmad.khalil@gmail.com',
                'category' => 'Photographer',
                'city_id' => 1,
                'pricing_type' => 'fixed',
                'base_price' => 350.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 300,

                'en_title' => 'Wedding Photography Package',
                'ar_title' => 'باقة تصوير حفلات الزفاف',

                'en_description' =>
                    'Professional wedding photography covering the main moments of your special day with high-quality edited photos.',

                'ar_description' =>
                    'خدمة تصوير احترافية لحفلات الزفاف تغطي أهم لحظات يومكم المميز مع صور عالية الجودة ومعالجة احترافية.',

                'address_en' => 'Mazzeh, Damascus',
                'address_ar' => 'المزة، دمشق',
            ],

            [
                'provider' => 'ahmad.khalil@gmail.com',
                'category' => 'Photographer',
                'city_id' => 1,
                'pricing_type' => 'per_hour',
                'base_price' => 45.00,
                'min_hours' => 2,
                'max_hours' => 10,
                'max_guests' => 200,

                'en_title' => 'Event Photography',
                'ar_title' => 'تصوير الفعاليات',

                'en_description' =>
                    'Professional event photography service suitable for birthdays, corporate events and private celebrations.',

                'ar_description' =>
                    'خدمة تصوير احترافية للفعاليات والمناسبات وأعياد الميلاد والفعاليات الخاصة والشركات.',

                'address_en' => 'Mazzeh, Damascus',
                'address_ar' => 'المزة، دمشق',
            ],

            // Lina - Makeup Artist
            [
                'provider' => 'lina.hassan@gmail.com',
                'category' => 'Makeup Artist',
                'city_id' => 2,
                'pricing_type' => 'fixed',
                'base_price' => 120.00,
                'min_hours' => 2,
                'max_hours' => 4,
                'max_guests' => 1,

                'en_title' => 'Bridal Makeup',
                'ar_title' => 'مكياج العروس',

                'en_description' =>
                    'Professional bridal makeup including skin preparation, long-lasting makeup and final touch-ups.',

                'ar_description' =>
                    'مكياج احترافي للعروس يشمل تحضير البشرة ومكياج ثابت ولمسات نهائية قبل المناسبة.',

                'address_en' => 'Kafr Sousa, Damascus',
                'address_ar' => 'كفرسوسة، دمشق',
            ],

            [
                'provider' => 'lina.hassan@gmail.com',
                'category' => 'Makeup Artist',
                'city_id' => 2,
                'pricing_type' => 'per_person',
                'base_price' => 35.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 8,

                'en_title' => 'Event Makeup',
                'ar_title' => 'مكياج المناسبات',

                'en_description' =>
                    'Elegant makeup service for guests attending weddings, parties and special occasions.',

                'ar_description' =>
                    'خدمة مكياج أنيقة للضيوف في حفلات الزفاف والمناسبات والحفلات الخاصة.',

                'address_en' => 'Kafr Sousa, Damascus',
                'address_ar' => 'كفرسوسة، دمشق',
            ],
        

[
    'provider' => 'lina.hassan@gmail.com',
    'category' => 'Makeup Artist',
    'city_id' => 2,
    'pricing_type' => 'per_hour',
    'base_price' => 40.00,
    'min_hours' => 2,
    'max_hours' => 6,
    'max_guests' => 4,

    'en_title' => 'Professional Event Makeup',
    'ar_title' => 'مكياج احترافي للمناسبات',

    'en_description' =>
        'Professional makeup service for weddings, engagements, birthdays and private events with personalized looks.',

    'ar_description' =>
        'خدمة مكياج احترافية لحفلات الزفاف والخطوبة وأعياد الميلاد والمناسبات الخاصة مع إطلالة مخصصة لكل عميلة.',

    'address_en' => 'Kafr Sousa, Damascus',
    'address_ar' => 'كفرسوسة، دمشق',
],

[
    'provider' => 'lina.hassan@gmail.com',
    'category' => 'Photographer',
    'city_id' => 2,
    'pricing_type' => 'fixed',
    'base_price' => 250.00,
    'min_hours' => null,
    'max_hours' => null,
    'max_guests' => 150,

    'en_title' => 'Bridal Photography Package',
    'ar_title' => 'باقة تصوير العروس',

    'en_description' =>
        'Elegant bridal photography package capturing preparation, makeup details and special moments before the wedding.',

    'ar_description' =>
        'باقة تصوير أنيقة للعروس تشمل تصوير التحضيرات وتفاصيل المكياج واللحظات المميزة قبل حفل الزفاف.',

    'address_en' => 'Kafr Sousa, Damascus',
    'address_ar' => 'كفرسوسة، دمشق',
],

[
    'provider' => 'lina.hassan@gmail.com',
    'category' => 'Photographer',
    'city_id' => 2,
    'pricing_type' => 'per_hour',
    'base_price' => 45.00,
    'min_hours' => 2,
    'max_hours' => 8,
    'max_guests' => 200,

    'en_title' => 'Photography',
    'ar_title' => 'تصوير ',

    'en_description' =>
        'Professional photography service for birthdays, engagements and private celebrations with edited digital photos.',

    'ar_description' =>
        'خدمة تصوير احترافية لأعياد الميلاد والخطوبة والاحتفالات الخاصة مع تسليم الصور الرقمية بعد المعالجة.',

    'address_en' => 'Kafr Sousa, Damascus',
    'address_ar' => 'كفرسوسة، دمشق',
],

[
    'provider' => 'lina.hassan@gmail.com',
    'category' => 'Desserts',
    'city_id' => 2,
    'pricing_type' => 'fixed',
    'base_price' => 150.00,
    'min_hours' => null,
    'max_hours' => null,
    'max_guests' => 80,

    'en_title' => 'Custom Celebration Cake',
    'ar_title' => 'كيكة مناسبات مخصصة',

    'en_description' =>
        'Custom-designed celebration cake prepared according to the event theme, preferred style and number of guests.',

    'ar_description' =>
        'كيكة مناسبات مصممة حسب الطلب بما يتناسب مع ثيم الحفل والتصميم المطلوب وعدد الضيوف.',

    'address_en' => 'Kafr Sousa, Damascus',
    'address_ar' => 'كفرسوسة، دمشق',
],

[
    'provider' => 'lina.hassan@gmail.com',
    'category' => 'Desserts',
    'city_id' => 2,
    'pricing_type' => 'per_person',
    'base_price' => 7.50,
    'min_hours' => null,
    'max_hours' => null,
    'max_guests' => 150,

    'en_title' => 'Dessert Boxes',
    'ar_title' => 'علب حلويات للمناسبات',

    'en_description' =>
        'Beautifully prepared dessert boxes with assorted sweets suitable for weddings, birthdays and special occasions.',

    'ar_description' =>
        'علب حلويات أنيقة ومتنوعة مناسبة لحفلات الزفاف وأعياد الميلاد والمناسبات الخاصة.',

    'address_en' => 'Kafr Sousa, Damascus',
    'address_ar' => 'كفرسوسة، دمشق',
],

            // Omar - Decoration
            [
                'provider' => 'omar.saleh@gmail.com',
                'category' => 'Decoration',
                'city_id' => 3,
                'pricing_type' => 'fixed',
                'base_price' => 500.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 300,

                'en_title' => 'Wedding Decoration',
                'ar_title' => 'ديكور حفلات الزفاف',

                'en_description' =>
                    'Complete wedding decoration including entrance design, tables, lighting and coordinated decorative elements.',

                'ar_description' =>
                    'ديكور متكامل لحفلات الزفاف يشمل المدخل والطاولات والإضاءة والعناصر الزخرفية المتناسقة.',

                'address_en' => 'Barzeh, Damascus',
                'address_ar' => 'برزة، دمشق',
            ],

            [
                'provider' => 'omar.saleh@gmail.com',
                'category' => 'Decoration',
                'city_id' => 3,
                'pricing_type' => 'per_hour',
                'base_price' => 50.00,
                'min_hours' => 3,
                'max_hours' => 10,
                'max_guests' => 250,

                'en_title' => 'Floral Decoration Service',
                'ar_title' => 'خدمة تنسيق الزهور والديكور',

                'en_description' =>
                    'Professional floral decoration and event styling for weddings and private celebrations.',

                'ar_description' =>
                    'خدمة احترافية لتنسيق الزهور والديكور للمناسبات وحفلات الزفاف والاحتفالات الخاصة.',

                'address_en' => 'Barzeh, Damascus',
                'address_ar' => 'برزة، دمشق',
            ],

            // Sara - Venue
            [
                'provider' => 'sara.ahmad@gmail.com',
                'category' => 'Venue',
                'city_id' => 4,
                'pricing_type' => 'per_person',
                'base_price' => 12.00,
                'min_hours' => 4,
                'max_hours' => 8,
                'max_guests' => 250,

                'en_title' => 'Wedding Hall',
                'ar_title' => 'قاعة حفلات الزفاف',

                'en_description' =>
                    'Elegant event venue suitable for weddings and large celebrations with seating and basic event facilities.',

                'ar_description' =>
                    'قاعة مناسبات أنيقة مناسبة لحفلات الزفاف والاحتفالات الكبيرة مع تجهيزات أساسية للفعاليات.',

                'address_en' => 'Barzeh, Damascus',
                'address_ar' => 'برزة، دمشق',
            ],

            [
                'provider' => 'sara.ahmad@gmail.com',
                'category' => 'Venue',
                'city_id' => 4,
                'pricing_type' => 'fixed',
                'base_price' => 700.00,
                'min_hours' => 5,
                'max_hours' => 8,
                'max_guests' => 180,

                'en_title' => 'Private Event Hall',
                'ar_title' => 'قاعة مناسبات خاصة',

                'en_description' =>
                    'Private event space suitable for birthdays, engagement parties and intimate celebrations.',

                'ar_description' =>
                    'مساحة خاصة للمناسبات مناسبة لأعياد الميلاد وحفلات الخطوبة والاحتفالات الصغيرة.',

                'address_en' => 'Barzeh, Damascus',
                'address_ar' => 'برزة، دمشق',
            ],

            // Khaled - Music & DJ
            [
                'provider' => 'khaled.nasser@gmail.com',
                'category' => 'Music & DJ',
                'city_id' => 12,
                'pricing_type' => 'per_hour',
                'base_price' => 60.00,
                'min_hours' => 3,
                'max_hours' => 8,
                'max_guests' => 400,

                'en_title' => 'DJ and Sound System',
                'ar_title' => 'دي جي ونظام صوتي',

                'en_description' =>
                    'Professional DJ performance with high-quality sound equipment for weddings and private events.',

                'ar_description' =>
                    'خدمة دي جي احترافية مع نظام صوتي عالي الجودة لحفلات الزفاف والمناسبات الخاصة.',

                'address_en' => 'Aleppo',
                'address_ar' => 'حلب',
            ],

            [
                'provider' => 'khaled.nasser@gmail.com',
                'category' => 'Music & DJ',
                'city_id' => 12,
                'pricing_type' => 'fixed',
                'base_price' => 450.00,
                'min_hours' => 4,
                'max_hours' => 7,
                'max_guests' => 350,

                'en_title' => 'Wedding DJ Package',
                'ar_title' => 'باقة دي جي لحفلات الزفاف',

                'en_description' =>
                    'Complete wedding DJ package including music selection, professional equipment and event coordination.',

                'ar_description' =>
                    'باقة متكاملة للزفاف تشمل اختيار الموسيقى والمعدات الاحترافية وتنسيق الموسيقى مع مجريات الحفل.',

                'address_en' => 'Aleppo',
                'address_ar' => 'حلب',
            ],

            // Maya - Desserts
            [
                'provider' => 'maya.ibrahim@gmail.com',
                'category' => 'Desserts',
                'city_id' => 5,
                'pricing_type' => 'fixed',
                'base_price' => 180.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 100,

                'en_title' => 'Custom Wedding Cake',
                'ar_title' => 'كيكة زفاف مخصصة',

                'en_description' =>
                    'Custom-designed wedding cake prepared according to the event theme and guest requirements.',

                'ar_description' =>
                    'كيكة زفاف مصممة حسب الطلب بما يتناسب مع ثيم الحفل وعدد الضيوف.',

                'address_en' => 'Douma, Rif Damascus',
                'address_ar' => 'دوما، ريف دمشق',
            ],

            [
                'provider' => 'maya.ibrahim@gmail.com',
                'category' => 'Desserts',
                'city_id' => 5,
                'pricing_type' => 'per_person',
                'base_price' => 8.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 200,

                'en_title' => 'Dessert Table',
                'ar_title' => 'طاولة حلويات',

                'en_description' =>
                    'Elegant dessert table with assorted sweets suitable for weddings and private celebrations.',

                'ar_description' =>
                    'طاولة حلويات متنوعة وأنيقة مناسبة لحفلات الزفاف والمناسبات الخاصة.',

                'address_en' => 'Douma, Rif Damascus',
                'address_ar' => 'دوما، ريف دمشق',
            ],

            // Yazan - Catering
            [
                'provider' => 'yazan.mahmoud@gmail.com',
                'category' => 'Catering',
                'city_id' => 19,
                'pricing_type' => 'per_person',
                'base_price' => 15.00,
                'min_hours' => 2,
                'max_hours' => 5,
                'max_guests' => 300,

                'en_title' => 'Wedding Buffet',
                'ar_title' => 'بوفيه حفلات الزفاف',

                'en_description' =>
                    'Full buffet catering service with a selection of main dishes, salads, appetizers and desserts.',

                'ar_description' =>
                    'خدمة بوفيه متكاملة تشمل مجموعة من الأطباق الرئيسية والسلطات والمقبلات والحلويات.',

                'address_en' => 'Al-Hader, Hama',
                'address_ar' => 'الحاضر، حماة',
            ],

            [
                'provider' => 'yazan.mahmoud@gmail.com',
                'category' => 'Catering',
                'city_id' => 19,
                'pricing_type' => 'per_hour_per_person',
                'base_price' => 6.50,
                'min_hours' => 2,
                'max_hours' => 6,
                'max_guests' => 250,

                'en_title' => 'Event Catering Service',
                'ar_title' => 'خدمة ضيافة للفعاليات',

                'en_description' =>
                    'Flexible catering service priced per person and hour for private and corporate events.',

                'ar_description' =>
                    'خدمة ضيافة مرنة تحسب حسب عدد الأشخاص وعدد ساعات الفعالية للمناسبات الخاصة وفعاليات الشركات.',

                'address_en' => 'Al-Hader, Hama',
                'address_ar' => 'الحاضر، حماة',
            ],

            // Rana - Car Rental
            [
                'provider' => 'rana.samir@gmail.com',
                'category' => 'Car Rental',
                'city_id' => 36,
                'pricing_type' => 'per_hour',
                'base_price' => 35.00,
                'min_hours' => 3,
                'max_hours' => 12,
                'max_guests' => 4,

                'en_title' => 'Luxury Car Rental',
                'ar_title' => 'تأجير سيارة فاخرة',

                'en_description' =>
                    'Luxury vehicle rental service for weddings, celebrations and special occasions.',

                'ar_description' =>
                    'خدمة تأجير سيارات فاخرة لحفلات الزفاف والمناسبات والاحتفالات الخاصة.',

                'address_en' => 'Latakia',
                'address_ar' => 'اللاذقية',
            ],

            [
                'provider' => 'rana.samir@gmail.com',
                'category' => 'Car Rental',
                'city_id' => 36,
                'pricing_type' => 'fixed',
                'base_price' => 250.00,
                'min_hours' => 5,
                'max_hours' => 10,
                'max_guests' => 4,

                'en_title' => 'Wedding Car Service',
                'ar_title' => 'خدمة سيارة للزفاف',

                'en_description' =>
                    'Decorated wedding car with professional driver for the wedding ceremony and reception.',

                'ar_description' =>
                    'سيارة زفاف مزينة مع سائق محترف لخدمة مراسم الزفاف والاستقبال.',

                'address_en' => 'Latakia',
                'address_ar' => 'اللاذقية',
            ],

            // Tarek - Photographer + DJ
            [
                'provider' => 'tarek.ibrahim@gmail.com',
                'category' => 'Photographer',
                'city_id' => 39,
                'pricing_type' => 'per_hour',
                'base_price' => 50.00,
                'min_hours' => 2,
                'max_hours' => 10,
                'max_guests' => 300,

                'en_title' => 'Wedding Photography',
                'ar_title' => 'تصوير حفلات الزفاف',

                'en_description' =>
                    'Professional wedding photography service with edited digital photos.',

                'ar_description' =>
                    'خدمة تصوير احترافية لحفلات الزفاف مع تسليم الصور الرقمية بعد المعالجة.',

                'address_en' => 'Tartus',
                'address_ar' => 'طرطوس',
            ],

            [
                'provider' => 'tarek.ibrahim@gmail.com',
                'category' => 'Music & DJ',
                'city_id' => 39,
                'pricing_type' => 'per_hour_per_person',
                'base_price' => 2.50,
                'min_hours' => 3,
                'max_hours' => 8,
                'max_guests' => 300,

                'en_title' => 'DJ and Event Sound',
                'ar_title' => 'دي جي وصوت للفعاليات',

                'en_description' =>
                    'DJ and professional sound service priced according to event duration and number of guests.',

                'ar_description' =>
                    'خدمة دي جي ونظام صوتي احترافي يتم تسعيرها حسب مدة الفعالية وعدد الضيوف.',

                'address_en' => 'Tartus',
                'address_ar' => 'طرطوس',
            ],

            // Nour - Decoration
            [
                'provider' => 'nour.ali@gmail.com',
                'category' => 'Decoration',
                'city_id' => 42,
                'pricing_type' => 'fixed',
                'base_price' => 300.00,
                'min_hours' => null,
                'max_hours' => null,
                'max_guests' => 150,

                'en_title' => 'Event Decoration',
                'ar_title' => 'ديكور المناسبات',

                'en_description' =>
                    'Customized event decoration with themed tables, entrance decoration and floral arrangements.',

                'ar_description' =>
                    'ديكور مخصص للمناسبات يشمل تنسيق الطاولات والمدخل والزهور حسب ثيم الحفل.',

                'address_en' => 'As-Suwayda',
                'address_ar' => 'السويداء',
            ],

            [
                'provider' => 'nour.ali@gmail.com',
                'category' => 'Venue',
                'city_id' => 42,
                'pricing_type' => 'per_person',
                'base_price' => 10.00,
                'min_hours' => 4,
                'max_hours' => 8,
                'max_guests' => 120,

                'en_title' => 'Outdoor Event Venue',
                'ar_title' => 'مكان خارجي للمناسبات',

                'en_description' =>
                    'Outdoor event space suitable for intimate weddings, birthdays and private celebrations.',

                'ar_description' =>
                    'مساحة خارجية للمناسبات مناسبة لحفلات الزفاف الصغيرة وأعياد الميلاد والاحتفالات الخاصة.',

                'address_en' => 'As-Suwayda',
                'address_ar' => 'السويداء',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Services
        |--------------------------------------------------------------------------
        */

        foreach ($services as $data) {

            $provider = $getProvider($data['provider']);
            $category = $getCategory($data['category']);

            // تأكيد مهم:
            // المزود لازم يكون فعلاً مرتبط بهذا القسم
            $belongsToCategory = DB::table('service_provider_categories')
                ->where('service_provider_id', $provider->id)
                ->where('category_id', $category->id)
                ->exists();

            if (!$belongsToCategory) {
                throw new \Exception(
                    "Provider {$data['provider']} is not assigned to category {$data['category']}"
                );
            }

            $service = Service::updateOrCreate(
                [
                    'provider_id' => $provider->user_id,
                    'category_id' => $category->id,
                    'city_id' => $data['city_id'],
                    'pricing_type' => $data['pricing_type'],
                    'base_price' => $data['base_price'],
                ],
                [
                    'is_active' => true,
                    'min_hours' => $data['min_hours'],
                    'max_hours' => $data['max_hours'],
                    'max_guests' => $data['max_guests'],
                    'rating' => 0,
                    'rating_count' => 0,
                    'reviews_count' => 0,
                    'bookings_count' => 0,
                ]
            );

            DB::table('service_translations')->updateOrInsert(
                [
                    'service_id' => $service->id,
                    'locale' => 'en',
                ],
                [
                    'title' => $data['en_title'],
                    'description' => $data['en_description'],
                    'address' => $data['address_en'],
                ]
            );

            DB::table('service_translations')->updateOrInsert(
                [
                    'service_id' => $service->id,
                    'locale' => 'ar',
                ],
                [
                    'title' => $data['ar_title'],
                    'description' => $data['ar_description'],
                    'address' => $data['address_ar'],
                ]
            );
        }
    }
}