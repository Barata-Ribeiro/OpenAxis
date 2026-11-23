<?php

declare(strict_types=1);

arch('no debug calls')
    ->expect(value: 'app')
    ->not->toUse(targets: ['dd', 'ddd', 'dump', 'ray', 'var_dump', 'die', 'exit', 'print_r', 'printf', 'vprintf', 'trigger_error', 'sleep']);

arch('no env outside of config')
    ->expect(value: 'app')
    ->not->toUse(targets: 'env');
