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

        public function init()
        {
            parent::init();

            $token = $this->get_plugin_setting('typeform-token');
            $this->api = new TypeformApi($token);
            $this->timesSaved = 0;


            add_filter('gform_shortcode_form', [$this, 'getTypeform'], 1, 3);

            add_filter('gform_after_save_form', [$this, 'saveTypeformId']);

            // add_filter('gform_form_update_meta', [ $this, 'saveDesign'], 10, 3);

            // add_filter('gform_post_update_form_meta', [ $this, 'redirect_because_fail']);

        }

        public function save_form_settings($form, $settings)
        {
            $design_id = $this->getDesign($settings);
            $settings['design-id'] = $design_id;

            return parent::save_form_settings($form, $settings);
        }


        public function getDesign($settings)
        {
            try {
                $response = $this->api->getDesignId($settings);
                return $response->id;
            } catch (Exception $e) {
                return $e;
            }
            
        }

        public function saveTypeformId($form, $is_new = false)
        {
            $response = $this->getTypeformData($form);
            $form_id = $response->id;
            $form_url = $this->getTypeformUrl($response->_links);

            $settings = parent::get_form_settings($form);

            $settings['form-id'] = $form_id;
            $settings['form-url'] = $form_url;

            return parent::save_form_settings($form, $settings);

        }

        public function getTypeformUrl($typeform_links)
        {
            foreach ($typeform_links as $link) {
                if ($link->rel == 'form_render') {
                    return $link->href;
                }
            }
        }

        public function getTypeformData($form)
        {
            $webhook = apply_filters('typeform/webhook', add_query_arg('typeform-response', $form['id'], get_bloginfo('url')));
            $response = $this->api->getFormId($form['fields'], $webhook, $form['title'], ['form-' . $form['id']]);            
            return $response;
        }

        public function form_settings_fields($form)
        {

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
                            "type"    => "hidden",
                            "name"    => "design-id",
                        ],
                        [
                            "label"   => "Form ID",
                            "type"    => "hidden",
                            "name"    => "form-id",
                        ],
                        [
                            "label"   => "Form URL",
                            "type"    => "hidden",
                            "name"    => "form-url",
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

        public function plugin_settings_fields()
        {

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

        public function is_valid_setting($value)
        {
            return $value;
        }

        public function scripts()
        {
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

        public function styles()
        {

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

        public function getTypeform($shortcode_string, $attributes, $content)
        {
            // var_dump($shortcode_string, $attributes, $content);
            $form_id = $attributes['id'];
            $gf = new GFAPI();
            $form = $gf->get_form($form_id);

            $form_settings = $this->get_form_settings($form);

            // echo '<pre>'; print_r($form); echo '</pre>';
            // echo '<pre>'; print_r($form_settings); echo '</pre>';

            if (!$form_settings['enable-typeform'] || !isset($form_settings['form-id'])) {
                return $shortcode_string;
            }

            $this->embedTypeform($form_settings['form-url']);
        }

        public function embedTypeform($form_url)
        {
            echo '<iframe src="' . $form_url . '" height="500" width="100%">';
        }

        public function renderTypeform($form_url)
        {

        ?>
        <div class="typeform-widget" data-url="<?= $form_url; ?>" data-text="All fields" style="width:100%;height:500px;"></div>

        <script>(function(){var qs,js,q,s,d=document,gi=d.getElementById,ce=d.createElement,gt=d.getElementsByTagName,id='typef_orm',b='https://s3-eu-west-1.amazonaws.com/share.typeform.com/';if(!gi.call(d,id)){js=ce.call(d,'script');js.id=id;js.src=b+'widget.js';q=gt.call(d,'script')[0];q.parentNode.insertBefore(js,q)}})()</script>
        <?php
        }
    }

    new GFTypeformAddon();
}
