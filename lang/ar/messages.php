<?php


return [
    'fetched_success' => ' تم جلب ال:resource بنجاح.',
    'created_success' => 'تم إنشاء :resource بنجاح.',
    'updated_success' => 'تم تحديث بيانات :resource بنجاح.',
    'deleted_success' => 'تم حذف :resource بنجاح.',
    'empty'           => 'لا يوجد :resource لعرضها حالياً',



    'validation' => [
        'at_least_one' => 'يجب إدخال معلومات الخدمة بلغة واحدة على الأقل (العربية أو الإنجليزية).',
        'features' => [
            'ar_required' => 'حقل قيمة الميزة بالعربية مطلوب عند إدخال اللغتين معاً.',
            'en_required' => 'حقل قيمة الميزة بالإنجليزية مطلوب عند إدخال اللغتين معاً.',
        ],
    ],



    'exceptions' => [
        'not_found'       => 'العنصر المطلوب غير موجود.',
        'unauthorized'    => 'لا تمتلك الصلاحيات الكافية للوصول إلى هذا الإجراء.',
        'unauthenticated' => 'يجب تسجيل الدخول أولاً للوصول إلى هذا المورد.',
        'access_denied'   => 'الوصول إلى هذا المورد ممنوع تماماً.',
    ],


    'resources' => [
        'service'  => 'الخدمة',
        'services' => 'خدمات',
        'search_history' => 'سجل البحث',
    ]
];
