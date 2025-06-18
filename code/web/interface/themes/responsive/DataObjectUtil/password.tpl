<input type='password' name='{$propName}' id='{$propName}'
    {if !empty($propValue) && $property.type != 'storedPassword'} value='{$propValue|escape}'{/if}
    {if !empty($property.minLength)} minlength='{$property.minLength}'{/if}
    {if !empty($property.maxLength)} maxlength='{$property.maxLength}'{/if}
    {if !empty($property.size)} size='{$property.size}'{/if}
    class='form-control{if !empty($property.required)} required{/if}{if !empty($property.onlyDigitsAllowed)} digits{/if}'
    {if !empty($property.readOnly)} readonly{/if}
    {if $property.type == 'storedPassword' && !empty($propValue)} placeholder="{translate text='Use previously saved value' inAttribute=true isAdminFacing=true}"{/if}
    {if !empty($property.autocomplete)} autocomplete="{$property.autocomplete}"{/if}
/>

{if !empty($property.note)}
    <span id="{$propName}HelpBlock" class="help-block" style="margin-top:0">
        <small><i class="fas fa-info-circle"></i> {$property.note}</small>
    </span>
{/if}

{if !isset($property.showConfirm) || $property.showConfirm == true}
    <div>{translate text="Confirm %1%" 1=$property.label translateParameters=true isAdminFacing=true}</div>
    <input type='password' name='{$propName}Repeat' id='{$propName}Repeat'
        {if !empty($propValue) && $property.type != 'storedPassword'} value='{$propValue|escape}'{/if}
        {if !empty($property.minLength)} minlength='{$property.minLength}'{/if}
        {if !empty($property.maxLength)} maxlength='{$property.maxLength}'{/if}
        {if !empty($property.size)} size='{$property.size}'{/if}
        class='form-control repeat {if !empty($property.onlyDigitsAllowed)} digits{/if}'
        {if !empty($property.readOnly)} readonly{/if}
        {if !empty($property.autocomplete)} autocomplete="{$property.autocomplete}"{/if}
    />
{/if}