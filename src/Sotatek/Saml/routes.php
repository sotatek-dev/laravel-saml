<?php

Route::group([
    'prefix' => config('saml.routesPrefix'),
    'middleware' => config('saml.routesMiddleware')
], function () {
    Route::get('/logout', array(
        'as' => 'saml_logout',
        'uses' => 'Sotatek\Saml\Controllers\SamlController@logout',
    ));

    Route::get('/metadata', array(
        'as' => 'saml_metadata',
        'uses' => 'Sotatek\Saml\Controllers\SamlController@metadata',
    ));

    Route::get('/login', array(
        'as' => 'saml_login',
        'uses' => 'Sotatek\Saml\Controllers\SamlController@login',
    ));

    Route::post('/acs', array(
        'as' => 'saml_acs',
        'uses' => 'Sotatek\Saml\Controllers\SamlController@acs',
    ));
    Route::get('/sls', array(
        'as' => 'saml_sls',
        'uses' => 'Sotatek\Saml\Controllers\SamlController@sls',
    ));
});
