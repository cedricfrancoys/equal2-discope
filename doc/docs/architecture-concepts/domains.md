# Domains

A Domain is a structure that describes of a series of conditions, and is used for creating filtered lists of objects.

Domains are widely used in eQual and are involved in most controllers and views. 



## Concept definition

The purpose of a domain is to define a logical filter targeting all objects that comply to a series of criteria.

To represent such a filter, we take advantage of the fact that, whatever the logical configuration, it is always possible to convert it to a list of operands on which the OR operator is applied (disjunction).

Here below are some Math equivalences to illustrate this (`∨` = OR, `∧` =  AND):

```
(A ∨ B) ∧ (C ∨ D) = (A ∧ C) ∨ (A ∧ D) ∨ (B ∧ C) ∨ (B ∧ D)
```

In turn, each operand can be either a direct value or a conjunction of operands (logical AND).

In other words, a domain is a structure that represents a list of "disjunctions (OR) of conjunctions (AND)".

Based on that fact, it is possible to build a domain in an incremental way.

To help with domain manipulations, eQual offers a `Domain` class (`lib\equal\orm\Domain.class.php`) that both holds helper methods and allows to instantiate OOP modelization of domains.



## Structure

A domain is a list of one or more clauses, each clause consisting of one or more conditions.

The minimal domain being a single clause with a single condition.

### Condition

A condition is represented as an array holding 3 elements:

```
[ operand, operator, value ]
```

| **PART**  | **ROLE**                            |
| --------- | ----------------------------------- |
| operand   | Indicates the field on which the condition must be applied and matches one of the properties returned by `Model::getColumns()`. |
| operator  | Provides the operator of the condition. Accepted operators are : `=`, `<`, `>`,` <=`, `>=`, `<>`, `like`, `ilike` (case-insensitive), `in`, `contains`. |
| value  | Provides the value against which the operator must be applied on the operand. |

!!! note "About value type"
    The operator influences the type of the value (e.g.: 'in' implies that the value is an array).


### Clause

A clause is a series or conditions on which the AND operator is applied.

```
[ [ operand1, operator1, value1 ], [ operand1, operator1, value2 ] ]
```



For instance, the below notation can be interpreted as "*field1 must be equal to 1 AND field2 must be equal to 2*":

```
[ [ "field1", "=" , 1 ], [ "field2", "=", 2 ] ]
```



## Notation

The complete domain syntax is : 

``` 
[ [ [operand, operator, value], {...} ], {...} ]
```



!!! note "Shortcut notations"
    eQual accepts shortcut notations : `[operand, operator, value]` and `[[operand, operator, value]]` (two pairs of square brackets) will both be interpreted as a domain with a single clause and a single condition.



Here is a commented recap of the syntax : 


```php
<?php
$domain = [                                        // domain
	[                                              // clause
		[
			'{operand}', '{operator}', '{value}'   // condition
		],
		[
			'{operand}', '{operator}', '{value}'   // another contition (AND)
		]
	],
	[		                                       // another clause (OR)
		[
			'{operand}', '{operator}', '{value}'   // condition
		],
		[
			'{operand}', '{operator}', '{value}'   // another contition (AND)
		]
	]
];
```



One advantage of using the array notation is that the syntax is very similar in PHP controllers and in JSON views.

```php
<?php
$domain = [
	["login", "like", "%@equal.run"],
	["validated", "=", true]
];
// note : in PHP, single quote and double quote are equivalent
```
```json
{
	"domain": [
        ["login", "like", "%@equal.run"],
        ["validated", "=", true]
    ]
}
```



Domains can be used in URL (must be URL encoded).

Example:
```http
http://equal.local/?get=model_search&entity=core\User&domain=[[id,in,[1,2,3]]
```

## References

A few references can be used in the domain to improve the search.

| **REFERENCE** | **DESCRIPTION**     | **EXAMPLE**                                  |
| ------------- | ------------------- | -------------------------------------------- |
| object.       | The current object. | `["object_id", "=", "object.id"]`            |
| user.         | The current user.   | `["creator", "=", "user.id"]`                |
| date          | The current date.   | `["date_from", "=", "date.this.year.first"]` |





### Date References

Dates references can be used in domains to define a filter based on a date relative to the date at which the request is made.

Example: `date.this.year.first` (first day of the current year at 00:00 UTC)

Generic format of a date reference: `date.{origin}.{interval}[.{offset}]`

Possible values are:  

| **PART** | **VALUES** |
| :-- | :-- |
|origin|`prev`, `next`, `this`|
|interval|`day`, `week`, `month`, `quarter`, `semester`, `year`|
|offset|`first`, `last`|

Examples:  

* today's date: `date.this.day`
* first day of the current month: `date.this.month.first`
* last day of the previous month: `date.prev.month.last`
* last day of the current year: `date.this.year.last`
* first day of the next year: `date.next.year.first`
