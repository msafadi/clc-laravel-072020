<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'products.*',
                'categories.name as category_name',
            ])->paginate(1);

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
        ]);

        $data = $request->except(['image', '_token']);

        $image = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        $product = Product::create($data);
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
        return view('admin.products.edit', [
            'product' => $product,
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

        $data = $request->except(['image', '_token']);

        $image = $product->image;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            Storage::disk('public')->delete($product->image);
            $image = $request->file('image')->store('images', 'public');
        }
        $data['image'] = $image;

        $product->update($data);

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
}
