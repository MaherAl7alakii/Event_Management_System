<?php

namespace Database\Seeders;

use App\Models\EventTypeCategoryBudget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventTypeCategoryBudgetSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        EventTypeCategoryBudget::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $budgetsMap = [
            'Wedding Party' => [
                'Venue'         => 35.00,
                'Catering'      => 25.00,
                'Photographer'  => 10.00,
                'Decoration'    => 10.00,
                'Makeup Artist' => 8.00,
                'Music & DJ'    => 5.00,
                'Car Rental'    => 4.00,
                'Desserts'      => 3.00,
            ],
            'Graduation Ceremony' => [
                'Venue'        => 30.00,
                'Photographer' => 20.00,
                'Catering'     => 20.00,
                'Desserts'     => 10.00,
                'Music & DJ'   => 10.00,
                'Decoration'   => 10.00,
            ],
            'Birthday Party' => [
                'Venue'        => 25.00,
                'Catering'     => 25.00,
                'Desserts'     => 15.00,
                'Decoration'   => 15.00,
                'Photographer' => 10.00,
                'Music & DJ'   => 10.00,
            ],
            'Baby Shower' => [
                'Catering'     => 30.00,
                'Decoration'   => 25.00,
                'Desserts'     => 15.00,
                'Venue'        => 15.00,
                'Photographer' => 10.00,
                'Music & DJ'   => 5.00,
            ],
        ];


        $categoryNameMappings = [
            'Photographer'  => ['Photographer', 'مصور فوتوغرافي'],
            'Venue'         => ['Venue', 'قاعة فعاليات'],
            'Music & DJ'    => ['Music & DJ', 'موسيقى ودي جي'],
            'Decoration'    => ['Decoration', 'زينة وديكور'],
            'Desserts'      => ['Desserts', 'حلويات وقوالب كيك'],
            'Catering'      => ['Catering', 'ضيافة وبوفيه'],
            'Makeup Artist' => ['Makeup Artist', 'خبيرة تجميل'],
            'Car Rental'    => ['Car Rental', 'تأجير سيارات'],
        ];

        $eventTypeNameMappings = [
            'Wedding Party'       => ['Wedding Party', 'حفل زفاف'],
            'Graduation Ceremony' => ['Graduation Ceremony', 'حفل تخرج'],
            'Birthday Party'      => ['Birthday Party', 'عيد ميلاد'],
            'Baby Shower'         => ['Baby Shower', 'حفل استقبال مولود'],
        ];


        $eventCol = Schema::hasColumn('event_type_translations', 'name') ? 'name' : 'title';
        $catCol   = Schema::hasColumn('category_translations', 'name') ? 'name' : 'title';

        $rowsToInsert = [];

        foreach ($budgetsMap as $eventKey => $categories) {
            $searchEvents = $eventTypeNameMappings[$eventKey] ?? [$eventKey];


            $eventTypeId = DB::table('event_type_translations')
                ->whereIn($eventCol, $searchEvents)
                ->value('event_type_id');

            if (!$eventTypeId) {
                continue;
            }

            foreach ($categories as $catKey => $percentage) {
                $searchCats = $categoryNameMappings[$catKey] ?? [$catKey];

                // جلب ID الصنف بالبحث في العربية والإنجليزية
                $categoryId = DB::table('category_translations')
                    ->whereIn($catCol, $searchCats)
                    ->value('category_id');

                if ($categoryId) {
                    $rowsToInsert[] = [
                        'event_type_id'      => $eventTypeId,
                        'category_id'        => $categoryId,
                        'default_percentage' => $percentage,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
            }
        }

        if (!empty($rowsToInsert)) {
            EventTypeCategoryBudget::insert($rowsToInsert);
            $this->command->info('تم إدخال ' . count($rowsToInsert) . ' سجل بنجاح!');
        } else {
            $this->command->error('لم يتم العثور على أي تطابق في جدولي الترجمة.');
        }
    }
}
