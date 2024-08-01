<html>

<head>
    <title>reCAPTCHA demo</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
    <form method="post" action="{{ route('post') }}" id="form">
        @csrf

        <label>Name
            <input type="text" name="name">
        </label>

        @error('name')
            <div>{{ $message }}</div>
        @enderror

        <button class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"
            data-callback='onSubmit'>Submit</button>

        @error('g-recaptcha-response')
            <div>{{ $message }}</div>
        @enderror
    </form>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onSubmit(token) {
            document.getElementById('form').submit();
        }
    </script>
</body>

</html>
