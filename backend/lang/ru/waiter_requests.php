<?php

return [
    'navigation_label' => 'Запросы официанта',
    'navigation_group' => 'Операции',
    'model_label' => 'Запрос официанта',
    'plural_label' => 'Запросы официанта',
    
    'fields' => [
        'id' => 'ID',
        'restaurant' => 'Ресторан',
        'table' => 'Стол',
        'table_number' => 'Номер стола',
        'order' => 'Заказ',
        'order_id' => 'Заказ #',
        'status' => 'Статус',
        'note' => 'Примечание',
        'acknowledged_at' => 'Принято',
        'completed_at' => 'Завершено',
        'created_at' => 'Запрошено',
        'response_time' => 'Время ответа',
    ],
    
    'statuses' => [
        'pending' => 'Ожидание',
        'acknowledged' => 'Принято',
        'completed' => 'Завершено',
    ],
    
    'actions' => [
        'acknowledge' => 'Принять',
        'complete' => 'Завершить',
        'acknowledge_all' => 'Принять все ожидающие',
        'refresh' => 'Обновить',
        'im_on_it' => 'Я займусь!',
    ],
    
    'notifications' => [
        'acknowledged' => 'Запрос принят',
        'acknowledged_body' => 'Запрос стола :table принят.',
        'completed' => 'Запрос завершен',
        'completed_body' => 'Запрос стола :table завершен.',
        'all_acknowledged' => 'Все ожидающие приняты',
        'all_acknowledged_body' => ':count ожидающих запросов приняты.',
        'handling' => 'Вы теперь обрабатываете стол :table',
    ],
    
    'widgets' => [
        'pending_requests' => 'Ожидающие запросы официанта',
        'pending_description' => 'Клиенты ждут помощи',
        'empty_heading' => 'Нет ожидающих запросов',
        'empty_description' => 'Все клиенты обслуживаются! 🎉',
        
        'stats' => [
            'pending' => 'Ожидающие запросы',
            'pending_description' => 'Ожидают подтверждения',
            'in_progress' => 'В процессе',
            'in_progress_description' => 'Сейчас обрабатываются',
            'completed_today' => 'Завершено сегодня',
            'completed_description' => 'Успешно завершено',
            'avg_response_time' => 'Среднее время ответа',
            'avg_response_description' => 'Среднее время для подтверждения',
        ],
    ],
    
    'sections' => [
        'request_details' => 'Детали запроса',
        'timeline' => 'Временная шкала',
        'additional_info' => 'Дополнительная информация',
    ],
    
    'placeholders' => [
        'no_note' => 'Нет примечания',
        'not_acknowledged' => 'Еще не принято',
        'not_completed' => 'Еще не завершено',
    ],
];
