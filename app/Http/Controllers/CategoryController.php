<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Ramsey\Uuid\Uuid;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function category()
    {
        $category = DB::table('categories')
        ->select('id','category')
        ->orderBy('category', 'asc')
        ->get();
        return response()->json($category);
    }
    public function insertcategory(Request $request)
    {
        $request->validate([
            'category'      => 'required'
        ]);

        $category = new Category([
            'id'            => Uuid::uuid4()->toString(),
            'category'      => $request->category
            ]);
        $category->save();
        $categories = $this->category()->original;
        return response()->json($categories, 200);
    }
    public function updatecategory(Request $request)
    {
        $request->validate([
            'category'      => 'required'
        ]);

        $category = Category::find($request->id);
        $category->category   = $request->category;
        $category->save();
        $categories = $this->category()->original;
        return response()->json($categories, 200);
    }
    public function deletecategory(Request $request)
    {
        $request->validate([
            'id'              => 'required'
        ]);
        DB::table('categories')->where('id', $request->id)->delete();
        $categories = $this->category()->original;
        return response()->json($categories, 200);
    }
}
