<?php

https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=AIzaSyBApKO8bWPA3XdN3ZsKqM2z7p5caTQZD9c

namespace Ibtikar\ShareEconomyToolsBundle\Service;

/**
 * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
 * class to handle firebase requests
 * https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=AIzaSyBApKO8bWPA3XdN3ZsKqM2z7p5caTQZD9c

 */
class GoogleDistanceMatrix
{

    protected $authToken;
    protected $baseUrl;
    protected $units = "metric";

    public function __construct($baseUrl, $authToken)
    {
        if (!$authToken) {
            throw new \Exception('You should set google_distance_matrix_url_base in config.yml');
        }

        if (!$baseUrl) {
            throw new \Exception('You should set google_distance_matrix_key in config.yml');
        }

        $this->authToken = $authToken;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @author Micheal Mouner <micheal.mouner@ibtikar.net.sa>
     * get fair estimate from google
     * @return Json
     */
    public function fareEstimate($longSource, $latSource, $longDestination, $latDestination)
    {
        $params = [
            'key' => $this->authToken,
            'origins' => "$latSource,$longSource",
            'destinations' => "$latDestination,$longDestination",
            'units' => $this->units
        ];
        $url = $this->baseUrl;
        return self::CallAPI('GET', $url, $params);
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
