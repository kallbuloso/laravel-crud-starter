<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
    <label for="url" class="col-md-2 control-label">Title</label>
    <div class="col-md-10">
        <input required class="form-control" name="title" type="text" id="title" value="{{ old('title', optional($[%crud_var%])->title) }}" minlength="1" placeholder="Enter title here...">
        <p class="form-text text-muted">Insert the title</p>
        {!! $errors->first('title', '<p class="invalid-feedback form-text text-muted">:message</p>') !!}
    </div>
</div>