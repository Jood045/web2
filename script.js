function addIngredient() {
    const container = document.getElementById("ingredients-container");

    const div = document.createElement("div");
    div.innerHTML = `
        Name: <input type="text" required>
        Quantity: <input type="text" required>
        <br><br>
    `;

    container.appendChild(div);
}

function addStep() {
    const container = document.getElementById("steps-container");

    const div = document.createElement("div");
    div.innerHTML = `
        Step: <input type="text" required>
        <br><br>
    `;

    container.appendChild(div);
}