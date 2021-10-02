<?php

namespace App\Domain\Recipe\Services;

use App\Domain\Recipe\Entities\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use App\Domain\Recipe\Contracts\RecipeRepositoryInterface;

class RecipeCommandService
{
    /** @var RecipeRepositoryInterface $recipeRepository */
    private RecipeRepositoryInterface $recipeRepository;

    protected $validation = [
        'name'                  => 'required|string',
        'recipe_type'           => 'required|in:entrée,plat,dessert',
        'number_people'         => 'required|string|max:64',
        'preparation_time'      => 'integer',
        'cooking_time'          => 'integer',
        'complement'            => 'nullable|string',
        'products.*.product_id' => 'integer',
        'products.*.quantity'   => 'integer',
        // @TODO : define validation rule for product unit?
        'products.*.unit'       => 'nullable|string|in:grammes,litre,centilitre,cuilliere_cafe,cuilliere_dessert,cuilliere_soupe,verre_liqueur,verre_moutarde,grand_verre,tasse_cafe,bol,sachet,gousse',
        'steps.*.id'            => 'nullable|integer',
        'steps.*.name'          => 'string',
        'steps.*.instruction'   => 'string',
        'steps.*.position'      => 'integer|max:255',
    ];

    /**
     * Create Cart Recipe Service instance.
     *
     * @param RecipeRepositoryInterface $recipeRepository
     */
    public function __construct(RecipeRepositoryInterface $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * @param Request $request
     * @return Model
     */
    public function initializeRecipe(Request $request): Model
    {
        return $this->recipeRepository->initialize();
    }

    /**
     * @param Request $request
     * @return Recipe
     */
    public function createRecipe(Request $request): Recipe
    {
        $request->validate($this->validation);

        $attributes = $request->only(array_keys($this->validation));

        $recipe = $this->recipeRepository->create($attributes);
        $recipe = $this->updateProductsAndSteps($recipe, $attributes);

        return $recipe;
    }

    /**
     * @param $id
     * @param Request $request
     * @return Recipe
     */
    public function updateRecipe($id, Request $request): Recipe
    {
        $request->validate($this->validation);

        $attributes = $request->only(array_keys($this->validation));

        $recipe = $this->recipeRepository->update($attributes, $id);
        $recipe = $this->updateProductsAndSteps($recipe, $attributes);

        // if ($request->file('image') && $request->file('image')->isValid()) {
        //     $imageName = str_slug_fr($recipe->name).'-'.$recipe->id.'.'.$request->file('image')->getClientOriginalExtension();
        //
        //     $request->file('image')->move(
        //         base_path().'/public/brands', $imageName
        //     );
        //
        //     $recipe->visual = $request->file('image')->getClientOriginalExtension();
        //     $recipe->save();
        // }

        return $recipe;
    }

    /**
     * @param $id
     * @return int
     */
    public function destroyRecipe($id): int
    {
        return $this->recipeRepository->destroy($id);
    }

    /**
     * Update Recipe's Products and Steps
     *
     * @param Recipe $recipe
     * @param array $attributes
     * @return Recipe
     */
    private function updateProductsAndSteps(Recipe $recipe, array $attributes): Recipe
    {
        $recipe = $this->recipeRepository->syncProducts(
            $recipe,
            $this->sanitizeAttribute($attributes['products'])
        );
        $recipe = $this->recipeRepository->syncSteps(
            $recipe,
            $this->sanitizeAttribute($attributes['steps'])
        );

        return $recipe;
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function sanitizeAttribute(array $attributes = [])
    {
        $return = [];

        $attributes = reset($attributes);
        foreach ($attributes as $column => $values) {
            foreach ($values as $key => $value) {
                @$return[$key][$column] = $value;
            }
        }

        return $return;
    }

}
