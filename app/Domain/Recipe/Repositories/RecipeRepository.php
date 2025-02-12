<?php

namespace App\Domain\Recipe\Repositories;

use App\Domain\Recipe\Entities\RecipeProduct;
use App\Domain\Recipe\Entities\RecipeStep;
use App\Infrastructure\Contracts\BaseRepository;
use App\Domain\Recipe\Entities\Recipe as Recipe;
use \App\Helpers\ParseHelper;
use App\Domain\Recipe\Contracts\RecipeRepositoryInterface;

class RecipeRepository extends BaseRepository implements RecipeRepositoryInterface
{
    protected $with = ['products', 'steps'];

    /**
	 * Return repository entity model used
	 *
	 * @return string
	 */
	public function model(): string
    {
        return Recipe::class;
    }

    /**
     * Initialize new Eloquent model
     *
     * @return Recipe
     */
    public function initialize(): Recipe
    {
        return new $this->model();
    }

    // refactoring
    public function syncProducts(Recipe $recipe, array $productsList = [])
    {
        $recipe->products->map(function ($recipeProduct) use (&$productsList) {
            $found = array_filter($productsList, function ($product) use ($recipeProduct) {
                return $product['product_id'] == $recipeProduct->product_id;
            });

            // delete if product not in
            if (empty($found)) {
                $recipeProduct->delete();
                return true;
            }

            // update recette_produit row
            $recipeProduct->fill(reset($found));
            $recipeProduct->save();

            // unset all the postDatas produit where $key is $recette_produit product ID
            $productsList = array_filter($productsList, function ($product) use ($recipeProduct) {
                return $product['product_id'] != $recipeProduct->product_id;
            });
        });

        // after that, create the other recette_produit relation if it needed
        $listToAdd = [];
        foreach ($productsList as $product) {
            if ($product['product_id'] && $product['quantity']) {
                $listToAdd[] = new RecipeProduct($product);
            }
        };

        // and finally, save
        if (is_array($listToAdd) && !empty($listToAdd)) {
            $recipe->products()->saveMany($listToAdd);
        }

        $recipe->load($this->with);

        return $recipe;
    }

    // refactoring
    public function syncSteps(Recipe $recipe, array $stepsList = [])
    {
        $recipe->steps->map(function ($recipeStep) use (&$stepsList) {
            $found = array_filter($stepsList, function ($step) use ($recipeStep) {
                return $step['id'] == $recipeStep->id;
            });

            // delete if product not in
            if (empty($found)) {
                $recipeStep->delete();
                return true;
            }

            // update recette_produit row
            $recipeStep->fill(reset($found));
            $recipeStep->save();

            // unset all the postDatas produit where $key is $recette_produit product ID
            $stepsList = array_filter($stepsList, function ($step) use ($recipeStep) {
                return $step['id'] != $recipeStep->id;
            });
        });

        // after that, create the other recette_produit relation if it needed
        $listToAdd = [];
        foreach ($stepsList as $step) {
            if ($step['name']) {
                $listToAdd[] = new RecipeStep($step);
            }
        };

        // and finally, save
        if (is_array($listToAdd) && !empty($listToAdd)) {
            $recipe->steps()->saveMany($listToAdd);
        }

        $recipe->load($this->with);

        return $recipe;
    }
}
