<?php
if (!defined('ABSPATH')) die;
class Html
{
    private $ads_user, $user, $name_distri, $menu, $pasos_num, $ceros;
    public function __construct()
    {
        global $ads_user;

        $this->menu = "";
        $this->pasos_num = 0;
        $this->ceros = "0";

        $this->user = $ads_user->get_user();
    }

    public function form_login($page)
    {
        if (is_user_logged_in())
            return '';
        // return  wp_login_form();
        $req_uri =  esc_attr($_SERVER['REQUEST_URI']);
        $redirect = home_url();

        $redirect .= (preg_match('/failed/', $req_uri)) ?  substr($req_uri, 0, strpos($req_uri, '?')) : $req_uri;

        ob_start(); ?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="view" viewBox="0 0 16 16">
                <title>view</title>
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
            </symbol>

            <symbol id="hide" viewBox="0 0 16 16">
                <title>hide</title>
                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z" />
                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z" />
                <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z" />
            </symbol>

        </svg>
        <section class='contenedor-login'>
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="card" style="border-radius: 1rem;">
                    <div class="d-flex flex-nowrap">
                        <div class="img_login">
                            <img src="https://satrack.com/wp-content/uploads/sites/2/2023/05/banners-tech-778x960_1.jpg" alt="">
                        </div>
                        <div class='caja-login'>
                            <?php if (isset($_GET['login']) && !empty($_GET['login'])) { ?>
                                <div class="wp_login_error">
                                    <?php if (isset($_GET['login']) && (preg_match('/failed/', $_GET['login']))) { ?>
                                        El logeo fue incorrecto. Vuelva a intentarlo.
                                    <?php unset($_GET['login']);
                                    } else if (isset($_GET['login']) && $_GET['login'] == 'empty') { ?>
                                        Please enter both username and password.
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <h1>Login</h1>
                            <hr class="mb-5">

                            <form name="custom_loginform" id="custom_loginform" action="<?= bloginfo('url') ?>/wp-login.php" method="post">
                                <!-- Email input -->
                                <div class="login-username form-outline mb-4">
                                    <input type="text" id="user_login" autocomplete="username" autofocus name="log" class="input form-control" required minlength="4" />
                                    <label class="form-label" for="user_login">Email o usuario</label>
                                </div>

                                <!-- Password input -->
                                <div class="login-password form-outline mb-4">
                                    <input type="password" id="user_pass" name="pwd" autocomplete="current-password" spellcheck="false" value="" class="form-control" required minlength="3" />
                                    <label class="form-label" for="user_pass">Contraseña</label>
                                    <span class='icon-view' id='icon-view'>
                                        <svg width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <use xlink:href="#view" />
                                        </svg>
                                    </span>
                                </div>

                                <!-- 2 column grid layout for inline styling -->
                                <div class="row mb-4">
                                    <div class="col d-flex justify-content-center">
                                        <!-- Checkbox -->
                                        <div class="form-check">
                                            <input class="form-check-input" name="rememberme" type="checkbox" id="rememberme" value="forever">
                                            <label class="form-check-label" for="form2Example31"> Recuérdarme</label>
                                        </div>
                                    </div>

                                </div>
                                <input type="hidden" name="redirect_to" value="<?= $redirect ?>" />
                                <!-- Submit button -->
                                <input type="submit" id="wp-submit" name="wp-submit" value="Entrar" class="btn btn-primary text-center btn-block mx-auto w-100 mb-4">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.form-control').change(function(e) {
                    $valor = $(this).val();
                    if ($valor.length > 0) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });

                $('#icon-view').click(function() {
                    $('#icon-view').toggleClass('active');

                    if ($('#icon-view').hasClass('active')) {
                        $('#user_pass').attr('type', 'text');
                        $(this).children('.bi').html('<use xlink:href="#hide" />');
                    } else {
                        $('#user_pass').attr('type', 'password');
                        $(this).children('.bi').html('<use xlink:href="#view" />')
                    }
                });

            });
        </script>
    <?php
        $cuerpo = ob_get_clean();
        return $cuerpo;
    }
    public function create_menu($pais = null)
    {
        global $ads_data;
        $url_page = ($pais !== null) ? get_permalink(get_the_ID()) . strtolower($pais) . "/" : "#";
        $paises_user = $ads_data->get_list_paises_user();
        $all_paises = $ads_data->get_list_paises();

        ob_start(); ?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="satrack" viewBox="0 0 520 520">
                <title>Satrack</title>
                <style>
                    .st0 {
                        fill: var(--e-global-color-primary);
                    }
                </style>
                <path class="st0" d="M495.5,185.1l5.1-15c1.7-4.7,3.9-11.2,4.7-14.4c1.9-7.6,3.8-15.5-0.4-21.5c-3.1-3.9-7.7-6.3-12.7-6.4l-4.1-0.3
	c-2.5,0-5.7-0.3-9.1-0.7l-9.8-0.6c-0.3-2-0.9-3.9-2-5.6C423.5,52.2,349.3,9.4,268.3,5.7c-2.2,0-4.5,0-6.8-0.2s-3.4,0-5.1,0h-1.6
	C116.5,5.6,4.5,117.9,4.5,256.2c0.1,138.3,112.3,250.4,250.7,250.3c8.7,0,17.3-0.5,26-1.4l0,0h1.3h1.2
	c137.5-15,236.9-138.6,221.9-276.2c-1.4-12.9-3.8-25.6-7.2-38.1C497.9,188.8,496.9,186.8,495.5,185.1z M27.2,313.9
	C-4.7,187.1,72.1,58.4,198.8,26.5c18.6-4.7,37.8-7.1,57-7.2c2.3,0,4.6,0,7,0h4.9c75.8,4.2,145.1,44.5,186.1,108.5l-105.3-12.2
	C348.4,61.1,296.3,57,296.3,57h-54.9v232.8c0,0.6-0.1,1.2-0.3,1.8v0.5c-0.2,0.4-0.4,0.8-0.7,1.1l-0.2,0.3c-0.4,0.4-0.8,0.8-1.3,1.1
	h-0.3l-1.3,0.6h-0.5c-0.6,0.2-1.1,0.4-1.7,0.5l0,0l-1.9,0.3h-0.5h-1.5h-5.8c-51.9-4.9-94.4-50.5-94.4-75.9v-21.3
	c0.2-1.4-0.8-2.8-2.2-3c-0.3,0-0.5,0-0.8,0c-1.5-0.2-2.9,0.9-3.1,2.4c0,0.3,0,0.6,0,0.9c-0.4,10.3-9.9,32.9-22.2,48.1
	c-7.8,9.4-16.2,18.3-25.1,26.7l0,0l-50.3,40.8L27.2,313.9z M318.5,116.5c17.3,11.7,5.4,35.5-14.3,28.8
	C286.9,133.5,298.8,109.8,318.5,116.5L318.5,116.5z M281.2,366.8c0-2.5,0-4.8,0-7.1c0-0.7,0-1.5,0-2.2c0-1.5,0-3.1,0.2-4.5
	c0.2-1.5,0-1.7,0-2.5c0-0.8,0-2.5,0.3-3.8c0.3-1.2,0.2-1.7,0.3-2.5s0.2-2.3,0.4-3.4c0.2-1.1,0.2-1.6,0.4-2.4
	c0.2-0.8,0.3-2.1,0.5-3.1c0.2-1,0.3-1.5,0.5-2.3s0.4-1.9,0.6-2.8c0.2-0.9,0.4-1.5,0.6-2.2c0.2-0.7,0.5-1.7,0.7-2.6
	c0.2-0.8,0.5-1.4,0.7-2.1c0.2-0.7,0.6-1.6,0.9-2.4c0.3-0.7,0.5-1.3,0.8-1.9c0.2-0.6,0.6-1.5,1-2.2l0.9-1.8c0.3-0.7,0.7-1.3,1.1-2
	l1-1.7l1.2-1.8l1.1-1.5l1.3-1.7l1.2-1.4l1.4-1.5l1.3-1.2l1.5-1.4l1.4-1.2l1.6-1.2l1.5-1l1.7-1.1l1.5-0.9l1.8-1l1.6-0.8l1.9-0.9
	l1.7-0.7l2.1-0.7l1.8-0.6l2.2-0.7l1.8-0.5l2.3-0.6l1.9-0.5l2.4-0.5l2-0.4l2.6-0.4l0,0l1.9-0.3l2.7-0.3l2-0.2l3-0.3h2l5.1-0.4
	l5.3-0.2h5.6h5.7h12h10h3.1l3-0.4l3-0.4l2.9-0.6l2.9-0.6l2.7-0.7l2.9-0.8l2.5-0.9l2.8-1l2.3-1l2.8-1.3l2.1-1.1l2.9-1.5l1.8-1.1
	l2.9-1.8l1.6-1.1c1-0.7,3.9-2.9,4.1-3.2c0.3-0.4,1.9-1.5,2.9-2.3l1.1-1c1-0.9,2-1.7,2.9-2.7l4.5-4.5c1-1.1,2-2.2,3-3.3
	c0,0,2.5-2.9,3.4-4.1l0.2-0.3c12.2-15.6,21.9-33,28.6-51.6c32.4,126.7-44.1,255.7-170.8,288c-10.5,2.7-21.2,4.6-31.9,5.9
	L281.2,366.8z M487.3,141.3l3.3,0.2c1.9,0,2.7,0.5,2.8,0.5c-0.1,3.5-0.6,7-1.7,10.3c-0.5,2.1-2.2,7.2-3.9,12.1
	c-3.2-8.2-6.8-16.1-10.9-23.9h0.9C481.2,140.9,484.7,141.1,487.3,141.3L487.3,141.3z M29.5,328.3c2.4-0.4,4.8-1.4,6.7-2.9L80,289.8
	c16.3,84.7,90.4,145.9,176.6,145.9c3.6,0,7.2,0,10.7-0.4v55.8l0,0c0,0.3,0,0.7,0,1c-4.1,0.2-8.3,0.3-12.7,0.3
	C152,492.4,61,426.1,29.5,328.3z" transform="matrix(0.9999999999999999, 0, 0, 0.9999999999999999, -3.552713678800501e-15, -3.552713678800501e-15)"></path>
            </symbol>
            <symbol id="pdf-file" viewBox="0 0 16 16">
                <title>pdf-file</title>
                <style>
                    .st2 {
                        fill: #466888;
                    }
                </style>
                <path class="st2" fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z" />
            </symbol>
            <symbol id="check" viewBox="0 0 16 16">
                <title>check</title>
                <style>
                    .st1 {
                        fill: #7eba18;
                    }
                </style>
                <title>check</title>
                <path class="st1" d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z" />
                <path class="st1" d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z" />
                <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3Z" />
            </symbol>
            <symbol id="trash" viewBox="0 0 16 16">
                <title>trash</title>
                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
            </symbol>
            <symbol id="rest" viewBox="0 0 16 16">
                <title>rest</title>
                <style>
                    .st3 {
                        fill: #ff9900;
                    }
                </style>
                <path class="st3" d="m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672z" />
                <path d="M11 11.5a.5.5 0 0 1 .5-.5h4a.5.5 0 1 1 0 1h-4a.5.5 0 0 1-.5-.5z" />
            </symbol>
            <symbol id="band_col" viewBox="0 0 520 520">
                <title>band_col</title>

                <defs>
                    <style>
                        .cls-col-1 {
                            fill: none;
                            stroke: #000;
                            stroke-miterlimit: 10;
                        }

                        .cls-col-2 {
                            fill: #ffdb00;
                        }

                        .cls-col-3 {
                            fill: #2a1baf;
                        }

                        .cls-col-4 {
                            fill: #e90405;
                        }
                    </style>
                </defs>
                <path class="cls-col-1" d="M32.17,256H479.83c0-.1,0-.21,0-.31H32.18C32.18,255.79,32.17,255.9,32.17,256Z" />
                <path class="cls-col-2" d="M256,32.17c-123.51,0-223.65,100.05-223.82,223.52H479.82C479.65,132.22,379.51,32.17,256,32.17Z" />
                <path class="cls-col-3" d="M62.13,367.91H449.87a222.73,222.73,0,0,0,30-111.91H32.17A222.73,222.73,0,0,0,62.13,367.91Z" />
                <path class="cls-col-4" d="M449.87,367.91H62.13a223.87,223.87,0,0,0,387.74,0Z" />
            </symbol>
            <symbol id="band_ecu" viewBox="0 0 520 520">
                <title>band_ecu</title>
                <defs>
                    <style>
                        .cls-5 {
                            fill: #2a2faf;
                        }

                        .cls-6 {
                            fill: #e90405;
                        }

                        .cls-7 {
                            fill: #ffe70e;
                        }

                        .cls-8 {
                            fill: #38a9fb;
                        }
                    </style>
                </defs>
                <path class="cls-5" d="M32.17,256A222.76,222.76,0,0,0,56,356.62H456A222.76,222.76,0,0,0,479.83,256c0-7.46-.38-14.84-1.09-22.11H33.26C32.55,241.16,32.17,248.54,32.17,256Z" />
                <path class="cls-6" d="M256,479.83A223.81,223.81,0,0,0,456,356.62H56A223.81,223.81,0,0,0,256,479.83Z" />
                <path class="cls-7" d="M478.74,233.89C467.63,120.65,372.15,32.17,256,32.17S44.37,120.65,33.26,233.89H200v45a56,56,0,0,0,56,56h0a56,56,0,0,0,56-56v-45Z" />
                <path class="cls-8" d="M222.39,210.56h67.22a0,0,0,0,1,0,0v67.82A33.61,33.61,0,0,1,256,312h0a33.61,33.61,0,0,1-33.61-33.61V210.56A0,0,0,0,1,222.39,210.56Z" />
                <path d="M341.22,164.74l-44.91-15.3a2.46,2.46,0,0,0-1.79.07L256,166.59l-38.52-17.08a2.46,2.46,0,0,0-1.79-.07l-44.91,15.3a1.67,1.67,0,0,0-.32,3l52.08,31.47h66.92l52.08-31.47A1.67,1.67,0,0,0,341.22,164.74Z" />
            </symbol>
            <symbol id="band_pan" viewBox="0 0 520 520">
                <title>band_pan</title>

                <defs>
                    <style>
                        .cls-9 {
                            fill: #0052b4;
                        }

                        .cls-10 {
                            fill: #fcfcfc;
                        }

                        .cls-11 {
                            fill: #d80027;
                        }
                    </style>
                </defs>
                <path class="cls-9" d="M32.17,256c0,123.62,100.21,223.83,223.83,223.83V256Z" />
                <path class="cls-10" d="M256,32.17C132.38,32.17,32.17,132.38,32.17,256H256Z" />
                <path class="cls-10" d="M256,479.83c123.62,0,223.83-100.21,223.83-223.83H256Z" />
                <path class="cls-11" d="M256,32.17V256H479.83C479.83,132.38,379.62,32.17,256,32.17Z" />
                <polygon class="cls-9" points="165.58 109.14 180.29 154.41 227.89 154.41 189.38 182.39 204.09 227.66 165.58 199.68 127.07 227.66 141.78 182.39 103.27 154.41 150.87 154.41 165.58 109.14" />
                <polygon class="cls-11" points="347.28 283.42 361.99 328.69 409.58 328.69 371.08 356.66 385.79 401.93 347.28 373.95 308.77 401.93 323.48 356.66 284.97 328.69 332.57 328.69 347.28 283.42" />
            </symbol>
        </svg>
        <div class="menu-flex d-flex flex-column flex-shrink-0">
            <div class="hamburger">
                <svg class='close' xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                </svg>
                <svg class='open' xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z" />
                    <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                </svg>
            </div>
            <div class="caja-logo">
                <a href="#" class="link-logo d-flex align-items-center text-decoration-none" style="color: var( --e-global-color-primary)">
                    <div class="icono"><svg class="bi" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                            <use xlink:href="#satrack"></use>
                        </svg></div> <span class="d-full fs-5 fw-bold me-0 me-lg-2">Panel comercial</span>
                </a>
            </div>

            <hr class="mb-3">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="<?= $url_page ?>" class="nav-link <?= (!isset($_GET['action']) && $pais !== null) ? " active" : ""; ?><?= ($pais === null) ?  " disabled" : ''; ?>" aria-current="page">
                        <div class='icono'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-task" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H2zM3 3H2v1h1V3z" />
                                <path d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z" />
                                <path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V7zM2 7h1v1H2V7zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H2zm1 .5H2v1h1v-1z" />
                            </svg></div><span class="d-full ">Cotizaciones</span>
                    </a>
                </li>
                <li>
                    <a href="<?= ($pais !== null) ? $url_page . "?action=new_cotizacion" : $url_page; ?>" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'new_cotizacion') ? " active"  : ""; ?><?= ($pais === null) ?  " disabled" : ''; ?>">
                        <div class='icono'><svg class="bi" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v4h10V2a1 1 0 0 0-1-1H4zm9 6h-3v2h3V7zm0 3h-3v2h3v-2zm0 3h-3v2h2a1 1 0 0 0 1-1v-1zm-4 2v-2H6v2h3zm-4 0v-2H3v1a1 1 0 0 0 1 1h1zm-2-3h2v-2H3v2zm0-3h2V7H3v2zm3-2v2h3V7H6zm3 3H6v2h3v-2z" />
                            </svg></div><span class="d-full ">Nueva cotización</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= ($pais !== null) ? $url_page . "?action=cotizaciones_aprobadas" : $url_page; ?>" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'cotizaciones_aprobadas') ? " active" : ""; ?><?= ($pais === null) ?  " disabled" : ''; ?>" aria-current="page">
                        <div class='icono'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                            </svg></div><span class="d-full ">Cot. Aprobadas</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link  <?= (isset($_GET['action']) && $_GET['action'] == 'vcard') ? " active" : ""; ?> disabled" aria-disabled="true" disabled>
                        <div class='icono'><svg class="bi" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                                <path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0v-3Zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5ZM.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5Zm15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5ZM4 4h1v1H4V4Z" />
                                <path d="M7 2H2v5h5V2ZM3 3h3v3H3V3Zm2 8H4v1h1v-1Z" />
                                <path d="M7 9H2v5h5V9Zm-4 1h3v3H3v-3Zm8-6h1v1h-1V4Z" />
                                <path d="M9 2h5v5H9V2Zm1 1v3h3V3h-3ZM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8H8Zm2 2H9V9h1v1Zm4 2h-1v1h-2v1h3v-2Zm-4 2v-1H8v1h2Z" />
                                <path d="M12 9h2V8h-2v1Z" />
                            </svg></div><span class="d-full ">Vcard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link  <?= (isset($_GET['action']) && $_GET['action'] == 'manual_comercial') ? " active" : ""; ?> disabled" aria-disabled="true" disabled>
                        <div class='icono'><svg class="bi" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                                <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z" />
                                <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z" />
                            </svg></div><span class="d-full ">Manual comercial</span>
                    </a>
                </li>

            </ul>

            <hr class="mt-5 mb-1">
            <?php
            if (sizeof($paises_user) > 1) : ?>
                <?php
                $ind = 0;
                $gets = $_GET;
                $param_url = "";
                foreach ($gets as $key => $value) {
                    $param_url .= ($ind === 0) ?  "?" . $key . "=" . $value : "&" . $key . "=" . $value;
                    $ind++;
                } ?>
                <div class="dropdown mb-1">
                    <a href="#" class="caja-dropdown text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: .9rem">
                        <div class='icono'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0ZM2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484-.08.08-.162.158-.242.234-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z" />
                            </svg></div>
                        <span class="d-full"><?= ($pais === null || $pais === "") ? "Seleccione país" : '<strong>' . $all_paises[$pais]['name'] . '</strong>' ?></span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownUser2" style="">
                        <?php foreach ($paises_user as $pais_user) : ?>
                            <li><a class="dropdown-item" href="<?= ($pais_user !== $pais) ? get_permalink(get_the_ID()) . strtolower($pais_user) . "/" . $param_url : "#" ?>" style="font-size: .8rem"><?= $all_paises[$pais_user]['name'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="dropdown mb-5">
                <a href="#" class="caja-dropdown text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: .9rem">
                    <div class="icono_user rounded-circle">JZ</div>
                    <div class="d-full">
                        <strong><?= $this->user->first_name ?></strong><br><small> <?= $this->user->stockist ?></small>
                    </div>

                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownUser" style="">
                    <li><a class="dropdown-item" href="<?= get_permalink(get_the_ID()) ?>?action=edit_user" style="font-size: .8rem">Editar perfil</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?= wp_logout_url(get_permalink(get_the_ID())) ?>" style="font-size: .8rem">Salir</a></li>
                </ul>
            </div>
        </div>
        <script>
            jQuery(function($) {
                $('.hamburger').click(function() {
                    $padre = $(this).parents('.menu-flex');

                    console.log($padre);
                    if ($padre.hasClass('active')) {
                        $padre.removeClass('active');
                    } else {
                        $padre.addClass('active');
                    }

                })
            });
        </script>
    <?php
        $this->menu = ob_get_clean();
    }

    public function satrack_data_scripts($opciones, $datos, $pais)
    {
        if ($opciones->decimales > 0) {
            $this->pasos_num = 1 / pow(10, $opciones->decimales);
            $this->ceros = "0." . str_repeat("0", $opciones->decimales);
        }

        $pais = strtolower($pais);

        wp_localize_script(
            'cw_satrack-js', // Script al que se quiere añadir la variable
            'datos_cw', // Variable que se va a crear en JavaScript
            [
                'pais' => $pais,
                'decima' => $opciones->decimales,
                'imp_mes' => $opciones->imp_mes,
                'impuesto' => $opciones->valor_imp,
                'name_impuesto' => $opciones->imp,
                'lista_reglas' => $opciones->reglas,
                'lista_equipos' => $datos->equipos,
                'lista_tipoInst' => $datos->inst_equipos,
                'lista_planes' => $datos->planes,
                'lista_acc' => $datos->acc,
                /*'wqef' => $this->user->ID,*/
                'NONCE' => wp_create_nonce('cotizador_web_satrack'),
                'vendedor' => array(
                    'nombre' => $this->user->first_name . " " . $this->user->last_name,
                    'mobile' => $this->user->mobile,
                    'cargo' => $this->user->cargo,
                    'phone' => $this->user->phone,
                    'mail' => $this->user->user_email,
                    'dist' => $this->user->stockist,
                    'calendly' => $this->user->calendly,
                    'user_url' => $this->user->user_url,
                    'address' => $this->user->address
                )
            ]
        );

        wp_enqueue_script('cw_satrack-js');

        add_filter("script_loader_tag", array($this, "add_module_to_my_script"), 10, 3);
    }

    public function add_module_to_my_script($tag, $handle, $src)
    {
        if ("cw_satrack-js" === $handle) {
            $tag = '<script id="' . $handle . '" type="module" src="' . esc_url($src) . '?v=3.0.0" ></script>';
        }

        return $tag;
    }

    public function list_cotizacion($cotizaciones, string $titulo, $pais = 'COL')
    {
        global $ads_data;
        $this->create_menu($pais);
        $message = "";
        $tipo_cli = ["Alguna vez coticé sus servicio" => "Ya cotizo", "Primera vez que tengo contacto" => "Primera vez", "Soy excliente" => "Excliente", "Soy cliente actual" => "Cliente actual"];
        $list_prop = [];

        ob_start(); ?>
        <main class="cont-cotizador d-flex flex-grow-1" style="overflow: hidden;">
            <?= $this->menu ?>


            <div class='lista_cotizaciones w-100 p-md-3 p-lg-4' style="overflow: hidden;">
                <h1 class='titulo-seccion'><?= $titulo ?></h1>
                <div class='d-flex flex-wrap justify-content-center justify-content-md-between w-100 mb-4'>
                    <div class='d-flex align-items-center m-2'>
                        <span class="mx-2">Desde:</span><input type="text" id="min" name="min">
                    </div>
                    <div class='ms-2 d-flex align-items-center m-2'>
                        <span class="mx-2">Hasta:</span><input type="text" id="max" name="max">
                    </div>
                    <div class="d-flex flex-fill justify-content-center justify-content-md-end" id='botones-tabla'></div>
                </div>
                <hr class="mb-4">
                <table class='table' id='tabla_cotizaciones' data-order='[ 1, "desc" ]' style='font-size:.9rem;'>
                    <thead>
                        <tr>
                            <th class="d-none d-lg-table-cell">ID</th>
                            <th class="d-none d-xl-table-cell">Fecha</th>
                            <th>Nombre</th>
                            <th>Empresa</th>
                            <th class="d-none d-lg-table-cell">Email</th>
                            <th class="d-none d-lg-table-cell">Teléfono</th>
                            <th class="d-none d-lg-table-cell">Ciudad</th>
                            <th>Asesor</th>

                            <th style='min-width: 120px;' class='text-center'>Acciones</th>
                            <th class='d-none'>Data</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($cotizaciones as $proc) :
                            $propu = json_decode($proc->datos);

                            $nombre = ucwords(strtolower($proc->nombre_cliente . ' ' . $proc->apellido_cliente));
                        ?>
                            <tr>
                                <td class="d-none d-lg-table-cell"><?= str_pad($proc->id, 5, "0", STR_PAD_LEFT) ?></td>
                                <td class="d-none d-xl-table-cell" data-order="<?= strtotime($proc->date_updated) ?>"><?= date("d M Y H:i", strtotime($proc->date_updated)) ?></td>
                                <td><?= $nombre ?></td>
                                <td><?= $proc->empresa ?></td>
                                <td class="d-none d-lg-table-cell"><?= $proc->email ?></td>
                                <td class="d-none d-lg-table-cell"><?= $proc->phone ?></td>
                                <td class="d-none d-lg-table-cell"><?= ucwords(strtolower($proc->ciudad)) ?></td>
                                <?php
                                $user_temp = get_userdata($proc->user_wp);
                                $nombre = $user_temp->display_name;
                                ?>
                                <td><?= $nombre ?></td>

                                <td style='min-width: 120px;' class='text-center'>
                                    <?= get_permalink(get_the_ID()) . strtolower($pais) . "/?action=pdf_cotizacion&id_cot={$proc->id}&file=file.pdf" ?>
                                </td>
                                <td class='d-none'>
                                    <?= $proc->datos ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <script type='text/javascript'>
                    function cortar_texto(texto, largo = 16) {
                        if (texto.length > largo) {
                            texto = texto.substring(0, largo) + '...';
                        }
                        return texto;
                    }

                    var validiar_data = function(data, type, row, meta) {
                        return (type === 'display') ? cortar_texto(data) : data;
                    }


                    const tablaDraw = [{
                            target: 2,
                            render: function(data, type, row, meta) {
                                return (type === 'display') ? '<a href="http://satra/cotizador-web/col/?action=view_cotizacion&id="' + row[0] + ' class="text-secondary"><strong>' + cortar_texto(data) + '</strong></a>' : data;
                            }
                        },
                        {
                            target: 3,
                            render: validiar_data
                        },
                        {
                            target: 6,
                            render: validiar_data
                        },
                        {
                            target: 7,
                            visible: <?= ($this->user->is_admin) ? "true" : "false"; ?>,
                            render: validiar_data
                        },
                        {
                            target: 8,
                            visible: true,
                            searchable: false,
                            orderable: false,
                            render: function(data, type, row, meta) {
                                return (type === 'display') ? '<a href="' + data + '" class="text-primary mx-2" target="_blank"><svg class="bi bi-filetype-pdf" fill="currentColor" width="1em" height="1em"><use xlink:href="#pdf-file"></use></svg></a> | <?php if (!isset($_GET['action']) || $_GET['action'] != 'cotizaciones_aprobadas') : ?><a href="#" class="boton text-success mx-2 apro-coti" data-row="' + meta.row + '" data-id="' + row[0] + '"><svg class="bi bi clipboard2 check" fill="currentColor" width="1em" height="1em"><use xlink: href="#check"></use></svg ></a> | <a href = "#" class="boton text-danger mx-2 del-coti" data-id="' + row[0] + '" ><svg class="bi bi-trash3" fill="currentColor" width="1em" height="1em" ><use xlink: href="#trash"></use></svg></a><?php else : ?><a href = "#" class="boton text-danger mx-2 rest-coti" data-id="' + row[0] + '" ><svg class="bi bi-trash3" fill="currentColor" width="1em" height="1em" ><use xlink: href="#rest"></use></svg></a><?php endif; ?>' : data;
                            }
                        },
                        {
                            target: 9,
                            visible: false,
                            searchable: true,
                            orderable: false
                        }
                    ];
                </script>
            </div>
        </main>

    <?php
        $cuerpo = ob_get_clean();
        return $cuerpo;
    }

    public function new_cotizacion_front($pais)
    {
        global $ads_data;
        $db_datos = new DB_Config($pais);

        $opciones = $ads_data->get_options();
        $datos = $db_datos->get_datos();

        $this->satrack_data_scripts($opciones, $datos, $pais);
        $this->create_menu($pais);
        $message = "";

        $cuerpo = "<main class='cont-cotizador d-flex flex-grow-1'>
        {$this->menu}<div class=' p-md-3 p-lg-4 flex-fill'><h1 class='titulo-seccion'>Nueva Cotización</h1>
			<div id='contenedor_prop'></div><!--END div.contendio--></div>
		</main>
		";

        return $cuerpo;
    }

    public function raw_selector_pais()
    {
        global $ads_data;
        $this->create_menu();
        $all_paises = $ads_data->get_list_paises();
        $paises_user = $ads_data->get_list_paises_user();


        $cuerpo = "<main class='cont-cotizador d-flex flex-grow-1'>
        {$this->menu}<div class='p-5 flex-fill'><h1 class='fs-2 fw-bold mt-3' style='color: var( --e-global-color-primary); line-height: 1'>Dashboard</h1><hr class='mb-4'>
        	<div id='selector_pais'>";
        foreach ($paises_user as $pais) {
            $id_pais = strtolower($pais);
            $cuerpo .= "<a class='btn btn-pais' href='" . get_permalink(get_the_ID()) . $id_pais . "/'><svg class='' fill='currentColor' width='2em' height='2em'><use xlink:href='#band_{$id_pais}'></use></svg> " . $all_paises[$pais]['name'] . "</a>";
        }
        $cuerpo .= "</div><!--END div.selector_pais--></div></main";

        return $cuerpo;
    }

    public function edit_user_front($user, $pais = null)
    {
        //$this->html->create_form_options_user();
        $this->create_menu($pais);
        $user_nombres = $this->user->user_firstname;
        $user_apellidos = $this->user->user_lastname;
        /*$user_cargo = esc_attr(get_the_author_meta('user_cargo', $userid));
        $user_phone = esc_attr(get_the_author_meta('user_phone', $userid));
        $user_tele = esc_attr(get_the_author_meta('user_tele', $userid));
        $user_dir = esc_attr(get_the_author_meta('user_dir', $userid));
        $user_calendly = esc_attr(get_the_author_meta('user_calendly', $userid));
        $user_url = esc_attr(get_the_author_meta('user_weburl', $userid));*/

        ob_start(); ?>
        <main class="cont-cotizador d-flex flex-grow-1">
            <?= $this->menu ?>
            <div class='form-edit-user w-100 p-5'>
                <h1 class='titulo-seccion'>Datos del Asesor</h1>
                <form method='post'>
                    <?= wp_nonce_field('/cotizador-web/?action=edit_user', 'update_user', true) ?>
                    <div class='container-fluid' style='max-width:600px; padding-bottom:50px;'>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_nombres'>Nombres</label></div>
                            <div class='col-8'><input type='text' name='user_nombres' id='user_nombres' class='form-control' placeholder='Su cargo personalizado' value='<?= $this->user->user_firstname ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_apellidos'>Apellidos</label></div>
                            <div class='col-8'><input type='text' name='user_apellidos' id='user_apellidoso' class='form-control' placeholder='Su cargo personalizado' value='<?= $this->user->user_lastname ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_phone'>Cargo</label></div>
                            <div class='col-8'><input type='text' name='user_cargo' id='user_cargo' class='form-control' placeholder='Su cargo personalizado' value='<?= $this->user->cargo ?>'></div>
                        </div>
                        <hr>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_phone'>Celular</label></div>
                            <div class='col-8'><input type='tel' name='user_phone' id='user_phone' class='form-control' placeholder='000 000 0000' value='<?= $this->user->mobile ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_tele'>Teléfono</label></div>
                            <div class='col-8'><input type='tel' name='user_tele' id='user_tele' class='form-control' placeholder='000 0000' value='<?= $this->user->tele ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_dir'>Dirección</label></div>
                            <div class='col-8'><input type='text' name='user_dir' id='user_dir' placeholder='Dirección personalizada' class='form-control' value='<?= $this->user->address ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_calendly'>URL calendly</label></div>
                            <div class='col-8'><input type='url' name='user_calendly' id='user_calendly' placeholder='https://' class='form-control' value='<?= $this->user->calendly ?>'></div>
                        </div>
                        <div class='row py-1'>
                            <div class='col-4'><label for='user_weburl'>Sitio web</label></div>
                            <div class='col-8'><input type='url' name='user_url' id='user_url' placeholder='https://sitioweb.com' class='form-control' value='<?= $this->user->user_url ?>'></div>
                        </div>
                        <div class='row py-2'>
                            <div class='col text-end'><input type='submit' value='Guardar' class='btn btn-primary'></div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
<?php

        $cuerpo = ob_get_clean();
        return $cuerpo;
    }
}
