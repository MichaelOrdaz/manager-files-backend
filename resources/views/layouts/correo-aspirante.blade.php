<!DOCTYPE html>
<html lang="es-mx">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bachillerato</title>
</head>
<body>
    <img src="{{ env('APP_URL_FRONTEND').'/images/logo.png' }}"
      alt="logo academia ignacio de zaragoza puebla"
      class="center"
      style="width: 25%;margin-bottom:15px;margin-top:20px;"
    />

    <div class="parrafo">
      <p><strong>ACADEMIA DE FORMACIÓN Y DESARROLLO POLICIAL PUEBLA-INICIATIVA MÉRIDA</strong></p>
      <p><strong>“GENERAL IGNACIO ZARAGOZA”</strong></p>
      <p>BACHILLERATO TÉCNICO EN SEGURIDAD CIUDADANA</p>
      <p><strong>C.C.T.21ECT0019R</strong></p>
    </div>

    <div class="parrafo" style="margin-top:1em;">
      <p style="font-size:1.2em;">
        <strong>
          @section('asunto')
          @show
        </strong>
      </p>
    </div>

    <div class="parrafo" style="margin-top:3em;">
      @section('mensaje')
      @show
    </div>

    <div class="parrafo" style="margin-top:3em;">
      <p><strong>Dudas y aclaraciones al tel. (222) 144 1000 ext.30113</strong></p>
    </div>


  <style>
    html
    {
      box-sizing: border-box;
      overflow: -moz-scrollbars-vertical;
      overflow-y: scroll;
    }

    body
    {
      margin:0;
      background: #fafafa;
    }

    .center {
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    p, strong
    {
      text-align: center !important;
      word-break: break-word !important;
      color : black !important;
    }

    *
    {
      line-height: normal !important;
    }

    .parrafo
    {
      padding-right:2em;
      padding-left:2em;
    }

  </style>



</body>
</html>