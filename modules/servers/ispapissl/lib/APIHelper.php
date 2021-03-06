<?php

namespace HEXONET\WHMCS\ISPAPI\SSL;

use Exception;
use WHMCS\Module\Registrar\Ispapi\Ispapi;
use HEXONET\ResponseParser as RP;

class APIHelper
{
    /**
     * Get user status
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getUserStatus(): array
    {
        $command = [
            'COMMAND' => 'StatusUser'
        ];
        return self::getResponse($command);
    }

    /**
     * Create certificate
     * @param string $certClass
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function createCertificate(string $certClass): array
    {
        $command = [
            'COMMAND' => 'CreateSSLCert',
            'ORDER' => 'CREATE',
            'SSLCERTCLASS' => $certClass,
            'PERIOD' => 1
        ];
        return self::getResponse($command);
    }

    /**
     * Replace certificate
     * @param int $orderId
     * @param string $certClass
     * @param string $csr
     * @param string $serverType
     * @param string $domain
     * @param array<string, mixed> $contact
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function replaceCertificate(int $orderId, string $certClass, string $csr, string $serverType, string $domain, array $contact): array
    {
        $command = [
            'COMMAND' => 'CreateSSLCert',
            'ORDER' => 'REPLACE',
            'SSLCERTCLASS' => $certClass,
            'PERIOD' => 1,
            'ORDERID' => $orderId,
            'CSR' => explode(PHP_EOL, $csr),
            'SERVERSOFTWARE' => $serverType,
            'SSLCERTDOMAIN' => $domain
        ];
        $command = array_merge($command, $contact);
        return self::getResponse($command);
    }

    /**
     * Update certificate
     * @param int $orderId
     * @param string $certClass
     * @param string $email
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function updateCertificate(int $orderId, string $certClass, string $email): array
    {
        $command = [
            'COMMAND' => 'CreateSSLCert',
            'ORDER' => 'UPDATE',
            'ORDERID' => $orderId,
            'SSLCERTCLASS' => $certClass,
            'PERIOD' => 1,
            'EMAIL' => $email
        ];
        return self::getResponse($command);
    }

    /**
     * Renew certificate
     * @param int $orderId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function renewCertificate(int $orderId): array
    {
        $order = self::getOrder($orderId);
        $command = [
            'COMMAND' => 'RenewSSLCert',
            'SSLCERTID' => $order['SSLCERTID']
        ];
        return self::getResponse($command);
    }

    /**
     * Reissue certificate
     * @param int $orderId
     * @param string $csr
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function reissueCertificate(int $orderId, string $csr): array
    {
        $order = self::getOrder($orderId);
        $command = [
            'COMMAND' => 'ReissueSSLCert',
            'SSLCERTID' => $order['SSLCERTID'],
            'CSR' => explode(PHP_EOL, $csr)
        ];
        return self::getResponse($command);
    }

    /**
     * Revoke certificate
     * @param int $orderId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function revokeCertificate(int $orderId): array
    {
        $order = self::getOrder($orderId);
        $command = [
            'COMMAND' => 'RevokeSSLCert',
            'SSLCERTID' => $order['SSLCERTID'],
            'REASON' => 'WHMCS'
        ];
        return self::getResponse($command);
    }

    /**
     * Get order
     * @param int $orderId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getOrder(int $orderId): array
    {
        $command = [
            'COMMAND' => 'QueryOrderList',
            'ORDERID' => $orderId
        ];
        $response = self::getResponse($command);

        $sslCertId = 0;
        if (isset($response['LASTRESPONSE'][0])) {
            $lastResponse = RP::parse(urldecode($response['LASTRESPONSE'][0]));
            $sslCertId = $lastResponse['PROPERTY']['SSLCERTID'][0];
        }
        $response['SSLCERTID'] = $sslCertId;

        $orderCommand = [];
        if (isset($response['ORDERCOMMAND'][0])) {
            $orderCommand = RP::parse(urldecode($response['ORDERCOMMAND'][0]));
        }
        $response['COMMAND'] = $orderCommand;

        $csr = [];
        $i = 0;
        while (isset($response['COMMAND']['CSR' . $i])) {
            if (strlen($response['COMMAND']['CSR' . $i])) {
                $csr[] = $response['COMMAND']['CSR' . $i];
            }
            $i++;
        }
        $response['CSR'] = implode(PHP_EOL, $csr);

        return $response;
    }

    /**
     * Execute order
     * @param int $orderId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function executeOrder(int $orderId): array
    {
        $command = [
            'COMMAND' => 'ExecuteOrder',
            'ORDERID' => $orderId
        ];
        return self::getResponse($command);
    }

    /**
     * Parse CSR
     * @param string $csr
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function parseCSR(string $csr): array
    {
        $command = [
            'COMMAND' => 'ParseSSLCertCSR',
            'CSR' => explode(PHP_EOL, $csr)
        ];
        return self::getResponse($command);
    }

    /**
     * Get certificate status
     * @param int $certId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getCertStatus(int $certId): array
    {
        $command = [
            'COMMAND' => 'StatusSSLCert',
            'SSLCERTID' => $certId
        ];
        return self::getResponse($command);
    }

    /**
     * Get e-mail addreses for certificate
     * @param int $certId
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getCertEmail(int $certId): array
    {
        $command = [
            'COMMAND' => 'QuerySSLCertDCVEmailAddressList',
            'SSLCERTID' => $certId
        ];
        return self::getResponse($command);
    }

    /**
     * Get e-mail addresses for CSR
     * @param string $certClass
     * @param string $csr
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getEmailAddress(string $certClass, string $csr): array
    {
        $command = [
            'COMMAND' => 'QuerySSLCertDCVEmailAddressList',
            'SSLCERTCLASS' => $certClass,
            'CSR' => explode(PHP_EOL, $csr)
        ];
        return self::getResponse($command);
    }

    /**
     * Get email addresses for domain
     * @param string $certClass
     * @param string $domain
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getValidationAddresses(string $certClass, string $domain): array
    {
        $command = [
            'COMMAND' => 'QuerySSLCertDCVEMailAddressList',
            'SSLCERTCLASS' => $certClass,
            'DOMAIN' => $domain
        ];
        return self::getResponse($command);
    }

    /**
     * Resend SSL activation e-mail
     * @param int $certId
     * @param string $email
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function resendEmail(int $certId, string $email): array
    {
        $command = [
            'COMMAND' => 'ResendSSLCertEmail',
            'SSLCERTID' => $certId,
            'EMAIL' => $email
        ];
        return self::getResponse($command);
    }

    /**
     * Get current exchange rates
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getExchangeRates()
    {
        $command = [
            'COMMAND' => 'QueryExchangeRates'
        ];
        return self::getResponse($command);
    }

    /**
     * Make an API call and process the response
     * @param array<string, mixed> $command
     * @return array<string, mixed>
     * @throws Exception
     */
    private static function getResponse(array $command): array
    {
        if (!class_exists('\WHMCS\Module\Registrar\Ispapi\Ispapi')) {
            throw new Exception("The ISPAPI Registrar Module is required. Please install it and activate it.");
        }
        $response = Ispapi::call($command);
        if ($response['CODE'] != 200) {
            throw new Exception($response['CODE'] . ' ' . $response['DESCRIPTION']);
        }
        return isset($response['PROPERTY']) ? $response['PROPERTY'] : [];
    }
}
