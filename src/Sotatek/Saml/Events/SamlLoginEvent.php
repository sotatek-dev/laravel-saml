<?php
namespace Sotatek\Saml\Events;
use Sotatek\Saml\SamlUser;

class SamlLoginEvent{
    protected $user;
    function __construct(SamlUser $user)
    {
        $this->user = $user;
    }
    public function getSamlUser()
    {
        return $this->user;
    }
}