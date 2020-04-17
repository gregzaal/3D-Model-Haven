async function createDownload(name, files) {
    // prepare service worker
    const swreg = await navigator.serviceWorker.register('/download-sw.js', { scope: '/__download__/' });
    while (!swreg.active) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    console.log('updating service worker');
    await swreg.update();
    while (!swreg.active) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    console.log('service worker ready');
    // prepare virtual URL
    const url = window.location.origin
        + '/__download__/'
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
            return reject(new Error('could not prepare download' + (event.data.message ? `: ${event.data.message}` : '') + ' (reload the extension or restart chrome in case the error persists).'));
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
        setInterval(() => {
            if (swreg.active) {
                swreg.active.postMessage({ command: 'ping' });
            }
        }, 1000);
    });
}

var click_functions = function(){

    // Download multiselection
    $('.download-buttons .sel').click(function() {
        if ($(this).parent().hasClass("selected")){
            $(this).parent().removeClass("selected");
            $(this).html("<i class=\"material-icons\">check_box_outline_blank</i>");
        }else{
            $(this).parent().addClass("selected");
            $(this).html("<i class=\"material-icons\">check_box</i>");
        }
    });
    // Download track
    $('.dl-btn').click(function() {
        var id = $(this).attr("id");
        var fhash = $(this).attr("fhash");
        console.log("Tracking download - id:"+id+", fhash:"+fhash);
        $.post("dl_click.php", {id: id, fhash: fhash});
    });

    $('.zip-dl').click(function() {
        $('#sw-tab-warning').removeClass('hidden');  // TODO only needed on firefox
        $(this).children('.zip-loading').removeClass('hidden');
        var name = $(this).children('.zip-dl-files').attr('name');
        var files_parsed = JSON.parse($(this).children('.zip-dl-files').html());
        var files = [];
        files_parsed.forEach(function(f){
            files.push({url: location.origin + "/" + f[0], path: f[1]});
        });
        createDownload(name, files)
        .then(url => {
            window.location.href = url;
            $(this).children('.zip-loading').addClass('hidden');
        });
    });
};

$(document).ready(click_functions);
