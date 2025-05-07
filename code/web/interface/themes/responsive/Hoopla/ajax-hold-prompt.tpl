<div>
    {if count($hooplaUsers) > 1}
        <div class="alert alert-info">
            {translate text="There are currently %1% people waiting for this title. " isPublicFacing=true 1=$holdQueueSize}
        </div>
        <div class="form-group">
            <label class="control-label" for="patronId">{translate text="Place hold for account" isPublicFacing=true}</label>
            <div class="controls">
                <select name="patronId" id="patronId" class="form-control">
                    {foreach from=$hooplaUsers item=tmpUser}
                        <option value="{$tmpUser->id}">{$tmpUser->getNameAndLibraryLabel()|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {else if count($hooplaUsers) == 1}
       <div class="alert alert-info">
            {translate text="There are currently %1% people waiting for this title. Would you like to place a hold?" isPublicFacing=true 1=$holdQueueSize}
        </div>
        <br>
        <input type="hidden" id="patronId" value="{$singleUser->id}">
        <div class="form-group">
            <label for="stopHooplaHoldConfirmation" class="checkbox">
                <input type="checkbox" name="stopHooplaHoldConfirmation" id="stopHooplaHoldConfirmation"> 
                {translate text="Don't ask again. (This can be changed under your Account Settings)" isPublicFacing=true}
            </label>
        </div>
    {/if}
</div>
