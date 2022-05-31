<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function all(Request $request){
        $id=$request->input('id');
        $limit=$request->input('limit');
        $name=$request->input('id');
        // sebagai penampung ptoduct
        $show_product=$request->input('show_product');
        //untuk mengecek apaka $id ada atau tidak
        
        if($id){
            //variable untuk mengambil id
            $productcategory=ProductCategory::with('Products')->find($id);

            //pengecekan jika $productcategory tru maka akan di jalakan
            if($productcategory){
                return ResponseFormatter::success(
                    $productcategory, 'data productcategory berhasil datambahkan'
                );
            }else{
                return ResponseFormatter::error(
                    $productcategory,'Data prooductcxregorry tidak ada',
                    404
                );
            }
        }
    //untuk mengambil semua data
    $productcategory=ProductCategory::query();
    //unutk mengabil data nama
    if($name){
        $productcategory->where('name','Like'.$name.'%');
    }
    //untuk menampung relasi keproduct
    if($show_product){
        $productcategory->with('products');
    }
    //untuk menyimpan data ke database
    return ResponseFormatter::success(
        $productcategory->paginate($limit),
        'Data Berhasil ditambah'
    );
    }
}
