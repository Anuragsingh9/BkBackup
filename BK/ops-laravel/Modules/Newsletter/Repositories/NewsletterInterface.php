<?php

namespace Modules\Newsletter\Repositories;

use Illuminate\Http\Request;

interface NewsletterInterface
{
    public function createCommission();

    public function addNewSubscriber();

    

}