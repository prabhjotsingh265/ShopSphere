<template>
  <div class="mb-4">
    <div class="ss-filter-title">Brands</div>
    <ul class="nav d-flex flex-column">
        <li v-for="brand in productsStore.brands"
          :key="brand.id">
            <button class="ss-filter-link"
              @click="productsStore.filterProducts('brand',brand.slug)"
              >
                {{ brand.name }}
            </button>
        </li>
    </ul>
  </div>
</template>

<script setup>
  import { useProductsStore } from "../../stores/useProductsStore";

  //define the store variable
  const productsStore = useProductsStore()
</script>

<style scoped>
</style>