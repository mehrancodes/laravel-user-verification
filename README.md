# laravel-user-verification
A simple package to activate the users by token and code number.

This package allows you to verify the users either by token to be sent by email and code number for SMS.

## Installation
This package can be used in Laravel 5.4 or higher.

You can install the package via composer:
```bash
composer require rasulian/laravel-user-verification
```

In Laravel 5.5 the service provider will automatically get registered. In older versions of the framework just add the service provider in config/app.php file:
```php
'providers' => [
    // ...
    Rasulian\Verification\VerificationServiceProvider::class,
];
```

You may add the following aliases to your config/app.php:
```php
'aliases' => [
    // ...
    'Verification' => Rasulian\UserVerification\Facades\Verification::class,
```

Publish the package config and database migration file by running the following command:
```bash
php artisan vendor:publish --provider="Rasulian\Verification\VerificationServiceProvider::class"
```

## Configuration

### Migration
The table representing the user model must be updated with the `verified` column. This update will be performed by the migration included with this package.

**Please make sure that you don't have the this column on your user table.**

If your user table name is not `users`, you may change that in the `config/verification.php`.

Now you can migrate the normal way you do:
```bash
php artisan migrate
```

### Middleware
This package provides an optional middleware throwing `UserVerifiedMiddleware`.
Please refer to the [Laravel Documentation](https://laravel.com/docs/master/errors#the-exception-handler) to learn more
about how to work with the exception handler.

To register the default middleware add the following lines to the `$routeMiddleware` array within the `app/Http/Kernel.php` file:
```php
protected $routeMiddleware = [
    'user.verified' => \Rasulian\UserVerification\Middlewares\UserVerifiedMiddleware::class,
];
```

You may use this middleware for the routes that needs the user's email or phone number be verified:
```php
Route::middleware('auth', 'user.verified')->group(function () {
    // Routes here
});
```

### Errors
This package throws several exception. you may use `try\catch` statement or the Laravel exception handler.

* `UserIsVerifiedException` The given user is already verified
* `UserNotVerifiedException` The given user is not verified
* `VerifyTokenMismatchException` The given token is wrong or not available
* `VerifyCodeMismatchException` The given code is wrong or not available

## Usage

### Route
By default this package provide one route to verify the user by token.
```php
Route::get('user/verification/{token}', 'App\Http\Controllers\Auth\RegisterController@verifyUser')
    ->name('user.verify');
```

#### Overriding package route
To define your own custom routes, put the package service provider call before the `RouteServiceProvider` call in the `config/app.php` file.
```php
/*
 * Package Service Providers...
 */
Rasulian\UserVerification\VerificationServiceProvider::class,

/*
 * Application Service Providers...
 */
App\Providers\RouteServiceProvider::class,
```
Then, add your custom route in your route file.

### Facade
The package offers a facade Verification::.

### verification Config file
After publishing the package config, it will be located at the `config` directory. You are free to change the table name
for the `user` and the `user_verifications` which represents the fields for storing the token and code.

```php
<?php

return [
    'table_names' => [
        'users' => 'users',
        'user_verifications' => 'user_verifications'
    ],

    /**
     * number of hours that needs to pass before
     * we generate a new token\code but only if user request it.
     */
    'generate_after' => 24
];
```

### How to use the package
This package is written as simple as possible.
It creates a token and code for the user and verifies the user either by token or code.

Here is a sample on how to generate a token, send it as an email and verify it.

Edit the `App\Http\Auth\RegisterController` file:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Mail\Welcome;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use Rasulian\UserVerification\Facades\Verification;

class RegisterController extends Controller
{
    //
    // Code
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'verifyUser']);
    }

    //
    // Code
    //

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $token = Verification::getVerificationToken($user);
        $url = route('user.verify', $token);
        Mail::to($user->email)->send(new Welcome($url));

        return redirect('/home')->with('success', 'Registered. Verify your email!');
    }

    public function verifyUser($token)
    {
        $user = Verification::verifyUserByToken($token);

        if (auth()->guest())
            auth()->login($user);

        return redirect('/home')->with('success', 'Your email verified successfully!');
    }
}
```

Here we use the `registered` method to create token and send it,
which will overrides `\Illuminate\Foundation\Auth\RegistersUsers@registered` method.
We get a token for the given user, make it as a url and send it as an email.

If the user clicks on the link, the `verifyUser` method will be executed.
Here we just verify the user by the given token.

**Please make sure that you add the `verifyUser` to the `except` array of the `guest` middleware in the constructor.**

#### Relaunch the process
If you want to regenerate and resend the verification token, you can do this with the `getVerificationToken` method.

The generate method will generate a new token for the given user and change the `verified` column to `false`.

## CONTRIBUTE
Feel free to comment, contribute and help. 1 PR = 1 feature.

## LICENSE
Laravel User Verification is licensed under [The MIT License (MIT)](https://github.com/mehranrasulian/laravel-user-verification/blob/master/LICENSE).