<div class="row">
    <div class="col-md-6">
        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid mb-3" alt="{{ $product->name }}">
    </div>
    <div class="col-md-6">
        <h3>{{ $product->name }}</h3>
        <p class="text-primary font-weight-bold">${{ $product->price }}</p>
        <p>{{ $product->description }}</p>
        <form method="POST" action="{{ route('shop.cart.add', $product->id) }}">
            @csrf
            <div class="form-group">
                <label for="size">Size</label>
                <select name="size" id="size" class="form-control">
                    <option value="">Select size</option>
                    @foreach($product->sizes ?? [] as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="color">Color</label>
                <select name="color" id="color" class="form-control">
                    <option value="">Select color</option>
                    @foreach($product->colors ?? [] as $color)
                        <option value="{{ $color }}">{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
            </div>
            <button type="submit" class="btn btn-success">Add to Cart</button>
        </form>
    </div>
</div>
