<template>
        <div class="ss-content-card">
            <div class="ss-card-head">
                Change Password
            </div>
            <div class="ss-card-body">
                <form @submit.prevent="updatePassword">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password*</label>
                        <input
                            type="password"
                            class="form-control"
                            v-model="data.passwords.current_password"
                            name="current_password"
                            id="current_password"
                            :required="true"
                            aria-describedby="helpId"
                            placeholder="Current Password*"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password*</label>
                        <input
                            type="password"
                            class="form-control"
                            v-model="data.passwords.password"
                            name="new_password"
                            id="new_password"
                            :required="true"
                            aria-describedby="helpId"
                            placeholder="New Password*"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password*</label>
                        <input
                            type="password"
                            class="form-control"
                            v-model="data.passwords.password_confirmation"
                            name="new_password_confirmation"
                            id="new_password_confirmation"
                            :required="true"
                            aria-describedby="helpId"
                            placeholder="Confirm New Password*"
                        />
                    </div>
                    <div class="mb-3">
                        <button
                            type="submit"
                            class="btn btn-dark"
                        >
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
</template>

<script setup>
    import axios from "axios";
    import { reactive } from "vue";
    import { useToast } from "vue-toastification";
    import { BASE_URL, headersConfig } from "../../helpers/config";
    import { useAuthStore } from "../../stores/useAuthStore";

    //define the data object
    const data = reactive({
        passwords: {
            current_password: '',
            password: '',
            password_confirmation: ''
        }
    })

    //define the toast
    const toast = useToast()

    //define the store
    const authStore = useAuthStore()

    //reset the form fields back to empty
    const resetForm = () => {
        data.passwords.current_password = ''
        data.passwords.password = ''
        data.passwords.password_confirmation = ''
    }

    //update password function
    const updatePassword = async () => {
        authStore.clearValidationErrors()
        authStore.isLoading = true

        try {
            const response = await axios.put(`${BASE_URL}/user/update/password`,
                data.passwords,headersConfig(authStore.access_token)
            )
            authStore.isLoading = false
            toast.success(response.data.message,{
                timeout: 2000
            })
            resetForm()
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
</style>
