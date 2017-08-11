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
    const BASE_URL = 'http://authentication.mobileia.com/api/';
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
        try {
            // Ejecutamos la petición
            $response = $this->dispatchRequest($request);
        } catch (\RuntimeException $exc) {
            return false;
        }
        // Verificamos si se ha encontrado un error
        if(!$response->success){
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
    /**
     * Elimina un usuario.
     * @param int $id ID del usuario a eliminar
     * @return boolean
     */
    public function removeUser($id)
    {
        // Creamos la peticion con los parametros necesarios
        $request = $this->generateRequest('user/remove', array(
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'user_id' => $id
        ));
        // Ejecutamos la petición
        $response = $this->dispatchRequest($request);
        // Verificamos si se ha encontrado un error
        if(!$response->success){
            return false;
        }
        return true;
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
        if(!$response->success){
            return false;
        }
        // Devolvemos los datos
        return $response;
    }
    /**
     * Devuelve un array solo con los deviceToken de los dispositivos para enviar push.
     * @param array $ids Array de MIA IDs para buscar dispositivos
     * @return array
     */
    public function getDevicesTokenOnly($ids)
    {
        $devices = $this->getDevicesToken($ids);
        // Almacena los tokens
        $tokens = array();
        // Recorremos los dispositivos
        foreach($devices as $d){
            $tokens[] = $d->device_token;
        }
        // Devolvemos el array
        return $tokens;
    }
    
    public function authenticate($email, $password)
    {
        // Creamos la peticion con los parametros necesarios
        $request = $this->generateRequest('oauth', array(
            'grant_type' => 'normal',
            'app_id' => $this->appId,
            //'app_secret' => $this->appSecret,
            'email' => $email,
            'password' => $password
        ));
        // Ejecutamos la petición
        $response = $this->dispatchRequest($request);
        // Verificamos si se ha encontrado un error
        if(!$response->success){
            return false;
        }
        // Devolvemos los datos
        return $response->response;
    }
    
    /**
     * Realiza la peticion y devuelve los parametros
     * @param Request $request
     * @return array
     */
    protected function dispatchRequest($request)
    {
        $client = new Client();
        try {
            $response = $client->dispatch($request);
        } catch (\Zend\Http\Client\Adapter\Exception\RuntimeException $exc) {
            $object = new \stdClass();
            $object->success = false;
            return $object;
        }
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
