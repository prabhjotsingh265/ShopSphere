<template>
  <div class="mb-4">
    <div class="ss-filter-title">Colors</div>
    <div class="d-flex flex-wrap gap-2">
        <div class="ss-color-swatch"
          v-for="color in productsStore.colors"
          :key="color.id"
          @click="productsStore.filterProducts('color',color.id)"
          :title="color.name"
          :style="{ backgroundColor: color.name }"
          >
        </div>
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