<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Courier;
use Ramsey\Uuid\Uuid;

class CourierController extends Controller
{
    public function courier()
    {
        $courier = DB::table('couriers')
        ->select('id','courier','courierdesc')
        ->orderBy('courier', 'asc')
        ->get();
        return response()->json($courier);
    }
    public function insertcourier(Request $request)
    {
        $request->validate([
            'courier'      => 'required'
        ]);

        $courier = new Courier([
            'id'            => Uuid::uuid4()->toString(),
            'courier'       => $request->courier,
            'courierdesc'   => $request->courierdesc
            ]);
        $courier->save();
        $couriers = $this->courier()->original;
        return response()->json($couriers, 200);
    }
    public function updatecourier(Request $request)
    {
        $request->validate([
            'courier'      => 'required'
        ]);

        $courier = Courier::find($request->id);
        $courier->courier       = $request->courier;
         $courier->courierdesc  = $request->courierdesc;
        $courier->save();
        $couriers = $this->courier()->original;
        return response()->json($couriers, 200);
    }
    public function deletecourier(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);
        DB::table('couriers')->where('id', $request->id)->delete();
        $couriers = $this->courier()->original;
        return response()->json($couriers, 200);
    }
    //
}
