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
            $this->setData($form_id, $data);
        } else {
            throw new Exception(__('No verificable data response', 'typeform'));
        }
    }

    private function setData($form_id, $data)
    {
        $parsed_data = $this->parseData($data);

        $form_data = $parsed_data;
        $form_data['form_id'] = $form_id;
        $form_data['status'] = 'active';

        // die();
        // echo '<pre>'; print_r($data); echo '</pre>';

        return [
            'entry_id'  => GFAPI::add_entry($form_data, $form_id)
        ];
    }

    private function parseData($data)
    {
        $answers = $data->answers;
        $fields = [];
        // echo '<pre>'; print_r($data); echo '</pre>';
        // echo '<pre>'; var_dump(GFAPI::get_entries(1)); echo '</pre>';

        foreach ($answers as $answer) {
            $converted_field = $this->getGfField($answer);
            $fields[$converted_field['key']] = $converted_field['value'];
        }
        // echo '<pre>'; print_r($fields); echo '</pre>';
        // die();
        return $fields;
    }

    private function getGfField($answer)
    {
        $field_id = $this->getFieldId($answer);
        $field_value = $this->getFieldValue($answer);

        return [
            'key'   => $field_id,
            'value' => $field_value
        ];
    }

    private function getFieldId($answer)
    {
        if ($answer->tags[0]) {
            $field_raw_id = explode('-', $answer->tags[0]);
            return $field_raw_id[1];
        } else {
            throw new Exception(__('No field id'));
        }
    }

    private function getFieldValue($answer)
    {
        $type = $answer->type;
        if (in_array($type, ['select'])) {
            return '';
        } elseif ($type == 'number') {
            return $answer->value->amount;
        } else {
            return $answer->value;
        }
    }

    private function saveFields($fields)
    {
        if (empty($fiels)) {
            throw new Exception(__('No fields in answer', 'typeform'));
        }
    }

    private function verifyResponse($form_id, $data)
    {
        $form_data = $this->getFormData($form_id);
        // echo '<pre>'; print_r($form_data); echo '</pre>';
        
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
        $typeform_data = file_get_contents('php://input');

        // $typeform_data = get_option('typeform-data');
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
        return 'http://17aec997.ngrok.io/typeform-wh/';
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
