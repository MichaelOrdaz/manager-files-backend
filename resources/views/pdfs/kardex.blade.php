<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificado</title>
</head>
<body>
  <div>
    <span style="font-size:18px !important;"> {{$especialidad_nombre}} </span> <br>
    <span style="font-size:15px !important;"> {{$matricula}} {{$nombre_completo}} </span>
    <p style="font-size:12px !important;"> {{$fecha}} </p>
    <p style="font-size=smaller">
      <b>ACADEMIA DE FORMACIÓN Y DESARROLLO POLICIAL PUEBLA - INICIATIVA MÉRIDA</b><br>
      <b>BACHILLERATO TÉCNICO EN SEGURIDAD CIUDADANA</b><br>
      C.C.T 21ECT0019R ZONA ESCOLAR 073
    </p>
  </div>

  <hr style="height:15px;width:100%;background-color:#C4C4C4;border:none;">

    <div style="margin-top:3em;"></div>

    <div>
      @foreach($materias_periodo as $periodo)
        <div style="width:49%;display:inline-block;">
          <table>
            <colgroup style="width:100%;">
              <col style="width: auto" />
              <col style="width: 10%" />
              <col style="width: 40%" />
            </colgroup>
            <tr>
              <th><b>{{$periodo['ordinal']}} SEMESTRE CICLO ESCOLAR</b></th>
              <th><b>CALIF.</b></th>
              <th><b>OBSERVACIONES</b></th>
            </tr>
            <tr>
              <td>{{ $periodo['ciclo_escolar'] }}</td>
              <td></td>
              <td></td>
            </tr>
            @foreach($periodo['materias'] as $materia)
              <tr>
                <td> {{$materia->materia_nombre}} </td>
                <td> <center> {{$materia->calificacion_semestral}} </center> </td>
                <td> {{$materia->observaciones}} </td>
              </tr>
            @endforeach
          </table>
        </div>
      @endforeach
    </div>

    <div>
      <p style="margin-y:3em;">
        El PRESENTE CERTIFICADO AMPARA <B>CUARENTA Y OCHO</B> ASIGNATURAS QUE INTEGRAN EL PLAN DE ESTUDIOS DE BACHILLERATO <br>
        TECNOLOGICO CON CAPACITACIÓN PARA EL TRABAJO EN TÉCNICO PROFESIONAL EN SEGURIDAD CIUDADANA EQUIVALENTE AL<br>
        BACHILLERATO, CON UN PROMEDIO GENERAL DE APROVECHAMIENTO DE <b> {{$promedio_escolar}} {{$promedio_escolar_letra}}</b><br>
        .......................................
      </p>
    </div>

  <br><br><br>


  <div style="width:100%">
    <table style="width:100%">
      <tr>
        <td class="1" style="width:33%;">
          <center>EQUIVALENCIA DE CALIFICACIONES</center>
        </td>
        <td class="2 text-center" style="width:33%;">
          REVISADO Y CONFRONTADO POR:
        </td>
        <td class="3 text-center" style="width:33%;">DIRECTORA DE CONTROL ESCOLAR</td>
      </tr>
      <tr>
        <td class="1">
          <p>
            ACUERDO NÚMERO 17 DEL C. SECRETARIO DE<br>
            EDUCACIÓN PÚBLICA. DIARIO OFICIAL DE LA<br>
            FEDERACIÓN DEL 28 DE AGOSTO DE 1978.
          </p>
        </td>
        <td class="2"></td>
        <td class="3"></td>
      </tr>
      <tr>
        <td class="1">
          <table style="width:100%;">
            <tr>
              <td>INTERPRETACIÓN</td>
              <td class="text-center">SIMBOLO</td>
            </tr>
            <tr>
              <td>EXCELENTE</td>
              <td class="text-center">10</td>
            </tr>
            <tr>
              <td>MUY BIEN</td>
              <td class="text-center">9</td>
            </tr>
            <tr>
              <td>BIEN</td>
              <td class="text-center">8</td>
            </tr>
            <tr>
              <td>REGULAR</td>
              <td class="text-center">7</td>
            </tr>
            <tr>
              <td>SUFICIENTE</td>
              <td class="text-center">6</td>
            </tr>
          </table>
        </td>
        <td class="2 text-center">XOCHILT JUAREZ OVANDO</td>
        <td class="3 text-center">ANA GABRIELA HERRERA <br> VILLAGÓMEZ</td>
      </tr>
      <tr>
        <td>LA CALIFICACIÓN MINIMA APROBATORIA ES 6</td>
        <td class="text-center">FECHA: 27 DE JULIO DE 2020</td>
        <td class="text-center">SELLO</td>
      </tr>
    </table>
  </div>

  <br>
  <p>ESTE CERTIFICADO NO ES VÁLIDO SI PRESENTA BORRADURAS O ENMENDADURAS</p>

</body>
<style>
  *{
    font-family: 'sans-serif';
    font-size: 9px;
  }
  .text-center{
    text-align: center
  }
</style>
</html>