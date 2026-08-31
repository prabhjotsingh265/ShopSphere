<template>
  <div class="mb-4">
    <div class="ss-filter-title">Categories</div>
    <ul class="nav d-flex flex-column">
        <li v-for="category in productsStore.categories"
          :key="category.id">
            <button class="ss-filter-link"
              @click="productsStore.filterProducts('category',category.slug)"
              >
                {{ category.name }}
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