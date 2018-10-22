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

### Подпись XML

```php
$nca->xmlSign('<?xml version="1.0"?><root><dataToSign>mydata</dataToSign></root>', $p12InBase64, $password);
```

### Проверка подписи XML

```php
$nca->xmlVerify('<signed xml>', $verifyOcsp, $verifyCrl);
```

### Информация о ключе PKCS12

```php
$nca->pkcs12Info($p12Base64, $sPassword, $bVerifyOcsp, $bVerifyCrl)
```

### Информация о сертификате X509

```php
$nca->x509Info($x509Base64, $bVerifyOcsp, $bVerifyCrl)
```

### Информация о сервере NCANode

```php
$nca->nodeInfo()
```

## Авторы

- **Malik Zharykov** - Initial work

## Лицензия

Проект лицензирован под лицензией [MIT](LICENSE)