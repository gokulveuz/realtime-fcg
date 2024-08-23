<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mail Chimp</title>
    <style>
        .error {
            font-size: 12px;
            color: red;
        }
    </style>
</head>

<body>
    @if (@session('success'))
        {{ session('success') }}
    @endif
    <form action="{{ route('mailchimp') }}" class="main" method="POST">
        @csrf
        <div>
            <label for="">Name</label>
            <div>

                <input type="text" name="name" id="name" value="{{ old('name') }}">
            </div>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="">Subject</label>
            <div>
                <input type="text" name="subject" id="subject">
            </div>
            @error('subject')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="">Message</label>
            <div>
                <textarea name="message" id="message" cols="30" rows="4"></textarea>
            </div>
            @error('message')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
</body>

</html>
