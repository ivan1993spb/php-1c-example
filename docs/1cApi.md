
API 1С
======

AddOrder
--------

Добавляет пустой заказ для клиента с UUID `clientUUID`.

### Request:

```
{
    clientUUID: "1d345afc-ffff-eeee-1234-56ecba946552" string required
}
```

### Поля запроса:

- `clientUUID` - UUID клиента

### Response:

```
{
    orderUUID:    "e5735434-f84b-11e2-ae7e-f46d04994676", string required
    orderID:      "СВ00-005539"                           string required
    VAT:          18,                                     int    required
    payType:      0,                                      int    required
    deliveryType: 0,                                      int    required
    status:       0                                       int    required
}
```

### Поля ответа:

- `orderUUID` - UUID добавленного заказа. `orderUUID = NULL` в случае ошибки
- `orderID` - ID добавленного заказа в 1С. `orderID = NULL` в случае ошибки
- `VAT` - НДС
- `payType` - тип оплаты
- `deliveryType` - тип доставки
- `status` - статус код операции

`payType` может быть:

1. `payType = 0` - предоплата 100%
2. `payType = 1` - предоплата 50%
3. `payType = 2` - оплата согласно условиям договора
4. `payType = NULL` - ошибка

`deliveryType` может быть:

1. `deliveryType = 0` - самовывоз
2. `deliveryType = 1` - доставка
3. `deliveryType = NULL` - ошибка

`status` может быть:

1. `status = 0` - все хорошо
2. `status = 1` - `clientUUID` не был передан
3. `status = 2` - `clientUUID` не корректен
4. `status = 3` - клиент с `clientUUID` не найден
5. `status = 4` - невозможно определить соглашение
6. `status = 5` - ошибка записи заказа

AddGoodToOrder
--------------

Добавляет одну позицию в существующий заказ.

### Request:

```
{
    orderUUID: "e5735434-f84b-11e2-ae7e-f46d04994676", string required
    goodUUID:  "1aa35434-f84b-4556-3333-ffff56adffaa", string required
    count:     5                                       int    required
}
```

### Поля запроса:

- `orderUUID` - UUID заказа
- `goodUUID` - UUID товара
- `count` - сколько единиц данного товара требуется добавить в заказ

### Response:

```
{
    goodUUID:         "87a8bf70-ba5c-11df-8288-0030849f1849", string required
    count:            3,                                      int    required
    required:         5,                                      int    required
    order:            [                                       array  optional
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    3,                                      int    required
            price:    1532                                    float  required
        },
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    2,                                      int    required
            price:    874                                     float  required
        }
    ],
    discountAmount:   1234,                                   float  required
    status:           0                                       int    required
}
```

### Поля ответа:

- `goodUUID` - UUID добавленного в заказ товара
- `count` - сколько единиц данного товара добавлено в заказ
- `required` - сколько единиц данного товара требовалось
- `order` - текущий состав заказа
- `order[n].goodUUID` - UUID товара
- `order[n].count` - кол-во
- `order[n].price` - цена товара с НДС
- `discountAmount` - сумма скидки заказа
- `status` - статус запроса

`status` может быть:

1. `status = 0` - все хорошо
2. `status = 1` - `orderUUID` не переданн
3. `status = 2` - `orderUUID` не корректный
4. `status = 3` - заказ c `orderUUID` не найден
5. `status = 4` - `goodUUID` не был передан
6. `status = 5` - `goodUUID` не корректный
7. `status = 6` - товар с `goodUUID` не найден
8. `status = 7` - `count` не был переданн
9. `status = 8` - `count` не корректный (отрицательный)
10. `status = 9` - заказ уже подтвержден
11. `status = 10` - товар добавлен в корзину частично (`< count`)
12. `status = 11` - товар не может быть добавлен из-за нулевого остатка
13. `status = 12` - товар для удаления не найден в заказе
14. `status = 13` - превышен лемит ожидания транзакции
15. `status = 14` - ошибка проведения заказа
16. `status = 15` - запрос на изменение устарел и не будет выполнен
17. `status = 16` - максимальное время соединения превышено
18. `status = 17` - заказ уже отменен

AddGoodsToOrder
---------------

Добавляет список товаров в cуществующий заказ.

### Request:

```
{
    orderUUID:        "12345678-f84b-11e2-ae7e-f46d04994676", string required
    goods:            [                                       array  required
        {
            goodUUID: "e91abfdb-6d99-11e1-8023-f46d04994676", string required
            count:    4                                       int    required
        },
        {
            goodUUID: "7011fcaa-d119-11df-802b-0030849f1849", string required
            count:    1                                       int    required
        },
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    3                                       int    required
        }
    ]
}
```

### Поля запроса:

- `orderUUID` - UUID заказа
- `goods` - список товаров для добавления в заказ
- `goods[n].goodUUID` - UUID товара
- `goods[n].count` - сколько надо добавить в заказ

### Response:

```
{
    goods:            [                                          array  required
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    3,                                      int    required
            required: 3,                                      int    required
            status:   0,                                      int    required
        },
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    0,                                      int    required
            required: 3,                                      int    required
            status:   1,                                      int    required
        }
    ],
    order:            [                                       array  required
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    3,                                      int    required
            price:    1532                                    float  required
        },
        {
            goodUUID: "87a8bf70-ba5c-11df-8288-0030849f1849", string required
            count:    2,                                      int    required
            price:    874                                     float  required
        }
    ],
    discountAmount:   1234,                                   float  required
    status:           0                                       int    required
}
```

### Поля ответа:

- `goods` - Отчет по товарам, которые требовалось добавить в заказ
- `goods[n].goodUUID` - UUID товара
- `goods[n].count` - сколько удалось добавить в заказ товара
- `goods[n].required` - сколько требовалось добавить в заказ товара
- `goods[n].status` - статус код для операции добавления данного товара в заказ
- `order` - состав заказа
- `order[n].goodUUID` - UUID товара
- `order[n].count` - кол-во
- `order[n].price` - цена товара с НДС
- `discountAmount` - сумма скидки заказа
- `status` - статус код для операции добавления списка товара в заказ

`goods[n].status` может быть:

1. `goods[n].status = 0` - все хорошо
2. `goods[n].status = 1` - `goods[n].goodUUID` не передан
3. `goods[n].status = 2` - `goods[n].goodUUID` не корректный
4. `goods[n].status = 3` - товар с идентификатором `goods[n].goodUUID` не найден
5. `goods[n].status = 4` - `goods[n].count` не был передан
6. `goods[n].status = 5` - количество `goods[n].count` не корректно (отрицательное)
7. `goods[n].status = 6` - товар добавлен в корзину частично (`< goods[n].count`)
8. `goods[n].status = 7` - товар не может быть добавлен из-за нулевого остатка
9. `goods[n].status = 8` - товар для удаления не найден
10. `goods[n].status = 9` - запрос на изменение устарел и не будет выполнен

`status` может быть:

1. `status = 0` - все хорошо
2. `status = 1` - `orderUUID` не передан
3. `status = 2` - `orderUUID` не корректен
4. `status = 3` - заказ с UUID `orderUUID` не найден
5. `status = 4` - список товара для добавления `goods` не был передан
6. `status = 5` - список товара для добавления `goods` пустой
7. `status = 6` - запрос выполнен частично
8. `status = 7` - заказ уже подтвержден
9. `status = 8` - превышен лемит ожидания транзакции
10. `status = 9` - ошибка проведения заказа
11. `status = 10` - максимальное время соединения превышено
12. `status = 11` - заказ уже удален

ConfirmOrder
------------

Подтвердить заказ.

### Request:

```
{
    orderUUID: "87318f9e-c6f6-475b-98b9-c41ac4de553e" string required
}
```

### Поля запроса:

- `orderUUID` - UUID заказа

### Response:

```
{
    discountAmount: 1234, float required
    status:         0     int required
}
```

### Поля ответа:

- `discountAmount` - сумма скидки заказа
- `status` - статус код для операции

`status` может быть:

1. `status = 0` - все хорошо, заказ подтвержден
2. `status = 1` - отсутствует `orderUUID`
3. `status = 2` - некорректный `orderUUID`
4. `status = 3` - заказ с переданным `orderUUID` не найден
5. `status = 4` - заказ уже подтвержден
6. `status = 5` - ошибка проведения заказа
7. `status = 6` - максимальное время соединения превышено
8. `status = 7` - заказ уже удален
9. `status = 8` - невозможно подтвердить пустой заказ

ResetOrder
----------

Очищает список товаров в заказе и помечает документ на удаление.

### Request:

```
{
    orderUUID: "87318f9e-c6f6-475b-98b9-c41ac4de553e" string required
}
```

### Поля запроса:

- `orderUUID` - UUID заказа

### Response:

```
{
    status: 0 int required
}
```

### Поля ответа:

- `status` - статус код для операции

`status` может быть:

1. `status = 0` - все хорошо (заказ отменен)
2. `status = 1` - отсутствует `orderUUID`
3. `status = 2` - некорректный `orderUUID`
4. `status = 3` - заказ с переданным `orderUUID` не найден
5. `status = 4` - заказ уже подтвержден
6. `status = 5` - ошибка проведения заказа
7. `status = 6` - максимальное время соединения превышено
8. `status = 7` - заказ уже удален
