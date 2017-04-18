<?php
namespace Sotatek\Saml\Facades;
use Illuminate\Support\Facades\Facade;
class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Sotatek\Saml\SamlAuth';
    }
} 