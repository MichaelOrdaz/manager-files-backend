**Siga las siguientes reglas al escribir un controlador**

1. Siempre retorne una respuesta en formato JSON.

2. Use siempre el siguiente formato de comentarios al escribir controladores:

```PHP
    /**
     * @author Nombre autor <correo@autor.com>
     * @updated  Nombre de quien actualiza o modifica <correo@modifica.com>
     * @version  1.0
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse $data
     * Descripcion del metodo
     */
    public function homework(Request $request){
        return response()->json($data, 200);
    }
```
