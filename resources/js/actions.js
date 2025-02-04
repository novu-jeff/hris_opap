export function post(showConfirmation, url, data, tabs = null) {
    if(showConfirmation) {
        Swal.fire({
            title: "Are you sure?",
            text: "Please make sure that all informations are correct!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, continue"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        handleResponse(response, tabs);
                    }
                });
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                handleResponse(response, tabs);
            }
        });
    }
}

export function put(showConfirmation, url, data, tabs = null) {
    if(showConfirmation) {
        Swal.fire({
            title: "Are you sure?",
            text: "Please make sure that all informations are correct!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, continue"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        handleResponse(response, tabs)
                    }
                });
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                handleResponse(response, tabs)
            }
        });
    }
}

export function _delete(showConfirmation, url, data) {
    if(showConfirmation) {
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, I know"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if(response.status == 'success') {
                            Swal.fire({
                                icon: "success",
                                title: "Congratulations!",
                                text: response.message,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    redirect(response.redirect);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: response.message,
                            });    
                        }
                    }
                });
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (response) {
                if(response.status == 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Congratulations!",
                        text: response.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            redirect(response.redirect);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: response.message,
                    });    
                }
            }
        });
    }
}

function handleResponse(response, tabs = null) {
    if(response.status == 'success') {
        Swal.fire({
            icon: "success",
            title: "Congratulations!",
            text: response.message,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                redirect(response.redirect);
            }
        });
    } else {
        
        const errors = response.errors;

        if (response.hasOwnProperty('errors')) {

            const field = Object.keys(errors)[0];

            if (tabs !== null) {
                navigateToTab(field, tabs);
            }

            $.each(errors, function (field, error) {

                const errorField = $('.error-field');
                const divField = $("#" + field).next(errorField);

                errorField.empty();

                if (error.length > 0) {
                    setTimeout(() => {
                        if (divField.length > 0) {
                            const errorMessage = `<div class="error-field" style="color: red; text-transform: uppercase; font-size: 11px; font-weight: 600; margin-top: 8px;">${error[0]}</div>`;
                            divField.append(errorMessage);
                        } else {
                            divField.empty();
                        }
                    }, 100);
                }
            });

        } else {

            $('.error-field').empty();

            Swal.fire({
                icon: "error",
                title: response.title,
                text: response.message,
            });
            
        }
    }
}

function redirect(option) {
    console.log(option);
    if(option == '_clear') {
        $('textarea, input').val('');
        $('select').prop('selectedIndex', 0);
    } else if(option == '_reload') {
        $('textarea, input').val('');
        $('select').prop('selectedIndex', 0);
        setTimeout(() => {
            location.reload();
        }, 300)
    } else if(option == '_stay') {
          
    } else {
        location.href = option;
    }
}

function navigateToTab(field, tabs) {
    let parentKey = null;

    for (let key in tabs) {
        if (tabs[key].includes(field)) {
            parentKey = key;
            break; 
        }
    }

    $('.nav-pills .nav-link')
        .removeClass('active')
            .filter('[data-bs-target="#pills-' + parentKey + '"]')
                .addClass('active');
    $('.tab-content .tab-pane')
        .removeClass('show active')
            .filter('#pills-' + parentKey)
                .addClass('show active');
    
  }
  