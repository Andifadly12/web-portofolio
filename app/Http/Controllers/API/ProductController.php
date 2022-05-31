<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request){
        $id=$request->input('id');
        $limit=$request->input('limit');
        $name=$request->input('id');
        $description=$request->input('description');
        $tags=$request->input('tags');
        $categories=$request->input('categories');
        $price_from=$request->input('price_from');
        $price_to=$request->input('price_to');
        
        if($id){
            //category dan galleries diambil berdasarkan relasi
            //fungsi find untuk mengambil id
            $Product= Product::with(['category', 'galleries'])->find($id);
            //jika product ada maka akan dijalankan product dan mebuat sesiaon succes dengan pengambillan message ini diambil dari $ressponseFormatter.php
            if($Product){
                return ResponseFormatter::success(
                    $Product,
                    'Data produk berhasil diambil'
                );
                //begitu pula dengan error jika error maka aka adijalankan ressponsformatter dengan function statis function yaitu terdapat$data $massage dan $error atau pesan error  
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data produk tidak ada',
                    404
                );
            }
        }
        //untuk memngambil semua data kita memtudukan variable data diabawah ini
        $Product=Product::with(['category', 'galleries']);
        //mengambil variable name dengan menggunakan like 
        if ($name){
            $Product->where('name', 'Like', '%'.$name.'%');
        }
        if ($description){
            $Product->where('description', 'Like', '%'.$description.'%');
        }
        if ($tags){
            $Product->where('tags', 'Like', '%'.$tags.'%');
        }
        //mengambil price from jika besar atau = 
        if ($price_from){
            $Product->where('price','>=', $price_from);
        }
        //mengambil price ygna lebih kecil
        if ($price_to){
            $Product->where('price','>=', $price_to);
        }
        // mengambil category dengan menggunakan id
        if ($categories){
            $Product->where('categories',$categories);
        }
        //untuk mengambil semua data kita harus menggunakan responseFormatter dan pagginate untuk mgambil data tersubut
        return ResponseFormatter::success(
            $Product->paginate($limit),
            'Data produk berhasil diambil'
        );
    }
}
