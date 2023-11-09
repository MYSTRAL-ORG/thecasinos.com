<div class="form-group {{ $errors->has('actif') ? 'has-error' : ''}}">
    <label for="actif" class="control-label">{{ 'Actif' }}</label>

    <input class="form-control" name="actif" type="checkbox" id="actif"   @if(isset($casinoonline->actif) && $casinoonline->actif  ==1)checked  @endif >
    {!! $errors->first('actif', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nom_casino') ? 'has-error' : ''}}">
    <label for="nom_casino" class="control-label">{{ 'Nom Casino' }}</label>
    <input class="form-control" name="nom_casino" type="text" id="nom_casino" value="{{ isset($casinoonline->nom_casino) ? $casinoonline->nom_casino : ''}}" >
    {!! $errors->first('nom_casino', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('classement') ? 'has-error' : ''}}">
    <label for="classement" class="control-label">{{ 'Position' }}</label>
    <input class="form-control" name="classement" type="text" id="classement" value="{{ isset($casinoonline->classement) ? $casinoonline->classement : ''}}" >
    {!! $errors->first('classement', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nom_casino_slug') ? 'has-error' : ''}}">
    <label for="nom_casino_slug" class="control-label">{{ 'Nom Casino Slug' }} *</label>
    <input class="form-control" name="nom_casino_slug" type="text" id="nom_casino_slug" value="{{ isset($casinoonline->nom_casino_slug) ? $casinoonline->nom_casino_slug : ''}}" >
    {!! $errors->first('nom_casino_slug', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('sous_titre') ? 'has-error' : ''}}">
    <label for="sous_titre" class="control-label">{{ 'Sous Titre' }}</label>
    <input class="form-control" name="sous_titre" type="text" id="sous_titre" value="{{ isset($casinoonline->sous_titre) ? $casinoonline->sous_titre : ''}}" >
    {!! $errors->first('sous_titre', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('key_feature') ? 'has-error' : ''}}">
    <label for="key_feature" class="control-label">{{ 'Key Feature' }} Key Feature1|Key Feature2|Key Feature3 .....</label>
    <input class="form-control" name="key_feature" type="text" id="key_feature" value="{{ isset($casinoonline->key_feature) ? $casinoonline->key_feature : ''}}" >
    {!! $errors->first('key_feature', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('note') ? 'has-error' : ''}}">
    <label for="note" class="control-label">{{ 'Note' }} * Format : 5,0 Separator  ","</label>
    <input class="form-control" name="note" type="text" id="note" value="{{ isset($casinoonline->note) ? $casinoonline->note : ''}}" >
    {!! $errors->first('note', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('screenshot') ? 'has-error' : ''}}">
    <label for="screenshot" class="control-label">{{ 'Screenshot' }}</label>
    <input class="form-control" name="screenshot" type="file" id="screenshot" value="{{ isset($casinoonline->screenshot) ? $casinoonline->screenshot : ''}}" >
    {!! $errors->first('screenshot', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('logo') ? 'has-error' : ''}}">
    <label for="logo" class="control-label">{{ 'Logo' }}</label>
    <input class="form-control" name="logo" type="file" id="logo" value="{{ isset($casinoonline->logo) ? $casinoonline->logo : ''}}" >
    {!! $errors->first('logo', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('point_pour') ? 'has-error' : ''}}">
    <label for="point_pour" class="control-label">{{ 'Pros' }}  Pros1|Pros2|Pros3 .....</label>
    <input class="form-control" name="point_pour" type="text" id="point_pour" value="{{ isset($casinoonline->point_pour) ? $casinoonline->point_pour : ''}}" >
    {!! $errors->first('point_pour', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('point_contre') ? 'has-error' : ''}}">
    <label for="point_contre" class="control-label">{{ 'Cons' }} Cons1|Cons2|Cons3 .....</label>
    <input class="form-control" name="point_contre" type="text" id="point_contre" value="{{ isset($casinoonline->point_contre) ? $casinoonline->point_contre : ''}}" >
    {!! $errors->first('point_contre', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('bonus') ? 'has-error' : ''}}">
    <label for="bonus" class="control-label">{{ 'Bonus' }}</label>
    <input class="form-control" name="bonus" type="text" id="bonus" value="{{ isset($casinoonline->bonus) ? $casinoonline->bonus : ''}}" >
    {!! $errors->first('bonus', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('sumup_description') ? 'has-error' : ''}}">
    <label for="sumup_description" class="control-label">{{ 'Sumup Description' }}</label>
    <textarea class="form-control" rows="5" name="sumup_description" type="textarea" id="sumup_description" >{{ isset($casinoonline->sumup_description) ? $casinoonline->sumup_description : ''}}</textarea>
    {!! $errors->first('sumup_description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('bonus_description') ? 'has-error' : ''}}">
    <label for="bonus_description" class="control-label">{{ 'Bonus Description' }}</label>
    <textarea class="form-control" rows="5" name="bonus_description" type="textarea" id="bonus_description" >{{ isset($casinoonline->bonus_description) ? $casinoonline->bonus_description : ''}}</textarea>
    {!! $errors->first('bonus_description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('deposit_mehods_description') ? 'has-error' : ''}}">
    <label for="deposit_mehods_description" class="control-label">{{ 'Deposit Mehods Description' }}</label>
    <textarea class="form-control" rows="5" name="deposit_mehods_description" type="textarea" id="deposit_mehods_description" >{{ isset($casinoonline->deposit_mehods_description) ? $casinoonline->deposit_mehods_description : ''}}</textarea>
    {!! $errors->first('deposit_mehods_description', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('deposit_mehods') ? 'has-error' : ''}}">
    <label for="deposit_mehods" class="control-label">{{ 'deposit mehods' }}deposit_mehods1|deposit_mehods2|deposit_mehods3 .....</label>
    <input class="form-control" name="deposit_mehods" type="text" id="deposit_mehods" value="{{ isset($casinoonline->deposit_mehods) ? $casinoonline->deposit_mehods : ''}}" >
    {!! $errors->first('deposit_mehods', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('contact_information_description') ? 'has-error' : ''}}">
    <label for="contact_information_description" class="control-label">{{ 'Contact Information Description' }}</label>
    <textarea class="form-control" rows="5" name="contact_information_description" type="textarea" id="contact_information_description" >{{ isset($casinoonline->contact_information_description) ? $casinoonline->contact_information_description : ''}}</textarea>
    {!! $errors->first('contact_information_description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('contact_information') ? 'has-error' : ''}}">
    <label for="contact_information" class="control-label">{{ 'Contact Information' }}Contact Information1|Contact Information2|Contact Information3 .....</label>
    <input class="form-control" name="contact_information" type="text" id="contact_information" value="{{ isset($casinoonline->contact_information) ? $casinoonline->contact_information : ''}}" >
    {!! $errors->first('contact_information', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('register_link') ? 'has-error' : ''}}">
    <label for="register_link" class="control-label">{{ 'Register Link' }}</label>
    <input class="form-control" name="register_link" type="text" id="register_link" value="{{ isset($casinoonline->register_link) ? $casinoonline->register_link : ''}}" >
    {!! $errors->first('register_link', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Description' }}</label>
    <textarea class="form-control" rows="5" name="description" type="textarea" id="description" >{{ isset($casinoonline->description) ? $casinoonline->description : ''}}</textarea>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('icone') ? 'has-error' : ''}}">
    <label for="icone" class="control-label">{{ 'Icone' }}</label>
    <input class="form-control" name="icone" type="file" id="icone" value="{{ isset($casinoonline->icone) ? $casinoonline->icone : ''}}" >
    {!! $errors->first('icone', '<p class="help-block">:message</p>') !!}
</div>



<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
