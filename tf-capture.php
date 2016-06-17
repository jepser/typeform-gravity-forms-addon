<?php

class TypeformWebHook
{

    const SLUG = 'typeform-wh';
    const TFID = 'typeform-id';

    public function registerHooks()
    {
        $this->captureEndpoint();

        add_action('template_redirect', [$this, 'getTypeformResponse']);
    }

    public function getTypeformResponse()
    {
        if ($this->isEndpointCalled()) {
            try {
                $data = $this->getAndSaveData();
                wp_send_json_success($data);
            } catch (Exception $e) {
                wp_send_json_error($e->getMessage());
            }
        }
    }

    private function getAndSaveData()
    {
        $data = $this->getResponseData();
        $form_id = $this->getFormId();

        if ($this->verifyResponse($form_id, $data)) {

        } else {
            throw new Exception(__('No verificable data response'));
        }
        $this->setData($form_id, $data);
    }

    private function setData($form_id, $data)
    {

        $parsed_data = $this->parseData($data);
        // update_option('typeform-data', $data);
    }

    private function parseData($data)
    {
        $answers = $data->answers;
        echo '<pre>'; print_r($data); echo '</pre>';
        
        die();
    }

    private function verifyResponse($form_id, $data)
    {
        $form_data = $this->getFormData($form_id);
        echo '<pre>'; print_r($form_data); echo '</pre>';
        
        // if(isset($settings['form-id']) && $settings['form-id'] == $data->) {

        // }
        return true;

    }

    private function getFormData($form_id)
    {
        $gf = new GFAPI();
        $form = $gf->get_form($form_id);
        return [
            'settings'  => $form[GFTypeformAddon::ADDON_SLUG],
            // 'data'      => $form,
            'entries'   => $gf->get_entries($form_id)
        ];
    }

    public function getFormId()
    {
        if (!isset($_GET[self::TFID])) {
            throw new Exception(__('No typeform id'));
        }
        return $_GET[self::TFID];
    }

    public function getResponseData()
    {
        // $typeform_data = file_get_contents('php://input');

        $typeform_data = get_option('typeform-data');
        if (!isset($typeform_data)) {
            throw new Exception(__('No typeform data'));
        }
        return json_decode($typeform_data);
    }

    public function isEndpointCalled()
    {
        global $wp_query;
        return isset($wp_query->query_vars[self::SLUG]);
    }

    public static function getEndpointUrl()
    {
        return 'http://f1d05714.ngrok.io/typeform-wh/';
        if (self::isPrettyUrls()) {
            return get_bloginfo('url') . '/' . self::SLUG;
        } else {
            return add_query_arg(self::SLUG, 1, get_bloginfo('url'));
        }
    }

    public function captureEndpoint()
    {
        if (self::isPrettyUrls()) {
            add_rewrite_endpoint(self::SLUG, EP_ROOT);
        }
    }

    public static function isPrettyUrls()
    {
        return get_option('permalink_structure');
    }

}

add_action('init', 'register_typeform_webhooks');

function register_typeform_webhooks()
{
    $webhook = new TypeformWebHook();
    $webhook->registerHooks();
}
