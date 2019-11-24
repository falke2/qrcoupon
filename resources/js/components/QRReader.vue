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
            onDecode(code) {
                if(code.indexOf('ASY') === 0) {
                    this.$emit('done', code.replace('ASY', ''));
                }
            }
        }
    }
</script>