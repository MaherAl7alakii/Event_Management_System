<?php


return [
    'fetched_success' => ' تم جلب ال:resource بنجاح.',
    'created_success' => 'تم إنشاء :resource بنجاح.',
    'updated_success' => 'تم تحديث بيانات :resource بنجاح.',
    'deleted_success' => 'تم حذف :resource بنجاح.',
    'empty'           => 'لا يوجد :resource لعرضها حالياً',

    'event_submitted_success' => 'تم إرسال الطلب بنجاح.',
    'booking_accepted_success' => 'تم قبول :resource بنجاح.',
    'booking_rejected_success' => 'تم رفض :resource بنجاح.',



    'validation' => [
        'at_least_one' => 'يجب إدخال معلومات الخدمة بلغة واحدة على الأقل (العربية أو الإنجليزية).',
        'features' => [
            'ar_required' => 'حقل قيمة الميزة بالعربية مطلوب عند إدخال اللغتين معاً.',
            'en_required' => 'حقل قيمة الميزة بالإنجليزية مطلوب عند إدخال اللغتين معاً.',
        ],

        'invalid_intervals' => 'يجب أن تكون المدة عبارة عن ساعات كاملة أو أنصاف ساعات فقط.',
    ],



    'exceptions' => [
        'not_found'       => 'العنصر المطلوب غير موجود.',
        'unauthorized'    => 'لا تمتلك الصلاحيات الكافية للوصول إلى هذا الإجراء.',
        'unauthenticated' => 'يجب تسجيل الدخول أولاً للوصول إلى هذا المورد.',
        'access_denied'   => 'الوصول إلى هذا المورد ممنوع تماماً.',
    ],


    'payment' => [
        'submission_fee_required'    => 'يرجى إكمال سداد رسوم الإرسال للبدء بتقديم طلبك للمزودين.',
        'event_submitted_success'    => 'تم إرسال طلب الحدث بنجاح إلى جميع المزودين المعنيين.',
        'deposit_intent_created'     => 'تم تجهيز جلسة دفع العربون بنجاح، يمكنك متابعة عملية الدفع.',
        'addon_intent_created'       => 'تم تجهيز جلسة دفع الخدمات الإضافية بنجاح.',
        'final_balance_intent_created'=> 'تم تجهيز جلسة دفع الرصيد المتبقي بنجاح.',
    ],


    'resources' => [
        'service'  => 'الخدمة',
        'services' => 'خدمات',
        'search_history' => 'سجل البحث',
        'event' => 'الحدث',
        'events' => 'احداث',
        'booking'  => 'الحجز',
        'bookings' => 'حجوزات',

    ]
];
