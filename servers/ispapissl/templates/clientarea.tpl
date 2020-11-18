{if $errorMessage}
    <div class="alert alert-danger text-center">{$errorMessage}</div>
    <br />
{/if}
{if $successMessage}
    <br />
    <div class='alert alert-success text-center'>{$successMessage}</div>
{/if}

{if $sslresendcertapproveremail}
    <h2>{$LANG.sslcertapproveremail}</h2>
    <p>{$LANG.sslcertapproveremaildetails}</p>

    <form method="post" action="{$smarty.server.PHP_SELF}?action=productdetails">
        <input type="hidden" name="id" value="{$id}" />
        <label for="customApproverEmail">Please enter your approver email here:</label>
        <input type="text" name="customApproverEmail" id="customApproverEmail" />
        <br />
        or
        <p>
        {foreach from=$approverEmails item=approverEmail key=num}
            <input type="radio" class="radio-button" name="approverEmail" id="approverEmail{$num}" value="{$approverEmail}"{if $num eq 0} checked{/if} />
            <label for="approverEmail{$num}">{$approverEmail}</label>
            <br />
        {/foreach}
        </p>
        <div class="center">
            <button type="submit" class="btn btn-default">{$LANG.clientareabacklink}</button>
            <button type="submit" name="sslresendcertapproveremail" class="btn btn-primary">{$LANG.ordercontinuebutton}</button>
        </div>
    </form>
{elseif $status}
    <h2>{$LANG.sslcertinfo}</h2>

    <table cellspacing="1" cellpadding="0" class="frame">
        <tr>
            <td>
                <table width="100%" cellpadding="2">
                    <tr>
                        <td width="200" class="fieldarea">{$LANG.sslstatus}:</td>
                        <td>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><b>{$status}</b></td>
                                    {if ($status eq "Incomplete") || ($status eq "Awaiting Configuration")}
                                    <td>
                                        <form method="post" action="{$systemsslurl}configuressl.php?cert={$md5certId}">
                                        {foreach from=$config key=configdataname item=configdatavalue}
                                            <input type="hidden" name='{$configdataname}' value='{$configdatavalue}' />
                                        {/foreach}
                                            <input type="submit" value="{$LANG.sslconfsslcertificate}" />
                                        </form>
                                    </td>
                                    {/if}
                                    {if in_array($processingStatus, ['REQUESTED', 'REQUESTEDCREATE', 'REQUESTEDRENEW', 'REQUESTEDREISSUE', 'PENDING', 'PENDINGCREATE', 'PENDINGRENEW', 'PENDINGREISSUE'])}
                                    <td>
                                        <form method="post" action="{$smarty.server.PHP_SELF}?action=productdetails">
                                            <input type="hidden" name="id" value="{$id}" />
                                            <input type="submit" name="sslresendcertapproveremail" value="{$LANG.sslresendcertapproveremail}" />
                                        </form>
                                    </td>
                                    {/if}
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {if $processingStatus}
                    <tr>
                        <td class="fieldarea">{$LANG.sslprocessingstatus}:</td>
                        <td><b>{$processingStatus}{if $processingdetails} / {$processingdetails}{/if}</b></td>
                    </tr>
                    {/if}

                    {foreach from=$displayData key=name item=value}
                    <tr>
                        <td class="fieldarea">{$name}:</td>
                        <td><b>{$value}</b></td>
                    </tr>
                    {/foreach}
                </table>
            </td>
        </tr>
    </table>

    {if $crt}
    <p>{$LANG.sslcrt}:</p>
    <table cellspacing="1" cellpadding="0" class="frame">
        <tr>
            <td>
                <table width="100%" cellpadding="2">
                    <tr>
                        <td><pre>{$crt}</pre></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {/if}

    {if $cacrt}
    <p>{$LANG.sslcacrt}:</p>
    <table cellspacing="1" cellpadding="0" class="frame">
        <tr>
            <td>
                <table width="100%" cellpadding="2">
                    <tr>
                        <td><pre>{$cacrt}</pre></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {/if}

    {if $config.csr}
    <p>{$LANG.sslcsr}:</p>
    <table cellspacing="1" cellpadding="0" class="frame">
        <tr>
            <td>
                <table width="100%" cellpadding="2">
                    <tr>
                        <td><pre>{$config.csr}</pre></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {/if}
{/if}