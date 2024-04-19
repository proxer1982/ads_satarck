<?php

/**
 *  WpApiFeaturedImage
 *
 *  Adds featured images to the products endpoint
 *  using register_rest_field hook.
 *
 *  @version   1.0
 *  @author    Juan Zorro
 */

if (!defined('ABSPATH')) die;


class AnunciosLogin
{

    /**
     * The endpoints we want to target
     */
    public $target_endpoints = array();

    /**
     * Constructor
     * @uses rest_api_init
     */
    function __construct()
    {
        $this->target_endpoints = array('login_announcement');

        add_action('init', array($this, 'login_announcement_post_type'));
        add_action('rest_api_init', array($this, 'add_image'));
        add_action('rest_api_init', array($this, 'set_custom_css'));
    }

    public function login_announcement_post_type()
    {
        $labels = array(
            "name" => __("Comunicados Login", "twentynineteen"),
            "singular_name" => __("Comunicado Login", "twentynineteen"),
            "menu_name" => __("Comunicados Login", "twentynineteen"),
            "all_items" => __("Todos los comunicados", "twentynineteen"),
            "add_new" => __("Añadir nuevo", "twentynineteen"),
            "add_new_item" => __("Añadir nuevo comunicado", "twentynineteen"),
            "edit_item" => __("Editar comunicado", "twentynineteen"),
            "new_item" => __("Nuevo comunicado", "twentynineteen"),
            "view_item" => __("Ver comunicado", "twentynineteen"),
            "view_items" => __("Ver comunicados", "twentynineteen"),
            "search_items" => __("Buscar comunicados", "twentynineteen"),
            "not_found" => __("No se han encontrado comunicados", "twentynineteen"),
            "not_found_in_trash" => __("No hay comunicados en la papelera", "twentynineteen"),
        );

        $args = array(
            "label" => __("Comunicados Login", "twentynineteen"),
            "labels" => $labels,
            "description" => "Agrega información que será visible en el Login de la plataforma Satrack",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => true,
            "rest_base" => "login_announcement",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => false,
            "show_in_nav_menus" => false,
            "exclude_from_search" => true,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => array("slug" => "login_announcement", "with_front" => true),
            "query_var" => true,
            "menu_icon" => "dashicons-megaphone",
            "supports" => array("title", "editor", "thumbnail")
        );

        register_post_type("login_announcement", $args);
    }


    public function add_image()
    {

        /**
         * Add 'featured_image'
         */
        register_rest_field(
            $this->target_endpoints,
            'featured_image',
            array(
                'get_callback'    => array($this, 'get_image_url_full'),
                'update_callback' => null,
                'schema'          => null,
            )
        );

        /*
      * Add 'featured_image_thumbnail'
      */
        register_rest_field(
            $this->target_endpoints,
            'featured_image_thumbnail',
            array(
                'get_callback'    => array($this, 'get_image_url_thumb'),
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
     * Get Image: Thumb
     */
    public function get_image_url_thumb()
    {
        $url = $this->get_image('thumbnail');
        return $url;
    }

    /**
     * Get Image: Full
     */
    public function get_image_url_full()
    {
        $url = $this->get_image('full');
        return $url;
    }

    /**
     * Get Image Helpers
     */
    public function get_image($size)
    {
        $id = get_the_ID();

        if (has_post_thumbnail($id)) {
            $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);
            $url = $img_arr[0];
            return $url;
        } else {
            return false;
        }
    }

    /**
     * Set Custom CSS
     */
    public function set_custom_css()
    {

        register_rest_field(
            $this->target_endpoints,
            'custom_css',
            array(
                'get_callback'    => array($this, 'custom_css'),
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    public function custom_css()
    {
        $css = '
        .image-login {
            background-position: bottom center!important;
        }

        .login_announcement {
            display: flex;
            height: 100%;
            align-items: stretch;
            justify-content: center;
            flex-direction: column;        
            }
            
            .login_announcement .header_login {
                
            }
            
            .login_announcement .content_login {
                flex-grow:2; 
             }
             
             .login_announcement .footer_login {
                
             }
             
             .d-flex {
               display:flex; 
             }
             
             .align-items-end {
                align-items:flex-end;
               }
               
               .ps-3 {
                padding-left:3rem;
               }

               .ps-4 {
                padding-left:4rem;
               }
               
               .ps-5 {
                padding-left:5rem;
               }
               
               .py-3 {
                padding-top:3rem;
                padding-bottom:3rem;
               }

               .py-4 {
                padding-top:4rem;
                padding-bottom:4rem;
               }
               
               .py-5 {
                padding-top:5rem;
                padding-bottom:5rem;
               }';

        return trim($css);
    }
}
