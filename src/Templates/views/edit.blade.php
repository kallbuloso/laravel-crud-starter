@extends('[%app_template%]')

@section('[%view_section_name%]')

    <h1>Create a new [%crud_space%]</h1>
    <form method="post" action="{{ route('[%crud_dash%].update', $[%crud_var%]->id) }}" accept-charset="UTF-8" id="update_[%crud_underscore%]_form" name="update_[%crud_underscore%]_form" class="form-horizontal">
        {{ csrf_field() }}
        <input name="_method" type="hidden" value="PUT">
        @include('[%crud_underscore%].create_edit_form', ['[%crud_var%]' => $[%crud_var%]])

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <a class="btn btn-primary" href="{{ route('[%crud_dash%].index') }}">Back</a>
                <input type="submit" class="btn btn-success" value="Update [%crud_space%]" />
            </div>
        </div>
    </form>
@endsection