<?php



return [
    'fetched_success' => ':resource fetched successfully.',
    'created_success' => ':resource created successfully.',
    'updated_success' => ':resource updated successfully.',
    'deleted_success' => ':resource deleted successfully.',
    'empty'           => 'No :resource found to display',

    'event_submitted_success' => 'The event has been submitted successfully.',
    'booking_accepted_success' => ':resource accepted successfully.',
    'booking_rejected_success' => ':resource rejected successfully.',


    'cannot_respond_to_booking' => 'Cannot respond to this booking in its current status.',
    'booking_accepted'          => 'Booking accepted successfully, awaiting provider deposit payment.',
    'booking_rejected'          => 'Booking request has been rejected successfully.',


    'validation' => [
        'at_least_one' => 'You must provide service details in at least one language (Arabic or English).',
        'features' => [
            'ar_required' => 'The Arabic value is required when both languages are provided.',
            'en_required' => 'The English value is required when both languages are provided.',
        ],

        'invalid_intervals' => 'The duration must be in full hours or half hours only (multiples of 30 minutes).',
    ],



    'exceptions' => [
        'not_found'       => 'The requested resource was not found.',
        'unauthorized'    => 'You do not have the required permissions for this action.',
        'unauthenticated' => 'You must log in first to access this resource.',
        'access_denied'   => 'Access to this resource is strictly forbidden.',
    ],


    'payment' => [
        'submission_fee_required'    => 'Submission fee payment required to route your request to service providers.',
        'event_submitted_success'    => 'Event request submitted successfully to service providers.',
        'deposit_intent_created'     => 'Deposit payment session initialized successfully.',
        'addon_intent_created'       => 'Add-on payment session initialized successfully.',
        'final_balance_intent_created'=> 'Final balance payment session initialized successfully.',
    ],

    'resources' => [
        'service'  => 'Service',
        'services' => 'Services',
        'search_history' => 'search history',
        'event' => 'Event',
        'events' => 'Events',
        'booking'  => 'Booking',
        'bookings' => 'Bookings',
    ]
];
