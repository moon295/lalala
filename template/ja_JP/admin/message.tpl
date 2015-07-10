{if $app_ne.error_message}
                <div class="alert alert-danger">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  {$app_ne.error_message}
                </div>
{elseif $app_ne.message}
                <div class="alert alert-info">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  {$app_ne.message}
                </div>
{/if}
