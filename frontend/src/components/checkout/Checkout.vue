<template>
    <div class="row my-4">
        <div class="ss-page-title">Checkout</div>
        <UpdateUserInfos :updatingProfile="false" />
        <div class="col-md-4">
            <Coupon />
            <div class="ss-content-card mb-3">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex"
                        v-for="product in cartStore.cartItems"
                        :key="product.ref"
                    >
                        <img :src="product.image"
                            width="56"
                            height="56"
                            class="img-fluid rounded me-2"
                            :alt="product.name"
                        >
                        <div class="d-flex flex-column">
                            <span class="fw-semibold small">{{ product.name }}</span>
                            <span class="text-muted small">
                                {{ product.color }} / {{ product.size }}
                            </span>
                        </div>
                        <div class="d-flex flex-column ms-auto text-end">
                            <span class="text-muted small mono">
                                ${{ product.price }} &times; {{ product.qty }}
                            </span>
                            <span class="fw-bold mono">
                                ${{ product.price * product.qty }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small">
                            Discount ({{ cartStore.validCoupon.discount }}%)
                        </span>
                        <span class="small" style="color: var(--rust)"
                            v-if="cartStore.validCoupon?.name">
                            {{ cartStore.validCoupon.name }}
                            <i class="bi bi-trash ms-1"
                                :style="{ cursor: 'pointer' }"
                                @click="removeCoupon"
                            ></i>
                        </span>
                        <span class="fw-bold mono" style="color: var(--rust)">
                            -${{ calculatedDiscount() }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center" style="background: var(--bg)">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold mono" style="font-size: 1.2rem; color: var(--rust)">${{ finalTotal() }}</span>
                    </li>
                </ul>
            </div>
            <div class="my-3">
                <Stripe
                    v-if="authStore.user?.profile_completed"
                />
                <Alert v-else
                    content="Add your billing details"
                    bgColor="warning"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted } from "vue"
    import { useRouter } from "vue-router"
    import { useToast } from "vue-toastification"
    import { useAuthStore } from "../../stores/useAuthStore"
    import { useCartStore } from "../../stores/useCartStore"
    import UpdateUserInfos from "../profile/UpdateUserInfos.vue"
    import Alert from "../layouts/Alert.vue"
    import Coupon from "../coupons/Coupon.vue"
    import Stripe from "../payment/Stripe.vue"

    //define the stores
    const cartStore = useCartStore()
    const authStore = useAuthStore()

    //define the router
    const router = useRouter()

    //define the toast
    const toast = useToast()

    //calculate the cart total
    const totalOfCartItems = cartStore.cartItems.reduce((acc,item) => acc += item.price * item.qty,0)

    //calculate the discount
    const calculatedDiscount = () => totalOfCartItems * cartStore.validCoupon.discount / 100

    //calculate the final total
    const finalTotal = () => totalOfCartItems - calculatedDiscount()

    //remove coupon function
    const removeCoupon = () => {
        cartStore.setValidCoupon({
            name: '',
            discount: 0
        })
        //set the coupon id for each item in the cart
        cartStore.addCouponToCartItem(null)
        toast.success("Coupon removed",{
            timeout: 2000
        })
    }

    //redirect the user to the home page if the cart is empty
    onMounted(() => {
        if(!cartStore.cartItems.length) {
            router.push('/')
        }
    })
</script>

<style scoped>
</style>
