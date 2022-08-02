<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Config;
use Carbon\Carbon;

class ConfigController extends Controller
{
    public function config()
    {
        $config = DB::table('configs')
        ->select('id','corporatename','phone','fax','email','facebook','whatsapp','about','transmethod','imglogo')
        ->first();
        return response()->json($config);
    }
    public function serverdate()
    {
        $date = Carbon::now()->toJSON();
        return response()->json($date);
    }
    public function paper()
    {
        $paper = DB::table('papers')->get();
        return response()->json($paper);
    }
    public function updateconfig(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);

        $config = Config::find($request->id);
        $config->corporatename  = $request->corporatename;
        $config->phone          = $request->phone;
        $config->fax            = $request->fax;
        $config->email          = $request->email;
        $config->facebook       = $request->facebook;
        $config->whatsapp       = $request->whatsapp;
        $config->save();

        $configs = $this->config()->original;
        return response()->json($configs, 200);
    }
    public function updateabout(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);

        $config = Config::find($request->id);
        $config->about          = $request->about;
        $config->save();

        $configs = $this->config()->original;
        return response()->json($configs, 200);
    }
    public function updatetransmethod(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);

        $config = Config::find($request->id);
        $config->transmethod    = $request->transmethod;
        $config->save();

        $configs = $this->config()->original;
        return response()->json($configs, 200);
    }
    public function routeclear()
    {
        \Artisan::call('route:clear');
    }
    public function routecache()
    {
        \Artisan::call('route:cache');
    }
    public function optimize()
    {
        \Artisan::call('optimize');
    }
}
