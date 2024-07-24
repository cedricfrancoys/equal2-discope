# Debugging



## Console
To help you with human-readable data, eQual comes with its own UI debug console (which simply reads the `qn_error.log` located  in the `/log` directory).

> Note: `eq_error.log` provides information for each occurring event, so don't forget to delete that file from time to time if you don't want to end up with a huge logs. Especially if you're running a lot of tests.

To access it from the browser:

```url
http://equal.local/console.php
```


There you can find informations about your error, here is an **example** :

```bash
01-05-2022 14:44:43+0.41235100 Warning **@** [`C:\wamp64\www\equal\lib\
equal\orm\Collection.class.php:335`] **in** `equal\orm\Domain::toString()`
: Undefined offset: 1
```



An other **example**, if I did the request :

```
http://equal.local/?get=model_collect
```

The built-in responses, usually already give some informations about the error :

```json
"errors": {
        "MISSING_PARAM": "entity"
}
```

Each Controller tells explicitly which parameters are **required**.



Now, if I check inside the console, it would tell me the same :

```bash
01-06-2022 12:10:17+0.31526700 Warning @ [C:\wamp64\www\equal\run.php:185] in 
{main}(): QN_DEBUG_ORM::MISSING_PARAM - entity
```

An entity is **missing**, if I do add one :

```
http://equal.local/?get=model_collect&entity=core\User
```

I will get a JSON-object with all the users. 


## Debug mode

It is possible to filter the kind of errors that are present in the console, by setting the DEBUG_MODE parameter in your `config.inc.php` file

```
define('DEBUG_MODE', QN_DEBUG_PHP | QN_DEBUG_ORM | QN_DEBUG_SQL | QN_DEBUG_APP);
```

The DEBUG_MODE constant expects a binary mask with the following values : 

|**VALUE**|**MEANING**|
|-|-|
|QN_DEBUG_PHP|The layer is PHP code, the lowest one. **ex** : `trigger_error()`|
|QN_DEBUG_SQL|The layer is SQL errors.|
|QN_DEBUG_ORM|The layer is eQual's [ObjectManager](../architecture-concepts/orm.md). The [validation](./validation.md) method also handles orm errors.|
|QN_DEBUG_APP|The application layer is handled by eQual's [controllers](./controllers.md) (data & actions folders).|

