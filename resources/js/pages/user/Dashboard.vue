<template>
    <div class="dashboard">
        <div class="container mt-3">
            <div class="row">
                <h1>Купоны</h1>
            </div>
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <create-coupon @add="loadCoupons" class="d-inline-block">Создать купоны</create-coupon>
                    <router-link to="/" class="btn btn-secondary">Считывание купонов</router-link>
                </div>
            </div>
            <div class="row mb-2 d-none d-lg-flex">
                <div class="col-lg-1"></div>
                <div class="col-lg-3">Код</div>
                <div class="col-lg-1">Скидка</div>
                <div class="col-lg-2">Начало</div>
                <div class="col-lg-2">Конец</div>
                <div class="col-lg-3"></div>
            </div>
            <div class="row coupon-row py-4" v-for="coupon in coupons">
                <div class="col-lg-1 col-1">
                    <input type="checkbox" />
                </div>
                <div class="col-lg-3 col-7 code-col">
                    {{ coupon.code }} <a :href="backend + '/code/' + coupon.qrlink" target="blank">QR</a><br />
                    <span class="tag infinite" v-if="coupon.infinite">бессрочный</span>
                    <span class="tag reusable" v-if="coupon.reusable">многоразовый</span>
                    <span class="tag printed" v-if="coupon.printed">распечатан</span>
                    <span class="tag disabled" v-if="!coupon.active">отключен</span>
                    <span class="tag used" v-if="coupon.activations.length">активирован {{ coupon.activations.length }} раз</span>
                </div>
                <div class="col-lg-1 col-4">{{ coupon.price }} руб.</div>
                <div class="col-1 d-lg-none"></div>
                <div class="col-lg-2 col-11">{{ formatDate(coupon.active_from) }}</div>
                <div class="col-1 d-lg-none"></div>
                <div class="col-lg-2 col-11">{{ formatDate(coupon.active_to) }}</div>
                <div class="col-lg-3 col-12 text-right">
                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        @click.prevent="deleteCoupon(coupon.id)"
                    >
                        Удалить
                    </button>
                    <button
                        v-if="coupon.active"
                        type="button"
                        class="btn btn-success btn-sm"
                        @click.prevent="toggleCouponActive(coupon.id)"
                    >
                        Отключить
                    </button>
                    <button
                        v-if="!coupon.active"
                        type="button"
                        class="btn btn-warning btn-sm"
                        @click.prevent="toggleCouponActive(coupon.id)"
                    >
                        Включить
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import CreateCoupon from '../../components/CreateCoupon';

import moment from 'moment';

export default {
    components: { CreateCoupon },
    data: () => ({
        coupons: [],
        filter: {
            active: false
        }
    }),
    computed: {
        backend() {
            return process.env.MIX_APP_URL;
        }
    },
    methods: {
        async loadCoupons() {
            const couponsPlainResponse = await this.$http({
                url: 'coupons/list',
                method: 'POST',
                data: this.filter
            }).then((response) => {
                this.coupons = response.data.data
            }, (e) => {
                console.error(e);
            })
        },
        formatDate(date) {
            return moment(date).locale('ru').format('L') + ' ' + moment(date).locale('ru').format('LT');
        },
        async deleteCoupon(coupon_id) {
            const deletePlainResponse = await this.$http({
                url: 'coupons/delete',
                method: 'POST',
                data: {
                    id: coupon_id
                }
            }).then((response) => {
                this.loadCoupons();
            }, (e) => {
                console.error(e);
            })
        }
        ,
        async toggleCouponActive(coupon_id) {
            const toggleActivePlainResponse = await this.$http({
                url: 'coupons/toggle/active',
                method: 'POST',
                data: {
                    id: coupon_id
                }
            }).then((response) => {
                this.loadCoupons();
            }, (e) => {
                console.error(e);
            })
        }
    },
    mounted() {
        this.loadCoupons();
    }
}
</script>

<style lang="scss">
    .code-col {
        line-height:14px;
    }
    .tag {
        font-size:10px;
        padding:0 5px;
        border-radius:3px;
        margin:0 1px;
        display:inline-block;
        line-height:14px;
        background:silver;
        color:white;
        &.reusable {
            background:#facd60;
        }
        &.infinite {
            background:#309975;
        }
        &.printed {
            background:#2a6fdb;
        }
        &.used {
            background:#e74645;
        }
    }
    .coupon-row:nth-child(even){
        background:#eee;
    }
</style>