const formulario_ajax = document.querySelectorAll(".FormularioAjax");

formulario_ajax.forEach(formularios => {
    formularios.addEventListener("submit",function(e){
        e.preventDefault();

        Swal.fire({
            title: "¿Estas seguro?",
            text: "¿Quieres realizar la acción solicitada?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, realizar",
            cancelButtonText: "No, cancelar"
          }).then((result) => {
            if (result.isConfirmed) {

                let data = new FormData(this);
                let method = this.getAttribute("method");
                let action = this.getAttribute("action");

                let encabezados = new Headers();
                
                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                };

                fetch(action, config)
                .then(response => response.json())
                .then(response => {
                    return alertas_ajax(response);
                });

            }
          });

    });
});

function alertas_ajax(alerta){

    if(alerta.tipo == "simple"){
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: "Aceptar"
        });
    }else if(alerta.tipo == "recargar"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });

    }else if(alerta.tipo == "limpiar"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector(".FormularioAjax").reset();
            }
        });

    }else if(alerta.tipo == "redireccionar"){
        window.location.href = alerta.url;
    }

}

//boton de cerrar sesion 
let btnExit = document.getElementById('btn_exit');

btnExit.addEventListener("click", function(e){
    e.preventDefault();

    Swal.fire({
        title: "¿Quieres cerrar la sesión?",
        text: "La sesión actual se cerrará y saldras del sistema",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, salir",
        cancelButtonText: "No, cancelar"
      }).then((result) => {
        if (result.isConfirmed) {

            let url = this.getAttribute("href");

            window.location.href = url;

        }
      });

});