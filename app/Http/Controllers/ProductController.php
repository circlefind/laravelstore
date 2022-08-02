<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Imgproduct;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function listproduct()
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->leftJoin('imgproducts AS imp', function($join)
            {
            $join->on('pr.id', '=', 'imp.idproduct')
            ->where('imp.imgcount', '=', '0');
            })
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold','imp.imgname')
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function productpromo()
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->leftJoin('imgproducts AS imp', function($join)
            {
            $join->on('pr.id', '=', 'imp.idproduct')
            ->where('imp.imgcount', '=', '0');
            })
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold','imp.imgname')
        ->where('pr.discount', '>', '0')
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function productcategory($id)
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->leftJoin('imgproducts AS imp', function($join)
            {
            $join->on('pr.id', '=', 'imp.idproduct')
            ->where('imp.imgcount', '=', '0');
            })
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold','imp.imgname')
        ->where('pr.idcategory', '=', $id)
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function productfilter($filter)
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->leftJoin('imgproducts AS imp', function($join)
            {
            $join->on('pr.id', '=', 'imp.idproduct')
            ->where('imp.imgcount', '=', '0');
            })
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold','imp.imgname')
        ->where('pr.productname', 'LIKE', "%{$filter}%")
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function otherproduct($id)
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->leftJoin('imgproducts AS imp', function($join)
            {
            $join->on('pr.id', '=', 'imp.idproduct')
            ->where('imp.imgcount', '=', '0');
            })
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold','imp.imgname')
        ->where('pr.id', '<>', $id)
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function detailproduct($id)
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->select('pr.id','pr.idcategory','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold')
        ->where('pr.id', '=', $id)
        ->orderBy('pr.productname', 'asc')
        ->first();

        $imgproduct = DB::table('imgproducts')
            ->select('id','idproduct','imgname','imgcount')
            ->where('idproduct',$id)
            ->get();

        return response()->json(['product' => $product, 'imgproduct' => $imgproduct], 200);
    }
    public function product()
    {
        $product = DB::table('products AS pr')
        ->leftJoin('categories AS ct', 'pr.idcategory', '=', 'ct.id')
        ->select('pr.id','pr.idcategory','ct.category','pr.productname','pr.productdesc','pr.price','pr.discount','pr.stock','pr.weight','pr.sold')
        ->orderBy('pr.created_at', 'desc')
        ->get();
        return response()->json($product);
    }
    public function getimage($imgname)
    {
        $path = public_path().'/images/product/'.$imgname;
        return response()->download($path);        
    }
    public function imgproduct($id)
    {
        $imgproduct = DB::table('imgproducts')
            ->select('id','idproduct','imgname','imgcount')
            ->where('idproduct',$id)
            ->orderBy('imgcount', 'asc')
            ->get();
        return response()->json($imgproduct);
    }
    public function insertproduct(Request $request)
    {
        DB::beginTransaction();
            try {
                $data = json_decode($request->data, true);
                $id = Uuid::uuid4()->toString();
                $i = 0;
                if ($request->hasFile('imgfile'))
                {
                    $imgfiles = $request->file('imgfile');
                    foreach ($imgfiles as $imgfile) {
                        $extension = $imgfile->extension();
                        $filename = Uuid::uuid4()->toString();
                        $path = $imgfile->storeAs('images/product', $filename.'.'.$extension, 'public');
                        $imgproduct = new Imgproduct([
                            'id'            => Uuid::uuid4()->toString(),
                            'idproduct'     => $id,
                            'imgname'       => $filename.'.'.$extension,
                            'imgcount'      => $i
                            ]);
                        $imgproduct->save();
                        $i++;
                    }
                }
                    $product = new Product([
                        'id'            => $id,
                        'idcategory'    => $data['idcategory'],
                        'productname'   => $data['productname'],
                        'productdesc'   => $data['productdesc'],
                        'price'         => $data['price'],
                        'discount'      => $data['discount'],
                        'stock'         => $data['stock'],
                        'weight'        => $data['weight']
                        ]);
                    $product->save();
            DB::commit();
            $products = $this->product()->original;
            return response()->json($products, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }
    }
    public function updateproduct(Request $request)
    {
        DB::beginTransaction();
            try {
                $data = json_decode($request->data, true);
                $id = $data['id'];
                $i = 0;

                $imgproduct = DB::table('imgproducts')
                    ->select('id','idproduct','imgname','imgcount')
                    ->where('idproduct','=',$id)
                    ->get();
                    $dataimg = json_decode(json_encode($imgproduct), true);

                    foreach ($dataimg as $file) {
                        File::delete('images/product/' . $file['imgname']);
                        }
                    
                    DB::table('imgproducts')->where('idproduct', $id)->delete();

                if ($request->hasFile('imgfile'))
                {
                    $imgfiles = $request->file('imgfile');
                    foreach ($imgfiles as $imgfile) {
                            $extension = $imgfile->extension();
                            $filename = Uuid::uuid4()->toString();
                            $path = $imgfile->storeAs('images/product', $filename.'.'.$extension, 'public');

                            $imgproduct = new Imgproduct([
                                'id'            => Uuid::uuid4()->toString(),
                                'idproduct'     => $id,
                                'imgname'       => $filename.'.'.$extension,
                                'imgcount'      => $i
                                ]);
                            $imgproduct->save();
                            $i++;
                        }
                    }

                $product = Product::find($data['id']);
                $product->idcategory    = $data['idcategory'];
                $product->productname   = $data['productname'];
                $product->productdesc   = $data['productdesc'];
                $product->price         = $data['price'];
                $product->discount      = $data['discount'];
                $product->stock         = $data['stock'];
                $product->weight        = $data['weight'];
                $product->save();
            DB::commit();
            $products = $this->product()->original;
            return response()->json($products, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }
    }
    public function deleteproduct(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);
        DB::beginTransaction();
            try {
                $imgproduct = DB::table('imgproducts')
                    ->select('id','idproduct','imgname','imgcount')
                    ->where('idproduct','=',$request->id)
                    ->get();
                    $dataimg = json_decode(json_encode($imgproduct), true);

                    foreach ($dataimg as $file) {
                        File::delete('images/product/' . $file['imgname']);
                        }
            
                    DB::table('imgproducts')->where('idproduct', $request->id)->delete();
                    DB::table('products')->where('id', $request->id)->delete();
            DB::commit();
            $products = $this->product()->original;
            return response()->json($products, 200);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e, 400);
            }
    }
}
