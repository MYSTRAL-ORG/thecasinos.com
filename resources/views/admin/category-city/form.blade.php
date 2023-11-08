<div class="form-group {{ $errors->has('city_title') ? 'has-error' : ''}}">
    <label for="city_title" class="control-label">{{ 'City Title' }} * </label>
    <input class="form-control" name="city_title" type="text" id="city_title" value="{{ isset($categorycity->city_title) ? $categorycity->city_title : ''}}" >
    {!! $errors->first('city_title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('city_name') ? 'has-error' : ''}}">
    <label for="city_name" class="control-label">{{ 'City Name' }}</label>
    <input class="form-control" name="city_name" type="text" id="city_name" value="{{ isset($categorycity->city_name) ? $categorycity->city_name : ''}}" >
    {!! $errors->first('city_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('header_text') ? 'has-error' : ''}}">
    <label for="header_text" class="control-label">{{ 'Header Text' }}</label>
    <textarea class="form-control" rows="5" name="header_text" type="textarea" id="header_text" >{{ isset($categorycity->header_text) ? $categorycity->header_text : ''}}</textarea>
    {!! $errors->first('header_text', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('footer_text') ? 'has-error' : ''}}">
    <label for="footer_text" class="control-label">{{ 'Footer Text' }}</label>
    <textarea class="form-control" rows="5" name="footer_text" type="textarea" id="footer_text" >{{ isset($categorycity->footer_text) ? $categorycity->footer_text : ''}}</textarea>
    {!! $errors->first('footer_text', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
