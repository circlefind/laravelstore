<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'nullable|email|unique:users'
        ]);

        $user = new User([
            'id'                => Uuid::uuid4()->toString(),
            'name'              => $request->name,
            'datebirth'         => Carbon::createFromFormat('d-m-Y', $request->datebirth),
            'address'           => strtoupper($request->address),
            'idprovince'        => $request->idprovince,
            'idregency'         => $request->idregency,
            'iddistrict'        => $request->iddistrict,
            'postalcode'        => $request->postalcode,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'email_verified_at' => now(),
            'password'          => Hash::make($request->password),
            'idroleuser'        => '99',
            'remember_token'    => Str::random(10)
            ]);
        $user->save();
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user  = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('token')->plainTextToken;
            $userdata = DB::table('users AS us')
                ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
                ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
                ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
                ->select('us.id','us.name','us.datebirth','us.address','us.idprovince','province.region AS province','us.idregency','regency.region AS regency','us.iddistrict','district.region AS district','us.postalcode','us.phone','us.email','us.idroleuser')
                ->where('us.email', $request->email)
                ->first();
            return response()->json(['data' => ['token' => $token, 'user' => $userdata]], 200);
        }
    }
    public function updateuser(Request $request)
    {
        $request->validate([
            'id'            => 'required'
        ]);
        $user = User::find($request->id);
        $user->name             = $request->name;
        $user->datebirth        = Carbon::createFromFormat('d-m-Y', $request->datebirth);
        $user->address          = $request->address;
        $user->idprovince       = $request->idprovince;
        $user->idregency        = $request->idregency;
        $user->iddistrict       = $request->iddistrict;
        $user->postalcode       = $request->postalcode;
        $user->phone            = $request->phone;
        $user->save();

        $users = DB::table('users AS us')
                ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
                ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
                ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
                ->select('us.id','us.name','us.datebirth','us.address','us.idprovince','province.region AS province','us.idregency','regency.region AS regency','us.iddistrict','district.region AS district','us.postalcode','us.phone','us.email','us.idroleuser')
                ->where('us.email', $request->email)
                ->first();

        return response()->json($users, 200);
    }
    public function resetpassword(Request $request)
    {
        $request->validate([
            'password'      => 'required'
        ]);
        try {
            $user = User::find($request->id);
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'Password Telah Direset!'], 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function userlogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" =>  "required",
            "password" =>  "required",
        ]);
        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }
        $user = User::where("email", $request->email)->first();
        if(is_null($user)) {
            return response()->json(["status" => "failed", "message" => "Failed Sign In, Email user tidak terdaftar!"],400);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user  = Auth::user();
            $userupdate = User::find($user->id);
            $userupdate->save();
            $user->tokens()->delete();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json(['data' => ['token' => $token, 'user' => $user]], 200);
        }
        else {
            return response()->json(['message' => 'Failed Sign In!'], 400);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" =>  "required",
            "password" =>  "required",
        ]);
        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $user = User::where("email", $request->email)->first();

        if(is_null($user)) {
            return response()->json(["status" => "failed", "message" => "Failed Sign In, Email tidak terdaftar!"],400);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'idroleuser' => '99'])){
            $user  = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('token')->plainTextToken;
            $userdata = DB::table('users AS us')
                    ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
                    ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
                    ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
                    ->select('us.id','us.name','us.datebirth','us.address','us.idprovince','province.region AS province','us.idregency','regency.region AS regency','us.iddistrict','district.region AS district','us.postalcode','us.phone','us.email','us.idroleuser')
                    ->where('us.email', $request->email)
                    ->first();

            return response()->json(['data' => ['token' => $token, 'user' => $userdata]], 200);
        }
        else {
            return response()->json(['message' => 'Failed Sign In!'], 400);
        }
    }
    public function loginadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" =>  "required",
            "password" =>  "required",
        ]);
        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $user = User::where("email", $request->email)->first();

        if(is_null($user)) {
            return response()->json(["status" => "failed", "message" => "Failed Sign In, Email tidak terdaftar!"],400);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'idroleuser' => '0'])){
            $user  = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('token')->plainTextToken;
            $userdata = DB::table('users AS us')
                    ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
                    ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
                    ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
                    ->select('us.id','us.name','us.datebirth','us.address','us.idprovince','province.region AS province','us.idregency','regency.region AS regency','us.iddistrict','district.region AS district','us.postalcode','us.phone','us.email','us.idroleuser')
                    ->where('us.email', $request->email)
                    ->first();

            return response()->json(['data' => ['token' => $token, 'user' => $userdata]], 200);
        }
        else {
            return response()->json(['message' => 'Failed Sign In!'], 400);
        }
    }
    public function user(Request $request)
    {
        $user = DB::table('users AS us')
                ->leftJoin('regions AS province', 'us.idprovince', '=', 'province.id')
                ->leftJoin('regions AS regency', 'us.idregency', '=', 'regency.id')
                ->leftJoin('regions AS district', 'us.iddistrict', '=', 'district.id')
                ->select('us.id','us.name','us.datebirth','us.address','us.idprovince','province.region AS province','us.idregency','regency.region AS regency','us.iddistrict','district.region AS district','us.postalcode','us.phone','us.email','us.idroleuser')
                ->where('us.email', $request->user()->email)
                ->first();
        return response()->json($user);
    }
    public function roleuser()
    {
        $roleuser = DB::table('roleusers')
        ->select('id','role')
        ->where('id','!=','0')
        ->orderBy('id', 'asc')
        ->get();
        return response()->json($roleuser);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Sign out from Co2.online'
        ]);
    }
    public function changepassword(Request $request){
        if (!(Hash::check($request->get('currentpassword'), Auth::user()->password))) {
            return response()->json(['message' => 'Password lama tidak sesuai'], 406);
        }
        $validatedData = $request->validate([
            'currentpassword'   => 'required',
            'password'          => 'required|string|min:6',
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return response()->json(['message' => 'Password telah diupdate'], 200);
    }
}