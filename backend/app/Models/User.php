<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Get the items for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Shifts the order of the items after the given one down.
     * 
     * @param int $old_order
     * @param int $old_order
     * @param string $type
     * @param boolean $up 
     */
    public function shiftItemsOrder(int $old_order, int $new_order = 0, string $type = 'asc'): void
    {
        if ($type == 'desc') {
            $this->items()
                ->where('order', '<=', $new_order)
                ->where('order', '>=', $old_order)
                ->update(['order' => DB::raw('`order` - 1')]);
        } else {
            $this->items()
                ->where('order', '<=', $old_order)
                ->where('order', '>=', $new_order)
                ->update(['order' => DB::raw('`order` + 1')]);
        }
    }
}
