<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bank;
use Ramsey\Uuid\Uuid;

class BankController extends Controller
{
    public function bank()
    {
        $bank = DB::table('banks')
        ->select('id','bankname','branch','accname','accnumber')
        ->orderBy('bankname', 'asc')
        ->get();
        return response()->json($bank);
    }
    public function insertbank(Request $request)
    {
        $request->validate([
            'bankname'      => 'required',
            'branch'        => 'required',
            'accname'       => 'required',
            'accnumber'     => 'required'
        ]);

        $bank = new Bank([
            'id'            => Uuid::uuid4()->toString(),
            'bankname'      => strtoupper($request->bankname),
            'branch'        => strtoupper($request->branch),
            'accname'       => strtoupper($request->accname),
            'accnumber'     => $request->accnumber
            ]);
        $bank->save();
        $banks = $this->bank()->original;
        return response()->json($banks, 200);
    }
    public function updatebank(Request $request)
    {
        $request->validate([
            'bankname'      => 'required',
            'branch'        => 'required',
            'accname'       => 'required',
            'accnumber'     => 'required'
        ]);

        $bank = Bank::find($request->id);
        $bank->bankname   = strtoupper($request->bankname);
        $bank->branch     = strtoupper($request->branch);
        $bank->accname    = strtoupper($request->accname);
        $bank->accnumber  = $request->accnumber;
        $bank->save();
        $banks = $this->bank()->original;
        return response()->json($banks, 200);
    }
    public function deletebank(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);
        DB::table('banks')->where('id', $request->id)->delete();
        $banks = $this->bank()->original;
        return response()->json($banks, 200);
    }
    //
}
