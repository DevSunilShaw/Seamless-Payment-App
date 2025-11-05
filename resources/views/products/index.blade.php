<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Product Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4 text-center text-primary">🛍️ All Products</h1>
        
        <div class="row">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="{{ route('products.show', ['id' => $product['id']]) }}" class="text-decoration-none text-dark">
                                    **{{ $product['name'] }}**
                                </a>
                            </h5>
                            <p class="card-text text-muted">Product ID: \#{{ $product['id'] }}</p>
                            <p class="card-text fs-4 fw-bold mt-auto">Rs. {{ number_format($product['price'], 2) }}</p>
                            
                            <a href="{{ route('products.show', ['id' => $product['id']]) }}" class="btn btn-primary mt-2">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        No products found.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>