<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Configuracion::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Configuracion::find($id), 200);
    }

    public function showResource ($name)
    {
        $data = Configuracion::where('nombre', $name)->first()->url;
        $r = getS3Url($data);
        return  response()->json($r,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Configuracion::find($id);

        if ($res) {
            Configuracion::find($id)->delete();
            return response()->json($res, 200);
        }
    }

    public function default ($id)
    {
        $res = Configuracion::find($id);
        $old_url = $res->url;
        $res->url = $res->default;
        if ($res->save() && ($old_url != $res->default)) {
            Storage::disk('s3')->delete($old_url);
        }
        return response()->json("Cambio a default", 200);
    }

    public function showCarusel(){
        $data = Configuracion::where('id','>', 2)->get() ;
        foreach ($data as  $value) {
            $value->url = getS3Url( $value->url);
        }
        return response()->json($data, 200);
    }
}
