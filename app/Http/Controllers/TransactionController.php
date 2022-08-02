<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function alltransaction()
    {
        $transaction = DB::table('transactions AS tr')
            ->leftJoin('couriers AS cr', 'tr.idcourier', '=', 'cr.id')
            ->leftJoin('checkouts AS co', 'co.idtransaction', '=', 'tr.id')
            ->leftJoin('users AS us', 'us.id', '=', 'tr.iduser')
            ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
            ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
            ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
            ->leftJoin('banks AS bank', 'tr.idbank', '=', 'bank.id')
            ->groupBy('tr.id')
            ->selectRaw('tr.id, tr.datetr, tr.iduser, us.name, us.phone, us.address, province.region AS province, regency.region AS regency, district.region AS district, us.postalcode, tr.idcourier, tr.weight, cr.courier, tr.receiptnumber, cr.courierdesc ,SUM(co.subtotal) AS subtotal, tr.couriercost, tr.uniquecode, tr.transfer, tr.idbank, bank.bankname, bank.branch, tr.status')
            ->orderBy('tr.id', 'desc')
            ->get();
        return response()->json($transaction);
    }
    public function reptransaction()
    {
        $transaction = DB::table('transactions AS tr')
            ->leftJoin('couriers AS cr', 'tr.idcourier', '=', 'cr.id')
            ->leftJoin('checkouts AS co', 'co.idtransaction', '=', 'tr.id')
            ->leftJoin('users AS us', 'us.id', '=', 'tr.iduser')
            ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
            ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
            ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
            ->leftJoin('banks AS bank', 'tr.idbank', '=', 'bank.id')
            ->groupBy('tr.id')
            ->selectRaw('tr.id, tr.datetr, tr.iduser, us.name, us.phone, us.address, province.region AS province, regency.region AS regency, district.region AS district, us.postalcode, tr.idcourier, tr.weight, cr.courier, tr.receiptnumber, cr.courierdesc ,SUM(co.subtotal) AS subtotal, tr.couriercost, tr.uniquecode, tr.transfer, tr.idbank, bank.bankname, bank.branch, tr.status')
            ->where('tr.status','=','4')
            ->orderBy('tr.id', 'desc')
            ->get();
        return response()->json($transaction);
    }
    public function listtransaction($id)
    {
        $transaction = DB::table('transactions AS tr')
            ->leftJoin('couriers AS cr', 'tr.idcourier', '=', 'cr.id')
            ->leftJoin('checkouts AS co', 'co.idtransaction', '=', 'tr.id')
            ->leftJoin('users AS us', 'us.id', '=', 'tr.iduser')
            ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
            ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
            ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
            ->leftJoin('banks AS bank', 'tr.idbank', '=', 'bank.id')
            ->groupBy('tr.id')
            ->selectRaw('tr.id, tr.datetr, tr.iduser, us.name, us.phone, us.address, province.region AS province, regency.region AS regency, district.region AS district, us.postalcode, tr.idcourier, tr.weight, cr.courier, tr.receiptnumber, cr.courierdesc ,SUM(co.subtotal) AS subtotal, tr.couriercost, tr.uniquecode, tr.transfer, tr.idbank, bank.bankname, bank.branch, tr.status')
            ->where('tr.iduser', $id)
            ->orderBy('tr.id', 'desc')
            ->get();
        return response()->json($transaction);
    }
    public function updatecourier(Request $request)
    {
        $request->validate([
            'iduser'      => 'required',
            'idcourier'   => 'required'
        ]);

        $datenow = date("Y/m/d");
        $date = Carbon::createFromFormat('Y/m/d', $datenow);
        $valsequence = $date->format('d').$date->format('m').$date->format('Y');

        $lastinvoice = DB::table('transactions')
        ->whereRaw('SUBSTRING(id, 1,  8) ='.$valsequence)
        ->orderBy('id', 'desc')
        ->first();

        if (is_null($lastinvoice))
        {
            $inc = 0;
            $invoice = $valsequence.str_pad((int)$inc + 1, 3, 0, STR_PAD_LEFT);
        }else
        {
            $inc = substr($lastinvoice->id,8,3);
            $invoice = $valsequence.str_pad((int)$inc + 1, 3, 0, STR_PAD_LEFT);
        }

        DB::beginTransaction();
            try {

                $transaction = new Transaction([
                    'id'             => $invoice,
                    'datetr'         => $datenow,
                    'iduser'         => $request->iduser,
                    'idcourier'      => $request->idcourier,
                    'weight'         => $request->weight
                    ]);
                $transaction->save();

                $cart = DB::table('carts AS cr')
                    ->leftJoin('products AS pr', 'cr.idproduct', '=', 'pr.id')
                    ->leftJoin('imgproducts AS imp', function($join)
                        {
                        $join->on('pr.id', '=', 'imp.idproduct')
                        ->where('imp.imgcount', '=', '0');
                        })
                    ->select('cr.id','cr.idproduct','pr.productname','pr.price','pr.discount','pr.stock','pr.sold','cr.qty','cr.weight','cr.remark','imp.imgname')
                    ->where('cr.iduser',$request->iduser)
                    ->orderBy('cr.created_at', 'desc')
                    ->get();

                    $datacart = json_decode(json_encode($cart), true);

                    foreach ($datacart as $cart) {
                        $subtotal = ($cart['price'] - (($cart['price'] * $cart['discount']) / 100)) * $cart['qty'];
                        $checkout = new Checkout([
                            'id'                => Uuid::uuid4()->toString(),
                            'idtransaction'     => $invoice,
                            'idproduct'         => $cart['idproduct'],
                            'price'             => $cart['price'],
                            'discount'          => $cart['discount'],
                            'qty'               => $cart['qty'],
                            'weight'            => $cart['weight'],
                            'subtotal'          => $subtotal,
                            'remark'            => $cart['remark']
                            ]);
                        $checkout->save();
                        }
                    $this->addnotification('00000');

                    DB::table('carts')->where('iduser', $request->iduser)->delete();

            DB::commit();
            $listtransactions = $this->listtransaction($request->iduser)->original;
            return response()->json($listtransactions, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }

    }
    public function updatecost(Request $request)
    {
        $request->validate([
            'id'            => 'required',
            'couriercost'   => 'required'
        ]);

        $uniquecode = mt_rand(100, 999);
        $transaction = Transaction::find($request->id);
        $transaction->couriercost    = $request->couriercost;
        $transaction->uniquecode     = $uniquecode;
        $transaction->status         = 1;
        $transaction->save();

        $this->addnotification($request->iduser);

        $transactions = $this->alltransaction()->original;
        return response()->json($transactions, 200);
    }
    public function updatereceipt(Request $request)
    {
        $request->validate([
            'id'             => 'required',
            'receiptnumber'  => 'required'
        ]);

            DB::beginTransaction();
            try {
                $checkout = DB::table('checkouts AS co')
                    ->leftJoin('products AS pr', 'co.idproduct', '=', 'pr.id')
                    ->select('co.id','co.idproduct','pr.stock','pr.sold','co.qty')
                    ->where('co.idtransaction',$request->id)
                    ->get();

                    $datacheckout = json_decode(json_encode($checkout), true);

                    foreach ($datacheckout as $data) {
                        $stock = $data['stock'] - $data['qty'];
                        $sold = $data['sold'] + $data['qty'];
                        $product = Product::find($data['idproduct']);
                        $product->stock    = $stock;
                        $product->sold     = $sold;
                        $product->save();
                        }

                        $transaction = Transaction::find($request->id);
                        $transaction->receiptnumber  = $request->receiptnumber;
                        $transaction->status         = 3;
                        $transaction->save();

                        $this->addnotification($request->iduser);

            DB::commit();
            $transactions = $this->alltransaction()->original;
            return response()->json($transactions, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }
    }
    public function updatearrived(Request $request)
    {
        $request->validate([
            'id'             => 'required'
        ]);

        $transaction = Transaction::find($request->id);
        $transaction->status         = 4;
        $transaction->save();

        $this->addnotification('00000');

        $transactions = $this->listtransaction($request->iduser)->original;
        return response()->json($transactions, 200);
    }
    public function updatetransfer(Request $request)
    {
        $request->validate([
            'id'         => 'required',
            'idbank'     => 'required',
            'transfer'   => 'required'
        ]);

        $transaction = Transaction::find($request->id);
        $transaction->idbank     = $request->idbank;
        $transaction->transfer   = $request->transfer;
        $transaction->status     = 2;
        $transaction->save();

        $this->addnotification('00000');

        $transactions = $this->listtransaction($request->iduser)->original;
        return response()->json($transactions, 200);
    }
    public function notification($id)
    {
        $notif = DB::table('notifications')
            ->where('id',$id)
            ->first();
        return response()->json($notif, 200);
    }
    public function addnotification($id)
    {
        if (DB::table('notifications')->where('id', $id)->exists()) {
            $notification = Notification::find($id);
            $notification->transaction     = true;
            $notification->save();
        } else {

            $notification = new Notification([
                'id'                => $id,
                'transaction'       => '1'
                ]);
            $notification->save();
        }
        
        $notif = DB::table('notifications')
            ->where('id',$id)
            ->first();
        return response()->json($notif, 200);
    }
    public function removenotification($id)
    {
        if (DB::table('notifications')->where('id', $id)->exists()) {
            $notification = Notification::find($id);
            $notification->transaction     = false;
            $notification->save();
        }
        
        $notif = DB::table('notifications')
            ->where('id',$id)
            ->first();
        return response()->json($notif, 200);
    }
}
