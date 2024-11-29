<?php

namespace Modules\DystoreAres\Domain\Ares\Actions;

use Carbon\Carbon;
use h4kuna\Ares\Ares;
use h4kuna\Ares\Ares\Core\Data;
use h4kuna\Ares\AresFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecord;

class GetCompanyInfoFromAres
{
    private Ares $ares;

    public function __construct()
    {
        $this->ares = (new AresFactory)->create();
    }

    /**
     * Get company info from Ares.
     */
    public function handle(string $in): AresRecord
    {
        $data = Cache::remember(
            "ares:company:{$in}",
            Carbon::now()->addWeek(),
            fn () => $this->loadBasic($in),
        );

        return AresRecord::fromData($data);
    }

    /**
     * Load basic data.
     */
    protected function loadBasic(string $in): ?Data
    {
        try {
            /** @var Data $response */
            $response = $this->ares->loadBasic($in);
        } catch (\h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException $e) {
            Log::error($e->getMessage(), ['in' => $in]);
        } catch (\h4kuna\Ares\Exceptions\AdisResponseException $e) {
            // If validation by adis failed, but data from ares returned
            /** @var Data $response */
            $response = $e->data;
            $response->adis === null; // true
        } catch (\h4kuna\Ares\Exceptions\ServerResponseException $e) {
            Log::error($e->getMessage(), ['in' => $in]);
        }

        return $response;
    }
}
