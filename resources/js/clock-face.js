import { setupMap } from './helpers';

const FACE_STABLE_MS = 1000;
const FACE_DETECTOR_OPTIONS = { inputSize: 320, scoreThreshold: 0.35 };
const CAPTURE_MAX_WIDTH = 640;
const CAPTURE_JPEG_QUALITY = 0.82;

export function initializeClockFace() {
    $(function () {

        let isFaceDetected = false;
        let faceDetectedSince = null;
        let place = null;
        let stream = null;
        let watchId = null;
        let longitude = null;
        let latitude = null;
        let proceedLocked = false;
        let modelsLoaded = false;
        let pendingCaptureData = null;

        const $video = $('#video');
        const $canvas = $('#canvas');
        const canvas = $canvas[0];
        const context = canvas.getContext('2d');
        const captureElement = document.querySelector('.camera');
        const $alertContainer = $('.alert-container');
        const $clockPreview = $('#clockInPreviewImage');
        const $clockModal = $('#clockInModal');
        const clockModal = new bootstrap.Modal($clockModal[0], { backdrop: 'static', keyboard: false });

        startCamera();

        Livewire.on('loadMap', (data) => {
            const { lng, lat, place: eventPlace, token } = data;

            longitude = lng;
            latitude = lat;
            place = eventPlace;

            setupMap(token, [lng, lat]);
        });

        Livewire.on('loadDefaults', () => {
            resetFaceDetectionState();
            startCamera();
        });

        $(document).on('click', '.clock-process', async function () {
            const status = $(this).data('status');

            if (!captureElement) {
                console.error('Camera element not found.');
                resetClockCard();
                return;
            }

            try {
                if (status === 'Done') {
                    showAlert('Please be informed', 'You\'ve completed today\'s work.');
                    resetClockCard();
                    return;
                }

                if (!isFaceDetected) {
                    showAlert('No Face Detected', 'Please hold still until your face is recognized, then try again.');
                    resetClockCard();
                    return;
                }

                // Capture the raw video frame (not html2canvas) so overlays/watermarks
                // do not cover the face and break detection.
                const imageData = await captureSnapshot();

                if (!imageData) {
                    showAlert('Capture Failed', 'Could not capture your image. Please try again.');
                    resetClockCard();
                    return;
                }

                const faceInCapture = await verifyFaceInImage(null, imageData);
                if (!faceInCapture) {
                    showAlert('No Face Detected', 'No face was found in the captured image. Please try again.');
                    resetClockCard();
                    return;
                }

                const sessionId = crypto.randomUUID();
                const compressedImage = await compressImage(imageData);
                await showCountdownModal(compressedImage, true, false, sessionId);
                stopCamera();
            } catch (err) {
                console.error('Clock capture error:', err);
                abortCapture();
                showAlert('Error', 'Something went wrong while capturing your image. Please try again.');
            }
        });

        $(document).on('click', '.retakeButton', () => {
            proceedLocked = false;
            pendingCaptureData = null;
            setProceedEnabled(true);
            const component = getClockComponent();
            if (component) {
                component.call('clearCapture');
            }
            resetFaceDetectionState();
            clockModal.hide();
            startCamera();
        });

        $(document).on('click', '#clockProceedBtn', async function () {
            if (proceedLocked) {
                return;
            }

            proceedLocked = true;
            setProceedEnabled(false);

            try {
                const previewImg = document.getElementById('clockInPreviewImage');
                const imageData = pendingCaptureData ?? previewImg?.src;

                const faceInPreview = await verifyFaceInImage(previewImg, imageData || null);
                if (!faceInPreview) {
                    showAlert('No Face Detected', 'No face found in the preview. Please retake your photo.');
                    proceedLocked = false;
                    setProceedEnabled(true);
                    return;
                }

                const component = getClockComponent();
                if (!component) {
                    showAlert('Error', 'Clock session expired. Please refresh the page and try again.');
                    proceedLocked = false;
                    setProceedEnabled(true);
                    return;
                }

                await component.call('triggerClock', pendingCaptureData);
            } catch (err) {
                console.error('Proceed error:', err);
                showAlert('Error', 'Could not complete clocking. Please try again.');
                proceedLocked = false;
                setProceedEnabled(true);
            }
        });

        window.addEventListener('close-clock-modal', () => {
            proceedLocked = false;
            pendingCaptureData = null;
            setProceedEnabled(true);
            clockModal.hide();
            resetFaceDetectionState();
            startCamera();
        });

        window.addEventListener('reset-proceed-button', () => {
            proceedLocked = false;
            setProceedEnabled(true);
        });

        window.addEventListener('reset-clock-button', () => {
            resetClockCard();
        });

        async function captureSnapshot() {
            const video = $video[0];

            if (video?.readyState >= 2 && video.videoWidth > 0) {
                const snapshotCanvas = document.createElement('canvas');
                snapshotCanvas.width = video.videoWidth;
                snapshotCanvas.height = video.videoHeight;
                snapshotCanvas.getContext('2d').drawImage(video, 0, 0);

                return snapshotCanvas.toDataURL('image/png');
            }

            if (!captureElement) {
                return null;
            }

            $alertContainer.empty();

            const snapshotCanvas = await html2canvas(captureElement, {
                useCORS: true,
                allowTaint: true,
                scale: window.devicePixelRatio,
            });

            return snapshotCanvas.toDataURL('image/png');
        }

        function showAlert(title, text) {
            Swal.fire({ title, text, icon: 'info' });
        }

        async function showCountdownModal(imageData, faceStatus, forced, sessionId) {
            const component = getClockComponent();
            if (!component) {
                showAlert('Error', 'Clock session expired. Please refresh the page and try again.');
                resetClockCard();
                return;
            }

            pendingCaptureData = imageData;
            proceedLocked = false;
            setProceedEnabled(false);

            // Small payload only — image stays client-side until Proceed.
            await component.call('prepareCapture', sessionId, faceStatus, forced);

            $clockPreview.attr('src', imageData);
            clockModal.show();
            setProceedEnabled(true);
        }

        function abortCapture() {
            proceedLocked = false;
            pendingCaptureData = null;
            setProceedEnabled(true);
            clockModal.hide();

            const component = getClockComponent();
            if (component) {
                component.call('clearCapture');
            }

            resetFaceDetectionState();
            resetClockCard();
            startCamera();
        }

        function compressImage(dataUrl, maxWidth = CAPTURE_MAX_WIDTH, quality = CAPTURE_JPEG_QUALITY) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round(height * (maxWidth / width));
                        width = maxWidth;
                    }

                    const snapshotCanvas = document.createElement('canvas');
                    snapshotCanvas.width = width;
                    snapshotCanvas.height = height;
                    snapshotCanvas.getContext('2d').drawImage(img, 0, 0, width, height);
                    resolve(snapshotCanvas.toDataURL('image/jpeg', quality));
                };
                img.onerror = () => reject(new Error('Failed to compress image'));
                img.src = dataUrl;
            });
        }

        function setProceedEnabled(enabled) {
            $('#clockProceedBtn').prop('disabled', !enabled);
        }

        function resetFaceDetectionState() {
            isFaceDetected = false;
            faceDetectedSince = null;
            renderFaceStatus('waiting');
        }

        function renderFaceStatus(state) {
            if (state === 'ready') {
                $alertContainer.html(`
                    <div class="alert-face-ready">
                        <div>Face detected — you may clock in</div>
                    </div>
                `);
                return;
            }

            if (state === 'stabilizing') {
                $alertContainer.html(`
                    <div class="alert-face-stabilizing">
                        <div>Hold still — detecting face...</div>
                    </div>
                `);
                return;
            }

            if (state === 'not-detected') {
                $alertContainer.html(`
                    <div class="alert-no-face">
                        <div>Face Is Not Detected</div>
                    </div>
                `);
                return;
            }

            $alertContainer.empty();
        }

        function resetClockCard() {
            window.dispatchEvent(new Event('reset-clock-button'));
        }

        function getClockComponent() {
            const root = document.querySelector('.clockinout');
            const wireId = root?.closest('[wire\\:id]')?.getAttribute('wire:id');

            return wireId ? Livewire.find(wireId) : Livewire.first();
        }

        async function ensureFaceModelsLoaded() {
            if (modelsLoaded) {
                return true;
            }

            await faceapi.nets.tinyFaceDetector.loadFromUri('/faceapi');
            modelsLoaded = true;
            return true;
        }

        function waitForImageLoad(imgElement) {
            if (!imgElement?.src) {
                return Promise.reject(new Error('Image source missing'));
            }

            if (imgElement.complete && imgElement.naturalWidth > 0) {
                return Promise.resolve();
            }

            return new Promise((resolve, reject) => {
                imgElement.onload = () => resolve();
                imgElement.onerror = () => reject(new Error('Image failed to load'));
            });
        }

        async function verifyFaceInImage(imgElement, imageData = null) {
            await ensureFaceModelsLoaded();

            const options = new faceapi.TinyFaceDetectorOptions(FACE_DETECTOR_OPTIONS);

            if (imageData) {
                const target = await faceapi.fetchImage(imageData);
                const results = await faceapi.detectAllFaces(target, options);
                return results.length > 0;
            }

            if (imgElement) {
                await waitForImageLoad(imgElement);
                const results = await faceapi.detectAllFaces(imgElement, options);
                return results.length > 0;
            }

            return false;
        }

        function startLocate() {
            if (!('geolocation' in navigator)) {
                console.error('Geolocation not supported.');
                Swal.fire('Error', 'Your device does not support GPS.', 'error');
                return;
            }

            watchId = navigator.geolocation.watchPosition(
                ({ coords }) => {
                    latitude = coords.latitude;
                    longitude = coords.longitude;

                    Livewire.dispatch('getLocation', [longitude, latitude, false]);
                },
                error => {
                    console.error('GPS ERROR', error);

                    let msg = '';
                    switch (error.code) {
                        case 1: msg = 'Location permission denied.'; break;
                        case 2: msg = 'Location unavailable.'; break;
                        case 3: msg = 'Location request timed out.'; break;
                        default: msg = error.message;
                    }

                    Swal.fire('Location Error', msg, 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                }
            );
        }

        function stopLocate() {
            if (watchId !== null && 'geolocation' in navigator) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
                Livewire.dispatch('getLocation', [longitude, latitude, true]);
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                $video[0].srcObject = stream;

                $video[0].onloadeddata = async () => {
                    await ensureFaceModelsLoaded();
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

        async function detectFacesLoop() {
            const options = new faceapi.TinyFaceDetectorOptions(FACE_DETECTOR_OPTIONS);

            async function detect() {
                try {
                    if ($video[0].readyState >= 2) {
                        const results = await faceapi.detectAllFaces($video[0], options);

                        if (results.length === 0) {
                            faceDetectedSince = null;
                            isFaceDetected = false;
                            renderFaceStatus('not-detected');
                        } else if (!faceDetectedSince) {
                            faceDetectedSince = Date.now();
                            isFaceDetected = false;
                            renderFaceStatus('stabilizing');
                        } else if (Date.now() - faceDetectedSince >= FACE_STABLE_MS) {
                            isFaceDetected = true;
                            renderFaceStatus('ready');
                        } else {
                            isFaceDetected = false;
                            renderFaceStatus('stabilizing');
                        }
                    }
                } catch (err) {
                    console.error('Face detection error:', err);
                }

                requestAnimationFrame(detect);
            }

            detect();
        }

        $video.on('loadedmetadata', () => {
            canvas.width = $video[0].videoWidth;
            canvas.height = $video[0].videoHeight;
        });
    });
}
