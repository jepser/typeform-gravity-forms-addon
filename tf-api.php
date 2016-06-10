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
            $request = wp_remote_get($this->url . $endpoint);
        } else {

            $request = wp_remote_post($this->url . $endpoint, [
                'headers'   => [
                    'X-API-TOKEN'   => $this->token,
                    'Content-Type'  => 'application/json'
                ],
                'body'  => json_encode($data)
            ]);
        }

        echo '<pre>'; print_r($data); echo '</pre>';
        echo '<pre>'; print_r(json_encode($data)); echo '</pre>';

        return $request;
    }

    public function getResponse($response)
    {
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return ['error' => "Something went wrong: $error_message"];
        } else {
            return json_decode($response['body']);
        }
    }

    public function getDesignId($data)
    {
        
        $design_args = TypeformHandler::setDesignFields($data);

        // echo '<pre>'; print_r($design_args); echo '</pre>';

        $request = $this->makeCall('/designs', 'POST', $design_args);
        $response = $this->getResponse($request);

        // echo '<pre>'; print_r($response); echo '</pre>';
        // echo '<pre>'; print_r($form_meta[$this->_slug]); echo '</pre>';

        return $response;
    }

    public function getFormId($data, $webhook, $title,  $tags = [])
    {
        $form_fields = TypeformHandler::convertFields($data);

        $forms_args = [
            'title' => $title,
            'tags'  => (!is_array($tags)) ? [$tags]: $tags,
            'webhook_submit_url'    =>  '',//$webhook,
            'fields'    => $form_fields
        ];

        $request = $this->makeCall('/forms', 'POST', $form_args);
        $response = $this->getResponse($request);

        echo '<pre>'; print_r($forms_args); echo '</pre>';
        echo '<pre>'; print_r($request); echo '</pre>';
        echo '<pre>'; print_r($response); echo '</pre>';

        return $response;
    }

    public function parseResponse($response, $type = 'form')
    {

    }
}
