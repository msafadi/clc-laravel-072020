<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Category;
use App\Rules\CheckParent;
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
        
        $categories = Category::leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name',
            ])
            ->get();
        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function edit($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $id;
        }
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
        $category->delete();
        // DELETE FROM categories WHERE id = ?
        Storage::disk('public')->delete($category->image);

        

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
}
