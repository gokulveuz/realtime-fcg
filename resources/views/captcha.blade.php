<html>

<head>
    <title>reCAPTCHA demo</title>
</head>

<body>
    <form id="form">
        @csrf

        <label>Name
            <input type="text" name="name">
        </label>

        @error('name')
            <div>{{ $message }}</div>
        @enderror

        <button class="btn btn-success">Submit</button>

        @error('g-recaptcha-response')
            <div>{{ $message }}</div>
        @enderror
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('GOOGLE_RECAPTCHA_KEY') }}"></script>

    <script>
        $("#form").on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            grecaptcha.execute('{{ env('GOOGLE_RECAPTCHA_KEY') }}', {
                action: 'submit'
            }).then(function(token) {
                console.log(token);
                formData.append('g-recaptcha-response', token);

                $.ajax({
                    type: "post",
                    url: "{{ route('post') }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success == true) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                    }
                });
            });

        });
    </script>
</body>

</html>
