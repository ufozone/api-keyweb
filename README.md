# api-keyweb
A PHP client library for accessing Keyweb AG server status API.

License: GNU AGPLv3

## Examples of using Keyweb API
### Get server list
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->GetServerList();
print_r($result);
```

### Get server properties
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->GetServerProperties('KM21212');
print_r($result);
```

### Set server properties
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->SetServerProperties('KM21212', 'ns.status.keyweb.de', 'ns2.status.keyweb.de', 'status.keyweb.de');
print_r($result);
```

### Get reset history by server
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->ResetHistory('KM21212');
print_r($result);
```

### Reset server
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->Reset('KM21212', '23232');
print_r($result);
```

### Get traffic history by server
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->Traffic('KM21212');
print_r($result);
```

### Set IP address reverse lookup
```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->SetReverseLookup('KM21212', '95.169.160.13', 'status.keyweb.de');
print_r($result);
```

## External Resources

* [Keyweb AG API Documentation](https://status.keyweb.de/API/Keyweb-ServerStatus-API.pdf)
* [Keyweb AG Website](https://www.keyweb.de/de/)
