<?php

return [
    'dashboard' => [
        'low_stock_threshold_kg' => env('LOW_STOCK_THRESHOLD_KG', 10),
        'recent_messages_limit' => env('RECENT_MESSAGES_LIMIT', 10),
        'recent_sales_limit' => env('RECENT_SALES_LIMIT', 10),
        'low_stock_lots_limit' => env('LOW_STOCK_LOTS_LIMIT', 5),
        'pending_deliveries_limit' => env('PENDING_DELIVERIES_LIMIT', 3),
        'rfq_lookback_days' => env('RFQ_LOOKBACK_DAYS', 3),
        'email_lookback_days' => env('EMAIL_LOOKBACK_DAYS', 7),
        'payment_due_days' => env('PAYMENT_DUE_DAYS', 3),
        'delivery_due_days' => env('DELIVERY_DUE_DAYS', 7),
    ],

    'events' => [
        'agenda_lookahead_days' => env('EVENT_AGENDA_LOOKAHEAD_DAYS', 7),
    ],
];
