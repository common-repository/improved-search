<?php

namespace Mochabear\ImprovedSearch\Includes;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_TemplateBuilder extends MBIS_Base {

	function __construct()
	{
		parent::__construct();
	}

	public function create_section(Array $parameters)
	{
		return new MBSection($parameters);
	}
}

class MBSection {

	protected $label;
	protected $description;
	protected $fields;

	function __construct(Array $parameters)
	{
		$this->label = @$parameters['label'];
		$this->description = @$parameters['description'];
	}		

	public function add_field(Array $parameters)
	{
		$this->fields[] = new MBField($parameters);
		return $this;
	}

	private function build_fields()
	{
		$html = '';
		foreach ($this->fields as $field) {
			$html .= $field->build();
		}
		return $html;
	}

	public function build()
	{
		echo <<<EOT
<h3>{$this->label}</h3>
<p>{$this->description}</p>
<table class="form-table">
	<tbody>
		{$this->build_fields()}
	</tbody>
</table>
EOT;

		return $this;
	}

}

class MBField extends MBIS_Base {

	protected $label;
	protected $type;
	protected $option_key;
	protected $default;
	protected $choices;
	protected $description;

	function __construct(Array $parameters)
	{
		$this->label = @$parameters['label'];
		$this->name = @$parameters['name'];
		$this->type = @$parameters['type'];
		$this->settings_key = @$parameters['settings_key'];
		$this->default = @$parameters['default'];
		$this->choices = @$parameters['choices'];
		$this->description = @$parameters['description'];
		$this->multiple = @$parameters['multiple'];
		$this->settings = get_option($this->settings_option, $this->default_settings);
	}

	private function get_value()
	{
		return isset( $this->settings[$this->settings_key] ) ? $this->settings[$this->settings_key] : $this->default_settings[$this->settings_key];
	}

	public function build()
	{
		$field = '';

		switch ($this->type) {
			case 'checkbox':
				$checked = '';
				if ( @$this->settings[$this->settings_key] == "true" ) { $checked = 'checked'; }
				$field = "<input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->name}\" {$checked}/>"; 
				break;
			case 'select':
				$multiple = '';
				if ( $this->multiple == true ) { $multiple = 'multiple size="5"'; }
				$field = "<select name=\"{$this->name}\" id=\"{$this->name}\" {$multiple}>";
					foreach ($this->choices as $id => $label) {
						if ($this->multiple) {
							$selected_html = in_array( $id, @$this->settings[$this->settings_key] ) ? 'selected="selected"' : '';		
						} else {
							$selected_html = $id == @$this->settings[$this->settings_key] ? 'selected="selected"' : '';		
						}
						$field .= "<option value='{$id}' {$selected_html}>{$label}</option>";
					}
				$field .= '</select>';
				break;
			case 'text':
			case 'number':
			default:
				$field = "<input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->name}\" value=\"{$this->get_value()}\" />"; 
				break;
		}

		$tooltip = $this->description == null ? '' : <<<EOT
<div class="tooltip">?
  <span class="tooltiptext">{$this->description}</span>
</div>
EOT;

		return <<<EOT
<tr>
	<th>
		<label for="{$this->name}">
			{$this->label} {$tooltip}
		</label>

	</th>
	<td>
		<p>{$field}</p>
	</td>
</tr>
EOT;

	}

}