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
            $entry_id = $this->setData($form_id, $data);
            if ($entry_id) {
                gform_update_meta($entry_id, 'typeform_data', $data);
                $this->sendNotifications($form_id, $entry_id);
            }
            return [
                'entry_id'  => $entry_id
            ];
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

        return GFAPI::add_entry($form_data, $form_id);
    }

    private function parseData($data)
    {
        $answers = $this->getAnswers($data);
        $fields = [];

        foreach ($answers as $answer) {
            $converted_field = $this->getGfField($answer);
            if ($converted_field) {
                $this->addField($fields, $converted_field);
            }
        }
        return $fields;
    }

    private function addField(&$fields, $converted_field)
    {
        $value = $converted_field['value'];
        $key = $converted_field['key'];
        $type = $converted_field['type'];
        if (is_array($value)) {
            if ($type == 'multiselect') {
                $fields[$key] = implode(',', $value);
            } else {
                foreach ($value as $i => $v) {
                    $fields[$key . '.' . ($i + 1)] = $v;
                }
            }
        } else {
            $fields[$key] = $value;
        }
        
    }

    private function getAnswers($data)
    {
        return $data->answers;
    }

    private function getGfField($answer)
    {
        $field_id = $this->getFieldId($answer);
        $field_value = $this->getFieldValue($answer);
        $field_type = $this->getFieldType($answer);

        return [
            'key'   => $field_id,
            'value' => $field_value,
            'type'  => $field_type
        ];
    }

    private function getFieldId($answer)
    {
        return $this->getFieldMeta($answer, 0);
    }

    private function getFieldType($answer)
    {
        return $this->getFieldMeta($answer, 1);
    }

    private function getFieldMeta($answer, $position)
    {
        if ($answer->tags[$position]) {
            $field_raw_id = explode('-', $answer->tags[$position]);
            return $field_raw_id[1];
        } else {
            throw new Exception(__('No field type'));
        }
    }

    private function getFieldValue($answer)
    {
        $type = $answer->type;
        $value = $answer->value;
        if ($type == 'choice') {
            return $value->label;
        } elseif ($type == 'choices') {
            return $value->labels;
        } elseif ($type == 'number') {
            return $value->amount;
        } else {
            return $value;
        }
    }

    private function verifyResponse($form_id, $data)
    {
        return true;
    }

    private function getFormData($form_id)
    {
        $gf = new GFAPI();
        $form = $gf->get_form($form_id);
        return [
            'settings'  => $form[GFTypeformAddon::ADDON_SLUG],
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
        if (!isset($typeform_data)) {
            throw new Exception(__('No typeform data'));
        }
        return json_decode($typeform_data);
    }

    private function sendNotifications($form_id, $entry_id)
    {
        $form = GFAPI::get_form($form_id);
        $entry = GFAPI::get_entry($entry_id);
        GFAPI::send_notifications($form, $entry);
    }

    public function isEndpointCalled()
    {
        global $wp_query;
        return isset($wp_query->query_vars[self::SLUG]);
    }

    public static function getEndpointUrl()
    {
        // return 'http://25bec729.ngrok.io/typeform-wh/';
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
