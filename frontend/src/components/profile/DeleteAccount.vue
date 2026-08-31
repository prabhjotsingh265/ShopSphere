<template>
        <div class="ss-content-card ss-danger-card">
            <div class="ss-card-head ss-danger-head">
                Delete Account
            </div>
            <div class="ss-card-body">
                <p class="text-muted">
                    Deleting your account is permanent and cannot be undone. All of your
                    orders, reviews and saved details will be removed.
                </p>
                <form @submit.prevent="confirmAndDeleteAccount">
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Password*</label>
                        <input
                            type="password"
                            class="form-control"
                            v-model="data.password"
                            name="delete_password"
                            id="delete_password"
                            :required="true"
                            aria-describedby="helpId"
                            placeholder="Enter your password to confirm*"
                        />
                    </div>
                    <div class="mb-3">
                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
</template>

<script setup>
    import axios from "axios";
    import { reactive } from "vue";
    import { useRouter } from "vue-router";
    import { useToast } from "vue-toastification";
    import { BASE_URL, headersConfig } from "../../helpers/config";
    import { useAuthStore } from "../../stores/useAuthStore";

    //define the data object
    const data = reactive({
        password: ''
    })

    //define the toast
    const toast = useToast()

    //define the store
    const authStore = useAuthStore()

    //define the router
    const router = useRouter()

    //ask for a final confirmation before deleting the account
    const confirmAndDeleteAccount = () => {
        if(confirm('Are you sure you want to permanently delete your account? This cannot be undone.')) {
            deleteAccount()
        }
    }

    //delete account function
    const deleteAccount = async () => {
        authStore.clearValidationErrors()
        authStore.isLoading = true

        try {
            const response = await axios.delete(`${BASE_URL}/user/delete`,
                {
                    ...headersConfig(authStore.access_token),
                    data: { password: data.password }
                }
            )
            authStore.isLoading = false
            authStore.clearAuthData()
            toast.success(response.data.message,{
                timeout: 2000
            })
            router.push('/login')
        } catch (error) {
            authStore.isLoading = false
            if(error?.response?.status === 422) {
                authStore.setValidationErrors(error.response.data.errors)
            }
            console.log(error)
        }
    }
</script>

<style scoped>
    .ss-danger-card {
        border-color: var(--rust, #B54A2C);
    }
    .ss-danger-head {
        color: var(--rust, #B54A2C);
    }
</style>
