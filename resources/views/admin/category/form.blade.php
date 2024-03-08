<div class="form-group {{ $errors->has('') ? 'has-error' : ''}}">
    <label for="country_title" class="control-label">{{ 'Country Title' }} *</label>
    <input class="form-control" name="country_title" type="text" id="country_title" value="{{ isset($category->country_title) ? $category->country_title : ''}}" >
    {!! $errors->first('country_title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('country_name') ? 'has-error' : ''}}">
    <label for="country_name" class="control-label">{{ 'Country Name' }}</label>
    <input class="form-control" name="country_name" type="text" id="country_name" value="{{ isset($category->country_name) ? $category->country_name : ''}}" >
    {!! $errors->first('country_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('header_text') ? 'has-error' : ''}}">
    <label for="header_text" class="control-label">{{ 'Header Text' }}</label>
    <textarea class="form-control" name="header_text"   id="header_text"   >{{ isset($category->header_text) ? $category->header_text : ''}}</textarea>
    {!! $errors->first('header_text', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('footer_text') ? 'has-error' : ''}}">
    <label for="footer_text" class="control-label">{{ 'Footer Text' }}</label>
    <textarea class="form-control" name="footer_text" type="text" id="footer_text"  >{{ isset($category->footer_text) ? $category->footer_text : ''}}</textarea>
    {!! $errors->first('footer_text', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
