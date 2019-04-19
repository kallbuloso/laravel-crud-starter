@extends('[%app_template%]')

@section('[%view_section_name%]')

    <h1>{{ $[%crud_var%]->title }}</h1>
    <p><a href="{{ route('[%crud_dash%].index') }}" class="btn btn-primary">Back</a></p>

@endsection