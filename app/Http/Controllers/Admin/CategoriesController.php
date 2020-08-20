<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Category;
use App\Product;
use App\Rules\CheckParent;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoriesController extends Controller
{
    public function index()
    {
        session()->forget('message');
        
        /*$categories = Category::leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name',
            ])
            ->get();*/

        $categories = Category::withCount('products')->paginate();
        // Select categories.id, categories.name, COUNT(products.id) FROM categories
        //    INNER JOIN products ON ..
        //    GROUP BY categories.id, categories.name

        //$categories = Category::doesntHave('products')->withCount('products')->get();

        //$categories = Category::has('products', '>=', 2)->withCount('products')->get();
        
        /*$categories = Category::whereHas('products', function($query) {
            $query->where('price', '>', 20);
        })->get();*/

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function edit(Category $category)
    {
        //$category = Category::findOrFail($id);

        $parents = Category::all();/* Category::where('id', '<>', $id)
            ->where(function($query) use ($id) {
                $query->where('parent_id', '<>', $id)
                      ->orWhereNull('parent_id');
            })
            ->get();*/
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validator($request, $id);
        $validator->validate();
        /*try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|min:3|alpha',
                'description' => 'max:4000',
                'parent_id' => 'nullable|exists:categories,id',
            ]);
            $validator->fails();
            /*$request->validate([
                'name' => 'required|max:255|min:3|alpha',
                'description' => 'max:4000',
                'parent_id' => 'nullable|exists:categories,id',
            ]);*/
        /*} catch(Throwable $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }*/

        
        $image = $this->storeImage($request);

        $category = Category::findOrFail($id);

        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->description = $request->description;
        if ($image) {
            Storage::disk('public')->delete($category->image);
            $category->image = $image;
        } else if ($request->delete_image == 1) {
            Storage::disk('public')->delete($category->image);
            $category->image = null;
        }
        $category->save();

        $message = sprintf('Category "%s" updated!', $category->name);
        return redirect('/admin/categories')
            ->with('success', $message);
    }

    public function create()
    {
        return view('admin.categories.create', [
            'category' => new Category,
            'parents' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request);
        $validator->validate();

        $category = new Category;
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->description = $request->description;
        $category->image = $this->storeImage($request);
        $category->save();

        $message = sprintf('Category "%s" created!', $category->name);
        return redirect(route('admin.categories.index'))
            ->with('success', $message);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        try {
            $category->delete();
            // DELETE FROM categories WHERE id = ?
            Storage::disk('public')->delete($category->image);
        } catch (QueryException $e) {
            if (strpos($e->getMessage(), '1451') !== false) {
                $message = 'Cannot delete a parent item';
            } else {
                $message = $e->getMessage();
            }
            return redirect()->route('admin.categories.index')
                ->with('error', $message);
        }

        $message = sprintf('Category "%s" deleted!', $category->name);

        session()->put('message', $message);

        return redirect()->route('admin.categories.index')
            ->with('success', $message);
    }

    protected function validator($request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:3|string',
            'description' => 'max:4000',  
            'parent_id' => [
                'nullable',
                new CheckParent($id),
            ],
            'image' => 'nullable|image|max:512',
        ]);

        return $validator;
    }

    protected function storeImage($request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $name = $image->getClientOriginalName();
            $ext = $image->getClientOriginalExtension();
            $size = $image->getSize();
            $mime = $image->getType();
            return $image->store('images', 'public');
        }
    }

    public function products(Category $category)
    {
        //return $category->products()->where('price', '>', 10)->get();
        //Product::where('category_id', $category->id)->where('price', '>', 10)->get();

        return view('admin.categories.products', [
            'category' => $category,
        ]);
    }
}
