Tables:

    Foods
        id (GUID)
        [all nutrient fields]

    Meals
        id (GUID)
        name
        date

    Recipes
        id (GUID)
        name

    Parts
        id (GUID)
        parent_id (GUID)
        parent_type (Meal/Recipe GUID)
        join_id (GUID)
        join_id (Recipe/Food GUID)
        size
        unit

    Exercises
        id (GUID)
        name
        activity
        date
        weight
        sets
        reps
        feedback

    Weight
        id (GUID)
        pounds
        date