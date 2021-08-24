<?php namespace App\Models;

use App\Events\RewardOnProductCreate;
use App\Services\Product\PriceCalculation;
use App\Services\Product\ProductCombinationService;
use Carbon\Carbon;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;



Relation::morphMap(['product'=>'App\Models\Product']);

class Product extends BaseModel
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    public static  $savedEventClass = RewardOnProductCreate::class;

    protected $guarded = ['id'];
    protected $casts = ['vat_percentage' => 'double'];
    protected $cascadeDeletes = ['skus', 'images', 'productOptions', 'productChannels', 'discounts'];

    public function setNameAttribute($name)
    {
        $this->attributes['name']=json_encode($name);
    }

    public function getNameAttribute($name)
    {
        return json_decode($name);
    }

    public function setDescriptionAttribute($description){

        $this->attributes['description']=json_encode($description);
    }

    public function getDescriptionAttribute($description){

        return html_entity_decode(json_decode($description));
    }

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

       return [
            'id' => $array['id'],
            'name' => $array['name'],
            'description' => $array['description'],
            'partner_id' => $array['partner_id'],
            'warranty_unit' => $array['warranty_unit']
        ];

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

    public function collections()
    {
        return $this->belongsToMany(Collection::class,'collection_products','product_id','collection_id')->withTimestamps();
    }

    public function collectionIds()
    {
        return $this->collections ? $this->collections->pluck('id') : [];
    }

    public function unit ()
    {
        return $this->belongsTo(Unit::class,'unit_id')->select('id', 'name_bn', 'name_en');
    }

    public function getOriginalPrice($channel = 2)
    {
        /** @var  $priceCalculation PriceCalculation */
        $priceCalculation = app(PriceCalculation::class);
        return $priceCalculation->setProduct($this)->setChannel($channel)->getWebstoreOriginalPrice();
    }

    public function getDiscountedPrice($channel = 2)
    {
        /** @var  $priceCalculation PriceCalculation */
        $priceCalculation = app(PriceCalculation::class);
        return $priceCalculation->setProduct($this)->setChannel($channel)->getWebstoreDiscountedPrice();
    }


    public function getRatingandCount()
    {
        return  app(PriceCalculation::class)->getProductRatingReview($this);
    }

    public function getOriginalPriceWithVat()
    {
        $price = $this->getOriginalPrice();
        return  $price + ($price * $this->vat_percentage) / 100;
    }

    public function getDiscountedPriceWithVat()
    {
        list($discounted_price,$discount_percentage) = $this->getDiscountedPrice();
        return  [$discounted_price + ($discounted_price * $this->vat_percentage) / 100, $discount_percentage];
    }

    public function getDiscountedAmount()
    {
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
        return $this->morphMany(Discount::class, 'type', 'type', 'type_id');
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
        $original_price = $this->getOriginalPrice();
        if($original_price == 0){
            return 0;
        }
        $discount = $this->discount();
        if ($discount->is_amount_percentage){
            return $discount->amount;
        }
        return round((($discount->amount / $original_price) * 100), 1);
    }

    public function combinations(): array
    {
        /** @var ProductCombinationService $productCombinationService */
        $productCombinationService = app(ProductCombinationService::class);
        return $productCombinationService->setProduct($this)->getCombinationData();
    }

    public function combinationsforWebstore()
    {
        list($options, $combinations) = app(ProductCombinationService::class)->setProduct($this)->getCombinationDataForWebstore();
        return $combinations;
    }

    public function stock(){
        $total_stock = 0;
        /** @var Builder $batch_repo */
        foreach ($this->skus as $sku) {
            $total_stock += $sku->batch->sum('stock');
        }
        return $total_stock;
    }

    public function logs()
    {
        return $this->hasMany(ProductUpdateLog::class);
    }
}
