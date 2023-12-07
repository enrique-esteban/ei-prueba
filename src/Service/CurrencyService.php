<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

use App\Model\CurrencyModel;

use function PHPUnit\Framework\isNull;

class CurrencyService
{
    private const CURRENCIES_LIST_URL = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies.min.json";
    private const CURRENCY_EXCHANGE_VALUE_URL = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/[FROM]/[TO].json";

    /**
     * construct
     *
     * @param HttpClientInterface $client
     */
    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * Gets a currency names list and returns them as an object
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        $cache = new FilesystemAdapter();

        $cache->delete('currencies_list_cache');

        $currencies = $cache->get('currencies_list_cache', function (ItemInterface $item): array {
            $item->expiresAfter(86400); // refreshes every day

            $resp = $this->client->request('GET', self::CURRENCIES_LIST_URL);

            if ($resp->getStatusCode() != "200") {
            }

            return $this->parsedCurriencies($resp->getContent());
        });

        if (is_null($currencies)) {
            return [
                'error_code' => 1,
            ];
        }

        return [
            'list' => $currencies,
            'error_code' => 0,
        ];
    }

    /**
     * Get the conversion ratio of two currencies
     *
     * @param string $fromCoin
     * @param string $toCoin
     * @param string $ammount
     * @return array
     */
    public function getCurrencyRate($fromCoin, $toCoin, $ammount): array
    {

        $cache = new FilesystemAdapter();
        $cacheName = 'currency_data_' . $fromCoin . '_' . $toCoin . '_cache';

        // $cache->delete($cacheName);

        $exchangeRatio = $cache->get($cacheName, function (ItemInterface $item) use ($fromCoin, $toCoin) {
            $item->expiresAfter(3600); // refreshes every hour

            $url = str_replace("[FROM]", $fromCoin, self::CURRENCY_EXCHANGE_VALUE_URL);
            $url = str_replace("[TO]", $toCoin, $url);

            $resp = $this->client->request('GET', $url);
            $exchangeData = json_decode($resp->getContent(), true);

            return $exchangeData[$toCoin];
        });


        if ((float) $exchangeRatio <= 0) {
            return [
                'price' => 0,
                'error_code' => 1,
            ];
        }

        $exchangePrice = (float) $ammount * (float) $exchangeRatio;

        return [
            'price' => $exchangePrice,
            'error_code' => 0,
        ];
    }

    /**
     * Get a JSON string of currencies, decode it and returns an array of currencyModel objects with the parsed data
     *
     * @param string $data
     * @return array
     */
    private function parsedCurriencies($data): array
    {
        $currenciesRaw = json_decode($data);

        foreach ($currenciesRaw as $slug => $name) {
            $currency = new CurrencyModel;

            $currency->setName($name);
            $currency->setSlug($slug);

            $currencies[] = $currency;
        }

        return $currencies;
    }
}
