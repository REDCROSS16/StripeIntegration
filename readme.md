### Тестовое задание PHP

#### Задача: Интегрировать платежную систему как кастомную интеграцию (php framework любой).

Пояснение: детально описать (насколько возможно) как интегрировать платежную систему Stripe (или любую другую, PayPal и др. тоже подойдет). PHP фреймворк - любой. В описание включить создание формы и работу с рекуррентными платежами. Регистрация Stripe Sandbox аккаунта для тестирования доступна для всех, на произвольный email (для выполнения тестового задания sandbox и тестовых карт достаточно).

Интегрируем платежную систему как кастомную интеграцию (примеры интеграции Stripe есть и в стандартном Marketplace практически любой CMS/Framework, но в нашем случае создаем свою интеграцию) не установку из Marketplace, для дальнейшей кастомизации.

#### Уровень (чем дальше тем лучше):
- Любая рабочая интеграция sandbox платежки.
- Обработка статусов оплат.
- Работа с рекуррентами (особенно важно затронуть, пояснить как бы интегрировали с примерами).
- и т.д.

#### Результат тестового задания:
1. Интеграция кастомная (не через Marketplace) т.е. с получением id пользователя, платежного средства и т.д. (для sandbox это все так же работает).
2. Интеграция платежной формы (не хардкод тестовой карты) с получением webhook и проверкой статусов оплат. Полноценная интеграция для любого пользователя (пусть и оплачивающего тестовой картой в sandbox).
3. Черновик таблиц (либо пояснение) в SQL базе данных в котором будут храниться как первые оплаты, так и рекуррентные платежи (подписки).
4. Логика отмены подписки для пользователя (обращение support, запрос через api - какие поля нужны будут для этого, либо автоматеческая отмена по cron - по каким критериям). Желательно с примерами, но можно и пояснить словами.
5. Минимальное описание выполненной интеграции и готовность пояснить в случае необходимости все особенности интеграции на очной встрече.

#### FAQ:
Можно ли использовать готовые библиотеки для php?\
```Да```\
Подскажите что вы имеете ввиду под marketplace?
```marketplace = любое готовое решение по интеграции
(любое готовое решение интеграции использовать нельзя, т.к. в открытом доступе ничего подходящего именно под рекуррентные платежи нет. В целях экономии Вашего времени - основной фокус, что ожидаем увидеть: интеграция платежной формы + webhook api о обработка крайних случаев + mysql структура хранения пользователей и логика работы с рекуррентными платежами.
Кратче: фокус на умение интеграции API и базы данных) Может это поможет для большего понимания. Но все это на примере Stripe в тестовом задании.)
Можно узнать подробнее, что проверяется этим ТЗ?
форма оплаты, работающая для всех пользователей;
сохранение карты в базы для последующего использования в рекуррентных платежах (sdk платежной системы);
sql структура базы данных для хранения токенов карт и данных пользователей, важно: для использования в схеме рекуррентных платежей, а не разовых оплатах по invoice.
обработка статусов ошибок оплат, webhook (особенно в случаях рекуррентных платежей), в том числе их логирование в базу данных для разбора.
```

