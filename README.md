
# Пример взаимодействия сайта с 1С через SoapClient

PHP, 1С, SOAP, SoapClient, Обмен

В данном репозитории собран пример организации взаимодействия сайта на PHP с 1С через SOAP.

## [Описание API 1С - веб-сервисы](docs/1cApi.md)

Описание веб-сервисов 1С, входящих и исходящих типов данных. Веб-сервисы доступны в PHP через [SoapClient](http://php.net/manual/ru/book.soap.php).

## Для вызова веб-сервисов 1С используются классы

* `Client1C` - класс-обертка для `SoapClient` для вызова веб-сервисов 1С;
* `Client1CWrapper` - удобный класс-обертка для `Client1C`.

## Тестирование

Для тестирования логики,которая использует веб-сервисы 1С, а также для локальной разработки доступны классы:

* `DumbClient1C` - mock-объект, выполняет все действия, которые должен выполнять 1С, **локально**;
* `FailureClient1C` - mock-объект, не выполняет никаких действий, а при каждом вызове выбрасывает исключение `MethodInvocationException`, как буд-то сервер 1С недоступен.
