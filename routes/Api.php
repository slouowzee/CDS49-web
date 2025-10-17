<?php

namespace routes;

use routes\base\Route;
use controllers\MobileApiController;

class Api
{
    function __construct()
    {
        $mobileApiController = new MobileApiController();

        Route::Add('/api', [$mobileApiController, 'index']);
        Route::Add('/api/', [$mobileApiController, 'index']);
        Route::Add('/api/login', [$mobileApiController, 'login']);
		Route::Add('/api/register', [$mobileApiController, 'register']);
        Route::Add('/api/profile/get', [$mobileApiController, 'getProfile']);
        Route::Add('/api/profile/update', [$mobileApiController, 'updateProfile']);
        Route::Add('/api/questions/{n}', [$mobileApiController, 'getQuestions']);
        Route::Add('/api/fin-test', [$mobileApiController, 'saveScore']);
    }
}
