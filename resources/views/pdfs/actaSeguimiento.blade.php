<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acta de seguimiento</title>
  @php($totalAlumnos = sizeof($alumnos))
  @php ($meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'] )
</head>
<body>
  <!-- TITULOS -->
  <div class="full-witdh">
      <p class="text-20 text-center" style="padding-top: 10px;padding-bottom: 7px;">
        SECRETARIA DE EDUCACIÓN DEL ESTADO DE PUEBLA
      </p>
      <p class="text-14 text-center" style="padding-top: 7px;padding-bottom: 7px;">
        CUADRO ACTA DE SEGUIMIENTO EVALUATORIO
      </p>
  </div>

  <!-- ENCABEZADO -->
  <div class="full-width" style="margin-bottom: 7px;">
    <table class="center-table text-center border-table" border="1">
      <tr class="bg-gray">
        <td>BACHILLERATO</td>
        <td>NOMBRE</td>
        <td>CLAVE</td>
      </tr>
      <tr class="text-bold">
        <td>GENERAL OFICIAL</td>
        <td> BACHILLERATO TÉCNICO EN SEGURIDAD CIUDADANA </td>
        <td> 21ECT0019R </td>
      </tr>
    </table>
    <table
      class="center-table text-center border-table" border="1"
      style="margin-top: -3px;"
    >
      <tr class="bg-gray">
        <td>LOCALIDAD</td>
        <td>MUNICIPIO</td>
        <td>TURNO</td>
      </tr>
      <tr class="text-bold">
        <td>CAMINO VECINAL A SANTA CRUZ ALPUYECA KM. 6.5 AMOZOC DE MOTA</td>
        <td>PUEBLA</td>
        <td>MATUTINO</td>
      </tr>
    </table>
    <table
      class="center-table text-center border-table" border="1"
      style="margin-top: -3px;"
    >
      <tr class="bg-gray">
        <td style="width:30%;">MODALIDAD EDUCATIVA</td>
        <td style="width:25%;">CICLO ESCOLAR</td>
        <td style="width:55%;">MATERIA</td>
      </tr>
      <tr class="text-bold">
        <td>BACHILLERATO GENERAL</td>
        <td> {{ $ciclo_escolar }} </td>
        <td class="bg-yellow"> {{ $materia_nombre }} </td>
      </tr>
    </table>
    <table
      class="center-table text-center border-table" border="1"
      style="margin-top: -3px;"
    >
      <tr class="bg-gray">
        <td style="width: 15%;">SEMESTRE</td>
        <td style="width: 15%;">GRADO</td>
        <td style="width: 10%;">GRUPO</td>
        <td style="width: 65%;">PROFESOR DE LA MATERIA</td>
      </tr>
      <tr class="text-bold">
        <td> {{ $semestre }} </td>
        <td> {{ $grado }} </td>
        <td> {{ $grupo }} </td>
        <td> {{ $docente_nombre }} </td>
      </tr>
    </table>
  </div>

  <!-- LISTADO ALUMNOS -->
  <div class="full-width" style="margin-bottom: 30px;">
    <table class="center-table text-center border-table" border="1">
      <tr>
        <td style="width: 5%;">No.</td>
        <td style="width: 33%;">NOMBRE DEL ALUMNO</td>
        <td style="width: 8%;"> Asistencia </td>
        <td style="width: 5%;">%</td>
        <td style="width: 16%;">
          <table class="text-center border-table" border="1" style="border: none;">
            <tr>
              <td colspan="3"> EVALUACIONES PARCIALES </td>
            </tr>
            <tr class="text-bold bg-gray">
              <td style="width: 33%;">1er</td>
              <td style="width: 33%;">2do</td>
              <td style="width: 34%;">3r</td>
            </tr>
          </table>
        </td>
        <td style="width: 5%;">SUMA</td>
        <td style="width: 8%;">Calificación <br> semestral </td>
        <td style="width: 6%;">LETRA</td>
        <td style="width: 14%;">OBSERVACIONES</td>
      </tr>
    </table>
    <!-- Listado de alumnos -->
    <table
      class="center-table text-center border-table" border="1"
      style="margin-top: -2px;"
    >

      @foreach($alumnos as $index => $alumno )
        <tr>
          <td style="width: 5%;">  {{ ($index+1) }} </td>
          <td style="width: 33%;">  {{ $alumno['alumno_nombre'] }}  </td>
          <td style="width: 8%;">   {{ $alumno['asistencia_total'] }}  </td>
          <td style="width: 5%;">   {{ $alumno['porcentaje'] }}   </td>
          <td style="width: 16%;">
            <table class="text-center full-width border-table" border="1" style="border: none;">
              <tr class="text-bold">
                <td style="width: 33%;" > {{ $alumno['parcial_uno'] }}  </td>
                <td style="width: 33%;" > {{ $alumno['parcial_dos'] }}  </td>
                <td style="width: 34%;" > {{ $alumno['parcial_tres'] }}  </td>
              </tr>
            </table>
          </td>
          <td style="width: 5%;"> {{ $alumno['suma'] }}  </td>
          <td style="width: 8%;"> {{ $alumno['calificacion_semestral'] }}  </td>
          @if($alumno['calificacion_semestral'] > 5 )
            <td style="width: 6%;"> <b> {{ $alumno['calificacion_letra'] }} </b> </td>
            <td style="width: 14%;">{{ $alumno['observaciones'] }}  </td>
          @else
            <td class="bg-red" style="width: 6%;"> <b> {{ $alumno['calificacion_letra'] }} </b> </td>
            <td class="bg-red" style="width: 14%;">{{ $alumno['observaciones'] }}  </td>
          @endif
        </tr>
      @endforeach
    </table>
  </div>

  <!-- BOOTOM -->
  <div class="pie-pagina">
    <table class="full-width">
      <tr class="text-center">
        <td style="width:30%;"></td>
        <td style="width:30%;">Vo. Bo.</td>
        <td style="width:40%;">PROMEDIO DE APROVECHAMIENTO SEMESTRAL: {{ $promedio_grupo }} </td>
      </tr>
      <tr class="text-center">
        <td>Docente</td>
        <td>Encargado del Bachillerato</td>
        <td></td>
      </tr>
      <tr class="text-center">
        <td>
          <p style="margin-bottom: -70px;">
            <br>
              ______________________________
            <br>
            ALICIA LERÍN JIMÉNEZ
          </p>
        </td>
        <td>
          <p style="margin-bottom: -70px;">
            <br>
              ______________________________
            <br>
            C. ALMA ROSA VARGAS GARCÍA
          </p>
        </td>
        <td>
          <table class="full-width border-table" border="1">
            <tr class="bg-gray text-center">
              <td>CONCEPTO</td>
              <td>H</td>
              <td>M</td>
              <td>T</td>
            </tr>
            <tr>
              <td>INSCRITOS</td>
              <td class="text-bold text-center"> {{ $total_alumnos_masculinos }} </td>
              <td class="text-bold text-center"> {{ $total_alumnos_femininos }} </td>
              <td class="text-bold text-center"> {{ $total_alumnos }} </td>
            </tr>
            <tr>
              <td>EXISTENCIA</td>
              <td class="text-bold text-center"> {{ $total_alumnos_masculinos }} </td>
              <td class="text-bold text-center"> {{ $total_alumnos_femininos }} </td>
              <td class="text-bold text-center"> {{ $total_alumnos }} </td>
            </tr>
            <tr>
              <td>PROMOVIDOS</td>
              <td class="text-bold text-center"> - </td>
              <td class="text-bold text-center"> - </td>
              <td class="text-bold text-center"> - </td>
            </tr>
            <tr>
              <td>NO PROMOVIDOS</td>
              <td class="text-bold text-center"> - </td>
              <td class="text-bold text-center"> - </td>
              <td class="text-bold text-center"> - </td>
            </tr>
            <tr>
              <td colspan="3">CLASES IMPARTIDAS SEMESTRE</td>
              <td class="text-bold text-center"> {{$clases_impartidas}} </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr class="text-center">
        <td colspan="3">
          <p style="margin-top: 40px;">
            LUGAR Y FECHA:
              <u><b>PUEBLA, PUE.</b></u>	A
              <u><b> {{ Date('d') }}  </b></u>	DE
              <u><b>{{ $meses[ Date('m')-1 ] }}</b></u>	DE
              <u><b> {{ Date('Y')  }} </b></u>
          </p>
        </td>
      </tr>
    </table>
  </div>

<style>
  *{
    margin: 0px;
    padding: 0px;
    box-sizing: content-box;
    font-family: 'sans-serif';
    font-size: 10px;
  }
  .full-width{ width: 100%; }
  .text-20{ font-size: 20px; }
  .text-14{ font-size: 14px; }
  .text-8{ font-size: 8px; }
  .text-7{ font-size: 7px; }
  .text-6{ font-size: 6px; }
  .text-center{ text-align: center; }

  .center-table{
    width: 90%;
    margin-left: 5%;
    margin-right: 5%;
  }

  .pie-pagina{

    @if($totalAlumnos <= 40)
      position: absolute;;
      bottom: 0;
    @endif

    width: 90%;
    margin-left: 5%;
    height: 230px;
  }

  .border-table{ border-collapse: collapse;}
  .text-bold{ font-weight: bold; }
  .bg-red{ background-color: #F4C7C3; }
  .bg-yellow{ background-color: #FBFFDB; }
  .bg-gray{ background-color: #D9D9D9; }
</style>

</body>
</html>