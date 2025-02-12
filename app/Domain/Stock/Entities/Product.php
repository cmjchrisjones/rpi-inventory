<?php

namespace App\Domain\Stock\Entities;

use App\Domain\Recipe\Entities\RecipeProduct;
use App\Domain\Cart\Entities\ProductLine;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Product extends Eloquent
{
    const STATUS_SUCCESS = 'alert-success';
    const STATUS_WARNING = 'alert-warning';
    const STATUS_DANGER = 'alert-danger';

    public function __toString()
    {
        return $this->name;
    }

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'quantity',
        'min_quantity',
        'unit'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class)
            ->orderBy('created_at', 'DESC');
    }

    public function productLine()
    {
        return $this->hasMany(ProductLine::class);
    }

    public function recipes()
    {
        return $this->hasMany(RecipeProduct::class);
    }

    public function scopeWithoutIds($query, $ids = [])
    {
        if (is_array($ids) && !empty($ids)) {
            $query->whereNotIn('id', $ids);
        }
        return $query;
    }

    public static function getList($withoutId = null, $emptyLine = true)
    {
        $return = [];
        if ($emptyLine) {
            $return['-1'] = '---';
        }

        static::without($withoutId)->get()->map(function ($item) use (&$return) {
            $return[$item->id] = $item->name;
        });

        return $return;
    }

    public function getStatus()
    {
        if ($this->min_quantity == 0 || $this->quantity > $this->min_quantity) {
            return self::STATUS_SUCCESS;
        } elseif ($this->quantity == $this->min_quantity) {
            return self::STATUS_WARNING;
        }
        return self::STATUS_DANGER;
    }

    /**
     * récup des produits avec stock > stock mini hors produit dans la liste
     *
     * @param array $withoutIds
     * @return array
     */
    public static function getOutOfStockProducts($withoutIds = [])
    {
        return self::withoutIds($withoutIds)
                ->where('min_quantity', '>', 0)
                ->whereRaw('products.quantity <= products.min_quantity')
                ->get();
    }

    public function getUniteList()
    {
        return [
            '' 			=> '---',
            'grammes' 	=> 'Grammes',
            'litre' 	=> 'Litre',
            'sachet' 	=> 'Sachet'
        ];
    }

    public function toArray()
    {
        $datas = parent::toArray();

        $datas['status'] = $this->getStatus();

        return $datas;
    }
}
