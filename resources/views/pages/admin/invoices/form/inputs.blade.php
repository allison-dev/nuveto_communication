<div class="container col-sm-12" style="display: flex">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <label for="ini_date">@lang('system.ini_date')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('ini_date') ? 'is-invalid' : '' }}" type="date" id="ini_date" placeholder="@lang('system.inform_the_m', ['value' => trans('system.ini_date')])" value="" name="ini_date" required />
            @if ($errors->has('ini_date'))
                <label id="ini_date-error" class="error" for="ini_date"><strong>{{ $errors->first('ini_date') }}</strong></label>
            @endif
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <label for="end_date">@lang('system.end_date')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="date" id="end_date" placeholder="@lang('system.inform_the_m', ['value' => trans('system.end_date')])" value="" name="end_date" required />
            @if ($errors->has('end_date'))
                <label id="end_date-error" class="error" for="end_date"><strong>{{ $errors->first('end_date') }}</strong></label>
            @endif
        </div>
    </div>
</div>
