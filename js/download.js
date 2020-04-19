document.addEventListener("DOMContentLoaded", async () => {
    try {
        // prepare service worker
        const swreg = await navigator.serviceWorker.register('/download-sw.js', { scope: '/__download__/' });
        await swreg.update();
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
    }
    catch (error) {
        console.error(error);
    }
});

async function createDownload(name, files) {
    // prepare service worker
    const swreg = await navigator.serviceWorker.register('/download-sw.js', { scope: '/__download__/' });
    while (!swreg.active) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    console.log('service worker ready');
    // prepare virtual URL
    const url = window.location.origin
        + '/__download__/'
        + new Date().toISOString().slice(0, 19).replace(/[T\-:]/gi, '') + '/'
        + name
        + '.zip';
    console.log('creating download ' + url);
    console.log(JSON.stringify(files, null, 2));
    // configure URL in service worker
    return await new Promise(function (resolve, reject) {
        const channel = new MessageChannel();
        channel.port1.addEventListener('message', (event) => {
            if (event.data.result) {
                return resolve(url);
            }
            return reject(new Error('could not prepare download' + (event.data.message ? `: ${event.data.message}` : '')));
        });
        channel.port1.start();
        if (!swreg.active) {
            return reject(new Error('could not find active service worker'));
        }
        swreg.active.postMessage(
            {
                command: 'create',
                data: { url, files },
            },
            [channel.port2]
        );
    });
}
