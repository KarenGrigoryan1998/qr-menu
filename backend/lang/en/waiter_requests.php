<?php

return [
    'navigation_label' => 'Waiter Requests',
    'navigation_group' => 'Operations',
    'model_label' => 'Waiter Request',
    'plural_label' => 'Waiter Requests',
    
    'fields' => [
        'id' => 'ID',
        'restaurant' => 'Restaurant',
        'table' => 'Table',
        'table_number' => 'Table Number',
        'order' => 'Order',
        'order_id' => 'Order #',
        'status' => 'Status',
        'note' => 'Note',
        'acknowledged_at' => 'Acknowledged At',
        'completed_at' => 'Completed At',
        'created_at' => 'Requested At',
        'response_time' => 'Response Time',
    ],
    
    'statuses' => [
        'pending' => 'Pending',
        'acknowledged' => 'Acknowledged',
        'completed' => 'Completed',
    ],
    
    'actions' => [
        'acknowledge' => 'Acknowledge',
        'complete' => 'Complete',
        'acknowledge_all' => 'Acknowledge All Pending',
        'refresh' => 'Refresh',
        'im_on_it' => 'I\'m on it!',
    ],
    
    'notifications' => [
        'acknowledged' => 'Request Acknowledged',
        'acknowledged_body' => 'Table :table request acknowledged.',
        'completed' => 'Request Completed',
        'completed_body' => 'Table :table request completed.',
        'all_acknowledged' => 'All Pending Acknowledged',
        'all_acknowledged_body' => ':count pending requests have been acknowledged.',
        'handling' => 'You\'re now handling Table :table',
    ],
    
    'widgets' => [
        'pending_requests' => 'Pending Waiter Requests',
        'pending_description' => 'Customers waiting for assistance',
        'empty_heading' => 'No Pending Requests',
        'empty_description' => 'All customers are being served! 🎉',
        
        'stats' => [
            'pending' => 'Pending Requests',
            'pending_description' => 'Waiting for acknowledgment',
            'in_progress' => 'In Progress',
            'in_progress_description' => 'Currently being handled',
            'completed_today' => 'Completed Today',
            'completed_description' => 'Successfully completed',
            'avg_response_time' => 'Avg Response Time',
            'avg_response_description' => 'Average time to acknowledge',
        ],
    ],
    
    'sections' => [
        'request_details' => 'Request Details',
        'timeline' => 'Timeline',
        'additional_info' => 'Additional Information',
    ],
    
    'placeholders' => [
        'no_note' => 'No note',
        'not_acknowledged' => 'Not yet acknowledged',
        'not_completed' => 'Not yet completed',
    ],
];
