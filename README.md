# php-ncanode-client

Клиент NCANode для PHP

## Установка

Установка производится через *composer*. Для установки наберите команду в директории вашего php проекта:

```bash
composer require malikzh/php-ncanode
```

## Использование

### Подключение к серверу NCANode

```php
$nca = new \Malikzh\PhpNCANode\NCANodeClient('http://127.0.0.1:14579');
```

### ПодписьCMS

```php
$nca->cmsSign(string $base64data, string $p12Base64, string $sPassword);
```

### Проверка подписи

```php
$nca->cmsVerify(string $base64data)->isValid();
```

### Информация о сервере NCANode

```php
$nca->nodeInfo()
```

## Авторы

- **Malik Zharykov** - Initial work
- **Rustem Kaimolla** - updated works

## Лицензия

Проект лицензирован под лицензией [MIT](LICENSE)