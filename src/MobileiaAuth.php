<?php

namespace MobileIA\Auth;

use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Zend\Json\Json;

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
    const BASE_URL = 'http://auth.mobileia.com/';
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
     * Almanena los datos del usuario que se verifico el AccessToken
     * @var array
     */
    protected $current = null;
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
        // Creamos la peticion con los parametros necesarios
        $request = $this->generateRequest('token/valid', array(
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'access_token' => $access_token
        ));
        // Ejecutamos la petición
        $response = $this->dispatchRequest($request);
        // Verificamos si se ha encontrado un error
        if(isset($response->status) && $response->status == 422){
            return false;
        }
        // El Access Token es valido Guardamos los datos del usuario
        $this->current = $response;
        // La respuesta es correcta
        return true;
    }
    /**
     * Devuelve el UserID del ultimo accessToken validado.
     * @return int
     */
    public function getCurrentUserID()
    {
        // Verificar si ya se valido un access_token
        if(!is_array($this->current) && !is_object($this->current)){
            return 0;
        }
        // Devolver el UserID del usuario que se verifico el Access Token
        return $this->current->id;
    }
    
    public function getDevicesToken($ids)
    {
        // Creamos la peticion con los parametros necesarios
        $request = $this->generateRequest('devices', array(
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'users' => implode(',', $ids)
        ));
        // Ejecutamos la petición
        $response = $this->dispatchRequest($request);
        // Verificamos si se ha encontrado un error
        if(isset($response->status) && $response->status == 422){
            return false;
        }
        // Devolvemos los datos
        return $response;
    }
    
    public function authenticate($email, $password)
    {
        // Creamos la peticion con los parametros necesarios
        $request = $this->generateRequest('oauth', array(
            'grant_type' => 'password',
            'app_id' => $this->appId,
            //'app_secret' => $this->appSecret,
            'email' => $email,
            'password' => $password
        ));
        // Ejecutamos la petición
        $response = $this->dispatchRequest($request);
        // Verificamos si se ha encontrado un error
        if(isset($response->status) && ($response->status == 422 || $response->status == 401)){
            return false;
        }
        // Devolvemos los datos
        return $response;
    }
    
    /**
     * Realiza la peticion y devuelve los parametros
     * @param Request $request
     * @return array
     */
    protected function dispatchRequest($request)
    {
        $client = new Client();
        $response = $client->dispatch($request);
        return Json::decode($response->getBody());
    }
    /**
     * Genera un request con el path y los parametros
     * @param string $path
     * @param array $params
     * @return Request
     */
    protected function generateRequest($path, $params)
    {
        $request = new Request();
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ));
        $request->setUri(self::BASE_URL . $path);
        $request->setMethod(Request::METHOD_POST);
        $request->setContent(Json::encode($params));
        $request->setPost(new Parameters($params));
        
        return $request;
    }
}
