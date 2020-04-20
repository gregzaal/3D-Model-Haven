let downloadStatus = null;

function renderDownloadWindow() {
    const isDownloading = downloadStatus && Object.keys(downloadStatus.active).length > 0;
    document.getElementById('status').textContent
        = (isDownloading ? 'DOWNLOADING... DO NOT CLOSE!\n\n' : '')
        + JSON.stringify(downloadStatus, null, 2);
}

window.addEventListener('beforeunload', function (event) {
    if (downloadStatus == null || Object.keys(downloadStatus.active).length > 0) {
        event.preventDefault();
        event.returnValue = '';
        return false;
    }
});

document.addEventListener("DOMContentLoaded", async () => {
    try {
        // prepare service worker
        const swreg = await navigator.serviceWorker.register('/download-sw.js', { scope: '/__download__/' });
        // keep alive ping (required for firefox)
        setInterval(() => {
            try {
                if (swreg.active) {
                    swreg.active.postMessage({ command: 'ping' });
                }
            }
            catch (error) {
                console.error(error);
            }
        }, 1000);
        // status update handler
        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data.command == 'update-status') {
                downloadStatus = event.data.status;
                renderDownloadWindow();
            }
        });
        let activeWorker = null;
        setInterval(() => {
            try {
                if (swreg.active && activeWorker !== swreg.active) {
                    activeWorker = swreg.active;
                    activeWorker.postMessage({ command: 'broadcast-status' });
                }
            }
            catch (error) {
                console.error(error);
            }
        }, 1000);
    }
    catch (error) {
        console.error(error);
    }
});
