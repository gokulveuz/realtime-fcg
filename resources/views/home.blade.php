@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top: 30px;">
        <div class="row" style="margin-top: 30px;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Real Time Data</h4>
                        <button class="btn btn-success" style="margin-bottom: 20px;" onclick="modalOpen(1)">Add Data</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered mb-5">
                            <tr>
                                <th>Sl.No</th>
                                <th>Title</th>
                                <th>Decription</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($data as $key=>$value)
                                <tr>
                                    <td>{{ $i}}</td>
                                    <td>{{ $value['title'] }}</td>
                                    <td>{{ $value['description'] }}</td>
                                    <td><img style="width:200px;" src="{{ asset('images1/' . $value['image']) }}" alt=""></td>
                                    <th>
                                        <input type="button" class="btn btn-success editBtn" value="Edit" data-data="{{$key}}">
                                        <input type="button" value="Delete" class="btn btn-danger deleteBtn" data-data="{{$key}}">
                                    </th>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach

                            @if (count($data) == 0)
                                <tr>
                                    <td colspan="4" style="text-align: center;">No data found!</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                </div>


            </div>

        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="employeeModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="employeeForm">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Enter title">
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <img src="" id="imagesrc" alt="" style="width:380px;">
                        </div>
                        <br>
                        <div>
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" id="description" cols="10" rows="4"></textarea>
                        </div>
                        <br>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="dataButton" onclick="saveData(event)">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function modalOpen(value) {
            if(value == 1)
            {
                $("#id").val("");
                $("#dataButton").text("Save");
                $("#imagesrc").attr('src',"");
                $('#employeeForm').trigger("reset");
            }
            $("#employeeModal").modal('show');

        }

        function closeModal() {
            $("#employeeModal").modal('hide');
        }

        function saveData(e) {
            e.preventDefault();

            let data = $("#employeeForm")[0];
            const formData = new FormData(data);

            $.ajax({
                url: '{{ route('adddata') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#dataButton').attr('disabled', 'disabled');
                    $('#dataButton').text('Adding..');
                },
                complete: function() {
                    $('#dataButton').attr("disabled", false);
                    $('#dataButton').text('Add');
                },
                success: function(data) {
                    if (data.success == true) {
                        closeModal();
                        $('#employeeForm').trigger("reset");
                        notify("success", data.message);

                        window.location.href = "/";

                    } else {
                        notify("warning", data.message);
                    }
                },
                error: function(data) {
                    console.log(data);
                    notify('danger', 'Something went wrong!');
                }
            });

        }


       $(".editBtn").on('click',function(){
            $("#dataButton").text("Update");
            let  id = $(this).attr('data-data');
            let url = "{{ route('getdata', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'get',
                success: function(data) {
                    $("#id").val(id);
                    $("#title").val(data.title);
                    $("#description").val(data.description);
                    $("#imagesrc").attr('src', `images1/${data.image}`);
                    modalOpen(2);
                },
                error: function(data) {
                    console.log(data);
                    notify('danger', 'Something went wrong!');
                }
            });
        });

        $(".deleteBtn").on('click',function() {
            let  id = $(this).attr('data-data');
            $.confirm({
                title: 'Confirm Delete',
                content: 'Do you want to delete?',
                type: 'red',
                buttons: {
                    tryAgain: {
                        text: 'CONFIRM',
                        //btnClass: 'btn-success',
                        keys: ['y'],
                        action: function() {
                            let route = '{{ route('delete') }}';
                            const formData = new FormData();
                            formData.append('_token', "{{ csrf_token() }}");
                            formData.append('id', id);
                            $.ajax({
                                url: route,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(data) {
                                    notify("success", "Deleted");
                                    window.location.href = "/";
                                },
                                error: function(data) {
                                    console.log(data);
                                    notify('danger', 'Something went wrong!');
                                }
                            });

                        }
                    },
                    cancel: {
                        keys: ['n'],
                        action: function() {

                        }
                    }
                }
            });

        });
    </script>
@endpush
