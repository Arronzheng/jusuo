<?php

namespace App\Providers;

use App\Validators\PhoneValidator;
use App\Validators\UsernameValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


class AppServiceProvider extends ServiceProvider
{
    protected $validators = [
        'phone' => PhoneValidator::class,
        'login_username' => UsernameValidator::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->registerValidators();
        \Carbon\Carbon::setLocale('zh');
    }

    protected function registerValidators()
    {
        foreach ($this->validators as $rule => $validator) {
            Validator::extend($rule, "{$validator}@validate");
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
