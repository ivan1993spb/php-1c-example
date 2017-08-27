<?php

return [
    // Любой валидный UUID
    'valid-uuid' => '00000000-0000-0000-0000-000000000000',

    // UUID несуществующего клиента
    'client-uuid-not-found' => 'some uuid',

    // UUID клиента не имеющего соглашение
    'client-uuid-has-not-agreement' => 'some uuid',

    // UUID существующего клиента
    'client-uuid-exists' => 'some uuid',

    // Некорректный UUID
    'invalid-uuid' => 'invalid uuid',

    // UUID несуществующего заказа
    'order-uuid-not-found' => 'some uuid',

    // UUID несуществующей номенклатуры
    'good-uuid-not-found' => 'some uuid',

    // UUID товара, которого очень много на складе
    'good-uuid-many' => 'some uuid',

    // UUID товара, которого > 0 и < 10 штук на складе
    'good-uuid-more-then-0-less-then-10' => 'some uuid',

    // UUID товара, которого 0 штук на складе
    'good-uuid-have-not' => 'some uuid',

    // UUID существующего товара
    'good-uuid-exists' => 'some uuid',

    // UUID товаров, которых > 0 на складе
    'good-uuid-more-then-0-1' => 'some uuid',
    'good-uuid-more-then-0-2' => 'some uuid',
    'good-uuid-more-then-0-3' => 'some uuid',
    'good-uuid-more-then-0-4' => 'some uuid',
];
