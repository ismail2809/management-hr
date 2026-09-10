<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;

class AccountWidget extends BaseAccountWidget
{
    protected int|string|array $columnSpan = 'full';
}
