<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
     * Returns the next order available for the items.
     *
     * @return integer
     */
    public function nextItemOrder(): int
    {
        return Item::query()
            ->where('deleted_at', null)
            ->max('order') + 1;
    }

    /**
     * Shifts the order of the items after the given one down.
     * 
     * @param int $order
     * @param boolean $up 
     */
    public function shiftItemsOrder(int $order): void
    {
        $this->items()
            ->where('order', '>', $order)
            ->get()
            ->each(static function (Item $item) {
                $item->order -= 1;
                $item->save();
            });
    }
}
