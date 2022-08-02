<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function region()
    {
        $region = DB::table('regions')
        ->select('id','region')
        ->orderBy('id', 'asc')
        ->get();
        return response()->json($region);
    }
}
