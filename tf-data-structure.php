<?php

class TypeformHandler
{

    public static function getFieldStructure($field)
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

        return (isset($available_fields[$field['type']])) ? $available_fields[$field['type']]: [];
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
            $converted_fields[] = TypeformHandler::convertField($field);
        }

        return $converted_fields;
    }

    public static function convertField($field)
    {

        $new_field = [
            'question'      => $field['label'],
            // 'description'   => $field['description'],
            // 'required'      => ($field['isRequired']) ? true: false,
            // 'tags'          => ['field-' . $field['id']]
        ];

        $new_field['type'] = TypeformHandler::getFieldType($field);
        // $new_field['choices'] = TypeformHandler::getFieldChoices($field);

        // $field_options = TypeformHandler::getFieldOptions($field);
        $field_options = [];
        

        return array_merge($new_field, $field_options);
    }

    public static function getFieldOptions($field)
    {
        $field_options = TypeformHandler::getFieldOptionsStructure($field);
        $new_field = [];

        foreach ($field_options as $key => $callback) {
            if (is_callable($callbalck, true)) {
                $new_field[$key] = $callback($data);
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

        return (isset($field_structure['type'])) ? $field_structure['type']: 'text';
    }

    public static function getFieldChoices($field)
    {
        $structured_field = TypeformHandler::getFieldStructure($field);
        $choices = [];

        if ($structured_field['multiple']) {
            foreach ($field['choices'] as $option) {
                $nchoices[] = [
                    'label' => $option['text']
                ];
            }
        }

        return $choices;
    }
}
