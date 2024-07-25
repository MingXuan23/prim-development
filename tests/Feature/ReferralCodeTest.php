<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Http\Controllers\DirectPayController;

use App\Models\Donation;
use App\Models\Organization;
use App\Models\Transaction;


class ReferralCodeTest extends TestCase
{
    
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $donation = new Donation();
        $user = new User();
        $organization = new Organization();
        $transaction = new Transaction();
        $this->controller =  new \App\Http\Controllers\DirectPayController($donation, $user, $organization, $transaction);
    }

    public function testGetReferralCodeFromSource()
    {
        // Test case 1: Request referral code
        Auth::shouldReceive('id')->andReturn(17151);
        $result = $this->controller->getReferralCodeFromSource('REQUEST_CODE');
        $this->assertEquals(['code' => 'REQUEST_CODE', 'source' => 'request'], $result);

        // Test case 2: Session referral code
        Session::put('referral_code', 'SESSION_CODE');
        $result = $this->controller->getReferralCodeFromSource(null);
        $this->assertEquals(['code' => 'SESSION_CODE', 'source' => 'session'], $result);

        // Test case 3: Own referral code
       
        // DB::shouldReceive('table')->with('referral_code')->andReturnSelf()
        //     ->shouldReceive('where')->with('user_id', 17151)->andReturnSelf()
        //     ->shouldReceive('first')->andReturn((object)['code' => 'OWN_CODE']);
        // DB::shouldReceive('table')->with('referral_code_member as rcm')->andReturnSelf()
        //     ->shouldReceive('join')->andReturnSelf()
        //     ->shouldReceive('where')->andReturnSelf()
        //     ->shouldReceive('select')->andReturnSelf()
        //     ->shouldReceive('first')->andReturn(null);
        
        // $result = $this->controller->getReferralCodeFromSource(null);
        // $this->assertEquals(['code' => 'OWN_CODE', 'source' => 'own'], $result);

        // Test case 4: Leader referral code
        // Auth::shouldReceive('id')->andReturn(22086);
        // DB::shouldReceive('table')->with('referral_code')->andReturnSelf()
        //     ->shouldReceive('where')->with('user_id', 22086)->andReturnSelf()
        //     ->shouldReceive('first')->andReturn((object)['code' => 'OWN_CODE']);
        // DB::shouldReceive('table')->with('referral_code_member as rcm')->andReturnSelf()
        //     ->shouldReceive('join')->andReturnSelf()
        //     ->shouldReceive('where')->andReturnSelf()
        //     ->shouldReceive('select')->andReturnSelf()
        //     ->shouldReceive('first')->andReturn((object)['code' => 'LEADER_CODE']);
        
        // $result = $this->controller->getReferralCodeFromSource(null);
        // $this->assertEquals(['code' => 'LEADER_CODE', 'source' => 'leader'], $result);

       
    }
}
   

