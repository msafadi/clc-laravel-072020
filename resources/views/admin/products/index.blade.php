@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between mb-5">
        <h1>Products</h1>
        <div>
            <a class="btn btn-outline-dark" href="{{ route('admin.products.create') }}">Create New</a>
        </div>
    </div>

    @include('_alert')
    
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($products as $model)
            <tr>
                <td><img height="50" src="{{ asset('storage/' . $model->image) }}" alt=""></td>
                <td><a href="{{ route('admin.products.edit', [$model->id]) }}">{{ $model->name }}</a></td>
                <td>{{ $model->category->name }}</td>
                <td>{{ $model->price }}</td>
                <td>{{ $model->quantity }}</td>
                <td>{{ $model->created_at }}</td>
                <td>
                <form method="post" action="{{ route('admin.products.destroy', [$model->id]) }}">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
{{ $products->links() }}
@endsection