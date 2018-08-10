@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload file csv</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    csv
                </div>
                <div>
                    <form method="post" action="{{route('upload-csv')}}" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input type="file" name="file">
                        <button type="submit">Submit</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
