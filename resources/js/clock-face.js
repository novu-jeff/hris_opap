import { setupMap } from './helpers';

export function initializeClockFace() {
    $(function () {

        let isFaceDetected = false;
        let place = null;
        let stream = null;
        let watchId = null;
        let longitude = null;
        let latitude = null;

        const $video = $('#video');
        const $canvas = $('#canvas');
        const canvas = $canvas[0];
        const context = canvas.getContext('2d');
        const captureElement = document.querySelector('.camera');
        const $alertContainer = $('.alert-container');
        const $clockPreview = $('#clockInPreviewImage');
        const $clockModal = $('#clockInModal');
        const clockModal = new bootstrap.Modal($clockModal[0], { backdrop: 'static', keyboard: false });

        startLocate();
        startCamera();

        Livewire.on('loadMap', ([{ token, lng, lat, place: eventPlace }]) => {
            longitude = lng;
            latitude = lat;
            place = eventPlace;
            setupMap(token, [lng, lat]);
        });

        Livewire.on('loadDefaults', () => {
            startLocate();
            startCamera();
        });

        $(document).on('click', '.clock-process', async function () {
            const status = $(this).data('status');

            if (!captureElement) return console.error('Camera element not found.');

            const imageData = await captureSnapshot(captureElement);
            if (status === 'Done') return showAlert('Please be informed', 'You\'ve completed today’s work.');

            if (!isFaceDetected) return showAlert('No Face Detected', 'Please ensure your face is visible to the camera.');
            if (!place) return showAlert('No Location Detected', 'Please enable your location or GPS.');

            if (imageData) {
                $clockPreview.attr('src', imageData);
                showCountdownModal(imageData, isFaceDetected);
                stopCamera();
                stopLocate();
            }
        });

        $(document).on('click', '.clock-process-forced', async () => {
            if (!captureElement) return console.error('Camera element not found.');

            const imageData = await captureSnapshot(captureElement);

            if (!isFaceDetected) return showAlert('No Face Detected', 'Please ensure your face is visible to the camera.');
            if (!place) return showAlert('No Location Detected', 'Please enable your location or GPS.');

            if (imageData) {
                $clockPreview.attr('src', imageData);
                showCountdownModal(imageData, isFaceDetected, true); 
                stopCamera();
                stopLocate();
            }
        });


        $(document).on('click', '.retakeButton', () => {
            startCamera();
            startLocate();
        });

        async function captureSnapshot(element) {
            const canvas = await html2canvas(element, {
                useCORS: true,
                allowTaint: true,
                scale: window.devicePixelRatio,
            });
            return canvas.toDataURL('image/png');
        }

        function showAlert(title, text) {
            Swal.fire({ title, text, icon: 'info' });
        }

        function showCountdownModal(imageData, faceStatus, forced = false) {
            clockModal.show();
            const proceedBtn = $clockModal.find('button[type="submit"]')[0];
            const proceedLabel = proceedBtn.querySelector('span');
            let countdown = 5;

            proceedBtn.disabled = true;
            proceedLabel.textContent = `Proceed (${countdown})`;

            const interval = setInterval(() => {
                countdown--;
                proceedLabel.textContent = countdown > 0 ? `Proceed (${countdown})` : 'Proceed';
                if (countdown <= 0) {
                    clearInterval(interval);
                    proceedBtn.disabled = false;
                }
            }, 1000);

            Livewire.dispatch('imageCaptured', [imageData, faceStatus, forced]);
        }


        function startLocate() {
            if (!('geolocation' in navigator)) return console.error('Geolocation not supported.');

            watchId = navigator.geolocation.watchPosition(
                ({ coords }) => {
                    const { latitude: lat, longitude: lng } = coords;
                    Livewire.dispatch('getLocation', { lat, lng });
                },
                error => console.error('Geolocation error:', error),
                { enableHighAccuracy: true, timeout: 50000, maximumAge: 0 }
            );
        }

        function stopLocate() {
            if (watchId !== null && 'geolocation' in navigator) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
                Livewire.dispatch('getLocation', { lat: latitude, lng: longitude, isToHide: true });
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                $video[0].srcObject = stream;

                $video[0].onloadeddata = async () => {
                    await loadFaceApiModels();
                    await $video[0].play();
                    detectFacesLoop();
                };
            } catch (err) {
                showAlert('Please be informed', 'Camera and location access are required to continue.');
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                $video[0].srcObject = null;
            }
        }

        async function loadFaceApiModels() {
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/faceapi');
                console.log('Face API model loaded');
            } catch (error) {
                console.error('Failed to load Face API model:', error);
            }
        }

        async function detectFacesLoop() {
            const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 });

            async function detect() {
                try {
                    if ($video[0].readyState >= 2) {
                        const results = await faceapi.detectAllFaces($video[0], options);
                        if (results.length === 0) {
                            $alertContainer.html(`
                                <div class="alert-no-face">
                                    <div>Face Is Not Detected</div>
                                </div>
                            `);
                            isFaceDetected = false;
                        } else {
                            $alertContainer.empty();
                            isFaceDetected = true;
                        }
                    }
                } catch (err) {
                    console.error('Face detection error:', err);
                }
                requestAnimationFrame(detect);
            }

            detect();
        }

        // Set canvas size when video metadata is loaded
        $video.on('loadedmetadata', () => {
            canvas.width = $video[0].videoWidth;
            canvas.height = $video[0].videoHeight;
        });
    });
}
