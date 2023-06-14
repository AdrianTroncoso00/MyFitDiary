let form = document.forms[0];
let ingredientsWrapper = document.getElementById("ingredientsWrapper");
let excludedWrapper = document.getElementById("excludedWrapper");
function addField(element, wrapper) {
    let nombre = wrapper=='ingredientsWrapper' ? 'ingredients[]' : 'excluded[]';
    let clase = wrapper=='ingredientsWrapper' ? 'ingredients' : 'excluded';
    let buenWrapper = wrapper=='ingredientsWrapper' ? ingredientsWrapper : excludedWrapper;
    let nombreWrapper = wrapper=='ingredientsWrapper' ? 'ingredientsWrapper' : 'excludedWrapper';
    if (element.previousElementSibling.value.trim() === "") {
        return false;
    }
    let div = document.createElement("div");
    div.setAttribute("class", clase+"Form col-10 d-flex justify-content-between");

    let input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("class", "border-0 me-2 form-control form-control-sm col-7");
    input.setAttribute("name", nombre);
    input.setAttribute("placeholder", "Enter query or ingredient");

    let plus = document.createElement("span");
    plus.setAttribute("onclick", "addField(this, '"+nombreWrapper+"')");
    plus.setAttribute("class", "btn btn-success px-3");
    let plusText = document.createTextNode("+");
    plus.appendChild(plusText);

    let minus = document.createElement("span");
    minus.setAttribute("onclick", "removeField(this)");
    minus.setAttribute("class", "btn btn-success px-3");
    let minusText = document.createTextNode("−");
    minus.appendChild(minusText);

    buenWrapper.insertBefore(div, buenWrapper.lastChild);
    div.appendChild(input).focus();
    div.appendChild(plus);
    div.appendChild(minus);

    element.nextElementSibling.style.display = "block";

    element.style.display = "none";
}



// Remove input
function removeField(element) {
    element.parentElement.remove();
}


