#   Tables:

##  Foods
    id (GUID)
    name
    brand
    servingSize
    servingUnit

    # Macronutrients
    protein
    fat
    carbs
    fiber
    sugar
    sodium

    # Vitamins
    vitaminA
    vitaminB1
    vitaminB2
    vitaminB3
    vitaminB5
    vitaminB6
    vitaminB7
    vitaminB9
    vitaminB12
    vitaminC
    vitaminD
    vitaminE
    vitaminK

    # Minerals
    calcium
    iron
    magnesium
    potassium
    zinc

##  Meals
    id (GUID)
    name
    date

##  Recipes
    id (GUID)
    name

##  Parts
    id (GUID)
    parent_id (GUID)
    parent_type (Meal/Recipe)
    join_id (GUID)
    join_type (Recipe/Food)
    amount
    amount_type

##  Exercises
    id (GUID)
    name
    activity
    date
    weight
    sets
    reps
    feedback

##  Weight
    id (GUID)
    pounds
    date