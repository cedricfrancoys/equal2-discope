# Testing

Before anything, please check your package consistency :

|**PATH**|`core\actions\test\package-consistency.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=test_package-consistency&package=myPackage`|
|**CLI**|`$ ./equal.run --do=test_package-consistency --package=myPackage`|
|**DESCRIPTION**|Consistency checks between DB and class as well as syntax validation for classes (PHP), views and translation files (JSON).|

It will tell you if you have available tests. If it's not the case, see [**Writing the tests**](#writing-the-tests)



## Running the tests locally

Open your CLI at the root of eQual (where run.php is), then launch this :

```bash
php run.php --do=test_package --package=mypackage
```

It should log an overview of each test you wrote, resulting as "ok" or "ko" depending on the expected results

> If it doesn't show anything it means there is an error somewhere in your code
>
> Tip: use http://[localhost]**/console.php** for potential insights

### Test data set

If you want to put your database in a certain state before running the tests, then you need a data set.

> Refer to section [Building the data set](#building_the_data_set) below to know how to proceed

If you already have one, you can enter this before the test command :

|**PATH**|`core\actions\init\package.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=init_package&package=myPackage`|
|**CLI**|`$ ./equal.run --do=init_package --package=myPackage`|
|**DESCRIPTION**|Initialise database for given package. If no package is given, initialize core package.|



## Running the tests in a CI/CD environment

There are many ways out there you can choose from, for example you can use BitBucket Pipelines, GitLab CI, or any versioning platform with a compatible pipeline tool

Here we'll use GitHub with Travis CI

> To learn how to setup a testing environment with Travis, see [Advanced > CI/CD > Travis](../advanced/ci-cd.md)

Assuming you have a data set ready for your tests, add this in the **script** section of your **.travis.yml** file :

```yaml
# initate data set
- php run.php --do=init_package --package=mypackage
# run tests
- php run.php --do=test_package --package=mypackage
```

> Note: if the tests succeed in local but fail with Travis, try to change your Http request with **localhost** as host instead of 127.0.0.1 or any localhost name you're using



## Building the data set <a name="building_the_data_set"></a>

At some point in your testing you're very likely to operate changes in your database, which may aswell make a few of your previous tests obsolete

To avoid this, we use a data set that we initiate everytime we want to run tests

### Steps

1) In MySQL :

- Write the data you're gonna use for your tests

- When you're happy with it, click on your database, go to the **Export** tab, select **Personalized** and unpick the tables you don't want to use, then press **Execute**

2) It should give you a **.sql** file named after your database, go ahead and rename it **data.sql**

3) In your **package directory** (public/packages/mypackage/) :

- Create a folder named **/init**
- Move **data.sql** into this folder

4) Open **data.sql**. We're going to update it to make it compatible with eQual :

- Remove anything that doesn't start with "**INSERT INTO**"
- Manually adapt each insertion :

For example, instead of

```mysql
INSERT INTO `my_table` (`field1`, `field2`)
VALUES
('value1','value1'),
('value2','value2'),
('value3','value3');
```

Write it like that

```mysql
INSERT INTO `my_table` (`field1`, `field2`) VALUES ('value1','value1');
INSERT INTO `my_table` (`field1`, `field2`) VALUES ('value2','value2');
INSERT INTO `my_table` (`field1`, `field2`) VALUES ('value3','value3');
```

> Note: you can add "IGNORE" between INSERT and INTO to avoid future problems

That's it.

To see if it works, run the following CLI command to initiate your data set

```bash
php run.php --do=init_package --package=mypackage
```

And compare in MySQL if you get the data you expected

From now on, you can run the command above everytime you want to reinitialize your DB state



## Writing the tests

The testing folder should be located at the root of each package, under the name **/tests**

In this folder add a file **mytests.php** (or whatever name you want)

Here is a minimal template for test-writing :

```php
// \tests\mytests.php
<?php
use equal\http\HttpRequest;
use myapp\Myobject
    
$providers = inject(['context']);

$tests = [
    '0001' => [
        'description'   =>  '',	// what the test does
        'return'        =>  [],	// expected value type
        'expected'      =>  [],	// expected result of 'test'
        'test'          =>  function(){
            					// testing goes here
                            },
        'assert'		=> function($assertion){
            					// Checks if assertion is false
        }
    ],
    // working example
    '0002' => [
        'description'   =>  'checking test_value',
        'return'        =>  ['string'], 
        'expected'      =>  "test",
        'test'          =>  function(){
            					$test_value = "test"
            					return $test_value;
                            },
        'assert'		=> function($assertion){
            					// Checks if assertion is false
        }
    ],
	// [...] add as many as you want!
]
```

There is 2 additional parameters (they have to fit right between 'expected' and 'test') you can use in case you need to run some specific function before and after a test :

- **'arrange' => function(){  }**    (used to do something before the test)
- **'rollback' => function(){  }**    (used to do something after the test)

**Important :** Keep in mind that 'arrange' doesn't trigger if the test fails or doesn't match with the 'expected' field



### Syntax details

The type of value expected in **'return'** must be in array (ex: ['integer'], ['boolean'], ['array'] ... )

The **'expected'** field value can be anything **but null**

The **'test'** field is where the logic is written, it has to end with a **return**. This returned value will go through the built-in function test() which tells us whether it's "ok" or "ko"

Let's have a look at **Tester.class.php** to understand how the testing works :

```php
<?php

public function test() {
// [...]
    $result = $test['test']();
    $status = 'ok';
    
    if(in_array(gettype($result), (array) $test['return'])) {				// (1)
      if(isset($test['expected'])) {										// (2)
        if(gettype($result) == gettype($test['expected'])) {				// (4)
          if(gettype($result) == "array") {									// (5)
            if(!self::array_equals($result, $test['expected'])) {			// (6)
              $success = false;
            }
          }
          else {
            if($result != $test['expected']) {								// (7)
              $success = false;
            }
          }
        }
        else {
          $success = false;
        }
      }
      else if(isset($test['assert']) && is_callable($test['assert'])) {		// (3)
        $success = $test['assert']($result);
      }
    }
    else {
      $success = false;
    }
// [...]
}
```

**What it does :**

Store the 'test' function results in $result, and tell $status = 'ok' by default

Cascading logic :

- **(1)** If $result and 'return' value are respectively inside an array (if 'return' isn't, do so automatically)
- **(2)** If the 'expected' field has been set. If not, **(3)** Check if there is an 'assert' field available
- **(4)** If the type of $result is the same as 'return'
- **(5)** If $result is of type "array", **(6)** Call the array_equals() method to verify if $result and 'expected' are identical. If not, **(7)** Use a simpler logic to do the verification

'$success = false' is the equivalent of '$status = ko'

