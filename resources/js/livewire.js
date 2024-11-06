import { 
    resetErrorsAndFields,
    removeRowDT,
    reloadDT,
    hideModal ,
    ckeditor,
    reinitializeDataTable
} from "./helpers";

Livewire.on('notice', (event) =>  {
    const notice = JSON.parse(JSON.stringify(event))[0];
    Swal.fire({
        icon: "warning",
        title: notice.title,
        html: notice.message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: true,        
        cancelButtonText: 'Cancel',   
        confirmButtonText: 'Proceed',   
        confirmButtonColor: '#143953', 
        cancelButtonColor: '#d33',      
        reverseButtons: true,  
    }).then((result) => {
        if(result.isConfirmed) {
            Livewire.dispatch('withdraw', [notice.id, true]);
        }
    });  
}) 

Livewire.on('alert', (event) => {

    const alert = JSON.parse(JSON.stringify(event))[0]; 

    if (alert.status === 'success') {

        if (alert.resetFields === true) {
            resetErrorsAndFields();
        }

        $('.modal.show').each(function() {  
            var modalInstance = bootstrap.Modal.getInstance(this);  
            if (modalInstance) {
                modalInstance.hide(); 
            }
        });

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

    if(alert.status === 'processing') {
        alert();
        Swal.fire({
            title: alert.title,
            text: alert.message,
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
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

Livewire.on('hideModal', function(data) {
    var modalElement = document.getElementById(data[0].modal);
    if (modalElement) {
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    } else {
        console.error('Modal with ID ' + data[0].modal + ' not found.');
    }
});

Livewire.on('showLatest', function() {
    setTimeout(() => {
        $('.modal-body').animate({
            scrollTop: $('.modal-body')[0].scrollHeight * 100
        }, 1); 
    }, 100);
});

