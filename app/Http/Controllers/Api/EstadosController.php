<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\EstadosFormRequest;
use App\Models\Estado;
use App\Http\Traits\ListTrait;
use Exception;

class EstadosController extends Controller
{

    use ListTrait;
    /**
     * Display a listing of the assets.
     *
     * @return Estado
     */
    public function list()
    {
        $this->authorize('viewAny', Estado::class);
        $estados = Estado::paginate(25);

        $data = $estados->transform(function ($estado) {
            return $this->transform($estado);
        });

        return $this->successResponse(
            'Estados se recuperaron correctamente.',
            $data,
            $this->pagination($estados)
        );
    }


    /**
     * Store a new estado in the storage.
     *
     * @param EstadosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(EstadosFormRequest $request)
    {
        $this->authorize('create', Estado::class);
        $data = $request->getData();
        $estado = Estado::create($data);

        return $this->successCreate(
          'Estado fue agregado exitosamente.',
          $this->transform($estado)
        );
    }

    /**
     * Display the specified estado.
     *
     * @param int $id
     *
     * @return
     */
    public function get($id)
    {
        $this->authorize('view', Estado::class);
        $estado = Estado::findOrFail($id);

        return $this->successResponse(
          'Estado fue recuperado con éxito.',
          $this->transform($estado)
        );
    }

    /**
     * Update the specified proyecto in the storage.
     *
     * @param $id
     * @param EstadosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, EstadosFormRequest $request)
    {
        $this->authorize('update', Estado::class);
        $data = $request->getData();

        $estado = Estado::findOrFail($id);
        $estado->update($data);

        return $this->successResponse(
          'Estado se actualizó con éxito.',
          $this->transform($estado)
        );
    }

    /**
     * Remove the specified proyecto from the storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $this->authorize('delete', Estado::class);
        $estado = Estado::findOrFail($id);
        $estado->delete();

        return $this->successResponse(
          'Estado fue eliminado con éxito.',
          $this->transform($estado)
        );
    }



    /**
     * Transform the giving estado to public friendly array
     *
     * @param App\Models\Estado $estado
     *
     * @return array
     */
    protected function transform(Estado $estado)
    {
        return [
            'id' => $estado->id,
            'nombre' => $estado->nombre,
            'activo' => ($estado->activo) ? 'Yes' : 'No',
        ];
    }


}
