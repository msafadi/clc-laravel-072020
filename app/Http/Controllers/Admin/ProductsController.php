<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductDescription;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'products.*',
                'categories.name as category_name',
            ])->paginate(1);*/
        
        $products = Product::with('category')->paginate();
        // SELECT * FROM products
        // SELECT * FROM categories WHERE id IN (1, 2, 3)

        /*$products = Product::whereHas('tags', function($query) {
            $query->where('name', 'php');
        })->paginate();*/

        return view('admin.products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create', [
            'product' => new Product(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'numeric',
            'quantity' => 'numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
            'tags' => 'nullable|string',
        ]);

        $data = $request->except(['image', '_token', 'tags']);

        $image = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        DB::beginTransaction();
        try {
            $product = Product::create($data);
            $this->insertTags($request, $product);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with('error', 'Operation failed');
        }

        //$category = Category::findOrFail($request->category_id);
        //$category->products()->create($data);

        $message = sprintf('Product %s created', $product->name);
        return redirect()->route('admin.products.index')
            ->with('success', $message);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //$product = Product::findOrFail($id);
        $tags = $product->tags()->pluck('name')->toArray();
        $tags = implode(', ', $tags);
        return view('admin.products.edit', [
            'product' => $product,
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'numeric',
            'quantity' => 'numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
        ]);

        $product = Product::findOrFail($id);

        $data = $request->except(['image', '_token', 'tags']);

        $image = $product->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            Storage::disk('public')->delete($product->image);
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        DB::beginTransaction();
        try {
            $desc = ProductDescription::firstOrCreate([
                'product_id' => $product->id
            ], [
                'description' => $request->post('description')
            ]);

            $product->update($data);
            $desc->product()->associate($product);
            
            $this->insertTags($request, $product);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with('error', 'Operation failed: ' . $e->getMessage());
        }
        

        $message = sprintf('Product %s updated', $product->name);
        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        Storage::disk('public')->delete($product->image);

        $message = sprintf('Product %s deleted', $product->name);
        return redirect()->route('admin.products.index')
            ->with('success', $message);

    }

    protected function insertTags($request, $product)
    {
        $tags = $request->post('tags');
        $tags_ids = [];
        if ($tags) {
            $tags_array = explode(',', $tags);
            foreach ($tags_array as $tag_name) {
                $tag_name = trim($tag_name);
                $tag = Tag::where('name', $tag_name)->first();
                if (!$tag) {
                    $tag = Tag::create([
                        'name' => $tag_name,
                    ]);
                }
                //$product->tags()->save($tag);
                $tags_ids[] = $tag->id;
            }
        }
        $product->tags()->sync($tags_ids);

        /*DB::table('products_tags')->where('product_id', $product->id)->delete();
        if ($tags) {
            $tags_array = explode(',', $tags);
            foreach ($tags_array as $tag_name) {
                $tag_name = trim($tag_name);
                $tag = Tag::where('name', $tag_name)->first();
                if (!$tag) {
                    $tag = Tag::create([
                        'name' => $tag_name,
                    ]);
                }
                DB::table('products_tags')->insert([
                    'product_id' => $product->id,
                    'tag_id' => $tag->id,
                ]);
            }
        }*/
    }
}
