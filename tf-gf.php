<?php

// add_action('init', 'gf_fields_post');

// function gf_fields_post(){
//     if(isset($_POST['gform-settings-save'])){
//         echo '<pre>'; print_r($_POST); echo '</pre>';
//         die();
//     }
// }

if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFTypeformAddon extends GFAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.8";
        protected $_slug = "typeform-gravity-forms-addon";
        protected $_path = "typeform-gravity-forms-addon/index.php";
        protected $_full_path = __FILE__;
        protected $_title = "Typeform Add-On";
        protected $_short_title = "Typeform";

        var $token;
        var $typeform_url;

        public function init(){
            parent::init();

            $this->typeform_url = 'https://api.typeform.io/latest/';
            $this->token = $this->get_plugin_setting('typeform-token');

            add_filter('gform_shortcode_form', [ $this, 'get_form'], 1, 3);

            add_filter('gform_form_update_meta', [ $this, 'get_design'], 10, 3);

            add_filter('gform_post_update_form_meta', [ $this, 'redirect_because_fail']);

        }

        function redirect_because_fail(){
            ?>
            <script>
            location.reload();
            </script>
            <?php
        }

        function get_design($form_meta, $form_id, $meta_name){

            $form_data = $form_meta[$this->_slug];

            $design_args = [
                'colors'    => [
                    'question'  => $form_data['font-color'],
                    'button'  => $form_data['button-color'],
                    'answer'  => $form_data['answer-color'],
                    'background'  => $form_data['background-color']
                ],
                'font'      => $form_data['font-family']
            ];

            // echo '<pre>'; print_r($design_args); echo '</pre>';

            //is a typeform settings form
            if(isset($form_data['form-typeform-settings'])){
                $design_response = wp_remote_post($this->typeform_url . 'designs', [
                    'headers'   => [
                        'X-API-TOKEN'   => $this->token
                    ],
                    'body'  => json_encode($design_args)
                ]);
                if ( is_wp_error( $design_response ) ) {
                   $error_message = $design_response->get_error_message();
                   echo "Something went wrong: $error_message";
                } else {
                    $design_data = json_decode($design_response['body']);
                    $form_meta[$this->_slug]['design-id'] = $design_data->id;
                    // echo '<pre>'; print_r($design_data); echo '</pre>';
                    // echo '<pre>'; print_r($form_meta[$this->_slug]); echo '</pre>';
                }

            }

            return $form_meta;
        }

        public function form_settings_fields($form) {

            return [
                [
                    "title"  => "Typeform Settings",
                    "fields" => [
                        [
                            "label"   => __("Enable Typeform render", 'tf-gf'),
                            "type"    => "checkbox",
                            "name"    => "enable-typeform",
                            "tooltip" => __("Render form with Typeform", 'tf-gf'),
                            "choices" => [
                                [
                                    "label" => "Enabled",
                                    "name"  => "enable-typeform"
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    "title" => "Design",
                    "fields" => [
                        [
                            "label"   => "Design ID",
                            "type"    => "text",
                            "name"    => "design-id",
                        ],
                        [
                            "label"   => "",
                            "type"    => "hidden",
                            "name"    => "form-typeform-settings",
                            "value"   => "it-is"
                        ],
                        [
                            "label"   => __("Questions color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "font-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Button color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "button-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Answers color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "answer-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ]
                        ,
                        [
                            "label"   => __("Background color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "background-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Font Family", 'tf-gf'),
                            "type"    => "select",
                            "name"    => "font-family",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "choices"   => [
                                [
                                    "label" => "Acme"
                                ],
                                [
                                    "label" => "Arial"
                                ],
                                [
                                    "label" => "Arvo"
                                ],
                                [
                                    "label" => "Bangers"
                                ],
                                [
                                    "label" => "Cabin"
                                ],
                                [
                                    "label" => "Cabin Condensed"
                                ],
                                [
                                    "label" => "Courier"
                                ],
                                [
                                    "label" => "Crete Round"
                                ],
                                [
                                    "label" => "Dancing Script"
                                ],
                                [
                                    "label" => "Exo"
                                ],
                                [
                                    "label" => "Georgia"
                                ],
                                [
                                    "label" => "Handlee"
                                ],
                                [
                                    "label" => "Karla"
                                ],
                                [
                                    "label" => "Lato"
                                ],
                                [
                                    "label" => "Lobster"
                                ],
                                [
                                    "label" => "Lora"
                                ],
                                [
                                    "label" => "McLaren"
                                ],
                                [
                                    "label" => "Monsterrat"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Old Standard TT"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Open Sans"
                                ],
                                [
                                    "label" => "Oswald"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Playfair"
                                ],
                                [
                                    "label" => "Quicksand"
                                ],
                                [
                                    "label" => "Raleway"
                                ],
                                [
                                    "label" => "Signika"
                                ],
                                [
                                    "label" => "Sniglet"
                                ],
                                [
                                    "label" => "Source Sans Pro"
                                ],
                                [
                                    "label" => "Vollkorn"
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        public function plugin_settings_fields() {

            return [
                [
                    "title"  => __("Typeform Add-On Settings", 'tf-gf'),
                    "fields" => [
                        [
                            "name"    => "typeform-token",
                            "tooltip" => __("Token generated by typeform.io", 'tf-gf'),
                            "label"   => __("Typeform IO Token", 'tf-gf'),
                            "type"    => "text",
                            "class"   => "large",
                            "feedback_callback" => [$this, "is_valid_setting"]
                        ]
                    ]
                ]
            ];
        }

        public function is_valid_setting($value){
            return $value;
        }

        public function scripts() {
            $scripts = array(
                array("handle"  => "my_script_js",
                      "src"     => $this->get_base_url() . "/js/my_script.js",
                      "version" => $this->_version,
                      "deps"    => array("jquery"),
                      "strings" => array(
                          'first'  => __("First Choice", "simpleaddon"),
                          'second' => __("Second Choice", "simpleaddon"),
                          'third'  => __("Third Choice", "simpleaddon")
                      ),
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "simpleaddon"
                          )
                      )
                ),

            );

            return array_merge(parent::scripts(), $scripts);
        }

        public function styles() {

            $styles = array(
                array("handle"  => "my_styles_css",
                      "src"     => $this->get_base_url() . "/css/my_styles.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array("field_types" => array("poll"))
                      )
                )
            );

            return array_merge(parent::styles(), $styles);
        }

        function get_form($shortcode_string, $attributes, $content){

            // var_dump($shortcode_string, $attributes, $content);
            $form_id = $attributes['id'];

            //getting form 
            $gf = new GFAPI();
            $form = $gf->get_form($form_id);

            //getting if we have to render this typeform
            $form_settings = $this->get_form_settings($form);

            if(!$form_settings['enable-typeform']) return $shortcode_string;

            //(show title, show description, fields)
            $this->render_typeform($attributes['title'], $attributes['description'], $form);
        }

        function render_typeform($show_title, $show_description, $form){
            // echo '<pre>'; print_r($form['fields']); echo '</pre>';
            // die();
            foreach ($form['fields'] as $field) {
                $typeform_fields[] = $this->convert_field($field);
            }

            //getting form 
            $gf = new GFAPI();
            $form_data = $gf->get_form($form['id']);

            //getting if we have to render this typeform
            $form_settings = $this->get_form_settings($form_data);

            $new_form = [
                'title'     => $form['title'],
                'fields'    => $typeform_fields,
                'design_id' => $form_settings['design-id']
            ];
            $this->parse_typeform($new_form);
            die();
        }

        function convert_field($field){

            $multiple_options_field = [
                'select',
                'radio',
                'checkbox'
            ];

            $new_field = [
                'question'      => $field['label'],
                'description'   => $field['description'],
                'required'      => ($field['isRequired']) ? true: false,
                'tags'          => ['field-' . $field['id']]
            ];

            if(in_array($field['type'], $multiple_options_field)){
                $new_field['choices'] = [];

                foreach($field['choices'] as $option){
                    $new_field['choices'][] = [
                        'label' => $option['text']
                    ];
                }
            }
            switch ($field['type']) {
                case 'text':
                    $new_field['type'] = 'short_text';
                    if($field['maxLength']){
                        $new_field['max_characters'] = (int) $field['maxLength'];
                    }
                    break;
                case 'textarea':
                    $new_field['type'] = 'long_text';
                    if($field['maxLength']){
                        $new_field['max_characters'] = (int) $field['maxLength'];
                    }
                    break;
                case 'email':
                    $new_field['type'] = 'email';
                    break;
                case 'select':
                    $new_field['type'] = 'dropdown';
                    break;
                case 'radio':
                    $new_field['type'] = 'multiple_choice';
                    break;
                case 'checkbox':
                    $new_field['type'] = 'multiple_choice';
                    $new_field['allow_multiple_selections'] = true;
                    break;
                case 'number':
                    $new_field['type'] = 'number';
                    if($field['rangeMin']){
                        $new_field['min_value'] = (int) $field['rangeMin'];
                    }
                    if($field['rangeMax']){
                        $new_field['max_value'] = (int) $field['rangeMax'];
                    }
                    break;
                default:
                    # code...
                    break;
            }       
            return $new_field;
        }

        function parse_typeform($form){

            $create_response = wp_remote_post($this->typeform_url . 'forms', [
                'headers'   => [
                    'X-API-TOKEN'   => $this->token
                ],
                'body'  => json_encode($form)
            ]);

            // echo '<pre>'; print_r($create_response); echo '</pre>';
            if ( is_wp_error( $create_response ) ) {
               $error_message = $create_response->get_error_message();
               echo "Something went wrong: $error_message";
            } else {
               $this->print_form(json_decode($create_response['body']));

            }
        }

        function print_form($form){

            $link = $form->_links;
            $href = '';

            foreach($link as $l){
                if($l->rel == 'form_render'){
                    $href = $l->href;
                    break;
                }
            }
            ?>
            <div class="typeform-widget" data-url="<?= $href; ?>" data-text="All fields" style="width:100%;height:500px;"></div>

            <script>(function(){var qs,js,q,s,d=document,gi=d.getElementById,ce=d.createElement,gt=d.getElementsByTagName,id='typef_orm',b='https://s3-eu-west-1.amazonaws.com/share.typeform.com/';if(!gi.call(d,id)){js=ce.call(d,'script');js.id=id;js.src=b+'widget.js';q=gt.call(d,'script')[0];q.parentNode.insertBefore(js,q)}})()</script>
            <?php
        }
    }

    new GFTypeformAddon();
}
