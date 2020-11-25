<?php


if(!function_exists('fa_amount_taxable'))
{
    /**
     * Calcolate taxable value for amount
     * 
     * @param float $amount
     * 
     * @return float
     */
    function fa_amount_taxable($amount)
    {
        return round($amount / floatval("1.".WP_FA_TAXRATE), 2);
    }
}

if(!function_exists('fa_amount_taxrated'))
{
    /**
     * Calcolate taxrated value for amount
     * 
     * @param float $amount
     * 
     * @return float
     */
    function fa_amount_taxrated($amount)
    {
        return round($amount * floatval("1.".WP_FA_TAXRATE), 2);
    }
}

if(!function_exists('fa_amount_text'))
{
    /**
     * Show price formatted
     * 
     * @param float $amount     amount to show
     * @param bool  $taxable    append + taxrate string, default false
     * 
     * @return float
     */
    function fa_amount_text($amount, $taxable = false)
    {
        return number_format($amount,2,',','.'). ($taxable ? ' € + '._('IVA') : ' €');
    }
}
