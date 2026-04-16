@php($product = $product ?? null)

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-card p-3">
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label">Название</label>
            <input id="name"
                   name="name"
                   type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $product?->name) }}"
                   maxlength="255"
                   required>
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Описание</label>
            <textarea id="description"
                      name="description"
                      rows="5"
                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description) }}</textarea>
        </div>

        <div class="col-12 col-md-6">
            <label for="price" class="form-label">Цена</label>
            <input id="price"
                   name="price"
                   type="number"
                   min="0"
                   step="0.01"
                   class="form-control @error('price') is-invalid @enderror"
                   value="{{ old('price', $product?->price) }}"
                   required>
        </div>

        <div class="col-12 col-md-6">
            <label for="category_id" class="form-label">Категория</label>
            <select id="category_id"
                    name="category_id"
                    class="form-select @error('category_id') is-invalid @enderror"
                    required>
                <option value="">Выберите категорию</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Отмена</a>
    </div>
</div>
