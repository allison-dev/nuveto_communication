<div class="row clearfix">
	<div class="card-body">
		<label for="doctor">@lang('system.doctor')</label>
		<div class="form-group">
			@php
				$doctor = '';
				if (!is_null($scheduling) && !is_null($scheduling->doctor)) {
					$doctor = $scheduling->doctor->name;
				} elseif (old('doctor')) {
					$doctor = old('doctor');
				}
			@endphp
			<input type="text" class="form-control {{ $errors->has('doctor') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_m', ['value' => trans('system.doctor')])" id="doctor" value="{{ $doctor }}" name="doctor" />
			<input type="hidden" name="doctor_id" value="{{ !is_null($scheduling) ? $scheduling->doctor_id : old('doctor_id') }}">
			@if ($errors->has('doctor_id'))
				<label id="doctor-error" class="error" for="doctor"><strong>{{ $errors->first('doctor_id') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="card-body">
		<label for="patient">@lang('system.patient')</label>
		<div class="form-group">
			@php
				$patient = '';
				if (!is_null($scheduling) && !is_null($scheduling->patient)) {
					$patient = $scheduling->patient->name;
				} elseif (old('patient')) {
					$patient = old('patient');
				}
			@endphp
			<input type="text" class="form-control {{ $errors->has('patient') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_m', ['value' => trans('system.patient')])" id="patient" value="{{ $patient }}" name="patient" />
			<input type="hidden" name="patient_id" value="{{ !is_null($scheduling) ? $scheduling->patient_id : old('patient_id') }}">
			@if ($errors->has('patient_id'))
				<label id="patient-error" class="error" for="patient"><strong>{{ $errors->first('patient_id') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="card-body">
		<label for="schedule">@lang('system.date_schedule')</label>
		<div class="form-group">
			<input class="form-control {{ $errors->has('schedule') ? 'is-invalid' : '' }}" type="datetime-local" id="schedule" placeholder="@lang('system.inform_the_f', ['value' => trans('system.date_schedule')])" value="{{ !is_null($scheduling) ? date('Y-m-d\TH:i', strtotime($scheduling->schedule)) : (old('schedule') ?: date('Y-m-d\TH:i')) }}" name="schedule" />
			@if ($errors->has('schedule'))
				<label id="schedule-error" class="error" for="schedule"><strong>{{ $errors->first('schedule') }}</strong></label>
			@endif
		</div>
	</div>
</div>