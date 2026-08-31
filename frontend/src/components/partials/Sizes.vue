<template>
  <div class="mb-4">
    <div class="ss-filter-title">Sizes</div>
    <div class="d-flex flex-wrap gap-2">
      <button class="ss-size-chip"
        v-for="size in productsStore.sizes"
        :key="size.id"
        @click="productsStore.filterProducts('size',size.id)"
        >
          {{ size.name }}
      </button>
    </div>
  </div>
</template>

<script setup>
  import { useProductsStore } from "../../stores/useProductsStore";

  //define the store variable
  const productsStore = useProductsStore()
</script>

<style scoped>
</style>