@extends('v1.site.layout',[
   'css'=>[],
   'js'=>[]
])

@section('content')

    @if(Session::has('errors'))
        {{Session::get('errors')->first()}}
    @endif


@endsection

@section('script')
    <script>

    </script>
@endsection
