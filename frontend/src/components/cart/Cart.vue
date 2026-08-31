<template>
  <div class="row my-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center gap-2 mb-3 ss-reveal">
            <h3 class="m-0">Your Cart</h3>
            <span class="count-pill">{{ cartStore.cartItems.length }} item{{ cartStore.cartItems.length === 1 ? '' : 's' }}</span>
        </div>
        <div class="ss-cart-card ss-reveal" v-if="cartStore.cartItems.length">
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product,index) in cartStore.cartItems"
                        :key="product.ref">
                        <td>{{ index += 1 }}</td>
                        <td>
                            <img :src="product.image"
                                width="56"
                                height="56"
                                class="img-fluid rounded ss-cart-thumb"
                                :alt="product.name"
                            >
                        </td>
                        <td class="fw-semibold">{{ product.name }}</td>
                        <td>
                            <div class="ss-qty-stepper">
                                <button type="button" @click="cartStore.decrementQty(product)"><i class="bi bi-dash"></i></button>
                                <span class="mono">{{ product.qty }}</span>
                                <button type="button" @click="cartStore.incrementQty(product)"><i class="bi bi-plus"></i></button>
                            </div>
                        </td>
                        <td class="mono">${{ product.price }}</td>
                        <td>
                            <div class="ss-color-swatch"
                                :style="{ backgroundColor: product.color }"
                            ></div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ product.size }}
                            </span>
                        </td>
                        <td class="mono fw-semibold">
                            ${{ product.qty * product.price }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="cartStore.removeFromCart(product)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="ss-cart-total">
                <span>Total</span>
                <span class="mono fw-bold" style="font-size:1.3rem; color: var(--rust)">${{ total }}</span>
            </div>
        </div>
        <Alert v-else bgColor="info" content="Your cart is empty!" />
        <div class="d-flex justify-content-end gap-2 my-3">
            <router-link to="/"
                class="btn btn-outline-secondary"
                >Continue Shopping</router-link>
            <router-link to="/checkout"
                class="btn btn-dark"
                v-if="cartStore.cartItems.length"
                >Checkout <i class="bi bi-arrow-right"></i></router-link>
        </div>
    </div>
  </div>
</template>

<script setup>
    import { computed } from "vue"
    import { useCartStore } from "../../stores/useCartStore"
    import Alert from "../layouts/Alert.vue"

    //define the cart store
    const cartStore = useCartStore()

    //calculate the cart total
    const total = computed(() => cartStore.cartItems.reduce((acc,item) => acc += item.price * item.qty,0))
</script>

<style scoped>
.ss-cart-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-m, 10px);
    overflow: hidden;
}
.ss-cart-thumb { object-fit: cover; }
.ss-cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 22px;
    border-top: 1px solid var(--line);
    background: var(--bg, #EEF0E8);
}
.ss-qty-stepper {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 2px 4px;
}
.ss-qty-stepper button {
    border: none;
    background: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color .15s ease;
}
.ss-qty-stepper button:hover { background: var(--bg, #EEF0E8); }
</style>
