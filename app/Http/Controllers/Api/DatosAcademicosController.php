<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\DatosAcademicosFormRequest;
use App\Models\DatosAcademicos;
use App\Http\Traits\ListTrait;
use Exception;

class DatosAcademicosController extends Controller
{

    use ListTrait;
    /**
     * Display a listing of the assets.
     *
     * @return DatosAcademicos
     */
    public function list($usuario_id)
    {
        $this->authorize('viewAny', DatosAcademicos::class);
        $datosAcademicosObjects = DatosAcademicos::where('usuario_id',$usuario_id)->paginate(25);

        $data = $datosAcademicosObjects->transform(function ($datosAcademicos) {
            return $this->transform($datosAcademicos);
        });

        return $this->successResponse(
            'Datos Academicos se recuperaron correctamente.',
            $data,
            $this->pagination($datosAcademicosObjects)
        );
    }


    /**
     * Store a new datos academicos in the storage.
     *
     * @param DatosAcademicosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(DatosAcademicosFormRequest $request)
    {
        $this->authorize('create', DatosAcademicos::class);

        $data = $request->getData();

        if(DatosAcademicos::where('usuario_id',$data['usuario_id'])->count() > 0){
            return response()->json([
              "errors" => ["Este usuario ya cuenta con datos académicos"],
              "success" => false,
            ],422);
          }

        $datosAcademicos = DatosAcademicos::create($data);

        return $this->successCreate(
          'Datos Academicos fue agregado exitosamente.',
          $this->transform($datosAcademicos)
        );

    }

    /**
     * Display the specified datos academicos.
     *
     * @param int $id
     *
     * @return
     */
    public function get($usuario_id,$datos_academicos_id)
    {
        $this->authorize('view', DatosAcademicos::class);
        $datosAcademicos = DatosAcademicos::with('user','bajastipo')->findOrFail($datos_academicos_id);

        return $this->successResponse(
          'Datos Academicos fue recuperado con éxito.',
          $this->transform($datosAcademicos)
        );
    }

    /**
     * Update the specified proyecto in the storage.
     *
     * @param $id
     * @param DatosAcademicosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($usuario_id,$datos_academicos_id, DatosAcademicosFormRequest $request)
    {
        $this->authorize('update', DatosAcademicos::class);


        $data = $request->getData();

        $datosAcademicos = DatosAcademicos::findOrFail($datos_academicos_id);
        $datosAcademicos->update($data);

          return $this->successResponse(
            'Datos Academicos se actualizó con éxito.',
            $this->transform($datosAcademicos)
          );

    }

    /**
     * Remove the specified proyecto from the storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($usuario_id,$datos_academicos_id)
    {
        $this->authorize('delete', DatosAcademicos::class);

        $datosAcademicos = DatosAcademicos::findOrFail($datos_academicos_id);
        $datosAcademicos->delete();

        return $this->successResponse(
          'Datos Academicos fue eliminado con éxito.',
          $this->transform($datosAcademicos)
        );
    }



    /**
     * Transform the giving datos academicos to public friendly array
     *
     * @param App\Models\DatosAcademicos $datosAcademicos
     *
     * @return array
     */
    protected function transform(DatosAcademicos $datosAcademicos)
    {
        return [
            'id' => $datosAcademicos->id,
            'matricula' => $datosAcademicos->matricula,
            'generacion' => $datosAcademicos->generacion,
            'usuario_id' => $datosAcademicos->usuario_id,
            'usuario' => $datosAcademicos->User,
            'baja_id' => $datosAcademicos->baja_id,
            'baja' => $datosAcademicos->BajasTipo,
            'status_id' => $datosAcademicos->status_id,
            'status' => $datosAcademicos->status,
            'fecha_baja' => $datosAcademicos->fecha_baja,
        ];
    }


}
