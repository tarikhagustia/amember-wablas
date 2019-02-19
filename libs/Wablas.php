<?php

class Wablas
{
    const URL = 'https://wablas.com/api';
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function sendMessage(string $phone, string $message)
    {
        $resposne = $this->_HttpRequest('/send-message', [
            'phone' => $phone,
            'message' => $message
        ]);

        return $resposne;
    }

    private function _HttpRequest($endpoint, $data)
    {
        $url = self::URL;

        $ch = curl_init($url . $endpoint); // Initialise cURL
        $post = http_build_query($data); // Encode the data array into a JSON string
        $authorization = "Authorization: " . $this->getToken(); // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization)); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        return json_decode($result); // Return the received data
    }

    protected function getToken()
    {
        return $this->token;
    }
}