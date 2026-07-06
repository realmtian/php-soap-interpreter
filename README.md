# PHP SOAP Interpreter [![codecov.io](https://codecov.io/github/meng-tian/php-soap-interpreter/coverage.svg?branch=master)](https://codecov.io/github/meng-tian/php-soap-interpreter?branch=master) ![workflow](https://github.com/meng-tian/php-soap-interpreter/actions/workflows/main.yaml/badge.svg)


A PHP library for interpreting `SOAP 1.1` and `SOAP 1.2` messages. It can be used in WSDL or non-WSDL mode. The implementation is built on the top of PHP's [SoapClient](http://php.net/manual/en/class.soapclient.php).

### Prerequisite

PHP 8.2 or newer with `ext-soap`. PHP 7.x and PHP 8.0-8.1 remain available
only through older releases.

### Install
```
composer require meng-tian/php-soap-interpreter:^2.1
```

### Usage
An `Interpreter` is responsible for generating SOAP request messages and translating SOAP response messages. The constructor of `Interpreter` class is the same as `SoapClient`. The first parameter is `wsdl`, the second parameter is an array of `options`.

It should be noted that *not* all `options` supported by `SoapClient` are supported by `Interpreter`. The supported `options` of `Interpreter` are: `location`, `uri`, `style`, `use`, `soap_version`, `encoding`, `exceptions`, `classmap`, `typemap`, `cache_wsdl` and `features`. More detailed explanations of those options can be found in [SoapClient::SoapClient](http://php.net/manual/en/soapclient.soapclient.php). The unsupported options are related to debugging or HTTP transport, which are not the intended responsibility of `Interpreter`.

### Basic Examples
###### Generate SOAP request message in WSDL mode

```php
$interpreter = new Interpreter('http://www.webservicex.net/length.asmx?WSDL');
$request = $interpreter->request(
    'ChangeLengthUnit',
    [['LengthValue'=>'1', 'fromLengthUnit'=>'Inches', 'toLengthUnit'=>'Meters']]
);

print_r($request->getSoapMessage());
```
Output:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.webserviceX.NET/">
<SOAP-ENV:Body><ns1:ChangeLengthUnit><ns1:LengthValue>1</ns1:LengthValue><ns1:fromLengthUnit>Inches</ns1:fromLengthUnit><ns1:toLengthUnit>Meters</ns1:toLengthUnit></ns1:ChangeLengthUnit></SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

###### Translate SOAP response message

```php
$interpreter = new Interpreter('http://www.webservicex.net/length.asmx?WSDL');
$response = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <ChangeLengthUnitResponse xmlns="http://www.webserviceX.NET/">
            <ChangeLengthUnitResult>0.025400000000000002</ChangeLengthUnitResult>
        </ChangeLengthUnitResponse>
    </soap:Body>
</soap:Envelope>
EOD;
$response = $interpreter->response($response, 'ChangeLengthUnit');

print_r($response);
```
Output:
```php
/*
Output:
stdClass Object
(
    [ChangeLengthUnitResult] => 0.0254
)
*/
```

### Advanced Examples
###### Generate SOAP request message in non-WSDL mode

```php
// In non-WSDL mode, location and uri must be provided as they are required by SoapClient.
$interpreter = new Interpreter(null, ['location'=>'http://www.webservicex.net/length.asmx', 'uri'=>'http://www.webserviceX.NET/']);
$request = $interpreter->request(
    'ChangeLengthUnit',
    [
        new SoapParam('1', 'ns1:LengthValue'),
        new SoapParam('Inches', 'ns1:fromLengthUnit'),
        new SoapParam('Meters', 'ns1:toLengthUnit')
    ],
    ['soapaction'=>'http://www.webserviceX.NET/ChangeLengthUnit']
);

print_r($request->getSoapMessage());
```
Output:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.webserviceX.NET/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
<SOAP-ENV:Body><ns1:ChangeLengthUnit><ns1:LengthValue xsi:type="xsd:string">1</ns1:LengthValue><ns1:fromLengthUnit xsi:type="xsd:string">Inches</ns1:fromLengthUnit><ns1:toLengthUnit xsi:type="xsd:string">Meters</ns1:toLengthUnit></ns1:ChangeLengthUnit></SOAP-ENV:Body>
</SOAP-ENV:Envelope>

```


###### SOAP input headers

```php
$interpreter = new Interpreter('http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');
$request = $interpreter->request('ConversionRate', [['FromCurrency' => 'AFA', 'ToCurrency' => 'ALL']], null, [new SoapHeader('www.namespace.com', 'test_header', 'header_data')]);
print_r($request->getSoapMessage());
```
Output:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.webserviceX.NET/" xmlns:ns2="www.namespace.com">
<SOAP-ENV:Header><ns2:test_header>header_data</ns2:test_header></SOAP-ENV:Header>
<SOAP-ENV:Body><ns1:ConversionRate><ns1:FromCurrency>AFA</ns1:FromCurrency><ns1:ToCurrency>ALL</ns1:ToCurrency></ns1:ConversionRate></SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### Testing

```sh
composer install
vendor/bin/phpunit
```

### Relevant
- [SOAP HTTP Binding](https://github.com/meng-tian/soap-http-binding): binding SOAP messages to PSR-7 HTTP messages.
- [PHP Asynchronous SOAP](https://github.com/meng-tian/php-async-soap): asynchronous SOAP clients.

### License
This library is released under [MIT](https://github.com/meng-tian/php-soap-interpreter/blob/master/LICENSE.md) license.
