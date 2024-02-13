@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top: 30px;">
        <div class="row" style="margin-top: 30px; d-flex justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>IMAGE READER</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('image.reader') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Upload Image</label>
                                <input class="form-control" type="file" name="image" id="formFile">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="contents mb-3 ml-4">

                        @if (session('data'))
                            @if (isset(session('data')['email']))
                                <div>
                                    Name : {{ implode(',', session('data')['email']) }}
                                </div>
                            @endif
                            @if (isset(session('data')['email']))
                                <div>
                                    Email : {{ implode(',', session('data')['email']) }}
                                </div>
                            @endif
                            @if (isset(session('data')['phone_number']))
                                <div>
                                    phone_number : {{ implode(',', session('data')['phone_number']) }}
                                </div>
                            @endif
                            @if (isset(session('data')['website']))
                                <div>
                                    Website : {{ implode(',', session('data')['website']) }}
                                </div>
                            @endif
                        @endif

                    </div>
                </div>


            </div>

        </div>
    </div>

@endsection
