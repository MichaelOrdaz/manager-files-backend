<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script defer src="index.js"></script>
    <title>Calificaciones</title>
    <style>
        table{
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            table-layout: fixed;
            width: 100%;
            max-width: 400px;
        }
        .imagen{
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            max-height: 55px;
            width: 90%;
        }
        /* Bordes */

        .bordes{border: solid 1px black; border-collapse: collapse} /*Ésta clase aplica bordes de forma general a un elemento */
        .border-left{border-left: solid 1px black; border-collapse: collapse}
        .border-right{border-right: solid 1px black; border-collapse: collapse}
        .border-top{border-top: solid 1px black; border-collapse: collapse}
        /* tr,td{border: solid 1px black; border-collapse: collapse} */ /* Borde global */

        /* Colores */
        .bg-primary{background-color: #2B2E3E}

        /* Tipografía */
        .font-white{color: white}
        .text-center{text-align: center}
        .text-end{ text-align: end }
        .leyenda{ font-size: 10px }
        .texto-14{font-size: 14px}
        .texto-12{font-size: 12px}
        p{font-size: 10px}

        /*Estilos especiales de fila de Materias*/
        .materia{
            text-align:start;
            height: 120px;
        }
        .materia p{
            transform-origin:50% 50%;
            transform: rotate(-90deg);
        }
        .materias{
            height: 120px;
        }
        .encabezado{
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div style="border:1px solid black;">
        <div style="min-height:30px;box-sizing:content-box;border:1px solid black;">
            <table>
                <tr>
                    <td style="width:10%;box-sizing:content-box;margin:0px;padding:0px;border-right:1px solid black;">
                        <center>
                            <img
                                src="http://qa-bachiller.puller.mx/images/logo.png"
                                class="imagen"
                                alt="logo"
                            >
                        </center>
                    </td>
                    <td style="width:90%;margin:0px;padding:0px;box-sizing:content-box;">
                        <table>
                            <tr>
                                <td class="text-center bordes encabezado">
                                    ACADEMIA DE FORMACIÓN Y DESARROLLO POLICIAL PUEBLA - INICIATIVA MÉRIDA
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center bordes encabezado">
                                    BACHILLERATO TÉCNICO EN SEGURIDAD CIUDADANA
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center bordes encabezado">
                                    C.C.T. 21ECT0019R ZONA ESCOLAR 073
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <table>
            <tr>
                <td colspan="10" class="bg-primary text-center font-white" style="width:100%;"><div class="texto-14">REPORTE DE EVALUACIÓN {{ $nombreOrdinalDelPeriodo }} SEMESTRE</div></td>
            </tr>
            <tr class="bordes">
                <td colspan="2" class="bordes texto-12">
                    <b>ALUMNO (A):</b>
                </td>
                <td colspan="8" class="bordes" style="font-size:13px;">
                    <b>{{ $nombre_completo }}</b>
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="2" class="exact-width-200">
                    <p class="texto-12"><b> GRADO: {{ $grado }} </b></p>
                </td>
                <td colspan="5" class="bordes">
                    <p class="texto-12"><b> GRUPO: {{ $grupo }} </b></p>
                </td>
                <td colspan="3" class="bordes">
                    <p class="texto-12"><b>CICLO ESCOLAR: {{ $ciclo_escolar }}</b></p>
                </td>
            </tr>
            <!--    Le linea de abajo es un separador vacio-->
            <tr class="bg-primary bordes">
                <td colspan="12"></td>
            </tr>
            <tr class="materias bordes">
                <td colspan="1" class="bg-primary font-white exact-width-200 bordes"><p>MATERIAS</p></td>

                @foreach(range(0,7) as $index)
                    <td colspan="1" class="materia bordes">
                        <p> {{optional(optional($materias)[$index])->materia_nombre}} </p>
                    </td>
                @endforeach

                <td colspan="1" class="exact-width-200 bg-primary font-white text-center "><p>Promedio de unidad</p></td>
            </tr>
            <tr class="bordes">
                <td colspan="1" class="exact-width-200 exact-width-200 bordes"><p>PRIMER MOMENTO</p></td>

                @foreach(range(0,7) as $index)
                    <td colspan="1" class="bordes">
                        <p class="text-center">
                            {{optional(optional($materias)[$index])->parcial_uno}}
                        </p>
                    </td>
                @endforeach

                <td colspan="1" class="text-center bordes texto-12">
                    {{optional($promedios_parciales)[0]}}
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="1" class="exact-width-200 exact-width-200 bordes"><p>SEGUNDO MOMENTO</p></td>

                @foreach(range(0,7) as $index)
                    <td colspan="1" class="bordes">
                        <p class="text-center">
                            {{optional(optional($materias)[$index])->parcial_dos}}
                        </p>
                    </td>
                @endforeach
                <td colspan="1" class="text-center bordes texto-12">
                    {{optional($promedios_parciales)[1]}}
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="1" class="exact-width-200 exact-width-200 bordes"><p>TERCER MOMENTO</p></td>

                @foreach(range(0,7) as $index)
                    <td colspan="1" class="bordes">
                        <p class="text-center">
                            {{optional(optional($materias)[$index])->parcial_tres}}
                        </p>
                    </td>
                @endforeach
                <td colspan="1" class="text-center bordes texto-12">
                    {{optional($promedios_parciales)[2]}}
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="1" class="exact-width-200 bordes"><p>PUNTAJE TOTAL</p></td>
                @foreach(range(0,7) as $index)
                    <td colspan="1" class="bordes">
                        <p class="text-center">
                            {{ optional($puntaje_total)[$index] }}
                        </p>
                    </td>
                @endforeach

                <td colspan="1" class="text-center bg-primary font-white bordes">
                    <p> <b> PROMEDIO DEL SEMESTRE </b> </p>
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="1" class="bordes bg-primary font-white exact-width-200"><p>PROMEDIO DE LA MATERIA</p></td>
                @foreach(range(0,7) as $index)
                    <td colspan="1" class="bordes">
                        <p class="text-center texto-12">
                            {{ optional($promedio_materia)[$index] }}
                        </p>
                    </td>
                @endforeach
                <td colspan="1" class="text-center bordes texto-12">
                    {{$promedio_semestral}}
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="10" class="text-center  ">
                    <p class="texto-14"> <b> C. ALMA ROSA VARGAR GARCÍA </b> </p>
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="10" class="text-center  bg-primary font-white ">
                    <p class="texto-14"> <b> DIRECTORA </b> </p>
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="10" class="text-center bordes">
                    <p class="texto-14">
                        <b>BACHILLERATO TÉCNICO EN SEGURIDAD CIUDADANA</b>
                    </p>
                </td>
            </tr>
            <tr class="bordes">
                <td colspan="10" class="text-center bordes">
                    <p class="texto-14">
                        <b>TELÉFONO 22 21 44 10 10 EXT. 30111 - 30112 - 30113</b>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    <table style="border:none;">
        <tr>
            <td></td>
            <td>
                <p class="leyenda text-center">
                    Camino a Santa Cruz Alpuyeca, Km 5.5 Cahachapa, C.P 72700 Amozoc de <br>
                    Mota, Pue. Tel. 2221441000 Ext. 30113 <br>
                    aiz.direccionbachillerato@gmail.com
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
