<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [

            'Wedding Photography Package' => [
                ['en' => 'Edited high-resolution photos', 'ar' => 'صور عالية الدقة بعد المعالجة'],
                ['en' => 'Professional photographer', 'ar' => 'مصور فوتوغرافي محترف'],
                ['en' => 'Wedding day coverage', 'ar' => 'تغطية كاملة ليوم الزفاف'],
            ],

            'Event Photography' => [
                ['en' => 'Professional camera equipment', 'ar' => 'معدات تصوير احترافية'],
                ['en' => 'Edited digital photos', 'ar' => 'صور رقمية معالجة'],
                ['en' => 'Flexible hourly booking', 'ar' => 'حجز مرن بالساعة'],
            ],

            'Bridal Makeup' => [
                ['en' => 'Skin preparation', 'ar' => 'تحضير البشرة'],
                ['en' => 'Long-lasting makeup', 'ar' => 'مكياج ثابت'],
                ['en' => 'Final touch-ups', 'ar' => 'لمسات نهائية'],
            ],

            'Event Makeup' => [
                ['en' => 'Professional makeup', 'ar' => 'مكياج احترافي'],
                ['en' => 'Suitable for guests', 'ar' => 'مناسب للضيوف'],
                ['en' => 'Multiple makeup styles', 'ar' => 'عدة أساليب للمكياج'],
            ],

            'Wedding Decoration' => [
                ['en' => 'Entrance decoration', 'ar' => 'ديكور المدخل'],
                ['en' => 'Table decoration', 'ar' => 'تنسيق الطاولات'],
                ['en' => 'Lighting decoration', 'ar' => 'تنسيق الإضاءة'],
            ],

            'Floral Decoration Service' => [
                ['en' => 'Fresh flower arrangements', 'ar' => 'تنسيق زهور طبيعية'],
                ['en' => 'Event styling', 'ar' => 'تنسيق ديكور المناسبة'],
                ['en' => 'Customized themes', 'ar' => 'ثيمات مخصصة'],
            ],

            'Wedding Hall' => [
                ['en' => 'Guest seating', 'ar' => 'تجهيز أماكن جلوس الضيوف'],
                ['en' => 'Event lighting', 'ar' => 'إضاءة للمناسبة'],
                ['en' => 'Private event space', 'ar' => 'مساحة خاصة للمناسبة'],
            ],

            'Private Event Hall' => [
                ['en' => 'Private space', 'ar' => 'مساحة خاصة'],
                ['en' => 'Event seating', 'ar' => 'أماكن جلوس'],
                ['en' => 'Basic event facilities', 'ar' => 'تجهيزات أساسية للمناسبة'],
            ],

            'DJ and Sound System' => [
                ['en' => 'Professional DJ', 'ar' => 'دي جي محترف'],
                ['en' => 'Professional sound system', 'ar' => 'نظام صوتي احترافي'],
                ['en' => 'Music selection', 'ar' => 'اختيار الموسيقى'],
            ],

            'Wedding DJ Package' => [
                ['en' => 'DJ performance', 'ar' => 'أداء دي جي'],
                ['en' => 'Sound equipment', 'ar' => 'معدات صوت'],
                ['en' => 'Wedding playlist', 'ar' => 'قائمة موسيقى للزفاف'],
            ],

            'Custom Wedding Cake' => [
                ['en' => 'Custom cake design', 'ar' => 'تصميم مخصص للكيكة'],
                ['en' => 'Fresh ingredients', 'ar' => 'مكونات طازجة'],
                ['en' => 'Wedding theme matching', 'ar' => 'متناسقة مع ثيم الزفاف'],
            ],

            'Dessert Table' => [
                ['en' => 'Assorted desserts', 'ar' => 'حلويات متنوعة'],
                ['en' => 'Elegant presentation', 'ar' => 'تقديم أنيق'],
                ['en' => 'Custom decoration', 'ar' => 'ديكور مخصص'],
            ],

            'Wedding Buffet' => [
                ['en' => 'Main dishes', 'ar' => 'أطباق رئيسية'],
                ['en' => 'Salads and appetizers', 'ar' => 'سلطات ومقبلات'],
                ['en' => 'Dessert selection', 'ar' => 'مجموعة حلويات'],
            ],

            'Event Catering Service' => [
                ['en' => 'Flexible menu', 'ar' => 'قائمة طعام مرنة'],
                ['en' => 'Professional service', 'ar' => 'خدمة احترافية'],
                ['en' => 'Guest-based pricing', 'ar' => 'تسعير حسب عدد الضيوف'],
            ],

            'Luxury Car Rental' => [
                ['en' => 'Luxury vehicle', 'ar' => 'سيارة فاخرة'],
                ['en' => 'Professional driver', 'ar' => 'سائق محترف'],
                ['en' => 'Flexible hourly rental', 'ar' => 'تأجير مرن بالساعة'],
            ],

            'Wedding Car Service' => [
                ['en' => 'Decorated wedding car', 'ar' => 'سيارة زفاف مزينة'],
                ['en' => 'Professional driver', 'ar' => 'سائق محترف'],
                ['en' => 'Wedding day service', 'ar' => 'خدمة طوال يوم الزفاف'],
            ],

            'DJ and Event Sound' => [
                ['en' => 'DJ service', 'ar' => 'خدمة دي جي'],
                ['en' => 'Professional speakers', 'ar' => 'مكبرات صوت احترافية'],
                ['en' => 'Event music coordination', 'ar' => 'تنسيق موسيقى المناسبة'],
            ],

            'Event Decoration' => [
                ['en' => 'Customized decoration', 'ar' => 'ديكور مخصص'],
                ['en' => 'Floral arrangements', 'ar' => 'تنسيق الزهور'],
                ['en' => 'Themed event design', 'ar' => 'تصميم حسب ثيم المناسبة'],
            ],

            'Outdoor Event Venue' => [
                ['en' => 'Outdoor seating', 'ar' => 'أماكن جلوس خارجية'],
                ['en' => 'Private event space', 'ar' => 'مساحة خاصة للمناسبة'],
                ['en' => 'Suitable for small events', 'ar' => 'مناسبة للفعاليات الصغيرة'],
            ],
        ];

        foreach ($features as $title => $items) {

            $service = Service::whereHas('translations', function ($query) use ($title) {
                $query->where('locale', 'en')
                    ->where('title', $title);
            })->first();

            if (!$service) {
                continue;
            }

            $service->features()->delete();

            foreach ($items as $item) {

                $featureId = DB::table('features')->insertGetId([
                    'service_id' => $service->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('feature_translations')->insert([
                    [
                        'feature_id' => $featureId,
                        'locale' => 'en',
                        'value' => $item['en'],
                    ],
                    [
                        'feature_id' => $featureId,
                        'locale' => 'ar',
                        'value' => $item['ar'],
                    ],
                ]);
            }
        }
    }
}