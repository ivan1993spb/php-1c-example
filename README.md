
# Пример взаимодействия сайта с 1С через SoapClient

PHP, 1С, SOAP, SoapClient, Обмен.

В данном репозитории собран пример организации взаимодействия сайта на PHP с 1С через SOAP.

Описаны PHP-классы для работы системы в продакшене, для локальной разработки (моки) и для тестирования.

## Веб-сервисы 1С

В 1С существует возможность добавлять [веб-сервисы](http://v8.1c.ru/overview/Term_000000273.htm). Веб-сервисы 1С могут выполнять какие-то действия на стороне 1С. Веб-сервисы могут быть вызваны из вне через SOAP с помощью PHP. В PHP обращение к веб-сервисам через SOAP осуществляется с помощью [SoapClient](http://php.net/manual/ru/book.soap.php).

Для иллюстрации взаимодействия 1С и PHP написан клиент на PHP и описан функционал, который (в данном примере) предоставляет 1С через веб-сервисы. Смотрите описание функционала [здесь](docs/1cApi.md).

## Классы-клиенты 1С

* `Client1C` - класс-обертка для `SoapClient` для вызова веб-сервисов 1С;
* `Client1CWrapper` - удобный класс-обертка для `Client1C`.

## Тестирование

Для тестирования логики, которая использует веб-сервисы 1С, а также для локальной разработки доступны классы:

* `DumbClient1C` - mock-объект, выполняет все действия, которые должен выполнять 1С, **локально**;
* `FailureClient1C` - mock-объект, не выполняет никаких действий, а при каждом вызове выбрасывает исключение `MethodInvocationException`, как буд-то сервер 1С недоступен. Удобно использовать для проверки ситуации, в которой 1С не работает, а сайт работает и доступен для клиентов.
