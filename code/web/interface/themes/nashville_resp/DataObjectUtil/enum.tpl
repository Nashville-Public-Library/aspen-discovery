<select name='{$propName}' id='{$propName}Select' {if !empty($property.accessibleLabel)}aria-label="{$property.accessibleLabel}"{/if} class="form-control"{if !empty($property.onchange)}onchange="{$property.onchange}"{/if} {if $property.readOnly}readonly{/if}>
{foreach from=$property.values item=propertyName key=propertyValue}
	<option value='{$propertyValue}'{if $propValue == $propertyValue} selected='selected'{/if}>{$propertyName}</option>
{/foreach}
</select>
