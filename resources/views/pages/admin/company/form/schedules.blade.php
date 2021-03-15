<div class="row clearfix">
	<div class="col-sm-12">
		<label for="days_week">@lang('system.days_week')</label>
		<div class="form-group">
			<select class="selectpicker" name="schedules[days_week][]" id="days_week" multiple>
				@foreach(daysWeek() as $dayWeek)
					@if(!is_null($company) && !is_null($company->schedules))
						<option value="{{ $dayWeek['value'] }}" {{ $company->schedules['days_week'] && in_array($dayWeek['value'], $company->schedules['days_week'], true) ? 'selected="selected"' : '' }}>
							{{ $dayWeek['name'] }}
						</option>
					@else
						<option value="{{ $dayWeek['value'] }}">
							{{ $dayWeek['name'] }}
						</option>
					@endif
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-12">
		<label for="time_start">@lang('system.time_start')</label>
		<div class="form-group">
			<input class="form-control"
				   type="time"
				   id="time_start"
				   placeholder="@lang('system.inform_the_m', ['value' => trans('system.time_start')])"
				   value="{{ !is_null($company) && !is_null($company->schedules) ? $company->schedules['time_start'] : (old('time_start') ?: '08:00') }}"
				   name="schedules[time_start]" />
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-12">
		<label for="time_end">@lang('system.time_end')</label>
		<div class="form-group">
			<input class="form-control"
				   type="time"
				   id="time_end"
				   placeholder="@lang('system.inform_the_m', ['value' => trans('system.time_end')])"
				   value="{{ !is_null($company) && !is_null($company->schedules) ? $company->schedules['time_end'] : (old('time_end') ?: '18:00') }}"
				   name="schedules[time_end]" />
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-12">
		<label for="time_interval">@lang('system.time_interval')</label>
		<div class="form-group">
			<input class="form-control"
				   type="time"
				   id="time_interval"
				   placeholder="@lang('system.inform_the_m', ['value' => trans('system.time_interval')])"
				   value="{{ !is_null($company) && !is_null($company->schedules) ? $company->schedules['time_interval'] : (old('time_interval') ?: '01:00') }}"
				   name="schedules[time_interval]" />
		</div>
	</div>
</div>
