//TITULO CONTACTOS PIE PAGINA

    //sin funcion

//TITULO BOTON CORREO PIE PAGINA

    //boton 1: correo
    const btn_contactos1 = document.getElementById("btn_contactos1"); // obtiene el boton mediante su id
    if (btn_contactos1) { // verifica que el boton exista en la pagina
        btn_contactos1.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            // Define el correo
            const email = "alex.aravena@duocuc.cl";
            // Opción A: Abrir la aplicación de correo (lo más común)
            window.location.href = "mailto:" + email;

            
        });
    }
//TITULO BOTON TELEFONO PIE PAGINA

    //boton 2: telefono
    const btn_contactos2 = document.getElementById("btn_contactos2"); // obtiene el boton mediante su id
    if (btn_contactos2) { // verifica que el boton exista en la pagina
        btn_contactos2.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
           // Redirige directamente al chat de WhatsApp
            window.location.href = "https://wa.me/56920540915";
        });
    }

//TITULO BOTON INSTAGRAM PIE PAGINA

    //boton 3: instagram
    const btn_contactos3 = document.getElementById("btn_contactos3"); // obtiene el boton mediante su id
    if (btn_contactos3) { // verifica que el boton exista en la pagina
        btn_contactos3.addEventListener("click", function (e) { // detecta cuando se hace click
            e.preventDefault();
            window.location.href = "https://www.instagram.com/el_chico_ale/";
        });
    }