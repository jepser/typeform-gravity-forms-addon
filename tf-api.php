<?php

class TypeformApi
{
    public function __construct($token)
    {
        $this->url = 'https://api.typeform.io/latest';
        $this->token = $token;

    }

    public function makeCall($endpoint = '', $method = 'GET', $data = [])
    {
        if ($method == 'GET') {
            $request = wp_remote_get($this->typeform_url . $endpoint);
        } else {
            $request = wp_remote_post($this->typeform_url . $endpoint, [
                'headers'   => [
                    'X-API-TOKEN'   => $this->token
                ],
                'body'  => json_encode($data)
            ]);
        }

        return $request;
    }

    public function getResponse($response)
    {
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return ['error' => "Something went wrong: $error_message"];
        } else {
            return json_decode($design_response['body']);
        }
    }

    public function getDesignId($data)
    {
        
        $design_args = TypeformHandler::setDesignFields($data);

        // echo '<pre>'; print_r($design_args); echo '</pre>';

        $request = $this->makeCall('/designs', 'POST', $design_args);
        $response = $this->getResponse($request);

        return $response;
        // echo '<pre>'; print_r($design_data); echo '</pre>';
        // echo '<pre>'; print_r($form_meta[$this->_slug]); echo '</pre>';
    }

    public function getFormId($data)
    {
        $form_args = TypeformHandler::convertFields($data);
        $request = $this->makeCall('/forms', 'POST', $form_args);
        $response = $this->getResponse($request);

        return $response;
    }

    public function parseResponse($response, $type = 'form')
    {

    }
}
