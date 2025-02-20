export function copy_link() {
    $('.copy-link').on('click', function(e) {
        e.preventDefault();
        const link = $(this).data('target');

        const tempInput = document.createElement('input');
        tempInput.value = link;
        document.body.appendChild(tempInput);

        tempInput.select();
        tempInput.setSelectionRange(0, 99999); 
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        Swal.fire({
            icon: "success",
            title: 'Job Link Copied',
            html: 'You can now share the job link you copied!',  
            confirmButtonText: 'GOT IT',   
            confirmButtonColor: '#143953', 
            cancelButtonColor: '#d33',      
            reverseButtons: true,  
        });  
    });
}

export function reinitializeDataTable() {
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('.data-tables')) {
        $('.data-tables').DataTable().destroy();
    }
    if ($.fn.DataTable) {
        $('.data-tables').DataTable({
            scrollX: true,
            pageLength: 10 
        });
    } else {
        console.error('DataTable plugin is not loaded.');
    }
}

export function resetErrorsAndFields() {
    $('input[type="text"], input[type="number"], textarea').val('');
    $('select').prop('selectedIndex', 0);
    $('.error-field span').text('');
    $('.ck-content').empty();
}

export function ckeditor(isReadOnly = false) {

    let editEditor;

    // Check if an existing instance of CKEditor exists and destroy it
    const ckeditorElement = document.querySelector('#ckeditor');
    if (ckeditorElement && ckeditorElement.ckeditorInstance) {
        ckeditorElement.ckeditorInstance.destroy()
        .then(() => {
            console.log('Existing CKEditor instance destroyed.');
        })
        .catch(error => {
            console.error('Error destroying the existing editor:', error);
        });
    }

    // Create a new instance of CKEditor
    ClassicEditor
    .create(ckeditorElement, {
        toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote'],
        height: '500px'
    })
    .then(editor => {
        editEditor = editor;

        // Store the editor instance on the DOM element for future reference
        ckeditorElement.ckeditorInstance = editor;

        editor.model.document.on('change:data', () => {
            Livewire.dispatch('ckeditor', [editor.getData()]);
        });
        if (isReadOnly) {
            editor.isReadOnly = true;
        }
    })
    .catch(error => {
        console.error('Error creating the CKEditor instance:', error);
    });
}


export function removeRowDT(id) {
    var row = $('tr[data-id="' + id + '"]');
    
    if (row.length) {
        row.remove();
        
        if ($('tr').length === 1) {
            location.reload(); 
        }
    } else {
        console.log('Row not found!');
    }
}


export function reloadDT() {
    location.reload();
}

export function hideModal() {
    // $('body').css('overflow-y', 'scroll');
    $('.modal').removeClass('show').css('display', 'none');
    $('.modal-backdrop').remove(); 
    $('.modal-backdrop').css({
        'position': 'relative',
        'height': '100%'
    });
}

export function formatTime(date) {
    if (!date) return '';  // If no date is provided, return an empty string

    // Check if the date is already in a string format (e.g., "06:58 AM")
    if (typeof date === 'string' && /\d{2}:\d{2} (AM|PM)/.test(date)) {
        return date;  // Return the string as is if it's already in the correct format
    }

    // If it's a Date object, format it
    if (date instanceof Date) {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    // In case the date is neither a string nor a Date object, return an empty string
    return '';
}

export function convertToHoursAndMinutes(mins) {
    const hours = Math.floor(mins / 60);
    const minutes = mins % 60;
    return `${hours} hr ${minutes} min`;
}


export function getLocation(token) {
    mapboxgl.accessToken = token;

    let infoBox = $('#location-info');
    infoBox.html("<div class='mb-2 text-nowrap' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'>Locating <i class='ms-2 fa-solid fa-spinner fa-spin'></i></div>");

    // Watch the position in real time
    navigator.geolocation.watchPosition(successLocation, handleError, {
        enableHighAccuracy: true,
        maximumAge: 1000,  // Set the maximum age for location data to avoid stale information
        timeout: 5000  // Set a timeout for location fetching
    });

    function successLocation(position) {
        const { latitude, longitude } = position.coords;
        setupMap([longitude, latitude]);
        getAddress(longitude, latitude);
    }

    function handleError(error) {
        let $card = $('.clock-process'); // Select elements with wire:click
    
        // Set initial opacity
        $card.css('opacity', '0.5');
    
        // Remove wire:click after a short delay
        setTimeout(() => {
            $card.removeAttr('wire:click wire:target');
        }, 1500); // Adjust delay as needed
    
        Swal.fire({
            icon: "info",
            title: "Please be informed",
            text: "Camera and location access are required to continue. Please ensure both are enabled in your device settings before proceeding.",
            confirmButtonText: "Got it",
            confirmButtonColor: "#143953" 
        });         
    }

    function setupMap(center) {
        if (!document.getElementById("map")) {
            console.error("Error: #map container not found in the DOM.");
            return;
        }

        const map = new mapboxgl.Map({
            container: "map",
            style: "mapbox://styles/mapbox/streets-v12",
            center: center,
            zoom: 14,
            interactive: false,

            // Allows html2canvas to capture WebGL map
            preserveDrawingBuffer: true,
        });

        new mapboxgl.Marker().setLngLat(center).addTo(map);
    }

    function getAddress(lng, lat) {
        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;

        $.getJSON(url, function (data) {
            if (!data.features || data.features.length === 0) {
                console.error("No location data found");
                displayAddress("Location not found", lng, lat);
                return;
            }

            const features = data.features;
            const placeName = features[0]?.place_name || "Location not found";
            const city = getFeature(features, 'place') || "Unknown City";
            const province = getFeature(features, 'region') || "Unknown Province";
            const country = getFeature(features, 'country') || "Unknown Country";
            const address = features[0]?.text || "Unknown Address";
            const region = getRegion(features[0]?.context) || "Unknown Region";
            const postalCode = getFeature(features, 'postcode') || "Unknown Postal Code";

            const formattedAddress = `
                <div class='mb-0'>${city}, ${province}, ${country}</div>
                <div class='mb-0'>${address}, ${region}, ${postalCode} ${country}</div>
                <div class='mb-0'>Lat ${lat.toFixed(5)} ° , Long ${lng.toFixed(5)} °</div>
                <div class='mb-0'>${getCurrentDateTime()}</div>
            `;

            displayAddress(formattedAddress, lng, lat);
        }).fail(function () {
            displayAddress("Unable to retrieve address", lng, lat);
        });
    }

    function getFeature(features, type) {
        return features.find(f => f.place_type.includes(type))?.text || null;
    }

    function getRegion(context) {
        if (!context) return null;
        const regionFeature = context.find(f => f.id.includes('region'));
        return regionFeature ? regionFeature.text.toUpperCase() : null;
    }

    function getCurrentDateTime() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, "0");
        const month = String(now.getMonth() + 1).padStart(2, "0");
        const year = String(now.getFullYear()).slice(-2);
        const hours = now.getHours() % 12 || 12;
        const minutes = String(now.getMinutes()).padStart(2, "0");
        const ampm = now.getHours() >= 12 ? "PM" : "AM";
        const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        return `${day}/${month}/${year} ${hours}:${minutes} ${ampm} ${timeZone}`;
    }

    function displayAddress(address, lng, lat) {
        infoBox.html(address);
    }
}

