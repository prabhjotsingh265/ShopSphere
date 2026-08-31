<template>
    <div>
        <section class="ss-hero ss-reveal">
            <div class="ss-hero-copy">
                <span class="ss-eyebrow">New Season &middot; In Stock Now</span>
                <h1>Stitched for scale.</h1>
                <p>Every garment on this shelf is checked, sized, and shipped by hand &mdash; browse the catalog below and filter by category, brand, color or size.</p>
            </div>
            <div class="ss-hero-mark">
                <Logo :size="120" variant="on-pine" />
            </div>
        </section>

        <div class="row my-4">
            <Spinner :store="productsStore" />
            <Sidebar />
            <div class="col-md-8">
                <ProductList />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted } from "vue"
    import { useProductsStore } from "../stores/useProductsStore"
    import ProductList from "./products/ProductList.vue"
    import Spinner from "./layouts/Spinner.vue"
    import Sidebar from "./layouts/Sidebar.vue"
    import Logo from "./layouts/Logo.vue"

    //define the store variable
    const productsStore = useProductsStore()

    //once the component is mounted we call the action
    onMounted(() => productsStore.fetchAllProducts())
</script>

<style scoped>
.ss-hero {
    background: var(--pine);
    border-radius: 14px;
    padding: 48px 40px;
    margin-top: 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    overflow: hidden;
    position: relative;
}
.ss-hero-copy { max-width: 560px; position: relative; z-index: 1; }
.ss-eyebrow {
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
    font-size: .72rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--brass, #C9922B);
    display: block;
    margin-bottom: 14px;
}
.ss-hero h1 {
    color: #EEF0E8;
    font-family: 'Big Shoulders Display', sans-serif;
    font-weight: 900;
    text-transform: uppercase;
    font-size: clamp(2.2rem, 5vw, 3.4rem);
    line-height: .95;
    margin: 0 0 16px;
}
.ss-hero p {
    color: #C7D2C9;
    font-size: 1rem;
    margin: 0;
}
.ss-hero-mark { flex: none; opacity: .9; }
@media (max-width: 767px) {
    .ss-hero { flex-direction: column; text-align: center; padding: 36px 24px; }
    .ss-hero-mark { display: none; }
}
</style>
