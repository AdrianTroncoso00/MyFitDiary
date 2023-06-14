const desayuno = document.getElementById('porcentDesayuno');
const brunch = document.getElementById('porcentBrunch');
const comida = document.getElementById('porcentComida');
const snack = document.getElementById('porcentSnack');
const cena = document.getElementById('porcentCena');
const numComidas = document.getElementById('num_comidas');

numComidas.addEventListener('input', () => {
    var valor = numComidas.value;
    if (valor == 3) {
        desayuno.style.display = 'block';
        brunch.style.display = 'none';
        comida.style.display = 'block';
        snack.style.display = 'none';
        cena.style.display = 'block';
    }
    if (valor == 4) {
        desayuno.style.display = 'block';
        brunch.style.display = 'block';
        comida.style.display = 'block';
        snack.style.display = 'none';
        cena.style.display = 'block';
    }
    if (valor == 5) {
        desayuno.style.display = 'block';
        brunch.style.display = 'block';
        comida.style.display = 'block';
        snack.style.display = 'block';
        cena.style.display = 'block';
    }

});

window.addEventListener('load', function () {
    var valor = numComidas.value;
    if (valor == 3) {
        desayuno.style.display = 'block';
        brunch.style.display = 'none';
        comida.style.display = 'block';
        snack.style.display = 'none';
        cena.style.display = 'block';
    }
    if (valor == 4) {
        desayuno.style.display = 'block';
        brunch.style.display = 'block';
        comida.style.display = 'block';
        snack.style.display = 'none';
        cena.style.display = 'block';
    }
    if (valor == 5) {
        desayuno.style.display = 'block';
        brunch.style.display = 'block';
        comida.style.display = 'block';
        snack.style.display = 'block';
        cena.style.display = 'block';
    }
});

