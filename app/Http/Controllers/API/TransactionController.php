<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request){
        $id=$request->input('id');
        $limi=$request->input('limit');
        $status=$request->input('status');
        //mengambil data berdasarkan id
        if($id){
            //megembil data
            $transaction=Transaction::with('items.product')->find($id);
            //jika data transaksinya dapat diambil
            if($transaction){
                return ResponseFormatter::success(
                    $transaction,'Data transaksi berhasil'
                );
            }else{
                return ResponseFormatter::error(
                    null, 'Data transaksi tidak ada', 484
                );
            }
        }
        $transaction=Transaction::with('items.product')->where('users_id', Auth::user()->id);
        if ($status){
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limi),
            'data berhasil diambil'
        );
    }

    public function checkOut(Request $request){
        $request->validate([
            'items'=>'required:array',
            //* agar mengetahui apakah barang yang ingin di belih memang ada atau tidak
             //mengambil datanya product dengan menggunakan id
            'items.*.id'=>'exists:products,id',
            'total_price'=>'required',
            'shipping_price'=>'required',
            //validate in agar data yg dimasukkan di status itu hanya yang terdaftar saja
            'status'=>'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPING,SHIPPED'
        ]);
        $transaction=Transaction::create([
            'users_id'=>Auth::user()->id,
            'address'=>$request->address,
            'total_price'=>$request->total_price,
            'shipping_price'=>$request->shipping_price,
            'status'=>$request->status,
        ]);
        foreach($request->items as $product){
            TransactionItem::create([
                'users_id'=>Auth::user()->id,
                'products_id'=>$product['id'],
                'transactions_id'=>$transaction['id'],
                'quantity'=>$product['quantity']
            ]);
        }
        return ResponseFormatter::success($transaction->load('items.product'),'Transaksi berhasil');
    }
}
