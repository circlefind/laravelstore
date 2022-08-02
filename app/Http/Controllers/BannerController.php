<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function banner()
    {
        $banner = DB::table('banners')
        ->select('id','imgbanner','imgcount')
        ->orderBy('imgcount', 'asc')
        ->get();
        return response()->json($banner);
    }
    public function updatebanner(Request $request)
    {
        DB::beginTransaction();
            try {
                $i = 0;

                $banner = DB::table('banners')
                    ->select('id','imgbanner','imgcount')
                    ->orderBy('imgcount', 'asc')
                    ->get();
                $databanner = json_decode(json_encode($banner), true);

                    foreach ($databanner as $file) {
                        File::delete('images/banner/' . $file['imgbanner']);
                        }
                    
                    DB::table('banners')->delete();

                if ($request->hasFile('imgfile'))
                {
                    $imgfiles = $request->file('imgfile');
                    foreach ($imgfiles as $imgfile) {
                            $extension = $imgfile->extension();
                            $filename = Uuid::uuid4()->toString();
                            $path = $imgfile->storeAs('images/banner', $filename.'.'.$extension, 'public');

                            $banner = new Banner([
                                'id'            => Uuid::uuid4()->toString(),
                                'imgbanner'     => $filename.'.'.$extension,
                                'imgcount'      => $i
                                ]);
                            $banner->save();
                            $i++;
                        }
                    }

            DB::commit();
            $banners = $this->banner()->original;
            return response()->json($banners, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }
    }
    public function getimage($imgname)
    {
        $path = public_path().'/images/banner/'.$imgname;
        return response()->download($path);        
    }
}
