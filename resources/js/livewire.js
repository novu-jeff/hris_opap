import { 
    resetErrorsAndFields,
    removeRowDT,
    reloadDT,
    hideModal ,
    ckeditor
} from "./helpers";

Livewire.on('alert', (event) => {

    const alert = JSON.parse(JSON.stringify(event))[0]; 

    if (alert.status === 'success') {

        console.log(alert);
        if (alert.resetFields === true) {
            resetErrorsAndFields();
        }

        hideModal();

        Swal.fire({
            icon: "success",
            title: alert.title,
            html: alert.message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'GOT IT',   
            confirmButtonColor: '#143953',
        }).then((result) => {
            if (result.isConfirmed && alert.isRemoveRowDT) {
                removeRowDT(alert.id);
            }

            if (result.isConfirmed && alert.isReloadDT) {
                reloadDT();
            }
        });

    } else {
        if (alert.showAlert === true) {
            Swal.fire({
                icon: "error",
                title: alert.title,
                html: alert.message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'GOT IT',   
                confirmButtonColor: '#143953',
            });
        }
    }
});

Livewire.on('showConfirmation', function(data) {
    Swal.fire({
        icon: "info",
        title: data[0].title,
        html: data[0].message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: true,        
        cancelButtonText: 'Cancel',   
        confirmButtonText: 'Proceed',   
        confirmButtonColor: '#143953', 
        cancelButtonColor: '#d33',      
        reverseButtons: true,  
    }).then(function(result) {
        if(result.isConfirmed) {
            console.log(data[0]);
            Livewire.dispatch(data[0].action, [false]);
        }
    });  
});

Livewire.on('showModal', function(data) {
    var modal = new bootstrap.Modal($('#'+data[0].modal));
    modal.show();
    if (data[0].plugins && data[0].plugins.includes('ckeditor')) {
        ckeditor();
    }
});