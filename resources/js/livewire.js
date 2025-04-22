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

            if (alert.hasOwnProperty('redirect') && alert.redirect == '_reload') {
                return location.reload();
            }

            if (alert.hasOwnProperty('redirect') && alert.redirect == '_stay') {
                return;
            }

            if (alert.hasOwnProperty('redirect') && alert.redirect !== '') {
                location.href = alert.redirect;
            }
        });
        
    } else if(alert.status === 'processing') {
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

});

Livewire.on("showConfirmation", function (data) {
    let fileInputHtml = "";
    let formData = {};
    let isFileUploadPresent = false;

    if (data[0].plugin && data[0].plugin[0] === "file") {
        const pluginTitle = data[0].plugin["title"] || "";
        const hrElement = pluginTitle.trim() ? '<hr class="my-4">' : "";

        fileInputHtml = `
            ${hrElement}
            <div class="form-group">
                <label for="accomplishment-file">${pluginTitle}</label>
                <input type="file" id="accomplishment-file" class="form-control mt-2 mb-3">
                <a style="font-size: 12px;" href="/templates/forms/HRMS-PD Form 07.docx" class="text-primary text-uppercase mb-3" download>Download Template</a>
                <div id="error-message"></div> <!-- Placeholder for error message -->
            </div>
        `;

        isFileUploadPresent = true;
    }

    function showSwal(errorMessage = "") {
        Swal.fire({
            icon: "info",
            title: data[0].title,
            html: data[0].message + fileInputHtml,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            cancelButtonText: "Cancel",
            confirmButtonText: "Proceed",
            confirmButtonColor: "#143953",
            cancelButtonColor: "#d33",
            reverseButtons: true,
            didOpen: () => {
                if (errorMessage) {
                    document.getElementById("error-message").innerHTML =
                        `<p style="font-size:11px" class="text-danger mt-2 text-uppercase fw-bold">${errorMessage}</p>`;
                }
            },
        }).then(function (result) {
            const fileInput = document.getElementById("accomplishment-file");
            const selectedFile = fileInput ? fileInput.files[0] : null;

            if (result.isConfirmed) {
                if (isFileUploadPresent && !selectedFile) {
                    showSwal("The accomplishment report file is required."); // Reopen modal with error message
                    return; // Prevent dispatching if validation fails
                }

                if (selectedFile) {
                    // Convert file to Base64 and send it to Livewire
                    const reader = new FileReader();
                    reader.readAsDataURL(selectedFile);
                    reader.onload = () => {
                        formData["report"] = reader.result;
                        formData["filename"] = selectedFile.name;
                        formData["mimeType"] = selectedFile.type;

                        Livewire.dispatch(data[0].action, [formData, data[0]["time"]]);
                    };
                } else {
                    Livewire.dispatch(data[0].action, [false]);
                }
            }
        });
    }

    showSwal(); // Initial call to show the modal
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

Livewire.on('scrollToError', function(errors) {
    if (errors.length) {
        let errorField = Array.isArray(errors[0]) ? errors[0][0] : errors[0]; 

        // Check for wire:model first
        let $inputElement = $(`[wire\\:model="${errorField}"]`); 
        // If the input element isn't found, check for wire:model.live
        if (!$inputElement.length) {
            $inputElement = $(`[wire\\:model\\.live="${errorField}"]`);
        }

        if ($inputElement.length) {
            $('html, body').animate({
                scrollTop: $inputElement.offset().top - 100 
            }, 100);
        }
    }
});



Livewire.on('isUploadingLogs', (event) => {
    console.log(event);
    let batchId = event[0] ?? "";

    console.log(batchId);

    if (!batchId) {
        console.warn("No batch ID found. Aborting job progress check.");
        return;
    }

    // Ensure modal is initialized and shown
    setTimeout(() => {
        $('#modal-loading').modal('show');
    }, 100);

    function checkJobProgress(batchId) {
        $.ajax({
            url: `/admin/timekeeping/upload/job/${batchId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {

                if (response.progress !== undefined) {

                    $('#progress-text').text(`Uploading... ${response.progress}%`);

                    if (response.progress < 100) {
                        setTimeout(() => checkJobProgress(batchId), 2000); // Poll every 2 sec (fixed timeout)
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Yey!',
                            html: 'Uploading Success',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'GOT IT',
                            confirmButtonColor: '#143953',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modal-loading').modal('hide');
                            }
                        });
                    }
                }

            },
            error: function(xhr) {
                console.error("Error fetching job progress:", xhr);
                $('#modal-loading').modal('hide');
            }
        });
    }

    // Start job progress check
    setTimeout(() => checkJobProgress(batchId), 100); // Small delay to ensure modal visibility

    $('#cancel-job').on('click', () => {
        Livewire.dispatch('cancelUpload', [batchId]);
    });

});