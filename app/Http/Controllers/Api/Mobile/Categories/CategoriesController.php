<?php

namespace App\Http\Controllers\Api\Mobile\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $categories = DB::table('categorias')->orderBy('nombre')->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json(['message' => $categories]);
    }

    public function store(Request $request)
    {
        try {
            $save = DB::table('categorias')->insert([
                ['nombre' => $request->nombre],
                ['thumbnail' => $request->thumbnail,],
                ['background' => $request->background],
            ]);
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json(['exito' => 'true', 'data' => $save,'status' => '200'], 200);

    }

    public function update(Request $request,$id)
    {
       // dd($request);
        try {
            $category = DB::table('categorias')
            ->where('id', '=', $id)
            ->update(
                ['nombre'       => $request['nombre']],
                ['thumbnail'    =>  $request['thumbnail']],
                ['background'   => $request['background']]
            );
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json(['exito' => 'true','status' => '200'], 200);

    }

    public function delete(Request $request, $id)
    {
        try {
            $deleted = DB::table('categorias')->where('id', '=', $id)->delete();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json(['exito' => 'true', 'data' => $deleted,'status' => '200'], 200);

    }
}
