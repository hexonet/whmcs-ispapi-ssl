<?php

namespace HEXONET\WHMCS\ISPAPI\SSL;

use Illuminate\Database\Capsule\Manager as DB;

class SSLHelper
{
    public static function createEmailTemplateIfNotExisting()
    {
        $exists = DB::table('tblemailtemplates')
            ->where('name', 'SSL Certificate Configuration Required')
            ->exists();
        if (!$exists) {
            DB::table('tblemailtemplates')->insert([
                'type' => 'product',
                'name' => 'SSL Certificate Configuration Required',
                'subject' => 'SSL Certificate Configuration Required',
                'message' => '<p>Dear {\$client_name},</p><p>Thank you for your order for an SSL Certificate. Before you can use your certificate, it requires configuration which can be done at the URL below.</p><p>{\$ssl_configuration_link}</p><p>Instructions are provided throughout the process but if you experience any problems or have any questions, please open a ticket for assistance.</p><p>{\$signature}</p>'
            ]);
        }
    }

    public static function sendConfigurationEmail($serviceId, $sslOrderId)
    {
        global $CONFIG;
        $sslconfigurationlink = $CONFIG['SystemURL'] . '/configuressl.php?cert=' . md5($sslOrderId);

        $sslconfigurationlink = '<a href="' . $sslconfigurationlink . '">' . $sslconfigurationlink . '</a>';
        $postData = [
            'messagename' => 'SSL Certificate Configuration Required',
            'id' => $serviceId,
            'customvars' => base64_encode(serialize(["ssl_configuration_link" => $sslconfigurationlink])),
        ];
        localAPI('SendEmail', $postData);
    }

    public static function orderExists($serviceId)
    {
        return DB::table('tblsslorders')
            ->where('serviceid', $serviceId)
            ->where("module", "ispapissl")
            ->exists();
    }

    public static function getOrderId($serviceId)
    {
        return DB::table('tblsslorders')
            ->where('serviceid', $serviceId)
            ->where("module", "ispapissl")
            ->value('id');
    }

    public static function getOrder($serviceId, $addonId)
    {
        return DB::table('tblsslorders')
            ->where('serviceid', $serviceId)
            ->where("addon_id", $addonId)
            ->where("module", "ispapissl")
            ->select(['id', 'remoteid', 'status'])
            ->first();
    }

    public static function createOrder($userId, $serviceId, $orderId, $certClass)
    {
        return DB::table('tblsslorders')->insertGetId([
            'userid' => $userId,
            'serviceid' => $serviceId,
            'remoteid' => $orderId,
            'module' => 'ispapissl',
            'certtype' => $certClass,
            'status' => 'Awaiting Configuration'
        ]);
    }

    public static function updateOrder($serviceId, $addonId, $data)
    {
        DB::table('tblsslorders')
            ->where('serviceid', $serviceId)
            ->where("addon_id", $addonId)
            ->where("module", "ispapissl")
            ->update($data);
    }

    public static function updateHosting($serviceId, $data)
    {
        DB::table('tblhosting')
            ->where('id', $serviceId)
            ->update($data);
    }

    public static function getProductGroups()
    {
        $productGroups = DB::table('tblproductgroups')->pluck('name');
        if (empty($productGroups)) {
            DB::table('tblproductgroups')->insert(['name' => 'SSL Certificates']);
            return ['SSL Certificates'];
        }
        return $productGroups;
    }

    public static function getProductGroupId($productGroupName)
    {
        return DB::table('tblproductgroups')->where('name', $productGroupName)->value('id');
    }

    public static function getProductId($certificateClass, $gid)
    {
        return DB::table('tblproducts')
            ->where('configoption1', $certificateClass)
            ->where('gid', $gid)
            ->value('id');
    }

    public static function getProductCurrencies($productId)
    {
        return DB::table('tblpricing')
            ->where('relid', $productId)
            ->where('type', 'product')
            ->pluck('currency');
    }

    public static function createProduct($productName, $productGroupId, $certificateClass)
    {
        return DB::table('tblproducts')->insertGetId([
            'type' => 'other',
            'gid' => $productGroupId,
            'name' => $productName,
            'paytype' => 'recurring',
            'autosetup' => 'payment',
            'servertype' => 'ispapissl',
            'configoption1' => $certificateClass,
            'tax' => 1
        ]);
    }

    public static function updatePricing($productId, $currency, $price)
    {
        DB::table('tblpricing')
            ->where('relid', $productId)
            ->where('currency', $currency)
            ->update(['annually' => $price]);
    }

    public static function createPricing($productId, $currency, $price)
    {
        DB::table('tblpricing')->insert([
            'type' => 'product',
            'currency' => $currency,
            'relid' => $productId,
            'msetupfee' => 0,
            'qsetupfee' => 0,
            'ssetupfee' => 0,
            'asetupfee' => 0,
            'bsetupfee' => 0,
            'tsetupfee' => 0,
            'monthly' => -1,
            'quarterly' => -1,
            'semiannually' => -1,
            'annually' => $price,
            'biennially' => -1,
            'triennially' => -1
        ]);
    }

    public static function getProductName($certificateClass)
    {
        $certificateNames = [
            'COMODO_ESSENTIALSSL' => 'Sectigo Essential SSL',
            'COMODO_ESSENTIALSSL_WILDCARD' => 'Sectigo Essential SSL Wildcard',
            'COMODO_SSL_EV' => 'Sectigo EV SSL',
            'COMODO_INSTANTSSL' => 'Sectigo Instant SSL',
            'COMODO_INSTANTSSL_PREMIUM' => 'Sectigo Instant SSL Premium',
            'COMODO_POSITIVESSL' => 'Sectigo Positive SSL',
            'COMODO_PREMIUMSSL_WILDCARD' => 'Sectigo Premium SSL Wildcard',
            'COMODO_SSL' => 'Sectigo DV SSL',
            'COMODO_SSL_WILDCARD' => 'Sectigo DV SSL Wildcard',
            'GEOTRUST_QUICKSSL' => 'GeoTrust Quick SSL',
            'GEOTRUST_QUICKSSLPREMIUM' => 'GeoTrust Quick SSL Premium',
            'GEOTRUST_QUICKSSLPREMIUM_SAN' => 'GeoTrust Quick SSL Premium SAN Package',
            'GEOTRUST_RAPIDSSL' => 'GeoTrust Rapid SSL',
            'GEOTRUST_RAPIDSSL_WILDCARD' => 'GeoTrust Rapid SSL Wildcard',
            'GEOTRUST_TRUEBIZID' => 'GeoTrust True Business ID',
            'GEOTRUST_TRUEBIZID_SAN' => 'GeoTrust True Business ID SAN Package',
            'GEOTRUST_TRUEBIZID_EV' => 'GeoTrust True Business ID EV',
            'GEOTRUST_TRUEBIZID_EV_SAN' => 'GeoTrust True Business ID EV SAN Package',
            'GEOTRUST_TRUEBIZID_WILDCARD' => 'GeoTrust True Business ID Wildcard',
            'SYMANTEC_SECURESITE' => 'Symantec Secure Site',
            'SYMANTEC_SECURESITE_EV' => 'Symantec Secure Site EV',
            'SYMANTEC_SECURESITE_PRO' => 'Symantec Secure Site Pro',
            'SYMANTEC_SECURESITE_PRO_EV' => 'Symantec Secure Site Pro EV',
            'THAWTE_SSL123' => 'thawte SSL 123',
            'THAWTE_SSLWEBSERVER' => 'thawte SSL Webserver',
            'THAWTE_SSLWEBSERVER_EV' => 'thawte SSL Webserver EV',
            'THAWTE_SSLWEBSERVER_WILDCARD' => 'thawte SSL Webserver Wildcard',
            'TRUSTWAVE_DOMAINVETTEDSSL' => 'Trustwave Domain vetted SSL',
            'TRUSTWAVE_PREMIUMSSL' => 'Trustwave Premium SSL',
            'TRUSTWAVE_PREMIUMSSL_SAN' => 'Trustwave Premium SSL SAN Package',
            'TRUSTWAVE_PREMIUMSSL_EV' => 'Trustwave Premium SSL EV',
            'TRUSTWAVE_PREMIUMSSL_EV_SAN' => 'Trustwave Premium SSL EV SAN Package',
            'TRUSTWAVE_PREMIUMSSL_WILDCARD' => 'Trustwave Premium SSL Wildcard'
        ];
        if (isset($certificateNames[$certificateClass])) {
            return $certificateNames[$certificateClass];
        }

        $certificateName = str_replace('_', ' ', strtolower($certificateClass));
        $certificateName = str_replace('ssl', 'SSL', $certificateName);
        $certificateName = str_replace(' ev', ' EV', $certificateName);
        $certificateName = str_replace(' san', ' SAN', $certificateName);
        $certificateName = str_replace('domainvetted', 'Domain-Vetted ', $certificateName);
        return ucwords($certificateName);
    }

    public static function formatArrayKeys(&$array)
    {
        $array = array_change_key_case($array, CASE_LOWER);
        $array = array_combine(array_map(function ($str) {
            $str = str_replace('_', ' ', $str);
            $str = str_replace('ssl', 'SSL', $str);
            $str = str_replace(' ev', ' EV', $str);
            $str = str_replace(' san', ' SAN', $str);
            $str = str_replace('truebizid', 'True BusinessID', $str);
            $str = str_replace('securesite', 'Secure Site', $str);
            $str = str_replace('domainvetted', 'Domain-Vetted ', $str);
            return ucwords($str);
        }, array_keys($array)), array_values($array));
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                self::formatArrayKeys($array[$key]);
            }
        }
    }

    public static function calculateProfitMargin($products, $profitMargin)
    {
        foreach ($products as $certificateClass => $product) {
            $newPrice = $product['NewPrice'] + ($profitMargin / 100) * $product['NewPrice'];
            $products[$certificateClass]['NewPrice'] = number_format((float)$newPrice, 2, '.', '');
        }
        return $products;
    }

    public static function importProducts()
    {
        $currencies = self::getCurrencies();
        $productGroupId = self::getProductGroupId($_POST['SelectedProductGroup']);

        foreach ($_POST['SelectedCertificate'] as $certificateClass => $val) {
            $productName = self::getProductName($certificateClass);
            $productId = self::getProductId($certificateClass, $productGroupId);
            if (!$productId) {
                $productId = self::createProduct($productName, $productGroupId, $certificateClass);
            }

            $productPrices = [];
            foreach ($currencies as $currency) {
                $productPrices[$currency['id']] = round($_POST['SalePrice'][$certificateClass] * $currency['rate'], 2);
            }

            $productCurrencies = self::getProductCurrencies($productId);
            foreach ($productCurrencies as $currencyId) {
                if (array_search($currencyId, array_column($currencies, 'id')) === false) {
                    continue; // this should not happen, but just to be sure...
                }
                self::updatePricing($productId, $currencyId, $productPrices[$currencyId]);
            }
            foreach ($currencies as $currency) {
                if (in_array($currency['id'], $productCurrencies->toArray())) {
                    continue;
                }
                self::createPricing($productId, $currency['id'], $productPrices[$currency['id']]);
            }
        }
    }

    public static function getCurrencies()
    {
        $currencies = localAPI('GetCurrencies', []);
        if ($currencies['result'] == 'success') {
            return $currencies['currencies']['currency'];
        }
        return [];
    }

    public static function getDefaultCurrency()
    {
        return DB::table('tblcurrencies')
            ->where('default', 1)
            ->value('code');
    }

    public static function getLogo()
    {
        $data = file_get_contents(__DIR__ . '/../../../addons/ispapissl_addon/logo.png');
        return 'data:image/png;base64,' . base64_encode($data);
    }
}
