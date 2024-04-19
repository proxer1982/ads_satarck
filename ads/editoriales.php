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


class Editoriales
{

  /**
   * The endpoints we want to target
   */
  public $target_endpoints = '';

  /**
   * Constructor
   * @uses rest_api_init
   */
  function __construct()
  {
    $this->target_endpoints = array('ed-satrack');

    add_action('init', array($this, 'e_satrack_post_type'));
  }

  public function e_satrack_post_type()
  {
    /**
     * Post Type: Comunicados Login.
     */

    $labels = array(
      "name" => __("Editoriales Satrack", "ed-satrack"),
      "singular_name" => __("Editorial", "ed-satrack"),
      "menu_name" => __("E! Satrack", "ed-satrack"),
      "all_items" => __("Todos los anuncios", "ed-satrack"),
      "add_new" => __("Añadir nuevo", "ed-satrack"),
      "add_new_item" => __("Añadir nuevo editorial", "ed-satrack"),
      "edit_item" => __("Editar editorial", "ed-satrack"),
      "new_item" => __("Nuevo editorial", "ed-satrack"),
      "view_item" => __("Ver editorial", "ed-satrack"),
      "view_items" => __("Ver editoriales", "ed-satrack"),
      "search_items" => __("Buscar editoriales", "ed-satrack"),
      "not_found" => __("No se han encontrado editoriales", "ed-satrack"),
      "not_found_in_trash" => __("No hay editoriales en la papelera", "ed-satrack"),
    );

    $args = array(
      'labels'             => $labels,
      'description'        => 'Editoriales de Satrack',
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => false,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'ed-satrack'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => 10,
      'show_in_rest'       => true,
      "menu_icon" => "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTIyLjg4IDEwMS4zNyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMTIyLjg4IDEwMS4zNyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZD0iTTEyLjY0LDc3LjI3bDAuMzEtNTQuOTJoLTYuMnY2OS44OGM4LjUyLTIuMiwxNy4wNy0zLjYsMjUuNjgtMy42NmM3Ljk1LTAuMDUsMTUuOSwxLjA2LDIzLjg3LDMuNzYgYy00Ljk1LTQuMDEtMTAuNDctNi45Ni0xNi4zNi04Ljg4Yy03LjQyLTIuNDItMTUuNDQtMy4yMi0yMy42Ni0yLjUyYy0xLjg2LDAuMTUtMy40OC0xLjIzLTMuNjQtMy4wOCBDMTIuNjIsNzcuNjUsMTIuNjIsNzcuNDYsMTIuNjQsNzcuMjdMMTIuNjQsNzcuMjd6IE0xMDMuNjIsMTkuNDhjLTAuMDItMC4xNi0wLjA0LTAuMzMtMC4wNC0wLjUxYzAtMC4xNywwLjAxLTAuMzQsMC4wNC0wLjUxVjcuMzQgYy03LjgtMC43NC0xNS44NCwwLjEyLTIyLjg2LDIuNzhjLTYuNTYsMi40OS0xMi4yMiw2LjU4LTE1LjksMTIuNDRWODUuOWM1LjcyLTMuODIsMTEuNTctNi45NiwxNy41OC05LjEgYzYuODUtMi40NCwxMy44OS0zLjYsMjEuMTgtMy4wMlYxOS40OEwxMDMuNjIsMTkuNDh6IE0xMTAuMzcsMTUuNmg5LjE0YzEuODYsMCwzLjM3LDEuNTEsMy4zNywzLjM3djc3LjY2IGMwLDEuODYtMS41MSwzLjM3LTMuMzcsMy4zN2MtMC4zOCwwLTAuNzUtMC4wNi0xLjA5LTAuMThjLTkuNC0yLjY5LTE4Ljc0LTQuNDgtMjcuOTktNC41NGMtOS4wMi0wLjA2LTE4LjAzLDEuNTMtMjcuMDgsNS41MiBjLTAuNTYsMC4zNy0xLjIzLDAuNTctMS45MiwwLjU2Yy0wLjY4LDAuMDEtMS4zNS0wLjE5LTEuOTItMC41NmMtOS4wNC00LTE4LjA2LTUuNTgtMjcuMDgtNS41MmMtOS4yNSwwLjA2LTE4LjU4LDEuODUtMjcuOTksNC41NCBjLTAuMzQsMC4xMi0wLjcxLDAuMTgtMS4wOSwwLjE4QzEuNTEsMTAwLjAxLDAsOTguNSwwLDk2LjY0VjE4Ljk3YzAtMS44NiwxLjUxLTMuMzcsMy4zNy0zLjM3aDkuNjFsMC4wNi0xMS4yNiBjMC4wMS0xLjYyLDEuMTUtMi45NiwyLjY4LTMuMjhsMCwwYzguODctMS44NSwxOS42NS0xLjM5LDI5LjEsMi4yM2M2LjUzLDIuNSwxMi40Niw2LjQ5LDE2Ljc5LDEyLjI1IGM0LjM3LTUuMzcsMTAuMjEtOS4yMywxNi43OC0xMS43MmM4Ljk4LTMuNDEsMTkuMzQtNC4yMywyOS4wOS0yLjhjMS42OCwwLjI0LDIuODgsMS42OSwyLjg4LDMuMzNoMFYxNS42TDExMC4zNywxNS42eiBNNjguMTMsOTEuODJjNy40NS0yLjM0LDE0Ljg5LTMuMywyMi4zMy0zLjI2YzguNjEsMC4wNSwxNy4xNiwxLjQ2LDI1LjY4LDMuNjZWMjIuMzVoLTUuNzd2NTUuMjJjMCwxLjg2LTEuNTEsMy4zNy0zLjM3LDMuMzcgYy0wLjI3LDAtMC41My0wLjAzLTAuNzgtMC4wOWMtNy4zOC0xLjE2LTE0LjUzLTAuMi0yMS41MSwyLjI5Qzc5LjA5LDg1LjE1LDczLjU3LDg4LjE1LDY4LjEzLDkxLjgyTDY4LjEzLDkxLjgyeiBNNTguMTIsODUuMjUgVjIyLjQ2Yy0zLjUzLTYuMjMtOS4yNC0xMC40LTE1LjY5LTEyLjg3Yy03LjMxLTIuOC0xNS41Mi0zLjQzLTIyLjY4LTIuNDFsLTAuMzgsNjYuODFjNy44MS0wLjI4LDE1LjQ1LDAuNzEsMjIuNjQsMy4wNiBDNDcuNzMsNzguOTEsNTMuMTUsODEuNjQsNTguMTIsODUuMjVMNTguMTIsODUuMjV6Ii8+PC9nPjwvc3ZnPg==",
      "supports" => array('title', 'editor', 'author', 'thumbnail', "revisions")
    );

    register_post_type("ed-satrack", $args);
  }
}
