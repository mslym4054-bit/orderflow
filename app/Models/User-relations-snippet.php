<?php
// ضيف هاي الدوال جوّا كلاس User الموجود بـ app/Models/User.php (اللي بينشئه Breeze)
// بعد ما تضيفهم، امسح هذا الملف — هو بس للمرجع.

use Illuminate\Database\Eloquent\Relations\HasMany;

public function orders(): HasMany
{
    return $this->hasMany(\App\Models\Order::class);
}

public function customers(): HasMany
{
    return $this->hasMany(\App\Models\Customer::class);
}
