<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Model\CurrencyModel;

class CurrencyService
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * Gets a currency names list and returns them as an object
     *
     * @return \stdClass
     */
    public function getCurrencies(): array
    {
        $res = $this->client->request('GET', "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies.min.json");

        return $this->parsedCurriencies($res->getContent());
    }

    /**
     * Get a JSON string of currencies, decode it and returns an array of currencyModel objects with the parsed data
     *
     * @param [type] $data
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
