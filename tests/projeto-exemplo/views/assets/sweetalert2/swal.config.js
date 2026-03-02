
const swalModal = {
    methods: {
        
        getDefaultOptions(message = "", title = "", icon = "success") {
          return {
            title: title,
            text: message,
            icon: icon,
            confirmButtonText: 'Ok'
          }
        },
  
        getDefaultButtons() {
          return {
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary mx-2',
                denyButton: 'btn btn-default mx-2',
            },          
          }
        }
    },
  };
  
  function $_alert(message, title = '') {
      return Swal.fire(  Object.assign({}, 
        swalModal.methods.getDefaultOptions(message, title, 'info'),
        swalModal.methods.getDefaultButtons(),
      ))
  }
  
  function $_alert_error(message, title = '') {
    return Swal.fire(  Object.assign({}, 
      swalModal.methods.getDefaultOptions(message, title, 'error'),
      swalModal.methods.getDefaultButtons(),
    ))  
  }
  
  function $_alert_success(message, title = '') {
    return Swal.fire(  Object.assign({}, 
      swalModal.methods.getDefaultOptions(message, title, 'success'),
      swalModal.methods.getDefaultButtons(),
    ))
  }
  
  //Impreciso acima de 8000ms
  function $_alert_loading(message, title = '', estimatedProgressTime = 2000, autoClose = false, setupCallback, finishCallback) {
      
      let timerInterval;
      let htmlMessage = `<div>${message}</div>`;
      let progressHtml = `<div><progress value="0" max="100"></progress></div>`;
      if(estimatedProgressTime <= 0) {
        progressHtml = "";
      }   
  
      return Swal.fire({
          allowOutsideClick: false,
          allowEnterKey: false,
          title: title,
          html: `${htmlMessage}${progressHtml}`,
  
          didOpen: () => {
  
            Swal.showLoading()
  
            //Lifecycle:setup()
            if(typeof setupCallback == 'function') {
              return setupCallback(Swal);
            }          
  
            if(estimatedProgressTime > 0) {
  
              const progressElement = Swal.getHtmlContainer().querySelector('progress')
              const framerate = 100;
              const totalsteps = parseFloat(estimatedProgressTime/framerate);
              const sizestep = parseFloat(100/totalsteps);
              let porcent = 0;
          
              timerInterval = setInterval(() => {
                  porcent = Math.ceil(progressElement.value+sizestep)
                  progressElement.value = porcent;
                  if(porcent >= 100) {
                    clearInterval(timerInterval);
                    //Lifecycle:finish()
                    if(typeof finishCallback == 'function') {
                      return finishCallback(Swal);
                    }
                  }
              }, framerate);
  
              //AutoCloseAlert
              if(autoClose) {
                setTimeout(() => {
                  Swal.close();
                }, estimatedProgressTime);
              }
            }
          },
          
          willClose: () => {
              clearInterval(timerInterval)
          }
      })
      /*
      .then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          console.log('I was closed by the timer')
        }
      })
      */
  }
  
  function $_alert_waiting(message = "Por favor, aguarde...", title = '') {
    return $_alert_loading(message, title, 0, false)
  }
  
  function $_alert_ajax() {
      Swal.fire({
          title: 'Submit your Github username',
          input: 'text',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Look up',
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return fetch(`//api.github.com/users/${login}`)
              .then(response => {
                if (!response.ok) {
                  throw new Error(response.statusText)
                }
                return response.json()
              })
              .catch(error => {
                Swal.showValidationMessage(
                  `Request failed: ${error}`
                )
              })
          },
          allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: `${result.value.login}'s avatar`,
              imageUrl: result.value.avatar_url
            })
          }
        })
  }~

  function $_alert_confirm(message, title = '', confirmButtonText = 'Ok', denyButtonText = 'Cancelar') {
    Swal.fire({
      title: title,
      text: message,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: confirmButtonText
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: "Deleted!",
          text: "Your file has been deleted.",
          icon: "success"
        });
      }
    });
  }
