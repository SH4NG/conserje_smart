
//TITULO NAVEGACION PIE PAGINA

    //sin funcion

//TITULO BOTON QUIENES_SOMOS PIE PAGINA

    //boton 1: quienes somos
    const btn_navegacion1 = document.getElementById("btn_navegacion1"); // obtiene el boton mediante su id
    if (btn_navegacion1) { // verifica que el boton exista en la pagina
        btn_navegacion1.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            alert("Ir a quienes somos"); // muestra un mensaje indicando que funciono
        });
    }
//TITULO BOTON NUESTRO_OBJETIVO PIE PAGINA

    //boton 2: nuestro objetivo
    const btn_navegacion2 = document.getElementById("btn_navegacion2"); // obtiene el boton mediante su id
    if (btn_navegacion2) { // verifica que el boton exista en la pagina
        btn_navegacion2.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            alert("Ir a nuestro objetivo"); // muestra un mensaje indicando que funciono
        });
    }

//TITULO BOTON NUESTRA_VISION PIE PAGINA

    //boton 3: nuestra vision
    const btn_navegacion3 = document.getElementById("btn_navegacion3"); // obtiene el boton mediante su id
    if (btn_navegacion3) { // verifica que el boton exista en la pagina
        btn_navegacion3.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            alert("Ir a nuestra vision"); // muestra un mensaje indicando que funciono
        });
    }
//TITULO BOTON CONSULTAS PIE PAGINA

    //boton 4: consultas
    const btn_navegacion4 = document.getElementById("btn_navegacion4"); // obtiene el boton mediante su id
    if (btn_navegacion4) { // verifica que el boton exista en la pagina
        btn_navegacion4.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            alert("Ir a consultas"); // muestra un mensaje indicando que funciono
        });
    }