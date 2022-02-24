<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Municipio;
use Illuminate\Http\Request;
use App\Http\Traits\ListTrait;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\MunicipiosFormRequest;

class MunicipiosController extends Controller
{

    use ListTrait;
    /**
     * Display a listing of the assets.
     *
     * @return Municipio
     */
    public function list(Request $request)
    {
        $this->authorize('viewAny', Municipio::class);
        $municipios = Municipio::where('estado_id',$request->estado_id)->paginate(25);

        $data = $municipios->transform(function ($municipio) {
            return $this->transform($municipio);
        });

        return $this->successResponse(
            'Municipios se recuperaron correctamente.',
            $data,
            $this->pagination($municipios)
        );
    }


    /**
     * Store a new municipio in the storage.
     *
     * @param MunicipiosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(MunicipiosFormRequest $request)
    {
        $this->authorize('create', Municipio::class);
        $data = $request->getData();
        $municipio = Municipio::create($data);

        return $this->successCreate(
          'Municipio fue agregado exitosamente.',
          $this->transform($municipio)
        );
    }

    /**
     * Display the specified municipio.
     *
     * @param int $id
     *
     * @return
     */
    public function get($estado_id, $id)
    {
        $this->authorize('view', Municipio::class);
        $municipio = Municipio::with('estado')->findOrFail($id);

        return $this->successResponse(
          'Municipio fue recuperado con éxito.',
          $this->transform($municipio)
        );
    }

    /**
     * Update the specified proyecto in the storage.
     *
     * @param $id
     * @param MunicipiosFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($estado_id, $id, MunicipiosFormRequest $request)
    {
        $this->authorize('update', Municipio::class);
        $data = $request->getData();
        $municipio = Municipio::findOrFail($id);
        $municipio->update($data);

        return $this->successResponse(
          'Municipio se actualizó con éxito.',
          $this->transform($municipio)
        );
    }

    /**
     * Remove the specified proyecto from the storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($estado_id, $id)
    {
        $this->authorize('delete', Municipio::class);
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();

        return $this->successResponse(
          'Municipio fue eliminado con éxito.',
          $this->transform($municipio)
        );
    }



    /**
     * Transform the giving municipio to public friendly array
     *
     * @param App\Models\Municipio $municipio
     *
     * @return array
     */
    protected function transform(Municipio $municipio)
    {
        return [
            'id' => $municipio->id,
            'nombre' => $municipio->nombre,
            'estado_id' => $municipio->estado_id,
            'estado' => $municipio->Estado,
        ];
    }


}
