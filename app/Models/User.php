<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    protected $appends = ['referer', 'totalReferrers', 'userCategory', 'is_distributor', 'total_sales'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'purchaser_id');
    }

    public function getTotalReferrersAttribute()
    {
        return $this->hasMany(User::class, 'id', 'referred_by')->count();
    }


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRefererAttribute(){
        $user = User::find($this->referred_by);
        return $user;
        // return $this->hasOne(User::class, 'referred_by', 'id');
    }

    public function referrers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }
    

    public function userCategory(){
        return $this->hasOne(UserCategory::class, 'user_id')->with('category');
    }

    public function getUserCategoryAttribute($value) {
        return $this->category?->name;
    }

    public function getIsDistributorAttribute()
    {
        return $this->userCategory()->whereHas('category', function($query){
            $query->where('name', 'Distributor');
        })->exists();
    }

    public function getTotalSalesAttribute()
    {
        if($this->is_distributor === true){
            $allSales = Order::with('orderItems.product')->where('purchaser_id', $this->id)->get();
            $total = 0;
            foreach($allSales as $sales){
                foreach($sales->orderItems as $orderItem){
                    $total += $orderItem->product->price * $orderItem->qantity;
                }
            }

            return $total;
        }else{
            return 0;
        }

    }
}
