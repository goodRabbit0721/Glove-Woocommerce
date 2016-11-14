<?php
class ConstantcontactApi
{
    private $api_key = 'bftdvjjf64nj34q7mn3zczr8';
    private $access_token;
    private $api_endpoint = 'https://api.constantcontact.com/v2';

    /**
     * Create a new instance
     * @param string $api_key Your Constant Contact API key
     */
    public function __construct($access_token)
    {
        $this->access_token = $access_token;
    }

    public function getLists() {
        return $this->makeRequest('lists');
    }

    public function getListById($listId) {
        $lists = $this->getLists();
        foreach ($lists as $list) {
            if ($list['id'] == $listId) return $list;
        }
    }

    /**
     * Performs the underlying HTTP request. Not very exciting
     * @param  string $method The API method to be called
     * @param  array  $args   Assoc array of parameters to be passed
     * @return array          Assoc array of decoded result
     */
    private function makeRequest($method, $timeout = 10)
    {
        $url = $this->api_endpoint.'/'.$method. '?api_key='. $this->api_key;

        if (function_exists('curl_init') && function_exists('curl_setopt')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '. $this->access_token
            ));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $result    = file_get_contents($url, null, stream_context_create(array(
                'http' => array(
                    'protocol_version' => 1.1,
                    'user_agent'       => 'PHP-MCAPI/2.0',
                    'method'           => 'POST',
                    'header'           => "Content-type: application/json\r\n".
                                          "Authorization: Bearer ". $this->access_token. "\r\n".
                                          "Connection: close\r\n" .
                                          "Content-length: " . strlen($json_data) . "\r\n"
                ),
            )));
        }

        return $result ? json_decode($result, true) : false;
    }
}
