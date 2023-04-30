<?php

namespace App\Requests;

use App\Requests\MyBaseRequest;

class AdvertRequest extends MyBaseRequest {
    public function validateBeforeSave(string $ruleGroup, bool $respondWithRedirect = false)
    {
        $this->validate($ruleGroup, $respondWithRedirect);
    }
}
