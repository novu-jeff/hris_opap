import {
    getGPSCoordinates,
    setupMap
} from './helpers';

export function initializeClockFace(Livewire) {
    $(function () {
        locateMe();
        startCameraWithFaceDetection();

        Livewire.on('loadMap', (event) => {
            const { token, lng, lat } = event[0];
            setupMap(token, [lng, lat]);
        });

        Livewire.on('captureImage', async () => {
            const captureElement = document.querySelector('.camera');

            if (!captureElement) {
                console.error('Capture element not found.');
                return;
            }

            const snapshotCanvas = await html2canvas(captureElement, {
                useCORS: true,
                allowTaint: true,
                scale: window.devicePixelRatio,
            });

            const imageData = snapshotCanvas.toDataURL('image/png');
            Livewire.dispatch('imageCaptured', [imageData]);
        });

        async function locateMe() {
            try {
                const { lat, lng } = await getGPSCoordinates();
                Livewire.dispatch('getLocation', { lat, lng });
            } catch (error) {
                console.error('Geolocation error:', error);
            }
        }

        const video = $('#video')[0];
        const canvas = $('#canvas')[0];
        const context = canvas.getContext('2d');

        $(video).on('loadedmetadata', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        });

        async function startCameraWithFaceDetection() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;

                video.onloadeddata = async () => {
                    await loadFaceApiModels();
                    await video.play(); // Ensure the video plays
                    detectFacesLoop();
                };
            } catch (err) {
                Swal.fire({
                    title: 'Please be informed',
                    text: 'Camera and location access are required to continue. Please ensure both are enabled in your device settings before proceeding.',
                    icon: 'info',
                });
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
                    if (video.readyState >= 2) {
                        const result = await faceapi.detectAllFaces(video, options);
                        if (result.length < 1) {
                            $('.alert-container').html(`
                                <div class="alert-no-face">
                                    <div>Face Is Not Detected</div>
                                </div>
                            `);
                            $('.clock-process').attr('wire:click', 'triggerClock(true, false, false)');
                        } else {
                            $('.alert-container').empty();
                            $('.clock-process').attr('wire:click', 'triggerClock(true, false, true)');
                        }
                    }
                } catch (error) {
                    console.error('Face detection failed:', error);
                }
                requestAnimationFrame(detect);
            }

            detect();
        }
    });
}
