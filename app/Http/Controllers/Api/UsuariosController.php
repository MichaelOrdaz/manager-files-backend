<?php

namespace App\Http\Controllers\Api;

use DB;
use Carbon\Carbon;
use App\Http\Traits\ListTrait;
use App\Models\User as Usuario;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\MailerController;
use App\Http\Requests\UsuariosFormRequest;
use App\Models\AspiranteStatus;
use App\Models\DatosAcademicos;
use Faker\Factory;

class UsuariosController extends Controller
{

    use ListTrait;
    /**
     * Display a listing of the assets.
     *
     * @return Usuario
     */
    public function list()
    {
        $this->authorize('viewAny', Usuario::class);
        $usuarios = Usuario::with('datosGenerales')->paginate(25);

        $data = $usuarios->transform(function ($usuario) {
            return $this->transform($usuario);
        });

        return $this->successResponse(
            'Usuarios se recuperaron correctamente.',
            $data,
            $this->pagination($usuarios)
        );
    }


    /**
     * Store a new usuario in the storage.
     *
     * @param UsuariosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UsuariosFormRequest $request)
    {
        DB::beginTransaction();
        try {
          $data = $request->getData();

          if (in_array('Padre de familia', $data['role'])) {
            $tutorado = Usuario::findOrFail($data['tutorado_id']);
            if(!$tutorado->hasRole('Alumno')) {
              return $this->errorResponse(
                'El tutorado no es un alumno',
                422
              );
            }
          }

          $faker = Factory::create();
          $contrasenia = $faker->password();

          $data['password'] = Hash::make($contrasenia);
          $usuario = Usuario::create($data);
          $usuario = Usuario::findOrFail($usuario->id);
          $usuario->contrasenia = $contrasenia;
          $usuario->syncRoles($data['role']);

          $statusAspirante = AspiranteStatus::where('nombre', 'No aplica')->first();
          $datos = [
            'status_id' => $statusAspirante->id,
          ];
          // Code::Fixme > Issue #451
          if (in_array('Alumno', $data['role'])) {
            $datos['generacion'] = $data['generacion'];
            $datos['anio_ingreso'] = $data['anio_ingreso'];
          }
          $usuario->datosAcademicos()->create($datos);

          if (in_array('Padre de familia', $data['role'])) {
            $tutorado->update([
              'tutor_id' => $usuario->id
            ]);
          }

          $mailer = new  MailerController();
          $subject = "Confirma tu correo";
          $token = $mailer->sendMail($usuario->email,$subject,MailerController::CONFIRM_MAIL_USUARIO,$usuario->toArray());

          DB::table('emails_confirm')->insert([
            'email' => $usuario->email,
            'token' => $token,
            'created_at' => Carbon::now(),
          ]);

          DB::commit();

          return $this->successCreate(
              'Usuario fue agregado exitosamente.',
              $this->transform($usuario)
          );

        } catch (\Exception $e) {
          DB::rollBack();
          throw $e;
        }
    }

    /**
     * Display the specified usuario.
     *
     * @param int $id
     *
     * @return
     */
    public function get($id)
    {
        $this->authorize('view', Usuario::class);
        $usuario = Usuario::findOrFail($id);

        return $this->successResponse(
          'Usuario fue recuperado con éxito.',
          $this->transform($usuario)
        );
    }

    /**
     * Update the specified proyecto in the storage.
     *
     * @param $id
     * @param UsuariosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UsuariosFormRequest $request, $usuario_id)
    {
        DB::beginTransaction();
        try {
          $this->authorize('update', Usuario::class);
          $data = $request->getData();

          $usuario = Usuario::findOrFail($usuario_id);
          $usuario->update($data);

          $usuario = Usuario::findOrFail($usuario_id);

          if( (bool) array_diff($usuario->getRoleNames()->toArray(),$data['role']) ) {
            $usuario->syncRoles($data['role']);
          }

          if (in_array('Alumno', $data['role'])) {
            // Code::Fixme > Issue #451
            DatosAcademicos::updateOrCreate([
              'usuario_id' => $usuario->id,
            ],[
              'usuario_id' => $usuario->id,
              'generacion' => $data['generacion'],
              'anio_ingreso' => $data['anio_ingreso'],
            ]);
          }

          DB::commit();
          return $this->successResponse(
              'Usuario se actualizó con éxito.',
              $this->transform($usuario)
          );
        } catch (\Exception $e) {
          DB::rollBack();
          throw $e;
        }
    }

    /**
     * Remove the specified proyecto from the storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $this->authorize('delete', Usuario::class);

        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return $this->successResponse(
            'Usuario fue eliminado con éxito.',
            $this->transform($usuario)
        );

    }

    /**
     * Transform the giving usuario to public friendly array
     *
     * @param App\Models\Users $usuario
     *
     * @return array
     */
    protected function transform(Usuario $usuario)
    {
        $data = [
          'id' => $usuario->id,
          'email' => $usuario->email,
          'email_verified_at' => $usuario->email_verified_at,
          'firebase_uid' => $usuario->firebase_uid,
          'roles' => $usuario->getRoleNames(),
          'datos_generales' => $usuario->datosGenerales,
          'datos_familiares' => $usuario->datosFamiliares,
          'datos_academicos' => $usuario->datosAcademicos,
        ];
        
        if ($usuario->hasRole('Alumno')) {
          $data['tutor'] = $usuario->tutor;
        }
        if ($usuario->hasRole('Padre de familia')) {
          $data['tutorados'] = $usuario->tutorados;
        }
        return $data;
    }


}
