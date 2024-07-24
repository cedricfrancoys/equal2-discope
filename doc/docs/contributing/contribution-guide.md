# Contributing



## Naming conventions

The languages involved in eQual are varied: JS, TS, PHP, JSON, HTML, whether it's at the back-end or front-end. 

To facilitate cross-stack work, communication between teams, and code reading, the same logic is used for the syntax for variables, constants, classes, functions, and methods.

Indeed, consistently following naming conventions throughout the codebase of packages enhances readability, maintainability, and collaboration among developers.

Here is a summary of the conventions used across the framework:

**1. Scalar Variables & Functions - snake_case**

   - Scalar variables and functions (or var holding an anonymous function) should be named using snake_case, where words are separated by underscores.
     Examples: `$total_count`, `calculate_area()`, `calculate_area()`

**2. Objects - camelCase**

   - Objects (vars holding instances of classes) should be named using camelCase, starting with a lowercase letter and capitalizing subsequent words.
     Examples: `$myObject`, `$userProfile`

**3. Classes - PascalCase**

   - Classes should be named using PascalCase, where each word in the name is capitalized, including the first word.
     Example: `BankAccount`, `UserProfileManager`

**4. Classes Members - camelCase**

   - Class members, including attributes and methods, should follow camelCase convention.
     Example: `$this->myAttribute`, `calculateInterest()`

**5. Entity Properties - snake_case **

   - Entity properties (as defined in `getColumns()` for classes or `params` for controllers), should use snake_case.
     Example: `first_name`, `is_paid`
   - Maps and Objects properties should be named using snake_case
      Example: `{origin_country: "BE", is_shipped: true} `

**6. Controllers - kebab-case**

   - Controllers, typically used in web development frameworks, should be named using kebab-case, where words are separated by hyphens.
     Example: `user-controller.php`, `do-pay.php`



## Commit convention

As commit convention, we adopt Conventional Commits v1.0.0. We have a history of commits that do not assume the convention, but any new commit should follow it.
The commit message should be structured as follows:

```
<type>[(scope)]: <description>

[body]

[footer(s)]
```
(items within square brackets are optional)
[See examples here](https://www.conventionalcommits.org/en/v1.0.0/#examples)

The most important elements in the commit message to communicating the intent to the consumers are the type and the description

### type
* `fix:` type when patching a bug
* `feat:` type when introducing a new feature

Other commonly used types : `build:`, `docs:`, `style:`, `refactor:`, `test:`
Appending `!` after the type means that a breaking change was introduced.

### description
All messages should describe the action performed by the author of the commit (what has been done : "added", "removed", "improved", "corrected").

### examples
```
feat: allowed config object to extend other configs
```
```
feat(api)!: added sending of an email to the customer when a product is shipped
```
```
docs: improved comments
```
```
docs: corrected spelling in CHANGELOG
```



## Coding conventions


Coding conventions guidelines:

* eQual naming conventions should be used;
* reference guidelines of the code language used should be followed;
* significant functionality should come with appropriate testing to be run in the automated test suite;
* licensing guidelines must be followed.



## Licensing Guidelines

The following guidelines apply to all repositories under the eQual organization:

* our Governance's licensing requirements must be respected;
* unless otherwise specified, all code must be licensed under the GNU GPL 3.0;
* each repository must have a `LICENSE` file in its root folder and, when applicable, a `3rdpartylicenses.txt` file;
* if third-party code is used, their licenses must be vetted to ensure compatibility with our licensing requirements.