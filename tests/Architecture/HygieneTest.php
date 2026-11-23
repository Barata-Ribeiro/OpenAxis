<?php

arch('no debug calls')
    ->expect('app')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump', 'die', 'exit', 'print_r', 'printf', 'vprintf', 'trigger_error']);

arch('no env outside of config')
    ->expect('app')
    ->not->toUse('env');
