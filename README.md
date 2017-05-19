# Scopes for Laravel Eloquent
This package includes some useful global scopes for Laravel Eloquent.

[Installation](#installation)  
[Traits](#traits)  
[Usage](#usage)  
[Release Notes](#release-notes)  
[Version Compatibility](#version-compatibility)  
[License](#license)

## Installation

### Via Composer
```bash
composer require wowe/laravel-eloquent-scopes
```


## Traits

### ActivatableTrait
When this trait is added to an Eloquent model, it will only return rows which have the `active` field set to `true`.

If you want to use a field with a different name than `active`, set a constant called `ACTIVE` on the Eloquent model to whichever name you would like to use.

The following methods are included with the trait:
- `deactivate`: sets the `active` field to `false`.
- `activate`: sets the `active` field to `true`.
- `withInactive`: will include all entries in results regardless of the value of the `active` field.
- `onlyInactive`: will include only those entries with `active` fields set to `false`.

## Usage

### Add Trait to Model
Add the [`ActivatableTrait`](#activatabletrait) to an Eloquent model.
```php
use Wowe\Eloquent\Scopes\ActivatableTrait;

class Test extends \Eloquent
{
    use ActivatableTrait;

    const ACTIVE = 'is_active';
}


```

### Query Model
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

## Version Compatibility
This Package | Laravel
-------------|--------
1.x.x        | 4.2.x
2.x.x        | 5.0.x
3.x.x        | 5.1.x

License
-------
This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
