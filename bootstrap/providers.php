<?php

use App\Providers\AppServiceProvider;
use App\Providers\FieldDirectiveServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\JetstreamServiceProvider;

return [
    AppServiceProvider::class,
    FieldDirectiveServiceProvider::class,
    FortifyServiceProvider::class,
    JetstreamServiceProvider::class,
];
