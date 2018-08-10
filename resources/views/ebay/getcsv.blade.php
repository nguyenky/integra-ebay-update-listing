@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
            <div class="frame">
              <div class="center">
                    <div class="bar"></div>
                    <div class="title">Drop file to upload</div>
                    <form method="post" action="{{route('upload-csv')}}" enctype="multipart/form-data">
                    <div class="dropzone">
                        <div class="content">
                            <img src="http://100dayscss.com/codepen/upload.svg" class="upload">
                            <span class="filename" style="color: gray"></span>
                            
                                {!! csrf_field() !!}
                                <input type="file" name="file"  class="input" id="file_name">            
                        </div>
                    </div>
                    <img src="http://100dayscss.com/codepen/syncing.svg" class="syncing">
                    <img src="http://100dayscss.com/codepen/checkmark.svg" class="done">
                    <button class="upload-btn btn btn-warning" type="submit">Submit</button>
                    </form>
                     @if($errors->has('file'))
                            <p style="color:red;text-align: center;">{{$errors->first('file')}}</p>
                    @endif
              </div>
            </div>
    </div>
</div>
@endsection
@section('javascript')
    <script>
    $(document).ready(function(){
        $('input[name=file]').change(function (){
            // alert($(this).val()); 
           var fileName = $(this)[0].files[0].name;
            $('.filename').html(fileName);

          
        });
    });
    </script>
@endsection
