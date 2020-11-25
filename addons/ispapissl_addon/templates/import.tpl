{extends file="layout.tpl"}

{block name="content"}
    <h2>Bulk Pricing update</h2>

    <div class="tld-import-step top-margin-10">

        <form action="addonmodules.php?module=ispapissl_addon" id="frmTldImport" class="form-horizontal" method="POST">
            <div class="admin-tabs-v2">
                <div class="form-group">
                    <label for="ProductGroup" class="col-md-4 col-sm-6 control-label">
                        Product Group<br>
                        <small>Choose product group new products should be assigned to</small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <select class="form-control select-inline" name="ProductGroup" id="ProductGroup">
                            <option></option>
                            {foreach $productGroups as $name}
                                <option value="{$name}"{if $smarty.post.ProductGroup eq $name} selected{/if}>{$name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputMarginType" class="col-md-4 col-sm-6 control-label">
                        Margin Type<br>
                        <small>Choose the type of markup to apply to cost prices </small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <select id="inputMarginType" name="MarginType" class="form-control select-inline">
                            <option value="percentage"{if $smarty.post.MarginType eq 'percentage'} selected{/if}>Percentage</option>
                            <option value="fixed"{if $smarty.post.MarginType eq 'fixed'} selected{/if}>Fixed Amount</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputMarginPercent" class="col-md-4 col-sm-6 control-label">
                        Profit Margin<br>
                        <small>The markup amount to apply to cost prices</small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <div class="tld-import-percentage-margin{if $smarty.post.MarginType eq 'fixed'} hidden{/if}">
                            <div class="input-group">
                                <input id="inputMarginPercent" name="MarginPercent" type="number" class="form-control" value="{$smarty.post.MarginPercent|default:20}">
                                <span class="input-group-addon hidden-sm">%</span>
                            </div>
                        </div>
                        <div class="tld-import-fixed-margin{if $smarty.post.MarginType neq 'fixed'} hidden{/if}">
                            <div class="input-group">
                                <input id="inputMarginFixed" name="MarginFixed" type="number" class="form-control" value="{$smarty.post.MarginFixed|default:20}" step="0.01">
                                <span class="input-group-addon hidden-sm">{$currency}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputRoundingValue" class="col-md-4 col-sm-6 control-label">
                        Round to Next <br>
                        <small>Use to round prices to a common human friendly number</small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <select id="inputRoundingValue" name="Rounding" class="form-control select-inline">
                            <option value="-1"{if $smarty.post.Rounding eq ''} selected{/if}>No Rounding</option>
                            <option value="0.00"{if $smarty.post.Rounding eq '0.00'} selected{/if}>x.00</option>
                            <option value="0.09"{if $smarty.post.Rounding eq '0.09'} selected{/if}>x.09</option>
                            <option value="0.19"{if $smarty.post.Rounding eq '0.19'} selected{/if}>x.19</option>
                            <option value="0.29"{if $smarty.post.Rounding eq '0.29'} selected{/if}>x.29</option>
                            <option value="0.39"{if $smarty.post.Rounding eq '0.39'} selected{/if}>x.39</option>
                            <option value="0.49"{if $smarty.post.Rounding eq '0.49'} selected{/if}>x.49</option>
                            <option value="0.50"{if $smarty.post.Rounding eq '0.50'} selected{/if}>x.50</option>
                            <option value="0.59"{if $smarty.post.Rounding eq '0.59'} selected{/if}>x.59</option>
                            <option value="0.69"{if $smarty.post.Rounding eq '0.69'} selected{/if}>x.69</option>
                            <option value="0.79"{if $smarty.post.Rounding eq '0.79'} selected{/if}>x.79</option>
                            <option value="0.89"{if $smarty.post.Rounding eq '0.89'} selected{/if}>x.89</option>
                            <option value="0.90"{if $smarty.post.Rounding eq '0.90'} selected{/if}>x.90</option>
                            <option value="0.95"{if $smarty.post.Rounding eq '0.95'} selected{/if}>x.95</option>
                            <option value="0.99"{if $smarty.post.Rounding eq '0.99'} selected{/if}>x.99</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputRoundAllCurrencies" class="col-md-4 col-sm-6 control-label">
                        Apply rounding to all currencies<br>
                        <small>Also round the converted prices</small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-id-inputSetAutoRegistrar bootstrap-switch-animate">
                            <div class="bootstrap-switch-container">
                                <input id="inputRoundAllCurrencies" type="checkbox" name="RoundAllCurrencies" value="1" data-on-text="Yes" data-off-text="No"{if $smarty.post.RoundAllCurrencies} checked{/if}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputSetAutoSetup" class="col-md-4 col-sm-6 control-label">
                        Automatic Registration<br>
                        <small>Enable auto-register upon payment for synced products</small>
                    </label>
                    <div class="col-md-4 col-sm-6">
                        <select id="inputSetAutoSetup" class="form-control select-inline" name="AutoSetup">
                            <option value=""{if $smarty.post.AutoSetup eq ''} selected{/if}>Disabled</option>
                            <option value="order"{if $smarty.post.AutoSetup eq 'order'} selected{/if}>On Order</option>
                            <option value="payment"{if !$smarty.post or $smarty.post.AutoSetup eq 'payment'} selected{/if}>On Payment</option>
                            <option value="on"{if $smarty.post.AutoSetup eq 'on'} selected{/if}>On Accept</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            Import <span id="importCount">0</span> products
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning text-center" role="alert" style="padding: 4px 15px;">
                All prices below are shown converted to your system default currency {$currency}.
            </div>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th class="tld-check-all-th">
                        <label>
                            <input type="checkbox" class="check-all-products" name="SelectedCertificate[{$certificateClass}]">
                        </label>
                    </th>
                    <th style="width: 350px;">SSL Certificate</th>
                    <th class="tld-import-list text-center"></th>
                    <th class="tld-import-list text-center">Existing Product</th>
                    <th class="tld-import-list text-center">Reg Period</th>
                    <th class="text-center">
                        <span class="inline-block tld-pricing">
                            <span class="local-pricing">Current</span>
                            <br>
                            <span class="remote-pricing">Cost</span>
                        </span>
                        <span class="tld-margin">Margin</span>
                    </th>
                    <th class="text-center">
                        <span class="inline-block tld-pricing">
                            <span class="local-pricing">New</span>
                            <br>
                            <span class="remote-pricing">Cost</span>
                        </span>
                        <span class="tld-margin">Margin</span>
                    </th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                {foreach $products as $certificateClass => $product}
                <tr class="product-item" data-cert="{$certificateClass}" data-cost="{$product.Cost}">
                    <td>
                        <input type="checkbox" name="SelectedCertificate[{$certificateClass}]" value="1" class="toggle-switch product-checkbox" id="{$certificateClass}">
                    </td>
                    <td>
                        <label for="{$certificateClass}">{$product.Name}</label>
                    </td>
                    <td class="text-center">
                        {if $product.AutoSetup}
                            <i class="fas fa-cog text-success" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Auto-registration enabled"></i>
                        {/if}
                    </td>
                    <td class="text-center">
                        {if $product.id}
                            <i class="fas fa-check text-success"></i>
                        {/if}
                    </td>
                    <td class="text-center">1 Year</td>
                    <td class="text-center tld-pricing-td register-pricing">
                        {if $product.id}
                        <span class="tld-pricing inline-block">
                            <span class="current-pricing">{$product.Price}</span><br>
                            <span class="remote-pricing">{$product.Cost}</span>
                        </span>
                        <span class="tld-margin">
                            <span>
                                <span class="inline-block percentage-display" style="display: inline;">{$product.Margin}%</span>
                            </span>
                        </span>
                        {/if}
                    </td>
                    <td class="text-center tld-pricing-td register-pricing">
                        <span class="tld-pricing inline-block">
                            <input type="hidden" name="NewPrice[{$certificateClass}]" value="{$product.Cost}" />
                            <span class="current-pricing new-pricing" data-cert="{$certificateClass}">{$product.Cost}</span><br>
                            <span class="remote-pricing">{$product.Cost}</span>
                        </span>
                        <span class="tld-margin">
                            <span>
                                <span class="inline-block percentage-display new-margin" data-cert="{$certificateClass}" style="display: inline;">0%</span>
                            </span>
                        </span>
                    </td>
                    <td class="text-center pricing-button">
                        {if $product.id}
                        <a class="btn btn-default btn-sm" href="configproducts.php?action=edit&id={$product.id}#tab=2">
                            Pricing
                        </a>
                        {/if}
                    </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#inputRoundAllCurrencies').bootstrapSwitch();
            countProducts();
            calculatePrices();
            jQuery(document).on('click', '.check-all-products', function () {
                let checked = this.checked;
                jQuery('input.product-checkbox').each(function () {
                    jQuery(this).prop('checked', checked);
                    jQuery(this).trigger('change');
                });
            });
            jQuery(document).on('change', '.product-checkbox', function() {
                countProducts();
            });
            jQuery(document).on('change', '#inputMarginType', function () {
                jQuery('.tld-import-percentage-margin,.tld-import-fixed-margin').toggleClass('hidden');
                calculatePrices();
            });
            jQuery(document).on('change', '#inputMarginPercent', function() {
                calculatePrices();
            });
            jQuery(document).on('change', '#inputMarginFixed', function() {
                calculatePrices();
            });
            jQuery(document).on('change', '#inputRoundingValue', function() {
                calculatePrices();
            });
            function countProducts() {
                jQuery('#importCount').text(jQuery('input[type="checkbox"].product-checkbox:checked').length);
            }
            function calculatePrices() {
                let marginType = jQuery('#inputMarginType').val();
                let profit = Number(jQuery((marginType === 'fixed') ? '#inputMarginFixed' : '#inputMarginPercent').val());
                let roundTo = Number(jQuery('#inputRoundingValue').val());
                jQuery('tr.product-item').each(function () {
                    let cert = jQuery(this).data('cert');
                    let cost = Number(jQuery(this).data('cost'));
                    let newPrice;
                    if (marginType === 'fixed') {
                        newPrice = cost + profit;
                    } else {
                        newPrice = cost * (profit / 100 + 1);
                    }
                    if (roundTo >= 0) {
                        let whole = Math.floor(newPrice);
                        let fraction = newPrice - whole;
                        newPrice = whole + roundTo;
                        if (fraction > roundTo) {
                            newPrice += 1;
                        }
                    }
                    newPrice = Number(newPrice).toFixed(2);
                    let newMargin = Number((newPrice - cost) / cost * 100).toFixed(2);
                    jQuery("input[name='NewPrice["+cert+"]'").val(newPrice);
                    jQuery("span.new-pricing[data-cert='"+cert+"']").html(newPrice);
                    jQuery("span.new-margin[data-cert='"+cert+"']").html(newMargin + '%');
                });
            }
        });
    </script>
{/block}