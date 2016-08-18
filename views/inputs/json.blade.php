<fieldset class="form-group field_{{ $field_name }} {{ $field_name }} {{ $field_class }} text-field" >
	<label for="field_{{ $field_name }}">{{ $field_label ? $field_label : $field_name }}</label>
	<div id="json_{{ $field_name }}" style="width: 100%; height: 400px;"></div>
	<input name="{{ $prefixed_field_name }}" id="field_{{ $field_name }}" type="hidden" value="{{ json_decode($field_value) ? $field_value : '{}' }}">
</fieldset>

<script>
	// create the editor
	var container = $("#json_{{ $field_name }}");
	var json_field = $("#field_{{ $field_name }}");
	var json = json_field.val();
	var options = {};
	var editor = new JSONEditor(container[0], options);
	editor.setText(json);

	json_field.closest('form').on('submit', function(e) {
		json_field.val(editor.getText());
	})
</script>