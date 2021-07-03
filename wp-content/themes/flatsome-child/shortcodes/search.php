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
                <a class="B_clear" type="button"><i class="fa fa-close"></i></a>
            </div>

            <article class="jugadores">
                <table class="table_players">
                    <thead id="thead_players"></thead>
                    <tbody class="tbody_players" id="tbody_players"></tbody>
                </table>
            </article>

            <article class="equipos">
                <table class="table_teams">
                    <thead id="thead_teams"></thead>
                    <tbody class="tbody_teams" id="tbody_teams"></tbody>
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

        .B_clear {
            background-color: red;
            color: #FFF;
            margin-left: 5px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 1px;
            border-radius: 5px;
        }

        .B_clear:hover {
            color: #FFF;
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

        #img_players {
            display: flex;
        }

        #img_teams {
            height: 90px;
            width: 90px;
        }

        #teamsRow {
            display: flex;
            padding: 10px;
        }

        #teamName {
            font-size: 20px;
            font-weight: bold;
        }

        .whatsapp_players span,
        .whatsapp_teams span {
            color: white;
            font-weight: bold;
            font-size: 17px;
            vertical-align: middle;
        }

        .whatsapp_players img,
        .whatsapp_teams img {
            height: 25px;
            width: 25px;
            margin-right: 3px;
        }

        .whatsapp_players,
        .whatsapp_teams {
            border-radius: 5px;
            text-align: center;
            margin: 10px 0;
            white-space: nowrap;
            background: #1bd741;
            padding: 0 10px;
            display: inline-block;
        }

        .whatsapp_players {
            height: 85%;
        }

        #data_user {
            margin-top: 10px;
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
                //$('.tbody').empty();
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
                        switch (articleActive) {
                            case 'jugadores':
                                //Limpia el tbody antes de mostrar otros datos
                                $('.tbody_players').empty();

                                r.data.forEach(e => {
                                    let num = "<a target='blank' href='whatsapp://send?phone=57" + e.telefono + "' class='whatsapp_players'>" +
                                        "<img src='" + window.location.origin + "/wp-content/uploads/2021/05/whatsapp_icon.png'>" +
                                        "<span>" + e.telefono + "</span>" +
                                        "</a>";

                                    aux =
                                        "<tr>" +
                                        "<td>" +
                                        "<div id='img_players'>" +
                                        "<img src='" + (e.profile_picture == "" || e.profile_picture == undefined ? window.location.origin + "/wp-content/uploads/2021/05/user_avatar.png" : e.profile_picture) + "'/>" +
                                        "<div id='data_user'>" +
                                        e.nombre[0] + " " + e.apellido[0] +
                                        "<br>" + (e.posicion == "" || e.posicion == undefined ? "" : e.posicion) +
                                        "</div>" +
                                        "</div>" +
                                        "<br />" + (e.telefono == undefined || e.telefono == "" ? "" : num) +
                                        "</td>" +

                                        "<td id='secondTD'>" +
                                        "Pase: " + (e.stats_pac == undefined ? "0" : e.stats_pac) +
                                        "<br>Regate: " + (e.stats_sho == undefined ? "0" : e.stats_sho) +
                                        "<br>Visión: " + (e.stats_pas == undefined ? "0" : e.stats_pas) +
                                        "<br>Fuerza: " + (e.stats_dri == undefined ? "0" : e.stats_dri) +
                                        "<br>Defensa: " + (e.stats_def == undefined ? "0" : e.stats_def) +
                                        "<br>Físico: " + (e.stats_phy == undefined ? "0" : e.stats_phy) +
                                        "</td>" +
                                        "</tr>";

                                    $('.tbody_players').append(aux);
                                });
                                break;

                            case 'equipos':
                                //Limpia el tbody antes de mostrar otros datos
                                $('.tbody_teams').empty();

                                r.data.forEach(e => {
                                    let num = "<a target='blank' href='whatsapp://send?phone=57" + e[1].telefono + "' class='whatsapp_teams'>" +
                                        "<img src='" + window.location.origin + "/wp-content/uploads/2021/05/whatsapp_icon.png'>" +
                                        "<span>" + e[1].telefono + "</span>" +
                                        "</a>";

                                    aux =
                                        "<tr id='teamsRow'>" +

                                        "<td>" +
                                        "<div>" +
                                        "<img id='img_teams' src='" + (e[0].logo_url == "" || e[0].logo_url == undefined ? window.location.origin + "/wp-content/uploads/2021/05/cancha.png" : e[0].logo_url) + "'/>" +
                                        "</div>" +
                                        "</td>" +

                                        "<td>" +
                                        "<p id='teamName'>" + e[0].nombre + "</p>" +
                                        "<p>" + e[0].descripcion + "</p>" +
                                        "<p>Futbol " + e[0].tipo + "</p>" +
                                        (e[1].telefono == undefined || e[1].telefono == "" ? "" : num) +
                                        "</td>" +

                                        "</tr>";

                                    $('.tbody_teams').append(aux);
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