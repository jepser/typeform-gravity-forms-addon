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

        $request = $this->makeCall('/designs', 'POST', $design_args);
        $response = $this->getResponse($request);

        if (isset($response->id)) {
            return $response;
        } else {
            throw new Exception(__('No required fields for design'));
        }
        
    }


    public function getFormId($data, $webhook, $title, $tags = [], $design_id = null)
    {

        $form_fields = TypeformHandler::convertFields($data);

        $form_args = [
            'title' => $title,
            'tags'  => $tags,
            'webhook_submit_url'    =>  $webhook,
            'fields'    => $form_fields
        ];

        if ($design_id != null) {
            $form_args['design_id'] = $design_id;
        }
        
        $request = $this->makeCall('/forms', 'POST', $form_args);
        $response = $this->getResponse($request);

        return $response;
    }

    public function parseResponse($response, $type = 'form')
    {

    }
}
