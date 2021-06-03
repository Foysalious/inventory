<?php namespace App\Models;

use App\Http\Controllers\ProductController;
use App\Services\Product\ProductCalculator;
use App\Services\Product\ProductCombinationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = ['vat_percentage' => 'double'];

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    public function skuChannels()
    {
        return $this->hasManyThrough(SkuChannel::class,Sku::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $data = [
            'id' => $array['id'],
            'name' => $array['name'],
            'description' => $array['description'],
            'partner_id' => $array['partner_id'],
            'warranty_unit' => $array['warranty_unit']
        ];

        return $data;
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class)->select(['id','image_link']);
    }

    public function productOptions()
    {
        return $this->hasMany(ProductOption::class,'product_id');
    }

    public function productChannels()
    {
        return $this->hasMany(ProductChannel::class,'product_id');
    }

    public function unit ()
    {
        return $this->belongsTo(Unit::class,'unit_id')->select('id', 'name_bn', 'name_en');
    }

    public function getOriginalPrice($channel = 2)
    {
        /** @var  $productCalculator ProductCalculator */
        $productCalculator = app(ProductCalculator::class);
        return $productCalculator->setProduct($this)->setChannel($channel)->getOriginalPrice();
    }

    public function getDiscountedPrice($channel = 2)
    {
        /** @var  $productCalculator ProductCalculator */
        $productCalculator = app(ProductCalculator::class);
        return $productCalculator->setProduct($this)->setChannel($channel)->getDiscountedPrice();
    }


    public function getRatingandCount()
    {
        return  app(ProductCalculator::class)->getProductRatingReview($this);
    }

    public function getOriginalPriceWithVat()
    {
        $price = $this->getOriginalPrice();
        return  $price + ($price * $this->vat_percentage) / 100;
    }

    public function getDiscountedPriceWithVat()
    {
        $discounted_price = $this->getDiscountedPrice();
        return  $discounted_price + ($discounted_price * $this->vat_percentage) / 100;
    }

    public function getDiscountedAmount()
    {
        return 0;
        $amount = $this->price - $this->getDiscount();
        return ($amount < 0) ? 0 : (float)$amount;
    }

    public function getDiscount()
    {
        $discount = $this->discount();
        if ($discount->is_amount_percentage) {
            $amount = ($this->price * $discount->amount) / 100;
            if ($discount->hasCap()) {
                $amount = ($amount > $discount->cap) ? $discount->cap : $amount;
            }
        } else {
            $amount = $discount->amount;
        }

        return ($amount < 0) ? 0 : (float)$amount;
    }
    public function discount()
    {
        return $this->runningDiscounts()->first();
    }

    public function discounts()
    {
        return $this->morphMany(Discount::class);
    }

    public function runningDiscounts()
    {
        $now = Carbon::now();
        return $this->discounts->filter(function ($discount) use ($now) {
            return $discount->start_date <= $now && $discount->end_date >= $now;
        });
    }

    public function getDiscountPercentage()
    {
        return 0;
        $original_price = $this->getOriginalPrice();
        if($original_price == 0)
            return 0;
        $discount = $this->discount();
        if ($discount->is_amount_percentage)
            return $discount->amount;
        return round((($discount->amount / $original_price) * 100), 1);
    }

    public function combinations()
    {
        list($options,$combinations) = app(ProductCombinationService::class)->setProduct($this)->getCombinationData();
        return $combinations;
    }

    public function getStock(){
        $total_stock = 0;
        $combinations = $this->combinations();
        foreach ($combinations as $combination){
            $total_stock += $combination['stock'];
        }
        return $total_stock;
    }
}
