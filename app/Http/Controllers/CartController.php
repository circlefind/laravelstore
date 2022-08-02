<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use Ramsey\Uuid\Uuid;

class CartController extends Controller
{
    public function cart($id)
    {
        $cart = DB::table('carts AS cr')
            ->leftJoin('products AS pr', 'cr.idproduct', '=', 'pr.id')
            ->leftJoin('imgproducts AS imp', function($join)
                {
                $join->on('pr.id', '=', 'imp.idproduct')
                ->where('imp.imgcount', '=', '0');
                })
            ->select('cr.id','cr.idproduct','pr.productname','pr.price','pr.discount','pr.stock','cr.qty','cr.weight','cr.remark','imp.imgname')
            ->where('cr.iduser',$id)
            ->orderBy('cr.created_at', 'desc')
            ->get();
        return response()->json($cart);
    }
    public function insertcart(Request $request)
    {
        $request->validate([
            'idproduct'      => 'required',
            'iduser'         => 'required',
            'qty'            => 'required'
        ]);

        $findcart = DB::table('carts')
            ->where('idproduct', $request->idproduct)
            ->where('iduser', $request->iduser)
            ->first();

        if ($findcart == null)
        {
            $cart = new Cart([
                'id'            => Uuid::uuid4()->toString(),
                'idproduct'     => $request->idproduct,
                'iduser'        => $request->iduser,
                'qty'           => $request->qty,
                'weight'        => $request->weight * $request->qty,
                'remark'        => $request->remark
                ]);
            $cart->save();
            $carts = $this->cart($request->iduser)->original;
            return response()->json($carts, 200);
        } else {
                $cart = cart::find($findcart->id);
                $cart->qty       = $findcart->qty + $request->qty;
                $cart->remark    = $request->remark;
                $cart->save();
                $carts = $this->cart($request->iduser)->original;
                return response()->json($carts, 200);
        }
    }
    public function updatecart(Request $request)
    {
        $request->validate([
            'id'      => 'required'
        ]);

        $cart = Shopchart::find($request->id);
        $cart->qty       = $request->qty;
        $cart->remark    = $request->remark;
        $cart->save();
        $carts = $this->cart($request->iduser)->original;
        return response()->json($carts, 200);
    }
    public function updateqty(Request $request)
    {
        $request->validate([
            'id'      => 'required'
        ]);

        $cart = Cart::find($request->id);
        $cart->weight    = ($cart->weight / $cart->qty) * $request->qty;
        $cart->qty       = $request->qty;
        $cart->save();
        $carts = $this->cart($request->iduser)->original;
        return response()->json($carts, 200);
    }
    public function updateremark(Request $request)
    {
        $request->validate([
            'id'      => 'required'
        ]);

        $cart = Cart::find($request->id);
        $cart->remark       = $request->remark;
        $cart->save();
        $carts = $this->cart($request->iduser)->original;
        return response()->json($carts, 200);
    }
    public function deletecart($id, $iduser)
    {
        DB::table('carts')->where('id', $id)->delete();
        $carts = $this->cart($iduser)->original;
        return response()->json($carts, 200);
    }
}
