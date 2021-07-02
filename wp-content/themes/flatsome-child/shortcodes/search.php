<?php
global $wpdb;

$teams = $wpdb->get_results("SELECT * FROM wp_teams");

$directory = "/wp-content/uploads/2021/05/user_avatar.png";
?>
<div class="fake_header">
    <a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
    <h2 class="title">Buscar</h2>
</div>
<div class="fake_body">
    <div class="cont">
        <ul>
            <div id="jugadores">Jugadores</div>
            <div id="equipos">Equipos</div>
        </ul>

        <div class="sections" style="font-size: 15;">
            <div>
                <input placeholder="Buscar" class="input" />
                <a type="button"><i class="fa fa-close"></i></a>
            </div>

            <article class="jugadores">
                <table class="table_players">
                    <thead id="thead_players"></thead>
                    <tbody class="tbody" id="tbody_players"></tbody>
                </table>
            </article>

            <article class="equipos">
                <table class="table_teams">
                    <thead id="thead_teams"></thead>
                    <tbody class="tbody" id="tbody_teams"></tbody>
                </table>
            </article>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>

<?php add_action('wp_footer', function () { ?>
    <style>
        .fake_body {
            margin: 75px 10px 10px 10px;
        }

        .fake_header .icon.left {
            position: absolute;
            top: 10px;
            left: 15px;
            color: white;
            padding: 10px;
            z-index: 5;
        }

        .fake_header .title {
            position: absolute;
            top: 23px;
            left: 15px;
            color: white;
            text-align: center;
            width: 93%;
        }

        .fake_header {
            position: fixed;
            top: 0;
            left: 0;
            background: #004454;
            width: 100%;
            padding: 5px;
            color: white;
            height: 70px;
            z-index: 9999;
        }

        * {
            margin: 0;
            padding: 0;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .cont {
            width: 950px;
            max-width: 100%;
            margin: auto;
        }

        .cont ul {
            width: 100%;
            background-color: #004454;
            list-style: none;
            display: flex;
            overflow-x: scroll;
            overflow: auto;
        }

        .cont ul div {
            padding: 5px;
            color: #FFF;
            text-decoration: none;
        }

        .active {
            background-color: #4e657b;
        }

        .jugadores {
            overflow-y: scroll;
            height: 65vh;
        }

        .equipos {
            overflow-y: scroll;
            height: 65vh;
        }

        /* @media screen and (height: 850px) {
            .jugadores {
                overflow-y: scroll;
            }
        } */

        .sections div {
            display: flex;
            height: 30px;
            margin-bottom: 15px;
        }

        .sections div a {
            background-color: red;
            color: #FFF;
            margin-left: 5px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 1px;
            border-radius: 5px;
        }

        .input {
            width: 100%;
            height: 100%;
            padding-left: 10px;
            border-radius: 5px;
        }

        table thead tr td {
            font-weight: bolder;
            font-size: 15px;
        }

        /*#secondTD {}*/

        table tbody tr td {
            font-size: 13px;
        }

        table tbody tr td img {
            height: 60px;
            width: 55px;
            margin-right: 15px;
        }

        #div_img_players {
            display: flex;
        }

        #div_img_teams {
            height: 50px;
            width: 100px;
        }

        #td_teamsRow {
            display: flex;
        }
    </style>
    <script>
        jQuery(function($) {
            var articleActive;

            //Maneja la navegación de las pestañas
            $('div.cont ul div:first').addClass('active'); //Al iniciar se selecciona el primer div de la etiqueta ul
            $('.sections article').hide(); //Esconde todos los "article" de sections
            $('.sections article:first').show(); //Mustra el primer article

            $('div.cont ul div').click(function() {
                $('div.cont ul div').removeClass();
                $(this).addClass('active');
                $('.sections article').hide();

                articleActive = $(this).attr('id');
                $('.' + articleActive).show();
                $('.input').val('');
                get_Data();
            });

            //Se ejecuta al cargarse la página
            $(document).ready(function() {
                articleActive = $('div.cont ul div:first').attr('id');
                $('#thead_players').append("<tr><td>Datos</td><td>Estadísticas</td></tr>"); //Se pone el thead en la tabla
                clean_button();
                get_Data();
            });

            //Muestra el botón de eliminar
            function clean_button() {
                if ($('.input').val() == "") {
                    $('.sections div a').hide();
                } else {
                    $('.sections div a').show();
                }
            }

            $('.sections div a').click(function() {
                $('.input').val('');
                clean_button();
                get_Data();
                $('.tbody').empty();
            });

            //Se manejan los valores que se escriben en el input para filtrar los datos
            $('.input').keyup(function() {
                clean_button();
                get_Data();
            })

            function get_Data() {
                $.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=filter_search', {
                        'screen': articleActive,
                        'value': ($('.input').val() == "" ? "" : $('.input').val())
                    },
                    function(r) {
                        //Limpia el tbody antes de mostrar otros datos
                        $('.tbody').empty();

                        switch (articleActive) {
                            case 'jugadores':
                                r.data.forEach(e => {
                                    aux =
                                        "<tr>" +
                                        "<td><div id='div_img_players'><img src='" + (e.profile_picture == "" || e.profile_picture == undefined ? window.location.origin + "/wp-content/uploads/2021/05/user_avatar.png" : e.profile_picture) + "'/>" +
                                        "<div>" + e.nombre[0] + " " + e.apellido[0] +
                                        "<br>" + (e.posicion == "" || e.posicion == undefined ? "" : e.posicion) +
                                        "<br>" + (e.telefono == "" || e.telefono == undefined ? "" : e.telefono) + "</div></div></td>" +


                                        "<td id='secondTD'>" +
                                        "Pase: " + (e.stats_pac == undefined ? "0" : e.stats_pac) +
                                        "<br>Regate: " + (e.stats_sho == undefined ? "0" : e.stats_sho) +
                                        "<br>Visión: " + (e.stats_pas == undefined ? "0" : e.stats_pas) +
                                        "<br>Fuerza: " + (e.stats_dri == undefined ? "0" : e.stats_dri) +
                                        "<br>Defensa: " + (e.stats_def == undefined ? "0" : e.stats_def) +
                                        "<br>Físico: " + (e.stats_phy == undefined ? "0" : e.stats_phy) +
                                        "</td>" +
                                        "</tr>";

                                    $('.tbody').append(aux);
                                });
                                break;

                            case 'equipos':
                                r.data.forEach(e => {
                                    console.log(e[0].logo_url)
                                    aux =
                                        "<>" +
                                        "<td id='td_teamsRow'>" +
                                        "<div id='div_img_teams'>" +
                                        "<img src='" + (e[0].logo_url == "" || e[0].logo_url == undefined ? window.location.origin + "/wp-content/uploads/2021/05/cancha.png" : e[0].logo_url) + "'/>" +
                                        "</div>" +
                                        "<div>" +
                                        "<br /><p>" + e[0].nombre + "</p>" +
                                       /*  "<br /><p>" + e[0].descripcion + "</p>" +
                                        "<br /><p>Futbol " + e[0].tipo + "</p>" +
                                        "<br /><p>Tel: " + e[1].telefono + "</p>" +
                                        "</div>" +
                                        "</td>" + */
                                        "</tr>";

                                    $('.tbody').append(aux);
                                });
                                break;

                            default:
                                console.log('Pestaña con encontrada')
                                break;
                        }

                    }
                );
            }
        });
    </script>
<?php }); ?>