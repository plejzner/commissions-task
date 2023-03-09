# How to run refactored solution

Project is dockerized, so only dependency needed is docker with compose.

- install dependencies:  
  `docker compose run php-cli composer install`
- run tests and cs-fixer:  
  `docker compose run -e "PHP_CS_FIXER_IGNORE_ENV=1" php-cli composer run test` (IGNORE_ENV, because cs-fixer doesn't officially support php 8.2 yet)
- provide exchange api key in  
  `src/Config.php`
- run script with example input file:  
  `docker compose run php-cli php src/bootstrap.php tests/integration/input.txt`
- error details are logged to  
  `var/log.log`

---

# Task description

Refactor given legacy script.  

- Idea is to calculate commissions for already made transactions;
- Transactions are provided each in it's own line in the input file, in JSON;
- BIN number represents first digits of credit card number. They can be used to resolve country where the card was issued;
- We apply different commission rates for EU-issued and non-EU-issued cards;
- We calculate all commissions in EUR currency.  

## Improvements

- As an improvement, add ceiling of commissions by cents. For example, 0.46180... should become 0.47.  
- Consider possibility of huge input files.

### The script:

```php
<?php

foreach (explode("\n", file_get_contents($argv[1])) as $row) {

    if (empty($row)) break;
    $p = explode(",",$row);
    $p2 = explode(':', $p[0]);
    $value[0] = trim($p2[1], '"');
    $p2 = explode(':', $p[1]);
    $value[1] = trim($p2[1], '"');
    $p2 = explode(':', $p[2]);
    $value[2] = trim($p2[1], '"}');

    $binResults = file_get_contents('https://lookup.binlist.net/' .$value[0]);
    if (!$binResults)
        die('error!');
    $r = json_decode($binResults);
    $isEu = isEu($r->country->alpha2);

    $rate = @json_decode(file_get_contents('https://api.exchangeratesapi.io/latest'), true)['rates'][$value[2]];
    if ($value[2] == 'EUR' or $rate == 0) {
        $amntFixed = $value[1];
    }
    if ($value[2] != 'EUR' or $rate > 0) {
        $amntFixed = $value[1] / $rate;
    }

    echo $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);
    print "\n";
}

function isEu($c) {
    $result = false;
    switch($c) {
        case 'AT':
        case 'BE':
        case 'BG':
        case 'CY':
        case 'CZ':
        case 'DE':
        case 'DK':
        case 'EE':
        case 'ES':
        case 'FI':
        case 'FR':
        case 'GR':
        case 'HR':
        case 'HU':
        case 'IE':
        case 'IT':
        case 'LT':
        case 'LU':
        case 'LV':
        case 'MT':
        case 'NL':
        case 'PO':
        case 'PT':
        case 'RO':
        case 'SE':
        case 'SI':
        case 'SK':
            $result = 'yes';
            return $result;
        default:
            $result = 'no';
    }
    return $result;
}
```
## Example `input.txt` file

```
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}

```

## Running the code

Assuming PHP code is in `app.php`, you could run it by this command, output might be different due to dynamic data:
```
> php app.php input.txt
1
0.46180844185832
1.6574127786525
2.4014038976632
43.714413735069

```
