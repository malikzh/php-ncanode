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

### Получение информации о сертификате

```php
$response = $nca->pkcs12Info(p12Base64: $cert, sPassword: 'AAaa1234');
$response->raw();
```

### Получение информации о нескольких сертификатов

```php
$response = $nca->pkcs12InfoBulk(p12s: [
    [
        'key' => $p12_1,
        'password' => 'AAaa1234'
    ],
    [
        'key' => $p12_2,
        'password' => 'AAaa1234'
    ],
    # ...
], revocationCheck: ['OCSP'], alias: null);
```

### Получение алиаса для ключа

```php
$response = $nca->pkcs12AliasInfo(p12Base64: $p12, sPassword: 'AAaa1234');
```

### Получение списка алиасов для ключей

```php
$response = $nca->pkcs12AliasesInfoBulk(p12s: [
    [
        'key' => $p12,
        'password' => 'AAaa1234'
    ],
    # ...
]);
```


### Подпись CMS

```php
$nca->cmsSign(string $base64data, string $p12, string $certPassword);
```

### Множественный подпись CMS

```php
$nca->cmsBulkSign(string $base64data, string $p12s);
```

### Добавить подпись в существующий CMS

```php
$nca->cmsSignAdd(string $base64data, string $cmsFilebase64, string $p12, string $certPassword);
```

### Проверка подписи

```php
$nca->cmsVerify(string $base64data)->isValid();
```

### Извлекать данные из подписанной CMS.

```php
$nca->cmsExtract($cmsFileBase64);
```

## Авторы

- **Malik Zharykov** - Initial work
- **Rustem Kaimolla** - updated works
- **Rakhat Bakytzhanov** - updated works

## Лицензия

Проект лицензирован под лицензией [MIT](LICENSE)