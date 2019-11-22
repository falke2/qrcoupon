<template>
    <div>
        <qrcode-stream @decode="onDecode"></qrcode-stream>
    </div>
</template>

<script>
    import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader';

    export default {
        name: 'QRReader',
        components: {
            QrcodeStream
        },
        methods: {
            onDecode(event) {
                console.log(event);
            }
        }
    }
</script>