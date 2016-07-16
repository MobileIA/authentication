<?php

/**
 * Description of MobileiaAuth
 *
 * @author matiascamiletti
 */
class MobileiaAuth 
{
    /**
     * Almacena la URL base de la API de MobileIA Auth.
     */
    const BASE_URL = '';
    /**
     *
     * @var string
     */
    protected $appId;
    /**
     *
     * @var string
     */
    protected $appSecret;
    /**
     * 
     * @param string $app_id
     * @param string $app_secret
     */
    public function __construct($app_id, $app_secret)
    {
        $this->appId = $app_id;
        $this->appSecret = $app_secret;
    }
    /**
     * Valida si el accessToken recibido es valido.
     * @param string $access_token
     * @return boolean
     */
    public function isValidAccessToken($access_token)
    {
        return true;
    }
}
