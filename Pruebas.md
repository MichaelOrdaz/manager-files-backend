# PRUEBAS AUTOMATIZADAS

## Pasos a seguir (Instrucciones)
1. Crea tu prueba para tu modelo con el siguiente comando:
```php
  php artisan puller-test:create --model={ nombre_del_modelo }
```
Para la creación de test unitarios o de características **específicos** se utilizan los comandos básicos de laravel
```php
  php artisan make:test {Nombre_de_la_clase}Test
  php artisan make:test {Nombre_de_la_clase}Test --unit
```

2. Se crea un directorio con el nombre del modelo en el path `tests/Feature`, dentro del directorio se habrán creado cuatro templates para la elaboración de las pruebas de integración.
  - **{Modelo}CrudTest.php:**
    - Se definen pruebas de crud para el modelo
    - Se verifican todos los alcances relacionadas del *modelo* con su *recurso* :
      - Eliminación en cascada
      - Si la creación o actualización del modelo afecta a otra tabla se tiene que verificar que fue el otro modelo fue afectado
      - Verificación de flujos de integración
<br>
  - **{Modelo}RelacionesTest.php:**
    - Se verifican las relaciones hasOne, hasMany, belongsTo, ... etc. del modelo
    - Se crean datos temporales para la verificación de las relaciones
<br>
  - **{Modelo}RolesEndpoint.php:**
    - El fichero contiene un usuario con el que se verifica el acceso a los endpoints a partir del rol que se le asigne al usuario
    - Sirve para verificar que los permisos de los roles vayan concorde con los accesos que tiene ese rol en cuestión
    - Se verifica acceso al recurso para todos los roles
<br>
  - **{Modelo}RolesPermisosTest.php:**
    - Se verifica los permisos que tienen los roles con relación al modelo

___

## ¿Que es TDD?
El test-driven development (TDD) es una metodología de diseño de software que se basa en test o pruebas para guiar el proceso. Al contrario de lo que ocurre en metodologías que posponen los test a un punto ulterior, los casos de prueba en TDD se realizan al inicio del proceso de diseño y después se implementa el código.

**La metodología TDD sigue el siguiente orden:**
Primero se crean las pruebas pensando en el resultado que nos va a dar el código que realicemos para después proceder a escribirlo, de tal manera que seguimos el siguiente camino:

> <span style="color:white"><span style="background-color:red">Rojo</span> :eyes: <span style="background-color:green">Verde</span> :white_check_mark: <span style="background-color:blue">Refactor</span>  </span> :+1:

**Significado :**
- **Rojo :** Error, la falta implementación o que la prueba fallo
- **Verde :** Paso exitoso de nuestro código a través de las pruebas, esta fase quiere decir que hace lo esperado pero puede ser posible mejorar la calidad del código escrito
- **Refactor :** Es la parte donde tenemos código funcional que es aprobado por nuestras pruebas y  donde se reduce la complejidad del algoritmo como el hacerlo mas legible

___
## ¿ Que es una prueba unitaria ?
Las **pruebas unitarias** consisten en aislar una parte del código y comprobar que funciona a la perfección. Son pequeños tests que validan el comportamiento de un objeto y la lógica.

#### Crear una prueba unitaria:
> php artisan make:test {Nombre_de_la_clase}Test --unit

#### Características
- Funciones chicas o aisladas de otros contextos
- Las pruebas dentro de su directorio de prueba "Unit" no inician la aplicación Laravel y, por lo tanto, **no pueden acceder a la base de datos** u otros servicios.

____
## ¿ Que es una prueba de integración ?
Una prueba de integración permite corroborar los cambios realizados en el sistema para agregar una nueva funcionalidad o modificar la funcionalidad existente. Se dice que cada característica tiene características que están diseñadas para ser útiles, intuitivas y efectivas.

#### Crear una prueba
> php artisan make:test {Nombre_de_la_clase}Test

#### Características
- Códigos largos donde interactúan varios recursos, varios modelos, relaciones, consultas http ...
- Pueden interactuar con la base de datos y otros serviciós
- En laravel tienen su directorio **tests/Feature**

___

## ¿ Que es una prueba End to End ?
Las pruebas end to end verifican el flujo real completo de la aplicación de inicio a fin.
Este tipo de pruebas por lo general son las mas lentas en su proceso de ejecución ya que pasan por todo el sistema, aun que estas pruebas verifique todo el flujo del sistema siguen manteniendo el mismo principio que las pruebas de integración

#### Crear una prueba
> php artisan make:test {Nombre_de_la_prueba}Test

#### Características
- Simulan un usuario real
- Validan la integridad de los datos
- Verifican integraciones con sistemas externos
- En laravel tienen su directorio **tests/Feature**
___

## Ejecutar las pruebas para el proyecto

Podemos ejecutar las pruebas con el siguiente comando :

> php artisan test

Esto ejecuta  todas la pruebas ya sean unitarias o de características, las ejecuta una por una de forma alfabética. También se puede ejecutar un test especifico

> ./vendor/bin/phpunit --filter {nombre_clase}Test

#### phpunit.xml
En este archivo se encuentra toda la configuración para phpunit, normalmente laravel ya tiene el archivo preconfigurado por lo tanto no es necesario modificarlo.
Dentro de este archivo podemos seleccionar los colores para los mensajes `Error`, `Warm`, `Success` así como otras configuraciones

___

## ¿Como realizar una prueba?
Para realizar una prueba unitaria o de característica podemos utilizar los comandos de laravel
```php
  // Pruebas de características
  php artisan make:test {Nombre_de_la_clase}Test
  // Pruebas unitarias
  php artisan make:test {Nombre_de_la_clase}Test --unit
```
Crea un fichero en el path **tests/Feature** para una prueba de característica o **tests/Unit** para una prueba unitaria, independientemente del tipo de prueba que hayamos pedido realizar a nuestra linea de comandos nos creara un archivo con la siguiente estructura

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class {Modelo}Test extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }
}
```

#### Nombre de las funciones:
El nombre de las funciones es muy importante, las funciones se escriben separando las palabras con guion bajo (underscore), para **indicar** que la función es un punto de entrada para la **ejecución de la prueba** lleva el prefijo `test_`.

```php
  public function test_example()
    {
        $this->assertTrue(true);
    }
```

De igual manera se puede especificar que una función es un punto de entrada para la prueba con el comentario `/** @test */` esto permite ya no anteponer el prefijo `test_`

```php
  /** @test */
  public function example()
    {
        $this->assertTrue(true);
    }
```
### Afirmaciones
Las afirmaciones son la parte mas **esencial** de los test, su función reside en **confirmar la preposición que estable cada afirmación**, de tal manera que si esperamos que un bloque de nuestro código nos devuelva cierto valor de respuesta la afirmación nos permite compararlo y verificar que sea así, en caso de no ser el valor esperado esto indica que el test no a culminado de forma exitosa

Ejemplo: Si queremos **confirmar** que un **valor booleano** sea **verdadero** utilizamos la siguiente afirmación:

```php
 $this->assertTrue(true);
```
En caso de serlo el test donde se ejecute esta afirmación concluye de forma exitosa, de caso contrario nos mostrara en la consola un mensaje de error y la posible razón del por que no se haya pasado la prueba

**Algunas afirmaciones que podemos utilizar** cabe señalar que hay mas

```php
  $this->assertInstanceOf(Collection::class,$category->posts); // La instancia pertenece a ese tipo de dato
  $this->assertEquals($slug->render(),"curso-de-laravel"); // Equivale a una comparación ==
  $this->assertSame(10,"10"); // Equivale a una comparación ===
  $this->assertTrue(true); // Esperamos un true
  $this->assertFalse(false); // Esperamos un false
  $this->assertIsArray([]); // Esperamos que sea de tipo Array
  $this->assertIsBool(true); // Solo que sea un booleano
  $this->assertIsString("Texto"); // Solo que sea un tipo String
  $this->assertEmpty([]); // Que sea un dato vació
  $this->assertCount(2,["uno","dos"]); // Que sea exactamente ese numero de elementos
  $this->assertArrayHasKey("color",["color" => "azul"]); // Que contenga la llave
  // Afirmación de consulta a endpoint
  $response = $this->get('/');
  $response->assertStatus(200); // Confirmamos que la respuesta nos devuelva un 200
```

>**Links de referencia:**
[Afirmaciones laravel](https://laravel.com/docs/8.x/http-tests#available-assertions)

**Ejemplo de una prueba unitaria**
Esta no necesito utilizar algún registro de la base de datos solo prueba que la función de su instancia de la clase sea correcta

```php
use Path\Slug;
public function test_render(){

  $slug = new Slug("Curso de laravel");
  $expected = "curso-de-laravel";

  $this->assertEquals($slug->render(),$expected);
}
```


**Ejemplo de una prueba de característica (método post):**
Esta prueba interactuá con la base de datos y también puede hacerlo con otros servicios si fuese necesario
```php
  /** @test */
  public function a_post_can_be_created()
  {

    /*
    Esto va ejecutar (migraciones,modelos,fakers...) todo lo
    pertinente a la base de datos para poder ejecutar la prueba
    */
    use RefreshDatabase;

    //Desactiva el try catch del test para ver el error de ejecución
    $this->withoutExceptionHandling();

    $response = $this->post('post',[
      'title' => 'Test title',
      'content' => 'Test Content',
    ]);

    $this->assertOk(); // Comprábamos que el post fue correcto

    // Comprobamos que el dato este almacenado
    $this->assertCount(1,Post::all());
  }

```

**Primer función a ejecutar (setUp):**
La primer función que se ejecuta en una prueba es **setUp**, dentro de ella podemos definir datos que necesitan ser iniciados antes de que la prueba comience para usarlos en el proceso de ejecución de la misma

**Ejemplo con función setUp**
```php
class UserTest extends TestCase
{
  protected $user;
  // primer función a ejecutar
  public function setUp(){
    $this->user = new User;
  }

  /** @test */
  public function test_i_can_the_name(){
    $this->user->setName("Nombre");
    $this->assertEquals($this->user->getName(),"Nombre");
  }
}
```

**Ultima funcion a ejecutar (tearDown):**
La ultima función a ejecutar es **tearDown**, normalmente no se ocupa, pero permite ejecución de su bloque de código una vez concluida la prueba
```php
class UserTest extends TestCase
{
  protected $user;

  public function setUp(){
    $this->user = new User;
  }
  // ultima función a ejecutar
  public function tearDown(){
    User::find($this->user->id)->delete();
  }

  /** @test */
  public function test_i_can_the_name(){
    $this->user->setName("Nombre");
    $this->assertEquals($this->user->getName(),"Nombre");
  }
}
```

> Para mas información consulte :
https://laravel.com/docs/8.x/testing
https://laravel.com/docs/8.x/http-tests
___
## ¿Como realizar una prueba en laravel sin registrar los datos de prueba en la base de datos?
Podemos utilizar en **DatabaseTransactions** esto realizara un rollback sobre las tablas que hayamos afectado sobre el proceso, las desventajas es que dejara de funcionar si hemos definido un comportamiento distinto para la función **tearDown** y no restaura los id auto incrementables

```php
...
use Illuminate\Foundation\Testing\DatabaseTransactions;
class GrupoTest extends TestCase
{
    use DatabaseTransactions;
  ...

```
**Restaurar los indices :**
Para restaurar los indices de las tablas afectadas podemos utilizar las funciones **setUp** y **tearDown**
```php
public function setUp()
{
    parent::setUp();
    DB::beginTransaction();
}

public function tearDown()
{
    parent::tearDown();
    DB::rollBack();
    $max = {Modelo}::max('id') + 1;
    DB::statement("ALTER TABLE {tabla_del_modelo} AUTO_INCREMENT =  $max");
}
```

> De igual forma podemos escribir una prueba que se ejecute al final para poder restaurar los indices

**Links de referencia:**
[ Realizar test sin registrar los datos de prueba ](https://es.stackoverflow.com/questions/381083/como-realizar-una-prueba-unitaria-en-laravel-sin-registrar-los-datos-de-prueba-e)



-- Creando y usando factories
documentacion oficial https://laravel.com/docs/8.x/database-testing#belongs-to-relationships

las factories son una forma rapida de crear registros en base de datos
Para usar las factory, cada modelo debe usar el trait de laravel 
Illuminate\Database\Eloquent\Factories\HasFactory

despues de con la consola de laravel creamos las factories que son clases diferentes que se colocan en la carpeta de database/factories

php artisan make:factory PostFactory

y podemos especificar el modelo que usara esa factory para que nos cree la plantilla facilmente

php artisan make:factory PostFactory --model=Post

una vez creada la clase, esta tiene un metodo llamada definition, donde debemos declarar que valores predeterminados debemos aplicar cuando se crea un modelo
en este definicion podemos usar la propiedad faker que tiene acceso a la biblioteca de faker, para poder simular datos aleatorios
este es la documentacion donde podemos encontrar todos los datos de prueba que ofrece la libreria https://fakerphp.github.io/

Una vez definida la factory, podemos hacer uso de ella llamando al metodo factory y desencadenando otra llamada al metodo make o create,
el metodo make, crea el modelo sin guardar en base de datos, y el metodo create, lo crea insertando en base de datos

Ejemplo
User::factory()->make();
User::factory()->create();

para poder crear muchos modelos se usa el metodo count
User::factory()->count(3)->make();
User::factory()->count(3)->create();

cuando los modelos necesitan relaciones como un modelo tiene muchas relaciones, se hace uso del metodo has, que recibe la factory del modelo que se relacionara muchas veces con el modelo principal
Ejemplo:
use App\Models\Post;
use App\Models\User;

$user = User::factory()
->has(Post::factory()->count(3))
->create();

cuando el modelo pertence a una relacion se usa el metodo for, que recibe el modelo o una factory de modelo que sera el padre de los modelos que se estan creando
ejemplo, crando la factory en linea
use App\Models\Post;
use App\Models\User;

$posts = Post::factory()
->count(3)
->for(User::factory()->state([
    'name' => 'Jessica Archer',
]))
->create();

ejemplo, cuando ya tienes el modelo existente
$user = User::factory()->create();

$posts = Post::factory()
->count(3)
->for($user)
->create();

para las relaciones de muchos a muchos se usa el mismo metodo has, de la misma manera