<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class MailerController extends Controller {

    public const CONFIRM_MAIL_ASPIRANT = 'CONFIRM_MAIL_ASPIRANT';
    public const RESET_PASSWORD = 'RESET_PASSWORD';
    public const FAIL_EXAM_ASPIRANT = 'FAIL_EXAM_ASPIRANT';
    public const CONFIRM_MAIL_USUARIO = 'CONFIRM_MAIL_USUARIO';
    public const ACEPT_MAIL_USUARIO = 'ACEPT_MAIL_USUARIO';
    public const PROGRESS_MAIL_USUARIO = 'PROGRESS_MAIL_USUARIO';

    public function sendPasswordResetEmail(Request $request){
        // If email does not exist
        if(!$this->validEmail($request->email)) {
            return response()->json([
                'message' => 'emailNotFound'
            ], Response::HTTP_NOT_FOUND);
        } else {
            // If email exists
            $subject = 'Restablecer tu contraseña';
            $typeMail = RESET_PASSWORD;
            $this->sendMail($request->email,$subject,$typeMail);
            return response()->json([
                'message' => 'Se ha enviado un correo a {$request->email} con un enlace para restablecer tu contraseña.'
            ], Response::HTTP_OK);
        }
    }

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse $data
     * Se valida el correo, si es que existe se busca en la tabla emails_confirm
     * y se valida eñ token relacionado al correo
     */
    public function cofirmEmail(Request $request){
      if(!$this->validEmail($request->email)) {
        return response()->json([
          'message' => 'emailNotFound'
        ], Response::HTTP_NOT_FOUND);
      }

      $existToken = DB::table('emails_confirm')->where('token',$request->token);
      if($existToken->count()>0){
        User::Where('email',$request->email)->update(['email_verified_at' => Carbon::now() ]);
        return response()->json([
          'message' => 'Se ha verificado el correo electrónico con éxito',
        ],Response::HTTP_OK);
      }else{
        return response()->json([
          'message' => 'El token no es valido',
        ],401);
      }

    }


    /**
     * @updated  Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * @param  string  $email
     * @param  string  $subject
     * @param  const string  $typeMail
     * @param  optional array $data
     * @return string $token
     * Enviá un correo electrónico con los datos configurables
     * subject -> asunto, lo primero que leerá el usuario
     * typeMail -> Constante de la plantilla que se enviá al usuario
     */
    public function sendMail($email,$subject,$typeMail,$data = []){
        $token = $this->generateToken($email);
        Mail::to($email)->send(new SendMail($token,$subject,$typeMail,$data));
        return $token;
    }

    /**
     * @updated  Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     * Valida si el correo existe en la base de datos
     */
    public function validEmail($email) {
      return (bool) User::where('email', $email)->first();
    }

    public function generateToken($email){
      $token = Str::random(80);
      return $token;
    }

}