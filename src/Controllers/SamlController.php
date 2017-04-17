<?php
namespace Hungnguyen\Saml\Controllers;
use Hungnguyen\Saml\Events\SamlLoginEvent;
use Hungnguyen\Saml\Auth;
use Hungnguyen\Saml\SamlAuth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
class SamlController extends Controller
{
    protected $samlAuth;
    /**
     * @param Saml2Auth $saml2Auth injected.
     */
    function __construct(SamlAuth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
    }
    /**
     * Generate local sp metadata
     * @return \Illuminate\Http\Response
     */
    public function metadata()
    {
        $metadata = $this->samlAuth->getMetadata();
        return response($metadata, 200, ['Content-Type' => 'text/xml']);
    }
    /**
     * Process an incoming saml2 assertion request.
     * Fires 'Saml2LoginEvent' event if a valid user is Found
     */
    public function acs()
    {
        $errors = $this->samlAuth->acs();
        if (!empty($errors)) {
            logger()->error('Saml2 error_detail', ['error' => $this->samlAuth->getLastErrorReason()]);
            session()->flash('saml2_error_detail', [$this->samlAuth->getLastErrorReason()]);
            logger()->error('Saml2 error', $errors);
            session()->flash('saml2_error', $errors);
            return redirect(config('saml2_settings.errorRoute'));
        }
        $user = $this->samlAuth->getSamlUser();
        event(new SamlLoginEvent($user));
        $redirectUrl = $user->getIntendedUrl();
        if ($redirectUrl !== null) {
            return redirect($redirectUrl);
        } else {
            return redirect(config('saml2_settings.loginRoute'));
        }
    }
    /**
     * Process an incoming saml2 logout request.
     * Fires 'saml2.logoutRequestReceived' event if its valid.
     * This means the user logged out of the SSO infrastructure, you 'should' log him out locally too.
     */
    public function sls()
    {
        $error = $this->samlAuth->sls(config('saml2_settings.retrieveParametersFromServer'));
        if (!empty($error)) {
            throw new \Exception("Could not log out");
        }
        return redirect(config('saml2_settings.logoutRoute')); //may be set a configurable default
    }
    /**
     * This initiates a logout request across all the SSO infrastructure.
     */
    public function logout(Request $request)
    {
        $returnTo = $request->query('returnTo');
        $sessionIndex = $request->query('sessionIndex');
        $nameId = $request->query('nameId');
        $this->samlAuth->logout($returnTo, $nameId, $sessionIndex); //will actually end up in the sls endpoint
        //does not return
    }
    /**
     * This initiates a login request
     */
    public function login()
    {
        $this->samlAuth->login(config('saml2_settings.loginRoute'));
    }
}