<?php
if (!defined('ABSPATH')) die;
class ADS_Users
{
    public $user;

    private $distri;
    public function __construct()
    {
    }

    public function is_logged()
    {
        if (function_exists('is_user_logged_in') && is_user_logged_in() && function_exists('get_current_user_id') && get_current_user_id() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_user()
    {
        return $this->user;
    }

    public function get_list_paises()
    {
        if (isset($this->user->pais) && !empty($this->user->pais)) {
            return $this->user->pais;
        } else {
            return false;
        }
    }

    /*public function get_name_distri()
    {
        if (!empty($this->user)) {
            $this->load_user();
        }
        echo "llamando el distri";
       
        if (isset($this->user->stockist)) {
            return $this->user->stockist['nombre'];
        } else {
            return false;
        }
    }*/

    public function load_user()
    {
        $userid = get_current_user_id();

        $this->user = get_userdata($userid);
        if (!empty($this->user)) {
            $this->get_metas_userid($this->user);

            $allowed_roles = array('administrator');
            if (array_intersect($allowed_roles, $this->user->roles)) {
                $this->user->is_admin = true;
            } else {
                $this->user->is_admin = false;
            }
        }
    }

    public function get_metas_userid($user)
    {
        if (isset($user->ID) && !empty($user->ID)) {
            $user->first_name = esc_attr(get_the_author_meta('user_firstname', $user->ID));
            $user->last_name = esc_attr(get_the_author_meta('user_lastname', $user->ID));
            $user->mobile = esc_attr(get_the_author_meta('user_phone', $user->ID));
            $user->tele = esc_attr(get_the_author_meta('user_tele', $user->ID));
            $user->address = esc_attr(get_the_author_meta('user_dir', $user->ID));
            $user->calendly = esc_attr(get_the_author_meta('user_calendly', $user->ID));
            $user->cargo = esc_attr(get_the_author_meta('user_cargo', $user->ID));
            $user->pais = get_the_author_meta('user_pais', $user->ID);

            if (!is_array($user->pais)) {
                $user->pais = array();
            }

            $user->stockist = esc_attr(get_the_author_meta('user_stockist', $user->ID));


            if (empty($user->first_name) && empty($user->last_name)) {
                $nombs = explode(" ", $user->display_name);
                if (sizeof($nombs) === 1) {
                    $user->first_name = $nombs[0];
                    $user->last_name = "";
                } elseif (sizeof($nombs) === 2) {
                    $user->first_name = $nombs[0];
                    $user->last_name = $nombs[1];
                } elseif (sizeof($nombs) === 3) {
                    $user->first_name = $nombs[0];
                    $user->last_name = $nombs[1] . " " . $nombs[2];
                } elseif (sizeof($nombs) >= 4) {
                    $user->first_name = $nombs[0] . " " . $nombs[1];
                    foreach ($nombs as $key => $nomb) {
                        if ($key > 1) {
                            $user->last_name .= $nomb . " ";
                        }
                    }
                    $user->last_name = rtrim($user->last_name);
                }
            }

            return $user;
        } else {
            return false;
        }
    }

    /* public function get_pais()
    {
        /*global $wp_query;
        global $id_pais;

        if (isset($wp_query->query_vars['pais_coti']) && !empty($wp_query->query_vars['pais_coti']) && !is_admin()) {
            /*echo "<pre>";
            var_dump($this->user);
            echo "</pre>";*
            $id_pais = $wp_query->query_vars['pais_coti'];
            return $id_pais;
        } else {
            return false;
        }*
    }*/

    /*public function get_distri()
    {
        $this->user->distri = $cw_data->get_active_distri()[$this->user->stockist];
    }*/
}
