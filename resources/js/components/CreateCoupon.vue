<template>
    <div>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCouponModal"><slot /></button>
        <modal class="text-left" ident="createCouponModal" title="Создать купоны" save="Сохранить" @submit="create">
            <form>
                <div class="form-group">
                    <label for="price">Скидка</label>
                    <input v-model="coupon.price" type="text" name="price" class="form-control" id="price" placeholder="Введи сумму скидки по купону">
                </div>
                <div class="form-group">
                    <label for="active_from">Дата начала</label>
                    <input v-model="coupon.active_from" type="datetime-local" name="active_from" class="form-control" id="active_from" placeholder="Дата начала действия скидочного купона">
                </div>
                <div class="form-group">
                    <label for="active_to">Дата окончания</label>
                    <input v-model="coupon.active_to" type="datetime-local" name="active_to" class="form-control" id="active_to" placeholder="Дата окончания действия скидочного купона">
                </div>
                <div class="form-group">
                    <label for="count">Количество</label>
                    <input v-model="coupon.count" type="number" name="count" class="form-control" id="count" aria-describedby="countHelp" placeholder="Сколько штук сгенерировать?">
                    <small id="countHelp" class="form-text text-muted">Какое количество купонов сгенерировать.</small>
                </div>
                <div class="form-group form-check">
                    <input v-model="coupon.infinite" type="checkbox" name="infinite" class="form-check-input" aria-describedby="infiniteHelp" id="infinite">
                    <label class="form-check-label" for="infinite">Бессрочный</label>
                    <small id="infiniteHelp" class="form-text text-muted">Будет действовать вне зависимости от даты.</small>
                </div>
                <div class="form-group form-check">
                    <input v-model="coupon.reusable" type="checkbox" name="reusable" class="form-check-input" aria-describedby="reusableHelp" id="reusable">
                    <label class="form-check-label" for="reusable">Многоразовый</label>
                    <small id="reusableHelp" class="form-text text-muted">Будет действовать сколько угодно раз пока не будет выпилен отсюда.</small>
                </div>
            </form>
        </modal>
    </div>
</template>

<script>
import Modal from './Modal';

import moment from 'moment';
import $ from 'jquery';

export default {
    name: 'CreateCoupon',
    components: { Modal },
    data: () => ({
       coupon: {
           price: 0,
           active_from: moment().format('YYYY-MM-DDT00:00'),
           active_to: moment().format('YYYY-MM-DDT23:59'),
           count: 1,
           infinite: false,
           reusable: false,
       }
    }),
    methods: {
        create() {
            this.$http({
                url: 'coupons/create',
                method: 'POST',
                data: this.coupon
            }).then((response) => {
                this.$emit('add');
                $('#createCouponModal').modal('hide');

            }, (e) => {
                console.error(e);
            });
        },

    }
}
</script>