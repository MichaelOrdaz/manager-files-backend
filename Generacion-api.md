## API ORIENTADA A RECURSOS ##

### COMANDO PARA GENERAR UN NUEVO RECURSO
> php artisan create:api-scaffold {nombre_recurso} --table-exists --table-name={nombre_tabla_de_BD } --with-form-request --with-soft-delete
____
### INTRODUCCIÓN

El objetivo de esta Guía de diseño es ayudar a los desarrolladores a diseñar API simples, coherentes y fáciles de usar. Además, ayuda a la convergencia de diseños de API de RPC (Remote Procedure Call: comunicación en sistemas cliente-servidor) basadas en socket con API de REST basadas en HTTP.

> Esta guía fue escrita siguiendo las recomendaciones de guiá de diseño de [api de google cloud](https://cloud.google.com/apis/design)

---

### DISEÑO DE API ORIENTADA A RECURSOS

En la Guía de diseño, **se sugiere seguir los pasos** a continuación durante el diseño de API orientadas a recursos:

- Determinar los tipos de recursos que proporciona una API
- Determinar la relación entre recursos
- Decidir los esquemas de nombres de recurso según los tipos y las relaciones
- Decidir los esquemas de recursos
- Adjuntar un conjunto de métodos mínimo a los recursos

---

### CONCEPTOS

***¿Que es un recurso?***
Se puede decir un recurso es una composición de una o mas tablas que guardan relación entre si, ya que **un recurso puede ser constituido por varias tablas que lo componen**

- Un ejemplo de esto puede ser que tenemos **un recurso nombrado usuarios** mas sin embargo a este recurso lo componen **dos tablas (usuarios y datos_generales)** que tienen relación con este.

***Tipos de recursos***
Por lo general, una API orientada a recursos **se modela como una jerarquía** en la que cada nodo es un **recurso simple** o un **recurso de colección.**

- Una **colección** contiene una lista de recursos del mismo tipo, por ejemplo.
  - En gmail un usuario tiene una colección de contactos.
  - Cada usuario tiene una colección de mensajes, una de conversaciones, una de etiquetas, etc.

- Un **recurso** contiene sus propios atributos y cero o más subrecursos que guarden relación con este mismo. Cada subrecurso puede ser siemple o de colección.

---

### NOMBRES DE LOS RECURSOS

Los recursos son entidades con nombre y los nombres de recursos son sus identificadores. **Cada recurso debe tener su propio nombre único**. El nombre del recurso está formado por el identificador del recurso, los identificadores de los recursos superiores y el nombre del servicio de API.

**El nombre del recurso se organiza** de forma jerárquica mediante el identificador de colección y de recursos, separados por barras diagonales. **Si un recurso contiene un subrecurso**, el nombre del subrecurso se forma mediante la especificación del nombre del recurso superior seguido por el identificador del subrecurso, de nuevo, **separados por barras diagonales.**

- Ejemplo 1: un servicio de almacenamiento tiene una colección de buckets, en la que cada bucket tiene una colección de objects:
`Endpoint: //storage.googleapis.com/buckets/bucket-id/objects/object-id`
  |Nombre del servicio de API | ID de la colección | ID de recurso | ID de la colección |ID de recurso|
  | ----------- | ----------- | ----------- | ----------- | ----------- |
  |`//storage.googleapis.com` | /buckets | /bucket-id |/objects |/object-id |

- Ejemplo 2: Un servicio de correo electrónico tiene una colección de users. Cada usuario tiene un subrecurso settings, y el subrecurso settings tiene otros subrecursos, incluido customFrom:
`Endpoint: //mail.googleapis.com/users/name@example.com/settings/customFrom`
  |Nombre del servicio de API|ID de la colección|ID de recurso|ID de recurso|ID de recurso|
  | ----------- | ----------- | ----------- | ----------- | ----------- |
  |`//mail.googleapis.com`|/users|/name@example.com|/settings|/customFrom|

---

### MÉTODOS ESTÁNDAR

La característica clave de una buena API orientada a recursos es enfatizar los recursos por sobre la funcionalidad. Una API orientada a recursos típica expone una gran cantidad de recursos con una pequeña cantidad de métodos. **Pueden ser métodos estándar o personalizados.**
Los métodos estándar reducen la complejidad y aumentan la coherencia, **antes de pensar en un método personalizado es preferible tratar de optar por un método estándar.**

> No confundir un método estándar con un http, los métodos estándar es el como se consumirá el api, y los métodos http son los que ocupan el protocolo http, como se ve en la siguiente tabla

| Método estándar | Asignación HTTP |  Cuerpo de la solicitud HTTP | Cuerpo de la respuesta HTTP
| ----------- | ----------- | ----------- | ----------- |
|List | `GET <collection URL>` | N/A| Lista de recursos |
|Get | `GET <resource URL>` | N/A | Recurso* |
|Create | `POST <collection URL>` | Recurso | Recurso* |
|Update | `PUT or PATCH <resource URL>` | Recurso | Recurso* |
|Delete | `DELETE <resource URL>` | N/A | Mensage de exito* |

> **Para los métodos que el cuerpo de su solicitud son N/A los parámetros de la url deben ser capaces de generar la respuesta de manera satisfactoria**

**EJEMPLO API DE GMAIL**
El servicio de la API de Gmail implementa la API de Gmail y expone la mayor parte de la funcionalidad de Gmail. Tiene el siguiente **modelo de recursos:**

Servicio de API: Cada usuario tiene los recursos que se detallan a continuación.

- Una colección de usuarios: `users/*.`
  - Una colección de mensajes: `users/*/messages/*.`
  - Una colección de conversaciones: `users/*/threads/*.`
  - Una colección de etiquetas: `users/*/labels/*.`
  - Una colección del historial de cambios: `users/*/history/*.`
  - Un recurso que representa el perfil del usuario: `users/*/profile`
  - Un recurso que representa las opciones de configuración del usuario: `users/*/settings`
  - Un usuario tiene publicaciones, las publicaciones tienen comentarios y los comentarios respuestas : `users/*/posts/*/comments/*/answers/`

**COMPACTANDO RUTAS**
Siempre y cuando se utilicen los métodos estándar se puede compactar las rutas para este fin
```php
Route::post('/post','PostController@store');
Route::get('/post','PostController@index');
Route::get('/post/{post}','PostController@show');
Route::put('/post/{post}','PostController@update');
Route::delete('/post/{post}','PostController@destroy');
// a esto
Route::resource('/post','PostController');
```

---

### MÉTODOS PERSONALIZADOS

**A medida de lo posible se recomienda presidir de los métodos personalizados**
Los métodos personalizados son los métodos de API distintos de los 5 métodos estándar.

> **Solo deben usarse para la funcionalidad que no se puede expresar con facilidad a través de los métodos estándar.**

Una ventaja de los **métodos estándar** es que la plataforma de API tiene una mejor comprensión y compatibilidad con los métodos estándar, como **la facturación, el manejo de errores, el registro y la supervisión**, por lo tanto para estos casos **los métodos personalizados quedan exentos de este uso**

Un método personalizado se puede asociar con un recurso, una colección o un servicio. Los nombres de métodos personalizados deben seguir las convenciones de nomenclatura de métodos.

**ASIGNACIÓN HTTP**
Para los métodos personalizados, se debe usar la siguiente asignación HTTP genérica:
> `https://service.name/v1/some/resource/name:customVerb`

**El motivo de usar** `:` en lugar de `/` para separar el verbo personalizado del nombre del recurso es admitir rutas de acceso arbitrarias, esto se hace de tal manera que se pueda **entender o diferenciar** que el verbo (Método) se refiere a un método personalizado y no a un parametro que esta en la construcción de la url

- Por ejemplo, recuperar un archivo puede asignarse a `POST /files/a/long/file/name:undelete`

Las siguientes pautas se aplicarán cuando se elija la asignación HTTP:

- **Los métodos personalizados deben usar el verbo HTTP POST**, ya que tiene la semántica más flexible; excepto los métodos que se entregan como alternativas a `get o list`, que **pueden** usar `GET` cuando sea posible.
-
- En particular, los métodos personalizados que usan `GET` HTTP `deben` tener **responsabilidad única** sin tener efectos colaterales. Por ejemplo, los métodos personalizados que implementan **respuestas de un recurso con formato diferente deben usar get** de lo contrario usar un método estándar.
- Los métodos personalizados **no deben** usar `PATCH` HTTP
- Se debe tratar que todos los parámetros que use un método personalizado, sean nombrados en la url
-Si el verbo HTTP que se usa para el método personalizado no admite un cuerpo de solicitud para HTTP `(GET, DELETE)`, la configuración HTTP de ese método **no debe** usar la cláusula `body` en absoluto y todos los campos del mensaje de solicitud restantes **deben** asignarse a los parámetros de consulta URL.
- Los métodos personalizados deben de ser únicos

**Métodos personalizados comunes**
|Nombre del método | Verbo personalizado | Verbo HTTP | Nota |
| ----------- | ----------- | ----------- | ----------- |
| Cancel |:cancel | POST | Cancela una operación pendiente, como `operations.cancel.`|
| BatchGet |:batchGet | GET | Batch get por lotes para múltiples recursos|
|Move|:move | POST | Mueve un recurso de un elemento superior a otro, como `folders.move.`|
|Search|:search|GET|Alternativa a List para recuperar datos que no cumplan con la semántica de List, como `services.search.` (filtros) |
|Undelete|:undelete|POST|Restablece un recurso que se borró anteriormente, como `services.undelete.` El período de retención recomendado es de 30 días.|
