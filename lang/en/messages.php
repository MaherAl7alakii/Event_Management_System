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



    'date_available' => 'The selected date is available.',
    'slot_available' => 'The selected time slot is available.',

    'booking_must_be_4_days_ahead' => 'The booking date must be at least 4 days from today.',


    'user_banned_success'        => 'User has been banned successfully.',
    'user_unbanned_success'      => 'User has been unbanned successfully.',
    'account_banned' => 'Your account has been banned. Please contact support.',



    'marked_all_read_success' => 'All :resource marked as read successfully.',
    'deleted_all_success'     => 'All :resource deleted successfully.',


    'cannot_message_self'  => 'You cannot start a conversation with yourself.',
    'marked_read_success'  => 'Messages marked as read successfully.',
    'typing_broadcasted'   => 'Typing status broadcasted successfully.',


    'price_proposal_submitted_successfully'       => 'Price proposal submitted successfully.',
    'price_proposal_history_fetched_successfully' => 'Price proposal history retrieved successfully.',
    'price_proposal_responded_successfully'       => 'Price proposal response recorded successfully.',


    'booking_cancelled_successfully' => 'Booking cancelled successfully.',
    'not_party_to_booking'          => 'You are not a party to this booking.',


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

        'service_availability' => [
            'service_inactive'      => 'This service is not currently active.',
            'outside_working_hours' => 'The requested time is outside the provider\'s working hours.',
            'time_off_conflict'     => 'The provider is unavailable at the requested time.',
            'booking_conflict'      => 'The requested time conflicts with an existing booking.',
            'default'               => 'This service is not available at the requested time.',
        ],
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

        'working_hours' => 'Working hours',
        'calendar'      => 'Calendar',
        'time_off'      => 'Time off',

        'customers'    => 'customers',
        'service_providers' => 'Service providers',

        'notification'  => 'Notification',
        'notifications' => 'Notifications',

        'conversation'  => 'Conversation',
        'conversations' => 'Conversations',
        'message'       => 'Message',
        'messages'      => 'Messages',

        'booking_complaint'  => 'Booking Complaint',
        'booking_complaints' => 'Booking Complaints',

        'payments' => 'payments',
        'payouts'  => 'payouts',
        'refunds' => 'refunds',
    ]
];
