<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;

uses(TestCase::class, RefreshDatabase::class)
    ->beforeEach(function () {
        Redis::flushall();
    })
    ->in('Feature');

uses(TestCase::class)->in('Unit');
