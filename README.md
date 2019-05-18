[![Latest Stable Version](https://poser.pugx.org/wildtuna/piastrix/v/stable)](https://packagist.org/packages/wildtuna/piastrix-sdk)
[![Total Downloads](https://poser.pugx.org/wildtuna/piastrix/downloads)](https://packagist.org/packages/wildtuna/piastrix-sdk)
[![License](https://poser.pugx.org/wildtuna/piastrix/license)](https://packagist.org/packages/wildtuna/piastrix-sdk)  

# Piastrix
PHP SDK для интеграции с платежным сервисом [Piastrix](https://piastrix.com)  
[Документация API Piastrix](https://piastrix.docs.apiary.io/#introduction/ptx-api)

# Установка
Для установки можно использовать менеджер пакетов Composer

    composer require wildtuna/piastrix

# Файл конфигурации
Для работы SDK нужно создать конфигурационный yaml файл. 
Пример можно посмотреть [тут](https://github.com/iamwildtuna/piastrix/blob/master/Examples/config.yml).

**Описание параметров:**
 - auth  
   - host - адрес API Piastrix  
   - shop_id - ID магазина из ЛК Piastrix  
   - secret_key - Секретный ключ магазина из ЛК Piastrix  
   - available_payment_systems - массивы с алиасами платежных направлений ввода и вывода, которые отправляются на платежную
 страницу вместе с платежной формой.
 
# Использование SDK
