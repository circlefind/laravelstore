<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Checkout;

class CheckoutController extends Controller
{
    public function checkout($id)
    {
        $checkout = DB::table('checkouts AS co')
            ->leftJoin('products AS pr', 'pr.id', '=', 'co.idproduct')
            ->leftJoin('imgproducts AS imp', function($join)
                {
                $join->on('pr.id', '=', 'imp.idproduct')
                ->where('imp.imgcount', '=', '0');
                })
            ->select('co.id','co.idtransaction','co.idproduct','pr.productname','co.price','co.discount','co.qty','co.subtotal','co.remark','imp.imgname')
            ->where('co.idtransaction',$id)
            ->get();
        return response()->json($checkout);
    }
}
