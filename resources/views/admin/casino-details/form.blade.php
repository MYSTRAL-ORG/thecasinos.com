
<div class="form-group ">
    <label for="id_casino" class="control-label">{{ 'Casino Name' }}</label>
    <div    id="id_casino"></div>

</div>
<div class="form-group {{ $errors->has('actif') ? 'has-error' : ''}}">
    <label for="actif" class="control-label">{{ 'Actif' }}</label>

    <input class="form-control" name="actif" type="checkbox" id="actif"   @if(isset($casinodetail->actif) && $casinodetail->actif  ==1)checked  @endif >
    {!! $errors->first('actif', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('id_casino') ? 'has-error' : ''}}">
    <label for="id_casino" class="control-label">{{ 'Id Casino' }}</label>
    <input class="form-control" name="id_casino" type="text" id="id_casino" value="{{ isset($casinodetail->id_casino) ? $casinodetail->id_casino : ''}}" >
    {!! $errors->first('id_casino', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Title' }}</label>
    <input class="form-control" name="title" type="text" id="title" value="{{ isset($casinodetail->title) ? $casinodetail->title : ''}}" >
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($casinodetail->description) ? $casinodetail->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('sumup') ? 'has-error' : ''}}">
    <label for="sumup" class="control-label">{{ 'Sumup' }}</label>
    <textarea class="form-control" rows="5" name="sumup" type="textarea" id="sumup" >{{ isset($casinodetail->sumup) ? $casinodetail->sumup : ''}}</textarea>
    {!! $errors->first('sumup', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('games') ? 'has-error' : ''}}">
    <label for="games" class="control-label">{{ 'Games' }}</label>
    <textarea class="form-control" rows="5" name="games" type="textarea" id="games" >{{ isset($casinodetail->games) ? $casinodetail->games : ''}}</textarea>
    {!! $errors->first('games', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('fun_facts') ? 'has-error' : ''}}">
    <label for="fun_facts" class="control-label">{{ 'Fun Facts' }}</label>
    <textarea class="form-control" rows="5" name="fun_facts" type="textarea" id="fun_facts" >{{ isset($casinodetail->fun_facts) ? $casinodetail->fun_facts : ''}}</textarea>
    {!! $errors->first('fun_facts', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('resume_1_line') ? 'has-error' : ''}}">
    <label for="resume_1_line" class="control-label">{{ 'Resume 1 Line' }}</label>
    <input class="form-control" name="resume_1_line" type="text" id="resume_1_line" value="{{ isset($casinodetail->resume_1_line) ? $casinodetail->resume_1_line : ''}}" >
    {!! $errors->first('resume_1_line', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('resume_2_words') ? 'has-error' : ''}}">
    <label for="resume_2_words" class="control-label">{{ 'Resume 2 Words' }}</label>
    <input class="form-control" name="resume_2_words" type="text" id="resume_2_words" value="{{ isset($casinodetail->resume_2_words) ? $casinodetail->resume_2_words : ''}}" >
    {!! $errors->first('resume_2_words', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('seo_title') ? 'has-error' : ''}}">
    <label for="seo_title" class="control-label">{{ 'Seo Title' }}</label>
    <input class="form-control" name="seo_title" type="text" id="seo_title" value="{{ isset($casinodetail->seo_title) ? $casinodetail->seo_title : ''}}" >
    {!! $errors->first('seo_title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('seo_description') ? 'has-error' : ''}}">
    <label for="seo_description" class="control-label">{{ 'Seo Description' }}</label>
    <input class="form-control" name="seo_description" type="text" id="seo_description" value="{{ isset($casinodetail->seo_description) ? $casinodetail->seo_description : ''}}" >
    {!! $errors->first('seo_description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('seo_keywords') ? 'has-error' : ''}}">
    <label for="seo_keywords" class="control-label">{{ 'Seo Keywords' }}</label>
    <input class="form-control" name="seo_keywords" type="text" id="seo_keywords" value="{{ isset($casinodetail->seo_keywords) ? $casinodetail->seo_keywords : ''}}" >
    {!! $errors->first('seo_keywords', '<p class="help-block">:message</p>') !!}
</div>



<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
