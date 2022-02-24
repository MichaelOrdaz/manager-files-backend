### Configuración de Swagger

1. Agregar la configuración inicial de swagger en el archivo public/swagger/openapi.yaml

2. Definirnir el parametro  **servers:** sirve para declarar nuestros entornos donde está disponible nuestra API.

3. Con el parámetro **paths:** declaramos las rutas que tiene nuestra API.

---

## Documentar un Endpoint

1. Definir el enpoint en el archivo [openapi.yaml](./docs/openapi.yaml) Recuerda usar [Teoria de APIs y formato](./Generacion-api.md) para construir el url del endpoint y usar la referencia a su definicion.
<br></br>
Recuerda que dependiendo de los enpoints pueden existir varios archivos que hacen referencia al mismo recurso pues, no se pueden duplicar las keys (mestos HTTP)

``` yaml
    Ejemplo:
        /estados/{estado_id}/municipios:
          $ref: "./paths/CatalogosMunicipios.yaml"
        /estados/{estado_id}/municipios/{municipio_id}:
          $ref: "./paths/CatalogosMunicipios-by-id.yaml"
```

2. Escribiendo estructura del Endpoint
    - Se pueden agrupar usando tags con la key: tags
    - El titulo (Etiqueta summary) debe componerse del Metodo estandar o personalizado seguido de "-" y el nombre del Recurso :
        - summary: "Get - Municipio"
        - summary: "Delete - Municipio"
    - Recuerda escribir una descripcion **DESCRIPTIVA**
    - Si el endpoint requiere parametros en la URL, definirlos mediante un $ref y escribir su definicion dentro del archivo parameters/_index.yaml
    - Definir la respuesta que retorna en caso satisfactorio y en caso de error.
        - Recuerda que para cualquier endpoint la respuesta se define como un esquema

Para crear una crud dentro de nuestro parámetro **path** se puede hacer referencia a un archivo utilizando la variable **$ref** seguido del path del archivo que contiene los tipos de petición **http**.

- El parámetro **schemas** que se encuentra dentro del parámetro **components**, sirve para declarar los schemas u objetos que maneja nuestra API.

- En parámetro **parameters** podemos declarar los parámetros que se utilizarán en nuestros archivos .yaml.

- En el parámetro **securitySchemes** declaramos el tipo de autenticación que se utilizará en nuestra API, en este caso: BearerAuth.

- En el parámetro responses podemos declarar los tipos de respuesta que tiene nuestra API, aquí van los códigos de respuesta con la estrucutra que debe llevar.

### Documentación de Swagger y OpenAPI

<https://swagger.io/docs/specification/about/>
