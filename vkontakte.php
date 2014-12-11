<?php
/**
*
* @package auth
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace aler\vk_oauth;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}


/**
* Bitly OAuth service
*
* @package auth
*/
class vkontakte extends \phpbb\auth\provider\oauth\service\base
{
    /**
    * phpBB config
    *
    * @var phpbb_config
    */
    protected $config;

    /**
    * phpBB request
    *
    * @var phpbb_request
    */
    protected $request;

    /**
    * Constructor
    *
    * @param    phpbb_config     $config
    * @param    phpbb_request     $request
    */
    public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request)
    {
        $this->config = $config;
        $this->request = $request;

    }

    /**
    * {@inheritdoc}
    */
    public function get_service_credentials()
    {
        return array(
            'key'        => $this->config['auth_oauth_vkontakte_key'],
            'secret'    => $this->config['auth_oauth_vkontakte_secret'],
        );
    }

    /**
    * {@inheritdoc}
    */
    public function perform_auth_login()
    {
        if (!($this->service_provider instanceof \OAuth\OAuth2\Service\Vkontakte))
        {
            throw new phpbb\auth\provider\oauth\service\exception('AUTH_PROVIDER_OAUTH_ERROR_INVALID_SERVICE_TYPE');
        }

        // This was a callback request from vkontakte, get the token
        $token = $this->service_provider->requestAccessToken($this->request->variable('code', ''));
	$vk_extraparam = $token->getExtraParams();

        // Send a request with it
        $result = json_decode($this->service_provider->request('users.get?user_ids='.$vk_extraparam['user_id'].'&fields=domain,timezone,connections,bdate'), true);

        // Return the unique identifier returned from vkontakte

        return $result['response'][0]['uid'];
    }

    /**
    * {@inheritdoc}
    */
    public function perform_token_auth()
    {
        if (!($this->service_provider instanceof \OAuth\OAuth2\Service\Vkontakte))
        {
            throw new phpbb\auth\provider\oauth\service\exception('AUTH_PROVIDER_OAUTH_ERROR_INVALID_SERVICE_TYPE');
        }
	$storage = $this->service_provider->getStorage();
	$token =  $storage->retrieveAccessToken($this->service_provider->service());

	$vk_extraparam = $token->getExtraParams();

        // Send a request with it
        $result = json_decode($this->service_provider->request('users.get?user_ids='.$vk_extraparam['user_id'].'&fields=domain,timezone,connections,bdate'), true);

        // Return the unique identifier returned from vkontakte
        return $result['response'][0]['uid'];
    }
}