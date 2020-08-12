    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @csrf
    <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input required type="text" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" name="name" id="name">
            @error('name')
            <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label for="parent_id" class="col-sm-2 col-form-label">Parent</label>
        <div class="col-sm-10">
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">No Parent</option>
                @foreach($parents as $parent)
                <option @if($parent->id == old('parent_id', $category->parent_id)) selected @endif value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
            @error('parent_id')
            <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label for="description" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" name="description" id="description">{{ old('description', $category->description) }}</textarea>
            @error('description')
            <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label for="image" class="col-sm-2 col-form-label">Image</label>
        <div class="col-sm-10">
            @if($category->image)
            <img class="mb-2" width="500" src="{{ asset('storage/' . $category->image) }}">
            <input type="checkbox" name="delete_image" value="1"> Delete Image
            @endif
            <input type="file" class="form-control" name="image" id="image">
            @error('image')
            <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <button class="btn btn-outline-primary" type="submit">Save</button>
    </div>