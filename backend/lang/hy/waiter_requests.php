<?php

return [
    'navigation_label' => 'Սպասարկողի հարցումներ',
    'navigation_group' => 'Գործառնություններ',
    'model_label' => 'Սպասարկողի հարցում',
    'plural_label' => 'Սպասարկողի հարցումներ',

    'fields' => [
        'id' => 'ID',
        'restaurant' => 'Ռեստորան',
        'table' => 'Սեղան',
        'table_number' => 'Սեղանի համար',
        'order' => 'Պատվեր',
        'order_id' => 'Պատվեր #',
        'status' => 'Կարգավիճակ',
        'note' => 'Նշում',
        'acknowledged_at' => 'Ընդունված',
        'completed_at' => 'Ավարտված',
        'created_at' => 'Հարցվել է',
        'response_time' => 'Պատասխանի ժամանակ',
    ],

    'statuses' => [
        'pending' => 'Սպասման մեջ',
        'acknowledged' => 'Ընդունված',
        'completed' => 'Ավարտված',
    ],

    'actions' => [
        'acknowledge' => 'Ընդունել',
        'complete' => 'Ավարտել',
        'acknowledge_all' => 'Ընդունել բոլոր սպասման մեջ գտնվող հարցումները',
        'refresh' => 'Թարմացնել',
        'im_on_it' => 'Ես կզբաղվեմ!',
    ],

    'notifications' => [
        'acknowledged' => 'Հարցումը ընդունված է',
        'acknowledged_body' => 'Սեղան :table հարցումը ընդունված է։',
        'completed' => 'Հարցումը ավարտված է',
        'completed_body' => 'Սեղան :table հարցումը ավարտված է։',
        'all_acknowledged' => 'Բոլոր սպասման մեջ գտնվող հարցումները ընդունված են',
        'all_acknowledged_body' => ':count սպասման մեջ գտնվող հարցումներ ընդունված են։',
        'handling' => 'Դուք այժմ զբաղվում եք Սեղան :table-ով',
    ],

    'widgets' => [
        'pending_requests' => 'Սպասման մեջ գտնվող սպասարկողի հարցումներ',
        'pending_description' => 'Հաճախորդներ, որոնք սպասում են օգնության',
        'empty_heading' => 'Սպասման մեջ գտնվող հարցումներ չկան',
        'empty_description' => 'Բոլոր հաճախորդները սպասարկվում են! 🎉',

        'stats' => [
            'pending' => 'Սպասման մեջ գտնվող հարցումներ',
            'pending_description' => 'Սպասում են ընդունման',
            'in_progress' => 'Ընթացքի մեջ',
            'in_progress_description' => 'Ներկայումս մշակվում է',
            'completed_today' => 'Այսօր ավարտված',
            'completed_description' => 'Հաջողությամբ ավարտված',
            'avg_response_time' => 'Միջին պատասխանի ժամանակ',
            'avg_response_description' => 'Միջին ժամանակը ընդունելու համար',
        ],
    ],

    'sections' => [
        'request_details' => 'Հարցման մանրամասներ',
        'timeline' => 'Ժամանակացույց',
        'additional_info' => 'Լրացուցիչ տեղեկություններ',
    ],

    'placeholders' => [
        'no_note' => 'Նշում չկա',
        'not_acknowledged' => 'Դեռ չի ընդունվել',
        'not_completed' => 'Դեռ չի ավարտվել',
    ],
];
