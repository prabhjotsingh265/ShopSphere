<template>
  <div class="row my-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h3 class="m-0">Favorites</h3>
            <span class="count-pill">{{ favoritesStore.favorites.length }}</span>
        </div>
        <div class="ss-content-card" v-if="favoritesStore.favorites.length">
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product,index) in favoritesStore.favorites"
                        :key="product.id">
                        <td>{{ index += 1 }}</td>
                        <td>
                            <img :src="product.thumbnail"
                                width="56"
                                height="56"
                                class="img-fluid rounded"
                                :alt="product.name"
                            >
                        </td>
                        <td>
                            <router-link :to="`/product/${product.slug}`" class="fw-semibold text-decoration-none" style="color: var(--ink)">
                                {{ product.name }}
                            </router-link>
                        </td>
                        <td class="mono">${{ product.price }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="favoritesStore.addToFavorites(product)">
                                <i class="bi bi-bookmark-x"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <Alert v-else bgColor="info" content="Your favorites list is empty !" />
    </div>
  </div>
</template>

<script setup>
    import { useFavoritesStore } from "../../stores/useFavoritesStore"
    import Alert from "../layouts/Alert.vue"

    //define the favorites store
    const favoritesStore = useFavoritesStore()
</script>

<style scoped>
</style>
