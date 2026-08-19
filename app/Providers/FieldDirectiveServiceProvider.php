<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FieldDirectiveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('field', function (string $expression): string {
            return "<?php
                \$__fieldParams = [{$expression}];
                echo \\App\\Helpers\\FieldHelper::render(
                    \$content ?? [],
                    \$fields ?? [],
                    \$__fieldParams[0],
                    \$__fieldParams[1] ?? null
                );
            ?>";
        });
    }
}
