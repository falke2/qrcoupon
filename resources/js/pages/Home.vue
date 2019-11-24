<template>
    <div>
        <QRReader class="reader" id="reader" @done="onCode" v-if="!code"></QRReader>
        <div id="accept-code" v-if="coupon.id">
            <div>
                <div class="title">Скидка: {{ coupon.price }} руб.</div>
                <div class="state my-2">
                    <span v-if="coupon.state.status === 'success'" class="success">Доступен для активации</span>
                    <span v-if="coupon.state.status === 'error'" class="error">Недоступен для активации</span>
                </div>
                <div>Действует с: {{ formatDate(coupon.active_from) }}</div>
                <div>Действует до: {{ formatDate(coupon.active_to) }}</div>
                <div class="my-2 mt-3">
                    <button v-if="coupon.state.status === 'success'" type="button" class="btn btn-success" @click.prevent="activate">Активировать</button>
                    <button type="button" class="btn btn-primary" @click.prevent="showActivations = !showActivations">Активации</button>
                    <button type="button" class="btn btn-secondary" @click.prevent="close">Закрыть</button>
                </div>
                <transition name="slide">
                    <div v-if="coupon.activations.length && showActivations">
                        <div class="my-2">Активации:</div>
                        <div v-for="activation in coupon.activations" :key="activation.id">
                            {{ formatDate(activation.date) }}
                        </div>
                    </div>
                </transition>
            </div>
        </div>
        <div class="dashboard-link">
            <router-link to="/dashboard" class="btn btn-primary">Панель управления</router-link>
        </div>
    </div>
</template>

<script>
import QRReader from '../components/QRReader';

import moment from 'moment';

export default {
    name: 'Home',
    components: { QRReader },
    data: () => ({
        code: null,
        coupon: {
            id: null,
            activations: []
        },
        showActivations: false,
    }),
    methods: {
        onCode(code) {
            this.$http({
                method: 'POST',
                url: 'coupons/get/bycode',
                data: {
                    code: code
                }
            }).then((response) => {
                console.log(response);
                this.coupon = response.data.data;
                this.code = code;
            }).catch((error) => {
                console.error(error);
            });
        },
        formatDate(date) {
            return moment(date).locale('ru').format('L') + ' ' + moment(date).locale('ru').format('LT');
        },
        activate() {
            this.$http({
                method: 'POST',
                url: 'coupons/activate',
                data: {
                    code: this.code
                }
            }, (e) => {
                console.error(e)
            }).then((response) => {
                console.log(response);
                if(response.data.success) {
                    this.coupon = {id: null, activations: []};
                    this.code = null;
                    alert('Купон успешно активирован!');
                } else {
                    alert('Ошибка активации купона, код ' + response.data.code + '!');
                }
            });
        },
        close() {
            this.coupon = {id: null, activations: []};
            this.code = null;
        },
        showError(error) {
            this.error = error;
        }
    }
}
</script>

<style lang="scss">
    #reader {
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:1;
    }
    #accept-code {
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:2;
        background:white;
        display:flex;
        justify-content:center;
        align-items:center;
        flex-direction:column;
        .title {
            font-size:26px;
        }
        .state {
            .success {
                color:#58b368;
            }
            .error {
                color:#e74645;
            }
        }
    }
    .slide-leave-active,
    .slide-enter-active {
        transition: 1s;
    }
    .slide-enter-active {
        transition-duration: 0.2s;
        transition-timing-function: ease-in;
    }

    .slide-leave-active {
        transition-duration: 0.2s;
        transition-timing-function: cubic-bezier(.25,.1,.25,1);
    }

    .slide-enter-to, .slide-leave {
        max-height: 200px;
        overflow: hidden;
    }

    .slide-enter, .slide-leave-to {
        overflow: hidden;
        max-height: 0;
    }
    .dashboard-link {
        position:absolute;
        z-index:2;
        bottom:30px;
        left:50%;
        margin-left:-79px;
    }

</style>