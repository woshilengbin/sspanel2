<?php

namespace App\Models;

class Wxpay extends Model
{
    protected $connection = "default";
    protected $table = "wxpay";

    public function renew_date()
    {
        return date("Y-m-d H:i:s", $this->attributes['renew']);
    }

    public function datetime()
    {
        return date("Y-m-d H:i:s", $this->attributes['datetime']);
    }
    
    public function user()
    {
        $user = User::where("id", $this->attributes['userid'])->first();
        if ($user == null) {
            Bought::where('id', '=', $this->attributes['id'])->delete();
            return null;
        } else {
            return $user;
        }
    }
    
    public function shop()
    {
        return Shop::where("id", $this->attributes['shopid'])->first();
    }
}
