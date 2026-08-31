<template>
    <div class="col-md-4">
        <div class="ss-filter-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="m-0">Filters</h5>
                <button class="btn btn-sm btn-link text-decoration-none p-0"
                    style="color: var(--brass-dark)"
                    @click="productsStore.clearFilters"
                    >Clear all</button>
            </div>
            <SearchForm />
            <Categories />
            <Brands />
            <Sizes />
            <Colors />
        </div>
    </div>
</template>

<script setup>
    import Categories from "../partials/Categories.vue"
    import Brands from "../partials/Brands.vue"
    import Colors from "../partials/Colors.vue"
    import Sizes from "../partials/Sizes.vue"
    import SearchForm from "../partials/SearchForm.vue"
    import { useProductsStore } from "../../stores/useProductsStore"

    //define the store
    const productsStore = useProductsStore()
</script>

<style scoped>
</style>