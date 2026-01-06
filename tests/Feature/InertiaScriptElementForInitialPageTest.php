<?php

use Illuminate\Support\Facades\Config;

it('renders the initial page data in a JSON script element when enabled', function () {
    Config::set('inertia.ssr.enabled', false);
    Config::set('inertia.use_script_element_for_initial_page', true);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<script data-page="app" type="application/json">', false);
    $response->assertSee('<div id="app"></div>', false);
    $response->assertDontSee('<div id="app" data-page="', false);
});
