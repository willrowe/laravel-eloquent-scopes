Scopes for Laravel Eloquent
===========================
This package includes some useful global scopes for Laravel Eloquent. It currently includes the `Activatable` scope.

[Installation](#installation)  
[Traits](#traits)
[Usage](#usage)  
[Release Notes](#release-notes)  
[Version Compatibility](#version-compatibility)  
[License](#license)  

Installation
------------
1. Add the package to your project's `composer.json` file from the command line:  
    `composer require wowe/laravel-eloquent-scopes`

Traits
------
###ActivatableTrait###
This trait will cause the Eloquent model to return only entries which have their `active` field set to `true` (or equivalent). If you want to use a field with a different name than `active`, set a constant called `ACTIVE` on the Eloquent model to what name you would like to use.  
The following methods are included with the trait:  
- `deactivate`: sets the `active` field to `false`.
- `activate`: sets the `active` field to `true`.
- `withInactive`: will include all entries in results regardless of the value of the `active` field.
- `onlyInactive`: will include only those entries with `active` fields set to `false`.

Usage
-----
###Add Trait to Model###
Add the `ActivatableTrait` to an Eloquent model and you will have access to all the methods described [here](#activatabletrait)
```php
use Wowe\Eloquent\Scopes\ActivatableTrait;

class Test extends \Eloquent
{
    use ActivatableTrait;

    const ACTIVE = 'is_active';
}


```

###Query Model###
```php
// Get all entries that are active
Test::all();

// Get a single entry only if active
Test::find(1);

// Get all entries
Test::withInactive()->get();

// Get all inactive entries
Test::inactiveOnly()->get();
```

Version Compatibility
---------------------
Laravel | Response Cache
--------|---------------
4.2.x   | 1.0.x

License
-------
This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)