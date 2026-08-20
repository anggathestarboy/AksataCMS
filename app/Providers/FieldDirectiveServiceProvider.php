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
                    \$__fieldParams[1] ?? (\$item ?? null)
                );
            ?>";
        });

        Blade::directive('repeater', function (string $expression): string {
            return "<?php
                \$__repeaterItems = data_get(\$content ?? [], {$expression}, []);
                if (count(\$__repeaterItems) > 0):
                    \$__parentFields = \$fields ?? [];
                    \$__repeaterDef = collect(\$__parentFields)->firstWhere('key', {$expression});
                    \$fields = \$__repeaterDef['fields'] ?? [];
                    foreach (\$__repeaterItems as \$item):
            ?>";
        });

        Blade::directive('endrepeater', function (): string {
            return '<?php
                    endforeach;
                    $fields = $__parentFields ?? [];
                endif;
            ?>';
        });
    }
}
