<template>
    <div class="modal fade" :id="ident" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{ title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <slot />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ close }}</button>
                    <button type="button" class="btn btn-primary" @click="submitForm">{{ save }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Modal',
    props: {
        title: {
            name: 'title',
            default: () => 'Внимание',
        },
        ident: {
            name: 'ident',
            required: true
        },
        save: {
            name: 'submit',
            default: () => 'Сохранить'
        },
        close: {
            name: 'close',
            default: () => 'Закрыть'
        }
    },
    methods: {
        submitForm() {
            this.$emit('submit');
        }
    }
}

</script>