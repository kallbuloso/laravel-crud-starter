@extends('[%app_template%]')

@section('[%view_section_name%]')

    <h1>[%crud_title%]s</h1>
    <p><a href="{{ route('[%crud_dash%].create') }}" class="btn btn-success">[%btn_add%]Create a new [%crud_space%]</a></p>
    @if(count($[%crud_var%]s))
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Title</th>
                <th>&nbsp;</th>
             </tr>
            </thead>
            <tbody>
            @foreach($[%crud_var%]s as $[%crud_var%])
                <tr>
                    <td>{{ $[%crud_var%]->title }}</td>
                    <td>
                        <form method="post" action="{!! route('[%crud_dash%].destroy', $[%crud_var%]->id) !!}" accept-charset="UTF-8">
                            <input name="_method" value="DELETE" type="hidden">
                            {{ csrf_field() }}
                        <div class="btn-group btn-group-xs pull-right" role="group">
                        <a class="btn btn-info btn-sm" href="{{ route('[%crud_dash%].show', $[%crud_var%]->id) }}">[%btn_show%]</a>&nbsp;
                        <a class="btn btn-primary btn-sm" href="{{ route('[%crud_dash%].edit', $[%crud_var%]->id) }}">[%btn_edit%]</a>&nbsp;
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" href="{{ route('[%crud_dash%].destroy', $[%crud_var%]->id) }}">[%btn_delete%]</button>
                        </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <p>{{ $[%crud_var%]s->render() }}</p>
    @endif

@endsection

