<?php

class TypeformHandler
{

    private static function getFields()
    {
        $available_fields = [
            'text'      => [
                'type'  => 'short_text',
                'options'    => [
                    'max_characters'    => function ($field) {
                        if ($field['maxLength']) {
                            return (int) $field['maxLength'];
                        }
                    }
                ]
            ],
            'textarea'  => [
                'type'  => 'long_text',
                'options'    => [
                    'max_characters'    => function ($field) {
                        if ($field['maxLength']) {
                            return (int) $field['maxLength'];
                        }
                    }
                ]
            ],
            'email'     => [
                'type'  => 'email'
            ],
            'select'    => [
                'type'      => 'dropdown',
                'multiple'  => true
            ],
            'radio'     => [
                'type'      => 'multiple_choice',
                'multiple'  => true
            ],
            'checkbox'  => [
                'type'  => 'multiple_choice',
                'multiple'  => true,
                'options'   => [
                    'allow_multiple_selections' => true
                ]
            ],
            'multiselect'  => [
                'type'  => 'multiple_choice',
                'multiple'  => true,
                'options'   => [
                    'allow_multiple_selections' => true
                ]
            ],
            'number'    => [
                'type'  => 'number',
                'options'   => [
                    'max_value'  => function ($field) {
                        if ($field['rangeMax']) {
                            return (int) $field['rangeMax'];
                        }
                    },
                    'min_value'  => function ($field) {
                        if ($field['rangeMin']) {
                            return (int) $field['rangeMin'];
                        }
                
                    }
                ]
            ]
        ];

        return $available_fields;
    }


    public static function getFieldStructure($field)
    {
        $available_fields = self::getFields();

        return (isset($available_fields[$field['type']])) ? $available_fields[$field['type']]: [];
    }

    public static function getEntryStructure($data)
    {
        $structure = [
            'form_id'   => 0
        ];
        return $structure;
    }

    public static function setDesignFields($data)
    {
        $design_args = [
            'colors'    => [
                'question'  => $data['font-color'],
                'button'  => $data['button-color'],
                'answer'  => $data['answer-color'],
                'background'  => $data['background-color']
            ],
            'font'      => $data['font-family']
        ];
        return $design_args;
    }

    public static function convertFields($fields)
    {
        if (!is_array($fields)) {
            return;
        }

        $converted_fields = [];

        foreach ($fields as $field) {
            $c_field = TypeformHandler::convertField($field);

            if ($c_field) {
                $converted_fields[] = TypeformHandler::convertField($field);
            }
        }

        return $converted_fields;
    }

    public static function convertField($field)
    {

        if(!self::isValidField($field)) {
            return false;
        }

        $field_options = [];

        $new_field = [
            'question'      => $field['label'],
            'description'   => $field['description'],
            'required'      => ($field['isRequired']) ? true: false,
            'tags'          => ['field-' . $field['id'], 'type-' . $field['type']]
        ];

        $new_field['type'] = TypeformHandler::getFieldType($field);

        $choices = TypeformHandler::getFieldChoices($field);
        if ($choices) {
            $new_field['choices'] = $choices;
        }
        
        $field_options = TypeformHandler::getFieldOptions($field);

        return array_merge($new_field, $field_options);
    }

    public static function isValidField($field)
    {
        return in_array($field['type'], self::availableFields());
    }

    public static function availableFields()
    {
        return array_keys(self::getFields());
    }

    public static function getFieldOptions($field)
    {
        $field_options = TypeformHandler::getFieldOptionsStructure($field);
        $new_field = [];

        foreach ($field_options as $key => $callback) {
            if (is_callable($callback)) {
                $result = $callback($field);
                if (!empty($result)) {
                    $new_field[$key] = $result;
                }
            } else {
                $new_field[$key] = $callback;
            }
        }
        return $new_field;
    }

    public static function getFieldOptionsStructure($field)
    {
        $field_structure = TypeformHandler::getFieldStructure($field);

        return (isset($field_structure['options'])) ? $field_structure['options']: [];
    }

    public static function getFieldType($field)
    {
        $field_structure = TypeformHandler::getFieldStructure($field);

        return (isset($field_structure['type'])) ? $field_structure['type']: [];
    }

    public static function getFieldChoices($field)
    {
        $structured_field = TypeformHandler::getFieldStructure($field);
        $choices = [];

        if (isset($structured_field['multiple']) && $structured_field['multiple']) {
            foreach ($field['choices'] as $option) {
                $choices[] = [
                    'label' => $option['text']
                ];
            }
        }

        return $choices;
    }
}
