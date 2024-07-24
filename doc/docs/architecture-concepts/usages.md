# Usages

Usages is a that can be used to define all at once : how to store a value, how to convert it from one context to
another, how to validate it and how to represent it in the UI.

Types are aliases representing specific usages.

* A field is defined based on a descriptor that always contains a type and possibly a usage.
* Usage takes precedence over type.
* A field always has an unambiguous resulting usage.

Services based on the DataAdapter interface are used to convert data to and from PHP (from the perspective of PHP).

Adapters are capable of bidirectional conversion of a value based on its usage:

| Conversion |                         |
|------------|-------------------------|
| x   => PHP | `adaptIn(value, usage)` |
| PHP => x   | `adaptOut(value, usage  |type)` can be applied on whole Collection with ::adapt(dest)|

* The objects manipulated in controllers and classes contain values using PHP types.
* Collections can be exported by recursively converting the values during export (`get()`):
  `adapter->adaptOut($value, $f->getUsage())`

Non-exhaustive list of supported Usages :

```
text/plain
text/html
amount/money:4
amount/money:4
amount/percent
amount/rate
numeric/hexadecimal:1
numeric/integer
numeric/integer:3
image/jpeg
email
phone
password
password/nist
date/month
date/year
date/year:4
country/iso-3166:2
language/iso-639
uri/url
uri/urn.iban
uri/urn.ean
```