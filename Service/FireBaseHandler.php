<?php

namespace Ibtikar\ShareEconomyToolsBundle\Service;

/**
 * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
 * class to handle firebase requests 
 */
class FireBaseHandler
{

    protected $authToken;
    protected $baseUrl;

    public function __construct($baseUrl, $authToken)
    {
        if (!$authToken) {
            throw new \Exception('You should set firebase_database_secret in config.yml');
        }

        if (!$baseUrl) {
            throw new \Exception('You should set firebase_url_base in config.yml');
        }
        
        $this->authToken = $authToken;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * find all users in the database
     * @return Json
     */
    public function findAll()
    {
        $url = $this->baseUrl . "/users.json?auth=" . $this->authToken;
        return self::CallAPI('GET', $url);
    }

    /**
     * get specific user from firebase DB
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * @param String $id
     * @return Json
     */
    public function findOne($id)
    {
        $url = $this->baseUrl . "/users/$id.json?auth=" . $this->authToken;
        return self::CallAPI('GET', $url);
    }

    /**
     * send general message for all users
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * @param String $message
     * @return Json
     */
    public function sendGeneralNotification($message)
    {
        $data = array(
            'message' => $message,
        );
        $time = time();
        $url = $this->baseUrl . "/general/$time.json?auth=" . $this->authToken;
        return self::CallAPI('PUT', $url, $data);
    }

    /**
     * send notification message for specific user
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * @param Strine $id
     * @param String $message
     * @return Json
     */
    public function sendNotification($id, $message)
    {
        $data = array(
            'message' => $message,
        );
        $time = time();
        $url = $this->baseUrl . "/users/$id/$time.json?auth=" . $this->authToken;
        return self::CallAPI('PUT', $url, $data);
    }

    /**
     * delete notification from specific user
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * @param String $id
     * @param Int $time
     * @return Json
     */
    public function deleteNotification($id, $time)
    {

        $url = $this->baseUrl . "/users/$id/$time.json?auth=" . $this->authToken;
        return self::CallAPI('DELETE', $url);
    }

    /**
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * delete general notification message
     * @param Int $time
     * @return Json
     */
    public function deleteGeneralNotification($time)
    {

        $url = $this->baseUrl . "/general/$time.json?auth=" . $this->authToken;
        return self::CallAPI('DELETE', $url);
    }

    /**
     * Curl PUT-POST-GET-DELETE
     * @param String $method
     * @param String $url
     * @param Array $data // Data: array("param" => "value") ==> index.php?param=value   
     * @return Json
     */
    protected function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;

            case "DELETE":
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        //send data for post and put requests
        if ($data && ($method == "POST" || $method == "PUT")) {
            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}
