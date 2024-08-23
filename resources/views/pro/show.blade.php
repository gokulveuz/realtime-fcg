@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $product->product_name }}</h1>

        <div id="product-variants">
            @foreach ($product->options as $option)
                <div class="form-group">
                    <label for="option-{{ $option->id }}">{{ $option->option_name }}</label>
                    <select id="option-{{ $option->id }}" class="form-control variant-option"
                        data-option-id="{{ $option->id }}">
                        <option value="">Select {{ $option->option_name }}</option>
                        @foreach ($option->optionValues as $value)
                            <option value="{{ $value->id }}">{{ $value->value_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>

        <div id="selected-sku">
            <!-- This will be dynamically updated based on variant selection -->
        </div>

        <button id="add-to-cart" class="btn btn-primary" disabled>Add to Cart</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.variant-option');
            const selectedSkuDiv = document.getElementById('selected-sku');
            const addToCartButton = document.getElementById('add-to-cart');

            options.forEach(option => {
                option.addEventListener('change', function() {

                    let selectedOptions = {};
                    options.forEach(opt => {

                        console.log(opt);

                        selectedOptions[opt.dataset.optionId] = opt.value;
                    });

                    fetchSku(selectedOptions);
                });
            });

            function fetchSku(selectedOptions) {

                console.log(selectedOptions);

                // Assuming you have an endpoint to fetch the SKU based on selected options
                fetch(`/product/{{ $product->id }}/sku`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(selectedOptions)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.sku) {
                            selectedSkuDiv.innerHTML = `<p>SKU: ${data.sku}</p><p>Price: $${data.price}</p>`;
                            addToCartButton.disabled = false;
                        } else {
                            selectedSkuDiv.innerHTML = '<p>No SKU available for the selected options.</p>';
                            addToCartButton.disabled = true;
                        }
                    });
            }
        });
    </script>
@endsection
