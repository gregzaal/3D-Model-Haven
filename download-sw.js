// Based on https://github.com/robbederks/downzip - MIT license
// With modifications by https://github.com/core-process/

class Crc32 {
    constructor() {
        this.crc = -1
        this.table = new Int32Array([
            0x00000000, 0x77073096, 0xee0e612c, 0x990951ba, 0x076dc419, 0x706af48f, 0xe963a535,
            0x9e6495a3, 0x0edb8832, 0x79dcb8a4, 0xe0d5e91e, 0x97d2d988, 0x09b64c2b, 0x7eb17cbd,
            0xe7b82d07, 0x90bf1d91, 0x1db71064, 0x6ab020f2, 0xf3b97148, 0x84be41de, 0x1adad47d,
            0x6ddde4eb, 0xf4d4b551, 0x83d385c7, 0x136c9856, 0x646ba8c0, 0xfd62f97a, 0x8a65c9ec,
            0x14015c4f, 0x63066cd9, 0xfa0f3d63, 0x8d080df5, 0x3b6e20c8, 0x4c69105e, 0xd56041e4,
            0xa2677172, 0x3c03e4d1, 0x4b04d447, 0xd20d85fd, 0xa50ab56b, 0x35b5a8fa, 0x42b2986c,
            0xdbbbc9d6, 0xacbcf940, 0x32d86ce3, 0x45df5c75, 0xdcd60dcf, 0xabd13d59, 0x26d930ac,
            0x51de003a, 0xc8d75180, 0xbfd06116, 0x21b4f4b5, 0x56b3c423, 0xcfba9599, 0xb8bda50f,
            0x2802b89e, 0x5f058808, 0xc60cd9b2, 0xb10be924, 0x2f6f7c87, 0x58684c11, 0xc1611dab,
            0xb6662d3d, 0x76dc4190, 0x01db7106, 0x98d220bc, 0xefd5102a, 0x71b18589, 0x06b6b51f,
            0x9fbfe4a5, 0xe8b8d433, 0x7807c9a2, 0x0f00f934, 0x9609a88e, 0xe10e9818, 0x7f6a0dbb,
            0x086d3d2d, 0x91646c97, 0xe6635c01, 0x6b6b51f4, 0x1c6c6162, 0x856530d8, 0xf262004e,
            0x6c0695ed, 0x1b01a57b, 0x8208f4c1, 0xf50fc457, 0x65b0d9c6, 0x12b7e950, 0x8bbeb8ea,
            0xfcb9887c, 0x62dd1ddf, 0x15da2d49, 0x8cd37cf3, 0xfbd44c65, 0x4db26158, 0x3ab551ce,
            0xa3bc0074, 0xd4bb30e2, 0x4adfa541, 0x3dd895d7, 0xa4d1c46d, 0xd3d6f4fb, 0x4369e96a,
            0x346ed9fc, 0xad678846, 0xda60b8d0, 0x44042d73, 0x33031de5, 0xaa0a4c5f, 0xdd0d7cc9,
            0x5005713c, 0x270241aa, 0xbe0b1010, 0xc90c2086, 0x5768b525, 0x206f85b3, 0xb966d409,
            0xce61e49f, 0x5edef90e, 0x29d9c998, 0xb0d09822, 0xc7d7a8b4, 0x59b33d17, 0x2eb40d81,
            0xb7bd5c3b, 0xc0ba6cad, 0xedb88320, 0x9abfb3b6, 0x03b6e20c, 0x74b1d29a, 0xead54739,
            0x9dd277af, 0x04db2615, 0x73dc1683, 0xe3630b12, 0x94643b84, 0x0d6d6a3e, 0x7a6a5aa8,
            0xe40ecf0b, 0x9309ff9d, 0x0a00ae27, 0x7d079eb1, 0xf00f9344, 0x8708a3d2, 0x1e01f268,
            0x6906c2fe, 0xf762575d, 0x806567cb, 0x196c3671, 0x6e6b06e7, 0xfed41b76, 0x89d32be0,
            0x10da7a5a, 0x67dd4acc, 0xf9b9df6f, 0x8ebeeff9, 0x17b7be43, 0x60b08ed5, 0xd6d6a3e8,
            0xa1d1937e, 0x38d8c2c4, 0x4fdff252, 0xd1bb67f1, 0xa6bc5767, 0x3fb506dd, 0x48b2364b,
            0xd80d2bda, 0xaf0a1b4c, 0x36034af6, 0x41047a60, 0xdf60efc3, 0xa867df55, 0x316e8eef,
            0x4669be79, 0xcb61b38c, 0xbc66831a, 0x256fd2a0, 0x5268e236, 0xcc0c7795, 0xbb0b4703,
            0x220216b9, 0x5505262f, 0xc5ba3bbe, 0xb2bd0b28, 0x2bb45a92, 0x5cb36a04, 0xc2d7ffa7,
            0xb5d0cf31, 0x2cd99e8b, 0x5bdeae1d, 0x9b64c2b0, 0xec63f226, 0x756aa39c, 0x026d930a,
            0x9c0906a9, 0xeb0e363f, 0x72076785, 0x05005713, 0x95bf4a82, 0xe2b87a14, 0x7bb12bae,
            0x0cb61b38, 0x92d28e9b, 0xe5d5be0d, 0x7cdcefb7, 0x0bdbdf21, 0x86d3d2d4, 0xf1d4e242,
            0x68ddb3f8, 0x1fda836e, 0x81be16cd, 0xf6b9265b, 0x6fb077e1, 0x18b74777, 0x88085ae6,
            0xff0f6a70, 0x66063bca, 0x11010b5c, 0x8f659eff, 0xf862ae69, 0x616bffd3, 0x166ccf45,
            0xa00ae278, 0xd70dd2ee, 0x4e048354, 0x3903b3c2, 0xa7672661, 0xd06016f7, 0x4969474d,
            0x3e6e77db, 0xaed16a4a, 0xd9d65adc, 0x40df0b66, 0x37d83bf0, 0xa9bcae53, 0xdebb9ec5,
            0x47b2cf7f, 0x30b5ffe9, 0xbdbdf21c, 0xcabac28a, 0x53b39330, 0x24b4a3a6, 0xbad03605,
            0xcdd70693, 0x54de5729, 0x23d967bf, 0xb3667a2e, 0xc4614ab8, 0x5d681b02, 0x2a6f2b94,
            0xb40bbe37, 0xc30c8ea1, 0x5a05df1b, 0x2d02ef8d
        ])
    }

    append(data) {
        for (let offset = 0; offset < data.length; offset++)
            this.crc = (this.crc >>> 8) ^ this.table[(this.crc ^ data[offset]) & 0xFF]
    }

    get() {
        return ((this.crc ^ (-1)) >>> 0)
    }
}


const ZipUtils = {

    // Data is an array in the format: [{data: 0x0000, size: 2} or {data: Buffer()}, ...]
    createByteArray: (data) => {
        const size = data.reduce((acc, value) => {
            return acc + (value.size ? value.size : value.data.length)
        }, 0)
        const array = new Uint8Array(size)
        const dataView = new DataView(array.buffer)

        let i = 0
        data.forEach((entry) => {
            if (entry.data.length !== undefined) {
                // Entry data is some kind of buffer / array
                array.set(entry.data, i)
                i += entry.data.length
            } else {
                // Entry data is some kind of integer
                switch (entry.size) {
                    case 1:
                        dataView.setInt8(i, parseInt(entry.data))
                        break
                    case 2:
                        dataView.setInt16(i, parseInt(entry.data), true)
                        break
                    case 4:
                        dataView.setInt32(i, parseInt(entry.data), true)
                        break
                    case 8:
                        dataView.setBigInt64(i, BigInt(entry.data), true)
                        break
                    default:
                        throw new Error(`no handler defined for data size ${entry.size} of entry data ${JSON.stringify(entry.data)}`);
                }
                i += entry.size
            }
        })
        return array
    },

    getTimeStruct: (date) => {
        return ((((date.getHours() << 6) | date.getMinutes()) << 5) | date.getSeconds() / 2)
    },

    getDateStruct: (date) => {
        return (((((date.getFullYear() - 1980) << 4) | (date.getMonth() + 1)) << 5) | date.getDate())
    },

    calculateSize: (files, zip64) => {
        const localHeaderSizeBig = (file) => BigInt(30 + file.path.length)
        const dataDescriptorSizeBig = BigInt(16)
        const centralDirectoryHeaderSizeBig = (file) => BigInt(46 + file.path.length)
        const endOfCentralDirectorySizeBig = BigInt(22)
        const zip64ExtraFieldSizeBig = BigInt(28)
        const zip64DataDescriptorSizeBig = BigInt(24)
        const zip64EndOfCentralDirectoryRecordSizeBig = BigInt(56)
        const zip64EndOfCentralDirectoryLocatorSizeBig = BigInt(20)

        let totalSizeBig = files.reduce((acc, file) => {
            return (acc
                + localHeaderSizeBig(file)
                + BigInt(file.size)
                + dataDescriptorSizeBig
                + centralDirectoryHeaderSizeBig(file)
            )
        }, BigInt(0))
        totalSizeBig += endOfCentralDirectorySizeBig

        if (zip64) {
            // We have a ZIP64! Add all the data we missed before
            totalSizeBig = files.reduce(acc => {
                return (acc
                    + zip64ExtraFieldSizeBig
                    + (zip64DataDescriptorSizeBig - dataDescriptorSizeBig)
                    + zip64ExtraFieldSizeBig
                )
            }, totalSizeBig)
            totalSizeBig += zip64EndOfCentralDirectoryRecordSizeBig
            totalSizeBig += zip64EndOfCentralDirectoryLocatorSizeBig
        }

        return totalSizeBig
    }
}


class Zip {
    constructor(zip64) {
        // Enable large zip compatibility?
        this.zip64 = zip64

        // Setup file record
        this.fileRecord = []
        this.finished = false

        // Setup byte counter
        this.byteCounterBig = BigInt(0)

        // Setup output stream
        this.outputStream = new ReadableStream({
            start: (controller) => {
                this.outputController = controller;
            },
            cancel: () => {
                console.warn('zip output stream has been canceled');
            },
            read: () => { },
        });
    }

    enqueue(data) {
        this.outputController.enqueue(data);
    }

    close() {
        this.outputController.close();
    }

    error(err) {
        this.outputController.error(err);
    }

    // Generators
    getZip64ExtraField(fileSizeBig, localFileHeaderOffsetBig) {
        return ZipUtils.createByteArray([
            { data: 0x0001, size: 2 },
            { data: 24, size: 2 },
            { data: fileSizeBig, size: 8 },
            { data: fileSizeBig, size: 8 },
            { data: localFileHeaderOffsetBig, size: 8 },
        ])
    }

    isWritingFile() {
        return (this.fileRecord.length > 0 && (this.fileRecord[this.fileRecord.length - 1].done === false));
    }

    // API
    startFile(fileName) {
        if (!this.isWritingFile() && !this.finished) {
            const date = new Date(Date.now())

            // Add file to record
            this.fileRecord = [
                ...this.fileRecord,
                {
                    name: fileName,
                    sizeBig: BigInt(0),
                    crc: new Crc32(),
                    done: false,
                    date,
                    headerOffsetBig: this.byteCounterBig
                }
            ]

            // Generate Local File Header
            const nameBuffer = new TextEncoder().encode(fileName)
            const header = ZipUtils.createByteArray([
                { data: 0x04034B50, size: 4 },
                { data: 0x002D, size: 2 },
                { data: 0x0808, size: 2 },
                { data: 0x0000, size: 2 },
                { data: ZipUtils.getTimeStruct(date), size: 2 },
                { data: ZipUtils.getDateStruct(date), size: 2 },
                { data: 0x00000000, size: 4 },
                { data: (this.zip64 ? 0xFFFFFFFF : 0x00000000), size: 4 },
                { data: (this.zip64 ? 0xFFFFFFFF : 0x00000000), size: 4 },
                { data: nameBuffer.length, size: 2 },
                { data: (this.zip64 ? 28 : 0), size: 2 },
                { data: nameBuffer },
                { data: (this.zip64 ? this.getZip64ExtraField(BigInt(0), this.byteCounterBig) : []) }
            ])

            // Write header to output stream and add to byte counter
            this.enqueue(header)
            this.byteCounterBig += BigInt(header.length)
        } else {
            throw new Error("tried adding file while adding other file or while zip has finished.")
        }
    }

    appendData(data) {
        if (this.isWritingFile() && !this.finished) {
            // Write data to output stream, add to CRC and increment the file and global size counters
            this.enqueue(data)
            this.byteCounterBig += BigInt(data.length)
            this.fileRecord[this.fileRecord.length - 1].crc.append(data)
            this.fileRecord[this.fileRecord.length - 1].sizeBig += BigInt(data.length)
        } else {
            throw new Error('tried to append file data, but there is no open file.')
        }
    }

    endFile() {
        if (this.isWritingFile() && !this.finished) {
            const file = this.fileRecord[this.fileRecord.length - 1]
            const dataDescriptor = ZipUtils.createByteArray([
                { data: 0x08074b50, size: 4 },
                { data: file.crc.get(), size: 4 },
                { data: file.sizeBig, size: (this.zip64 ? 8 : 4) },
                { data: file.sizeBig, size: (this.zip64 ? 8 : 4) }
            ])
            this.enqueue(dataDescriptor)
            this.byteCounterBig += BigInt(dataDescriptor.length)
            this.fileRecord[this.fileRecord.length - 1].done = true
        } else {
            throw new Error('tried to end file, but there is no open file.')
        }
    }

    finish() {
        if (!this.isWritingFile() && !this.finished) {
            // Write central directory headers
            let centralDirectorySizeBig = BigInt(0)
            const centralDirectoryStartBig = this.byteCounterBig
            this.fileRecord.forEach((file) => {
                const { date, crc, sizeBig, name, headerOffsetBig } = file
                const nameBuffer = new TextEncoder().encode(name)
                const header = ZipUtils.createByteArray([
                    { data: 0x02014B50, size: 4 },
                    { data: 0x002D, size: 2 },
                    { data: 0x002D, size: 2 },
                    { data: 0x0808, size: 2 },
                    { data: 0x0000, size: 2 },
                    { data: ZipUtils.getTimeStruct(date), size: 2 },
                    { data: ZipUtils.getDateStruct(date), size: 2 },
                    { data: crc.get(), size: 4 },
                    { data: (this.zip64 ? 0xFFFFFFFF : sizeBig), size: 4 },
                    { data: (this.zip64 ? 0xFFFFFFFF : sizeBig), size: 4 },
                    { data: nameBuffer.length, size: 2 },
                    { data: (this.zip64 ? 28 : 0), size: 2 },
                    { data: 0x0000, size: 2 },
                    { data: 0x0000, size: 2 },
                    { data: 0x0000, size: 2 },
                    { data: 0x00000000, size: 4 },
                    { data: (this.zip64 ? 0xFFFFFFFF : headerOffsetBig), size: 4 },
                    { data: nameBuffer },
                    { data: (this.zip64 ? this.getZip64ExtraField(sizeBig, headerOffsetBig) : []) }
                ])
                this.enqueue(header)
                this.byteCounterBig += BigInt(header.length)
                centralDirectorySizeBig += BigInt(header.length)
            })

            if (this.zip64) {
                // Write zip64 end of central directory record
                const zip64EndOfCentralDirectoryRecordStartBig = this.byteCounterBig
                const zip64EndOfCentralDirectoryRecord = ZipUtils.createByteArray([
                    { data: 0x06064b50, size: 4 },
                    { data: 44, size: 8 },
                    { data: 0x002D, size: 2 },
                    { data: 0x002D, size: 2 },
                    { data: 0, size: 4 },
                    { data: 0, size: 4 },
                    { data: this.fileRecord.length, size: 8 },
                    { data: this.fileRecord.length, size: 8 },
                    { data: centralDirectorySizeBig, size: 8 },
                    { data: centralDirectoryStartBig, size: 8 }
                ])
                this.enqueue(zip64EndOfCentralDirectoryRecord)
                this.byteCounterBig += BigInt(zip64EndOfCentralDirectoryRecord.length)

                // Write zip64 end of central directory locator
                const zip64EndOfCentralDirectoryLocator = ZipUtils.createByteArray([
                    { data: 0x07064b50, size: 4 },
                    { data: 0, size: 4 },
                    { data: zip64EndOfCentralDirectoryRecordStartBig, size: 8 },
                    { data: 1, size: 4 }
                ])
                this.enqueue(zip64EndOfCentralDirectoryLocator)
                this.byteCounterBig += BigInt(zip64EndOfCentralDirectoryLocator.length)
            }

            const endOfCentralDirectoryRecord = ZipUtils.createByteArray([
                { data: 0x06054b50, size: 4 },
                { data: 0, size: 2 },
                { data: 0, size: 2 },
                { data: (this.zip64 ? 0xFFFF : this.fileRecord.length), size: 2 },
                { data: (this.zip64 ? 0xFFFF : this.fileRecord.length), size: 2 },
                { data: (this.zip64 ? 0xFFFFFFFF : centralDirectorySizeBig), size: 4 },
                { data: (this.zip64 ? 0xFFFFFFFF : centralDirectoryStartBig), size: 4 },
                { data: 0, size: 2 }
            ])
            this.enqueue(endOfCentralDirectoryRecord)
            this.close()
            this.byteCounterBig += BigInt(endOfCentralDirectoryRecord.length)

            this.finished = true
        } else {
            throw new Error('empty zip, or there is still a file open.')
        }
    }
}


const activeDownloads = {};
const downloads = {};

function broadcastStatus() {
    self.clients.matchAll({ includeUncontrolled: true, type: 'window' })
        .then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    command: 'update-status',
                    status: {
                        active: activeDownloads,
                    },
                });
            });
        })
        .catch(console.error);
}

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    // get download info
    const download = downloads[event.request.url];
    if (download) {
        // determine zip size and format
        let zipSize = ZipUtils.calculateSize(download, false);
        const useZip64 = zipSize > BigInt('0xFFFFFFFF');
        zipSize = ZipUtils.calculateSize(download, useZip64);
        console.log('size of zip = ' + zipSize + ' bytes');
        console.log('using zip64 = ' + useZip64);
        // prepare response
        const zip = new Zip(useZip64);
        event.respondWith(
            new Response(zip.outputStream, {
                headers: new Headers({
                    'Content-Type': 'application/zip',
                    'Content-Length': zipSize + '',
                })
            })
        );
        // update status
        const activeId = Math.random().toString(36).substring(2) + Date.now().toString(36);
        activeDownloads[activeId] = {
            progress: 0,
            downloading: null,
        };
        broadcastStatus();
        // spin off download routine
        (async function () {
            try {
                // download files
                for (const file of download) {
                    // update status
                    console.log('downloading ' + file.url + '...');
                    activeDownloads[activeId].progress += 1 / download.length;
                    activeDownloads[activeId].downloading = file.url;
                    broadcastStatus();
                    // download file chunks
                    zip.startFile(file.path);
                    const reader = (await fetch(file.url)).body.getReader();
                    while (true) {
                        const chunk = await reader.read();
                        if (chunk.done) {
                            break;
                        }
                        zip.appendData(chunk.value);
                    }
                    zip.endFile();
                }
                // finalize zip file
                zip.finish();
                console.log('completed successfully');
            } catch (error) {
                console.error(error);
                zip.error(error);
            }
            // update status
            delete activeDownloads[activeId];
            broadcastStatus();
        })();
    }
});

self.addEventListener('error', (event) => {
    console.error(event.error);
});

self.addEventListener('message', (event) => {
    const { data, ports } = event;
    if (data.command == 'ping') {
        return;
    }
    if (data.command == 'broadcast-status') {
        broadcastStatus();
        return;
    }
    if (data.command == 'create') {
        (async function () {
            try {
                const { url, files } = data.data;
                // determine size of files
                const filesParallel = 3;
                for (let i = 0; i < files.length; i += filesParallel) {
                    const filesTodo = files.slice(i, i + filesParallel);
                    await Promise.all(filesTodo.map(file => async function () {
                        console.log('fetching metadata of ' + file.url + '...');
                        const response = await fetch(file.url, { method: 'HEAD' });
                        if (!response.ok) {
                            throw new Error(`could not get meta data of ${file.url}`);
                        }
                        if (!response.headers.has('Content-Length')) {
                            throw new Error(`could not determine size of ${file.url}`);
                        }
                        file.size = parseInt(response.headers.get('Content-Length'));
                    }()));
                }
                // add to downloads
                downloads[url] = files;
                ports.forEach(port => port.postMessage({ result: true }));
            }
            catch (error) {
                console.error(error);
                ports.forEach(port => port.postMessage({ result: false, message: error && error.message || 'An unknown error occured' }));
            }
        })()
        return;
    }
    console.error('unknown command: ' + data.command);
});
