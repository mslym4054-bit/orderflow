<?php

use Illuminate\Database\Eloquent\Relations\HasMany;

public function orders(): HasMany
{
    return $this->hasMany(\App\Models\Order::class);
}

public function customers(): HasMany
{
    return $this->hasMany(\App\Models\Customer::class);
}
