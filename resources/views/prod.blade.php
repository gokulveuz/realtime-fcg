<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>

<body>

    @foreach ($products as $product)
        <a href="{{ route('product.show', $product->id) }}">{{ $product->product_name }}</a>
    @endforeach
{{-- 
    <form id="productForm" action="{{ route('products.store') }}" method="POST">
        @csrf --}}

        <div>
            <label for="">search options</label>
            <input type="text" name="search_tag" id="search_tag">
            <div id="appendData">

            </div>
        </div>


        {{-- <div>
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>

        <div id="optionsContainer">
            <h4>Product Options</h4>
            <div class="option-group" data-index="0">
                <label>Option Name:</label>
                <input type="text" name="options[0][name]" placeholder="Size, Color, etc." required>

                <div class="valuesContainer">
                    <label>Option Values:</label>
                    <input type="text" name="options[0][values][]" placeholder="Small" required>
                    <button type="button" class="addValue">Add Value</button>
                </div>
                <button type="button" class="removeOption">Remove Option</button>
            </div>
        </div>

        <button type="button" id="addOption">Add Another Option</button>
        <button type="submit">Submit Product</button> --}}
    {{-- </form> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let firstClick = true;

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                if (firstClick) {
                    func.apply(this, args);
                    firstClick = false;
                } else {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                }
            };
        }

        function performAjax() {

            let searchValue = $("#search_tag")

            $.ajax({
                type: 'GET',
                url: "{{ route('get.attribute') }}",
                success: function(data) {
                    $("#appendData").html(data.view);
                },
                error: function(data) {}
            });
        }

        $("#search_tag").on('input keyup', debounce(performAjax, 300));
        $("#search_tag").on('click', function() {
            if (firstClick) {
                performAjax();
                firstClick = false;
            }
        });


        $(document).ready(function() {
            let optionIndex = 1;

            $('#addOption').click(function() {
                let optionGroup = `
                    <div class="option-group" data-index="${optionIndex}">
                        <label>Option Name:</label>
                        <input type="text" name="options[${optionIndex}][name]" placeholder="Size, Color, etc." required>

                        <div class="valuesContainer">
                            <label>Option Values:</label>
                            <input type="text" name="options[${optionIndex}][values][]" placeholder="Value" required>
                            <button type="button" class="addValue">Add Value</button>
                        </div>
                        <button type="button" class="removeOption">Remove Option</button>
                    </div>
                `;
                $('#optionsContainer').append(optionGroup);
                optionIndex++;
            });

            $(document).on('click', '.addValue', function() {
                let valuesContainer = $(this).closest('.valuesContainer');
                let index = valuesContainer.closest('.option-group').data('index');
                valuesContainer.append(`
                    <input type="text" name="options[${index}][values][]" placeholder="Value" required>
                `);
            });

            $(document).on('click', '.removeOption', function() {
                $(this).closest('.option-group').remove();
            });
        });
    </script>
</body>

</html>
