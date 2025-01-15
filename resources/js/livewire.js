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

            if (alert.hasOwnProperty('redirect') && alert.redirect !== '') {
                location.href = alert.redirect;
            }
        });

    } else {
        if (alert.showAlert === true) {
            Swal.fire({
                icon: alert.status,
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
    let textareaHtml = ''; // Initialize the variable for textarea HTML
    let formData = {};
    let isTextareaPresent = false; // Flag to track if textarea is present
    let errorHtml = ''; // Variable to store error message HTML
    // Check if 'textarea' exists in the 'plugin' and is an array
    if (data[0].plugin && data[0].plugin[0] == 'textarea') {
        // Add textarea HTML with default title
        if (data[0].plugin && data[0].plugin[0] == 'textarea') {
            const pluginTitle = data[0].plugin['title'] || ''; // Safely access the title
            const hrElement = pluginTitle.trim() ? '<hr class="my-4">' : ''; // Add <hr> only if title is not empty
        
            // Add textarea HTML with the <hr> conditionally
            textareaHtml = `
                ${hrElement}
                <div class="form-group">
                    <label for="accomplishment-report">${pluginTitle}</label>
                    <textarea id="accomplishment-report" class="form-control mt-2" rows="4"></textarea>
                    ${errorHtml} <!-- Error message will be appended here -->
                </div>
            `;
            isTextareaPresent = true; // Mark the textarea as present
        }
        
    }

    Swal.fire({
        icon: "info",
        title: data[0].title,
        html: data[0].message + textareaHtml, // Append the textarea HTML if applicable
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: true,        
        cancelButtonText: 'Cancel',   
        confirmButtonText: 'Proceed',   
        confirmButtonColor: '#143953', 
        cancelButtonColor: '#d33',      
        reverseButtons: true,  
    }).then(function(result) {
        const textarea = document.getElementById('accomplishment-report');
        if (result.isConfirmed) {
            if (data[0].plugin && data[0].plugin[0] == 'textarea') {

                const textareaValue = textarea ? textarea.value : '';

                // If textarea is present, make sure it's not empty
                if (isTextareaPresent && !textareaValue.trim()) {
                    // Create the error message HTML
                    errorHtml = `<p style="font-size:11px" class="text-danger mt-2 text-uppercase fw-bold mt-2">The accomplishment report is required.</p>`;
                    
                    // Reopen the Swal dialog and append the error message to the textarea
                    Swal.fire({
                        icon: "info",
                        title: data[0].title,
                        html: data[0].message + textareaHtml + errorHtml, // Append error message below textarea
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCancelButton: true,        
                        cancelButtonText: 'Cancel',   
                        confirmButtonText: 'Proceed',   
                        confirmButtonColor: '#143953', 
                        cancelButtonColor: '#d33',      
                        reverseButtons: true,  
                    });
                    return; // Prevent dispatching if validation fails
                }

                formData['report'] = textareaValue;
                Livewire.dispatch(data[0].action, [false, formData]);
            } else {
                Livewire.dispatch(data[0].action, [false]);
            }
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


Livewire.on('reinitializeSelect', function() {
    $(function() {
        $('.select-2').select2();
        $('.select-2').on('change', function() {
            var field = $(this).attr('id');
            var value = $(this).val(); 
            Livewire.dispatch('populateField', [field, value]);
        });
    });
});

Livewire.on('reinitializeDataTable', function() {
    $(function() {
        reinitializeDataTable();
    });
});

Livewire.on('select2:init', () => {
    $('.select-2').select2();
});


