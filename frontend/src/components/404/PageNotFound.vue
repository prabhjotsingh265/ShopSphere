<template>
    <div class="row my-5">
        <div class="col-md-6 mx-auto text-center">
            <Logo :size="64" />
            <h1 class="ss-404-code">404</h1>
            <p class="text-muted mb-4">The page you are looking for does not exist.</p>
            <router-link to="/" class="btn btn-dark">
                <i class="bi bi-house-door"></i> Back Home
            </router-link>
        </div>
    </div>
</template>

<script setup>
    import Logo from "../layouts/Logo.vue"
</script>

<style scoped>
.ss-404-code {
    font-family: 'Big Shoulders Display', sans-serif;
    font-weight: 900;
    font-size: 5rem;
    color: var(--pine, #1B3A2B);
    margin: 16px 0 8px;
    line-height: 1;
}
</style>
