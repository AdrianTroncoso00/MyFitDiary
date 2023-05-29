let form = document.forms[0];
let ingredientsWrapper = document.getElementById("ingredientsForm");
let excludedWrapper = document.getElementById("excludedForm");
let nameIngredientWrapper = "ingredients[]";
let nameExcludedWrapper = "excluded[]";
// Add input
function addField(element, wrapper){
    if (element.previousElementSibling.value.trim() === "") {
        return false;
    }
    let div = document.createElement("div");
    div.setAttribute("class", "col-10 row-cols-sm-6 d-flex flex-row justify-content-between");
    let input = document.createElement("input");
    input.setAttribute("type", "text");
    input.setAttribute("class", "border-0 me-2 form-control form-control-sm col-7");
    input.setAttribute("name", "ingredients[]");
    input.setAttribute("placeholder", "Enter query or ingredient");
    let plus = document.createElement("span");
    plus.setAttribute("onclick", "addField(this)");
    plus.setAttribute("class", " btn px-3 btn-primary");
    let plusText = document.createTextNode("+");
    plus.appendChild(plusText);

    let minus = document.createElement("span");
    minus.setAttribute("onclick", "removeField(this)");
    minus.setAttribute("class", " btn px-3 btn-primary");
    let minusText = document.createTextNode("−");
    minus.appendChild(minusText);
    wrapper+'Wrapper'.insertInto(div, wrapper+'Wrapper'.lastChild);
    div.appendChild(input).focus();
    div.appendChild(plus);
    div.appendChild(minus);
    element.nextElementSibling.style.display = "block";
    element.style.display = "none";
    
    

}

// Remove input
function removeField(element){
    element.parentElement.remove();
}


