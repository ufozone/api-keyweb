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
*Function parameters*
* Server ID

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->GetServerProperties('KM21212');
print_r($result);
```

### Set server properties
*Function parameters*
* Server ID
* rDNS for first IP address
* rDNS for second IP address
* Server Name as FQDN

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->SetServerProperties('KM21212', 'ns.status.keyweb.de', 'ns2.status.keyweb.de', 'status.keyweb.de');
print_r($result);
```

### Get reset history by server
*Function parameters*
* Server ID

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->ResetHistory('KM21212');
print_r($result);
```

### Reset server
*Function parameters*
* Server ID
* Customer Number (for reset history)

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->Reset('KM21212', '23232');
print_r($result);
```

### Get traffic history by server
*Function parameters*
* Server ID
* Type of history
** today
** yesterday
** weekly
** monthly
** date
** lastslots
* Date (only neccesary by using type 'date')
* Last slots (only neccesary by using type 'lastslots')

*API response*
Depending on which 'type' was chosen, you get different response formats.

If you using the types {{today}}, {{yesterday}}, {{weekly}} or {{monthly}}, you will get a PNG graphic of the traffic history encoded in base64.
In the other cases ({{date}}, {{lastslots}}), you get a tabular listing of the numeric traffic values.

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->Traffic('KM21212');
print_r($result);
```

### Set IP address reverse lookup
*Function parameters*
* Server ID
* IP address
* rDNS

```php
$keyweb = new Keyweb('23232', 'apiuser', 'apipass');
$keyweb->debug(true);

$result = $keyweb->SetReverseLookup('KM21212', '95.169.160.13', 'status.keyweb.de');
print_r($result);
```

## External Resources

* [Keyweb AG API Documentation](https://status.keyweb.de/API/Keyweb-ServerStatus-API.pdf)
* [Keyweb AG Website](https://www.keyweb.de/de/)
