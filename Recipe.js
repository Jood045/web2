const recipes = {
    1: {
        name: 'Banana Oat Pancakes',
        category: 'Breakfast',
        description: 'Healthy and delicious pancakes made with banana and oats',
        image: 'images/banana-pancakes.jpg',
        ingredients: ['1 ripe banana', '1 egg', '1/2 cup oats', '1/2 tsp cinnamon (optional)'],
        steps: ['Mash the banana and mix with the egg', 'Add oats and cinnamon', 'Cook small pancakes on a non-stick pan'],
        video_url: 'https://youtu.be/Stpe90s2pD4?si=Ky3jBuOxqy9zc5yd'
    },
    2: {
        name: 'Apple Peanut Butter Sandwich Bites',
        category: 'Dessert',
        description: 'Quick and healthy apple snack',
        image: 'images/apple-sandwich.jpg',
        ingredients: ['1 apple (sliced)', '2 tbsp peanut butter'],
        steps: ['Spread peanut butter between two apple slices', 'Serve immediately'],
        video_url: 'https://youtu.be/ANKtVORw05U?si=DDzevPe9DAggxJqQ'
    }
};

let ingCount = 0;
let stepCount = 0;

window.addEventListener('DOMContentLoaded', function () {
    const id = new URLSearchParams(window.location.search).get('id');
    const r = recipes[id];
    if (!r) return;

    document.getElementById('name').value        = r.name;
    document.getElementById('category').value    = r.category;
    document.getElementById('description').value = r.description;
    document.getElementById('video_url').value   = r.video_url;

    const img = document.getElementById('currentImg');
    img.src = r.image;
    img.style.display = 'block';

    const ingContainer = document.getElementById('ingredientsContainer');
    r.ingredients.forEach(function (ing, i) {
        ingCount++;
        const div = document.createElement('div');
        div.innerHTML = 'Ingredient ' + (i + 1) + ': <input type="text" name="ingredient[]" value="' + ing + '">'
            + '<button type="button" onclick="this.parentElement.remove()" style="background:#d8a7b1;color:white;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;margin-left:8px;">Remove</button>'
            + '<br><br>';
        ingContainer.appendChild(div);
    });

    const stepsContainer = document.getElementById('stepsContainer');
    r.steps.forEach(function (step, i) {
        stepCount++;
        const div = document.createElement('div');
        div.innerHTML = 'Step ' + (i + 1) + ': <input type="text" name="step[]" value="' + step + '">'
            + '<button type="button" onclick="this.parentElement.remove()" style="background:#d8a7b1;color:white;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;margin-left:8px;">Remove</button>'
            + '<br><br>';
        stepsContainer.appendChild(div);
    });
});

function addIngredient() {
    ingCount++;
    const container = document.getElementById('ingredientsContainer');
    const div = document.createElement('div');
    div.innerHTML = 'Ingredient ' + ingCount + ': <input type="text" name="ingredient[]" placeholder="e.g., 1 cup flour">'
        + '<button type="button" onclick="this.parentElement.remove()" style="background:#d8a7b1;color:white;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;margin-left:8px;">Remove</button>'
        + '<br><br>';
    container.appendChild(div);
}

function addStep() {
    stepCount++;
    const container = document.getElementById('stepsContainer');
    const div = document.createElement('div');
    div.innerHTML = 'Step ' + stepCount + ': <input type="text" name="step[]" placeholder="Describe this step">'
        + '<button type="button" onclick="this.parentElement.remove()" style="background:#d8a7b1;color:white;border:none;border-radius:5px;padding:4px 10px;cursor:pointer;margin-left:8px;">Remove</button>'
        + '<br><br>';
    container.appendChild(div);
}
